<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace Quantum\Mvc;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\SessionException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Asset\AssetException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Lang\LangException;
use Quantum\Exceptions\ViewException;
use Quantum\Renderer\DefaultRenderer;
use Quantum\Exceptions\DiException;
use Quantum\Renderer\TwigRenderer;
use Quantum\Factory\ViewFactory;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Twig\Error\RuntimeError;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use ReflectionException;
use Psr\Log\LogLevel;

/**
 * Class QtView
 * @package Quantum\Mvc
 */
class QtView
{

    /**
     * Layout file
     * @var string
     */
    private $layoutFile = null;

    /**
     * Rendered view
     * @var string
     */
    private $viewContent;

    /**
     * Assets to be included
     * @var array
     */
    private $assets = [];

    /**
     * View params
     * @var array
     */
    private $params = [];

    /**
     * QtView constructor.
     * @throws ViewException
     */
    public function __construct()
    {
        if (get_caller_class() != ViewFactory::class) {
            throw ViewException::directInstantiation(ViewFactory::class);
        }
    }

    /**
     * Sets a layout
     * @param string|null $layoutFile
     * @param array $assets
     */
    public function setLayout(?string $layoutFile, array $assets = [])
    {
        $this->layoutFile = $layoutFile;
        $this->assets = $assets;
    }

    /**
     * Gets the layout
     * @return string|null
     */
    public function getLayout(): ?string
    {
        return $this->layoutFile;
    }

    /**
     * Sets view parameter
     * @param string $key
     * @param mixed $value
     */
    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * Gets the view parameter
     * @param string $key
     * @return mixed|null
     */
    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Sets multiple view parameters
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * Gets all view parameters
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Flushes the view params
     */
    public function flushParams()
    {
        $this->params = [];
    }

    /**
     * Renders the view
     * @param string $viewFile
     * @param array $params
     * @return string|null
     * @throws AssetException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DebugBarException
     * @throws DiException
     * @throws LangException
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SessionException
     * @throws SyntaxError
     * @throws ViewException
     */
    public function render(string $viewFile, array $params = []): ?string
    {
        if (!$this->layoutFile) {
            throw ViewException::noLayoutSet();
        }

        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        $this->viewContent = $this->renderFile($viewFile);

        if (!empty($this->assets)) {
            AssetManager::getInstance()->register($this->assets);
        }

        $debugger = Debugger::getInstance();
        if ($debugger->isEnabled()) {
            $this->updateDebugger($debugger, $viewFile);
        }

        $layoutContent = $this->renderFile($this->layoutFile);

        $viewCacheInstance = ViewCache::getInstance();
        if ($viewCacheInstance->isEnabled()) {
            $layoutContent = $this->cacheContent($viewCacheInstance, route_uri(), $layoutContent);
        }

        return $layoutContent;
    }

    /**
     * Renders partial view
     * @param string $viewFile
     * @param array $params
     * @return string|null
     * @throws DiException
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ViewException
     */
    public function renderPartial(string $viewFile, array $params = []): ?string
    {
        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        return $this->renderFile($viewFile);
    }

    /**
     * Gets the rendered view
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->viewContent;
    }

    /**
     * Renders the view
     * @param string $viewFile
     * @return string
     * @throws DiException
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ViewException
     */
    private function renderFile(string $viewFile): string
    {
        $params = $this->xssFilter($this->params);

        $templateEngine = config()->get('template_engine');

        if ($templateEngine) {
            $configs = config()->get('template_engine.' . key($templateEngine));

            if (!$configs) {
                throw ViewException::missingTemplateEngineConfigs();
            }

            return (new TwigRenderer())->render($viewFile, $params, $configs);
        } else {
            return (new DefaultRenderer())->render($viewFile, $params);
        }
    }

    /**
     * XSS Filter
     * @param mixed $params
     * @return mixed
     */
    private function xssFilter($params)
    {
        if (is_string($params)) {
            $this->cleaner($params);
            $params = [$params];
        } else {
            array_walk_recursive($params, [$this, 'cleaner']);
        }

        return $params;
    }

    /**
     * Cleaner
     * @param mixed $value
     */
    private function cleaner(&$value)
    {
        if (is_object($value)) {
            $this->xssFilter($value);
        } else {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }
    }

    /**
     * @param Debugger $debugger
     * @param string $viewFile
     * @return void
     */
    private function updateDebugger(Debugger $debugger, string $viewFile)
    {
        $routesCell = $debugger->getStoreCell(Debugger::ROUTES);
        $currentData = current($routesCell)[LogLevel::INFO] ?? [];
        $additionalData = ['View' => current_module() . '/Views/' . $viewFile];
        $mergedData = array_merge($currentData, $additionalData);
        $debugger->clearStoreCell(Debugger::ROUTES);
        $debugger->addToStoreCell(Debugger::ROUTES, LogLevel::INFO, $mergedData);
    }

    /**
     * @param ViewCache $viewCacheInstance
     * @param string $uri
     * @param string $content
     * @return string|null
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws LangException
     * @throws ReflectionException
     * @throws SessionException
     */
    private function cacheContent(ViewCache $viewCacheInstance, string $uri, string $content): ?string
    {
        return $viewCacheInstance->set($uri, $content)->get($uri);
    }
}


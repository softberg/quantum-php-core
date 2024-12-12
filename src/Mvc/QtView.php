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

use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Exceptions\DatabaseException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Exceptions\SessionException;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\AssetException;
use Quantum\Exceptions\LangException;
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
    private $layout = null;

    /**
     * Rendered view
     * @var string
     */
    private $view = null;

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
     * @param string|null $layout
     * @param array $assets
     */
    public function setLayout(?string $layout, array $assets = [])
    {
        $this->layout = $layout;
        $this->assets = $assets;
    }

    /**
     * Gets the layout
     * @return string|null
     */
    public function getLayout(): ?string
    {
        return $this->layout;
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
     * @param string $view
     * @param array $params
     * @return string|null
     * @throws AssetException
     * @throws DiException
     * @throws LangException
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ViewException
     * @throws DebugBarException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws SessionException
     */
    public function render(string $view, array $params = []): ?string
    {
        if (!$this->layout) {
            throw ViewException::noLayoutSet();
        }

        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        $this->view = $this->renderFile($view);

        $debugger = Debugger::getInstance();

        if ($debugger->isEnabled()) {
            $routesCell = $debugger->getStoreCell(Debugger::ROUTES);
            $currentData = current($routesCell)[LogLevel::INFO] ?? [];
            $additionalData = ['View' => current_module() . '/Views/' . $view];
            $mergedData = array_merge($currentData, $additionalData);
            $debugger->clearStoreCell(Debugger::ROUTES);
            $debugger->addToStoreCell(Debugger::ROUTES, LogLevel::INFO, $mergedData);
        }

        if (!empty($this->assets)) {
            AssetManager::getInstance()->register($this->assets);
        }

        $content = $this->renderFile($this->layout);

        $viewCacheInstance = ViewCache::getInstance();

        if ($viewCacheInstance->isEnabled()) {
            $content = $viewCacheInstance
                ->set(route_uri(), $content)
                ->get(route_uri());
        }

        return $content;
    }

    /**
     * Renders partial view
     * @param string $view
     * @param array $params
     * @return string|null
     * @throws DiException
     * @throws ViewException
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderPartial(string $view, array $params = []): ?string
    {
        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        return $this->renderFile($view);
    }

    /**
     * Gets the rendered view
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * Renders the view
     * @param string $view
     * @return string
     * @throws DiException
     * @throws ViewException
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function renderFile(string $view): string
    {
        $params = $this->xssFilter($this->params);

        $templateEngine = config()->get('template_engine');

        if ($templateEngine) {
            $configs = config()->get('template_engine.' . key($templateEngine));

            if (!$configs) {
                throw ViewException::missingTemplateEngineConfigs();
            }

            return (new TwigRenderer())->render($view, $params, $configs);
        } else {
            return (new DefaultRenderer())->render($view, $params);
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
}

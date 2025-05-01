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
 * @since 2.9.7
 */

namespace Quantum\View;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Asset\Exceptions\AssetException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\View\Exceptions\ViewException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Renderer\Renderer;
use Quantum\Debugger\Debugger;
use ReflectionException;
use Psr\Log\LogLevel;

/**
 * Class QtView
 * @package Quantum\View
 */
class QtView
{

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var AssetManager|null
     */
    private $assetManager;

    /**
     * @var Debugger|null
     */
    private $debugger;

    /**
     * @var ViewCache|null
     */
    private $viewCache;

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
     * @param Renderer $renderer
     * @param AssetManager|null $assetManager
     * @param Debugger|null $debugger
     * @param ViewCache|null $viewCache
     */
    public function __construct(
        Renderer $renderer,
        ?AssetManager $assetManager = null,
        ?Debugger $debugger = null,
        ?ViewCache $viewCache = null
    )
    {
        $this->renderer = $renderer;
        $this->assetManager = $assetManager;
        $this->debugger = $debugger;
        $this->viewCache = $viewCache;
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
     * Renders the view.
     * @param string $viewFile
     * @param array $params
     * @return string|null
     * @throws AssetException
     * @throws BaseException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
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
            $this->assetManager->register($this->assets);
        }

        if ($this->debugger->isEnabled()) {
            $this->updateDebugger($viewFile);
        }

        $layoutContent = $this->renderFile($this->layoutFile);

        if ($this->viewCache->isEnabled()) {
            $layoutContent = $this->viewCache
                ->set(route_uri(), $layoutContent)
                ->get(route_uri());
        }

        return $layoutContent;
    }

    /**
     * Renders partial view.
     * @param string $viewFile
     * @param array $params
     * @return string|null
     */
    public function renderPartial(string $viewFile, array $params = []): ?string
    {
        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        return $this->renderFile($viewFile);
    }

    /**
     * Gets the rendered view.
     * @return string|null
     * @throws ViewException
     */
    public function getView(): ?string
    {
        if ($this->viewContent === null) {
            throw ViewException::viewNotRendered();
        }

        return $this->viewContent;
    }

    /**
     * Renders the view
     * @param string $viewFile
     * @return string
     */
    private function renderFile(string $viewFile): string
    {
        $params = $this->xssFilter($this->params);

        return $this->renderer->render($viewFile, $params);
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
    private function updateDebugger(string $viewFile)
    {
        $routesCell = $this->debugger->getStoreCell(Debugger::ROUTES);
        $currentData = current($routesCell)[LogLevel::INFO] ?? [];
        $additionalData = ['View' => current_module() . '/Views/' . $viewFile];
        $mergedData = array_merge($currentData, $additionalData);
        $this->debugger->clearStoreCell(Debugger::ROUTES);
        $this->debugger->addToStoreCell(Debugger::ROUTES, LogLevel::INFO, $mergedData);
    }
}
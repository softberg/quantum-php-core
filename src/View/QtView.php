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
 * @since 3.0.0
 */

namespace Quantum\View;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Asset\Exceptions\AssetException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\View\Exceptions\ViewException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Di\Exceptions\DiException;
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
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * @var ViewCache
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
     * @param AssetManager $assetManager
     * @param Debugger $debugger
     * @param ViewCache $viewCache
     */
    public function __construct(
        Renderer $renderer,
        AssetManager $assetManager,
        Debugger $debugger,
        ViewCache $viewCache
    ) {
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

        if ($assets !== []) {
            $this->assets = array_merge($this->assets, $assets);
        }
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
     * @param string $key
     * @param $value
     * @return void
     */
    public function setRawParam(string $key, $value)
    {
        $this->params[$key] = new RawParam($value);
    }

    /**
     * Gets the view parameter
     * @param string $key
     * @return mixed|null
     */
    public function getParam(string $key)
    {
        $param = $this->params[$key] ?? null;

        if ($param instanceof RawParam) {
            return $param->getValue();
        }

        return $param;
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
        $params = [];

        foreach ($this->params as $key => $param) {
            $params[$key] = ($param instanceof RawParam) ? $param->getValue() : $param;
        }

        return $params;
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

        if ($params !== []) {
            $this->params = array_merge($this->params, $params);
        }

        $this->viewContent = $this->renderFile($viewFile);

        if ($this->assets !== []) {
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
     * @return string
     */
    public function renderPartial(string $viewFile, array $params = []): string
    {
        if ($params !== []) {
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
        $filteredParams = $this->xssFilter($this->params);

        return $this->renderer->render($viewFile, $filteredParams);
    }

    /**
     * XSS Filter
     * @param mixed $params
     * @return mixed
     */
    private function xssFilter($params)
    {
        if ($params instanceof RawParam) {
            return $params->getValue();
        }

        if (is_string($params)) {
            return $this->sanitizeHtml($params);
        }

        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $params[$key] = $this->xssFilter($value);
            }

            return $params;
        }

        if (is_object($params)) {
            foreach (get_object_vars($params) as $property => $value) {
                $params->$property = $this->xssFilter($value);
            }

            return $params;
        }

        return $params;
    }

    /**
     * @param string $value
     * @return string
     */
    private function sanitizeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
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

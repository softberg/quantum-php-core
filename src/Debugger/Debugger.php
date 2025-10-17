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
 * @since 2.9.9
 */

namespace Quantum\Debugger;

use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\JavascriptRenderer;
use DebugBar\DebugBarException;
use DebugBar\DebugBar;

/**
 * Class Debugger
 * @package Quantum\Debugger
 * @uses DebugBar
 */
class Debugger
{

    /**
     * Messages tab
     */
    const MESSAGES = 'messages';

    /**
     * Queries tab
     */
    const QUERIES = 'queries';

    /**
     * Routes tab
     */
    const ROUTES = 'routes';

    /**
     * Hooks tab
     */
    const HOOKS = 'hooks';

    /**
     * Mails tab
     */
    const MAILS = 'mails';

    /**
     * Store
     * @var array
     */
    private $store;

    /**
     * @var Debugger
     */
    private static $instance;

    /**
     * DebugBar instance
     * @var DebugBar
     */
    private $debugBar;

    /**
     * Assets url
     * @var string
     */
    private $assetsUrl = '/assets/DebugBar/Resources';

    /**
     * Custom CSS
     * @var string
     */
    private $customCss = 'custom_debugbar.css';

    /**
     * Debugger constructor.
     * @param DebuggerStore $store
     * @param DebugBar $debugBar
     * @param array $collectors
     * @throws DebugBarException
     */
    public function __construct(DebuggerStore $store, DebugBar $debugBar, array $collectors = [])
    {
        $this->store = $store;
        $this->debugBar = $debugBar;

        foreach ($collectors as $collector) {
            $this->debugBar->addCollector($collector);
        }
    }

    /**
     * @param DebuggerStore|null $store
     * @param DebugBar|null $debugBar
     * @param array|null $collectors
     * @return Debugger
     * @throws DebugBarException
     */
    public static function getInstance(DebuggerStore $store = null, DebugBar $debugBar = null, ?array $collectors = []): Debugger
    {
        if (self::$instance === null) {
            $debugBar = $debugBar ?? new DebugBar();
            $store = $store ?? new DebuggerStore();
            $collectors = $collectors ?: self::getDefaultCollectors();

            self::$instance = new self($store, $debugBar, $collectors);
        }

        return self::$instance;
    }

    /**
     * Checks if debug bar enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return filter_var(config()->get('app.debug'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return void
     */
    public function initStore()
    {
        $this->store->init([
            Debugger::MESSAGES,
            Debugger::QUERIES,
            Debugger::ROUTES,
            Debugger::HOOKS,
            Debugger::MAILS
        ]);
    }

    /**
     * Adds data to the store cell
     * @param string $cell
     * @param string $level
     * @param mixed $data
     */
    public function addToStoreCell(string $cell, string $level, $data)
    {
        if (!empty($data)) {
            $this->store->set($cell, [$level => $data]);
        }
    }

    /**
     * @param string $cell
     * @return array
     */
    public function getStoreCell(string $cell): array
    {
        return $this->store->get($cell);
    }

    /**
     * Clears the store cell
     * @param string $cell
     */
    public function clearStoreCell(string $cell)
    {
        $this->store->delete($cell);
    }

    /**
     * @return void
     */
    public function resetStore()
    {
        $this->store->flush();
    }

    /**
     * Renders the debug bar
     * @return string
     * @throws DebugBarException
     */
    public function render(): string
    {
        foreach ([self::MESSAGES, self::QUERIES, self::ROUTES, self::HOOKS, self::MAILS] as $tab) {
            $this->createTab($tab);
        }

        $renderer = $this->getRenderer();

        return $renderer->renderHead() . $renderer->render();
    }

    /**
     * Creates a tab
     * @param string $type
     * @throws DebugBarException
     */
    protected function createTab(string $type)
    {
        if (!$this->debugBar->hasCollector($type)) {
            $this->debugBar->addCollector(new MessagesCollector($type));
        }

        $messages = $this->store->get($type);

        if (count($messages)) {
            foreach ($messages as $message) {
                $fn = key($message);
                $this->debugBar[$type]->$fn($message[$fn]);
            }
        }
    }

    /**
     * Gets the renderer
     * @return JavascriptRenderer
     */
    protected function getRenderer(): JavascriptRenderer
    {
        return $this->debugBar
            ->getJavascriptRenderer()
            ->setBaseUrl(base_url() . $this->assetsUrl)
            ->addAssets([$this->customCss], []);
    }

    /**
     * @return array
     */
    protected static function getDefaultCollectors(): array
    {
        return [
            new PhpInfoCollector(),
            new MessagesCollector(),
            new RequestDataCollector(),
            new TimeDataCollector(),
            new MemoryCollector(),
        ];
    }

}

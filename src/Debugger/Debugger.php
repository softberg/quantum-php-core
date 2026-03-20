<?php

declare(strict_types=1);

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
    public const MESSAGES = 'messages';

    /**
     * Queries tab
     */
    public const QUERIES = 'queries';

    /**
     * Routes tab
     */
    public const ROUTES = 'routes';

    /**
     * Hooks tab
     */
    public const HOOKS = 'hooks';

    /**
     * Mails tab
     */
    public const MAILS = 'mails';

    /**
     * Store
     */
    private DebuggerStore $store;

    private static ?Debugger $instance = null;

    /**
     * DebugBar instance
     */
    private DebugBar $debugBar;

    /**
     * Assets url
     */
    private string $assetsUrl = '/assets/DebugBar/Resources';

    /**
     * Custom CSS
     */
    private string $customCss = 'custom_debugbar.css';

    /**
     * Debugger constructor.
     * @param array<mixed> $collectors
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
     * @param array<mixed> $collectors
     * @throws DebugBarException
     */
    public static function getInstance(?DebuggerStore $store = null, ?DebugBar $debugBar = null, ?array $collectors = []): Debugger
    {
        if (self::$instance === null) {
            $debugBar ??= new DebugBar();
            $store ??= new DebuggerStore();
            $collectors = $collectors ?: self::getDefaultCollectors();

            self::$instance = new self($store, $debugBar, $collectors);
        }

        return self::$instance;
    }

    /**
     * Checks if debug bar enabled
     */
    public function isEnabled(): bool
    {
        return filter_var(config()->get('app.debug'), FILTER_VALIDATE_BOOLEAN);
    }

    public function initStore(): void
    {
        $this->store->init([
            Debugger::MESSAGES,
            Debugger::QUERIES,
            Debugger::ROUTES,
            Debugger::HOOKS,
            Debugger::MAILS,
        ]);
    }

    /**
     * Adds data to the store cell
     * @param mixed $data
     */
    public function addToStoreCell(string $cell, string $level, $data): void
    {
        if (!empty($data)) {
            $this->store->set($cell, [$level => $data]);
        }
    }

    /**
     * @return array<mixed>
     */
    public function getStoreCell(string $cell): array
    {
        return $this->store->get($cell);
    }

    /**
     * Clears the store cell
     */
    public function clearStoreCell(string $cell): void
    {
        $this->store->delete($cell);
    }

    public function resetStore(): void
    {
        $this->store->flush();
    }

    /**
     * Renders the debug bar
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
     * @return void
     * @throws DebugBarException
     */
    protected function createTab(string $type): void
    {
        if (!$this->debugBar->hasCollector($type)) {
            $this->debugBar->addCollector(new MessagesCollector($type));
        }

        $messages = $this->store->get($type);

        foreach ($messages as $message) {
            $fn = key($message);
            $this->debugBar[$type]->$fn($message[$fn]);
        }
    }

    /**
     * Gets the renderer
     */
    protected function getRenderer(): JavascriptRenderer
    {
        return $this->debugBar
            ->getJavascriptRenderer()
            ->setBaseUrl(base_url() . $this->assetsUrl)
            ->addAssets([$this->customCss], []);
    }

    /**
     * @return array<mixed>
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

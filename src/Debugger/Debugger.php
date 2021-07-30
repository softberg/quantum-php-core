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
 * @since 2.5.0
 */

namespace Quantum\Debugger;

use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\JavascriptRenderer;
use DebugBar\DebugBar;

/**
 * Class Debugger
 * @package Quantum\Debugger
 * @uses \DebugBar\DebugBar
 */
class Debugger
{

    /**
     * Messages tab
     */
    const MESSAGES = 'messages';

    /**
     * Quaeries tab
     */
    const QUERIES = 'queries';

    /**
     * Routes tab
     */
    const ROUTES = 'routes';

    /**
     * Mails tab
     */
    const MAILS = 'mails';

    /**
     * Debugbar instance
     * @var \DebugBar\DebugBar
     */
    private $debugbar;

    /**
     * Store
     * @var array
     */
    private static $store;

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
     * @throws \DebugBar\DebugBarException
     */
    public function __construct()
    {
        $this->debugbar = new DebugBar();

        $this->debugbar->addCollector(new PhpInfoCollector());
        $this->debugbar->addCollector(new MessagesCollector());
        $this->debugbar->addCollector(new RequestDataCollector());
        $this->debugbar->addCollector(new TimeDataCollector());
        $this->debugbar->addCollector(new MemoryCollector());
    }

    /**
     * Initiates the store
     */
    public static function initStore()
    {
        self::$store[self::MESSAGES] = [];
        self::$store[self::QUERIES] = [];
        self::$store[self::ROUTES] = [];
        self::$store[self::MAILS] = [];
    }

    /**
     * Adds data to store
     * @param string $cell
     * @param string $level
     * @param mixed $data
     */
    public static function addToStore(string $cell, string $level, $data)
    {
        if(!empty($data)) {
            array_push(self::$store[$cell], [$level => $data]);
        }
    }

    /**
     * Renders the debug bar
     * @return string
     */
    public function render(): string
    {
        $this->createTab(self::MESSAGES);
        $this->createTab(self::QUERIES);
        $this->createTab(self::ROUTES);
        $this->createTab(self::MAILS);

        $renderer = $this->getRenderer();

        return $renderer->renderHead() . $renderer->render();
    }

    /**
     * Creates a tab
     * @param string $type
     * @throws \DebugBar\DebugBarException
     */
    protected function createTab(string $type)
    {
        if(!$this->debugbar->hasCollector($type)) {
            $this->debugbar->addCollector(new MessagesCollector($type));
        }

        if (count(self::$store[$type])) {
            foreach (self::$store[$type] as $message) {
                $fn = key($message);
                $this->debugbar[$type]->$fn($message[$fn]);
            }
        }
    }

    /**
     * Gets the renderer
     * @return \DebugBar\JavascriptRenderer
     */
    protected function getRenderer(): JavascriptRenderer
    {
        return $this->debugbar
            ->getJavascriptRenderer()
            ->setBaseUrl(base_url() . $this->assetsUrl)
            ->addAssets([$this->customCss], []);
    }

}

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

 use Quantum\Handlers\ViewCacheHandler;
 use Quantum\Http\Request;
 use Quantum\Http\Response;
 use Quantum\Middleware\MiddlewareExecutor;
 use Quantum\Libraries\Csrf\Csrf;
 use Quantum\Loader\Loader;
 use Quantum\Di\Di;
 use Quantum\Exceptions\ControllerException;
 use Quantum\Di\Exceptions\DiException;
 use ReflectionException;
 use Quantum\Router\RouteController;
 
 class RouteDispatcher
 {
     public static function handle(Request $request, Response $response): void
     {
         // 1. Apply middleware
         [$request, $response] = (new MiddlewareExecutor())->execute($request, $response);
 
         // 2. Try serving from view cache
         $viewCacheHandler = new ViewCacheHandler();
         if ($viewCacheHandler->serveCachedView(route_uri(), $response)) {
             return;
         }
 
         // 3. Route callback or controller handling
         $callback = route_callback();
 
         if ($callback) {
             call_user_func_array($callback, self::getArgs($callback));
         } else {
             $controller = self::getController();
             $action = self::getAction($controller);
 
             if ($controller->csrfVerification && in_array($request->getMethod(), Csrf::METHODS)) {
                 csrf()->checkToken($request);
             }
 
             if (method_exists($controller, '__before')) {
                 call_user_func_array([$controller, '__before'], self::getArgs([$controller, '__before']));
             }
 
             call_user_func_array([$controller, $action], self::getArgs([$controller, $action]));
 
             if (method_exists($controller, '__after')) {
                 call_user_func_array([$controller, '__after'], self::getArgs([$controller, '__after']));
             }
         }
     }
 
     private static function getController(): RouteController
     {
         $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';
 
         $loader = Di::get(Loader::class);
 
         return $loader->loadClassFromFile(
             $controllerPath,
             function () {
                 return ControllerException::controllerNotFound(current_controller());
             },
             function () {
                 return ControllerException::controllerNotDefined(current_controller());
             }
         );
     }
 
     private static function getAction(RouteController $controller): ?string
     {
         $action = current_action();
 
         if ($action && !method_exists($controller, $action)) {
             throw ControllerException::actionNotDefined($action);
         }
 
         return $action;
     }
 
     private static function getArgs(callable $callable): array
     {
         return Di::autowire($callable, self::routeParams());
     }
 
     private static function routeParams(): array
     {
         return array_map(function ($param) {
             return $param['value'];
         }, route_params());
     }
 }
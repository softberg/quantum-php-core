<?php

return function ($route) {
    $route->group('auth', function ($route) {
        $route->get('/', 'DashboardController', 'index');

        $route->get('emails', 'EmailsController', 'list');
        $route->get('email/[:any]', 'EmailsController', 'single');
        $route->get('email/delete/[:any]', 'EmailsController', 'delete');

        $route->get('logs', 'LogsController', 'list');
        $route->get('logs/view', 'LogsController', 'single');

        $route->get('database', 'DatabaseController', 'list');
        $route->get('database/view', 'DatabaseController', 'single');
        $route->post('database/create', 'DatabaseController', 'create')->middlewares(['CreateTable']);
        $route->post('database/update', 'DatabaseController', 'update');
        $route->get('database/delete', 'DatabaseController', 'delete');
    })->middlewares(['BasicAuth']);
};
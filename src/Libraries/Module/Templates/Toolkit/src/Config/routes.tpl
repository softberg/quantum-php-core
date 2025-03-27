<?php

return function ($route) {
    $route->get('/', 'MainController', 'index');

    $route->get('[:alpha:2]?/emails', 'EmailsController', 'index');
    $route->get('[:alpha:2]?/emails/view', 'EmailsController', 'viewEmail');
    $route->get('[:alpha:2]?/emails/delete', 'EmailsController', 'deleteEmail');

    $route->get('[:alpha:2]?/logs', 'LogsController', 'index');
    $route->get('[:alpha:2]?/logs/view', 'LogsController', 'view');


    $route->get('[:alpha:2]?/database', 'DatabaseController', 'index');
    $route->get('[:alpha:2]?/database/view', 'DatabaseController', 'view');
    $route->post('[:alpha:2]?/database/create', 'DatabaseController', 'create');
    $route->post('[:alpha:2]?/database/update', 'DatabaseController', 'update');
    $route->get('[:alpha:2]?/database/delete', 'DatabaseController', 'delete');
};
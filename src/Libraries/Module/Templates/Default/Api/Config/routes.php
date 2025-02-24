<?php

return function ($route) {
    $route->get('/', 'MainController', 'index');
};
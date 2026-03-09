<?php

return function ($route): void {
    $route->get('[:alpha:2]?/tests', 'TestController', 'tests');
    $route->get('[:alpha:2]?/Test/[id=:any]', 'TestController', 'test');
};

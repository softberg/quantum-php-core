<?php

return function ($route) {
    $route->get("[:alpha:2]?/tests", "TestController", "tests");
    $route->get("[:alpha:2]?/Test/[id=:any]", "TestController", "test");
};
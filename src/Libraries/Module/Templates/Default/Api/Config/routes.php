<?php

return '<?php

use Quantum\Factory\ViewFactory;
use Quantum\Http\Response;

return function ($route) {
    $route->group("openapi", function ($route) {
        $route->get("docs", function (Quantum\Http\Response $response) {
            $response->html(partial("openApi/openApi"));
        });

        $route->get("spec", function (Quantum\Http\Response $response) {
            $fs = Quantum\Di\Di::get(Quantum\Libraries\Storage\FileSystem::class);
            $response->json((array) json_decode($fs->get(modules_dir() . "\Api\Resources\openapi\spec.json")));
        });
    });
    $route->get(\'/\', \'MainController\', \'index\');
};';
<?php
use Quantum\Libraries\Module\ModuleManager;

return '<?php

namespace Modules\\' . ModuleManager::$moduleName . '\Controllers;

use Quantum\Mvc\QtController;
use Quantum\Http\Response;

class MainController extends OpenApiMainController
{
    private $name = "' . ModuleManager::$moduleName . '";
    
    public function index(Response $response)
    {
        $response->json([
        \'status\' => \'success\',
        \'message\' => $this->name . \' module.\' 
        ]);
    }
};';
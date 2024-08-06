<?php

return '<?php

namespace Modules\\' . $this->moduleName . '\Controllers;

use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;

class MainController extends QtController
{
    private $name = "' . $this->moduleName . '";
    
    public function index(Response $response)
    {
        $response->json([
        \'status\' => \'success\',
        \'message\' => $this->name . \' module.\' 
        ]);
    }
};';
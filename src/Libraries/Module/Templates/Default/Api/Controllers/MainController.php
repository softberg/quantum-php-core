<?php

return '<?php

namespace Modules\\' . Quantum\Libraries\Module\ModuleManager::$moduleName . '\Controllers;

use Quantum\Mvc\QtController;
use Quantum\Http\Response;

class MainController extends QtController
{
    /**
     * Status error
     */
    const STATUS_ERROR = \'error\';

    /**
     * Status success
     */
    const STATUS_SUCCESS = \'success\';

    /**
     * CSRF verification
     * @var bool
     */
    public $csrfVerification = false;
    
    /**
     * Action - success response
     * @param Response $response
     */
    public function index(Response $response)
    {
        $response->json([
            \'status\' => \'success\',
            \'message\' => Quantum\Libraries\Module\ModuleManager::$moduleName . \' module.\' 
        ]);
    }
};';
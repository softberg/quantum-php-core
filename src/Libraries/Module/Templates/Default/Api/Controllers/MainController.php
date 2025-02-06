<?php

use Quantum\Libraries\Module\ModuleManager;

$moduleManager = ModuleManager::getInstance();

return '<?php

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

namespace '. $moduleManager->getBaseNamespace() .'\\' . $moduleManager->getModuleName() . '\Controllers;

use Quantum\Mvc\QtController;
use Quantum\Http\Response;

/**
 * Class MainController
 * @package Modules\Api
 */
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
            \'message\' => $moduleManager->getModuleName() . \' module.\' 
        ]);
    }
};';
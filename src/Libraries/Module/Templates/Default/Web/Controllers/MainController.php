<?php

return '<?php

namespace Modules\\' . Quantum\Libraries\Module\ModuleManager::$moduleName . '\Controllers;

use Quantum\Libraries\Asset\Asset;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;

class MainController extends QtController
{

    /**
     * Main layout
     */
    const LAYOUT = \'layouts/main\';

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before(ViewFactory $view)
    {
        $view->setLayout(static::LAYOUT, [
            new Asset(Asset::CSS, \'css/materialize.min.css\', null, -1, [\'media="screen,projection"\']),
            new Asset(Asset::CSS, \'css/custom.css\'),
            new Asset(Asset::JS, \'js/jquery-3.7.1.min.js\'),
            new Asset(Asset::JS, \'js/materialize.min.js\'),
            new Asset(Asset::JS, \'js/custom.js\')
        ]);
    }
    
   /**
     * Action - display home page
     * @param Response $response
     * @param ViewFactory $view
     */
    public function index(Response $response, ViewFactory $view)
    {   
        $view->setParams([
            \'title\' => config()->get(\'app_name\'),
        ]);
        
        $response->html($view->render(\'index\'));
    }
};';
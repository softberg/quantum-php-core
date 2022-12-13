<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.0
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Console\QtCommand;
use Quantum\Di\Di;

/**
 * Class OpenApiUiAssetsCommand
 * @package Quantum\Console\Commands
 */
class ModuleGenerateCommand extends QtCommand
{

    /**
     * File System
     * @var FileSystem
     */
    protected $fs;

    /**
     * Command name
     * @var string
     */
    protected $name = 'module:generate';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generate new module';

    /**
     * Command arguments
     * @var string[][]
     */
    protected $args = [
        ['module', 'required', 'The module name'],
    ];

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will create files for new module';

    /**
     * Executes the command
     * @throws \Quantum\Exceptions\FileSystemException
     * @throws \Quantum\Exceptions\DiException
     */
    public function exec()
    {
        $this->fs = Di::get(FileSystem::class);
        $newModuleName = ucfirst($this->getArgument('module'));

        $modulesConfigPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
        $modules = require_once $modulesConfigPath;

        foreach ($modules['modules'] as $module => $options) {
            if ($module == $newModuleName  || $options['prefix'] == strtolower($newModuleName)) {
                $this->error('A module or prefix named ' . $newModuleName . ' already exists');
                return;
            }
        }

        $this->fs->put(
            $modulesConfigPath,
            str_replace(
                "'modules' => [",
                $this->addModuleConfig($newModuleName),
                $this->fs->get($modulesConfigPath)
            )
        );

        $this->fs->makeDirectory('modules/' . $newModuleName);
        $this->fs->makeDirectory('modules/' . $newModuleName . '/Controllers');
        $this->fs->makeDirectory('modules/' . $newModuleName . '/Models');
        $this->fs->makeDirectory('modules/' . $newModuleName . '/Config');
        $this->fs->makeDirectory('modules/' . $newModuleName . '/Views');
        $this->fs->makeDirectory('modules/' . $newModuleName . '/Views/layouts');
        $this->fs->put('modules/' . $newModuleName . '/Controllers/MainController.php', $this->controllerTemplate($newModuleName));
        $this->fs->put('modules/' . $newModuleName . '/Views/index.php', $this->viewTemplate($newModuleName));
        $this->fs->put('modules/' . $newModuleName . '/Views/layouts/main.php', $this->viewLayoutsTemplate());
        $this->fs->put('modules/' . $newModuleName . '/Config/routes.php', $this->routesTemplate());

        $this->info($newModuleName . ' module resources successfully published');
    }

    /**
     * Add module to config
     * @param string $module
     * @return string
     */
    private function addModuleConfig(string $module): string
    {
        return "'modules' => [
        '" . $module . "' => [
            'prefix' => '" . strtolower($module) . "',
            'enabled' => false,
        ],";
    }

    /**
     * Controller template
     * @param string $moduleName
     * @return string
     */
    private function controllerTemplate($moduleName)
    {
        return '<?php

namespace Modules\\' . $moduleName . '\Controllers;

use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;

class MainController extends QtController
{
    public function index(Response $response, ViewFactory $view)
    {
        $view->setLayout(\'layouts/main\');
        $view->setParams([
            \'title\' => config()->get(\'app_name\'),
        ]);
        $response->html($view->render(\'index\'));
    }
};';
    }

    /**
     * View template
     * @param string $moduleName
     * @return string
     */
    private function viewTemplate($moduleName)
    {
        return '<div class="main-wrapper teal accent-4">
    <div class="container wrapper">
        <div class="center-align white-text">
            <div class="logo-block">
                <img src="<?php echo base_url() ?>/assets/images/quantum-logo-white.png" alt="<?php echo env(\'APP_NAME\') ?>" />
            </div>
            <h1>' . strtoupper($moduleName) . ' HOME PAGE</h1>
        </div>
    </div>
</div>
<ul class="bg-bubbles">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>';
    }

    /**
     * viewLayouts template
     * @return string
     */
    private function viewLayoutsTemplate()
    {
        return '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="shortcut icon" href="<?php echo asset()->url(\'images/favicon.ico\') ?>">
        <link rel=\'stylesheet\' href=\'<?php echo asset()->url(\'css/materialize.min.css\') ?>\' type=\'text/css\' media=\'screen,projection\' />
        <link rel=\'stylesheet\' href=\'<?php echo asset()->url(\'css/custom.css\') ?>\' type=\'text/css\' />
    </head>
    <body>

        <main><?php echo view() ?></main>

        <?php echo debugbar() ?>

        <script type=\'text/javascript\' src=\'<?php echo asset()->url(\'js/materialize.min.js\') ?>\'></script>
        <script type=\'text/javascript\' src=\'<?php echo asset()->url(\'js/custom.js\') ?>\'></script>
    </body>
</html>';
    }

    /**
     * Routes template
     * @return string
     */
    private function routesTemplate()
    {
        return '<?php

use Quantum\Factory\ViewFactory;
use Quantum\Http\Response;

return function ($route) {
    $route->get(\'[:alpha:2]?\', \'MainController\', \'index\');
};';
    }
}

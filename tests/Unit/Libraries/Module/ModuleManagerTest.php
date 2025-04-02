<?php

namespace Quantum\Tests\Unit\Libraries\Module;

use Quantum\Libraries\Module\ModuleManager;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;
use Quantum\App\App;
use Exception;
use Mockery;

class ModuleManagerTest extends AppTestCase
{
    private $modulesConfigPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->modulesConfigPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
    }

    public function testCreateModule(){

        $moduleManager = $this->getModuleManager();

        $moduleManager->writeContents();

        $moduleManager->addModuleConfig();

        $modules = $this->fs->require($this->modulesConfigPath);;

        $this->assertEquals("Api", $moduleManager->getModuleName());

        $this->assertArrayHasKey("Api", $modules);

        $this->assertEquals("api", $modules["Api"]["prefix"]);

        $mainController = new \Quantum\Tests\_root\modules\Api\Controllers\MainController();

        $this->assertInstanceOf(QtController::class, $mainController);

        $response = new Response();

        $mainController->index($response);

        $result = json_decode($response->getContent());

        $this->assertEquals("success", $result->status);

        $this->assertEquals("Api module.", $result->message);
    }

    public function testAddModuleConfigWithoutModule(){
        $moduleManager = $this->getModuleManager();

        $this->expectException(Exception::class);

        $this->expectExceptionMessage("Module directory does not exist, skipping config update.");

        $moduleManager->addModuleConfig();
    }

    public function testIncompleteCopyTemplates(){
        $moduleManager = Mockery::mock(ModuleManager::class, ["Api", "DefaultApi", "yes", false])->makePartial();

        $moduleManager->shouldAllowMockingProtectedMethods();

        $moduleManager->shouldReceive('validateModuleFiles')->andReturn(false);

        $this->expectException(Exception::class);

        $this->expectExceptionMessage('Module creation incomplete: missing files.');

        $moduleManager->writeContents();
    }

    public function testInvalidTemplate(){
        $moduleManager = $this->getModuleManager("NotExists", "notExists");

        $this->expectException(Exception::class);

        $moduleManager->writeContents();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $moduleConfigs = $this->fs->require($this->modulesConfigPath);

        $apiModulePath = App::getBaseDir() . DS . "modules" . DS . "Api";

        if(isset($moduleConfigs["Api"])){
            unset($moduleConfigs["Api"]);

            $this->fs->put(
                $this->modulesConfigPath,
                "<?php\n\nreturn " . export($moduleConfigs) . ";\n"
            );
        }

        if($this->fs->isDirectory($apiModulePath)) {
            deleteDirectoryWithFiles($apiModulePath);
        }
    }

    private function getModuleManager($name = 'Api', $template = "DefaultApi"): ModuleManager
    {
        return new ModuleManager($name, $template, "yes", false);
    }
}
<?php

namespace Quantum\Tests\Libraries\Module;

use Quantum\Libraries\Module\ModuleManager;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;
use Quantum\App\App;
use Exception;

class ModuleManagerTest extends AppTestCase
{
    private $modulesConfigPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->modulesConfigPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
    }

    public function testAddModuleConfig(){

        $moduleManager = ModuleManager::createInstance("Api", "api", "", true);

        $moduleManager->addModuleConfig();

        $modules = $this->getModuleConfigs();

        $this->assertEquals("Api", $moduleManager->getModuleName());

        $this->assertArrayHasKey("Api", $modules);

        $this->assertEquals("api", $modules["Api"]["prefix"]);
    }

    public function testWriteContents(){

        $moduleManager = ModuleManager::createInstance("Api", "api", "", true);

        $moduleManager->writeContents();

        $mainController = new \Quantum\Tests\_root\modules\Api\Controllers\MainController();

        $this->assertInstanceOf(QtController::class, $mainController);

        $response = new Response();

        $mainController->index($response);

        $result = json_decode($response->getContent());

        $this->assertEquals("success", $result->status);

        $this->assertEquals("Api module.", $result->message);
    }

    public function testInvalidTemplate(){
        $this->setPrivateProperty(ModuleManager::class, "instance", null);

        $moduleManager = ModuleManager::createInstance("NotExists", "notExists", "yes", true);

        $this->expectException(Exception::class);

        $moduleManager->writeContents();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $moduleConfigs = $this->getModuleConfigs();

        $apiModulePath = App::getBaseDir() . DS . "modules" . DS . "Api";

        if(isset($moduleConfigs["Api"])){
            unset($moduleConfigs["Api"]);

            $this->fs->put(
                $this->modulesConfigPath,
                "<?php\n\nreturn " . export($moduleConfigs) . ";\n"
            );
        }

        if($this->fs->isDirectory($apiModulePath)) {
            $this->deleteDirectory($apiModulePath);
        }
    }

    private function deleteDirectory(string $dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            $path = $dir . DS . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        if ($dir != uploads_dir()) {
            rmdir($dir);
        }
    }

    private function getModuleConfigs(){
        return $this->fs->require($this->modulesConfigPath);
    }

}
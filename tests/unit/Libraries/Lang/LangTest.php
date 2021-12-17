<?php

namespace Quantum\Libraries\Lang {

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Tests\Libraries\Lang {

    use PHPUnit\Framework\TestCase;
    use Quantum\Routes\RouteController;
    use Quantum\Libraries\Lang\Lang;
    use Quantum\Di\Di;
    use Quantum\App;

    /**
     * @runTestsInSeparateProcesses
     */
    class LangTest extends TestCase
    {

        private $lang;

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

            Di::loadDefinitions();

            config()->set('langs', ['en', 'ru', 'am']);

            config()->set('lang_default', 'en');

            config()->set('lang_segment', 1);

            $this->lang = Lang::getInstance();

            $this->lang->setLang('en');

            RouteController::setCurrentRoute([
                "route" => "api-signin",
                "method" => "POST",
                "controller" => "SomeController",
                "action" => "signin",
                "module" => "test",
            ]);
        }

        public function testLangLoad()
        {
            $this->assertEmpty($this->lang->getTranslations());

            $this->lang->load();

            $this->assertNotEmpty($this->lang->getTranslations());
        }

        public function testLangGetSet()
        {
            $this->lang->load();

            $this->assertEquals('en', $this->lang->getLang());

            $this->lang->setLang('ru');

            $this->assertEquals('ru', $this->lang->getLang());
        }

        public function testGetTranslation()
        {
            $this->lang->load();

            $this->assertEquals('Testing', $this->lang->getTranslation('custom.test'));

            $this->assertEquals('Information about the new feature', $this->lang->getTranslation('custom.info', 'new'));

            $this->assertEquals('custom.not-exists', $this->lang->getTranslation('custom.not-exists'));
        }

    }

}

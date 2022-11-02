<?php

namespace Quantum\Libraries\Lang {

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Tests\Libraries\Lang {

    use Quantum\Router\RouteController;
    use Quantum\Libraries\Lang\Lang;
    use Quantum\Tests\AppTestCase;

    /**
     * @runTestsInSeparateProcesses
     */
    class LangTest extends AppTestCase
    {

        private $lang;

        public function setUp(): void
        {
            parent::setUp();

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
            $this->lang->flush();

            $this->assertEmpty($this->lang->getTranslations());

            $this->lang->load();

            $this->assertNotEmpty($this->lang->getTranslations());
        }

        public function testLangGetSet()
        {
            $this->assertEquals('en', $this->lang->getLang());

            $this->lang->setLang('ru');

            $this->assertEquals('ru', $this->lang->getLang());
        }

        public function testGetTranslation()
        {
            $this->assertEquals('Testing', $this->lang->getTranslation('custom.test'));

            $this->assertEquals('Information about the new feature', $this->lang->getTranslation('custom.info', 'new'));

            $this->assertEquals('custom.not-exists', $this->lang->getTranslation('custom.not-exists'));
        }

    }

}

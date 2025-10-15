<?php

namespace Quantum\Tests\Unit\Libraries\Lang;

use Quantum\Libraries\Lang\Translator;
use Quantum\Router\RouteController;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Lang\Lang;

class LangTest extends AppTestCase
{

    private $lang;

    public function setUp(): void
    {
        parent::setUp();

        $translator = new Translator('en');
        $this->lang = new Lang('en', true, $translator);

        RouteController::setCurrentRoute([
            "route" => "api-signin",
            "method" => "POST",
            "controller" => "SomeController",
            "action" => "signin",
            "module" => "Test",
        ]);
    }

    public function testLangGetSet()
    {
        $this->assertEquals('en', $this->lang->getLang());

        $this->lang->setLang('ru');

        $this->assertEquals('ru', $this->lang->getLang());
    }

    public function testLangIsEnabled(): void
    {
        $this->assertTrue($this->lang->isEnabled());

        $langDisabled = new Lang('en', false, new Translator('en'));

        $this->assertFalse($langDisabled->isEnabled());
    }


    public function testLangLoadAndGetTranslation(): void
    {
        $this->lang->flush();

        $this->assertEquals('custom.test', $this->lang->getTranslation('custom.test'));

        $this->lang->load();

        $this->assertEquals('Testing', $this->lang->getTranslation('custom.test'));

        $this->assertEquals('Information about the new feature', $this->lang->getTranslation('custom.info', ['new']));
    }

    public function testLangFlushResetsTranslations(): void
    {
        $this->lang->load();

        $this->assertEquals('Testing', $this->lang->getTranslation('custom.test'));

        $this->lang->flush();

        $this->assertEquals('test', $this->lang->getTranslation('test'));
    }
}
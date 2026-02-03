<?php

namespace Quantum\Tests\Unit\Libraries\Lang;

use Quantum\Libraries\Lang\Translator;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Quantum\Di\Di;
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

        $route = new Route(
            ['POST'],
            '/api-signin',
            'SomeController',
            'signin',
            null
        );
        $route->module('Test');

        $matchedRoute = new MatchedRoute($route, []);
        Request::setMatchedRoute($matchedRoute);

        if (!Di::isRegistered(Request::class)) {
            $request = new Request();
            $request->create('POST', '/api-signin');
            Di::set(Request::class, $request);
        }
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

<?php

namespace Quantum\Tests\Unit\Libraries\Lang\Factories;

use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Lang\Factories\LangFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Lang\Lang;

class LangFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(LangFactory::class, 'instance', null);
    }

    public function testLangFactoryGetLangInstance()
    {
        $lang = LangFactory::get();

        $this->assertInstanceOf(Lang::class, $lang);

        $this->assertEquals('en', $lang->getLang());

        $this->assertTrue($lang->isEnabled());
    }

    public function testLangFactoryGetReturnsSameInstance()
    {
        $first = LangFactory::get();

        $second = LangFactory::get();

        $this->assertSame($first, $second);
    }

    public function testLangFactoryDetectedFromRouteParameter()
    {
        $this->testRequest('http://127.0.0.1/es/api/rest');

        $lang = LangFactory::get();

        $this->assertEquals('es', $lang->getLang());
    }

    public function testLangFactoryDetectedFromQueryParameter()
    {
        $this->testRequest('http://127.0.0.1/api/rest?lang=es');

        $lang = LangFactory::get();

        $this->assertEquals('es', $lang->getLang());
    }

    public function testLangFactoryDetectedFromAcceptedLangParameter()
    {
        $this->testRequest('http://127.0.0.1/api/rest', 'GET', [], ['Accept-Language' => 'es, en;q=0.8, fr;q=0.6']);

        $lang = LangFactory::get();

        $this->assertEquals('es', $lang->getLang());
    }

    public function testLangFactoryFallsBackToDefaultIfNoLangDetected()
    {
        $this->testRequest('http://127.0.0.1/api/rest');

        $lang = LangFactory::get();

        $this->assertEquals('en', $lang->getLang());
    }

    public function testLangFactoryGetFallsBackToDefaultIfProvidedLangIsNotSupported()
    {
        config()->set('lang', [
            'enabled' => true,
            'default' => 'en',
            'supported' => ['en', 'es'],
            'url_segment' => 1
        ]);

        $this->testRequest('http://127.0.0.1/fr/api/rest');

        $lang = LangFactory::get();

        $this->assertEquals('en', $lang->getLang());

        $this->setPrivateProperty(LangFactory::class, 'instance', null);

        $this->testRequest('http://127.0.0.1/api/rest?lang=fr');

        $lang = LangFactory::get();

        $this->assertEquals('en', $lang->getLang());

        $this->setPrivateProperty(LangFactory::class, 'instance', null);

        $this->testRequest('http://127.0.0.1/api/rest', 'GET', [], ['Accept-Language' => 'fr, en;q=0.8, fr;q=0.6']);

        $lang = LangFactory::get();

        $this->assertEquals('en', $lang->getLang());
    }

    public function testLangFactoryThrowsErrorIfNoDefaultLangFound()
    {
        config()->set('lang', [
            'enabled' => true,
            'default' => null,
            'supported' => ['en', 'es'],
            'url_segment' => 1
        ]);

        $this->testRequest('http://127.0.0.1/fr/api/rest');

        $this->expectException(LangException::class);

        $this->expectExceptionMessage('Misconfigured lang default config');

        LangFactory::get();
    }
}
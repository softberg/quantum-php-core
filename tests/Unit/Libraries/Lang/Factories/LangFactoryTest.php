<?php

namespace Quantum\Tests\Unit\Libraries\Lang;

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

        config()->set('lang', [
            'enabled' => true,
            'default' => 'en',
            'supported' => ['en', 'es'],
            'url_segment' => 1
        ]);
    }

    public function testLangFactoryGetLangInstance(): void
    {
        $lang = LangFactory::get();

        $this->assertInstanceOf(Lang::class, $lang);

        $this->assertEquals('en', $lang->getLang());

        $this->assertTrue($lang->isEnabled());
    }

    public function testLangFactoryGetReturnsSameInstance(): void
    {
        $first = LangFactory::get();

        $second = LangFactory::get();

        $this->assertSame($first, $second);
    }

    public function testLangFactoryGetFallsBackToDefaultIfSegmentInvalid(): void
    {
        config()->set('lang.url_segment', 0);

        $lang = LangFactory::get();

        $this->assertEquals('en', $lang->getLang());
    }

    public function testGetThrowsIfNoDefaultConfigured(): void
    {
        $this->expectException(LangException::class);

        config()->set('lang.default', null);

        LangFactory::get();
    }
}
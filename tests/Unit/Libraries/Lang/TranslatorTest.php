<?php

namespace Quantum\Tests\Unit\Libraries\Lang;

use Quantum\Libraries\Lang\Translator;
use Quantum\Tests\Unit\AppTestCase;

class TranslatorTest extends AppTestCase
{
    public function testTranslatorConstruct(): void
    {
        $translator = new Translator('en');

        $this->assertInstanceOf(Translator::class, $translator);
    }

    public function testTranslatorLoadTranslations(): void
    {
        $translator = new Translator('en');

        $translator->loadTranslations();

        $this->assertEquals('Testing', $translator->get('custom.test'));

        $this->assertEquals('Information about the value feature', $translator->get('custom.info', ['param' => 'value']));
    }

    public function testTranslatorGetTranslation(): void
    {
        $translator = new Translator('en');

        $translator->loadTranslations();

        $result = $translator->get('custom.info', ['new']);

        $this->assertEquals('Information about the new feature', $result);
    }
}

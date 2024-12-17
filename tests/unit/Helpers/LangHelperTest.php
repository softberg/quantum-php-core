<?php

namespace Quantum\Tests\Helpers;

use Quantum\Libraries\Lang\Lang;
use Quantum\Tests\AppTestCase;

class LangHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCurrentLang()
    {
        $this->assertEquals('en', current_lang());

        Lang::getInstance()->setLang('am');

        $this->assertEquals('am', current_lang());
    }

    public function testHelperT()
    {
        $translations = [
            'custom' => [
                'label' => 'Testing',
                'info' => 'Information about the new feature'
            ]
        ];

        Lang::getInstance()->setTranslations($translations);

        $this->assertEquals('Testing', t('custom.test'));

        $this->assertEquals('Information about the new feature', t('custom.info'));
    }

    public function testHelperTWithParams()
    {
        $translations = [
            'custom' => [
                'info' => 'Information about the new feature: {%1}'
            ]
        ];

        Lang::getInstance()->setTranslations($translations);

        $this->assertEquals('Information about the new feature: new', t('custom.info', 'new'));
    }

    public function testHelperUnderscoreT()
    {
        $translations = [
            'custom' => [
                'test' => 'Testing'
            ]
        ];

        Lang::getInstance()->setTranslations($translations);

        ob_start();

        _t('custom.test');
        $output = ob_get_clean();

        $this->assertEquals('Testing', $output);
    }

    public function testHelperTFail()
    {
        $translations = [
            'custom' => [
                'label' => 'Testing',
            ]
        ];

        Lang::getInstance()->setTranslations($translations);

        $this->assertEquals('custom.non_existing_key', t('custom.non_existing_key'));
    }

}
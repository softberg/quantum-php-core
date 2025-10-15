<?php

namespace Quantum\Tests\Unit\Libraries\Lang\Helpers;

use Quantum\Libraries\Lang\Factories\LangFactory;
use Quantum\Tests\Unit\AppTestCase;

class LangHelperFunctionsTest extends AppTestCase
{

    private $lang;

    public function setUp(): void
    {
        parent::setUp();

        $this->lang = LangFactory::get();

        $this->lang->load();
    }

    public function testCurrentLang()
    {
        $this->assertEquals('en', current_lang());

        $this->lang->setLang('am');

        $this->assertEquals('am', current_lang());
    }

    public function testHelperT()
    {
        $this->assertEquals('Testing', t('custom.test'));

        $this->assertEquals('Information about the new feature', t('custom.info', ['new']));
    }

    public function testHelperUnderscoreT()
    {
        ob_start();

        _t('custom.test');

        $output = ob_get_clean();

        $this->assertEquals('Testing', $output);
    }

    public function testHelperTFail()
    {
        $this->assertEquals('custom.non_existing_key', t('custom.non_existing_key'));
    }
}
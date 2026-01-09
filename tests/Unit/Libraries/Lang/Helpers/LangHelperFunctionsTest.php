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

        $this->setPrivateProperty(LangFactory::class, 'instance', null);

        $this->lang = LangFactory::get();

        $this->lang->load();
    }

    public function testLangHelperCurrentLang()
    {
        $this->assertEquals('en', current_lang());

        $this->lang->setLang('am');

        $this->assertEquals('am', current_lang());
    }

    public function testLangHelperT()
    {
        $this->assertEquals('Testing', t('custom.test'));

        $this->assertEquals('Information about the new feature', t('custom.info', ['new']));
    }

    public function testLangHelperUnderscoreT()
    {
        ob_start();

        _t('custom.test');

        $output = ob_get_clean();

        $this->assertEquals('Testing', $output);
    }

    public function testLangHelperTFail()
    {
        $this->assertEquals('custom.non_existing_key', t('custom.non_existing_key'));
    }
}

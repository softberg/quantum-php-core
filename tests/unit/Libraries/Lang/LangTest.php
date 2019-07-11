<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Lang\Lang;


class LangTest extends TestCase
{

    private $loader;

    private $langData = [
        'learn_more' => 'Learn more',
        'info' => 'Information about {%1} feature',
        'test' => 'Testing'
    ];


    public function setUp(): void
    {
        $this->loader = Mockery::mock('Quantum\Loader\Loader');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testLangLoad()
    {
        $this->assertEmpty(Lang::getTranslations());

        $this->loader->shouldReceive('load')
            ->andReturn($this->langData);

        Lang::load($this->loader, 'common');

        $this->assertNotEmpty(Lang::getTranslations());

    }

    public function testLangGetSet()
    {
        $this->assertNull(Lang::get());

        Lang::set('en');

        $this->assertEquals('en', Lang::get());
    }

    public function testGetTranslation()
    {
        $this->assertEquals('Testing', Lang::getTranslation('common.test'));

        $this->assertEquals('Information about new feature', Lang::getTranslation('common.info', 'new'));

        $this->assertEquals('common.not-exists', Lang::getTranslation('common.not-exists'));

        $this->loader->shouldReceive('load')
            ->andReturn($this->langData);

        Lang::load($this->loader, 'other');

        $this->assertEquals('Testing', Lang::getTranslation('other.test'));

    }

}

namespace Quantum\Libraries\Lang;

function _message($subject, $params)
{
    if (is_array($params)) {
        return preg_replace_callback('/{%\d+}/', function () use (&$params) {
            return array_shift($params);
        }, $subject);
    } else {
        return preg_replace('/{%\d+}/', $params, $subject);
    }
}
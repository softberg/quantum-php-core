<?php

namespace Quantum\Libraries\Lang {

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

    function modules_dir()
    {
        return __DIR__ . DS . 'modules';
    }

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Lang\Lang;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;

    class LangTest extends TestCase
    {

        private $lang;
        private $langDir;
        private $loaderMock;
        private $langData = [
            'learn_more' => 'Learn more',
            'info' => 'Information about {%1} feature',
            'test' => 'Testing'
        ];

        public function setUp(): void
        {
            $this->loaderMock = Mockery::mock('Quantum\Loader\Loader');

            $this->loaderMock->shouldReceive('setup')->andReturn($this->loaderMock);

            $this->loaderMock->shouldReceive('load')->andReturn($this->langData);

            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $loader->loadFile(dirname(__DIR__, 4) . DS . 'src' . DS . 'constants.php');

            config()->set('langs', ['en', 'ru', 'am']);

            config()->set('lang_default', 'en');

            config()->set('lang_segment', 1);

            $this->langDir = \Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources' . DS . 'lang' . DS . 'en';

            if (!is_dir($this->langDir)) {
                mkdir($this->langDir, 0777, true);
            }

            file_put_contents($this->langDir . DS . 'custom.php', null);

            $this->lang = Lang::getInstance(new FileSystem);

            $this->lang->setLang('en');

            $reflectionClass = new \ReflectionClass(Lang::class);

            $reflectionProperty = $reflectionClass->getProperty('translations');

            $reflectionProperty->setAccessible(true);

            $reflectionProperty->setValue([]);
        }

        public function tearDown(): void
        {
            Mockery::close();

            unlink($this->langDir . DS . 'custom.php');

            sleep(1);
            rmdir($this->langDir);

            sleep(1);
            rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources' . DS . 'lang');

            sleep(1);
            rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources');

            sleep(1);
            rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module());

            sleep(1);
            rmdir(\Quantum\Libraries\Lang\modules_dir());
        }

        public function testLangLoad()
        {
            $this->assertEmpty($this->lang->getTranslations());

            $this->lang->load($this->loaderMock);

            $this->assertNotEmpty($this->lang->getTranslations());
        }

        public function testLangGetSet()
        {
            $this->lang->load($this->loaderMock);

            $this->assertEquals('en', $this->lang->getLang());

            $this->lang->setLang('ru');

            $this->assertEquals('ru', $this->lang->getLang());
        }

        public function testGetTranslation()
        {
            $this->lang->load($this->loaderMock);

            $this->assertEquals('Testing', $this->lang->getTranslation('custom.test'));

            $this->assertEquals('Information about new feature', $this->lang->getTranslation('custom.info', 'new'));

            $this->assertEquals('custom.not-exists', $this->lang->getTranslation('custom.not-exists'));
        }

    }

}

<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Config\Config;
use Quantum\Libraries\Lang\Lang;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Factory\ViewFactory;
use Quantum\Loader\Loader;
use Quantum\Routes\Router;
use Quantum\Http\Request;
use Quantum\Http\Response;
use Mockery;

class HelperTest extends TestCase
{

    private $router;
    private $request;
    private $response;
    private $session;
    private $sessionData = [];

    public function setUp(): void
    {

        $loader = new Loader();

        $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

        $loader->loadFile(dirname(__DIR__, 3) . DS . 'src' . DS . 'constants.php');

        $this->request = new Request();

        $this->response = new Response();

        $this->router = new Router($this->request, $this->response);

        $cryptor = Mockery::mock('Quantum\Libraries\Encryption\Cryptor');

        $cryptor->shouldReceive('encrypt')->andReturnUsing(function ($arg) {
            return base64_encode($arg);
        });

        $cryptor->shouldReceive('decrypt')->andReturnUsing(function ($arg) {
            return base64_decode($arg);
        });

        $this->session = new Session($this->sessionData, $cryptor);
    }

    public function testRandomNumber()
    {
        $this->assertIsInt(random_number());

        $this->assertIsInt(random_number(5));

        $this->assertEquals(0,random_number(''));
    }

    public function testBaseUrl()
    {
        $this->request->create('GET', 'https://test.com');

        $this->assertEquals('https://test.com', base_url());
    }

    public function testCurrentUrl()
    {
        $this->request->create('GET', 'http://test.com/post/12');

        $this->assertEquals('http://test.com/post/12', current_url());

        $this->request->create('GET', 'http://test.com/user/12?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com/user/12?firstname=John&lastname=Doe', current_url());

        $this->request->create('GET', 'http://test.com:8080/?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com:8080/?firstname=John&lastname=Doe', current_url());
    }

    public function testRedirecting()
    {
        $this->assertFalse($this->response->hasHeader('Location'));
        
        try {
            redirect('/home');
        } catch (StopExecutionException $e) {}
       
        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/home', $this->response->getHeader('Location'));

    }

    public function testRedirectWithOldData()
    {
        $this->request->create('POST', '/', ['firstname' => 'Josh', 'lastname' => 'Doe']);
        
        try {
            redirectWith('/signup', $this->request->all());
        } catch (StopExecutionException $e) {}

        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/signup', $this->response->getHeader('Location'));

        $this->assertEquals('Josh', old('firstname'));

        $this->assertEquals('Doe', old('lastname'));
    }

    public function testSlugify()
    {
        $this->assertEquals('text-with-spaces', slugify('Text with spaces'));

        $this->assertEquals('ebay-com-itm-dual-arm-tv-trkparms-aid-3d111001-26brand-3dunbranded-trksid-p2380057', slugify('ebay.com/itm/DUAL-ARM-TV/?_trkparms=aid%3D111001%26brand%3DUnbranded&_trksid=p2380057'));
    }

    public function testDirHelpers()
    {
        $this->assertSame(base_dir(), BASE_DIR);

        $this->assertSame(modules_dir(), MODULES_DIR);

        $this->assertSame(public_dir(), PUBLIC_DIR);

        $this->assertSame(uploads_dir(), UPLOADS_DIR);

        $this->assertSame(assets_dir(), ASSETS_DIR);
    }

    public function testAsset()
    {
        $this->assertSame(asset('style.css'), asset_url() . '/style.css');

        $this->assertSame(asset('js/script.js'), asset_url() . '/js/script.js');
    }

    public function testPublishedAssets()
    {
        $assetManager = new AssetManager();

        $assetManager->registerCSS([
            'fakepath/style.css',
            'fakepath/responsive.css'
        ]);

        $assetManager->registerJS([
            'fakepath/bootstrap.js',
            'fakepath/bootstrap-datepicker.min.js'
        ]);

        $assetManager->publishCSS();
        $assetManager->publishJS();

        $expectedOutput = '<link rel="stylesheet" type="text/css" href="' . asset_url() . '/fakepath/style.css">' . PHP_EOL .
                '<link rel="stylesheet" type="text/css" href="' . asset_url() . '/fakepath/responsive.css">' . PHP_EOL .
                '<script src="' . asset_url() . '/fakepath/bootstrap.js"></script>' . PHP_EOL .
                '<script src="' . asset_url() . '/fakepath/bootstrap-datepicker.min.js"></script>' . PHP_EOL;


        $this->expectOutputString($expectedOutput);

        assets('css');
        assets('js');
    }

    public function testMvcHelpers()
    {
        $this->router->setRoutes([
            [
                "route" => "api-signin",
                "method" => "POST",
                "controller" => "AuthController",
                "action" => "signin",
                "module" => "Api",
                "middlewares" => ["guest", "anonymous"]
            ],
            [
                "route" => "api-user/[:num]",
                "method" => "GET",
                "controller" => "AuthController",
                "action" => "signout",
                "module" => "Api",
                "middlewares" => ["user"]
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/api-signin');

        $this->router->findRoute();

        $middlewares = current_middlewares();

        $this->assertIsArray($middlewares);

        $this->assertEquals('guest', $middlewares[0]);

        $this->assertEquals('anonymous', $middlewares[1]);

        $this->assertEquals('Api', current_module());

        $this->assertEquals('AuthController', current_controller());

        $this->assertEquals('signin', current_action());

        $this->assertEquals('api-signin', current_route());

        $this->assertEmpty(current_route_args());

        $this->request->create('GET', 'http://testdomain.com/api-user/12');

        $this->router->findRoute();

        $this->assertEquals([12], current_route_args());

        $this->assertEquals('(\/)?api-user(\/)([0-9]+)', current_route_pattern());

        $this->assertEquals('GET', current_route_method());

        $this->assertEquals('api-user/12', current_route_uri());
    }

    public function testView()
    {
        $viewsDir = \Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module() . DS . 'Views';

        $layoutContent = '<html><head></head><body></body></html>';

        $viewContent = '<p>Hello world, this is rendered view</p>';

        if (!is_dir($viewsDir))
            mkdir($viewsDir, 0777, true);

        file_put_contents($viewsDir . DS . 'layout.php', $layoutContent);

        file_put_contents($viewsDir . DS . 'index.php', $viewContent);

        $viewFactory = new ViewFactory();

        $viewFactory->setLayout('layout');

        $viewFactory->render('index');

        $this->assertEquals($viewContent, view());

        unlink($viewsDir . DS . 'layout.php');

        unlink($viewsDir . DS . 'index.php');

        sleep(1);
        rmdir($viewsDir);

        sleep(1);
        rmdir(\Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module());

        sleep(1);
        rmdir(\Quantum\Mvc\modules_dir());
    }

    public function testPartial()
    {
        $viewsDir = \Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module() . DS . 'Views';

        $partialContent = '<p>Hello <?php echo ($name ?? "World") ?>, this is rendered partial view</p>';

        if (!is_dir($viewsDir))
            mkdir($viewsDir, 0777, true);

        file_put_contents($viewsDir . DS . 'partial.php', $partialContent);

        $this->assertEquals('<p>Hello World, this is rendered partial view</p>', partial('partial'));

        $this->assertEquals('<p>Hello John, this is rendered partial view</p>', partial('partial', ['name' => 'John']));

        unlink($viewsDir . DS . 'partial.php');

        sleep(1);
        rmdir($viewsDir);

        sleep(1);
        rmdir(\Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module());

        sleep(1);
        rmdir(\Quantum\Mvc\modules_dir());
    }

    public function testConfigHelper()
    {
        $configData = [
            'langs' => ['en', 'es'],
            'lang_default' => 'en',
            'debug' => 'DEBUG',
            'test' => 'Testing'
        ];

        $loader = Mockery::mock('Quantum\Loader\Loader');

        $loader->shouldReceive('setup')->andReturn($loader);

        $loader->shouldReceive('load')->andReturn($configData);

        Config::getInstance()->load($loader);

        $this->assertFalse(config()->has('not-exists'));

        $this->assertEquals('Not found', config()->get('not-exists', 'Not found'));

        $this->assertEquals(config()->get('test', 'Testing'), 'Testing');

        $this->assertNull(config()->get('new-key'));

        config()->set('new-key', 'New value');

        $this->assertTrue(config()->has('new-key'));

        $this->assertEquals('New value', config()->get('new-key'));

        config()->delete('new-key');

        $this->assertFalse(config()->has('new-key'));
    }

    public function testGetEnvValue()
    {
        $this->assertNull(env('NEW_ENV_KEY'));

        putenv('NEW_ENV_KEY=New value');

        $this->assertEquals('New value', env('NEW_ENV_KEY'));
    }

    public function testSessionHelper()
    {
        $this->assertInstanceOf('Quantum\Libraries\Session\Session', session());

        $this->assertFalse(session()->has('test'));

        session()->set('test', 'Testing');

        $this->assertTrue(session()->has('test'));

        $this->assertEquals('Testing', session()->get('test'));
    }

    public function testCookieHelper()
    {
        $this->assertInstanceOf('Quantum\Libraries\Cookie\Cookie', cookie());

        $this->assertFalse(cookie()->has('test'));

        cookie()->set('test', 'Testing');

        $this->assertTrue(cookie()->has('test'));

        $this->assertEquals('Testing', cookie()->get('test'));
    }

    public function testValidBase64()
    {
        $validBase64String = base64_encode('test');

        $invalidBase64String = 'abc123';

        $this->assertTrue(valid_base64($validBase64String));

        $this->assertFalse(valid_base64($invalidBase64String));
    }

    public function testCurrentLang()
    {
        config()->set('langs', ['en', 'ru', 'am']);

        config()->set('lang_default', 'en');

        config()->set('lang_segment', 1);

        $this->assertNull(current_lang());

        Lang::getInstance()->setLang('en');

        $this->assertNotNull(current_lang());

        $this->assertEquals('en', current_lang());
    }

    public function testGetTranslations()
    {

        config()->set('langs', ['en', 'ru', 'am']);

        config()->set('lang_default', 'en');

        config()->set('lang_segment', 1);

        $langDir = \Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources' . DS . 'lang' . DS . 'en';

        if (!is_dir($langDir)) {
            mkdir($langDir, 0777, true);
        }

        file_put_contents($langDir . DS . 'custom.php', null);

        $loaderMock = Mockery::mock('Quantum\Loader\Loader');

        $loaderMock->shouldReceive('setup')->andReturn($loaderMock);

        $loaderMock->shouldReceive('load')->andReturn([
            'learn_more' => 'Learn more',
            'info' => 'Information about {%1} feature',
            'test' => 'Testing'
        ]);

        Lang::getInstance()->setLang('en')->load($loaderMock);

        $this->assertEquals('Learn more', t('custom.learn_more'));

        $this->assertEquals('Information about new feature', t('custom.info', 'new'));

        _t('custom.learn_more');

        $this->expectOutputString('Learn more');

        unlink($langDir . DS . 'custom.php');

        sleep(1);
        rmdir($langDir);

        sleep(1);
        rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources' . DS . 'lang');

        sleep(1);
        rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module() . DS . 'Resources');

        sleep(1);
        rmdir(\Quantum\Libraries\Lang\modules_dir() . DS . \Quantum\Libraries\Lang\current_module());

        sleep(1);
        rmdir(\Quantum\Libraries\Lang\modules_dir());
    }

    public function testCsrfToken()
    {
        putenv('APP_KEY=appkey');

        csrf_token();

        $this->expectOutputString(Csrf::getToken(session()));

        Csrf::deleteToken(session());
    }

    public function testMessageHelper()
    {
        $this->assertEquals('Hello John', _message('Hello {%1}', 'John'));

        $this->assertEquals('Hello John, greetings from Jenny', _message('Hello {%1}, greetings from {%2}', ['John', 'Jenny']));
    }



}

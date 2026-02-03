<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteBuilder;
use InvalidArgumentException;
use LogicException;

class RouteBuilderTest extends AppTestCase
{
    public function testRouteBuilderBuildReturnsRouteCollection()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build([
            'Web' => function (RouteBuilder $route) {
                $route->get('users', 'UserController', 'index');
            },
        ], []);

        $this->assertInstanceOf(\Quantum\Router\RouteCollection::class, $routes);

        $this->assertSame(1, $routes->count());
    }

    public function testRouteBuilderBuildCollectsRoutesFromMultipleModules()
    {
        $builder = new RouteBuilder();

        $closures = [
            'Api' => function (RouteBuilder $route) {
                $route->get('users', 'UsersController', 'index');
                $route->post('login', 'AuthController', 'login');
            },
            'Web' => function (RouteBuilder $route) {
                $route->get('', 'HomeController', 'index');
            },
        ];

        $moduleConfigs = [
            'Api' => ['prefix' => 'api'],
            'Web' => ['prefix' => ''],
        ];

        $routes = $builder->build($closures, $moduleConfigs);

        $this->assertSame(3, $routes->count());

        $all = $routes->all();

        $this->assertSame('Api', $all[0]->getModule());

        $this->assertSame('/api/users', $all[0]->getPattern());

        $this->assertTrue($all[0]->allowsMethod('GET'));

        $this->assertSame('Api', $all[1]->getModule());

        $this->assertSame('/api/login', $all[1]->getPattern());

        $this->assertTrue($all[1]->allowsMethod('POST'));

        $this->assertSame('Web', $all[2]->getModule());

        $this->assertSame('/', $all[2]->getPattern());

        $this->assertTrue($all[2]->allowsMethod('GET'));
    }

    public function testRouteBuilderMissingModuleConfigFallsBackSafely()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Test' => function (RouteBuilder $route) {
                    $route->get('ping', 'PingController', 'index');
                },
            ],
            []
        );

        $this->assertSame(1, $routes->count());

        $route = $routes->all()[0];

        $this->assertSame('Test', $route->getModule());

        $this->assertSame('/ping', $route->getPattern());
    }

    public function testRouteBuilderPrefixIsAppliedToRoutes()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Api' => function (RouteBuilder $route) {
                    $route->get('status', 'StatusController', 'index');
                },
            ],
            [
                'Api' => ['prefix' => 'v1'],
            ]
        );

        $route = $routes->all()[0];

        $this->assertSame('/v1/status', $route->getPattern());

        $this->assertSame('Api', $route->getModule());
    }

    public function testRouteBuilderGroupAssignsGroupNameToRoutes()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('auth', function (RouteBuilder $route) {
                        $route->get('dashboard', 'DashboardController', 'index');
                    });
                },
            ],
            []
        );

        $route = $routes->all()[0];

        $this->assertSame('auth', $route->getGroup());
    }

    public function testRouteBuilderNestedGroupsAreNotAllowed()
    {
        $this->expectException(LogicException::class);

        $builder = new RouteBuilder();

        $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('one', function (RouteBuilder $route) {
                        $route->group('two', function () {
                        });
                    });
                },
            ],
            []
        );
    }

    public function testRouteBuilderGroupMiddlewaresAreAppliedToRoutes()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('auth', function (RouteBuilder $route) {
                        $route->get('profile', 'ProfileController', 'show');
                    })->middlewares(['Auth']);
                },
            ],
            []
        );

        $route = $routes->all()[0];

        $this->assertSame(['Auth'], $route->getMiddlewares());
    }

    public function testRouteBuilderRouteMiddlewaresPrependToGroupMiddlewares()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('auth', function (RouteBuilder $route) {
                        $route
                            ->get('profile', 'ProfileController', 'show')
                            ->middlewares(['Editor']);
                    })->middlewares(['Auth']);
                },
            ],
            []
        );

        $route = $routes->all()[0];

        $this->assertSame(
            ['Auth', 'Editor'],
            $route->getMiddlewares()
        );
    }

    public function testRouteBuilderMiddlewaresMustBeCalledAfterRouteOrInsideGroup()
    {
        $this->expectException(LogicException::class);

        $builder = new RouteBuilder();

        $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->middlewares(['Invalid']);
                },
            ],
            []
        );
    }

    public function testRouteBuilderMiddlewareOrderAcrossModulesGroupsAndStandaloneRoutes()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('auth', function (RouteBuilder $route) {
                        $route->get('profile', 'ProfileController', 'show')->middlewares(['ProfileMw']);
                        $route->get('settings', 'SettingsController', 'show')->middlewares(['SettingsMw']);
                    })->middlewares(['AuthMw']);

                    $route->get('home', 'HomeController', 'index')->middlewares(['HomeMw']);
                },

                'Api' => function (RouteBuilder $route) {
                    $route->group('api-auth', function (RouteBuilder $route) {
                        $route->get('users', 'UsersController', 'index')->middlewares(['UsersMw1', 'UsersMw2']);
                        $route->post('login', 'AuthController', 'login')->middlewares(['LoginMw']);
                    })->middlewares(['ApiAuthMw']);

                    $route->get('status', 'StatusController', 'index')->middlewares(['StatusMw']);
                },
            ],
            []
        );

        $all = $routes->all();

        $this->assertSame(['AuthMw', 'ProfileMw'], $all[0]->getMiddlewares());

        $this->assertSame(['AuthMw', 'SettingsMw'], $all[1]->getMiddlewares());

        $this->assertSame(['HomeMw'], $all[2]->getMiddlewares());

        $this->assertSame(['ApiAuthMw', 'UsersMw1', 'UsersMw2'], $all[3]->getMiddlewares());

        $this->assertSame(['ApiAuthMw', 'LoginMw'], $all[4]->getMiddlewares());

        $this->assertSame(['StatusMw'], $all[5]->getMiddlewares());
    }

    public function testRouteBuilderRouteNameIsAssigned()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->get('home', 'HomeController', 'index')
                        ->name('home');
                },
            ],
            []
        );

        $this->assertSame('home', $routes->all()[0]->getName());
    }

    public function testRouteBuilderRouteNamesMustBeUnique()
    {
        $this->expectException(LogicException::class);

        $builder = new RouteBuilder();

        $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->get('a', 'AController', 'a')->name('dup');
                    $route->get('b', 'BController', 'b')->name('dup');
                },
            ],
            []
        );
    }

    public function testRouteBuilderRouteNamesCanRepeatAcrossModules()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Api' => function (RouteBuilder $route) {
                    $route->get('posts', 'ApiPostController', 'index')->name('posts');
                },
                'Web' => function (RouteBuilder $route) {
                    $route->get('posts', 'WebPostController', 'index')->name('posts');
                },
            ],
            [
                'Api' => ['prefix' => 'api'],
                'Web' => ['prefix' => ''],
            ]
        );

        $all = $routes->all();

        $this->assertSame('posts', $all[0]->getName());
        $this->assertSame('Api', $all[0]->getModule());

        $this->assertSame('posts', $all[1]->getName());
        $this->assertSame('Web', $all[1]->getModule());
    }

    public function testRouteBuilderNameMustBeCalledAfterRouteDefinition()
    {
        $this->expectException(LogicException::class);

        $builder = new RouteBuilder();

        $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->name('invalid');
                },
            ],
            []
        );
    }

    public function testRouteBuilderCacheableAppliesToSingleRoute()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->get('page', 'PageController', 'show')
                        ->cacheable(true, 60);
                },
            ],
            []
        );

        $cache = $routes->all()[0]->getCache();

        $this->assertSame(true, $cache['enabled']);
        $this->assertSame(60, $cache['ttl']);
    }

    public function testRouteBuilderCacheableAppliesToGroupRoutes()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->group('cached', function (RouteBuilder $route) {
                        $route->get('a', 'AController', 'a');
                        $route->get('b', 'BController', 'b');
                    })->cacheable(true, 120);
                },
            ],
            []
        );

        foreach ($routes->all() as $route) {
            $cache = $route->getCache();
            $this->assertSame(true, $cache['enabled']);
            $this->assertSame(120, $cache['ttl']);
        }
    }

    public function testRouteBuilderAddRouteRequiresControllerAndAction()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = new RouteBuilder();

        $builder->build(
            [
                'Web' => function (RouteBuilder $route) {
                    $route->get('broken', 'OnlyController');
                },
            ],
            []
        );
    }

    public function testRouteBuilderResolvesShortControllerNameToFqcn()
    {
        $builder = new RouteBuilder();

        $routes = $builder->build(
            [
                'Test' => function (RouteBuilder $route) {
                    $route->get('tests', 'TestController', 'tests');
                },
            ],
            []
        );

        $route = $routes->all()[0];

        $expected = 'Quantum\\Tests\\_root\\modules\\Test\\Controllers\\TestController';

        $this->assertTrue(class_exists($expected), 'Expected test controller class to exist');

        $this->assertSame($expected, $route->getController());
    }

    public function testRouteBuilderDoesNotModifyExplicitFqcnController()
    {
        $builder = new RouteBuilder();

        $fqcn = 'Quantum\\Tests\\_root\\modules\\Test\\Controllers\\TestController';

        $routes = $builder->build(
            [
                'Test' => function (RouteBuilder $route) use ($fqcn) {
                    $route->get('tests', $fqcn, 'tests');
                },
            ],
            []
        );

        $route = $routes->all()[0];

        $this->assertTrue(class_exists($fqcn), 'Expected test controller class to exist');

        $this->assertSame($fqcn, $route->getController());
    }

}

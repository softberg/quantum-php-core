<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Router\Exceptions\RouteException;
use Quantum\Router\PatternCompiler;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\Route;

class PatternCompilerTest extends AppTestCase
{
    protected PatternCompiler $compiler;
    private Route $route;

    public function setUp(): void
    {
        parent::setUp();
        $this->compiler = new PatternCompiler();
    }

    public function testPatternCompilerStaticRouteMatch()
    {
        $this->route = new Route(['GET'], 'users', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/users'));

        $this->assertSame([], $this->compiler->getParams());

        $this->assertTrue($this->compiler->match($this->route, '/users/'));

        $this->assertFalse($this->compiler->match($this->route, '/users/1'));
    }

    public function testPatternCompilerNumericParamMatch()
    {
        $this->route = new Route(['GET'], 'users/[id=:num]', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/users/42'));

        $this->assertSame(['id' => '42'], $this->compiler->getParams());

        $this->assertFalse($this->compiler->match($this->route, '/users/abc'));

        $this->assertSame([], $this->compiler->getParams());
    }

    public function testPatternCompilerAlphaParamMatch()
    {
        $this->route = new Route(['GET'], 'tag/[name=:alpha]', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/tag/test'));

        $this->assertSame(['name' => 'test'], $this->compiler->getParams());

        $this->assertFalse($this->compiler->match($this->route, '/tag/test1'));
    }

    public function testPatternCompilerAnyParamMatch()
    {
        $this->route = new Route(['GET'], 'file/[path=:any]', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/file/a-b_c'));

        $this->assertSame(['path' => 'a-b_c'], $this->compiler->getParams());
    }

    public function testPatternCompilerOptionalParamAtEnd()
    {
        $this->route = new Route(['GET'], 'post/[id=:num]?', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/post'));

        $this->assertSame(['id' => null], $this->compiler->getParams());

        $this->assertTrue($this->compiler->match($this->route, '/post/7'));

        $this->assertSame(['id' => '7'], $this->compiler->getParams());
    }

    public function testPatternCompilerLengthConstraint()
    {
        $this->route = new Route(['GET'], 'code/[id=:num:4]', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/code/1234'));

        $this->assertSame(['id' => '1234'], $this->compiler->getParams());

        $this->assertFalse($this->compiler->match($this->route, '/code/123'));
    }

    public function testPatternCompilerMultipleParams()
    {
        $this->route = new Route(
            ['GET'],
            'user/[id=:num]/post/[slug=:alpha]',
            'Ctrl',
            'act'
        );

        $this->assertTrue($this->compiler->match($this->route, '/user/5/post/hello'));

        $this->assertSame(['id' => '5', 'slug' => 'hello'], $this->compiler->getParams());
    }

    public function testPatternCompilerOptionalLeadingParam()
    {
        $this->route = new Route(
            ['GET'],
            '[:alpha:2]?/about',
            'Ctrl',
            'act'
        );

        $this->assertTrue($this->compiler->match($this->route, '/about'));

        $this->assertTrue($this->compiler->match($this->route, '/en/about'));
    }

    public function testPatternCompilerInvalidParamNameThrowsException()
    {
        $this->route = new Route(['GET'], '[id1=:num]', 'Ctrl', 'act');

        $this->expectException(RouteException::class);

        $this->compiler->compile($this->route);
    }

    public function testPatternCompilerDuplicateParamNameThrowsException()
    {
        $this->route = new Route(
            ['GET'],
            '[id=:num]/[id=:num]',
            'Ctrl',
            'act'
        );

        $this->expectException(RouteException::class);

        $this->compiler->compile($this->route);
    }

    public function tesPatternCompilertUrlDecodedInput()
    {
        $this->route = new Route(['GET'], 'user/[id=:num]', 'Ctrl', 'act');

        $this->assertTrue(
            $this->compiler->match(
                $this->route,
                '/user/%34%32'
            )
        );

        $this->assertSame(['id' => '42'], $this->compiler->getParams());
    }

    public function testPatternCompilerRootPatternMatches()
    {
        $this->route = new Route(['GET'], '/', 'Ctrl', 'act');

        $this->assertTrue($this->compiler->match($this->route, '/'));
    }

    public function testPatternCompilerParamsResetAfterFailedMatch()
    {
        $this->route = new Route(['GET'], 'users/[id=:num]', 'Ctrl', 'act');

        $this->compiler->match($this->route, '/users/42');
        $this->assertSame(['id' => '42'], $this->compiler->getParams());

        $this->compiler->match($this->route, '/users/abc');
        $this->assertSame([], $this->compiler->getParams());
    }
}

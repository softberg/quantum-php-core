<?php

namespace Quantum\Tests\Unit\Router\Exceptions;

use Quantum\Router\Exceptions\RouteException;
use Quantum\Tests\Unit\AppTestCase;

class RouteExceptionTest extends AppTestCase
{
    public function testCoreFactories(): void
    {
        $this->assertSame('Route must define at least one HTTP method', RouteException::noHttpMethods()->getMessage());
        $this->assertSame(E_ERROR, RouteException::noHttpMethods()->getCode());
        $this->assertSame('Closure route cannot define controller or action', RouteException::closureWithController()->getMessage());
        $this->assertSame(E_WARNING, RouteException::closureWithController()->getCode());
        $this->assertSame('Controller route must define non-empty controller and action', RouteException::incompleteControllerRoute()->getMessage());
        $this->assertSame('Nested route groups are not supported', RouteException::nestedGroups()->getMessage());
        $this->assertSame('middlewares() must be called inside a group or after a route definition', RouteException::middlewaresOutsideRoute()->getMessage());
        $this->assertSame('Cannot resolve controller without module context', RouteException::controllerWithoutModule()->getMessage());
        $this->assertSame('cacheable() must be called inside a group or after a route definition', RouteException::cacheableOutsideRoute()->getMessage());
        $this->assertSame('Closure route is missing its closure handler', RouteException::closureHandlerMissing()->getMessage());
        $this->assertSame('Route is not a closure', RouteException::notClosure()->getMessage());
        $this->assertSame('Names can not be set before route definition', RouteException::nameBeforeDefinition()->getMessage());
        $this->assertSame('Route param names can only contain letters', RouteException::paramNameNotValid()->getMessage());
    }

    public function testParameterizedFactories(): void
    {
        $this->assertSame('Routes for module `Blog` must return a Closure', RouteException::moduleRoutesNotClosure('Blog')->getMessage());
        $this->assertSame('Route name `home` must be unique within module', RouteException::nonUniqueNameInModule('home')->getMessage());
        $this->assertSame('Action `index` not found on controller `PostController`', RouteException::actionNotFound('index', 'PostController')->getMessage());
        $this->assertSame('Route handler `handler` must return `Quantum\\Http\\Response`', RouteException::invalidHandlerResponse('handler', 'Quantum\\Http\\Response')->getMessage());
        $this->assertSame('Route param name `id` already in use', RouteException::paramNameNotAvailable('id')->getMessage());
    }
}


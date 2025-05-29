<?php

namespace Tests\Unit\Route;

use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Middleware\ErrorHandlerMiddleware;
use Explt13\Nosmi\Middleware\MiddlewareRegistry;
use Explt13\Nosmi\Routing\Route;
use LogicException;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Tests\Unit\helpers\Reset;
use Tests\Unit\Route\mockadata\AlsoMatchesMiddleware;
use Tests\Unit\Route\mockadata\AnotherController;
use Tests\Unit\Route\mockadata\AnotherMiddleware;
use Tests\Unit\Route\mockadata\GenericMiddleware;
use Tests\Unit\Route\mockadata\LastCommonMiddleware;
use Tests\Unit\Route\mockadata\NotRelatedMiddleware;
use Tests\Unit\Route\mockadata\SomeCommonMiddleware;
use Tests\Unit\Route\mockadata\SomeController;
use Tests\Unit\Route\mockadata\SomeMiddleware;
use Tests\Unit\Route\mockadata\SpecificMiddleware;

class RouteTest extends TestCase
{
    private LightRouteInterface $route;
    private MiddlewareRegistryInterface&MockObject $middlewareRegistryMock;


    public static function setUpBeforeClass(): void
    {
        Route::get('/order/<string>:name/<int>:id', SomeController::class);
        Route::get('/order/add/:alias', SomeController::class);
        Route::get('/user/<string>:id', SomeController::class);
        Route::get('/:id', SomeController::class);
    }

    protected function tearDown(): void
    {
        Reset::resetStaticClass(Route::class);
    }

    protected function setUp(): void
    {
        $this->middlewareRegistryMock = $this->createMock(MiddlewareRegistryInterface::class);
        Route::get('/order/new/<slug>:product/<int>:id', SomeController::class);
        Route::get('/first/pattern/:id', AnotherController::class);
        Route::get('/second/pattern/<string>:name', SomeController::class);
        $this->route = new Route($this->middlewareRegistryMock);
        $uri = new Uri('https://example.com/order/new/dsda-09/213?address=Baker-av3-street&quantity=4');
        $this->route = $this->route->resolvePath($uri->getPath(), 'GET');
    }

    public function testUseMiddleware()
    {
        $some_common_middleware = new SomeCommonMiddleware;
        $some_middleware = new SomeMiddleware;
        $another_middleware = new AnotherMiddleware;
        $also_matches_middleware = new AlsoMatchesMiddleware;
        $not_related_middleware = new NotRelatedMiddleware;
        $specific_middleware = new SpecificMiddleware;
        $generic_middleware = new GenericMiddleware;
        $last_common_middleware = new LastCommonMiddleware;
        
        $middleware_registry = MiddlewareRegistry::getInstance();
        $another_route = new Route($middleware_registry);
        $uri = new Uri('https://example.com/something/really/different/123');
        Route::get('/something/really/different/<int>:id', AnotherController::class);
        $another_route = $another_route->resolvePath($uri->getPath(), 'GET');

        $middleware_registry->add($some_common_middleware);
        $middleware_registry->remove(ErrorHandlerMiddleware::class);
        Route::useMiddleware('/order/new/<slug>:product/<int>:id', $some_middleware);
        Route::useMiddleware('/order/new/<slug>:product/<int>:id', $another_middleware);
        Route::useMiddleware('/order/NOT_RELATED/<slug>:product/<int>:id', $not_related_middleware);
        Route::useMiddleware('/order/<string>:age/<slug>:product/<int>:id', $also_matches_middleware);
        Route::useMiddleware('/order/new/dsda-09/213', $specific_middleware);
        Route::useMiddleware('/<string>:entity/<string>:age/<slug>:product/<int>:id', $generic_middleware);
        $middleware_registry->add($last_common_middleware);
        $this->assertSame(
            [
                $some_common_middleware::class => $some_common_middleware, 
                $some_middleware::class => $some_middleware, 
                $another_middleware::class => $another_middleware, 
                $also_matches_middleware::class => $also_matches_middleware, 
                $specific_middleware::class => $specific_middleware, 
                $generic_middleware::class => $generic_middleware, 
                $last_common_middleware::class => $last_common_middleware
            ], 
            $this->route->getRouteMiddleware()
        );

        Route::disableMiddleware('/order/new/<slug>:product/<int>:id', $another_middleware::class);
        Route::disableMiddleware('/order/<string>:age/<slug>:product/<int>:id', $also_matches_middleware::class);
        Route::disableMiddleware('/order/new/dsda-09/213', $specific_middleware::class);
        $this->assertSame(
            [
                $some_common_middleware::class => $some_common_middleware, 
                $some_middleware::class => $some_middleware, 
                $generic_middleware::class => $generic_middleware, 
                $last_common_middleware::class => $last_common_middleware
            ], 
            $this->route->getRouteMiddleware()
        );

        Route::disableMiddleware('/order/new/dsda-09/213', $last_common_middleware::class);
        $this->assertSame(
            [
                $some_common_middleware::class => $some_common_middleware, 
                $some_middleware::class => $some_middleware, 
                $generic_middleware::class => $generic_middleware, 
            ], 
            $this->route->getRouteMiddleware()
        );

        $this->assertSame(
            [ 
                $some_common_middleware::class => $some_common_middleware, 
                $generic_middleware::class => $generic_middleware,
                $last_common_middleware::class =>  $last_common_middleware,
            ],
            $another_route->getRouteMiddleware()
        );

        $middleware_registry->remove($some_common_middleware::class);
        $this->assertSame(
            [
                $some_middleware::class => $some_middleware, 
                $generic_middleware::class => $generic_middleware, 
            ], 
            $this->route->getRouteMiddleware()
        );

        $this->assertSame(
            [ 
                $generic_middleware::class => $generic_middleware,
                $last_common_middleware::class =>  $last_common_middleware,
            ],
            $another_route->getRouteMiddleware()
        );
    }

    public static function pathAndPatternsProvider(): array
    {
        return [
            "no conversion needed due to absence of named parameters" => [
                "regexp" => "^/order/add/12334$",
                "path_pattern" => "/order/add/12334"
            ],
            "conversion typed params" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)$",
                "path_pattern" => "/order/<string>:name/<int>:id"
            ],
            "conversion typed params trailing slash" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)/$",
                "path_pattern" => "/order/<string>:name/<int>:id/"
            ],
            "implicit conversion to slug type if type is not specified" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/:id"
            ],
            "conversion explicit slug typed params to slug type with no dublications" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<slug>:id"
            ],
            "conversion with underscored name" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<id_for_user>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<slug>:id_for_user",
            ],
            
            "fail a conversion, a non-existed typed param" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<notexisted>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<notexisted>:id",
                "fail" => [
                    "class" => InvalidAssocArrayValueException::class,
                    "message" => "Assoc array invalid key: expected the `type` key to have value(s) `[<string>, <int>, <slug>]` but got `<notexisted>`"
                ]
            ],
            "fail a conversion, an empty string type" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<>:id",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<>:id has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, an invalid type syntax" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<no_triangle_braces>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/no_triangle_braces here:id",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/no_triangle_braces here:id has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains digits" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<int>:id21321",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<int>:id21321 has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains dashes" => [
                "regexp" => "^/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)$",
                "path_pattern" => "/order/<string>:name/<int>:id-for-user",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<int>:id-for-user has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
        ];
    }

    #[DataProvider('pathAndPatternsProvider')]
    public function testPathPatternConvertedToRegexp(string $regexp, string $path_pattern, bool|array $fail = false)
    {
        if ($fail) {
            $this->expectExceptionMessage($fail["message"]);
            $this->expectException($fail["class"]);
        }
        Route::get($path_pattern, SomeController::class);
        $this->assertSame($regexp, Route::getRegexpByPathPattern($path_pattern, 'GET'));
    }

    public function testGetController()
    {
        $this->assertSame(SomeController::class, $this->route->getController());
    }

    public function testGetAction()
    {
        Route::get('some/new/route/path', SomeController::class, 'pro2file');
        $route = (new Route($this->middlewareRegistryMock))->resolvePath('some/new/route/path', 'GET');
        $this->assertSame("pro2file", $route->getAction());

        Route::get('some/new/route/two', SomeController::class);
        $route = (new Route($this->middlewareRegistryMock))->resolvePath('some/new/route/two', 'GET');
        $this->assertSame(null, $route->getAction());

        Route::get('some/another', SomeController::class, 'd-09');
        $this->expectException(\LogicException::class);
        $route = (new Route($this->middlewareRegistryMock))->resolvePath('some/another', 'GET');
    }

    public function testGetRequestPathPattern()
    {
        $this->assertSame('/order/new/<slug>:product/<int>:id', $this->route->getPathPattern());
    }

    public function testGetRequestPathRegexp()
    {
        $this->assertSame('^/order/new/(?P<product>[a-zA-Z0-9-]+)/(?P<id>[0-9]+)$', $this->route->getPathRegexp());
    }

    public function testGetRequestParams()
    {
        $this->assertSame(['product' => 'dsda-09', 'id' => '213'], $this->route->getParams());
    }

    public function testGetRequestParam()
    {
        $this->assertSame('dsda-09', $this->route->getParam('product'));
        $this->assertSame(null, $this->route->getParam('notexisted'));
    }

    public function testRoutesAddAndRoutesMapsMethods()
    {
        Reset::resetStaticProp(Route::class, 'routes');
        Reset::resetStaticProp(Route::class, 'patterns_map');

        Route::get('/first/pattern/:id', SomeController::class, null);
        Route::get('/second/pattern/<string>:name', SomeController::class, null);
        $this->assertSame(
            ['/first/pattern/:id', '/second/pattern/<string>:name'],
            Route::getPathPatterns('GET')
        );
        $this->assertSame(
            [
                '/first/pattern/:id' => '^/first/pattern/(?P<id>[a-zA-Z0-9-]+)$',
                '/second/pattern/<string>:name' => '^/second/pattern/(?P<name>[a-zA-Z]+)$'
            ],
            Route::getPatternToRegexMap('GET')
        );
        $this->assertSame(
            ['^/first/pattern/(?P<id>[a-zA-Z0-9-]+)$', '^/second/pattern/(?P<name>[a-zA-Z]+)$'],
            Route::getPathRegexps('GET')
        );
        $this->assertSame(
            [
                '^/first/pattern/(?P<id>[a-zA-Z0-9-]+)$' => ['controller' => SomeController::class, 'action' => null],
                '^/second/pattern/(?P<name>[a-zA-Z]+)$' => ['controller' => SomeController::class, 'action' => null]
            ],
            Route::getRoutes('GET')
        );
    }

    public function testGetRegexpByPathPattern()
    {
        $this->assertSame(
            '^/second/pattern/(?P<name>[a-zA-Z]+)$',
            Route::getRegexpByPathPattern('/second/pattern/<string>:name', 'GET')
        );
        $this->assertSame(
            null,
            Route::getRegexpByPathPattern('/not_existed/pattern/<string>:name', 'GET')
        );
    }

    public function testGetControllerByPathPattern()
    {
        $this->assertSame(
            AnotherController::class,
            Route::getControllerByPathPattern('/first/pattern/:id', 'GET')
        );
        $this->assertSame(
            SomeController::class,
            Route::getControllerByPathPattern('/second/pattern/<string>:name', 'GET')
        );
        $this->assertSame(
            null,
            Route::getControllerByPathPattern('/not_existed/pattern/<string>:name', 'GET')
        );
    }

    public function testGetControllerByRegexp()
    {
        $this->assertSame(
            AnotherController::class,
            Route::getControllerByRegexp('^/first/pattern/(?P<id>[a-zA-Z0-9-]+)$', 'GET')
        );
        $this->assertSame(
            SomeController::class,
            Route::getControllerByRegexp('^/second/pattern/(?P<name>[a-zA-Z]+)$', 'GET')
        );
        $this->assertSame(
            null,
            Route::getControllerByRegexp('/not_existed/pattern/<string>:name', 'GET')
        );
    }

    public function testGetPathPatternsOfController()
    {
        Route::get('/another/pattern', AnotherController::class, null, 'GET');
        $this->assertSame(
            ['/first/pattern/:id', '/another/pattern'],
            Route::getPathPatternsOfController(AnotherController::class, 'GET')
        );
    }

    public function testGetRegexpsOfController()
    {
        Route::get('/another/pattern', AnotherController::class, null, 'GET');
        $this->assertSame(
            ['^/first/pattern/(?P<id>[a-zA-Z0-9-]+)$', '^/another/pattern$'],
            Route::getRegexpsOfController(AnotherController::class, 'GET')
        );
    }
}
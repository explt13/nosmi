<?php

namespace Tests\Unit\Route;

use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;
use Explt13\Nosmi\Routing\Route;
use LogicException;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\Reset;

class RouteTest extends TestCase
{
    private Route $route;

    public static function setUpBeforeClass(): void
    {
        Route::add('/order/<string>:name/<int>:id', "OrderController");
        Route::add('/order/add/:alias', "OrderController");
        Route::add('/user/<string>:id', "UserController");
        Route::add('/:id', "ArticleController");
    }

    protected function tearDown(): void
    {
        Reset::resetStaticProp(Route::class, 'routes');
        Reset::resetStaticProp(Route::class, 'patterns_map');
    }


    protected function setUp(): void
    {
        Route::add('/order/new/<slug>:product/<int>:id', 'OrderController');
        Route::add('/first/pattern/:id', 'FirstController');
        Route::add('/second/pattern/<string>:name', 'SecondController');
        $this->route = new Route();
        $uri = new Uri('https://example.com/order/new/dsda-09/213/?address=Baker-av3-street&quantity=4');
        $this->route->setRoute($uri->getPath());
        
    }

    public static function pathAndPatternsProvider(): array
    {
        return [
            "no conversion needed due to absence of named parameters" => [
                "regexp" => "/order/add/12334",
                "path_pattern" => "/order/add/12334"
            ],
            "conversion typed params" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)",
                "path_pattern" => "/order/<string>:name/<int>:id"
            ],
            "conversion typed params trailing slash" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)/",
                "path_pattern" => "/order/<string>:name/<int>:id/"
            ],
            "implicit conversion to slug type if type is not specified" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/:id"
            ],
            "conversion explicit slug typed params to slug type with no dublications" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/<slug>:id"
            ],
            "conversion with underscored name" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<id_for_user>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/<slug>:id_for_user",
            ],
            
            "fail a conversion, a non-existed typed param" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<notexisted>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/<notexisted>:id",
                "fail" => [
                    "class" => InvalidAssocArrayValueException::class,
                    "message" => "Assoc array invalid key: expected the `type` key to have value(s) `[<string>, <int>, <slug>]` but got `<notexisted>`"
                ]
            ],
            "fail a conversion, an empty string type" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/<>:id",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<>:id has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, an invalid type syntax" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<no_triangle_braces>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/no_triangle_braces here:id",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/no_triangle_braces here:id has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains digits" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)",
                "path_pattern" => "/order/<string>:name/<int>:id21321",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<int>:id21321 has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains dashes" => [
                "regexp" => "/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)",
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
        Route::add($path_pattern, 'SomeController');
        $this->assertSame($regexp, Route::getRegexpByPathPattern($path_pattern));
    }

    public function testGetRequestController()
    {
        $this->assertSame('OrderController', $this->route->getController());
    }

    public function testGetRequestPathPattern()
    {
        $this->assertSame('/order/new/<slug>:product/<int>:id', $this->route->getPathPattern());
    }

    public function testGetRequestPathRegexp()
    {
        $this->assertSame('/order/new/(?P<product>[a-zA-Z0-9-]+)/(?P<id>[0-9]+)', $this->route->getPathRegexp());
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

        Route::add('/first/pattern/:id', 'FirstController');
        Route::add('/second/pattern/<string>:name', 'SecondController');
        $this->assertSame(['/first/pattern/:id', '/second/pattern/<string>:name'], Route::getPathPatterns());
        $this->assertSame([
                '/first/pattern/:id' => '/first/pattern/(?P<id>[a-zA-Z0-9-]+)', 
                '/second/pattern/<string>:name' => '/second/pattern/(?P<name>[a-zA-Z]+)'
            ], 
            Route::getPatternToRegexMap()
        );
        $this->assertSame(['/first/pattern/(?P<id>[a-zA-Z0-9-]+)', '/second/pattern/(?P<name>[a-zA-Z]+)'], Route::getPathRegexps());
        $this->assertSame([
                '/first/pattern/(?P<id>[a-zA-Z0-9-]+)' => 'FirstController',
                '/second/pattern/(?P<name>[a-zA-Z]+)' => 'SecondController'
            ],
            Route::getRoutes()
        );
    }

    public function testGetRegexpByPathPattern()
    {
        $this->assertSame('/second/pattern/(?P<name>[a-zA-Z]+)', Route::getRegexpByPathPattern('/second/pattern/<string>:name'));
        $this->assertSame(null, Route::getRegexpByPathPattern('/not_existed/pattern/<string>:name'));
    }

    public function testGetControllerByPathPattern()
    {
        $this->assertSame('FirstController', Route::getControllerByPathPattern('/first/pattern/:id'));
        $this->assertSame('SecondController', Route::getControllerByPathPattern('/second/pattern/<string>:name'));
        $this->assertSame(null, Route::getControllerByPathPattern('/not_existed/pattern/<string>:name'));
    }

    public function testGetControllerByRegexp()
    {
        $this->assertSame('FirstController', Route::getControllerByRegexp('/first/pattern/(?P<id>[a-zA-Z0-9-]+)'));
        $this->assertSame('SecondController', Route::getControllerByRegexp('/second/pattern/(?P<name>[a-zA-Z]+)'));
        $this->assertSame(null, Route::getControllerByRegexp('/not_existed/pattern/<string>:name'));
    }

    public function testGetControllerPatternPaths()
    {
        Route::add('/another/pattern', 'FirstController');
        $this->assertSame(['/first/pattern/:id', '/another/pattern'], Route::getControllerPatternPaths('FirstController'));
    }

    public function testgetControllerRegexps()
    {
        Route::add('/another/pattern', 'FirstController');
        $this->assertSame(['/first/pattern/(?P<id>[a-zA-Z0-9-]+)', '/another/pattern'], Route::getControllerRegexps('FirstController'));
    }
}
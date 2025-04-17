<?php

namespace Tests\Unit\Route;

use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;
use Explt13\Nosmi\Routing\Route;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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

    protected function setUp(): void
    {
        $this->route = new Route();
    }

    public static function pathAndPatternsProvider(): array
    {
        return [
            "no conversion needed due to absence of named parameters" => [
                "pattern" => "/order/add/12334",
                "path" => "/order/add/12334"
            ],
            "conversion typed params" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)",
                "path" => "/order/<string>:name/<int>:id"
            ],
            "conversion typed params trailing slash" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[0-9]+)/",
                "path" => "/order/<string>:name/<int>:id/"
            ],
            "implicit conversion to slug type if type is not specified" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/:id"
            ],
            "conversion explicit slug typed params to slug type with no dublications" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<slug>:id"
            ],
            "conversion with underscored name" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<id_for_user>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<slug>:id_for_user",
            ],
            
            "fail a conversion, a non-existed typed param" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<notexisted>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<notexisted>:id",
                "fail" => [
                    "class" => InvalidAssocArrayValueException::class,
                    "message" => "Assoc array invalid key: expected the `type` key to have value(s) `[<string>, <int>, <slug>]` but got `<notexisted>`"
                ]
            ],
            "fail a conversion, an empty string type" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<invalidtype>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<>:id",
                "fail" => [
                    "class" => InvalidAssocArrayValueException::class,
                    "message" => "Path: /order/<string>:name/<>:id cannot be converted, `type` key is null for parameter: id. Check path's named parameters syntax."
                ]
            ],

            "fail a conversion, an invalid type syntax" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<no_triangle_braces>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/no_triangle_braces here:id",
                "fail" => [
                    "class" => InvalidAssocArrayValueException::class,
                    "message" => "Path: /order/<string>:name/no_triangle_braces here:id cannot be converted, `type` key is null for parameter: id. Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains digits" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<int>:id21321",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<int>:id21321 has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
            "fail a conversion, matched parameters count is less than needed count of conversion due to name contains dashes" => [
                "pattern" => "/order/(?P<name>[a-zA-Z]+)/(?P<int>[a-zA-Z0-9-]+)",
                "path" => "/order/<string>:name/<int>:id-for-user",
                "fail" => [
                    "class" => LogicException::class,
                    "message" => "Path: /order/<string>:name/<int>:id-for-user has the count of matched path parameters (1) less than needs to be converted (2). Check path's named parameters syntax."
                ]
            ],
        ];
    }

    #[DataProvider('pathAndPatternsProvider')]
    public function testPathConvertedToPathPattern(string $pattern, string $path, bool|array $fail = false)
    {
        if ($fail) {
            $this->expectExceptionMessage($fail["message"]);
            $this->expectException($fail["class"]);
        }
        $this->assertSame($pattern, $this->route->getPathPatternFromPath($path));
    }
}
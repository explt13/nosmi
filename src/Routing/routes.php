<?php

namespace Explt13\Nosmi\Routing;

"
<int> 0-9
<string> a-zA-Z
<slug>a-zA-Z-
";

Route::add('/order/<string>:name/<int>:id', "OrderController");
Route::add('/order/add/:alias', "OrderController");
Route::add('/user/<type>:id', "UserController");
Route::add('/:id', "ArticleController");

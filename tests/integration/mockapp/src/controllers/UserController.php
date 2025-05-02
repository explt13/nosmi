<?php

namespace Tests\Integration\mockapp\src\controllers;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Interfaces\LightResponseInterface;

class UserController extends Controller
{
    public function get(): LightResponseInterface
    {
        return $this->response;
    }

    public function profileAction(): LightResponseInterface
    {
       $html = $this->getView()->withData('name', 'App123')->render('profile');
    //    $this->response->getBody()->write($html);
       return $this->response->withJson(['html' => $html]);
    }

    public function settingsAction(): LightResponseInterface
    {
        $html = $this->getView()->withLayout('wrapper')->withData('info', ['name' => "John", "surname" => "Smith"])->render('settings');
        // $this->response->getBody()->write($html);
        return $this->response->withJson(['html' => $html]);
        // return $this->response->withJson(['html' => $html]);
    }
}
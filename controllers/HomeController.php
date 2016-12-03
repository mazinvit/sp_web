<?php

namespace Sp\Controlers;
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 02.12.2016
 * Time: 17:38
 */
class HomeController extends Controller
{
    public function index() {
        echo $this->twig->render('uvod.twig');
    }

    public function error404() {
        echo $this->twig->render('404.twig');
    }
}
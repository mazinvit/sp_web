<?php

namespace Sp\Controlers;
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 02.12.2016
 * Time: 17:38
 */
class ErrorController extends Controller
{
    public function error404() {
        echo $this->twig->render('404.twig');
    }

    public function no_articles() {
        echo $this->twig->render('no_articles.twig');
    }
}
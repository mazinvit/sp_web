<?php

namespace Sp\Controlers;
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 02.12.2016
 * Time: 17:38
 */
/**
 * Class ErrorController
 * @package Sp\Controlers
 *
 * Třída pro volání stránek s informací o chybě.
 */
class ErrorController extends Controller
{
    /**
     * Nechá vykreslit stránku s informací o chybě 404.
     */
    public function error404() {
        echo $this->twig->render('404.twig');
    }

    /**
     * Nechá vykreslit stránku, která informuje, že nejsou žádné
     * články pro veřejné vypsání.
     */
    public function no_articles() {
        echo $this->twig->render('no_articles.twig');
    }

    /**
     * Nechá vykreslit stránku, která informuje o neúspěšném přihlášení.
     */
    public function wrongsign() {
        echo $this->twig->render('wrongsign.twig');
    }
}
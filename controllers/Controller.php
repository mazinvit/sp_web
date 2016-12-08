<?php

// tento namespace pak poutivas k volani trid, bohuzel tu neni autoloading takze se musi ty tridy manualne includovat
namespace Sp\Controlers;


/**
 * Created by PhpStorm.
 * User: vitmazin
 * Date: 29.11.16
 * Time: 10:07
 */

/**
 * Class Controller
 * @package Sp\Controlers
 * Mateřská třída pro controllery
 */
class Controller
{
    /**
     * @var \Twig_Environment - instance Twigu
     */
    protected $twig;

    /**
     * Konstruktor třídy, ve kterém se vytvoří instance Twigu.
     */
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT.'views');
        $this->twig = new \Twig_Environment($loader);
        $this->twig->addFunction(new \Twig_SimpleFunction("makeURL", array($this, "makeURL")));
        $this->twig->addExtension(new \Twig_Extensions_Extension_Text());
        $this->twig->addGlobal("session", $_SESSION);
    }

    /**
     * Metoda vytvoří url ze zadaných parametrů.
     *
     * @param $page - představuje začátek názvu controlleru
     * @param $action - jméno metody
     * @param null $parametr - dodatečný parametr
     * @return string - vytvořenou url
     */
    public function makeURL($page, $action, $parametr = null) {
        if(!isset($parametr)) {
            return "index.php?page=" . $page . "&action=" . $action;
        }

        else {
            return "index.php?page=" . $page . "&action=" . $action . "&param=" . $parametr;
        }
    }

    /**
     * Metoda slouží ke snazšímu přesměrování.
     *
     * @param string $page - představuje začátek názvu controlleru
     * @param string $action - jméno metody
     * @param null $param - dodatečný parametr
     */
    protected function redirection($page = "Home", $action = "index", $param = null) {
        if($param == null) {
            header("location:" . $this->makeURL($page, $action));
        }

        else {
            header("location:" . $this->makeURL($page, $action, $param));
        }
    }
}
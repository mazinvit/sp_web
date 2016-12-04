<?php

// tento namespace pak poutivas k volani trid, bohuzel tu neni autoloading takze musis ty tridy manualne includovat
namespace Sp\Controlers;

/**
 * Created by PhpStorm.
 * User: vitmazin
 * Date: 29.11.16
 * Time: 10:07
 */

class Controller
{
    protected $twig;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT.'views');
        $this->twig = new \Twig_Environment($loader);
        $this->twig->addFunction(new \Twig_SimpleFunction("makeURL", array($this, "makeURL")));
        $this->twig->addGlobal("session", $_SESSION);
    }

    public function makeURL($page, $action, $parametr = null) {
        if(!isset($parametr)) {
            return "index.php?page=" . $page . "&action=" . $action;
        }

        else {
            return "index.php?page=" . $page . "&action=" . $action . "&param=" . $parametr;
        }
    }

    protected function redirection($page = "Home", $action = "index") {
        header("location:".$this->makeURL($page, $action));
    }
}
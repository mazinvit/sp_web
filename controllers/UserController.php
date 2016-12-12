<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 03.12.2016
 * Time: 19:29
 */

namespace Sp\Controlers;

use Sp\Models\UserModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";

/**
 * Class UserController
 * @package Sp\Controlers
 * Třída slouží jako controller pro uživatele.
 */
class UserController extends Controller
{
    /**
     * @var null instance UserModel
     */
    private $modelUser = null;

    /**
     * Odhlášení
     */
    public function signout() {
        $_SESSION['uzivatel'] = array();
        unset($_SESSION['uzivatel']);
        session_destroy();
        $this->redirection();
    }

    /**
     * Volání vykreslení stránky pro příhlášení.
     */
    public function login_page() {
        echo $this->twig->render('home/login_page.twig');
    }

    /**
     * Metoda umožňuje přihlášení uživatele. Podle toho,
     * zda se přihlášení povedlo ho přesměruje.
     */
    public function signin() {

        if(isset($_POST['uzivatel'])) {
            if($this->modelUser == null) {
                $this->modelUser = new UserModel();
            }

            $uzivatel = $_POST['uzivatel'];
            $login = stripslashes($uzivatel['login']);
            $pwd = stripslashes($uzivatel['heslo']);

            $retUzivatel = $this->modelUser->authorization($login, $pwd);

            if($retUzivatel != null) {
                $_SESSION['uzivatel'] = $retUzivatel;
                $this->redirection();
            }

            else {
                $this->redirection("Error", "wrongsign");
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Volání vykreslení stránky pro registraci.
     */
    public function register_page() {
        $template = $this->twig->loadTemplate('home/register_page.twig');
        if(isset($_SESSION['alert'])) {

            if($_SESSION['alert'] != 0) {
                $params['alert'] = $_SESSION['alert'];
                $_SESSION['alert'] = 0;
            }

            else {
                $params['alert'] = 0;
            }
        }

        else {
            $params['alert'] = 0;
        }

        echo $template->render($params);
    }

    /**
     * Metoda umožňuje registraci uživatele. Podle toho,
     * zda se registrace povedla ho přesměruje.
     */
    public function register() {
        if(isset($_POST['reg'])) {
            if($this->modelUser == null) {
                $this->modelUser = new UserModel();
            }

            $reg = $_POST['reg'];

            if($this->modelUser->registrate($reg)) {
                $_SESSION['alert'] = 1;
                $this->redirection("User", "register_page");
            }

            else {
                $_SESSION['alert'] = 2;
                $this->redirection("User", "register_page");
            }
        }

        else {
            $this->redirection("User", "errorregister");
        }
    }
}
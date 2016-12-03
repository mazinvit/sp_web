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
    private $modelUser = null;

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

    public function after_change($changes) {
        $template = $this->twig->loadTemplate('administration/after_change.twig');
        $params['changes'] = $changes;
        echo $template->render($params);
    }

    public function administration() {
        if($this->modelUser == null) {
            $this->modelUser = new \UserModel();
        }

        if($this->modelUser->isAdmin()) {
            echo $this->twig->render('administration/administration.twig');
        }

        else {
            $this->redirection();
        }
    }

    public function set_rights() {
        if($this->modelUser == null) {
            $this->modelUser = new \UserModel();
        }

        $id = $_POST['id'];
        $rights = $_POST['rights'];

        if($this->modelUser->setRigths($id, $rights)) {
            $this->after_change(1);
        }

        else {
            $this->after_change(0);
        }
    }

    public function delete_user() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        $id = $_POST['id'];

        $this->modelUser->deleteUser($id);

        $this->redirection("user_administration");
    }

    public function user_administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        $arr = $this->modelUser->selectAllUsers();

        if ($arr != null) {
            $template = $this->twig->loadTemplate('administration/user_administration.twig');
            $params['users'] = $arr;
            echo $template->render($params);
        }

        else {
            $this->redirection("administration");
        }
    }

    public function user_detail_administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        $id = $_POST['id'];

        $user = $this->modelUser->selectUserByID($id)[0];

        if($user != null) {
            $template = $this->twig->loadTemplate('administration/user_detail_administration.twig');
            $params['user'] = $user;
            echo $template->render($params);
        }

        else {
            $this->redirection("user_administration");
        }
    }

    protected function redirection($page = "Home", $action = "index") {
        header("location:".$this->makeURL($page, $action));
    }
}
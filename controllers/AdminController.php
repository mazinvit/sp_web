<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 03.12.2016
 * Time: 22:50
 */

namespace Sp\Controlers;

use Sp\Models\UserModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";

class AdminController extends Controller
{
    private $modelUser;

    public function administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            echo $this->twig->render('administration/administration.twig');
        }

        else {
            $this->redirection();
        }
    }

    public function user_administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $arr = $this->modelUser->selectAllUsers();

            if ($arr != null) {
                $template = $this->twig->loadTemplate('administration/user_administration.twig');
                $params['users'] = $arr;
                echo $template->render($params);
            } else {
                $this->redirection("Admin", "administration");
            }
        }

        else {
            $this->redirection();
        }
    }

    public function user_detail_administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $id = $_POST['id'];

            $user = $this->modelUser->selectUserByID($id)[0];

            if ($user != null) {
                $template = $this->twig->loadTemplate('administration/user_detail_administration.twig');
                $params['user'] = $user;
                echo $template->render($params);
            } else {
                $this->redirection("Admin", "user_administration");
            }
        }

        else {
            $this->redirection();
        }
    }

    public function set_rights() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $id = $_POST['id'];
            $rights = $_POST['rights'];

            if ($this->modelUser->setRigths($id, $rights)) {
                $this->after_change(1);
            } else {
                $this->after_change(0);
            }
        }

        else {
            $this->redirection();
        }
    }

    public function after_change($changes) {
        $template = $this->twig->loadTemplate('administration/after_change.twig');
        $params['changes'] = $changes;
        echo $template->render($params);
    }

    public function delete_user() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $id = $_POST['id'];

            $this->modelUser->deleteUser($id);

            $this->redirection("Admin", "user_administration");
        }

        else {
            $this->redirection();
        }
    }
}
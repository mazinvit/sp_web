<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 03.12.2016
 * Time: 22:50
 */

namespace Sp\Controlers;

use Sp\Models\UserModel;
use Sp\Models\ArticlesModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";

class AdminController extends Controller
{
    private $modelUser = null;
    private $modelArticles = null;

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
            }

            else {
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
    
    public function admin_article() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }
        
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }
        
        if($this->modelUser->isAdmin()) {
            $template = $this->twig->loadTemplate('administration/admin_article.twig');
            $params['articles'] = $this->modelArticles->getAllArticles();
            echo $template->render($params);
        }
        
        else {
            $this->redirection();
        }
    }

    public function article_detail($id) {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $article = $this->modelArticles->getArticleById($id);

            if($article == null) {
                $this->redirection('Admin', 'admin_artciles');
            }

            else {
                $template = $this->twig->loadTemplate('administration/admin_article_detail.twig');
                $params['article'] = $article;
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }
}
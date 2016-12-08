<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 03.12.2016
 * Time: 22:50
 */

namespace Sp\Controlers;

use Sp\Models\ReviewerModel;
use Sp\Models\UserModel;
use Sp\Models\ArticlesModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ReviewerModel.php";

/**
 * Class AdminController
 * @package Sp\Controlers
 *
 * Třída slouží jako controller pro administrátora.
 */
class AdminController extends Controller
{

    /**
     * @var null instance UserModel
     */
    private $modelUser = null;
    /**
     * @var null instance ArticleModel
     */
    private $modelArticles = null;
    /**
     * @var null instance ReviewerModel
     */
    private $modelReviewer = null;


    /**
     * Metoda nechá vykreslit administraci.
     */
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

    /**
     * Metoda vybere z databáze všechny uživatele a vykreslí administraci uživatelů
     */
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

    /**
     * Metoda vybere konkrétního uživatele a nechá vykreslit administraci uživatele
     * s tímto uživatelem.
     */
    public function user_detail_administration() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $id = $_POST['id'];

            $user = $this->modelUser->selectUserByID($id);

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

    /**
     * Metoda nastaví práva danému uživateli.
     */
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

    /**
     * Metoda vykreslí stránku na které se administrátor dozví, zda se změna povedla.
     *
     * @param $changes - 0 pokud se nastavení nepovedlo, jinak 1
     */
    public function after_change($changes) {
        $template = $this->twig->loadTemplate('administration/after_change.twig');
        $params['changes'] = $changes;
        echo $template->render($params);
    }

    /**
     * Metoda smaže uživatele. Pokud byl uživatel recenzent, nechá přepočítat
     * prům. hodnoc. u jím hodnocených článků.
     */
    public function delete_user() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isAdmin()) {
            $id = $_POST['id'];

            //pokud byl recenzent, upravit prumer clanku
            if($this->modelUser->isReviewer($id)) {
                //ziskam id vsech clanku, ktere recenzoval
                $articles = $this->modelReviewer->getReviewedArticles($id);
                //vymazu ho, aby byly pric i jeho recenze
                $this->modelUser->deleteUser($id);
                //upravim prumerne hodnoceni dle zbyvajicich recenzi
                foreach ($articles as $article) {
                    $score = $this->modelReviewer->getScore($article['id']);
                    $this->modelArticles->updateArticleScore($article['id'], $score);
                }
            }

            else {
                $this->modelUser->deleteUser($id);
            }

            $this->redirection("Admin", "user_administration");
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda vykreslí administraci článků
     */
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

    /**
     * Metoda vykreslí administraci daného článku.
     *
     * @param $id - id článku
     */
    public function article_detail($id) {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isAdmin()) {
            $article = $this->modelArticles->getArticleById($id);

            if($article == null) {
                $this->redirection('Admin', 'admin_artciles');
            }

            else {
                $_SESSION['article_detail'] = $article['id'];
                $template = $this->twig->loadTemplate('administration/admin_article_detail.twig');
                $params['article'] = $article;
                $params['reviews_count'] = $this->modelReviewer->getCountReviews($id);
                $params['reviewers'] = $this->modelReviewer->getAllReviewers($id);
                $params['posible_reviewers'] = $this->modelReviewer->getPosibleReviewers($id);
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda nechá nastavit článku, zda byl přijmut nebo zamítnut.
     * Poté zavolá přesměrování zpět na administraci tohoto článku.
     */
    public function set_allow_or_deny() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $this->modelArticles->setAllowOrDeny($_POST['allow'], $_POST['id']);
            $this->redirection('Admin', 'article_detail', $_SESSION['article_detail']);
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá odebrání práva recenzovat danému uživateli recenzovat daný článek.
     * Poté zavolá přesměrování zpět na administraci tohoto článku.
     */
    public function delete_reviewer() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isAdmin()) {
            $this->modelReviewer->deleteReviewer($_POST['id_article'], $_POST['id_reviewer']);
            $score = $this->modelReviewer->getScore($_POST['id_article']);
            $this->modelArticles->updateArticleScore($_POST['id_article'], $score);
            $this->redirection('Admin', 'article_detail', $_SESSION['article_detail']);
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá přidání recenzenta k danému článku.
     * Poté zavolá přesměrování zpět na administraci tohoto článku.
     */
    public function add_reviewer() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isAdmin()) {
            if(!empty($_POST['id_reviewer'])) {
                $this->modelReviewer->addReviewer($_POST['id_article'], $_POST['id_reviewer']);
            }
            $this->redirection('Admin', 'article_detail', $_SESSION['article_detail']);
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá smazání článku a poté
     * zaovlá přesměrování na administraci článků.
     * @param $id - id článku
     */
    public function delete_article($id) {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAdmin()) {
            $this->modelArticles->deletArticle($id);
            $this->redirection('Admin', 'admin_article');
        }

        else {
            $this->redirection();
        }
    }
}
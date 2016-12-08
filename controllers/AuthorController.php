<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 04.12.2016
 * Time: 15:28
 */

namespace Sp\Controlers;

use Sp\Models\UserModel;
use Sp\Models\ArticlesModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";

/**
 * Class AuthorController
 * @package Sp\Controlers
 * Třída slouží jako controller pro autory článků.
 */
class AuthorController extends Controller
{
    /**
     * @var null instance ArticleModel
     */
    private $modelArticles = null;
    /**
     * @var null instance UserModel
     */
    private $modelUser = null;

    /**
     * Metoda vykreslí sekci pro autory.
     */
    public function author_section() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAuthor()) {
            echo $this->twig->render('author/author.twig');
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá vykreslení stránky s formulářem pro přidání článku.
     */
    public function write_article() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isAuthor()) {
            $template = $this->twig->loadTemplate('author/write_article.twig');
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

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá přidání článku. Poté zavolá přesměrování zpět na formulář,
     * kde se zobrazí, zda byl článek přidán nebo ne.
     */
    public function add_article() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser->isAuthor()) {
            $article = @$_POST['article'];
            $id = $_SESSION['uzivatel']['id'];
            $pdf = @$_FILES['pdf'];

            if ($article['nazev'] != "" && $article['autori'] != "" && $article['abstract'] != "" && $pdf['name'] != "") {

                if($this->modelArticles->add_article($article, $id, $pdf)) {
                    $_SESSION['alert'] = 1;
                    $this->redirection('Author', 'write_article');
                }

                else {
                    $_SESSION['alert'] = 2;
                    $this->redirection('Author', 'write_article');
                }
            }
            else {
                $_SESSION['alert'] = 2;
                $this->redirection('Author', 'write_article');
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Zavolá vybrání všech článků daného uživatele a poté zavolá vykreslení
     * přehledu všech jeho článků. Šabloně předá vybrané články jako parametr.
     */
    public function my_articles() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser->isAuthor()) {
            $articles = $this->modelArticles->getMyArticles();
            if($articles == null) {
                $this->redirection('Author', 'author_section');
            }

            else {
                $template = $this->twig->loadTemplate("author/my_articles.twig");
                $params['articles'] = $articles;
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Zavolá vykreslení detailu článku pro úpravu.
     *
     * @param $id - id článku
     */
    public function my_article_detail($id) {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser->isAuthor()) {
            $article = $this->modelArticles->getArticleById($id);
            if($article == null) {
                $this->redirection('Author', 'my_articles');
            }

            else {
                $template = $this->twig->loadTemplate("author/my_article_detail.twig");
                $params['article'] = $article;
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá upravení daného článku.
     */
    public function update_article() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser->isAuthor()) {
            if(!isset($_POST['article'])) {
                $this->redirection('Author', 'my_articles');
            }

            if(!empty($_FILES['pdf']['name'])) {
                $this->modelArticles->updateArticle($_POST['article'], $_FILES['pdf']);
            }

            else {
                $this->modelArticles->updateArticle($_POST['article']);
            }

            $this->redirection('Author', 'my_article_detail', $_POST['article']['id']);
        }

        else {
            $this->redirection();
        }
    }
}
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

class AuthorController extends Controller
{
    private $modelArticles = null;
    private $modelUser = null;

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
            } else {
                $_SESSION['alert'] = 2;
                $this->redirection('Author', 'write_article');
            }
        }

        else {
            $this->redirection();
        }
    }
}
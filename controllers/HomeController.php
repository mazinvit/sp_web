<?php

namespace Sp\Controlers;

use Sp\Models;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";

/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 02.12.2016
 * Time: 17:38
 */
class HomeController extends Controller
{
    private $modelArticle = null;

    public function index() {
        echo $this->twig->render('uvod.twig');
    }

    public function articles() {
        if($this->modelArticle == null) {
            $this->modelArticle = new Models\ArticlesModel();
        }

        $arr = $this->modelArticle->getAllArticlesForHome();

        if($arr == null) {
            $this->redirection('Error', 'no_articles');
        }

        else {
            $template = $this->twig->loadTemplate('articles.twig');
            $params['articles'] = $arr;
            echo $template->render($params);
        }
    }

    public function download_pdf($param) {
        $filename = "./pdf/" . $param;

        if(file_exists($filename)) {

            header("Content-Length: " . filesize($filename));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $param);

            readfile($filename);
        }

        else {
            $this->redirection('Error', 'error404');
        }
    }

    public function where() {
        echo $this->twig->render("where.twig");
    }
}
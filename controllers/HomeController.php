<?php

/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 02.12.2016
 * Time: 17:38
 */

namespace Sp\Controlers;

use Sp\Models;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";

/**
 * Class HomeController
 * @package Sp\Controlers
 * Třída slouží jako controller pro veřejnou část stránek, kde nemusí být uživatel přihlášen.
 */
class HomeController extends Controller
{
    /**
     * @var null instance ArticleModel
     */
    private $modelArticle = null;

    /**
     * Metoda zavolá vykreslení úvodní stránky.
     */
    public function index() {
        echo $this->twig->render('uvod.twig');
    }

    /**
     * Metoda zavolá vykreslení stránky se schválenými příspěvky.
     */
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

    /**
     * Metoda umžňuje stáhnout přílohu článku.
     *
     * @param $param - jméno přílohy
     */
    public function download_pdf($param) {
        $filename = "./pdf/" . htmlspecialchars(stripslashes($param), ENT_QUOTES, 'UTF-8');

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

    /**
     * Metoda zavolá vykreslení stránky s informacemi o místě a času konání konference.
     */
    public function where() {
        echo $this->twig->render("where.twig");
    }
}
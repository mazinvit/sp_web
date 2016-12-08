<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 06.12.2016
 * Time: 17:52
 */

namespace Sp\Controlers;

use Sp\Models\UserModel;
use Sp\Models\ArticlesModel;
use Sp\Models\ReviewerModel;

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ReviewerModel.php";

/**
 * Class ReviewerController
 * @package Sp\Controlers
 * Třída slouží jako controller pro recenzety.
 */
class ReviewerController extends Controller
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
     * Metoda zavolá vykreslení recenzí uživatele.
     */
    public function my_reviews() {
        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isReviewer()) {
            $arr = $this->modelReviewer->getMyReviews($_SESSION['uzivatel']['id']);
            $template = $this->twig->loadTemplate("reviewer/my_reviews.twig");
            $params['arr'] = $arr;
            echo $template->render($params);
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda vykreslí stránku pro editaci dané recenze.
     *
     * @param $id
     */
    public function edit_review($id) {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isReviewer()) {
            $row = $this->modelReviewer->getReview($_SESSION['uzivatel']['id'], $id);
            if($row == null) {
                $this->redirection("Error", "error404");
            }

            else {
                $arr = $this->modelReviewer->getHtmlForTemplate($row);
                $template = $this->twig->loadTemplate("reviewer/edit_review.twig");
                $params['arr'] = $arr;
                $params['id'] = $id;
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }

    /**
     * Metoda zavolá upravení dané recenze a přesměruje uživatele zpět
     * na formulář pro úpravu recenze.
     */
    public function update_review() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelReviewer == null) {
            $this->modelReviewer = new ReviewerModel();
        }

        if($this->modelUser->isReviewer()) {
            $review = $_POST['review'];
            $id = $_POST['id'];
            if (empty($review) || empty($id)) {
                $this->redirection('Reviewer', 'my_reviews');
            }

            else {
                $this->modelReviewer->updateReview($review, $id);
                $score = $this->modelReviewer->getScore($id);
                $this->modelArticles->updateArticleScore($id, $score);

                $this->redirection('Reviewer', 'my_reviews');
            }
        }

        else {
            $this->redirection();
        }
    }
}
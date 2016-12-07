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

class ReviewerController extends Controller
{
    private $modelUser = null;
    private $modelArticles = null;
    private $modelReviewer = null;

    public function my_reviews() {
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
            $arr = $this->modelReviewer->getMyReviews($_SESSION['uzivatel']['id']);
            $template = $this->twig->loadTemplate("reviewer/my_reviews.twig");
            $params['arr'] = $arr;
            echo $template->render($params);
        }

        else {
            $this->redirection();
        }
    }

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
                echo $template->render($params);
            }
        }

        else {
            $this->redirection();
        }
    }
}
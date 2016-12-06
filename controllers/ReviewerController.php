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

require_once ROOT . "models" . DIRECTORY_SEPARATOR . "UserModel.php";
require_once ROOT . "models" . DIRECTORY_SEPARATOR . "ArticlesModel.php";

class ReviewerController extends Controller
{
    private $modelUser = null;
    private $modelArticles = null;

    public function my_reviews() {
        if($this->modelArticles == null) {
            $this->modelArticles = new ArticlesModel();
        }

        if($this->modelUser == null) {
            $this->modelUser = new UserModel();
        }

        if($this->modelUser->isReviewer()) {
            $arr = $this->modelArticles->getMyReviews($_SESSION['uzivatel']['id']);
            $template = $this->twig->loadTemplate("reviewer/my_reviews.twig");
            $params['arr'] = $arr;
            echo $template->render($params);
        }

        else {
            $this->redirection();
        }
    }
}
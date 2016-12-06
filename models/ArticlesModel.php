<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 04.12.2016
 * Time: 11:22
 */

namespace Sp\Models;


class ArticlesModel extends Model
{
    public function getAllArticlesForHome() {
        $q = $this->db->prepare("SELECT * FROM prispevky WHERE schvaleno = 1");

        $q->execute();
        if($q->columnCount() > 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    public function getAllArticles() {
        $q = $this->db->prepare("SELECT * FROM prispevky");

        $q->execute();

        return $q->fetchAll();
    }

    public function getMyArticles() {
        $q = $this->db->prepare("SELECT p.*, SUM(r.originalita + r.tema + r.pravopis + r.srozumitelnost) / (COUNT(*) * 4) as hodnoceni
                          FROM prispevky p, recenze r WHERE p.id_uzivatel = :id AND p.id = r.id_prispevek");
        $q->bindValue(":id", $_SESSION['uzivatel']['id']);
        $q->execute();

        if($q->columnCount() > 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    public function add_article($article, $id, $pdf) {
        $schvaleno = 0;
        $prumer = 0.0;
        $q = $this->db->prepare("INSERT INTO prispevky (nazev, autori, abstract, pdf, id_uzivatel, schvaleno, prumer_hodnoc) 
                                      VALUES (:nazev, :autori, :abstract, :pdf, :id_uzivatele, :schvaleno, :prumer_hodnoc)");
        $naz = $article['nazev'];
        $nazev = htmlspecialchars(stripslashes($naz), ENT_QUOTES, 'UTF-8');
        $aut = $article['autori'];
        $autori = htmlspecialchars(stripslashes($aut), ENT_QUOTES, 'UTF-8');
        $q->bindValue(":nazev", $nazev);
        $q->bindValue(":autori", $autori);
        $abstract = $article['abstract'];
        $q->bindValue(":abstract", htmlspecialchars(stripslashes($abstract), ENT_QUOTES, 'UTF-8'));
        $q->bindValue(":pdf", htmlspecialchars(stripslashes($pdf['name']), ENT_QUOTES, 'UTF-8'));
        $q->bindValue(":id_uzivatele", htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8'));
        $q->bindValue(":schvaleno", $schvaleno);
        $q->bindValue(":prumer_hodnoc", $prumer);

        if(!$q->execute()) {
            return false;
        }

        $file_name = $pdf['name'];
        $file_size = $pdf['size'];
        $file_tmp = $pdf['tmp_name'];
        $pdfname = $pdf['name'];
        $file_ext = strtolower(pathinfo($pdfname, PATHINFO_EXTENSION));

        $expensions = array("pdf");

        if(in_array($file_ext, $expensions) == false){
            $this->deletArticleCouseOfError($nazev, $autori);
            return false;
        }

        if($file_size > 5242880){
            $this->deletArticleCouseOfError($nazev, $autori);
            return false;
        }

        if(move_uploaded_file($file_tmp, ROOT . "www" . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . $file_name)) {
            return true;
        }

        else {
            $this->deletArticleCouseOfError($nazev, $autori);
            return false;
        }
    }

    public function getArticleById($id) {
        $q = $this->db->prepare("SELECT * FROM prispevky WHERE id = :id");
        $q->bindValue(":id", $id);
        $q->execute();

        if($q->columnCount() > 0) {
            return $q->fetch();
        }

        else {
            return null;
        }
    }

    public function getCountReviews($id) {
        $q = $this->db->prepare("SELECT originalita FROM recenze WHERE id_prispevek = :id");
        $q->bindValue(":id", $id);
        $q->execute();

        $arr = $q->fetchAll();

        $ret = 0;

        foreach($arr as $col) {
            if($col['originalita'] > 0) {
                $ret++;
            }
        }

        return $ret;
    }

    public function setAllowOrDeny($allow, $id) {
        $q = $this->db->prepare("UPDATE prispevky SET schvaleno = :schvaleno WHERE id = :id");
        $q->bindValue(":schvaleno", $allow);
        $q->bindValue(":id", $id);
        $q->execute();
    }

    public function getAllReviewers($id) {
        $q = $this->db->prepare("SELECT r.*, u.jmeno, u.id, ((r.originalita + r.pravopis + r.srozumitelnost + r.tema) / 4.0) AS prumer
                      FROM recenze r, uzivatele u WHERE r.id_prispevek = :id AND u.id = r.id_uzivatel");
        $q->bindValue(":id", $id);
        $q->execute();

        if($q->rowCount() > 0) {
            return $q->fetchAll();
        }

        else {
            return false;
        }
    }

    public function deleteReviewer($id) {
        $q = $this->db->prepare("DELETE FROM recenze WHERE id_uzivatel = :id");
        $q->bindValue(":id", $id);
        $q->execute();
    }

    public function getPosibleReviewers($id) {
        $q = $this->db->prepare("SELECT * FROM uzivatele WHERE prava = 2 AND id NOT IN (SELECT id_uzivatel FROM recenze WHERE id_prispevek = :id)");
        $q->bindValue(":id", $id);
        $q->execute();

        return $q->fetchAll();
    }

    private function deletArticleCouseOfError($nazev, $autori) {
        $q = $this->db->prepare("DELETE FROM prispevky WHERE nazev = :nazev AND autori = :autori");
        $q->bindValue(":nazev", $nazev);
        $q->bindValue(":autori", $autori);
        $q->execute();
    }

    public function addReviewer($id_article, $id_reviewer) {
        $q = $this->db->prepare("INSERT INTO recenze (id_uzivatel, id_prispevek, originalita, tema, pravopis, srozumitelnost) 
                                    VALUES (:id_reviewer, :id_article, :orig, :tema, :pravopis, :srozum)");
        $orig = $tema = $pravopis = $srozum = 0;
        $q->bindValue(":id_reviewer", $id_reviewer);
        $q->bindValue(":id_article", $id_article);
        $q->bindValue(":orig", $orig);
        $q->bindValue(":tema", $tema);
        $q->bindValue(":pravopis", $pravopis);
        $q->bindValue(":srozum", $srozum);

        $q->execute();
    }
}
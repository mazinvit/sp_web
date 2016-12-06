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
        /*
         SELECT p.*, SUM(r.originalita + r.tema + r.pravopis + r.srozumitelnost) / (COUNT(*) * 4) as hodnoceni
                          FROM prispevky p, recenze r WHERE schvaleno = 1 AND p.id = r.id_prispevek
         */
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

    private function deletArticleCouseOfError($nazev, $autori) {
        $q = $this->db->prepare("DELETE FROM prispevky WHERE nazev = :nazev AND autori = :autori");
        $q->bindValue(":nazev", $nazev);
        $q->bindValue(":autori", $autori);
        $q->execute();
    }
}
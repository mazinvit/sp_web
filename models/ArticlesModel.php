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
        //all_articles je pohled v DB
        $q = $this->db->prepare("SELECT * FROM all_articles");

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
        $q = $this->db->prepare("INSERT INTO prispevky (nazev, autori, abstract, pdf, id_uzivatel, schvaleno) 
                                      VALUES (:nazev, :autori, :abstract, :pdf, :id_uzivatele, :schvaleno)");
        $nazev = htmlspecialchars(stripslashes($article['nazev']), ENT_QUOTES, 'UTF-8');
        $autori = htmlspecialchars(stripslashes($article['autori']), ENT_QUOTES, 'UTF-8');
        $q->bindParam(":nazev", $nazev);
        $q->bindParam(":autori", $autori);
        $q->bindParam(":abstract", htmlspecialchars(stripslashes($article['abstract']), ENT_QUOTES, 'UTF-8'));
        $q->bindParam(":pdf", htmlspecialchars(stripslashes($pdf['name']), ENT_QUOTES, 'UTF-8'));
        $q->bindParam(":id_uzivatele", htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8'));
        $q->bindParam(":schvaleno", $schvaleno);

        $q->execute();

        if(!$q->execute()) {
            return false;
        }

        $file_name = $pdf['name'];
        $file_size = $pdf['size'];
        $file_tmp = $pdf['tmp_name'];
        $file_ext = strtolower(end(explode('.',$pdf['name'])));

        $expensions= array("pdf");

        if(in_array($file_ext, $expensions)=== false){
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

    private function deletArticleCouseOfError($nazev, $autori) {
        $q = $this->db->prepare("DELETE FROM prispevky WHERE nazev = :nazev AND autori = :autori");
        $q->bindParam(":nazev", $nazev);
        $q->bindParam(":autori", $autori);
        $q->execute();
    }
}
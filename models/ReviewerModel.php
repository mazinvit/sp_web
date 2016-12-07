<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 06.12.2016
 * Time: 21:27
 */

namespace Sp\Models;


class ReviewerModel extends Model
{
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

    public function getMyReviews($id) {
        $q = $this->db->prepare("SELECT r.originalita, r.pravopis, r.srozumitelnost, r.tema, ((r.originalita + r.pravopis + r.srozumitelnost + r.tema) / 4.0) AS prumer,
                            p.nazev, p.schvaleno, p.pdf, p.id FROM recenze r, prispevky p WHERE r.id_uzivatel = :id AND p.id = r.id_prispevek");
        $q->bindValue(":id", $id);
        $q->execute();
        if($q->rowCount() > 0) {
            return $q->fetchAll();
        }

        else {
            return false;
        }
    }

    public function getHtmlForTemplate($review) {
        if($review['originalita'] == 0) {
            $val = 1;
        }

        else {
            $val = $review['originalita'];
        }

        $originalita = "";

        for ($i = 1; $i <= 5; $i++) {
            if($i == $val) {
                $originalita .= '<option value="'.$i.'" selected="selected">' . $i . '</option>';
            }

            else {
                $originalita .= '<option value="'.$i.'">' . $i . '</option>';
            }
        }

        if($review['tema'] == 0) {
            $val = 1;
        }

        else {
            $val = $review['tema'];
        }

        $tema = "";

        for ($i = 1; $i <= 5; $i++) {
            if($i == $val) {
                $tema .= '<option value="'.$i.'" selected="selected">' . $i . '</option>';
            }

            else {
                $tema .= '<option value="'.$i.'">' . $i . '</option>';
            }
        }

        if($review['pravopis'] == 0) {
            $val = 1;
        }

        else {
            $val = $review['pravopis'];
        }

        $pravopis = "";

        for ($i = 1; $i <= 5; $i++) {
            if($i == $val) {
                $pravopis .= '<option value="'.$i.'" selected="selected">' . $i . '</option>';
            }

            else {
                $pravopis .= '<option value="'.$i.'">' . $i . '</option>';
            }
        }

        if($review['srozumitelnost'] == 0) {
            $val = 1;
        }

        else {
            $val = $review['srozumitelnost'];
        }

        $srozumitelnost = "";

        for ($i = 1; $i <= 5; $i++) {
            if($i == $val) {
                $srozumitelnost .= '<option value="'.$i.'" selected="selected">' . $i . '</option>';
            }

            else {
                $srozumitelnost .= '<option value="'.$i.'">' . $i . '</option>';
            }
        }

        return array("originalita" => $originalita, "tema" => $tema, "pravopis" => $pravopis, "srozumitelnost" => $srozumitelnost);
    }

    public function getReview($id_reviewer, $id_article) {
        $q = $this->db->prepare("SELECT * FROM recenze WHERE id_prispevek = :id_prispevek AND id_uzivatel = :id_uzivatel");
        $q->bindValue(":id_prispevek", $id_article);
        $q->bindValue(":id_uzivatel", $id_reviewer);
        $q->execute();

        if($q->rowCount() > 0) {
            return $q->fetch();
        }

        else {
            return null;
        }
    }

    public function getArticleReviews($id) {
        $q = $this->db->prepare("SELECT originalita, pravopis, srozumitelnost, tema FROM recenze WHERE id_prispevek = :id");
        $q->bindValue(":id", $id);
        $q->execute();
    }
}
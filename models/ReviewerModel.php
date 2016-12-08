<?php
/**
 * Created by PhpStorm.
 * User: n5ver
 * Date: 06.12.2016
 * Time: 21:27
 */

namespace Sp\Models;


/**
 * Class ReviewerModel
 * @package Sp\Model
 * Třída slouží jako model pro recenze.
 */
class ReviewerModel extends Model
{
    /**
     * Vrací počet recenzí daného článku
     *
     * @param $id - id článku
     * @return int - počet recenzí
     */
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

    /**
     * Vrací recenze, jména a id recenzentů pro daný článek.
     *
     * @param $id - id článku
     * @return array|bool - recenze, jména a id recenzentů
     */
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

    /**
     * Odebere recenzentovy článek pro recenzování.
     *
     * @param $id_article - id článku
     * @param $id_reviewer - id recenzenta
     */
    public function deleteReviewer($id_article, $id_reviewer) {
        $q = $this->db->prepare("DELETE FROM recenze WHERE id_uzivatel = :id_uzivatel AND id_prispevek = :id_prispevek");
        $q->bindValue(":id_uzivatel", $id_reviewer);
        $q->bindValue(":id_prispevek", $id_article);
        $q->execute();
    }

    /**
     * Metoda vrací recenzenty, kterí daný článek nerecenzují.
     *
     * @param $id - id článku
     * @return array - pole recenzentů
     */
    public function getPosibleReviewers($id) {
        $q = $this->db->prepare("SELECT * FROM uzivatele WHERE prava = 2 AND id NOT IN (SELECT id_uzivatel FROM recenze WHERE id_prispevek = :id)");
        $q->bindValue(":id", $id);
        $q->execute();

        return $q->fetchAll();
    }

    /**
     * Metoda přířadí recenzentovy článek.
     *
     * @param $id_article - id článku
     * @param $id_reviewer - id recenzenta
     */
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

    /**
     * Metoda vrací recenze daného recenzenta.
     *
     * @param $id - id recenzenta
     * @return array|bool - recenze
     */
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

    /**
     * Metoda generuje možnosti formuláře pro úpravu recenzí.
     *
     * @param $review - recenze
     * @return array - html
     */
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

    /**
     * Metoda vrací recenzi dle id článku a recenzenta.
     *
     * @param $id_reviewer - id recenzenta
     * @param $id_article - id článku
     * @return mixed|null - recenze
     */
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

    /**
     * Metoda vrací všechny recenze daného článku.
     *
     * @param $id - id článku
     * @return array - recenze článku
     */
    public function getArticleReviews($id) {
        $q = $this->db->prepare("SELECT originalita, pravopis, srozumitelnost, tema FROM recenze WHERE id_prispevek = :id");
        $q->bindValue(":id", $id);
        $q->execute();

        return $q->fetchAll();
    }

    /**
     * Metoda upravuje recenzi
     *
     * @param $review - recenze
     * @param $id - id článku
     */
    public function updateReview($review, $id) {
        $q = $this->db->prepare("UPDATE recenze SET originalita = :originalita, tema = :tema, pravopis = :pravopis, srozumitelnost = :srozumitelnost 
                                    WHERE id_prispevek = :id AND id_uzivatel = :id_u");
        $q->bindValue(":originalita", $review['originalita']);
        $q->bindValue(":tema", $review['tema']);
        $q->bindValue(":pravopis", $review['pravopis']);
        $q->bindValue(":srozumitelnost", $review['srozumitelnost']);
        $q->bindValue(":id", $id);
        $q->bindValue("id_u", $_SESSION['uzivatel']['id']);
        $q->execute();
    }

    /**
     * Metoda vypočítá průměrné hodnocení článku.
     *
     * @param $id - id článku
     * @return float - průmerné hodnocení
     */
    public function getScore($id) {
        $reviews = $this->getArticleReviews($id);

        $score = 0;
        $count = 0;

        for($i = 0; $i < count($reviews); $i++) {
            $score += $reviews[$i]['originalita'];
            $score += $reviews[$i]['tema'];
            $score += $reviews[$i]['pravopis'];
            $score += $reviews[$i]['srozumitelnost'];
            $count++;
        }

        $finalScore = $score / (double)($count * 4);

        return $finalScore;
    }

    /**
     * Metoda vrátí články, které recenzuje daný uživatel.
     *
     * @param $id - id uživatele
     * @return array - články
     */
    public function getReviewedArticles($id) {
        $q = $this->db->prepare("SELECT p.id FROM prispevky p, recenze r WHERE r.id_uzivatel = :id");
        $q->bindValue(":id", $id);
        $q->execute();
        return $q->fetchAll();
    }
}
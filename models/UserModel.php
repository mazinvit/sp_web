<?php
/**
 * Created by PhpStorm.
 * User: vitmazin
 * Date: 30.11.16
 * Time: 12:35
 */

namespace Sp\Models;


/**
 * Class UserModel
 * @package Sp\Models
 * Třída slouží jako model pro uživatele.
 */
class UserModel extends Model
{
    /**
     * Metodavkládá nového uživatele do databáze.
     *
     * @param $reg - údaje z formuláře
     * @return bool - zda se registrace zdařila
     */
    public function registrate($reg) {
        $prava = 3;
        if($this->validation($reg)) {
            $q = $this->db->prepare("INSERT INTO `uzivatele` (login, pass, jmeno, email, prava) 
                                     VALUES (:login, :pass, :jmeno, :email, :prava)");
            $q->bindValue(':login', htmlspecialchars(stripslashes($reg['login']), ENT_QUOTES, 'UTF-8'));
            $q->bindValue(':pass',  sha1(htmlspecialchars(stripslashes($reg['pass1']),  ENT_QUOTES, 'UTF-8')));
            $q->bindValue(':jmeno', htmlspecialchars(stripslashes($reg['jmeno']), ENT_QUOTES, 'UTF-8'));
            $q->bindValue(':email', htmlspecialchars(stripslashes($reg['email']), ENT_QUOTES, 'UTF-8'));
            $q->bindValue(':prava', $prava);
            if($q->execute()) {
                return true;
            }

            else {
                return false;
            }
        }

        else {
            return false;
        }
    }

    /**
     * Metoda ověřuje údaje zadané pro příhlášení.
     *
     * @param $login - uživ. jméno
     * @param $password - heslo
     * @return null - uživ. data pokud se zdařilo přihlášení jinak null
     */
    public function authorization($login, $password) {
        $usr = $this->selectUserByLoginAndPass($login, $password);
        $data = $usr[0];

        if($usr != null)
        {
            return $data;
        }

        else
        {
            return null;
        }
    }

    /**
     * Validace registrace
     *
     * @param $reg - data z formuláře pro registraci
     * @return bool - zda jsou data v pořádku
     */
    private function validation($reg) {
        if(!empty($reg)) {
            if($reg['pass1'] == $reg['pass2'] AND !$this->inDB($reg)) {
                return true;
            }

            else {
                return false;
            }
        }

        else {
            return false;
        }
    }

    /**
     * Metoda ověřuje, že daný uživatel již není v databázi.
     *
     * @param $reg - data z formuláře pro registraci
     * @return bool - true pokud není jinak false
     */
    private function inDB($reg) {
        $q = $this->db->prepare("SELECT `id` FROM `uzivatele` WHERE `login` = :login");
        $q->bindValue(':login', $reg['login']);
        $q->execute();

        if($q->rowCount() == 1) {
            return true;
        }

        else {
            return false;
        }
    }

    /**
     * Metoda vybere uživatele z databáze uživatele pomocí jeho uživ. jména a hesla.
     *
     * @param $login - uživ. jméno
     * @param $password - heslo
     * @return array|null - uživatelova data
     */
    private function selectUserByLoginAndPass($login, $password) {
        $q = $this->db->prepare("SELECT * FROM `uzivatele` WHERE `login` = :login AND `pass` = :pass");
        $q->bindValue(':login', htmlspecialchars(stripslashes($login), ENT_QUOTES, 'UTF-8'));
        $q->bindValue(':pass', sha1($password));
        $q->execute();

        if($q->rowCount() != 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function selectUserByID($id) {
        $id_esc = htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8');
        $q = $this->db->prepare("SELECT * FROM `uzivatele` WHERE `id` = :id");
        $q->bindValue(':id', $id_esc);
        $q->execute();

        if($q->rowCount() != 0) {
            return $q->fetch();
        }

        else {
            return null;
        }
    }

    /**
     * Metoda nastavuje uživateli práva.
     *
     * @param $id - id uživatele
     * @param $rights - práva
     * @return bool - zda se nastavení zdařilo
     */
    public function setRigths($id, $rights) {
        $id_esc = htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8');
        $rights_esc = htmlspecialchars(stripslashes($rights), ENT_QUOTES, 'UTF-8');

        $q = $this->db->prepare("UPDATE `uzivatele` SET `prava` = :prava WHERE `id` = :id");
        $q->bindValue(":prava", $rights_esc);
        $q->bindValue(":id", $id_esc);

        $ret = $q->execute();

        if($ret == 0) {
            return false;
        }

        else {
            return true;
        }
    }

    /**
     * Metoda maže uživatele z databáze.
     *
     * @param $id - id uživatele
     * @return bool - zda se vymazání zdařilo
     */
    public function deleteUser($id) {
        $user = $this->selectUserByID($id);
        $q = $this->db->prepare("DELETE FROM `uzivatele` WHERE `id` = :id");
        $q->bindValue(':id', htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8'));
        $q->execute();

        if($user['prava'] == 2) {
            return true;
        }

        else {
            return false;
        }
    }

    /**
     * Metoda vrací všechny uživatele z databáze seřazené dle loginu.
     *
     * @return array|null - všichni uživatelé v databázi
     */
    public function selectAllUsers() {
        $q = $this->db->prepare("SELECT * FROM `uzivatele` ORDER BY `login` ASC");
        $q->execute();

        if($q->rowCount() != 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    /**
     * Metoda ověřuje zda je uživatel administrátor.
     *
     * @return bool - true = je, false = není
     */
    public function isAdmin() {
        if(isset($_SESSION['uzivatel'])) {
            if($_SESSION['uzivatel']['prava'] == 1) {
                return true;
            }

            else {
                return false;
            }
        }

        else {
            return false;
        }
    }

    /**
     * Metoda ověřuje zda je uživatel recenzent. Metoda může ověřit jak přihlášeného uživatele,
     * tak i uživatele podle jeho id.
     *
     * @param null $id
     * @return bool - true = je, false = není
     */
    public function isReviewer($id = null) {
        if($id == null) {
            if (isset($_SESSION['uzivatel'])) {
                if ($_SESSION['uzivatel']['prava'] == 2) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        else {
            $user = $this->selectUserByID($id);

            if($user['prava'] == 2) {
                return true;
            }

            else {
                return false;
            }
        }
    }

    /**
     * Metoda ověřuje zda je uživatel autor příspěvků.
     *
     * @return bool - true = je, false = není
     */
    public function isAuthor() {
        if(isset($_SESSION['uzivatel'])) {
            if($_SESSION['uzivatel']['prava'] == 3) {
                return true;
            }

            else {
                return false;
            }
        }

        else {
            return false;
        }
    }
}
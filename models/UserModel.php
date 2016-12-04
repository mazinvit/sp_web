<?php

namespace Sp\Models;

/**
 * Created by PhpStorm.
 * User: vitmazin
 * Date: 30.11.16
 * Time: 12:35
 */
class UserModel extends Model
{
    public function registrate($reg) {
        $prava = 3;
        if($this->validation($reg)) {
            $q = $this->db->prepare("INSERT INTO `uzivatele` (login, pass, jmeno, email, prava) 
                                     VALUES (:login, :pass, :jmeno, :email, :prava)");
            $q->bindParam(':login', htmlspecialchars(stripslashes($reg['login']), ENT_QUOTES, 'UTF-8'));
            $q->bindParam(':pass',  sha1(htmlspecialchars(stripslashes($reg['pass1']),  ENT_QUOTES, 'UTF-8')));
            $q->bindParam(':jmeno', htmlspecialchars(stripslashes($reg['jmeno']), ENT_QUOTES, 'UTF-8'));
            $q->bindParam(':email', htmlspecialchars(stripslashes($reg['email']), ENT_QUOTES, 'UTF-8'));
            $q->bindParam(':prava', $prava);
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

    private function inDB($reg) {
        $q = $this->db->prepare("SELECT `id` FROM `uzivatele` WHERE `login` = :login");
        $q->bindParam(':login', $reg['login']);
        $q->execute();

        if($q->rowCount() == 1) {
            return true;
        }

        else {
            return false;
        }
    }

    private function selectUserByLoginAndPass($login, $password) {
        $q = $this->db->prepare("SELECT * FROM `uzivatele` WHERE `login` = :login AND `pass` = :pass");
        $q->bindParam(':login', htmlspecialchars(stripslashes($login), ENT_QUOTES, 'UTF-8'));
        $q->bindParam(':pass', sha1($password));
        $q->execute();

        if($q->rowCount() != 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    public function selectUserByID($id) {
        $id_esc = htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8');
        $q = $this->db->prepare("SELECT * FROM `uzivatele` WHERE `id` = :id");
        $q->bindParam(':id', $id_esc);
        $q->execute();

        if($q->rowCount() != 0) {
            return $q->fetchAll();
        }

        else {
            return null;
        }
    }

    public function setRigths($id, $rights) {
        $id_esc = htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8');
        $rights_esc = htmlspecialchars(stripslashes($rights), ENT_QUOTES, 'UTF-8');

        $q = $this->db->prepare("UPDATE `uzivatele` SET `prava` = :prava WHERE `id` = :id");
        $q->bindParam(":prava", $rights_esc);
        $q->bindParam(":id", $id_esc);

        $ret = $q->execute();

        if($ret == 0) {
            return false;
        }

        else {
            return true;
        }
    }

    public function deleteUser($id) {
        $q = $this->db->prepare("DELETE FROM `uzivatele` WHERE `id` = :id");
        $q->bindParam(':id', htmlspecialchars(stripslashes($id), ENT_QUOTES, 'UTF-8'));
        $ret = $q->execute();

        if($ret == 0) {
            return false;
        }

        else {
            return true;
        }
    }

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
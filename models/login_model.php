<?php

error_reporting(E_ALL);

class login_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    private function getStudentInfoByObject($obj) {
        return $this->db->selectSingleData("SELECT * FROM `tb_students` WHERE $obj");
    }

    private function checkExists($POST) {
        if ($this->getStudentInfoByObject("email = '{$POST["email"]}'")):
            die(json_encode(array("status" => 1702, "msg" => "Email address already exists")));
        endif;
        if ($this->getStudentInfoByObject("phone = '{$POST["phone"]}'")):
            die(json_encode(array("status" => 1702, "msg" => "Phone number already exists")));
        endif;
        if ($this->getStudentInfoByObject("regNo = '{$POST["regNo"]}'")):
            die(json_encode(array("status" => 1702, "msg" => "Matric. No. already exists")));
        endif;
    }

    public function register($POST) {
        if (!is_string($POST['fullname'])):
            die(json_encode(array("status" => 1702, "msg" => "Fullname must be letters and white space only")));
        endif;
        if (!filter_var($POST["email"], FILTER_VALIDATE_EMAIL)):
            die(json_encode(array("status" => 1702, "msg" => "Email address not valid")));
        endif;
        if (strlen($POST['phone']) != 11 || !filter_var($POST["phone"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(080|091|090|070|081)+[0-9]{8}$/")))):
            die(json_encode(array("status" => 1702, "msg" => "Invalid Phone Number")));
        endif;
        if (!filter_var($POST["regNo"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^([PAS])+\/+([CSC])+\/+([0-9]{2})+\/+([0-9]{3})+$/")))):
            die(json_encode(array("status" => 1702, "msg" => "Invalid Matric No")));
        endif;
        $this->checkExists($POST);
        $this->SaveNew($POST);
    }

    private function SaveNew($data) {
        $data["fullname"] = ucwords(strtolower($data["fullname"]));
        $password = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $image = $this->make_avatar(substr($data["fullname"], 0, 1), "students/");
        $dataArr = array_merge(array("password" => $password, "image" => $image), $data);
        if ($this->db->insert("tb_students", $dataArr)):
            $this->sendEmail($password, $data["email"], $data["fullname"]);
            die(json_encode(array("status" => 1701, "msg" => "Your account successfully created check your email for login details.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to create new account.")));
    }

    public function run() {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
        $INPUT = array(':email' => $username, ':password' => $password);
        $sth = $this->db->prepare("SELECT *, fullname name FROM tb_students WHERE email = :email AND password = :password");
        $sth->execute($INPUT);
        $count = $sth->rowCount();
        $image = 'uploads/students/';
        $role = 3;
        if ($count == 0) {
            $sth = $this->db->prepare("SELECT *, fullname name FROM tb_supervisors WHERE email = :email AND password = :password");
            $sth->execute($INPUT);
            $count = $sth->rowCount();
            $image = 'uploads/supervisors/';
            $role = 2;
            if ($count == 0) {
                $sth = $this->db->prepare("SELECT * FROM tb_users WHERE email = :email AND password = :password");
                $sth->execute($INPUT);
                $count = $sth->rowCount();
                $image = 'uploads/users/';
                $role = 1;
            }
        }
        if ($count > 0) {
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            $json = array("id" => $data["id"], "name" => $data["name"], "email" => $data["email"], "role" => $role, "image" => $image . $data["image"], "phone" => $data["phone"]);
            die(json_encode(array("status" => 1701, "msg" => "", "body" => $json)));
        }
        die(json_encode(array("status" => 1702, "msg" => "Invalid username or password")));
    }

    public function userAuthentication() {
        $sth = $this->db->prepare("SELECT * FROM users WHERE email = :email AND password = :password AND isdeleted = :isdeleted");
        $sth->execute(array(
            ':email' => $_POST['username'],
            ':password' => Hash::create('sha256', $_POST['password'], HASH_PASSWORD_KEY),
            ':isdeleted' => 0
        ));
        $data = $sth->fetch();

        $count = $sth->rowCount();
        if ($count > 0) {
            return array("result" => 1, "data" => $data);
        } else {
            return array("result" => -1, "data" => $data);
        }
    }

}

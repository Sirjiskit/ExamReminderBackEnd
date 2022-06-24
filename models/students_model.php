<?php

error_reporting(E_ALL);

class students_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getStudents($start, $end): array {
        $clause = '';
        return $this->db->select("SELECT `id`, `regNo`, `fullname`, `email`, `phone`, `image`, `dateAdded` FROM "
                        . "`tb_students` ORDER BY fullname LIMIT {$start},{$end}");
    }

    public function getTotalStudents(): int {
        $list = $this->db->select("SELECT * FROM `tb_students`");
        return count($list);
    }

    public function getStudentsMobileLists($page) {
        $end = 10;
        (int) $start = ($page - 1) * $end;
        $lists = $this->getStudents($start, $end); //filterBy
        $total = $this->getTotalStudents();
        $data = array();
        foreach ($lists as $item) {
            $data[] = array("id" => $item["id"], "regNo" => $item['regNo'], "fullname" => ucwords(strtolower($item['fullname'])),
                "phone" => $item["phone"], "email" => $item['email'], "image" => $item['image'] ? "uploads/students/" . $item["image"] : "no-image.gif");
        }
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => $total, "data" => $data))));
    }

    private function email($email = "") {
        $checkEmail = $this->db->selectSingleData("SELECT * FROM student WHERE email = '{$email}'");
        return $checkEmail ? true : false;
    }

    private function MatricNoExists($regNo = "") {
        $check = $this->db->selectSingleData("SELECT * FROM student WHERE regNo = '{$regNo}'");
        return $check ? true : false;
    }

    public function add($POST) {
        if ((!filter_var($POST["regNo"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^([A-Z]{2})+\/+([A-Z]{3})+\/+([A-Z]{2})+\/+([0-9]{2})+\/+([0-9]{3})+$/"))) && !filter_var($POST["regNo"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^([A-Z]{2})+\/+([A-Z]{2})+\/+([A-Z]{2})+\/+([0-9]{2})+\/+([0-9]{3})+$/"))) && !filter_var($POST["regNo"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^([A-Z]{2})+\/+([A-Z]{3})+\/+([A-Z]{3})+\/+([0-9]{2})+\/+([0-9]{3})+$/"))) && !filter_var($POST["regNo"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^([A-Z]{2})+\/+([A-Z]{2})+\/+([A-Z]{3})+\/+([0-9]{2})+\/+([0-9]{3})+$/"))))):
            die(json_encode(array("status" => 1702, "msg" => "Invalid registration number!")));
        endif;
        if (!filter_var($POST["email"], FILTER_VALIDATE_EMAIL)):
            die(json_encode(array("status" => 1703, "msg" => "Invalid email address!")));
        endif;
        if (!filter_var($POST["phone"], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(080|091|090|070|081)+[0-9]{8}$/")))):
            die(json_encode(array("status" => 1704, "msg" => "Invalid Phone number!")));
        endif;
        if ($this->MatricNoExists($POST["regNo"])):
            die(json_encode(array("status" => 1703, "msg" => "RegNo already exists!")));
        endif;
        if ($this->email($POST["email"])):
            die(json_encode(array("status" => 1703, "msg" => "Email address already exists!")));
        endif;
        $this->SaveNew($POST);
    }

    private function SaveNew($data) {
        $data["fullname"] = ucwords(strtolower($data["fullname"]));
        $pass = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $password = Hash::create('sha256', $pass, HASH_PASSWORD_KEY);
        $image = $this->make_avatar(substr($data["fullname"], 0, 1), "students/");
        $dataArr = array_merge(array("password" => $password, "image" => $image), $data);
        if ($this->db->insert("student", $dataArr)):
            $this->sendEmail($pass, $data["email"], $data["fullname"]);
            die(json_encode(array("status" => 1701, "msg" => "New student successfully added.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to add new student.")));
    }

    public function multipleDelete($POST) {
        $data = json_decode($POST["data"]);
        $deleted = 0;
        foreach ($data as $id) {
            if ($this->db->delete("tb_students", "id = $id")):
                $deleted++;
            endif;
        }
        die(json_encode(array("status" => 1701, "msg" => "{$deleted} student deleted.")));
    }

}

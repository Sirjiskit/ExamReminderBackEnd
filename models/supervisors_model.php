<?php

error_reporting(E_ALL);

class supervisors_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getSupervisors($start, $end): array {
        return $this->db->select("SELECT `id`, `fullname`, `email`, `phone`, `image`, `dateAdded` FROM "
                        . "`tb_supervisors` ORDER BY fullname LIMIT {$start},{$end}");
    }

    public function getTotalSuperviors(): int {
        $list = $this->db->select("SELECT * FROM `tb_supervisors`");
        return count($list);
    }

    public function getLists($page) {
        $end = 10;
        (int) $start = ($page - 1) * $end;
        $lists = $this->getSupervisors($start, $end); //filterBy
        $total = $this->getTotalSuperviors();
        $data = array();
        foreach ($lists as $item) {
            $data[] = array("id" => $item["id"], "fullname" => ucwords(strtolower($item['fullname'])),
                "phone" => $item["phone"], "email" => $item['email'], "image" => $item['image'] ? "uploads/supervisors/" . $item["image"] : "no-image.gif");
        }
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => $total, "data" => $data))));
    }

    public function multipleDelete($POST) {
        $data = json_decode($POST["data"]);
        $deleted = 0;
        foreach ($data as $id) {
            if ($this->db->delete("tb_supervisors", "id = $id")):
                $deleted++;
            endif;
        }
        die(json_encode(array("status" => 1701, "msg" => "{$deleted} record(s) deleted.")));
    }

    public function getDropDown(): array {
        $data = $this->db->select("SELECT `id`, CONCAT(`fullname`,' : ', `email`) name FROM `tb_supervisors` ORDER BY fullname");
        die(json_encode(array("status" => 1701, "msg" => "", "body" => $data)));
    }

    private function getSupInfoByObject($obj) {
        return $this->db->selectSingleData("SELECT * FROM `tb_supervisors` WHERE $obj");
    }

    public function add($POST) {
        if ($this->getSupInfoByObject("email = '{$POST["email"]}'")):
            die(json_encode(array("status" => 1702, "msg" => "Email address already exists")));
        endif;
        if ($this->getSupInfoByObject("phone = '{$POST["phone"]}'")):
            die(json_encode(array("status" => 1702, "msg" => "Phone number already exists")));
        endif;
        $POST["fullname"] = ucwords(strtolower($POST["fullname"]));
        $password = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $image = $this->make_avatar(substr($POST["fullname"], 0, 1), "supervisors/");
        $dataArr = array_merge(array("password" => $password, "image" => $image), $POST);
        if ($this->db->insert("tb_supervisors", $dataArr)):
            $this->sendEmail($password, $POST["email"], $POST["fullname"]);
            die(json_encode(array("status" => 1701, "msg" => "New supervisor successfully created.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to create new supervisor.")));
    }

}

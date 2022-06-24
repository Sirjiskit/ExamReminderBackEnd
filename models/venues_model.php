<?php

error_reporting(E_ALL);

class venues_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getVenues($start, $end): array {
        return $this->db->select("SELECT `id`, `name` FROM `tb_venues` ORDER BY name LIMIT {$start},{$end}");
    }

    public function getTotalVenues(): int {
        $list = $this->db->select("SELECT * FROM `tb_venues`");
        return count($list);
    }

    public function getLists($page) {
        $end = 10;
        (int) $start = ($page - 1) * $end;
        $lists = $this->getVenues($start, $end); //filterBy
        $total = $this->getTotalVenues();
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => $total, "data" => $lists))));
    }

    public function add($data) {
        $data["name"] = strtoupper($data["name"]);
        if ($this->db->insert("tb_venues", $data)):
            die(json_encode(array("status" => 1701, "msg" => "New venue successfully created.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to add new venue.")));
    }

    public function getDropDown(): array {
        $data = $this->db->select("SELECT `id`,`name` FROM `tb_venues` ORDER BY name");
        die(json_encode(array("status" => 1701, "msg" => "", "body" => $data)));
    }

}

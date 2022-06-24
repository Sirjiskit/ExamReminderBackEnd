<?php

error_reporting(E_ALL);

class courses_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getCourses($start, $end, $search = ''): array {
        return $this->db->select("SELECT `id`, `code`, `title`, `level`, `semester` FROM `tb_courses` $search ORDER BY code, level LIMIT {$start},{$end}");
    }

    public function getTotalCourses($search = ''): int {
        $list = $this->db->select("SELECT * FROM `tb_courses` $search");
        return count($list);
    }

    public function getLists($page) {
        $end = 10;
        (int) $start = ($page - 1) * $end;
        $lists = $this->getCourses($start, $end); //filterBy
        $total = $this->getTotalCourses();
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => $total, "data" => $lists))));
    }

    public function getSearch($POST) {
        $end = 10;
        (int) $start = ((int) $POST["page"] - 1) * $end;
        (int) $sId = (int) intval($POST["sId"]);
        $search = " WHERE code LIKE '%{$POST["search"]}%' OR title LIKE '%{$POST["search"]}%'";
        $lists = $this->getCourses($start, $end, $search); //filterBy
        $total = $this->getTotalCourses($search);
        $json = array();
        $newTotal = 0;
        foreach ($lists as $row):
            if (!$this->isCourseRegister($sId, $row["id"])):
                $json[] = $row;
            else:
                $newTotal++;
            endif;
        endforeach;
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => ($total - $newTotal), "data" => $json))));
    }

    private function isCourseRegister($sId, $cId) {
        return $this->db->selectSingleData("SELECT * FROM `tb_cou_registered` WHERE cId = $cId AND sId=$sId");
    }

    public function getCoursesByCode($code) {
        return $this->db->selectSingleData("SELECT * FROM `tb_courses` WHERE code = '{$code}'");
    }

    public function add($data) {
        if ($this->getCoursesByCode($data["code"])):
            die(json_encode(array("status" => 1702, "msg" => "Course code already exists")));
        endif;
        if ($this->db->insert("tb_courses", $data)):
            die(json_encode(array("status" => 1701, "msg" => "New course successfully saved.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to save new course.")));
    }

    public function getDropDown(): array {
        $data = $this->db->select("SELECT `id`, CONCAT(`code`,' : ', `title`) name FROM `tb_courses` ORDER BY name");
        die(json_encode(array("status" => 1701, "msg" => "", "body" => $data)));
    }

    public function register($POST) {
        $data = json_decode($POST["data"]);
        (int) $sId = (int) intval($POST["sId"]);
        $total = count($data);
        $reg = 0;
        foreach ($data as $id) {
            if ($this->db->insert("tb_cou_registered", array("sId" => $sId, "cId" => $id))):
                $reg++;
            endif;
        }
        $fail = $total - $reg;
        die(json_encode(array("status" => 1701, "msg" => "{$reg} course(s) successfully registered<br>{$fail} course(s) unsuccessful.")));
    }

    public function getRegisteredCourse($sId) {
        $json = $this->db->select("SELECT c.* FROM `tb_courses` c JOIN tb_cou_registered r ON c.id = r.cId WHERE r.sId=$sId ORDER BY code, level");
        die(json_encode(array("status" => 1701, "msg" => "", "body" => $json)));
    }

}

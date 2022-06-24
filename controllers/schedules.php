<?php
error_reporting(E_ALL);
class schedules extends Controller {

    function __construct() {
        parent::__construct();
    }

    function list($page) {
        $this->cors();
        $this->model->getLists($page);
        exit();
    }

    function add() {
        $this->model->add(filter_input_array(INPUT_POST));
    }

    function update() {
        $this->model->update(filter_input_array(INPUT_POST));
    }

    function drownDown() {
        $this->model->getDropDown();
    }

    function stat() {
        $this->model->statistics();
    }

    function read($sId, $role) {
        if ($role == "student"):
            $this->model->getUserStudentSheduled($sId);
        endif;
    }
    function notifyStudents(){
        $this->model->sendNotifyStudents();
    }
}

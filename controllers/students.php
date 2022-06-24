<?php

class students extends Controller {

    function __construct() {
        parent::__construct();
    }

    function list($page) {
        $this->cors();
        $this->model->getStudentsMobileLists($page);
        exit();
    }

    function add() {
        $this->model->add(filter_input_array(INPUT_POST));
    }

    function multi_delete() {
        $this->model->multipleDelete(filter_input_array(INPUT_POST));
    }

}

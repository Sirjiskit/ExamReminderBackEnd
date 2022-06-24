<?php

class supervisors extends Controller {

    function __construct() {
        parent::__construct();
    }

    function list($page) {
        $this->cors();
        $this->model->getLists($page);
        exit();
    }

    function multi_delete() {
        $this->model->multipleDelete(filter_input_array(INPUT_POST));
    }

    function drownDown() {
        $this->model->getDropDown();
    }
     function add() {
        $this->model->add(filter_input_array(INPUT_POST));
    }

}

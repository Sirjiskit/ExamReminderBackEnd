<?php

class courses extends Controller {

    function __construct() {
        parent::__construct();
    }

    function list($page) {
        $this->cors();
        $this->model->getLists($page);
        exit();
    }

    function search() {
        $this->model->getSearch(filter_input_array(INPUT_POST));
    }

    function add() {
        $this->model->add(filter_input_array(INPUT_POST));
    }

    function register() {
        $this->model->register(filter_input_array(INPUT_POST));
    }

    function registered($sId) {
        $this->model->getRegisteredCourse($sId);
    }

    function drownDown() {
        $this->model->getDropDown();
    }

}

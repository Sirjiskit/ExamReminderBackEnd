<?php

class login extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        header("location: " . URL);
    }

    function register() {
        $this->model->register(filter_input_array(INPUT_POST));
    }

    function run() {
        $this->model->run();
    }

    function userAuthentication() {//userAuthentication
        echo json_encode($this->model->userAuthentication());
    }

}

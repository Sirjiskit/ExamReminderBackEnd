<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author Jiskit
 */
class View {

    //put your code here
    function __construct() {
        //echo 'this is the view';
    }

    public function render($name) {
        require 'views/' . $name . '.php';
    }

}

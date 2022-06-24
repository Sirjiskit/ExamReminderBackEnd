<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author Jiskit
 */
class Session {
    //put your code here
     public static function init()
    {
        @session_start();
    }
    
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key)
    {
        if (isset($_SESSION[$key])):
            return $_SESSION[$key];
        endif;
    }
    
    public static function destroy()
    {
        $_SESSION = array();
        session_regenerate_id(); 
        unset($_SESSION);
        session_destroy();
    }    
}

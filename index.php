<?php


date_default_timezone_set('Africa/Lagos');

require 'config.php';
require 'util/Auth.php';
//require 'util/DataTable.php';
require 'util/vendor/autoload.php';
require("util/vendor/PHPMailer-master/src/PHPMailer.php");
require("util/vendor/PHPMailer-master/src/SMTP.php");
require("util/vendor/PHPMailer-master/src/Exception.php");
spl_autoload_register(function ($class) {
    require LIBS . $class . ".php";
});
//require 'processBackupCron.php';

$bootstrap = new Bootstrap();

$bootstrap->init();


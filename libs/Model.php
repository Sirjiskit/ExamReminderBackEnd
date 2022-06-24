<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author Jiskit
 */
class Model {

    // push message title
    private $title;
    private $message;
    private $image;
    // push message payload
    private $data;
    // flag indicating whether to show the push
    // notification or not
    // this flag will be useful when perform some opertation
    // in background when push is recevied
    private $is_background;

    //put your code here
    function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }

    function getStringBetween($string, $start, $end) {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function base64ToImageFile($base64String, $ext, $uploadDirectory) {
        $filenamePath = md5(time() . uniqid()) . "." . $ext;
        $decoded = base64_decode($base64String);
        file_put_contents($uploadDirectory . "/" . $filenamePath, $decoded);

        return $filenamePath;
    }

    public $toEmail = [];
    public $emailSubject;
    public $emailBody;
    public $emailAttach = [];
    public $errorInfo = '';

    public function sendLoginInfo() {
        $this->errorInfo = '';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP(true);
        $mail->Host = "localhost";
        $mail->SMTPDebug = 0;
        $mail->Port = 25; //465 or 587
        //$mail->SMTPSecure = 'ssl';
        //$mail->SMTPAuth = true;
        //Authentication
        $mail->Username = "localhost";
        //$mail->Username = "jigbashio@gmail.com";
        //$mail->Password = "@Jiskit015";
        $mail->addAddress($this->toEmail);
        $mail->setFrom("no-reply@aber.edu.ng", "Exam Schedule Reminder System");
        $mail->isHTML(true);
        $mail->Subject = $this->emailSubject;
        $body = preg_replace('/\\\\/', '', $this->emailBody);
        $mail->msgHTML($body);
        $mail->isHTML(true);
        if (!$mail->send()):
            $this->errorInfo = $mail->ErrorInfo;
            return false;
        endif;
        return true;
    }

    public function sendEmail($password, $email, $name) {
        $subject = "no-reply";
        $message = "<h1>Dear {$name}</h1>";
        $message .= "<p>Welcome to The FPB Admission System below are your login details:</p>";
        $message .= "<p>Username: {$email}<br>Password:{$password}</p>";
        $e = new Email();
        $e->toEmail = $email;
        $e->emailSubject = $subject;
        $e->emailBody = $message;
        if (!$e->sendLoginInfo()) {
            $this->error = $e->errorInfo;
            exit(0);
        }
        $this->error = $e->errorInfo;
    }

    public function sendMessege($email, $name, $messege) {
        $subject = "no-reply";
        $message = "<h1>Dear {$name}</h1>";
        $message .= $messege;
        $e = new Email();
        $e->toEmail = $email;
        $e->emailSubject = $subject;
        $e->emailBody = $message;
        if (!$e->sendLoginInfo()) {
            $this->error = $e->errorInfo;
            exit(0);
        }
        $this->error = $e->errorInfo;
    }

    function cors() {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    function getTokenHeader() {
        $header = null;
        if (isset($_SERVER['Authorization'])):
            $header = trim($_SERVER['Authorization']);
        elseif (function_exists('apache_request_headers')):
            $requestHeaders = apache_request_headers();
            $requestHeader = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeader["Authorization"]) && $requestHeader["Authorization"]):
                $header = trim($requestHeader["Authorization"]);
            endif;
        endif;
        return $header;
    }

    function getToken() {
        $headers = $this->getTokenHeader();
        if (!empty($headers)):
            return explode(" ", $headers)[1];
        endif;
        return null;
    }

    function make_avatar($character, $link) {
        $path = time() . ".png";
        $image = imagecreate(200, 200);
        $red = rand(0, 255);
        $green = rand(0, 255);
        $blue = rand(0, 255);
        imagecolorallocate($image, $red, $green, $blue);
        $textcolor = imagecolorallocate($image, 255, 255, 255);

        $font = PUBLIC_DIR . '/font/arial.ttf';

        imagettftext($image, 100, 0, 55, 150, $textcolor, $font, $character);
        imagepng($image, UPLOAD_DIR . $link . $path);
        imagedestroy($image);
        return $path;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setImage($imageUrl) {
        $this->image = $imageUrl;
    }

    public function setPayload($data) {
        $this->data = $data;
    }

    public function setIsBackground($is_background) {
        $this->is_background = $is_background;
    }

    public function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['is_background'] = $this->is_background;
        $res['data']['message'] = $this->message;
        $res['data']['image'] = $this->image;
        $res['data']['payload'] = $this->data;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        return $res;
    }

}

class Email {

    public $toEmail = [];
    public $emailSubject;
    public $emailBody;
    public $emailAttach = [];
    public $errorInfo = '';

    public function sendLoginInfo() {
        $this->errorInfo = '';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP(true);
        $mail->CharSet = "UTF-8";
        $mail->Host = "localhost";
//        $mail->Host = "chi119.greengeeks.net";
        $mail->SMTPDebug = 0;
//         $mail->Port = 465; //465 or 587
        $mail->Port = 25; //465 or 587
        //$mail->SMTPSecure = 'ssl';
        //$mail->SMTPAuth = true;
        //Authentication
        $mail->Username = "localhost";
//        $mail->Username = "info@dootech.com.ng";
//        $mail->Password = "@Jiskit015";
        $mail->addAddress($this->toEmail);
        $mail->setFrom("info@dootech.com.ng", "Exam Schedule Reminder System");
        $mail->isHTML(true);
        $mail->Subject = $this->emailSubject;
        $body = preg_replace('/\\\\/', '', $this->emailBody);
        $mail->msgHTML($body);
        $mail->isHTML(true);
        if (!$mail->send()):
            $this->errorInfo = $mail->ErrorInfo;
            return false;
        endif;
        return true;
    }

}

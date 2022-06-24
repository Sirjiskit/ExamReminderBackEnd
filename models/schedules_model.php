<?php

error_reporting(E_ALL);

class schedules_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    private function getSupervisorsList($id) {
        return $this->db->select("SELECT s2.id, s2.fullname name FROM tb_shedules_supervisors s1 JOIN `tb_supervisors` s2 ON s1.supId=s2.id WHERE s1.sId = $id");
    }

    private function getVenueInfo($id) {
        return $this->db->selectSingleData("SELECT v.id, v.name FROM tb_shedules s JOIN `tb_venues` v ON s.vId=v.id WHERE v.id = $id");
    }

    private function getCourseInfo($id) {
        return $this->db->selectSingleData("SELECT c.id, CONCAT(`code`,' : ', `title`) name FROM tb_shedules s JOIN `tb_courses` c ON s.cId=c.id WHERE c.id = $id");
    }

    public function getSchedule($start, $end): array {
        return $this->db->select("SELECT * FROM `tb_shedules` ORDER BY date, timeStart ASC LIMIT {$start},{$end}");
    }

    public function getTotalSchedule(): int {
        $list = $this->db->select("SELECT * FROM `tb_shedules`");
        return count($list);
    }

    public function getLists($page) {
        $end = 10;
        (int) $start = ($page - 1) * $end;
        $lists = $this->getSchedule($start, $end); //filterBy
        $total = $this->getTotalSchedule();
        $json = array();
        foreach ($lists as $res):
            $vanue = $this->getVenueInfo($res["vId"]);
            $course = $this->getCourseInfo($res["cId"]);
            $supList = $this->getSupervisorsList($res["id"]);
            $sTime = date("h:i A", strtotime($res['timeStart']));
            $eTime = date("h:i A", strtotime($res['timeEnd']));
            $json[] = array("id" => $res["id"], "date" => $res["date"], "timeStart" => $sTime, "timeEnd" => $eTime,
                "course" => $course, "venue" => $vanue, "supervisors" => $supList);
        endforeach;
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("total" => $total, "data" => $json))));
    }

    private function isCourseScheduled($cId) {
        return $this->db->selectSingleData("SELECT * FROM `tb_shedules` WHERE cId = $cId");
    }

    public function getScheduleListByDate($date): array {
        return $this->db->select("SELECT * FROM `tb_shedules` WHERE date = '{$date}'");
    }

    public function add($POST) {
        if ($this->isCourseScheduled((int) $POST["cId"])):
            die(json_encode(array("status" => 1702, "msg" => "Selected course already scheduled")));
        endif;
        $data = json_decode($POST["supId"]);
        unset($POST["supId"]);
        $sTime = strtotime($POST["timeStart"]);
        $eTime = strtotime($POST["timeEnd"]);
        if ($sTime > $eTime) {
            die(json_encode(array("status" => 1702, "msg" => "Exam time start can not be greater than end time")));
        }
        if ($sTime == $eTime) {
            die(json_encode(array("status" => 1702, "msg" => "Exam duration can not be zero minute")));
        }
        $list = $this->getScheduleListByDate($POST["date"]);
        foreach ($list as $row) {
            $pst = strtotime($POST["timeStart"]);
            $pet = strtotime($POST["timeEnd"]);
            $rst = strtotime($row["timeStart"]);
            $ret = strtotime($row["timeEnd"]);
            if (($pst >= $rst && $pst <= $ret) || ($pst == $rst && ($pet >= $ret || $pet <= $ret))) {
                die(json_encode(array("status" => 1702, "msg" => "Sorry another exam already scheduled at the selected time")));
            }
            if (($POST["vId"] == $row["vId"]) && ($pst >= $rst && $pst <= $ret) || ($pst == $rst)) {
                die(json_encode(array("status" => 1702, "msg" => "Sorry selected vaenue is not available")));
            }
        }
        $sub = $this->checkSup($data, $POST["date"], strtotime($POST["timeEnd"]), strtotime($POST["timeStart"]));
        if ($sub):
            die(json_encode(array("status" => 1702, "msg" => "{$sub} already scheduled for another invigilations")));
        endif;
        if ($this->db->insert("tb_shedules", $POST)):
            $id = $this->db->lastId();
            $this->saveSup($data, $id);
            die(json_encode(array("status" => 1701, "msg" => "Course successfully schedulled.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to schedule course.")));
    }

    private function saveSup($data, $sId) {
        foreach ($data as $id) {
            $this->db->insert("tb_shedules_supervisors", array("sId" => $sId, "supId" => $id));
        }
    }

    private function checkSup($data, $date, $pet, $pst) {
        $list = $this->db->select("SELECT timeStart,timeEnd,supId FROM tb_shedules_supervisors s1 JOIN `tb_shedules` s2 ON s1.sId=s2.id WHERE date = '{$date}'");
        foreach ($data as $id) {
            foreach ($list as $res):
                $rst = strtotime($res["timeStart"]);
                $ret = strtotime($res["timeEnd"]);
                if (($res["supId"] == $id) && ($pst >= $rst && $pst <= $ret) || ($pst == $rst)) {
                    return $this->getSupName($id);
                }
            endforeach;
        }
        return false;
    }

    private function getSupName($id) {
        $row = $this->db->selectSingleData("SELECT fullname FROM tb_supervisors s1 JOIN tb_shedules_supervisors s2 ON s1.id = s2.supId WHERE s2.supId = $id");
        return $row["fullname"];
    }

    private function isCourseUpdateScheduled($cId, $id) {
        return $this->db->selectSingleData("SELECT * FROM `tb_shedules` WHERE cId = $cId and id != $id");
    }

    public function update($POST) {
        $myId = (int) $POST["id"];
        unset($POST["id"]);
        if ($this->isCourseUpdateScheduled((int) $POST["cId"], $myId)):
            die(json_encode(array("status" => 1702, "msg" => "Selected course already scheduled")));
        endif;
        $data = json_decode($POST["supId"]);
        unset($POST["supId"]);
        $sTime = strtotime($POST["timeStart"]);
        $eTime = strtotime($POST["timeEnd"]);
        $dur = (float) $eTime - (float) $sTime;
        if ($sTime > $eTime) {
            die(json_encode(array("status" => 1702, "msg" => "Exam time start can not be greater than end time")));
        }
        if ($dur == 0) {
            die(json_encode(array("status" => 1702, "msg" => "Exam duration can not be zero minute")));
        }
        $list = $this->getScheduleListByDate($POST["date"]);
        foreach ($list as $row) {
            $pst = strtotime($POST["timeStart"]);
            $pet = strtotime($POST["timeEnd"]);
            $rst = strtotime($row["timeStart"]);
            $ret = strtotime($row["timeEnd"]);
            if (($pst >= $rst && $pst <= $ret) || ($pst == $rst && ($pet >= $ret || $pet <= $ret)) && $myId != $row["id"]) {
                die(json_encode(array("status" => 1702, "msg" => "Sorry another exam already scheduled at the selected time")));
            }
            if (($POST["vId"] == $row["vId"]) && ($pst >= $rst && $pst <= $ret) || ($pst == $rst) && $myId != $row["id"]) {
                die(json_encode(array("status" => 1702, "msg" => "Sorry selected vaenue is not available")));
            }
        }
        $sub = $this->checkUpdateSup($data, $POST["date"], strtotime($POST["timeEnd"]), strtotime($POST["timeStart"]), (int) $myId);
        if ($sub):
            die(json_encode(array("status" => 1702, "msg" => "{$sub} already scheduled for another invigilations")));
        endif;
        if ($this->db->update("tb_shedules", $POST, "id={$myId}")):
            if ($this->db->deleteAll("tb_shedules_supervisors", "sId = $myId")):
                $this->saveSup($data, $myId);
                die(json_encode(array("status" => 1701, "msg" => "schedulled successfully updated.")));
            endif;
            die(json_encode(array("status" => 1702, "msg" => "Unable to update supervisors.")));
        endif;
        die(json_encode(array("status" => 1702, "msg" => "Unable to update schedule.")));
    }

    private function checkUpdateSup($data, $date, $pet, $pst, $id) {
        $list = $this->db->select("SELECT timeStart,timeEnd,supId FROM tb_shedules_supervisors s1 JOIN `tb_shedules` s2 ON s1.sId=s2.id WHERE date = '{$date}' AND s2.id != {$id}");
        foreach ($data as $id) {
            foreach ($list as $res):
                $rst = strtotime($res["timeStart"]);
                $ret = strtotime($res["timeEnd"]);
                if (($res["supId"] == $id) && ($pst >= $rst && $pst <= $ret) || ($pst == $rst)) {
                    return $this->getSupName($id);
                }
            endforeach;
        }
        return false;
    }

    public function statistics() {
        $venue = $this->db->select("SELECT * FROM `tb_venues`");
        $courses = $this->db->select("SELECT * FROM `tb_courses`");
        $students = $this->db->select("SELECT * FROM `tb_students`");
        $supersors = $this->db->select("SELECT * FROM `tb_supervisors`");
        $json = array("venue" => count($venue), "courses" => count($courses), "students" => count($students), "supervisors" => count($supersors));
        die(json_encode(array("status" => 1701, "msg" => "", "body" => $json)));
    }

    private function getStudentScheduled($sId) {
        return $this->db->select("SELECT s.* FROM tb_cou_registered c JOIN `tb_shedules` s ON c.cId=s.cId WHERE c.sId = $sId");
    }

    public function getUserStudentSheduled($sId) {
        $allList = array();
        $upComing = array();
        $nextExam = array();
        $activeExam = array();
        $lists = $this->getStudentScheduled($sId);
        foreach ($lists as $res):
            $currentTime = time();
            $vanue = $this->getVenueInfo($res["vId"]);
            $course = $this->getCourseInfo($res["cId"]);
            $sTime = date("h:i A", strtotime($res['timeStart']));
            $eTime = date("h:i A", strtotime($res['timeEnd']));
            $allList[] = array("id" => $res["id"], "date" => $res["date"], "timeStart" => $sTime, "timeEnd" => $eTime,
                "course" => $course, "venue" => $vanue);
        endforeach;
        $currentTime = time();
        foreach ($allList as $res):
            $timeStart = strtotime($res['timeStart']);
            $toDate = date("Y-m-d");
            if ((strtotime($res['date']) > strtotime($toDate)) || ($currentTime <= $timeStart && strtotime($res['date']) == strtotime($toDate))):
                $upComing[] = $res;
            endif;
        endforeach;
        foreach ($allList as $res):
            $toDate = date("Y-m-d");
            $timeStart = strtotime($res['timeStart']);
            $endStart = strtotime($res['timeEnd']);
            $time1 = strtotime(date("h:i A", $currentTime));
            $diff = round((abs($timeStart - $time1) / ( 60 * 60 )), 2);
            $date = date("Y-m-d", strtotime($res['date']));
            if ((strtotime($res['date']) == strtotime($toDate)) && ($time1 <= $timeStart) && ($diff <= 1 && $diff > 0)):
                $nextExam[] = array_merge($res, array("reminder" => $diff));
            endif;
            if ((strtotime($date) == strtotime($toDate)) && ($time1 >= $timeStart && $endStart >= $time1)):
                $activeExam[] = array_merge($res, array("reminder" => $diff));
            endif;
        endforeach;
        //"Hello dear we remind you that Exam will be starting in 1 hour time"
        die(json_encode(array("status" => 1701, "msg" => "", "body" => array("upcoming" => $upComing,
                "all" => $allList, "nextExam" => $nextExam, "activeExam" => $activeExam))));
    }

    public function notifyStudents() {
        $send = $this->sendNotification("Exam Reminder", "Hello dear we remind you that Exam will be starting in 1 hour time");
        die($send);
    }

    public function sendNotifyStudents() {
        $studList = $this->db->select("SELECT s.id,s.fullname,s.phone,s.email FROM `tb_students` s JOIN `tb_cou_registered` c ON s.id = c.sId");
        foreach ($studList as $row):
            $this->preperedNotifyStudents($row);
        endforeach;
    }

    public function preperedNotifyStudents($POST) {
        $lists = $this->getStudentScheduled($POST["id"]);
        $currentTime = time();
        $message = "This is to notify you that your exam will be starting 1 hour time";
        $title = "Exam Reminder";
        foreach ($lists as $res):
            $toDate = date("Y-m-d");
            $timeStart = strtotime($res['timeStart']);
            $time1 = strtotime(date("h:i A", $currentTime));
            $diff = round((abs($timeStart - $time1) / ( 60 * 60 )), 2);
            if ((strtotime($res['date']) == strtotime($toDate)) && ($time1 <= $timeStart) && ($diff <= 1 && $diff > 0)):
                $this->sendSMS($message, $POST["phone"]);
                $this->sendMessege($POST["email"], $POST["fullname"], $message);
                $this->sendNotification($title, $message);
            endif;
        endforeach;
    }

    public function sendNotification($title, $message) {
        $this->setImage('');
        $this->setIsBackground(false);
        $this->setTitle($title);
        $this->setMessage($message);
        $json = $this->getPush();
        $response = $this->sendToTopic('my_exam', $json);
        return $response;
    }

    public function sendSMS($message, $phone) {
        //allow remote access to this script, replace the * to your domain e.g http://www.example.com if you wish to recieve requests only from your server
        header("Access-Control-Allow-Origin: *");
//rebuild form data
        $postdata = http_build_query(
                array('username' => "jigbashio@gmail.com", 'password' => "@Jiskit015", 'message' => $message, 'mobiles' => $phone, 'sender' => "Dootech")
        );
//prepare a http post request
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
//craete a stream to communicate with betasms api
        $context = stream_context_create($opts);
//get result from communication
        $result = file_get_contents('http://login.betasms.com/api/', false, $context);
//return result to client, this will return the appropriate respond code
        echo $result;
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $message) {
        $fields = array(
            'to' => '/topics/' . $to,
            'data' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    private function sendPushNotification($fields) {
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        return $result;
    }

}

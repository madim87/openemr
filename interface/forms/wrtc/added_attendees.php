<?php
require_once("../../globals.php");

$authUserID = $_SESSION['authUserID'];
if ($_POST['room_id']) {
    $room_id = $_POST['room_id'];
    $allAttendeesRecords = sqlGetAssoc("SELECT * FROM wrtc_patient_attendees WHERE room_id=? AND user_id=?", array($room_id, $authUserID));
    $allPids = [];
    foreach ($allAttendeesRecords as $record) {
        $allPids[$record['pid']] = $record['pid'];
    }
    echo json_encode($allPids);

}

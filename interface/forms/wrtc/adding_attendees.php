<?php
require_once("../../globals.php");

$authUserID = $_SESSION['authUserID'];

if ($_POST['room_id']) {
    if ($_POST["addedPids"]) {
        $pids = $_POST["addedPids"];
    } else {
        $pids = [];
    }
    $room_id = $_POST['room_id'];
    $allAttendeesRecord = sqlGetAssoc("SELECT * FROM wrtc_patient_attendees WHERE room_id=? AND user_id=?", array($room_id, $authUserID));
    $allPids = [];
    foreach ($allAttendeesRecord as $record) {
        $allPids[$record['pid']*1] = $record['pid'];
    }
    $addPids = [];
    foreach ($pids as $pid) {
        $addPids[$pid*1] = $pid;
        $addedAttendeesRecord = sqlQuery("SELECT * FROM wrtc_patient_attendees WHERE room_id=? AND pid=? AND user_id=?", array($room_id, $pid, $authUserID));
        if ($addedAttendeesRecord) {
            continue;
        } else {
            sqlStatement("INSERT INTO wrtc_patient_attendees (room_id, user_id, pid, status, created_at, updated_at) " . "VALUES (?,?,?,?,?,?)",
                array($room_id,
                    $authUserID,
                    $pid,
                    'active',
                    NULL,
                    NULL
                )
            );
        }
    }

    $deletePids = array_diff_key($allPids, $addPids);

    foreach ($allPids as $key => $pid) {
        echo "pid: ", $pid;
        if (!array_search($pid, $addPids)){
            echo "i: ", $i;

        } else {
            $deletePids[$pid] = $pid;
        }
    }

    echo "</br> deletePids: ";
    print_r($deletePids);

    if ($deletePids) {
        foreach ($deletePids as $pid) {
            sqlQuery("DELETE FROM wrtc_patient_attendees " .
                "WHERE pid=? AND room_id=? AND user_id=?", array($pid, $room_id, $authUserID));
        }
    }
    $result = $_POST["addedPids"];
    array_push($result, $_POST['room_id']);

    echo json_encode($result);

}

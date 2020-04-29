<?php

use OpenEMR\Common\Http\ClickMeetingRestClient;
require_once("../../globals.php");


if (isset($_POST['room_id'])) {
    try {
        $client = new ClickMeetingRestClient(array('api_key' => 'eu44f171ed09254888e77da774e88ffab076fedf87'));

        try {
            $res = $client->deleteConference($_POST['room_id']);
        } catch (\Exception $e) {
            print_r(json_decode($e->getMessage()));
        }
    }
    catch (\Exception $ex)
    {
        print_r(json_decode($ex->getMessage()));
    }

    sqlQuery("DELETE FROM user_wrtc " .
        "WHERE room_id=?", array($_POST['room_id']));
    sqlQuery("DELETE FROM wrtc_patient_attendees " .
        "WHERE room_id=?", array($_POST['room_id']));
}

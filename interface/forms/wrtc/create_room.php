<?php
require_once("../../globals.php");
use OpenEMR\Common\Http\ClickMeetingRestClient;

$authUserID = $_SESSION['authUserID'];

if ($_POST["roomName"] && $_POST["roomType"]  && $_POST["accessType"]) {

    $roomName = $_POST["roomName"];
    $roomType = $_POST["roomType"];
    $accessType = $_POST["accessType"];
    $password = $_POST["password"] ? $_POST["password"] : '';

    //init ClickMeetingClient
    try {
        $client = new ClickMeetingRestClient(array('api_key' => 'eu44f171ed09254888e77da774e88ffab076fedf87'));
    }
    catch (\Exception $ex)
    {
        print_r(json_decode($ex->getMessage()));
    }
    //Create room
    try {
        $params = array(
            'name' => $roomName,
            'room_type' => $roomType,
            'permanent_room' => 0,
            'access_type' => $accessType,
            'password' => $password
        );
        $room = $client->addConference($params);
    } catch (\Exception $e) {
        print_r(json_decode($e->getMessage()));
    }

    //Create direct-URL
    try {
        $urlHostParams = array(
            'email' => 'a.tkachev@belitsoft.com',
            'nickname' => $_SESSION['authUserID'],
            'role' => 'host',
        );
        $hash = $client->conferenceAutologinHash($room->room->id, $urlHostParams);
    } catch (\Exception $e) {
        print_r(json_decode($e->getMessage()));
    }
    echo "hash", $hash->autologin_hash;
    $room_id = $room->room->id;
    $res = $room->room->room_url;

    echo "room-pass: ", $room->room->password;

    sqlStatement("INSERT INTO user_wrtc(user_id, room_name, room_url, hash_host, room_id, room_type, access_type, password, created_at, updated_at) " . "VALUES (?,?,?,?,?,?,?,?,?,?)",
        array($authUserID,
            $room->room->name,
            $room->room->room_url,
            $hash->autologin_hash,
            $room->room->id,
            $room->room->room_type,
            $room->room->access_type,
            (isset($room->room->password) ? $room->room->password : NULL),
            $room->room->created_at,
            $room->room->updated_at)
    );

    $result = array(
        'url' => $res,
    );

    echo json_encode($result);

}

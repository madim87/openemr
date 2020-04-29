<?php
/**
 * OpenEMR About Page
 *
 * This Displays an About page for OpenEMR Displaying Version Number, Support Phone Number
 * If it have been entered in Globals along with the Manual and On Line Support Links
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Terry Hill <terry@lilysystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2016 Terry Hill <terry@lillysystems.com>
 * @copyright Copyright (c) 2017 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../globals.php");

use OpenEMR\Core\Header;
use OpenEMR\Services\VersionService;

$authUserID = $_SESSION['authUserID'];
$rooms = sqlGetAssoc("SELECT * FROM user_wrtc WHERE user_id=?", array($authUserID));
$pidsMatchRoom = [];
foreach ($rooms as $room) {
    $attendeesRecords = sqlGetAssoc("SELECT * FROM wrtc_patient_attendees WHERE user_id=? AND room_id=?", array($authUserID, $room['room_id']));
    foreach ($attendeesRecords as $record) {
        $pidsMatchRoom[$room['room_id']][] = $record['pid'];
    }

}

$patientsInRoom = [];
foreach ($pidsMatchRoom as $roomId => $attendees) {
    $patientsInRoom[$roomId] = '';
    foreach ($attendees as $attendee) {
        $attendeeRecord = sqlQuery("SELECT * FROM patient_data WHERE pid=?", array($attendee));
        $patientsInRoom[$roomId] .= $attendeeRecord['fname']." ".$attendeeRecord['lname']."</br>";
    }
}

?>
<html>
<head>

    <?php Header::setupHeader(["jquery-ui","jquery-ui-darkness"]); ?>
    <title><?php echo xlt("ClickMeeting");?> OpenEMR</title>
    <style>
        #create_room_btn {
            margin-top: 15px;;
        }

        td {
            padding: 3px;
        }
    /*    =====================================*/
        .admin-modal {
            width: 80%;
            height: 80%;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            /*border: 1px solid #000;*/

            padding: 10px;
            z-index: 20;
            background-color: #fff;
            display: none;
        }

        .admin-modal.is-show {
            display: inline-block;
        }

        .overlay.is-show {
            display: block;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .4);
            z-index: 10;
            display: none;
        }

        .modal-form {
            width: 100%;
            height: 100%;
        }

        .align-right {
            float: right;
        }

        .card-close {
            margin-left: auto;
            margin-right: 5.8em;
        }

    /*    =====================================*/

    </style>

    <script type="text/javascript">
        var registrationTranslations = <?php echo json_encode(array(
            'title' => xla('OpenEMR Product Registration'),
            'pleaseProvideValidEmail' => xla('Please provide a valid email address'),
            'success' => xla('Success'),
            'registeredSuccess' => xla('Your installation of OpenEMR has been registered'),
            'submit' => xla('Submit'),
            'noThanks' => xla('No Thanks'),
            'registeredEmail' => xla('Registered email'),
            'registeredId' => xla('Registered id'),
            'genericError' => xla('Error. Try again later'),
            'closeTooltip' => ''
        ));
            ?>;

        var registrationConstants = <?php echo json_encode(array(
            'webroot' => $GLOBALS['webroot']
        ))
            ?>;
    </script>

    <script type="text/javascript" src="<?php echo $webroot ?>/interface/product_registration/product_registration_service.js?v=<?php echo $v_js_includes; ?>"></script>
    <script type="text/javascript" src="<?php echo $webroot ?>/interface/product_registration/product_registration_controller.js?v=<?php echo $v_js_includes; ?>"></script>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            var productRegistrationController = new ProductRegistrationController();
            productRegistrationController.getProductRegistrationStatus(function(err, data) {
                if (err) { return; }

                if (data.statusAsString === 'UNREGISTERED') {
                    productRegistrationController.showProductRegistrationModal();
                } else if (data.statusAsString === 'REGISTERED') {
                    productRegistrationController.displayRegistrationInformationIfDivExists(data);
                }
            });

            $("#create_room_btn").click(
                function(){
                    sendCreateRoomForm('create-room-form', '../forms/wrtc/create_room.php');
                    location.reload();
                    return false;
                }
            );

            $("#add_attendees_btn").click(
                function(){
                    addAttendeesToRoom('adding-attendees-form', '../forms/wrtc/adding_attendees.php');
                    // location.reload();
                    console.log("modal work");
                    return false;
                }
            );
        });

        //===========================================
        function openModal(room_id) {
            if (room_id) {
                $('.js-room-id-wrapper').addClass('room-'+room_id);
                $.ajax({
                    url:     '../forms/wrtc/added_attendees.php',
                    type:     "POST",
                    dataType: "json",
                    data: {room_id: room_id},
                    success: function(response) {
                        for (var key in response) {
                            $("#ch-pid-"+key+".room-"+room_id).prop('checked', true)
                        }
                    },
                    error: function(response) {
                        console.log("err ", response);
                    }
                });

                $('#js-room-id').val(room_id);

            } else {
                console.log('error');
            }

            var modal = $('.js-modal');

            modal.addClass('is-show');
            $('.overlay').addClass('is-show');
            $('.js-modal-close').click(function () {
                $('.js-modal').removeClass('is-show');
                $('.js-overlay').removeClass('is-show');
            })
        }

        function closeModal() {
            $('.js-modal').removeClass('is-show');
            $('.js-overlay').removeClass('is-show');
            $(".js-room-id-wrapper").prop('checked', false)
            location.reload();
        }
        //===========================================
        function handleSelectInputChange() {

            console.log(e.target);
        }

        function deleteRoom(room_id) {
            // // e.preventDefault();
            $.ajax({
                url: "../forms/wrtc/delete_room.php",
                type: "POST",
                dataType: "json",
                data: {room_id: room_id},
                success: function(response) {
                    console.log(response);
                },
                error: function(response) {
                    console.log(response);
                }
            });
            location.reload();
            console.log(room_id, 'delete room');
        }

        function sendCreateRoomForm(ajax_form, url) {
            $.ajax({
                url:     url,
                type:     "POST",
                dataType: "json",
                data: $("#"+ajax_form).serialize(),
                success: function(response) {
                    console.log(response);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function addAttendeesToRoom(ajax_form, url) {
            $.ajax({
                url:     url,
                type:     "POST",
                dataType: "json",
                data: $("#"+ajax_form).serialize(),
                success: function(response) {
                    console.log(response);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
</head>
<?php
$versionService = new VersionService();
$version = $versionService->fetch();
?>
<body class="body_top">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-4 col-md-offset-4 text-center">
                <div class="page-header">
                    <h1><?php echo xlt("ClickMeeting");?></h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">

                <h2>Create room</h2>

                <form class="" name="" id="create-room-form" method="POST" action="" >
                    <div class="row">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Room name:</label>
                            <div class="col-lg-9">
                                <input class="form-control" placeholder="Room Name" type="text" name="roomName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Type room:</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="roomType">
                                    <option value="webinar">webinar</option>
                                    <option value="meeting">meeting</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Access type:</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="accessType" id="access_type" onchange="handleSelectInputChange(e)">
                                    <option value="1">free</option>
                                    <option value="2">password</option>
                                    <option value="3">token</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Password:</label>
                            <div class="col-lg-9">
                                <input class="form-control" placeholder="Password" id="password" type="password" name="password">
                            </div>
                        </div>
<!--                        <div class="form-group row">-->
<!--                            <label class="col-lg-3 col-form-label">Qty Tokens:</label>-->
<!--                            <div class="col-lg-9">-->
<!--                                <input class="form-control" placeholder="Qty Tokens" id="qty_tokens" type="text" name="qty_tokens">-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="form-group row">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4">
                                <button class="btn btn-default btn-save" type="submit" id="create_room_btn" value="Create">Create</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-9">
                <h2> All rooms </h2>
                <div>
                    <table width="100%" class="display dataTable no-footer">

                        <tr height="24" style="background:lightgrey" class="head">
                            <td width="10%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">
                                <span style="margin: 3px">Name</span>
                            </td>
                            <td width="5%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Type</td>
                            <td width="5%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">ID</td>
                            <td width="25%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Url</td>
                            <td width="25%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Attendees</td>
                            <td width="25%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Created at</td>
                            <td width="5%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold"></td>
                        </tr>
                        <?php
                            foreach ($rooms as $room) {
                                ?>
                        <tr id="row1" height="24" style="background:lightgrey">
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $room['room_name'] ?></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $room['room_type'] ?></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $room['room_id'] ?></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><a href="<?= $room['room_url']?>" target="_blank"><?= $room['room_url'] ?></a></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $patientsInRoom[$room['room_id']] ?></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $room['created_at'] ?></td>
                                <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid">
                                    <span><a class="btn" onclick="openModal(<?= $room['room_id']?>)"><i class="fa fa-user"></i></a></span>
                                    <span><a class="btn" onclick="deleteRoom(<?= $room['room_id']?>)"><i class="fa fa-trash"></i></a></span>
                                    <span><a class="btn" href="<?= $room['room_url']."?l=".$room['hash_host']?>" target="_blank"><i class="fa fa-play-circle-o"></i></a></span>

                                </td>
                        </tr>
                        <?php
                            }
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
<?php include("./click_meeting_attendees.php") ?>
</html>

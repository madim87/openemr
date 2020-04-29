<?php

require_once("verify_session.php");
require_once('../library/options.inc.php');
require_once("./../library/patient.inc");

$res = getWrtcData($pid);

?>
<table class="table table-striped table-condensed table-bordered">
    <tr class="header">
        <th><?php echo xlt('room name'); ?></th>
        <th><?php echo xlt('room type'); ?></th>
        <th><?php echo xlt('room id'); ?></th>
        <th><?php echo xlt('organiser'); ?></th>
        <th><?php echo xlt('room url'); ?></th>
        <th><?php echo xlt('action'); ?></th>
    </tr>
<?php
foreach ($res as $room) {
    ?>
    <tr class="header">
        <td><?php echo $room['room_name']; ?></td>
        <td><?php echo $room['room_type']; ?></td>
        <td><?php echo $room['room_id']; ?></td>
        <td><?php echo $room['fname']." ".$room['mname']." ".$room['lname']; ?></td>
        <td><?php echo $room['room_url']; ?></td>
        <td><a class="btn btn-success btn-xs" href="<?php echo $room['room_url']; ?>" target="_blank">Join To Meeting</a></td>
    </tr>
<?php
}
?>

</table>

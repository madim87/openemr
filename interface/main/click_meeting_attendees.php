<?php

$patients = sqlGetAssoc("SELECT * FROM patient_data");

?>

<div class="admin-modal js-modal" data-modal="user-form">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-close js-modal-close" style="float: right; margin: 10px; max-width: 20em;" onclick="closeModal()">
                <i class="fa fa-fw fa-times" aria-hidden="true"></i>
            </div>
            <h2>Patints List</h2>
            <form class="" name="" id="adding-attendees-form" method="POST" action="" >
                <input type="text" name="room_id" id="js-room-id" value="" hidden>
                <div class="row">
<!--                    <div class="col-lg-12">-->
<!--                        <div class="form-group row">-->
<!--                            <label class="col-lg-1 col-form-label" for="search-att">search:</label>-->
<!--                            <div class="col-lg-3">-->
<!--                                <input class="form-control" placeholder="Enter value" type="text" name="searchCase" id="search-att">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="col-lg-12">
                        <table width="100%" class="display dataTable no-footer">
                            <thead>
                                <tr height="24" style="background:lightgrey" class="head">
                                    <td width="2%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid; height: 40px" class="bold"></td>
                                    <td width="15%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Full Name</td>
                                    <td width="15%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Home Phone</td>
                                    <td width="15%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">Date of Birth</td>
                                    <td width="15%" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;" class="bold">External ID</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($patients as $patient) {
                                ?>
                                <tr id="row1" height="24" style="background:lightgrey">
                                    <td align="center" style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid">
                                        <input type="checkbox" class="js-room-id-wrapper" name="addedPids[]" id="<?php echo "ch-pid-".$patient['pubpid']?>" value="<?= $patient['pubpid'] ?> <?php echo array_key_exists($allPids, $patient['pubpid']) ? 'checked' : ''?>">
                                    </td>
                                    <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $patient['fname']." ".$patient['lname']  ?></td>
                                    <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $patient['phone_home'] ?></td>
                                    <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $patient['DOB'] ?></td>
                                    <td style="border-bottom: 1px #000000 solid; border-right: 1px #000000 solid"><?= $patient['pubpid'] ?></td>
                                </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div style="float: right; margin-top: 10px; max-width: 20em;">
                                <button class="btn btn-default" type="submit" id="add_attendees_btn" value="Added" >Add Patients</button>
                                <button class="btn btn-default" id="close_attendees_btn" onclick="closeModal()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<div class="overlay js-overlay"></div>

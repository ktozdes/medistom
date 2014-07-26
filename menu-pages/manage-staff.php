<!--validation js lib-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>
<div class="bs-docs-example tooltip-demo">
    <?php global $wpdb;
    $ServiceTable = $wpdb->prefix . "ap_services";
    $cabinetTable = $wpdb->prefix . "ap_cabinets";
    $cabinet_staff_Table = $wpdb->prefix . "ap_cabinets_staff";
    $StaffTable = $wpdb->prefix . "ap_staff";
    $updatestaff = $_GET[staffupdateid];

    // Add new staff
    if(isset($_POST['staffcreate'])) {
        global $wpdb;
        $staff_name = strip_tags($_POST['staff_name']);
        $staff_email = $_POST['staff_email'];
        $staff_phone = $_POST['staff_phone'];
        $staff_experience = $_POST['staff_experience'];
        $staff_group = $_POST['staff_group'];
        $staff_address = strip_tags($_POST['staff_address']);
        $staff_city = strip_tags($_POST['staff_city']);

        $Exitsstaffdetails = $wpdb->get_row("SELECT * FROM `$StaffTable` WHERE `email` = '$staff_email' ");
        if($Exitsstaffdetails) {
            $message = __('<span style="color:orange">Such email is already exists.</span>','appointzilla');
        } else {
            $insert_staff = "INSERT INTO `$StaffTable` (`id` ,`name` , `email`, `phone` ,`experience`, `group_id`, `address`, `city`)	VALUES ('NULL', '$staff_name', '$staff_email', '$staff_phone', '$staff_experience', '$staff_group', '$staff_address', '$staff_city');";
            if($wpdb->query($insert_staff)) {

                $LastInsertedStaffId = mysql_insert_id();
                // now assign this staff id in selected service
                $SeletedSerivcesIds = $_POST['service'];
                foreach($SeletedSerivcesIds as $SingleServiceID) {
                    $ServiceData = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$SingleServiceID' ");
                    $ServiceStaffIds = unserialize($ServiceData->staff_id);
                    if(is_array($ServiceStaffIds)) {
                        array_push($ServiceStaffIds, $LastInsertedStaffId);
                        $ServiceData = serialize($ServiceStaffIds);
                    } else {
                        $ServiceData = serialize($ServiceStaffIds);
                    }
                    $wpdb->query("UPDATE `$ServiceTable` SET `staff_id` = '$ServiceData' WHERE `id` = '$SingleServiceID'");
                }
                foreach($_POST[cabinet] as $cabinetID){
                    $wpdb->insert($cabinet_staff_Table,
                        array(
                            staff_id=>$LastInsertedStaffId,
                            cabinet_id=>$cabinetID)
                    );
                }
                $message = __('<span style="color:green">New staff was added successfully.</span>','appointzilla');
                echo 'aaaaa';
                $updatestaff = $LastInsertedStaffId;

            }
        }
    }

    // Update staff
    if(isset($_POST['staffupdate'])) {
        global $wpdb;
        $staff_name = strip_tags($_POST['staff_name']);
        $staff_email = $_POST['staff_email'];
        $staff_phone = $_POST['staff_phone'];
        $staff_experience = $_POST['staff_experience'];
        $staff_group = $_POST['staff_group'];
        $staff_address = strip_tags($_POST['staff_address']);
        $staff_city = strip_tags($_POST['staff_city']);
        $staffupdateid = $_POST['staffupdate'];
        $updatestaff = $staffupdateid;
        $update_staff = "UPDATE `$StaffTable` SET `name` = '$staff_name',
            `email` = '$staff_email',
            `phone` = '$staff_phone',
            `experience` = '$staff_experience',
            `group_id` = '$staff_group',
            `address` = '$staff_address',
            `city` = '$staff_city' WHERE `id` ='$staffupdateid';";
        if ($queryResult==false){

        }
        if($wpdb->query($update_staff)) {
            $message = __('<span style="color:green">Staff details successfully updated</span>','appointzilla');
        } else {
            $message = __('<span style="color:red">Staff details was not updated</span>','appointzilla');
        }
        //search n delete this staff id from all service
        foreach($_POST[service] as $serviceID){
            $tempService = $wpdb->get_row("SELECT `id`, `name`, `staff_id` FROM `$ServiceTable` WHERE id = $serviceID",ARRAY_A);
            if($tempService) {
                $StaffIDArray = unserialize($tempService[staff_id]);
                if(is_array($StaffIDArray) && array_search($staffupdateid, $StaffIDArray)===false) {
                    $StaffIDArray[] = $staffupdateid;
                    $staffIDstring = serialize($StaffIDArray);
                    $wpdb->query("UPDATE `$ServiceTable` SET `staff_id` = '$staffIDstring' WHERE `id` = ".$tempService[id]);
                }
                else if (!is_array($StaffIDArray)){
                    $staffIDstring = serialize(array($staffupdateid));
                    $wpdb->query("UPDATE `$ServiceTable` SET `staff_id` = '$staffIDstring' WHERE `id` = ".$tempService[id]);
                }
            }
        }
        $wpdb->delete($cabinet_staff_Table,
            array(staff_id=>$staffupdateid)
        );
        foreach($_POST[cabinet] as $cabinetID){
            $wpdb->insert($cabinet_staff_Table,
                array(
                    staff_id=>$staffupdateid,
                    cabinet_id=>$cabinetID)
                );
        }
    }

    // add new staff and update staffs
    $AllServiceIds = array();
    echo $updatestaff;
    if(($updatestaff>0 && is_numeric($updatestaff) && !isset($_GET['viewid'])) || $_GET[staffupdateid]=='new') {
        $updatestaffdetail= $wpdb->get_row("SELECT * FROM `$StaffTable` WHERE `id` = '$updatestaff'");
        $AllServices = $wpdb->get_results("SELECT `id`, `staff_id` FROM `$ServiceTable`");
        if($AllServices) {
            foreach($AllServices as $Service) {
                $staffIds = unserialize($Service->staff_id);
                if(is_array($staffIds)) {
                    if(in_array($updatestaff, $staffIds)) {
                        $AllServiceIds[] = $Service->id;
                    }
                }
            }
        } ?>
        <?php if (strlen($message)>10):?>
            <div id="message" class="updated below-h2"><p>
                    <?php echo $message;?>
                </p></div>
        <?php endif;?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-male"></i> <i class="fa fa-female"></i> <?php _e('Manage Staff','appointzilla'); ?></h3></div>
        <form action="<?php echo "?page=manage-staff&staffupdateid=$updatestaff"?>" method="post" name="Staff-manage">
            <table width="100%" class="detail-view table table-striped table-condensed">
                <tr>
                    <th width="15%"><?php _e('Name','appointzilla'); ?></th>
                    <td width="4%"><strong>:</strong></td>
                    <td width="81%">
                        <input type="text" name="staff_name" id="staff_name" value="<?php if($updatestaffdetail) echo $updatestaffdetail->name; ?>" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff Name.','appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Email','appointzilla'); ?> </th>
                    <td><strong>:</strong></td>
                    <td>
                        <input type="text" name="staff_email" id="staff_email" value="<?php if($updatestaffdetail) echo $updatestaffdetail->email; ?>" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff Email.','appointzilla'); ?>"><i  class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Phone','appointzilla'); ?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <input type="text" name="staff_phone" id="staff_phone" value="<?php if($updatestaffdetail) echo $updatestaffdetail->phone; ?>" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Phone Number.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr><th><?php _e('Experience','appointzilla'); ?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <input name="staff_experience" type="text" id="staff_experience"  value="<?php if($updatestaffdetail) echo $updatestaffdetail->experience; ?>" maxlength="2" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff Experience(In year).','appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Group Name','appointzilla'); ?> </th>
                    <td><strong>:</strong></td>
                    <td>
                      <select id="staff_group" name="staff_group">
                        <option value="0"><?php _e('Select Staff Group','appointzilla'); ?></option>
                        <?php // get  list staff group
                            $table_name = $wpdb->prefix . "ap_staff_groups";
                            $staffgroup= $wpdb->get_results("SELECT * FROM $table_name");
                            foreach($staffgroup as $group) { ?>
                        <option value="<?php if($group->id) echo $group->id; ?>" <?php if($updatestaffdetail){ if($group->id == $updatestaffdetail->group_id ) echo "selected"; } ?> <?php if($group->id == isset($_GET['grouid']) ) echo "selected"; ?> ><?php if($group->name == 'Default') echo _e('Default', 'appointzilla'); else echo ucfirst($group->name); ?></option>
                        <?php }	?>
                      </select>
                      &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff Group Name.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Address','appointzilla'); ?> </th>
                    <td><strong>:</strong></td>
                    <td>
                        <input type="text" name="staff_address" id="staff_address" value="<?php if($updatestaffdetail) echo $updatestaffdetail->address; ?>" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff Address.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('City','appointzilla'); ?> </th>
                    <td><strong>:</strong></td>
                    <td><input type="text" name="staff_city" id="staff_city" value="<?php if($updatestaffdetail) echo $updatestaffdetail->city ; ?>" />
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Staff City.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Assign Cabinet(s)','appointzilla'); ?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <label><?php _e('Use CTRL to Select Multiple Cabinets(s)','appointzilla'); ?></label>
                        <select id="cabinet" name="cabinet[]" multiple="multiple" size="7" style="width:300px;">
                            <!--<option value="0">Use CTRL to Select Multiple Service(s)</option>-->
                            <?php
                            $filterQuery = ($updatestaff=='new')?"GROUP BY $cabinetTable.cabinet_id":"AND staff_id = $updatestaff";
                            $cabinetList = $wpdb->get_results("SELECT $cabinetTable.cabinet_id,$cabinetTable.cabinet_name, $cabinet_staff_Table.staff_id FROM $cabinetTable
                                    LEFT JOIN $cabinet_staff_Table on $cabinet_staff_Table.cabinet_id = $cabinetTable.cabinet_id $filterQuery", ARRAY_A);
                            foreach($cabinetList as $cabinet) {
                                $selected = ( isset($cabinet[staff_id])) ? 'selected="selected"':"";
                                echo "<option value='$cabinet[cabinet_id]' $selected >$cabinet[cabinet_name]</option>";
                            }?>
                        </select>
                        &nbsp;<a href="#" rel="tooltip" title="<?php _e('Assign Staff(s) To This Service.&lt;br&gt;Use CTRL To Select Multiple Staffs','appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>

                <tr>
                  <th scope="row"><?php _e('Assign Service(s)','appointzilla'); ?></th>
                  <td><strong>:</strong></td>
                  <td>
                      <label><?php _e('Use CTRL to Select Multiple Service(s)','appointzilla'); ?></label>
                      <select id="service" name="service[]" multiple="multiple" size="7" style="width:300px;">
                          <!--<option value="0">Use CTRL to Select Multiple Service(s)</option>-->
                          <?php $AllServiceStaff = $wpdb->get_results("SELECT `id`,`code`, `name` FROM `$ServiceTable`", OBJECT);
                            foreach($AllServiceStaff as $Service) {
                                if( in_array($Service->id, $AllServiceIds) ) $selected = "Selected"; else $selected = "";
                                echo "<option value='$Service->id' $selected >".$Service->code ." ".$Service->name.'</option>'; }?>
                      </select>
                    &nbsp;<a href="#" rel="tooltip" title="<?php _e('Assign Staff(s) To This Service.&lt;br&gt;Use CTRL To Select Multiple Staffs','appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                  </td>
                </tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <?php if($updatestaff>0) { ?>
                            <button name="staffupdate" class="btn" type="submit" value="<?php if($updatestaffdetail) echo $updatestaffdetail->id; ?>" id="staffupdate"><i class="icon-pencil"></i> <?php _e('Update','appointzilla'); ?></button>
                        <?php } else { ?>
                            <button name="staffcreate" class="btn" type="submit" id="staffcreate"><i class="icon-ok"></i> <?php _e('Create','appointzilla'); ?></button>
                        <?php } ?>
                        <a href="?page=staff" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla'); ?></a>
                    </td>
                </tr>
          </table>
        </form>
    <?php }
    // view of staff details
    else if(isset($_GET['viewid'])) {
        $clientid=$_GET['viewid'];
        $staffdetails= $wpdb->get_row("SELECT * FROM `$StaffTable` WHERE `id` ='$clientid'"); ?>
         <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-male"></i> <i class="fa fa-female"></i> <?php _e('View Staff','appointzilla'); ?> - <?php echo ucfirst($staffdetails->name); ?></h3> </div>
         <table width="100%" class="detail-view table table-striped table-condensed">
            <tr>
                <th width="15%"><?php _e('Name','appointzilla'); ?></th>
                <td width="4%"><strong>:</strong></td>
                <td width="81%"><?php echo ucfirst($staffdetails->name); ?></td>
            </tr>
            <tr>
                <th><?php _e('Email','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><?php echo $staffdetails->email; ?></td>
            </tr>
            <tr>
                <th><?php _e('Phone','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><?php echo $staffdetails->phone ; ?></td>
            </tr>
            <tr>
                <th><?php _e('Experience','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><?php echo $staffdetails->experience; ?><?php _e('Year(s)','appointzilla'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Group name','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><?php $table_name = $wpdb->prefix . "ap_staff_groups";
                    $staffgroup= $wpdb->get_row("SELECT * FROM $table_name WHERE `id` ='$staffdetails->group_id'");
                    if($staffgroup->name == 'Default') echo _e('Default', 'appointzilla'); else echo ucfirst($staffgroup->name); ?>
                </td>
            </tr>
            <tr>
                <th><?php _e('Address','appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td><?php echo $staffdetails->address; ?></td>
            </tr>
            <tr>
                <th><?php _e('City','appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td><?php echo $staffdetails->city; ?></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td><a class="btn" href="?page=staff"><i class="icon-arrow-left"></i> <?php _e('Back','appointzilla'); ?></a></td>
            </tr>
         </table><?php
    }
?>

    <style type="text/css"> .error{  color:#FF0000; } </style>
    <script type="text/javascript">
    jQuery(document).ready(function () {

        // form submit validation js
        jQuery('form').submit(function() {
            jQuery('.error').hide();
            var staff_name = jQuery("input#staff_name").val();
            if (staff_name== "") {
                jQuery("#staff_name").after('<span class="error">&nbsp;<br><strong><?php _e('Staff name cannot be blank.','appointzilla'); ?></strong></span>');
                return false;
            } else {
                var staff_name = isNaN(staff_name);
                if(staff_name== false) {
                    jQuery("#staff_name").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid value.','appointzilla'); ?></strong></span>');
                    return false;
                }
            }

            var staff_email = jQuery("input#staff_email").val();
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (staff_email== "") {
                jQuery("#staff_email").after('<span class="error">&nbsp;<br><strong><?php _e('Email cannot be blank.','appointzilla'); ?></strong></span>');
                return false;
            } else {
                if(regex.test(staff_email) == false ) {
                    jQuery("#staff_email").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid  value.','appointzilla'); ?></strong></span>');
                    return false;

                }
            }

            var staff_phone = jQuery("input#staff_phone").val();
            if (staff_phone== "") {
                jQuery("#staff_phone").after('<span class="error">&nbsp;<br><strong><?php _e('Phone Number cannot be blank.','appointzilla'); ?></strong></span>');
                return false;
            }

            var staff_experience = jQuery("input#staff_experience").val();
            if (staff_experience) {
                var staff_experience = isNaN(staff_experience);
                if(staff_experience== true)  {
                    jQuery("#staff_experience").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid value.','appointzilla'); ?></strong></span>');
                    return false;
                }
            }

            var staff_group = jQuery("select#staff_group").val();
            if (staff_group == 0) {
                jQuery("#staff_group").after('<span class="error">&nbsp;<br><strong><?php _e('Select any group.','appointzilla'); ?></strong></span>');
                return false;
            }

            var service = jQuery("#service").val();
            if (!service) {
                jQuery("#service").after('<span class="error">&nbsp;<br><strong><?php _e('Assign any service.','appointzilla'); ?></strong></span>');
                return false;
            }
        });
    });
    </script>
</div>
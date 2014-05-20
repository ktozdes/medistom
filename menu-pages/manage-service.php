<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-wrench"></i> <?php _e('Manage Service','appointzilla');?></h3></div>
<!-- manage service form -->	
<div class="bs-docs-example tooltip-demo">
    <?php global $wpdb;
    $AllStaffIds = array();
    if(isset($_GET['sid']) || isset($_GET['gid'])) {
        if(isset($_GET['gid'])) $sid = -1; else $sid = $_GET['sid'];
        $ServiceTableName = $wpdb->prefix . "ap_services";
        $servicedetails = $wpdb->get_row("SELECT * FROM `$ServiceTableName` WHERE `id` ='$sid'" ,OBJECT);
        if($servicedetails) { $AllStaffIds = unserialize($servicedetails->staff_id); } ?>
        <form action="" method="post" name="manageservice">
            <table width="100%" class="table" >
                <tr>
                    <th width="20%" scope="row"><?php _e('Code','appointzilla'); ?></th>
                    <td width="3%"><strong>:</strong></td>
                    <td width="77%"><input name="code" type="text" id="code"  value="<?php if($servicedetails) echo $servicedetails->code; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Code','appointzilla');?>"><i class="icon-question-sign"></i></a></td>
                </tr>
                <tr>
                    <th width="20%" scope="row"><?php _e('Name','appointzilla'); ?></th>
                    <td width="3%"><strong>:</strong></td>
                    <td width="77%"><input name="name" type="text" id="name"  value="<?php if($servicedetails) echo $servicedetails->name; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Name','appointzilla');?>"><i class="icon-question-sign"></i></a></td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Description','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td><textarea name="desc" id="desc"><?php if($servicedetails) echo $servicedetails->desc; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Description','appointzilla');?>"><i class="icon-question-sign"></i></a>				</td>
                </tr>

                <tr>
                    <th scope="row"><strong><?php _e('Cost','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td><input name="cost" type="text" id="cost" value="<?php if($servicedetails) echo $servicedetails->cost; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Cost<br>Enter Numeric Value<br>Eg: 50, 100, 150','appointzilla');?>" ><i class="icon-question-sign"></i></a>				</td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Duration','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td><input name="duration" type="text" id="duration" value="<?php if($servicedetails) echo $servicedetails->duration; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Duration','appointzilla');?>" ><i class="icon-question-sign"></i></a></td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Implant','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td><select name="implant" id="implant">
                            <option value="no" <?php if($servicedetails){ if($servicedetails->implant == 'no') echo "selected"; } ?>><?php _e('No','appointzilla');?></option>
                            <option value="yes" <?php if($servicedetails){ if($servicedetails->implant == 'yes') echo "selected"; } ?>><?php _e('Yes','appointzilla');?></option>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Implantology', 'appointzilla');?>"><i class="icon-question-sign"></i></td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Accept Payment','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td>
                        <select name="accept_payment" id="accept_payment" onchange="enablepaymentfields()">
                            <option value="no" <?php if($servicedetails){ if($servicedetails->accept_payment == 'no') echo "selected"; } ?>><?php _e('No','appointzilla');?></option>
                            <option value="yes" <?php if($servicedetails){ if($servicedetails->accept_payment == 'yes') echo "selected"; } ?>><?php _e('Yes','appointzilla');?></option>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Accept Payment On Booking','appointzilla');?>"><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr id="PaymentTypeTr">
                    <th scope="row"><strong><?php _e('Payment Type','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td>
                        <select name="payment_type" id="payment_type" onchange="enablepercentagefields()">
                            <option value="percentage" <?php if($servicedetails){ if($servicedetails->payment_type == 'percentage') echo "selected"; } ?>><?php _e('In Percentage','appointzilla');?></option>
                            <option value="full" <?php if($servicedetails){ if($servicedetails->payment_type == 'full') echo "selected"; }
                            if (!$servicedetails) echo 'selected';?> ><?php _e('Full Payment','appointzilla');?></option>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Payment Type','appointzilla');?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr id="PercentageAmtTr">
                    <th scope="row"><strong><?php _e('Percentage Amount','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td><input name="percentage_ammount" type="text" id="percentage_ammount" value="<?php if($servicedetails) echo $servicedetails->percentage_ammount; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Percentage Amount<br> Eg: 5%, 10%, 20%, 50%<br>Enter Numeric Value','appointzilla');?>"><i class="icon-question-sign"></i></a>				</td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Availability','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td>
                        <select id="availability" name="availability">
                            <option value="0"><?php _e('Select Service Availability','appointzilla');?></option>
                            <option value="yes" <?php if($servicedetails){ if($servicedetails->availability == 'yes') echo "selected"; } ?> ><?php _e('Yes','appointzilla');?></option>
                            <option value="no" <?php if($servicedetails){ if($servicedetails->availability == 'no') echo "selected"; } ?> ><?php _e('No','appointzilla');?></option>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Service Availability','appointzilla');?>"><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Category','appointzilla');?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <select id="category_id" name="category_id">
                            <option value="0"><?php _e('Select Category','appointzilla');?></option>
                            <?php //get all category list
                            $table_name = $wpdb->prefix . "ap_service_category";
                            $service_category = $wpdb->get_results("select * from $table_name");
                            foreach($service_category as $gruopname) { ?>
                                <option value="<?php echo $gruopname->id; ?>"
                                <?php if($servicedetails){ if($servicedetails->category_id == $gruopname->id) echo "selected"; } ?><?php if( $_GET['gid']>0 && $_GET['gid'] == $gruopname->id) echo "selected='selected'"; ?> > <?php echo $gruopname->name; ?></option>
                            <?php } ?>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Select Category','appointzilla');?>"><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Assign Staff(s)','appointzilla');?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <label><?php _e('Use CTRL to Select Multiple Staff(s)','appointzilla');?></label>
                        <select id="staff" name="staff_id[]" multiple="multiple" size="7" style="width:300px;">
                            <?php
                                $StaffTableName = $wpdb->prefix . "ap_staff";
                                $AllStaff = $wpdb->get_results("SELECT `id`, `name` FROM `$StaffTableName`", OBJECT);
                                foreach($AllStaff as $Staff)
                                {
                                    if( in_array($Staff->id, $AllStaffIds) )  { $selected = "Selected"; } else { $selected = ""; }
                                    echo "<option value='$Staff->id' $selected >".ucwords($Staff->name)."</option>";
                                }
                            ?>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Assign Staff(s) To This Service<br>Use CTRL To Select Multiple Staffs','appointzilla');?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">&nbsp;</th>
                    <td>&nbsp;</td>
                    <td>
                        <?php if(isset($_GET['sid']))	{	?>
                        <button id="saveservice" type="submit" class="btn" name="updateservice"><i class="icon-pencil"></i> <?php _e('Update','appointzilla');?></button>
                        <?php } else {?>
                        <button id="saveservice" type="submit" class="btn" name="saveservice"><i class="icon-ok"></i> <?php _e('Create','appointzilla');?></button>
                        <?php } ?>
                        <a href="?page=service" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
                    </td>
                </tr>
            </table>
        </form><?php
    }
	//cabinet
	if(isset($_GET['cid'])) {
        $sid = $_GET['cid'];
        $cabinetsTable  = $wpdb->prefix . "ap_cabinets";
		$staffTable 	= $wpdb->prefix . "ap_staff";
		$cabinetsStaffTable 	= $wpdb->prefix . "ap_cabinets_staff";
		$cabinets = $wpdb->get_results("select `$cabinetsTable`.`cabinet_id`, cabinet_name, cabinet_note,`$staffTable`.`name` as 'staff_name',`$cabinetsStaffTable`.`staff_id` as 'staff_id'  from `$cabinetsTable`
			LEFT JOIN `$cabinetsStaffTable` on `$cabinetsTable`.`cabinet_id` = `$cabinetsStaffTable`.`cabinet_id`
			LEFT JOIN `$staffTable` on `$cabinetsStaffTable`.`staff_id` = `$staffTable`.`id`
			WHERE `$cabinetsTable`.`cabinet_id` = '$sid'
			ORDER BY `$cabinetsTable`.`cabinet_id`
		", ARRAY_A);
		$selectedStaff = array();
		$cabinet = array();
		foreach($cabinets as $single_cabinet){
			$cabinet = $single_cabinet;
			$selectedStaff[] = $single_cabinet[staff_id];
		}?>
        <form action="" method="post" name="manageservice">
            <table width="100%" class="table" >
                <tr>
                    <th width="20%" scope="row"><?php _e('Name','appointzilla'); ?></th>
                    <td width="3%"><strong>:</strong></td>
                    <td width="77%">
						<input name="cabinet_id" type="hidden" id="cabinet_id"  value="<?php echo $cabinet[cabinet_id]; ?>"/>
						<input name="cabinet_name" type="text" id="cabinet_name"  value="<?php echo $cabinet[cabinet_name]; ?>"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Cabinet Name','appointzilla');?>"><i class="icon-question-sign"></i></a></td>
                </tr>
                <tr>
                    <th scope="row"><strong><?php _e('Note','appointzilla');?></strong></th>
                    <td><strong>:</strong></td>
                    <td width="77%"><input name="cabinet_note" type="text" id="cabinet_note"  value="<?php echo $cabinet[cabinet_note]; ?>"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Cabinet Description','appointzilla');?>"><i class="icon-question-sign"></i></a></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Assign Staff(s)','appointzilla');?></th>
                    <td><strong>:</strong></td>
                    <td>
                        <label><?php _e('Use CTRL to Select Multiple Staff(s)','appointzilla');?></label>
                        <select id="staff" name="staff[]" multiple="multiple" size="7" style="width:300px;">
                            <?php
                                $StaffTableName = $wpdb->prefix . "ap_staff";
                                $AllStaff = $wpdb->get_results("SELECT `id`, `name` FROM `$StaffTableName`", ARRAY_A);
                                foreach($AllStaff as $Staff)
                                {
									if( in_array($Staff[id], $selectedStaff) )  { $selected = "Selected"; } else { $selected = ""; }
                                    echo "<option value='$Staff[id]' $selected >".ucwords($Staff[name])."</option>";
                                }
                            ?>
                        </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Assign Staff(s) To This Cabinet<br>Use CTRL To Select Multiple Staffs','appointzilla');?>" ><i class="icon-question-sign"></i></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">&nbsp;</th>
                    <td>&nbsp;</td>
                    <td>
                        <button id="updatecabinet" type="submit" class="btn" name="updatecabinet"><i class="icon-pencil"></i> <?php _e('Update','appointzilla');?></button>
                        <a href="?page=service" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
                    </td>
                </tr>
            </table>
        </form><?php
    }

    //service views
    if(isset($_GET['viewid'])) {
        $viewid = $_GET['viewid'];
        $ServiceTableName = $wpdb->prefix . "ap_services";
        $servicedetails = $wpdb->get_row("SELECT * FROM `$ServiceTableName` WHERE `id` ='$viewid'" ,OBJECT); ?>
        <table width="100%" class="detail-view table table-striped table-condensed">
            <tr>
                <th width="18%" scope="row"><?php _e('Code','appointzilla');?></th>
                <td width="3%"><strong>:</strong></td>
                <td width="79%"><?php echo $servicedetails->code; ?></td>
            </tr>
            <tr>
                <th width="18%" scope="row"><?php _e('Name','appointzilla');?></th>
                <td width="3%"><strong>:</strong></td>
                <td width="79%"><?php echo ucfirst($servicedetails->name); ?></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Description','appointzilla');?></strong></th>
                <td><strong>:</strong></td>
                <td><?php echo ucwords($servicedetails->desc); ?></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Cost','appointzilla');?> </strong></th>
                <td><strong>:</strong></td>
                <td>
                    <?php $cal_admin_currency_id = get_option('cal_admin_currency');
                        if($cal_admin_currency_id) {
                            $CurrencyTableName = $wpdb->prefix . "ap_currency";
                            $cal_admin_currency = $wpdb->get_row("SELECT `symbol` FROM `$CurrencyTableName` WHERE `id` = '$cal_admin_currency_id'");
                            $cal_admin_currency = $cal_admin_currency->symbol;
                        } else {
                            $cal_admin_currency = "&#36;";
                        }
                        echo $servicedetails->cost.' '.$cal_admin_currency;
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Implant','appointzilla');?></strong></th>
                <td><strong>:</strong></td>
                <td><?php if($servicedetails->implant == 'yes') echo _e('Yes', 'appointzilla'); else echo _e('No', 'appointzilla'); ?></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Availability','appointzilla');?></strong></th>
                <td><strong>:</strong></td>
                <td><?php if($servicedetails->availability == 'yes') echo _e('Yes', 'appointzilla'); else echo _e('No', 'appointzilla'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Category','appointzilla');?></th>
                <td><strong>:</strong></td>
                <td>
                    <?php
                        $ServiceTableName = $wpdb->prefix . "ap_service_category";
                        $CategoryList = $wpdb->get_row("SELECT * FROM `$ServiceTableName` WHERE `id` ='$servicedetails->category_id'" ,OBJECT);
                        if($CategoryList->name == 'Default') { echo _e('Default', 'appointzilla'); } else { echo $CategoryList->name; }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td><a class="btn" href="?page=service"><i class="icon-arrow-left"></i> <?php _e('Back','appointzilla');?></a></td>
            </tr>
        </table><?php
    } // end of service view


    // Creating new service
    if(isset($_POST['saveservice'])) {
        unset($_POST['saveservice']);
        $keysString = '';
        $valuesString = '';
        $_POST['staff_id'] = serialize($_POST['staff_id']);

        $_POST['accept_payment'] = strip_tags($_POST['accept_payment']);
        $_POST['payment_type'] = strip_tags($_POST['payment_type']);

        if($_POST['accept_payment'] == 'yes' && $_POST['payment_type'] == 'full') {
            $_POST['percentage_ammount'] = 0;
        }

        if($_POST['accept_payment'] == 'no') {
            $_POST['payment_type'] = 'none';
            $_POST['percentage_ammount'] = 'none';
        }

        foreach($_POST as $key => $value){
            $keysString .= (strlen($keysString)<3)?"`$key`":", `$key`";
            $valuesString .= (strlen($valuesString)<3)?"'".strip_tags($value)."'":", '".strip_tags($value)."'";
        }

        $ServiceTable = $wpdb->prefix . "ap_services";
        $CreateService = "INSERT INTO `$ServiceTable` ($keysString) VALUES ($valuesString);";
        if($wpdb->query($CreateService)) {
            echo "<script>alert('".__('New service added successfully.','appointzilla')."')</script>";
            echo "<script>location.href='?page=service';</script>";
        }
        else{
            echo "<script>alert('".__('New service was not added.','appointzilla')."')</script>";
            echo "<script>location.href='?page=service';</script>";
        }
    }


    //update a service
    if(isset($_POST['updateservice'])) {
        unset($_POST['updateservice']);
        $sid = $_GET['sid'];
        $keysString = '';
        $valuesString = '';
        $_POST['staff_id'] = serialize($_POST['staff_id']);

        $_POST['accept_payment'] = strip_tags($_POST['accept_payment']);
        $_POST['payment_type'] = strip_tags($_POST['payment_type']);

        if($_POST['accept_payment'] == 'yes' && $_POST['payment_type'] == 'full') {
            $_POST['percentage_ammount'] = 0;
        }

        if($_POST['accept_payment'] == 'no') {
            $_POST['payment_type'] = 'none';
            $_POST['percentage_ammount'] = 'none';
        }

        foreach($_POST as $key => $value){
            $keysString .= (strlen($keysString)<3)?"`$key` = '".strip_tags($value)."'":", `$key` = '".strip_tags($value)."'";
        }

        $ServiceTable = $wpdb->prefix . "ap_services";
        $update_service ="UPDATE `$ServiceTable` SET $keysString WHERE `id` ='$sid';";

        if($wpdb->query($update_service)) {
            //echo $wpdb->last_query;
            echo "<script>alert('".__('Service updated successfully.','appointzilla')."')</script>";
            echo "<script>location.href='?page=manage-service&viewid=$sid';</script>";
        } else {
            //echo $wpdb->last_query;
            echo "<script>alert('".__('Service was not updated.','appointzilla')."')</script>";
            echo "<script>location.href='?page=manage-service&viewid=$sid';</script>";
        }
    }
	//updating cabinet
	if(isset($_POST['updatecabinet'])) {
        $cabinetTable = $wpdb->prefix . "ap_cabinets";
		$result = $wpdb->update( 
			$cabinetTable, 
			array(
				'cabinet_name' => $_POST['cabinet_name'],
				'cabinet_note' => $_POST['cabinet_note']
			),
			array(
				'cabinet_id' => $_POST['cabinet_id']
			)
		);
		echo $wpdb->last_query;
		if($result!==false) {
			$cabinetStaffTable = $wpdb->prefix . "ap_cabinets_staff";
			$wpdb->delete( $cabinetStaffTable, array( 'cabinet_id' => $_POST['cabinet_id'] ) );
			foreach($_POST[staff] as $staff_id){
				$result = $wpdb->insert(
					$cabinetStaffTable, 
					array(
						'cabinet_id' => $_POST['cabinet_id'],
						'staff_id' => $staff_id
					)
				);
			}
			echo "<script>alert('".__('Cabinet updated successfully.','appointzilla')."')</script>";
            echo "<script>location.href='?page=manage-service&cid=$_POST[cabinet_id]';</script>";
		} else {
            echo "<script>alert('".__('Cabinet was not updated.','appointzilla')."')</script>";
            echo "<script>location.href='?page=manage-service&cid=$_POST[cabinet_id]';</script>";
        }
    } ?>
    <style type="text/css">
    .error{  color:#FF0000; }
    input.inputheight {
        height:30px;
    }
    </style>
    <!--validation js lib-->
    <script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script type="text/javascript">
    function checkduration() {
        jQuery('.error').hide();
    }

    //on change payment settings Yes/No
    function enablepaymentfields() {
        var accept_payment = jQuery('#accept_payment').val();
        if(accept_payment == 'yes') {
            jQuery('#PaymentTypeTr').show();
            jQuery('#PercentageAmtTr').show();
        } else {
            jQuery('#PaymentTypeTr').hide();
            jQuery('#PercentageAmtTr').hide();
        }
    }

    //if payment full then hide percentage ammount field
    function enablepercentagefields() {
        var payment_type = jQuery('#payment_type').val();
        if(payment_type == 'full') {
            jQuery('#PercentageAmtTr').hide();
        } else {
            jQuery('#PercentageAmtTr').show();
        }
    }

    jQuery(document).ready(function () {
        var accept_payment = jQuery('#accept_payment').val();
        var payment_type = jQuery('#payment_type').val();

        //onload on creating new service
        if(accept_payment == 'no') {
            jQuery('#PaymentTypeTr').hide();
            jQuery('#PercentageAmtTr').hide();
        }

        //onload service for update
        if(payment_type == 'full' && accept_payment == 'yes') {
            jQuery('#PaymentTypeTr').show();
            jQuery('#PercentageAmtTr').hide();
        }

        if(payment_type == 'percentage' && accept_payment == 'yes')
        {
            jQuery('#PaymentTypeTr').show();
            jQuery('#PercentageAmtTr').show();
        }

        jQuery('#saveservice').click(function() {
            jQuery('.error').hide();
            var name = jQuery("input#name").val();
            if (name == "") {
                jQuery("#name").after('<span class="error">&nbsp;<br><strong><?php _e('Name cannot be blank.','appointzilla');?></strong></span>');
                return false;
            } else {
                var name = isNaN(name);
                if(name == false) {
                    jQuery("#name").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid name.','appointzilla');?></strong></span>');
                    return false;
                }
            }

            var cost = jQuery("input#cost").val();
            if (cost == "") {
                jQuery("#cost").after('<span class="error">&nbsp;<br><strong><?php _e('Cost cannot be blank.','appointzilla');?></strong></span>');
                return false;
            } else {
                var cost = isNaN(cost);
                if(cost == true) {
                    jQuery("#cost").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid cost.','appointzilla');?></strong></span>');
                    return false;
                }
            }

            //check accept payment yes/no
            var accept_payment = jQuery('#accept_payment').val();
            if(accept_payment == 'yes') {
                var payment_type = jQuery('#payment_type').val();
                if(payment_type != 'full') {
                    var percentage_ammount = jQuery('#percentage_ammount').val();
                    if(percentage_ammount == '') {
                        jQuery("#percentage_ammount").after('<span class="error">&nbsp;<br><strong><?php _e('Percentage amount cannot be blank.','appointzilla');?></strong></span>');
                        return false;
                    } else {
                        var NotNumeric = isNaN(percentage_ammount);
                        if(NotNumeric == true) {
                             jQuery("#percentage_ammount").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid percentage amount. Eg.: 5, 10, 20, 25, 50','appointzilla');?></strong></span>');
                            return false;
                        }

                        if(percentage_ammount <= 0 ) {
                            jQuery("#percentage_ammount").after('<span class="error">&nbsp;<br><strong><?php _e('Amount greater than 0. Eg.: 5, 10, 20, 25, 50','appointzilla');?></strong></span>');
                            return false;
                        }

                        if(percentage_ammount >= 100 ) {
                            jQuery("#percentage_ammount").after('<span class="error">&nbsp;<br><strong><?php _e('Amount less then 100. Eg.: 5, 10, 20, 25, 50','appointzilla');?></strong></span>');
                            return false;
                        }
                    }
                }
            }

            var availability = jQuery('#availability').val();
            if(availability == 0) {
                jQuery("#availability").after('<span class="error">&nbsp;<br><strong><?php _e('Select availability.','appointzilla'); ?></strong></span>');
                return false;
            }

            var category = jQuery('#category').val();
            if(category == 0) {
                jQuery("#category").after('<span class="error">&nbsp;<br><strong><?php _e('Select category.','appointzilla');?></strong></span>');
                return false;
            }

            var staff = jQuery('#staff').val();
            if(!staff) {
                jQuery("#staff").after('<span class="error">&nbsp;<br><strong><?php _e('Assign any staff.','appointzilla');?></strong></span>');
                return false;
            }
        });
    });
    </script>
</div>
<?php
global $wpdb;
$TimeFormat = (get_option('apcal_time_format') == '')?"h:i" : get_option('apcal_time_format');
$cal_admin_currency_id = get_option('cal_admin_currency');
$service_table              = $wpdb->prefix . "ap_services";
$ServiceCategoryTable       = $wpdb->prefix . "ap_service_category";
$appointment_table          = $wpdb->prefix . "ap_appointments";
$appointment_service_table  = $wpdb->prefix . "ap_appointment_service";
$payment_table              = $wpdb->prefix . "ap_payment_transaction";


if(isset($_GET['from'])) {
    $FromBack = $_GET['from'];
} else {
    $FromBack = NULL;
}
if ($_GET[action]=='new_payment' || $_GET[action]=='delete_payment' ){?>
    <div class="ajax-payment-container">
    <?php if ($_GET[action]=='delete_payment'){
    $wpdb->delete($payment_table,
    array(
    'id' => $_GET[paymentID]
    ));?>
    <div style="color:#a9302a;background-color:#ee5f5b;padding: 10px;margin: 10px;"><?php _e('Payment Successfully Deleted', 'appointzilla'); ?></div>
<?php }
?>
<?php $appointmentID = $_GET[appointmentID];
$newPayment = isset($_GET[payment])?$_GET[payment]:0;
$ServicesList = $wpdb->get_results("SELECT cost, discount , (SELECT sum( ammount )FROM $payment_table  WHERE app_id =$appointmentID) AS paid FROM $appointment_service_table
    INNER JOIN $service_table on $appointment_service_table.service_id = $service_table.id
    WHERE `appointment_id` = $appointmentID",ARRAY_A);
if (count($ServicesList)>0){
    $total = array();
    foreach($ServicesList as $key=>$singleService){
        $total[paid]            = $singleService[paid];
        $total[full_price]     += $singleService[cost];
        $total[discount_price] += (strpos($singleService[discount],'%')!==false) ? round($singleService[cost]*$singleService[discount]/100,2) :
            round($singleService[cost]-$singleService[discount]);
    }
    $PaymentList = $wpdb->get_row("SELECT SUM(ammount) FROM $payment_table
    WHERE `app_id` = $appointmentID",ARRAY_A);
    if (($total[discount_price]-$total[paid]-$newPayment)>=0){
        $total[paid] = $total[paid]+$newPayment;
        if ($newPayment>0){
            $wpdb->insert($payment_table,
                array(
                    'ammount'=>$newPayment,
                    'app_id'=>$appointmentID,
                    'date'=>date('Y-m-d'),
                    'status'=>'paid',
                    'gateway'=>'cash',
                )
            );
        }
    }
    else{?>
    <div style="color:Red;"><?php _e('Payment Cannot Exceed Price', 'appointzilla'); ?></div>
<?php }

        $PaymentList = $wpdb->get_results("SELECT * FROM $payment_table
        WHERE `app_id` = $appointmentID",ARRAY_A);?>
        <table style="width:100%">
            <tr>
                <td><strong><?php _e('Full Price', 'appointzilla'); ?> : </strong><?php echo $total[full_price]; ?></td>
                <td><strong><?php _e('Discount Price', 'appointzilla'); ?> : </strong><?php echo $total[discount_price]; ?></td>
                <td><strong><?php _e('Paid', 'appointzilla'); ?> : </strong><?php echo $total[paid]; ?></td>
                <td><strong><?php _e('Left', 'appointzilla'); ?> : </strong><?php echo ($total[discount_price] - $total[paid]); ?></td>
            </tr>
        </table>
        <table class="table table-hover" style="width:100%">
        <tr>
            <td><strong><?php _e('Ammount','appointzilla');?></strong></td>
            <td><strong><?php _e('Date','appointzilla');?></strong></td>
            <td><strong><?php _e('Status','appointzilla');?></strong></td>
            <td><strong><?php _e('Action','appointzilla');?></strong></td>
        </tr>
        <?php
        foreach($PaymentList as $key=>$singlePayment){
            $singlePayment[date] = DateTime::createFromFormat('Y-m-d', $singlePayment[date]);
            ?>
            <tr>
                <td><?php echo $singlePayment[ammount];?></td>
                <td><?php echo $singlePayment[date]->format('d-m-Y');?></td>
                <td><?php echo $singlePayment[status];?></td>
                <td><a onclick="deletePayment(<?php echo $singlePayment[id].', '.$appointmentID ;?>)" rel="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="icon-remove"></i></a></td>
            </tr>
        <?php }?>
        </table>
    <?php } else{?>
    <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
<?php }?>
</div>
<?php exit();
} ?>
<div class="bs-docs-example tooltip-demo jquery-tab">
    <?php $DateFormat = (get_option('apcal_date_format') == '') ? "d-m-Y" : get_option('apcal_date_format');
    if(isset($_GET['updateid'])) {
        $AppointmentId = $_GET['updateid'];
        $AppointmentDetail_SQL = "SELECT * FROM `$appointment_table` WHERE `id` ='$AppointmentId'";
        $AppointmentDetails = $wpdb->get_row($AppointmentDetail_SQL); ?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <h3><i class="fa fa-edit"></i> <?php _e('Update Appointment', 'appointzilla'); ?></h3>
        </div>

    <form action="" method="post"><!---update appointment form--->
    <div id="messagebox"></div>
    <ul>
        <li><a href="#tab_cat_id-0"><?php _e('Appointment', 'appointzilla'); ?></a></li>
        <?php
        global $wpdb;
        //get all category list
        $service_category = $wpdb->get_results("select * from `$ServiceCategoryTable` order by name asc");
        foreach($service_category as $gruopname) { ?>
        <li><a href="#tab_cat_id-<?php echo $gruopname->id; ?>"><?php echo $gruopname->name; ?></a></li>
        <?php }?>
        <li><a href="#tab_cat_id-payment"><?php _e('Payment', 'appointzilla'); ?></a></li>
    </ul>
    <div id="tab_cat_id-0">
            <?php //appointment ?>
            <table width="100%" class="table" >
            <input type="hidden" name="fromback" id="fromback" value="<?php if(isset($_GET['from'])) echo $_GET['from']; ?>" />
            <tr>
              <th scope="row"><?php _e('Appointment Creation Date', 'appointzilla'); ?> </th>
              <td><strong>:</strong></td>
              <td><?php echo date($DateFormat." ".$TimeFormat.":s", strtotime("$AppointmentDetails->book_date")); ?></td>
            </tr>
            <tr>
                <th width="16%" scope="row"><?php _e('Name', 'appointzilla', 'appointzilla'); ?></th>
                <td width="5%"><strong>:</strong></td>
                <td width="79%"><input name="appname" type="text" id="appname" value="<?php echo $AppointmentDetails->name; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Name.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Email', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appemail" type="text" id="appemail" value="<?php echo $AppointmentDetails->email; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Email.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Staff', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="staffid" name="staffid">
                        <?php //get all service list
                        global $wpdb;
                        $staff_table_name = $wpdb->prefix . "ap_staff";
                        $staff_list = $wpdb->get_results("select * from $staff_table_name");
                        foreach($staff_list as $staff) { ?>
                            <option value="<?php echo $staff->id; ?>"
                                <?php if($AppointmentDetails->staff_id == $staff->id ) echo "selected";  ?> ><?php echo $staff->name; ?></option>
                        <?php } ?>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('staff Name.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>

            <tr>
                <th scope="row"><strong><?php _e('Phone', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appphone" type="text" id="appphone" value="<?php echo $AppointmentDetails->phone; ?>" class="inputheight" maxlength="12">&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Phone Number.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <?php if($TimeFormat == 'h:i') $ATimeFormat = "h:i A"; else $ATimeFormat = "H:i"; ?>
                <th scope="row"><strong><?php _e('Start Time', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_time" type="text" id="start_time" value="<?php echo date($ATimeFormat, strtotime($AppointmentDetails->start_time)); ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Start Time.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('End Time', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="end_time" type="text" id="end_time" value="<?php echo date($ATimeFormat, strtotime($AppointmentDetails->end_time)); ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment End Time.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Date', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_date" type="text" id="start_date" value="<?php echo $AppointmentDetails->date; ?>" class="inputheight">&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Date.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Description', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><textarea name="app_desc" id="app_desc"><?php echo $AppointmentDetails->note; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Description.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                 <th scope="row"><?php _e('Repeat', 'appointzilla'); ?></th>
                 <td><strong>:</strong></td>
                 <td>
                     <select name="recurring" id="recurring">
                        <option value="yes" <?php if($AppointmentDetails->recurring == 'yes') echo "selected"; ?>><?php _e('Yes', 'appointzilla'); ?></option>
                        <option value="no" <?php if($AppointmentDetails->recurring == 'no') echo "selected"; ?>><?php _e('No', 'appointzilla'); ?></option>
                     </select>&nbsp;<a href="#" rel="tooltip" title=" <?php _e('Appointment Repeat On/OFF.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i>
                 </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat Type', 'appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td>
                    <select id="recurring_type" name="recurring_type">
                        <option value="none" <?php if($AppointmentDetails->recurring_type == 'none') echo "selected"; ?>> <?php _e('None', 'appointzilla'); ?></option>
                        <option value="daily" <?php if($AppointmentDetails->recurring_type == 'daily') echo "selected"; ?>> <?php _e('Daily', 'appointzilla'); ?></option>
                        <option value="weekly" <?php if($AppointmentDetails->recurring_type == 'weekly') echo "selected"; ?>> <?php _e('Weekly', 'appointzilla'); ?></option>
                        <option value="monthly" <?php if($AppointmentDetails->recurring_type == 'monthly') echo "selected"; ?>> <?php _e('Monthly', 'appointzilla'); ?></option>
                        <option value="PD" <?php if($AppointmentDetails->recurring_type == 'PD') echo "selected"; ?>><?php _e('Particular Day', 'appointzilla'); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Repeat Type.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat Start Date', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input name="recurring_st_date" type="text" id="recurring_st_date" value="<?php echo $AppointmentDetails->recurring_st_date; ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Repeat Start Date.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat End Date', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input name="recurring_ed_date" type="text" id="recurring_ed_date" value="<?php echo $AppointmentDetails->recurring_ed_date; ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Repeat End Date.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Status', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="app_status" name="app_status">
                        <option value="pending" <?php if($AppointmentDetails->status == 'pending') echo "selected"; ?> ><?php _e('Pending', 'appointzilla'); ?></option>
                        <option value="approved" <?php if($AppointmentDetails->status == 'approved') echo "selected"; ?> ><?php _e('Approved', 'appointzilla'); ?></option>
                        <option value="cancelled" <?php if($AppointmentDetails->status == 'cancelled') echo "selected"; ?> ><?php _e('Cancelled', 'appointzilla'); ?></option>
                        <option value="done" <?php if($AppointmentDetails->status == 'done') echo "selected"; ?> > <?php _e('Done', 'appointzilla'); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Status.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Payment Status', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="payment_status" name="payment_status">
                        <option value="unpaid" <?php if($AppointmentDetails->payment_status == 'unpaid') echo "selected"; ?>><?php _e('Unpaid', 'appointzilla'); ?></option>
                        <option value="paid" <?php if($AppointmentDetails->payment_status == 'paid') echo "selected"; ?>><?php _e('Paid', 'appointzilla'); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Status.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                </td>
            </tr>
        </table>
        </div>
    <?php foreach($service_category as $gruopname) { ?>
    <div id="tab_cat_id-<?php echo $gruopname->id; ?>">
        <table class="table table-hover">
            <thead>
            <tr class="filter_row row_cat_id-<?php echo $gruopname->id; ?>">
                <td><input type="text" class="filter_code" style="max-width:100px;"/></td>
                <td><input type="text" class="filter_name" style="min-width:150px;"/></td>
                <td><input type="text" class="filter_cost" style="max-width:100px;"/></td>
                <td><input type="text" class="filter_discount" style="max-width:100px;"/></td>
                <td><select class="filter_action" style="max-width:100px;">
                        <option value="any" selected="selected"><?php _e('Any','appointzilla');?></option>
                        <option value="yes"><?php _e('Checked','appointzilla');?></option>
                        <option value="no"><?php _e('Unchecked','appointzilla');?></option>
                    </select></td>
            </tr>
            <tr class="title_row row_cat_id-<?php echo $gruopname->id; ?>">
                <td><strong><?php _e('Code','appointzilla');?></strong></td>
                <td><strong><?php _e('Name','appointzilla');?></strong></td>
                <td><strong><?php _e('Cost','appointzilla');?></strong></td>
                <td><strong><?php _e('Discount','appointzilla');?></strong></td>
                <td><strong> <?php _e('Action','appointzilla');?></strong></td>
            </tr>
            </thead>
            <tbody><?php // get service list group wise
            $ServiceDetails = $wpdb->get_results("SELECT $service_table.id as id, code, name, cost,percentage_ammount,  category_id, $appointment_service_table.id as checkbox_id, discount FROM $service_table LEFT JOIN $appointment_service_table on $appointment_service_table.service_id = $service_table.id WHERE `category_id` ='$gruopname->id' AND availability = 'yes' AND accept_payment='yes' ORDER BY implant");
           foreach($ServiceDetails as $service) {?>
            <tr class="odd value_row row_cat_id-<?php echo $gruopname->id; ?>" style="border-bottom:1px;">
                <td><?php echo ucwords($service->code); ?></td>
                <td><?php echo ucwords($service->name); ?></td>
                <td>
                        <?php
                        if($cal_admin_currency_id) {
                            $CurrencyTableName = $wpdb->prefix . "ap_currency";
                            $cal_admin_currency = $wpdb->get_row("SELECT `symbol` FROM `$CurrencyTableName` WHERE `id` = '$cal_admin_currency_id'");
                            $cal_admin_currency = $cal_admin_currency->symbol;
                        } else {
                            $cal_admin_currency = "&#36;";
                        }
                        echo '<span class="value">'.$service->cost.'</span> '.$cal_admin_currency;
                        ?>
                </td>
                <td>
                    <input type="text" name="discount[<?php echo $service->id; ?>]" value="<?php echo ($service->discount!=null)?$service->discount:$service->percentage_ammount;?>" style="max-width:100px;"/>
                </td>
                <td class="button-column">
                    <input type="checkbox" name="selected[<?php echo $service->id; ?>]" <?php echo ($service->checkbox_id!=null)?'checked="checked"':''?>/>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    } ?>
    <div id="tab_cat_id-payment">

        <?php $ServicesList = $wpdb->get_results("SELECT cost, discount, (SELECT sum( ammount )FROM $payment_table  WHERE app_id =$AppointmentId) AS paid  FROM $appointment_service_table
        INNER JOIN $service_table on $appointment_service_table.service_id = $service_table.id
        WHERE `appointment_id` = $AppointmentId",ARRAY_A);
        if (count($ServicesList)>0){
            $total = array();
            foreach($ServicesList as $key=>$singleService){
                $total[paid]            = $singleService[paid];
                $total[full_price]     += $singleService[cost];
                $total[discount_price] += (strpos($singleService[discount],'%')!==false) ? round($singleService[cost]*$singleService[discount]/100,2) : round($singleService[cost]-$singleService[discount]);
            }
            ?>
            <div class="payment_container">
                <table style="width:100%">
                    <tr>
                        <td><strong><?php _e('Full Price', 'appointzilla'); ?> : </strong><?php echo $total[full_price]; ?></td>
                        <td><strong><?php _e('Discount Price', 'appointzilla'); ?> : </strong><?php echo $total[discount_price]; ?></td>
                        <td><strong><?php _e('Paid', 'appointzilla'); ?> : </strong><?php echo $total[paid]; ?></td>
                        <td><strong><?php _e('Left', 'appointzilla'); ?> : </strong><?php echo ($total[discount_price] - $total[paid]); ?></td>
                    </tr>
                </table>
                <?php $PaymentList = $wpdb->get_results("SELECT * FROM $payment_table
        WHERE `app_id` = $AppointmentId",ARRAY_A);
                if (count($PaymentList)>0):?>
                    <table  class="table table-hover" style="width:100%">
                        <tr>
                            <td><strong><?php _e('Ammount','appointzilla');?></strong></td>
                            <td><strong><?php _e('Date','appointzilla');?></strong></td>
                            <td><strong><?php _e('Status','appointzilla');?></strong></td>
                            <td><strong><?php _e('Action','appointzilla');?></strong></td>
                        </tr>
                        <?php
                        foreach($PaymentList as $key=>$singlePayment){
                            $singlePayment[date] = DateTime::createFromFormat('Y-m-d', $singlePayment[date]);
                            ?>
                            <tr>
                                <td><?php echo $singlePayment[ammount];?></td>
                                <td><?php echo $singlePayment[date]->format('d-m-Y');?></td>
                                <td><?php echo $singlePayment[status];?></td>
                                <td><a onclick="deletePayment(<?php echo $singlePayment[id].', '.$AppointmentId ;?>)" rel="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="icon-remove"></i></a></td>
                            </tr>
                        <?php }?>
                    </table>
                <?php endif;?>
            </div>
            <?php if ($total[discount_price]-$total[paid]>0):?>
            <div id="new_payment">
                <h3><?php _e('New Payment','appointzilla');?></h3>
                <table style="width: 100% ">
                    <tr>
                        <th scope="row"><strong><?php _e('Amount', 'appointzilla'); ?>:</strong></th>
                        <td><input type="text" class="amount" value=""/></td>
                        <td><a href="javascript:void(0)" onclick="newPayment(<?php echo $AppointmentId;?>)" class="btn "><?php _e('Pay', 'appointzilla'); ?></a></td>
                    </tr>
                </table>
            </div>
                <div id="loading" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
            <?php endif;?>
        <?php
        } else {?>
            <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
        <?php }?>
    </div>
    <table style="width: 100% ">
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td> <?php if(isset($_GET['updateid']))	{	?>
                    <button id="updateppointments" type="submit" class="btn" name="updateppointments" value="<?php echo $AppointmentDetails->id; ?>"><i class="icon-pencil"></i> <?php _e('Update', 'appointzilla'); ?></button>
                <?php } else {?>
                    <!--<button id="saveservice" type="submit" class="btn btn-primary" name="saveservice">Create</button>-->
                <?php } ?>
                <?php if(isset($_GET['from'])) { ?>
                    <a href="?page=appointment-calendar" class="btn"><i class="icon-remove"></i> <?php _e('Cancel', 'appointzilla'); ?></a>
                <?php } else {  ?>
                    <a href="?page=manage-appointments" class="btn"><i class="icon-remove"></i> <?php _e('Cancel', 'appointzilla'); ?></a>
                <?php }?>
            </td>
        </tr>
    </table>
    </form>
    <?php } ?>

    <!--validation js lib-->
    <script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

    <script type="text/javascript">
    <?php
        if($TimeFormat == 'h:i') {
            $ATimePickerFormat = "hh:mm TT"; $Tflag = 'true';
        }
        if($TimeFormat == 'H:i') {
            $ATimePickerFormat = "hh:mm"; $Tflag = 'false';
        }
    ?>
    jQuery(document).ready(function () {
        jQuery('.jquery-tab').tabs();
        //filter change
        jQuery('.filter_row select').change(function(){
            jQuery('#messagebox').html(jQuery(this).val());
            filterTable(this);
        });
        jQuery('.filter_row input').keyup(function(){
            filterTable(this);
        });
        jQuery(function(){
            //load date and time picker
            jQuery('#start_time').timepicker({
                ampm: <?php echo $Tflag; ?>,
                timeFormat: '<?php echo $ATimePickerFormat; ?>'
            });

            jQuery('#end_time').timepicker({
                ampm: <?php echo $Tflag; ?>,
                timeFormat: '<?php echo $ATimePickerFormat; ?>'
            });

            jQuery('#start_date').datepicker({
                //minDate: 0,
                dateFormat: 'dd-mm-yy'
            });

            jQuery('#recurring_st_date').datepicker({
                //minDate: 0,
                dateFormat: 'dd-mm-yy'
            });

            jQuery('#recurring_ed_date').datepicker({
                //minDate: 0,
                dateFormat: 'dd-mm-yy'
            });

        });

        // update appointment validation
        jQuery('#updateppointments').click(function() {

            jQuery(".error").hide();
            //start-date appname appemail serviceid appphone start_time end_time start_date
            var appname = jQuery("#appname").val();
            if(appname == '') {
                jQuery("#appname").after('<span class="error"><br><strong><?php _e('Name cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }
            var appemail = jQuery("input#appemail").val();
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (appemail == "") {
                jQuery("#appemail").after('<span class="error"><br><strong><?php _e('Email field cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            } else {
                if(regex.test(appemail) == false ) {
                    jQuery("#appemail").after('<span class="error"><br><strong><?php _e('Invalid email address', 'appointzilla'); ?></strong></span>');
                    return false;
                }
            }
            //start-date
            var appphone = jQuery("#appphone").val();
            if(appphone == '') {
                jQuery("#appphone").after('<span class="error"><br><strong><?php _e('Phone field cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            } else {
                var appphone = isNaN(appphone);
                if(appphone == true) {
                    jQuery("#appphone").after('<span class="error"><br><strong><?php _e('Invalid Phone number.', 'appointzilla'); ?></strong></span>');
                    return false;
                }
            }

            var start_time = jQuery("#start_time").val();
            if(start_time == '') {
                jQuery("#start_time").after('<span class="error"><br><strong><?php _e('Start Time  cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }
            var end_time = jQuery("#end_time").val();
            if(end_time == '') {
                jQuery("#end_time").after('<span class="error"><br><strong><?php _e('End Time cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }

            if(start_time == end_time) {
                jQuery("#start_time").after('<span class="error"><br><strong><?php _e("Start-time and End-time cant be equal.",'appointzilla'); ?></strong></span>');
                jQuery("#end_time").after('<span class="error"><br><strong><?php _e("Start-time and End-time cant be equal.",'appointzilla'); ?></strong></span>');
                return false;
            }

            //Time compression + convert both time into timestamp
            var stt = new Date("October 13, 2013 " + start_time);
            stt = stt.getTime();

            var endt = new Date("October 13, 2013 " + end_time);
            endt = endt.getTime();
            console.log("Time1: "+ stt + " Time2: " + endt);

            if(stt > endt) {
                jQuery("#start_time").after('<span class="error"><br><strong><?php _e("Start-time must be smaller then End-time.",'appointzilla'); ?></strong></span>');
                jQuery("#end_time").after('<span class="error"><br><strong><?php _e("End-time must be bigger then Start-time.",'appointzilla'); ?></strong></span>');
                return false;
            }

            // appointment date
            var start_date = jQuery("#start_date").val();
            if(start_date == '') {
                jQuery("#start_date").after('<span class="error"><br><strong><?php _e('Start Date cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }

            var recurring = jQuery("#recurring").val();
            if(recurring == 'yes') {
                if(jQuery("#recurring_type").val() == 'none') {
                    jQuery("#recurring_type").after('<span class="error"><br><strong><?php _e('Select valid repeat type.', 'appointzilla'); ?></strong></span>');
                    return false;
                }
            }

            var recurring_st_date = jQuery("#recurring_st_date").val();
            if(recurring_st_date == '') {
                jQuery("#recurring_st_date").after('<span class="error"><br><strong><?php _e('Repeat start date cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }

            var recurring_ed_date = jQuery("#recurring_ed_date").val();
            if(recurring_ed_date == '') {
                jQuery("#recurring_ed_date").after('<span class="error"><br><strong><?php _e('Repeat end date cannot be blank.', 'appointzilla'); ?></strong></span>');
                return false;
            }
        });
    });

    function filterTable(thisFilter)
    {
        var thisValue = jQuery(thisFilter).val();
        var parentTable = jQuery(thisFilter).parents('table.table-hover');
        //jQuery('#messagebox').html(jQuery(thisFilter).attr('class')+' value:'+thisValue);
        jQuery(parentTable).find('tr.value_row').each(function(index, currentRow){
            var currentCell;
            var numericFilter = false;
            var selectFilter = false;
            if (jQuery(thisFilter).attr('class')=='filter_code'){
                currentCell = jQuery(currentRow).find('td:first-child');
            }
            else if (jQuery(thisFilter).attr('class')=='filter_name'){
                currentCell = jQuery(currentRow).find('td:nth-child(2)');
            }
            else if (jQuery(thisFilter).attr('class')=='filter_cost'){
                currentCell = jQuery(currentRow).find('td:nth-child(3)');
                if (!isNaN(thisValue)){
                    numericFilter = true;
                }
                //jQuery('#messagebox').append('<br/>span:'+currentCell.find('span').length+' val:'+parseFloat(currentCell.find('span').html()));
            }
            else if (jQuery(thisFilter).attr('class')=='filter_discount'){
                currentCell = jQuery(currentRow).find('td:nth-child(4)');
                if (!isNaN(thisValue)){
                    numericFilter = true;
                }
            }
            else if (jQuery(thisFilter).attr('class')=='filter_action'){
                currentCell = jQuery(currentRow).find('td:nth-child(5)');
                selectFilter = true;
            }
            //jQuery('#messagebox').append('<br/>'+index+':'+currentCell.html().indexOf(thisValue)+':'+currentCell.html());
            if (jQuery(currentRow).hasClass('filtered_'+jQuery(thisFilter).attr('class'))){
                jQuery(currentRow).removeClass('filtered_'+jQuery(thisFilter).attr('class'));
                if (jQuery(currentRow).attr('class').indexOf('filtered')<0){
                    jQuery(currentRow).show();
                }

            }
            if (numericFilter==true){
                if( ( currentCell.find('span').length>0 && parseFloat(currentCell.find('span').html())<thisValue) ||
                    (currentCell.find('input').length>0 && parseFloat(currentCell.find('input').val())<thisValue)){
                    jQuery(currentRow).addClass('filtered_'+jQuery(thisFilter).attr('class'));
                    jQuery(currentRow).hide();
                }
            }
            else if (selectFilter==true && (currentCell.find('input[type=checkbox]').length>0 && currentCell.find('input[type=checkbox]').attr('checked')=='checked' && thisValue=='yes') || (currentCell.find('input[type=checkbox]').length>0 && currentCell.find('input[type=checkbox]').attr('checked')!='checked' && thisValue=='no')){

            }
            else if (currentCell.html().indexOf(thisValue)<0 && thisValue!=''){
                jQuery(currentRow).addClass('filtered_'+jQuery(thisFilter).attr('class'));
                jQuery(currentRow).hide();
            }
        });
    }

    function newPayment(appointmentID)
    {
        var paymentAmount = jQuery('#new_payment input.amount').val();
        jQuery('#loading').show();
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            cache: false,
            data : 'action=new_payment&payment='+paymentAmount+'&appointmentID='+appointmentID,
            complete : function() {  },
            success: function(data) {
                jQuery('#loading').hide();
                data = jQuery(data).find('div.ajax-payment-container');
                jQuery('.payment_container').html(data);
            }
        });
    }
    function deletePayment(paymentID, appointmentID)
    {
        if (!confirm('<?php _e('Do you want to delete this payment?','appointzilla')?>'))
            return false;
        jQuery('#loading').show();
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            cache: false,
            data : 'action=delete_payment&paymentID='+paymentID+'&appointmentID='+appointmentID,
            complete : function() {  },
            success: function(data) {
                jQuery('#loading').hide();
                data = jQuery(data).find('div.ajax-payment-container');
                jQuery('.payment_container').html(data);
            }
        });
    }
    </script>


    <?php //update appointment details
    if(isset($_POST['updateppointments'])) {
        $discountList = array();
        foreach($_POST[selected] as $key=>$singleSelected){
            $discountList[$key] = $_POST[discount][$key];
        }
        $_POST[discount] = $discountList;
        global $wpdb;
        $up_app_id = $_POST['updateppointments'];
        $name = strip_tags($_POST['appname']);
        $email = $_POST['appemail'];
        $serviceid = $_POST['serviceid'];
        $staffid = $_POST['staffid'];
        $phone = $_POST['appphone'];
        $start_time = date("h:i A", strtotime($_POST['start_time']));
        $end_time = date("h:i A", strtotime($_POST['end_time']));
        $appointmentdate = date("Y-m-d", strtotime($_POST['start_date']));
        $note = strip_tags($_POST['app_desc']);
        $payment_status = strip_tags($_POST['payment_status']);
        $status =  $_POST['app_status'];
        $recurring = $_POST['recurring'];
        $recurring_type = $_POST['recurring_type'];
        $recurring_st_date = date("Y-m-d", strtotime($_POST['recurring_st_date'])); //$_POST['recurring_st_date'];
        $recurring_ed_date = date("Y-m-d", strtotime($_POST['recurring_ed_date'])); //$_POST['recurring_ed_date'];
        $appointment_by = $_POST['app_appointment_by'];
        $AppointmentTable = $wpdb->prefix . "ap_appointments";
        $appointment_service_table = $wpdb->prefix . "ap_appointment_service";
        $result = $wpdb->query("UPDATE `$AppointmentTable` SET `name` = '$name', `email` = '$email', `staff_id` = '$staffid', `phone` = '$phone', `start_time` = '$start_time', `end_time` = '$end_time', `date` = '$appointmentdate', `note` = '$note', `status` = '$status', `recurring` = '$recurring', `recurring_type` = '$recurring_type', `recurring_st_date` = '$recurring_st_date', `recurring_ed_date` = '$recurring_ed_date', `payment_status` = '$payment_status' WHERE `id` = '$up_app_id'");
        if($result!==false) {
            //saving services
            $wpdb->delete( $appointment_service_table, array( 'appointment_id' => $up_app_id ) );
            foreach($_POST[selected] as $key=>$value){
                $wpdb->insert( $appointment_service_table,
                    array(
                        'appointment_id' => $up_app_id,
                        'service_id'=>$key,
                        'discount'=>$_POST[discount][$key],
                        'update_date'=>date('Y-m-d')
                ));
            }
            //send notification to client if appointment approved or cancelled
            if($status == 'approved' || $status == 'cancelled' ) {
                $BlogName =  get_bloginfo();
                $ClientTable = $wpdb->prefix . "ap_clients";
                $GetClient = $wpdb->get_row("SELECT * FROM `$ClientTable` WHERE `email` = '$email' ", OBJECT);
                // don't notify no@email.com coz its without attendee
                if($up_app_id && $GetClient->id && $email != "no@email.com") {
                    $AppId = $up_app_id;
                    $ServiceId = $serviceid;
                    $StaffId = $staffid;
                    $ClientId = $GetClient->id;
                    //include notification class
                    require_once('notification-class.php');
                    $Notification = new Notification();
                    if($status == 'approved') $On = "approved";
                    if($status == 'cancelled') $On = "cancelled";
                    $Notification->notifyclient($On, $AppId, $ServiceId, $StaffId, $ClientId, $BlogName, $DateFormat, $TimeFormat);
                    if(get_option('staff_notification_status') == 'on') {
                        $Notification->notifystaff($On, $AppId, $ServiceId, $StaffId, $ClientId, $BlogName, $DateFormat, $TimeFormat);
                    }
                }
            }// end send notification to client if appointment approved or cancelled ckech

            //if status is approved then sync appointment
            if($status == 'approved') {

                //add service name with event title($name)
                //$ServiceTable = $wpdb->prefix . "ap_services";
                //$ServiceData = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$serviceid'");
                //$name = $name."(".$ServiceData->name.")";

                $CalData = get_option('google_caelndar_settings_details');
                if($CalData['google_calendar_client_id'] != '' && $CalData['google_calendar_secret_key']  != '') {
                    $ClientId = $CalData['google_calendar_client_id'];
                    $ClientSecretId = $CalData['google_calendar_secret_key'];
                    $RedirectUri = $CalData['google_calendar_redirect_uri'];
                    require_once('google-appointment-sync-class.php');

                    global $wpdb;
                    $AppointmentSyncTableName = $wpdb->prefix . "ap_appointment_sync";
                    $AnySycnId = $wpdb->get_row("SELECT `id` FROM `$AppointmentSyncTableName` WHERE `app_id` = '$up_app_id'");
                    //check appointment already synced or first time approved
                    if(count($AnySycnId)) {
                        // update this appointment event on calendar
                        global $wpdb;
                        $AppointmentSyncTableName = $wpdb->prefix . "ap_appointment_sync";
                        $SyncDetails = $wpdb->get_row("SELECT * FROM `$AppointmentSyncTableName` WHERE `app_id` = '$up_app_id'");
                        $SyncTableRowId = $SyncDetails->id;
                        $SyncDetailsData = unserialize($SyncDetails->app_sync_details);
                        $json  = json_encode($SyncDetailsData);
                        $SyncDetailsData = json_decode($json, true);
                        $sync_id = $SyncDetailsData['id'];
                        $sync_email = $SyncDetailsData['creator']['email'];

                        $GoogleAppointmentSync = new GoogleAppointmentSync($ClientId, $ClientSecretId, $RedirectUri);
                        $tag = "Appointment with: ";
                        if($repeat == 'none') {
                            $OAuth = $GoogleAppointmentSync->UpdateNormalSync($sync_id, $sync_email, $name, $appointmentdate, $start_time, $end_time, $note, $tag);
                        }
                        if($repeat != 'none') {
                            $OAuth = $GoogleAppointmentSync->UpdateRecurringSync($sync_id, $sync_email, $name, $recurring_st_date, $recurring_ed_date, $start_time, $end_time, $recurring_type, $note, $tag);
                        }

                        //update appointment sync details
                        $OAuth = serialize($OAuth);
                        $wpdb->query("UPDATE `$AppointmentSyncTableName` SET `app_sync_details` = '$OAuth' WHERE `id` = '$SyncTableRowId'");
                    } else {
                        // insert this appointment event on calendar
                        $GoogleAppointmentSync = new GoogleAppointmentSync($ClientId, $ClientSecretId, $RedirectUri);
                        $tag = "Appointment with: ";
                        if($recurring_type == 'none') {
                            $OAuth = $GoogleAppointmentSync->NormalSync($name, $appointmentdate, $start_time, $end_time, $note, $tag);
                        }
                        if($recurring_type != 'none') {
                            $OAuth = $GoogleAppointmentSync->RecurringSync($name, $recurring_st_date, $recurring_ed_date, $start_time, $end_time, $recurring_type, $note, $tag);
                        }

                        //insert appointment sync details
                        $OAuth = serialize($OAuth);
                        $wpdb->query("INSERT INTO `$AppointmentSyncTableName` ( `id` , `app_id` , `app_sync_details` ) VALUES ( NULL , '$up_app_id', '$OAuth' );");
                    }
                }//end of if cal settings check
            }//end of if approved


            //if status is cancelled then delete sync appointment
            if($status == 'cancelled' || $status == 'pending') {
                global $wpdb;
                $AppointmentSyncTableName = $wpdb->prefix . "ap_appointment_sync";
                $Row = $wpdb->get_row("SELECT * FROM `$AppointmentSyncTableName` WHERE `app_id` = '$up_app_id'");
                $Row = unserialize($Row->app_sync_details);
                $json  = json_encode($Row);
                $Row = json_decode($json, true);
                $SyncId = $Row['id'];
                if($SyncId) {
                    $CalData = get_option('google_caelndar_settings_details');
                    $ClientId = $CalData['google_calendar_client_id'];
                    $ClientSecretId = $CalData['google_calendar_secret_key'];
                    $RedirectUri = $CalData['google_calendar_redirect_uri'];

                    require_once('google-appointment-sync-class.php');
                    $GoogleAppointmentSync = new GoogleAppointmentSync($ClientId, $ClientSecretId, $RedirectUri);
                    $OAuth = $GoogleAppointmentSync->DeleteSync($SyncId);

                    // delete sync details
                    $wpdb->query("DELETE FROM `$AppointmentSyncTableName` WHERE `app_id` = '$up_app_id'");
                }
            }//end of cancel status check


            //redirect to updated appointment details page
            if($_POST['fromback']) {
                echo "<script>alert('".__('Appointment successfully updated', 'appointzilla')."');</script>";
                echo "<script>location.href='?page=update-appointment&viewid=$up_app_id&from=calendar';</script>";
            } else {
                echo "<script>alert('".__('Appointment successfully updated', 'appointzilla')."');</script>";
                echo "<script>location.href='?page=update-appointment&viewid=$up_app_id';</script>";
            }
        }
        else {
            if($_POST['fromback']) {
                echo "<script>alert('".__('Appointment was not updated', 'appointzilla')."');</script>";
                echo "<script>location.href='?page=update-appointment&viewid=$up_app_id&from=calendar';</script>";
            } else {
                echo "<script>alert('".__('Appointment was not updated', 'appointzilla')."');</script>";
                echo "<script>location.href='?page=update-appointment&viewid=$up_app_id';</script>";
            }
        }
    }// end of isset



    //appointment view page
    if(isset($_GET['viewid'])) {
        $AppId = $_GET['viewid'];
        $appointment_service_table = $wpdb->prefix . "ap_appointment_service";
        $service_table = $wpdb->prefix . "ap_services";
        $service_category_table = $wpdb->prefix . "ap_service_category";
        $AppDetail_SQL ="SELECT * FROM `$appointment_table` WHERE `id` ='$AppId'";
        $AppDetails = $wpdb->get_row($AppDetail_SQL); ?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <h3><i class="fa fa-eye"></i> <?php _e('View Appointment', 'appointzilla'); ?> - <?php echo ucwords($AppDetails->name); ?></h3>
        </div>
        <ul>
            <li><a href="#tab_cat_id-0"><?php _e('Appointment', 'appointzilla'); ?></a></li>
            <li><a href="#tab_cat_id-1"><?php _e('Services', 'appointzilla'); ?></a></li>
        </ul>
        <div id="tab_cat_id-0">
        <table width="100%" class="table" ><!---update appointment form--->
            <tr>
                <th scope="row"><?php _e('Appointment Creation Date', 'appointzilla'); ?>
                </th>
                <td><strong>:</strong></td>
                <td><?php echo date($DateFormat." ".$TimeFormat.":s", strtotime("$AppDetails->book_date")); ?></td>
            </tr>
            <tr>
                <th width="16%" scope="row"><?php _e('Name', 'appointzilla'); ?></th>
                <td width="5%"><strong>:</strong></td>
                <td width="79%"><em><?php echo ucwords($AppDetails->name); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo $AppDetails->email; ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Staff', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td>
                    <em>
                    <?php
                        $staff_table_name = $wpdb->prefix . "ap_staff";
                        $staffdetails= $wpdb->get_row("SELECT * FROM $staff_table_name WHERE `id` ='$AppDetails->staff_id'");
                        echo ucwords($staffdetails->name);
                    ?>
                    </em>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo $AppDetails->phone; ?></em></td>
            </tr>
            <tr>
                <?php if($TimeFormat == 'h:i') $ATimeFormat = "h:i A"; else $ATimeFormat = "H:i"; ?>
                <th scope="row"><?php _e('Start Time', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo date($ATimeFormat, strtotime($AppDetails->start_time)); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('End Time', 'appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td><em><?php echo date($ATimeFormat, strtotime($AppDetails->end_time)); ?></em></td>
            </tr>
            <tr>
                <?php
                if($DateFormat == 'd-m-Y') $VDateFormat = "jS F Y";
                if($DateFormat == 'm-d-Y') $VDateFormat = "F jS Y";
                if($DateFormat == 'Y-m-d') $VDateFormat = "Y F jS";
                ?>
                <th scope="row"><?php _e('Date', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo date($DateFormat, strtotime($AppDetails->date)); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Description', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo ucfirst($AppDetails->note); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Appointment Key', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo $AppDetails->appointment_key; ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Status', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo _e(ucfirst($AppDetails->status), 'appointzilla'); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo _e(ucfirst($AppDetails->recurring), 'appointzilla'); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat Type', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo _e(ucfirst($AppDetails->recurring_type), 'appointzilla'); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat Start Date', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo date($DateFormat, strtotime($AppDetails->recurring_st_date)); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Repeat End Date', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo date($DateFormat, strtotime($AppDetails->recurring_ed_date)); ?></em></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Payment Status', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><em><?php echo _e(ucfirst($AppDetails->payment_status), 'appointzilla');?></em></td>
            </tr>
            <tr>
                <th scope="row">&nbsp;</th>
                <td>&nbsp;</td>
                <td>
                    <?php if($FromBack) { ?>
                        <a href="?page=appointment-calendar" class="btn"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></a>
                        <a href="?page=update-appointment&updateid=<?php echo $AppId; ?>&from=calendar" class="btn btn-primary"><?php _e('Edit', 'appointzilla'); ?></a>
                    <?php } else { ?>
                        <a href="?page=manage-appointments" class="btn"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></a>
                    <?php } ?>
                </td>
            </tr>
        </table>
        </div>

        <div id="tab_cat_id-1">
        <?php
        $total =array();
        $total[paid] =0;
        $ServiceDetails = $wpdb->get_results("SELECT $service_category_table.name as category_name, code, $service_table.name as service_name, cost, percentage_ammount, discount , (SELECT sum( ammount ) FROM $payment_table WHERE app_id =$AppId) AS paid
        FROM $appointment_service_table
        INNER JOIN $service_table  on $appointment_service_table.service_id = $service_table.id
        INNER JOIN $service_category_table on $service_table.category_id = $service_category_table.id
        WHERE $appointment_service_table.appointment_id = $AppId ORDER BY $service_category_table.name, $service_table.name ", ARRAY_A);
        if (count($ServiceDetails)>0){?>
            <table width="100%" class="table" ><!---update appointment form--->
                <tr>
                    <th scope="row"><?php _e('Category', 'appointzilla'); ?></th>
                    <th scope="row"><?php _e('Name', 'appointzilla'); ?></th>
                    <th scope="row"><?php _e('Code', 'appointzilla'); ?></th>
                    <th scope="row"><?php _e('Cost', 'appointzilla'); ?></th>
                    <th scope="row"><?php _e('Discount', 'appointzilla'); ?></th>
                    <th scope="row"><?php _e('Price', 'appointzilla'); ?></th>
                </tr>
                <?php foreach($ServiceDetails as $service) :
                    $service[price] = (strpos($service[discount],'%')!==false) ? round($service[cost]*$service[discount]/100,2) :
                        round($service[cost]-$service[discount]);
                    $total[full_price] += $service[cost];
                    $total[discount_price] += $service[price];
                    $total[paid] = $service[paid];


                ?>
                <tr>
                    <td><?php echo $service[category_name]; ?></td>
                    <td><?php echo $service[service_name]; ?></td>
                    <td><?php echo $service[code]; ?></td>
                    <td><?php echo $service[cost]; ?></td>
                    <td><?php echo $service[discount]; ?></td>
                    <td><?php echo $service[price]; ?></td>
                </tr>
                <?php endforeach;?>
            </table>
            <table style="width:100%">
                <tr>
                    <td colspan="3"><strong></strong><?php _e('Full Price', 'appointzilla'); ?>:</strong></td>
                    <td><?php echo $total[full_price]; ?></td>
                    <td colspan="3"><strong><?php _e('Discount Price', 'appointzilla'); ?>:</strong></td>
                    <td><?php echo $total[discount_price]; ?></td>
                    <td colspan="3"><strong><?php _e('Paid', 'appointzilla'); ?>:</strong></td>
                    <td><?php echo $total[paid]; ?></td>
                    <td colspan="3"><strong><?php _e('Left', 'appointzilla'); ?>:</strong></td>
                    <td><?php echo ($total[discount_price] - $total[paid]); ?></td>
                </tr>
            </table>
    <?php }
    else{?>
        <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
    <?php } ?>
    </div>
    <?php } ?>
    <!--time-picker js -->
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
</div>
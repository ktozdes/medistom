<?php
include_once('includes/AppointmentController.php');
global $wpdb;
$TimeFormat = (get_option('apcal_time_format') == '')?"h:i" : get_option('apcal_time_format');
$cal_admin_currency_id = get_option('cal_admin_currency');
$service_table              = $wpdb->prefix . "ap_services";
$ServiceCategoryTable       = $wpdb->prefix . "ap_service_category";
$appointment_table          = $wpdb->prefix . "ap_appointments";
$appointment_service_table  = $wpdb->prefix . "ap_appointment_service";
$payment_table              = $wpdb->prefix . "ap_payment_transaction";
$appointmentID = isset($_GET[appointmentID])?$_GET[appointmentID]:'';
$appointmentID = isset($_GET[updateid])?$_GET[updateid]:$appointmentID;
$appointmentID = isset($_POST['updateppointments'])?$_POST['updateppointments']:$appointmentID;
$appointmentID = isset($_GET['viewid'])?$_GET['viewid']:$appointmentID;
$AppointmentController = new AppointmentController();

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
    $appointmentID = $_GET[appointmentID];
    $newPayment = isset($_GET[payment])?$_GET[payment]:0;
    $total = $AppointmentController->getTotalPrice($appointmentID);
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
        if (($total[discount_price]-$total[paid])<0.01){
            $wpdb->update($appointment_table,
                array(
                    'payment_status'=>'paid'),
                array(
                    'id'=>$appointmentID,
                )
            );
        }
    }
    else{?>
    <div style="color:Red;"><?php _e('Payment Cannot Exceed Price', 'appointzilla'); ?></div>
<?php }
    $AppointmentController->getPaymentTab(array(appointmentID=>$appointmentID));
    exit();
}?>
<div id="ajax-loading-container">
    <img src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/big-loading.gif');?>"/>
</div>
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
            <?php $AppointmentController->getEditableMainAppointmentTab(array(appointmentID=>$appointmentID)) ?>

    </div>
    <?php foreach($service_category as $gruopname) { ?>
    <div id="tab_cat_id-<?php echo $gruopname->id; ?>">
        <?php $AppointmentController->getSelectableServiceTab(array('appointmentID'=>$appointmentID,'serviceCategoryID'=>$gruopname->id));?>
    </div>
    <?php } ?>
    <div id="tab_cat_id-payment">
        <?php $AppointmentController->getPaymentTab(array(appointmentID=>$appointmentID));?>
    </div>
    <table class="table" style="width: 100% ">
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td>
                <a href="?page=update-appointment&viewid=<?php echo $AppointmentDetails->id; ?>" class="btn"><i class="icon-ok"></i> <?php _e('Done', 'appointzilla'); ?></a>
                <button id="updateppointments" type="submit" class="btn" name="updateppointments" value="<?php echo $AppointmentDetails->id; ?>"><i class="icon-pencil"></i> <?php _e('Update', 'appointzilla'); ?></button>
                <a href="?page=manage-appointments" class="btn"><i class="icon-remove"></i> <?php _e('Cancel', 'appointzilla'); ?></a>
            </td>
        </tr>
    </table>
    </form>
    <?php } ?>

    <?php
    if($TimeFormat == 'h:i') {
        $ATimePickerFormat = "hh:mm TT"; $Tflag = 'true';
    }
    if($TimeFormat == 'H:i') {
        $ATimePickerFormat = "hh:mm"; $Tflag = 'false';
    }?>
    <!--validation js lib-->
    <script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

    <script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.jquery-tab').tabs();
        //filter change
        jQuery('.filter_row select').change(function(){
            //jQuery('#messagebox').html(jQuery(this).val());
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
    });
    jQuery('#diagnosisID').change(function() {
        if (jQuery(this).val()=='')
            return false;
        jQuery('#ajax-loading-container').show();
        var data = {
            'action': 'get_diagnosis_service_ids',
            'diagnosis_id': jQuery(this).val()
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.get(ajaxurl, data, function(response) {
            jQuery('#ajax-loading-container').hide();
            var tempArray = jQuery.parseJSON(response);
            jQuery('input[type=checkbox]').prop('checked', false);
            jQuery.each(tempArray, function(key,val){
                jQuery('input.checkbox_'+val).prop('checked', true);
            });
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
                echo "<script>location.href='?page=update-appointment&viewid=$up_app_id';</script>";
            } else {
                echo "<script>alert('".__('Appointment successfully updated', 'appointzilla')."');</script>";
                echo "<script>location.href='?page=update-appointment&updateid=$up_app_id';</script>";
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
            <li><a href="#tab_cat_id-2"><?php _e('Payment', 'appointzilla'); ?></a></li>
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
        </table>
        </div>

        <div id="tab_cat_id-1">
        <?php
        $total = $AppointmentController->getTotalPrice($AppId);
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
                    $service[price] = (strpos($service[discount],'%')!==false) ? round($service[cost]*(100-$service[discount])/100,2) :
                        round($service[cost]-$service[discount]);
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
            <table class="table">
                <tr>
                    <td><strong><?php _e('Full Price', 'appointzilla'); ?>:</strong><?php echo $total[full_price]; ?></td>
                    <td><strong><?php _e('Discount Price', 'appointzilla'); ?>:</strong><?php echo $total[discount_price];?></td>
                    <td><strong><?php _e('Paid', 'appointzilla'); ?>:</strong><?php echo $total[paid]; ?></td>
                    <td><strong><?php _e('Left', 'appointzilla'); ?>:</strong><?php echo ($total[discount_price] - $total[paid]); ?></td>
                </tr>
            </table>
    <?php }
    else{?>
        <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
    <?php } ?>
    </div>
        <div id="tab_cat_id-2">
            <?php
            $total = $AppointmentController->getTotalPrice($AppId);
            $PaymentList = $wpdb->get_results("SELECT * FROM $payment_table
        WHERE `app_id` = $AppId",ARRAY_A);
            if (count($ServiceDetails)>0){?>
                <table width="100%" class="table" ><!---update appointment form--->
                    <tr>
                        <th scope="row"><?php _e('Date', 'appointzilla'); ?></th>
                        <th scope="row"><?php _e('Amount', 'appointzilla'); ?></th>
                        <th scope="row"><?php _e('Status', 'appointzilla'); ?></th>
                        <th scope="row"><?php _e('Payment Method', 'appointzilla'); ?></th>
                    </tr>
                    <?php foreach($PaymentList as $singlePayment) :
                        $singlePayment[date] = DateTime::createFromFormat('Y-m-d', $singlePayment[date]);
                        $singlePayment[date] = $singlePayment[date]->format('d-m-Y');
                        if      ($singlePayment[gateway]=='cash')
                            $singlePayment[gateway] = __('Cash','appointzilla');
                        else if ($singlePayment[gateway]=='card')
                            $singlePayment[gateway] = __('Card','appointzilla');
                        ?>
                        <tr>
                            <td><?php echo $singlePayment[date]; ?></td>
                            <td><?php echo $singlePayment[ammount]; ?></td>
                            <td><?php echo $singlePayment[status]; ?></td>
                            <td><?php echo $singlePayment[gateway]; ?></td>
                        </tr>
                    <?php endforeach;?>
                </table>
                <table class="table">
                    <tr>
                        <td><strong><?php _e('Full Price', 'appointzilla'); ?>:</strong><?php echo $total[full_price]; ?></td>
                        <td><strong><?php _e('Discount Price', 'appointzilla'); ?>:</strong><?php echo $total[discount_price]; ?></td>
                        <td><strong><?php _e('Paid', 'appointzilla'); ?>:</strong><?php echo $total[paid]; ?></td>
                        <td><strong><?php _e('Left', 'appointzilla'); ?>:</strong><?php echo ($total[discount_price] - $total[paid]); ?></td>
                    </tr>
                </table>
            <?php }
            else{?>
                <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
            <?php } ?>
        </div>
        <div>
            <table clas="table">
                <tr>
                    <th scope="row">&nbsp;</th>
                    <td>&nbsp;</td>
                    <td><a href="?page=appointment-calendar" class="btn"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></a>
                        <a href="?page=update-appointment&updateid=<?php echo $AppId; ?>&from=calendar" class="btn btn-primary"><?php _e('Edit', 'appointzilla'); ?></a></td>
                </tr>
            </table>
        </div>
    <?php } ?>
    <!--time-picker js -->
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
</div>
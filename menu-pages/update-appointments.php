<?php
global $wpdb;
$TimeFormat = (get_option('apcal_time_format') == '')?"h:i" : get_option('apcal_time_format');
$cal_admin_currency_id = get_option('cal_admin_currency');
$service_table              = $wpdb->prefix . "ap_services";
$ServiceCategoryTable       = $wpdb->prefix . "ap_service_category";
$appointment_table          = $wpdb->prefix . "ap_appointments";
$appointment_service_table  = $wpdb->prefix . "ap_appointment_service";
$medical_cart_table         = $wpdb->prefix . "ap_medical_cart";
$payment_table              = $wpdb->prefix . "ap_payment_transaction";
$appointmentID = isset($_GET[appointmentID])?$_GET[appointmentID]:'';
$appointmentID = isset($_GET[updateid])?$_GET[updateid]:$appointmentID;
$appointmentID = isset($_POST['updateppointments'])?$_POST['updateppointments']:$appointmentID;
$appointmentID = isset($_GET['viewid'])?$_GET['viewid']:$appointmentID;
$AppointmentController = new AppointmentController();
if (isset($_POST[updateppointments])){
    $message = $AppointmentController->updateAppointment(array(appointmentID=>$appointmentID));
}
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
<?php if (strlen($message)>10):?>
    <div id="message" class="updated below-h2"><p>
            <?php echo $message;?>
        </p></div>
<?php endif;?>
    <?php $DateFormat = (get_option('apcal_date_format') == '') ? "d-m-Y" : get_option('apcal_date_format');
    if(isset($_GET['updateid'])) {
        $AppointmentId = $_GET['updateid'];
        $AppointmentDetail_SQL = "SELECT * FROM `$appointment_table` WHERE `id` ='$AppointmentId'";
        $AppointmentDetails = $wpdb->get_row($AppointmentDetail_SQL); ?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-edit"></i> <?php _e('Update Appointment', 'appointzilla'); ?></h3>
    </div>

    <form action="" method="post"><!---update appointment form--->
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
        <li><a href="#tab_cat_id-medical-cart"><?php _e('Medical Cart', 'appointzilla'); ?></a></li>
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
    <div id="tab_cat_id-medical-cart">
        <?php $AppointmentController->getMedicalCartTab(array(appointmentID=>$appointmentID,client_id=>$AppointmentDetails->client_id));?>
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
    <script type="text/javascript">
    jQuery(document).ready(function ($) {

        jQuery('.jquery-tab').tabs();
        //filter change
        jQuery('.filter_row select').change(function(){
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

        jQuery("a.fancybox-thumbs").fancybox({
            helpers : {
                thumbs : true,
                theme : 'dark'
            }
        });

    });

    jQuery('#treatmentID').change(function() {
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
        jQuery('#ajax-loading-container').show();
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            cache: false,
            data : 'action=new_payment&payment='+paymentAmount+'&appointmentID='+appointmentID,
            complete : function() {  },
            success: function(data) {
                jQuery('#ajax-loading-container').hide();
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

    function newMedicalCartSave(appID)
    {
        if (jQuery('#medical_cart_date').val()==''){
            jQuery("#medical_cart_date").after('<span class="error">&nbsp;<br><strong><?php _e('Cannot be blank.','appointzilla'); ?></strong></span>');
            return false;
        }
        if (jQuery('#medical_cart_code').val()==''){
            jQuery("#medical_cart_code").after('<span class="error">&nbsp;<br><strong><?php _e('Cannot be blank.','appointzilla'); ?></strong></span>');
            return false;
        }
        if (jQuery('#medical_cart_tooth').val()==''){
            jQuery("#medical_cart_tooth").after('<span class="error">&nbsp;<br><strong><?php _e('Cannot be blank.','appointzilla'); ?></strong></span>');
            return false;
        }

        jQuery('#ajax-loading-container').show();

        var data  = {
            'action':'new_medical_cart_row',
            'medical_cart_date':jQuery('#medical_cart_date').val(),
            'medical_cart_code':jQuery('#medical_cart_code').val(),
            'medical_cart_tooth':jQuery('#medical_cart_tooth').val(),
            'medical_cart_image_ids':jQuery('#medical_cart_image_ids').val(),
            'medical_cart_appointment_id':appID
        };


        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#ajax-loading-container').hide();
            if (response>0){
				jQuery('#messagebox').html('<?php _e('New Item Successfully Added.','appointzilla'); ?>');
				jQuery('#messagebox').addClass('alert-success');
				jQuery('#medical_cart_date').val('');
				jQuery('#medical_cart_code').val('');
				jQuery('#medical_cart_tooth').val('');
				jQuery('#medical_cart_image_ids').val('');
			}
			else{
				jQuery('#messagebox').html('<?php _e('New Item Was Not Added.','appointzilla'); ?>');
				jQuery('#messagebox').removeClass('alert-success').addClass('alert-error');
			}
			jQuery('#messagebox').stop().fadeIn().delay(5000).fadeOut();
		});
    };

    function cancelnewMedicalRow()
    {
        jQuery('.new_medical_row').hide();
    }

    function NewCartRow()
    {
        jQuery('.new_medical_row').show();
    }
    //image uploader
    </script>
    <script>
    var custom_uploader;

    jQuery('#upload_image_button, #upload_image').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').toJSON();
            var imageIDList = '';
            jQuery.each(attachment, function(key,singleImage){
                imageIDList += (imageIDList.length==0) ? singleImage.id:','+singleImage.id;
            });
            jQuery('#medical_cart_image_ids').val(imageIDList);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });

    </script>


    <?php
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
            <li><a href="#tab_cat_id-3"><?php _e('Medical Cart', 'appointzilla'); ?></a></li>
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
        <div  class="alert alert-warning"><?php _e('No Service Selected', 'appointzilla'); ?></div>
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
                <div  class="alert alert-warning"><?php _e('No Payment', 'appointzilla'); ?></div>
            <?php } ?>
        </div>
        <div id="tab_cat_id-3">
            <?php
            $medicalCartRows = $wpdb->get_results("SELECT * FROM $medical_cart_table
        WHERE `medical_cart_appointment_id` = $AppId",ARRAY_A);
            if (count($medicalCartRows)>0){?>
            <table width="100%" class="detail-view table table-striped table-condensed medical_cart_list">
                <thead>
                <tr>
                    <th width="6%"><?php _e('Date','appointzilla'); ?> </th>
                    <th width="6%"><?php _e('Code','appointzilla'); ?></th>
                    <th width="6%"><?php _e('Tooth','appointzilla'); ?></th>
                    <th width="26%"><?php _e('Note','appointzilla'); ?></th>
                    <th width="50%"><?php _e('Images','appointzilla'); ?></th>
                </tr>
                </thead>
                <?php foreach($medicalCartRows as $key=>$singleRow):
                $imageList = explode(',',$singleRow['medical_cart_image_ids']);?>
                <tr>
                    <td><?php echo $singleRow['medical_cart_date']; ?></td>
                    <td><?php echo $singleRow['medical_cart_code']; ?></td>
                    <td><?php echo $singleRow['medical_cart_tooth']; ?></td>
                    <td><?php echo $singleRow['medical_cart_note']; ?></td>
                    <td><div="imglist">
                        <?php foreach($imageList as $singleImage):
                        $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
                        if ($imgStuff!=''):?>
                        <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
                    <?php endif;?>
                    <?php endforeach;?>
                </tr>
                <?php endforeach;?>
            </table>
            <?php }
            else{?>
                <div class="alert alert-warning"><?php _e('Cart was not created', 'appointzilla'); ?></div>
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
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
</div>
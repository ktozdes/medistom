<?php
class AppointmentController
{
    private $timeFormat;
    private $currency;
    private $dateFormat;
    public function __construct()
    {
        $this->timeFormat   = (get_option('apcal_time_format') == '') ? "h:i" : get_option('apcal_time_format');
        $this->dateFormat   = (get_option('apcal_date_format') == '') ? "d-m-Y" : get_option('apcal_date_format');
        $this->currency     = get_option('cal_admin_currency');
    }
	
    public function getEditableMainAppointmentTab($params)
    {
        global $wpdb;
        $appointment_table  = $wpdb->prefix . "ap_appointments";
        $staff_table_name   = $wpdb->prefix . "ap_staff";
        $clients_table   = $wpdb->prefix . "ap_clients";
        $treatment_table   = $wpdb->prefix . "ap_treatment";
        $AppointmentDetails = $wpdb->get_row("SELECT *,$clients_table.name as clientName, $clients_table.phone as clientPhone, $clients_table.email as clientEmail FROM `$appointment_table` INNER JOIN $clients_table on $clients_table.id = $appointment_table.client_id WHERE $appointment_table.`id` ='$params[appointmentID]'",ARRAY_A);
        ?>
        <table width="100%" class="table table-hover" >
            <tr>
                <th scope="row" style="width:15%;"><?php _e('Appointment Creation Date', 'appointzilla'); ?> </th>
                <td ><strong>:</strong></td>
                <td style="width:80%;"><?php echo date($this->dateFormat." ".$this->timeFormat.":s", strtotime("$AppointmentDetails[book_date]")); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Client Info', 'appointzilla', 'appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><?php _e('Name', 'appointzilla', 'appointzilla'); ?>:<?php echo $AppointmentDetails[clientName]; ?> <?php _e('Phone', 'appointzilla'); ?>:<?php echo $AppointmentDetails[clientPhone]; ?></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Staff', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="staffid" name="staffid">
                        <?php //get all service list
                        global $wpdb;

                        $staff_list = $wpdb->get_results("select * from $staff_table_name",ARRAY_A);
                        foreach($staff_list as $staff) { ?>
                            <option value="<?php echo $staff[id]; ?>"
                                <?php if($AppointmentDetails[staff_id] == $staff[id] ) echo "selected";  ?> ><?php echo $staff[name]; ?></option>
                        <?php } ?>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('staff Name.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Treatment', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="treatmentID" name="treatmentID">
                        <option value="" <?php echo ($AppointmentDetails[treatment_id] == '' || $AppointmentDetails[treatment_id] == '0' ) ? "selected":'';?>></option>
                        <?php //get all service list
                        global $wpdb;
                        $treatmentList = $wpdb->get_results("select * from $treatment_table",ARRAY_A);
                        foreach($treatmentList as $treatment) { ?>
                            <option value="<?php echo $treatment[treatment_id]; ?>"
                                <?php echo ($AppointmentDetails[treatment_id] == $treatment[treatment_id] ) ? "selected":'';  ?> ><?php echo $treatment[treatment_name]; ?></option>
                        <?php } ?>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Treatment.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Start Time', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_time" type="text" id="start_time" value="<?php echo date($this->timeFormat, strtotime($AppointmentDetails[start_time])); ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Start Time.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('End Time', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="end_time" type="text" id="end_time" value="<?php echo date($this->timeFormat, strtotime($AppointmentDetails[end_time])); ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment End Time.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Date', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_date" type="text" id="start_date" value="<?php echo date($this->dateFormat,strtotime($AppointmentDetails[date])); ?>" class="inputheight">&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Date.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Description', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><textarea name="app_desc" id="app_desc"><?php echo $AppointmentDetails[note]; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Description.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Status', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="app_status" name="app_status">
                        <option value="pending" <?php if($AppointmentDetails[status] == 'pending') echo "selected"; ?> ><?php _e('Pending', 'appointzilla'); ?></option>
                        <option value="approved" <?php if($AppointmentDetails[status] == 'approved') echo "selected"; ?> ><?php _e('Approved', 'appointzilla'); ?></option>
                        <option value="cancelled" <?php if($AppointmentDetails[status] == 'cancelled') echo "selected"; ?> ><?php _e('Cancelled', 'appointzilla'); ?></option>
                        <option value="done" <?php if($AppointmentDetails[status] == 'done') echo "selected"; ?> > <?php _e('Done', 'appointzilla'); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Status.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Payment Status', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="payment_status" name="payment_status">
                        <option value="unpaid" <?php if($AppointmentDetails[payment_status] == 'unpaid') echo "selected"; ?>><?php _e('Unpaid', 'appointzilla'); ?></option>
                        <option value="paid" <?php if($AppointmentDetails[payment_status] == 'paid') echo "selected"; ?>><?php _e('Paid', 'appointzilla'); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Appointment Status.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>
                </td>
            </tr>
        </table>
        <?php
    }

    public function getSelectableServiceTab($params)
    {
        global $wpdb;
        $service_table              = $wpdb->prefix . "ap_services";
        $appointment_service_table  = $wpdb->prefix . "ap_appointment_service";
        ?>
        <table class="table table-hover">
            <thead>
            <tr class="filter_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>">
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
            <tr class="title_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>">
                <td><strong><?php _e('Code','appointzilla');?></strong></td>
                <td><strong><?php _e('Name','appointzilla');?></strong></td>
                <td><strong><?php _e('Cost','appointzilla');?></strong></td>
                <td><strong><?php _e('Discount','appointzilla');?></strong></td>
                <td><strong> <?php _e('Action','appointzilla');?></strong></td>
            </tr>
            </thead>
            <tbody><?php // get service list group wise
            $ServiceDetails = $wpdb->get_results("SELECT $service_table.id as id, code, name, cost,percentage_ammount,  category_id, $appointment_service_table.id as checkbox_id, discount FROM $service_table LEFT JOIN $appointment_service_table on $appointment_service_table.service_id = $service_table.id AND $appointment_service_table.appointment_id = $params[appointmentID] WHERE `category_id` =$params[serviceCategoryID] AND availability = 'yes' AND accept_payment='yes' ORDER BY implant");
            //echo $wpdb->last_query;
            foreach($ServiceDetails as $service) {?>
                <tr class="odd value_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>" style="border-bottom:1px;">
                    <td><?php echo ucwords($service->code); ?></td>
                    <td><?php echo ucwords($service->name); ?></td>
                    <td>
                        <?php
                        if($this->currency) {
                            $CurrencyTableName = $wpdb->prefix . "ap_currency";
                            $cal_admin_currency = $wpdb->get_row("SELECT `symbol` FROM `$CurrencyTableName` WHERE `id` = '$this->currency'");
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
                        <input type="checkbox" class="checkbox_<?php echo $service->id; ?>" name="selected[<?php echo $service->id; ?>]" <?php echo ($service->checkbox_id!=null)?'checked="checked"':''?>/>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php
    }

    public function getPaymentTab($params)
    {
        global $wpdb;
        $service_table              = $wpdb->prefix . "ap_services";
        $appointment_service_table  = $wpdb->prefix . "ap_appointment_service";
        $payment_table              = $wpdb->prefix . "ap_payment_transaction";

        $ServicesList = $wpdb->get_results("SELECT cost, discount, (SELECT sum( ammount )FROM $payment_table  WHERE app_id =$params[appointmentID]) AS paid  FROM $appointment_service_table
        INNER JOIN $service_table on $appointment_service_table.service_id = $service_table.id
        WHERE `appointment_id` = $params[appointmentID]",ARRAY_A);
        if (count($ServicesList)>0){
        $total = $this->getTotalPrice($params[appointmentID]);?>
    <div class="payment_container">
        <table class='table table-hover' style="width:100%">
            <tr class='header-row'>
                <td><strong><?php _e('Full Price', 'appointzilla'); ?> : </strong><?php echo $total[full_price]; ?></td>
                <td><strong><?php _e('Discount Price', 'appointzilla'); ?> : </strong><?php echo $total[discount_price]; ?></td>
                <td><strong><?php _e('Paid', 'appointzilla'); ?> : </strong><?php echo $total[paid]; ?></td>
                <td><strong><?php _e('Left', 'appointzilla'); ?> : </strong><?php echo ($total[discount_price] - $total[paid]); ?></td>
            </tr>
        </table>
        <?php $PaymentList = $wpdb->get_results("SELECT * FROM $payment_table
        WHERE `app_id` = $params[appointmentID]",ARRAY_A);
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
                        <td><a onclick="deletePayment(<?php echo $singlePayment[id].', '.$params[appointmentID] ;?>)" rel="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="icon-remove"></i></a></td>
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
                    <td><a href="javascript:void(0)" onclick="newPayment(<?php echo $params[appointmentID];?>)" class="btn "><?php _e('Pay', 'appointzilla'); ?></a></td>
                </tr>
            </table>
        </div>
        <div id="loading" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
    <?php endif;?>
    <?php
    } else {?>
        <div><?php _e('No Service Selected', 'appointzilla'); ?></div>
    <?php }
    }

    public function getMedicalCartTab($params)
    {
        global $wpdb;
        $medical_cart_treatment_table   = $wpdb->prefix . "ap_medical_cart_treatment";
        $medical_cart_table             = $wpdb->prefix . "ap_medical_cart";
        $diagnosis_table                = $wpdb->prefix . "ap_diagnosis";
        $treatment_table                = $wpdb->prefix . "ap_treatment";
        $medicalCartRows = $wpdb->get_results("SELECT * FROM $medical_cart_table
            LEFT JOIN $diagnosis_table on $diagnosis_table.diagnosis_id  = $medical_cart_table.medical_cart_diagnosis_id
            WHERE `medical_cart_client_id` = $params[client_id]
            Order by medical_cart_id DESC",ARRAY_A);
    if (count($medicalCartRows)>0){?>
    <table width="100%" class="table table-hover">
        <thead>
        <tr>
            <th width="5%"><?php _e('Date','appointzilla'); ?> </th>
            <th width="5%"><?php _e('Tooth','appointzilla'); ?></th>
            <th width="15%"><?php _e('Note','appointzilla'); ?></th>
            <th width="35%"><?php _e('Diagnosis','appointzilla'); ?></th>
            <th width="35%"><?php _e('Images','appointzilla'); ?></th>
            <th width="5%"><?php _e('Action','appointzilla'); ?></th>
        </tr>
        </thead>
        <?php foreach($medicalCartRows as $key=>$singleRow):
            $imageList = explode(',',$singleRow['medical_cart_image_ids']);?>
            <tr>
                <td><?php echo $singleRow['medical_cart_date']; ?></td>
                <td><?php echo $singleRow['medical_cart_tooth']; ?></td>
                <td><?php echo $singleRow['medical_cart_note']; ?></td>
                <td><?php echo $singleRow['diagnosis_name']; ?>
                    <?php
                    $treatmentList = $wpdb->get_results("SELECT medical_cart_treatment_date, $medical_cart_treatment_table.treatment_id,$treatment_table.treatment_name FROM $medical_cart_treatment_table
                        INNER JOIN $medical_cart_table on $medical_cart_table.medical_cart_id  = $medical_cart_treatment_table.medical_cart_id
                        INNER JOIN $treatment_table on $medical_cart_treatment_table.treatment_id = $treatment_table.treatment_id
                        WHERE $medical_cart_treatment_table.`medical_cart_id` = $singleRow[medical_cart_id]",ARRAY_A);
                    ?>
                    <?php if (count($treatmentList)>0):?>
                    <h6><?php _e('Treatment','appointzilla'); ?></h6>
                    <ul>
                        <?php foreach($treatmentList as $singleTreatment):?>
                            <li><?php echo $singleTreatment[medical_cart_treatment_date]. ' '. $singleTreatment[treatment_name];?> </li>
                        <?php endforeach;?>
                    </ul>
                <?php else:?>
                <?php endif;?>
                </td>
                <td><div="imglist">
                    <?php foreach($imageList as $singleImage):
                        $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
                        if ($imgStuff!=''):?>
                            <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
                        <?php endif;?>
                    <?php endforeach;?>
                </td>
                <td></td>
            </tr>
        <?php endforeach;?>
    </table>
    <?php }
    else{?>
        <div class="alert alert-warning"><?php _e('Cart was not created', 'appointzilla'); ?></div>
    <?php }?>
    <div id="medical_cart_action">
        <a class="btn" href="?page=medical_cart&action=new&client_id=<?php echo $params[client_id]?>&appointment_id=<?php echo $params[appointmentID]?>"><i class="icon-plus icon-white"></i><?php _e('New Medical Info','appointzilla'); ?></a>
    </div>
    <?php
    }

    public function createAppointment($params)
	{
		$AppointmentKey = md5(date("F j, Y, g:i a"));
		if(isset($AllCalendarSettings['apcal_new_appointment_status'])) {
			$Status = $AllCalendarSettings['apcal_new_appointment_status'];
		} else {
			$Status = "pending";
		}
		$AppointmentBy = "user";
		$Recurring = "no";
		$RecurringType = "none";
		$PaymentStatus = "unpaid";
		global $wpdb;
		
		$AppointmentsTable = $wpdb->prefix ."ap_appointments";
		$reminder_table = $wpdb->prefix ."ap_reminders";
		$client_table = $wpdb->prefix ."ap_clients";
		
		$CreateAppointments = "INSERT INTO `$AppointmentsTable` (`id` ,`name` ,`email` ,`staff_id` ,`cabinet_id` ,`phone` ,`start_time` ,`end_time` ,`date` ,`note` , `appointment_key` ,`status` ,`recurring` ,`recurring_type` ,`appointment_by`, `payment_status`) VALUES ('NULL', '$_POST[ClientFirstName]', '$_POST[ClientEmail]', '$_POST[StaffId]','$_POST[CabinetId]', '$_POST[ClientPhone]', '$_POST[StartTime]', '$_POST[EndTime]', '$_POST[AppDate]', '', '$AppointmentKey', '$Status', '$Recurring', '$RecurringType', '$AppointmentBy', '$PaymentStatus');";
		if($wpdb->query($CreateAppointments)) {
			$LastAppointmentId = mysql_insert_id();
			$ClientTable = $wpdb->prefix."ap_clients";
			$ExistClientDetails = $wpdb->get_row("SELECT * FROM `$ClientTable` WHERE `email` = '".$_POST[ClientEmail]."' OR (`name` like '%".$_POST[ClientFirstName]."%'");
			if(count($ExistClientDetails)) {
				// update  exiting client deatils
				$LastClientId = $ExistClientDetails->id;
			} else {
				// insert new client deatils
				$InsertClient = "INSERT INTO `$ClientTable` (`id` ,`name` ,`email` ,`phone`,address,occupation ,`note`) VALUES ('NULL', '$_POST[ClientFirstName]', '$_POST[ClientEmail]', '$_POST[ClientPhone]','$_POST[ClientAddress_]', '$_POST[ClientOccupation]', '$_POST[ClientNote]');";
				if($wpdb->query($InsertClient)) {
					$LastClientId = mysql_insert_id();
				}
			}
			$wpdb->update(
				$AppointmentsTable,
				array('client_id'=>$LastClientId),
				array(id=>$LastAppointmentId)
			);
			//sending mail to staff
            $clientData = $wpdb->get_row("SELECT * FROM $client_table WHERE id = $_POST[StaffId]",ARRAY_A);
            $appDate = DateTime::createFromFormat('Y-m-d', $_POST[AppDate]);
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );

			wp_mail( $clientData[email], __('New Appointment For Dental Service','appointzilla'),"
			Уважаемый $clientData[name],
			Ниже указано время вашего лечения.
			<table>
			<tr>
				<td>".__('Date','appointzilla').':</td><td>'.$appDate->format($this->dateFormat).'</td>
				<td>'.__('Time','appointzilla').':</td><td>'.$_POST[StartTime].' - '.$_POST[EndTime].'</td>
			</tr>
			</table>');

			// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

			function set_html_content_type() {

				return 'text/html';
			}
			return $LastAppointmentId;
		}
		return -1;
	}
	
	public function getTotalPrice($appID)
    {
        global $wpdb;
        $appointment_service_table = $wpdb->prefix . "ap_appointment_service";
        $service_table          = $wpdb->prefix . "ap_services";
        $payment_table          = $wpdb->prefix . "ap_payment_transaction";
        $returnValue = array();
        $ServiceDetails = $wpdb->get_results("SELECT cost, discount, (SELECT sum( ammount ) FROM $payment_table WHERE app_id =$appID) AS paid
            FROM $appointment_service_table
            INNER JOIN $service_table  on $appointment_service_table.service_id = $service_table.id
            WHERE $appointment_service_table.appointment_id = $appID", ARRAY_A);
        //echo $wpdb->last_query;
        foreach($ServiceDetails as $service) {
            $returnValue[full_price] += $service[cost];
            $returnValue[discount_price] += (strpos($service[discount],'%')!==false) ? round($service[cost]*(100-$service[discount])/100,2) :
                round($service[cost]-$service[discount]);
            $returnValue[paid] = $service[paid];
        }
        return $returnValue;
    }

    public function paymentExists($params)
    {
        global $wpdb;
        $appointments_table  = $wpdb->prefix . "ap_appointments";
        $payment_table       = $wpdb->prefix . "ap_payment_transaction";
        $paymentList = $wpdb->get_results("SELECT * FROM $payment_table", ARRAY_A);
        if ($wpdb->num_rows>0)
            return true;
        return false;

    }

    public function updateAppointment($params)
    {

        $discountList = array();
        foreach($_POST[selected] as $key=>$singleSelected){
            $discountList[$key] = $_POST[discount][$key];
        }
        $_POST[discount] = $discountList;
        global $wpdb;
        $up_app_id = $_POST['updateppointments'];
        $treatmentID = $_POST['treatmentID'];
        $staffid = $_POST['staffid'];
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
        $result = $wpdb->query("UPDATE `$AppointmentTable` SET `staff_id` = '$staffid', treatment_id = $treatmentID,  `start_time` = '$start_time', `end_time` = '$end_time', `date` = '$appointmentdate', `note` = '$note', `status` = '$status', `recurring` = '$recurring', `recurring_type` = '$recurring_type', `recurring_st_date` = '$recurring_st_date', `recurring_ed_date` = '$recurring_ed_date', `payment_status` = '$payment_status' WHERE `id` = '$up_app_id'");
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
                    $StaffId = $staffid;
                    $ClientId = $GetClient->id;
                    //include notification class
                    $Notification = new Notification();
                    if($status == 'approved') $On = "approved";
                    if($status == 'cancelled') $On = "cancelled";
                    $Notification->notifyclient($On, $AppId, $treatmentID, $StaffId, $ClientId, $BlogName);
                    if(get_option('staff_notification_status') == 'on') {
                        $Notification->notifystaff($On, $AppId, $treatmentID, $StaffId, $ClientId, $BlogName);
                    }
                }
            }// end send notification to client if appointment approved or cancelled ckech


            //redirect to updated appointment details page
            $message = __('Appointment successfully updated', 'appointzilla');
            return $message;
        }
        else {
            $message = __('Appointment was not updated', 'appointzilla');
            return $message;
        }
    }
}
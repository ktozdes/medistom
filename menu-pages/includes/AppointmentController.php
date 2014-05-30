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
        $AppointmentDetails = $wpdb->get_row("SELECT * FROM `$appointment_table` WHERE $appointment_table.`id` ='$params[appointmentID]'",ARRAY_A);
        ?>
        <table width="100%" class="table table-hover" >
            <tr>
                <th scope="row"><?php _e('Appointment Creation Date', 'appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td><?php echo date($this->dateFormat." ".$this->timeFormat.":s", strtotime("$AppointmentDetails[book_date]")); ?></td>
            </tr>
            <tr>
                <th width="16%" scope="row"><?php _e('Name', 'appointzilla', 'appointzilla'); ?></th>
                <td width="5%"><strong>:</strong></td>
                <td width="79%"><input name="appname" type="text" id="appname" value="<?php echo $AppointmentDetails[name]; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Name.', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e('Email', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appemail" type="text" id="appemail" value="<?php echo $AppointmentDetails[email]; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Email.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
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
                <th scope="row"><strong><?php _e('Phone', 'appointzilla'); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appphone" type="text" id="appphone" value="<?php echo $AppointmentDetails[phone]; ?>" class="inputheight" maxlength="12">&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Phone Number.', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
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
                        <input type="checkbox" name="selected[<?php echo $service->id; ?>]" <?php echo ($service->checkbox_id!=null)?'checked="checked"':''?>/>
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
}
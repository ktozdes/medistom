<?php
class ReportController
{
    function __construct()
    {
    }

    function getFilterStaff($params = null)
    {
        global $wpdb;
        $staff_table = $wpdb->prefix . "ap_staff";
        $clients_table = $wpdb->prefix . "ap_clients";
        $staffList = $wpdb->get_results("SELECT * FROM $staff_table",ARRAY_A);
        $clientList = $wpdb->get_results("SELECT * FROM $clients_table",ARRAY_A);?>

        <table class="table filter_table">
            <tr class="filter_row">
                <td><input type="hidden" class="filter_category"  value="staff"/>
                    <?php _e('Date','appointzilla'); ?> : <input type="text" class="datepicker filter_start_date"style="max-width: 120px;"/>  <input type="text" class="datepicker filter_end_date"style="max-width: 120px;"/></td>
                <td><?php _e('Staff','appointzilla'); ?> : <select class="filter_staff" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($staffList as $singleStaff):?>
                        <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td><?php _e('Client','appointzilla'); ?> : <select class="filter_client" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($clientList as $singleStaff):?>
                            <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td><button class="btn filterbutton"><i class="icon-filter "></i> <?php _e('Filter','appointzilla'); ?></button></td>
            </tr>
        </table>
        <?php
    }

    function getFilterService($params = null)
    {
        global $wpdb;
        $srv_table = $wpdb->prefix . "ap_services";
        $staff_table = $wpdb->prefix . "ap_staff";
        $client_table = $wpdb->prefix . "ap_clients";
        $serviceList = $wpdb->get_results("SELECT * FROM $srv_table",ARRAY_A);
        $staffList = $wpdb->get_results("SELECT * FROM $staff_table",ARRAY_A);
        $clientList = $wpdb->get_results("SELECT * FROM $client_table",ARRAY_A);?>

        <table class="table filter_table">
            <tr class="filter_row">
                <td><input type="hidden" class="filter_category"  value="service"/>
                    <?php _e('Date','appointzilla'); ?> : <input type="text" class="datepicker filter_start_date" style="max-width: 120px;"/>  <input type="text" class="datepicker filter_end_date" style="max-width: 120px;"/></td>
                <td><?php _e('Price','appointzilla'); ?> : <input type="text" class="filter_min_numeric" style="max-width: 100px;"/>  <input type="text" class="filter_max_numeric" style="max-width: 100px;"/></td>
                <td><?php _e('Service','appointzilla'); ?> : <select class="filter_service" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($serviceList as $singleService):?>
                            <option value="<?php echo $singleService[id];?>"><?php echo $singleService[code].' '.$singleService[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td></td>
                </tr>
                <tr>
                <td><?php _e('Staff','appointzilla'); ?> : <select class="filter_staff" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($staffList as $singleStaff):?>
                            <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td><?php _e('Client','appointzilla'); ?> : <select class="filter_client" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($clientList as $singleStaff):?>
                            <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                    <td></td>
                    <td ><button class="btn filterbutton"><i class="icon-filter "></i> <?php _e('Filter','appointzilla'); ?></button></td>
            </tr>
        </table>
    <?php
    }

    function getFilterAppointment($params = null)
    {
        global $wpdb;
        $staff_table = $wpdb->prefix . "ap_staff";
        $clients_table = $wpdb->prefix . "ap_clients";
        $appointment_table = $wpdb->prefix . "ap_appointments";
        $staffList = $wpdb->get_results("SELECT * FROM $staff_table",ARRAY_A);
        $clientList = $wpdb->get_results("SELECT * FROM $clients_table",ARRAY_A);
        $appointmentList = $wpdb->get_results("SELECT * FROM $appointment_table",ARRAY_A);?>

        <table class="table filter_table">
            <tr class="filter_row">
                <td><input type="hidden" class="filter_category"  value="appointment"/>
                    <?php _e('Date','appointzilla'); ?> : <input type="text" class="datepicker filter_start_date" style="max-width: 120px;"/>  <input type="text" class="datepicker filter_end_date" style="max-width: 120px;"/></td>
                <td><?php _e('Payment Status','appointzilla'); ?> : <select class="filter_payment" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <option value="paid" ><?php _e('Paid','appointzilla'); ?></option>
                        <option value="unpaid" ><?php _e('Unpaid','appointzilla'); ?></option>
                    </select></td>
                <td><?php _e('Appointment No','appointzilla'); ?> : <select class="filter_appointment" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($appointmentList as $singleStaff):?>
                        <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[id];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td></td>
            </tr>
            <tr>
                <td><?php _e('Staff','appointzilla'); ?> : <select class="filter_staff" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($staffList as $singleStaff):?>
                            <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td><?php _e('Client','appointzilla'); ?> : <select class="filter_client" style="max-width: 120px;">
                        <option value="all" selected="selected"><?php _e('All','appointzilla');?></option>
                        <?php foreach($clientList as $singleStaff):?>
                            <option value="<?php echo $singleStaff[id];?>"><?php echo $singleStaff[name];?></option>
                        <?php endforeach;?>
                    </select></td>
                <td></td>
                <td><button class="btn filterbutton"><i class="icon-filter "></i> <?php _e('Filter','appointzilla'); ?></button></td>
            </tr>
        </table>
    <?php
    }

    function getContentStaff($params = null)
    {
        global $wpdb;
        $queryFilter = '';
        if (isset($params[category])){
            //date
            if (strlen($params[startDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')";
            }
            if (strlen($params[endDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[endDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[startDate]','%d-%endDate-%Y')";
            }
            if ($params[staffID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.staff_id = $params[staffID]" : " AND app_tb.staff_id = $params[staffID]";
            if ($params[clientID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.client_id = $params[clientID]" : " AND app_tb.client_id = $params[clientID]";
        }

        $total = array();
        $prev_app_id = 0;
        $contentList = $wpdb->get_results("SELECT app_tb.id, app_tb.date, app_tb.start_time, app_tb.end_time, app_tb.cabinet_id, app_tb.client_id, app_tb.staff_id, stff_tb.name as staff_name,cb_tb.cabinet_name as cabinet_name, ser_tb.name as service_name,cl_tb.name as client_name, ser_tb.code, ser_tb.cost, app_ser_tb.discount,(select sum(ammount) FROM ms_ap_payment_transaction WHERE app_id =app_tb.id) as paid
        FROM `ms_ap_appointments` AS app_tb
        LEFT JOIN ms_ap_appointment_service AS app_ser_tb ON app_ser_tb.appointment_id = app_tb.id
        LEFT JOIN ms_ap_services AS ser_tb ON ser_tb.id = app_ser_tb.service_id
        LEFT JOIN ms_ap_staff AS stff_tb ON stff_tb.id = app_tb.staff_id
        LEFT JOIN ms_ap_clients AS cl_tb ON cl_tb.id = app_tb.client_id
        LEFT JOIN ms_ap_cabinets AS cb_tb ON cb_tb.cabinet_id = app_tb.cabinet_id
        $queryFilter
        ",ARRAY_A);
        foreach($contentList as $key=>$singleList){
            $total[$singleList[id]][paid]            = $singleList[paid];
            $total[$singleList[id]][full_price]     += $singleList[cost];
            $total[$singleList[id]][discount_price] += (strpos($singleList[discount],'%')!==false) ? round($singleList[cost]*(100-$singleList[discount])/100,2) : round($singleList[cost]-$singleList[discount]);
            $singleList[date] = DateTime::createFromFormat('Y-m-d', $singleList[date]);
            $contentList[$key][date] = $singleList[date]->format('d-m-Y');
        }
       // echo $wpdb->last_query;?>
        <table class="table table-hover table-content">
            <thead>
            <tr class="title_row">
                <td><strong><?php _e('Date','appointzilla');?></strong></td>
                <td><strong><?php _e('Staff','appointzilla');?></strong></td>
                <td><strong><?php _e('Client','appointzilla');?></strong></td>
                <td><strong><?php _e('Room','appointzilla');?></strong></td>
                <td><strong><?php _e('Time','appointzilla');?></strong></td>
                <td><strong><?php _e('Price','appointzilla');?></strong></td>
                <td><strong><?php _e('Paid','appointzilla');?></strong></td>
                <td><strong><?php _e('Left','appointzilla');?></strong></td>
            </tr>
            </thead>
        <?php foreach($contentList as $singleList):
            if ($prev_app_id!=$singleList[id]):?>
            <tr class="value_row">
                <td><?php echo $singleList[date];?></td>
                <td><?php echo $singleList[staff_name];?></td>
                <td><?php echo $singleList[client_name];?></td>
                <td><?php echo $singleList[cabinet_name];?></td>
                <td><?php echo $singleList[start_time].' - '.$singleList[end_time];?></td>
                <td><?php echo $total[$singleList[id]][discount_price];?></td>
                <td><?php echo $total[$singleList[id]][paid];?></td>
                <td><?php echo $total[$singleList[id]][discount_price]- $total[paid];?></td>
            </tr>
        <?php endif;
            $prev_app_id = $singleList[id];
            endforeach;?>
        </table>
        <?php
    }

    function getContentService($params = null)
    {
        global $wpdb;
        $staff_table = $wpdb->prefix . "ap_staff";
        $service_table = $wpdb->prefix . "ap_services";
        $appointment_service_table = $wpdb->prefix . "ap_appointment_service";
        $queryFilter = '';
        if (isset($params[category])){
            if (strlen($params[startDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')";
            }
            if (strlen($params[endDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[endDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[startDate]','%d-%endDate-%Y')";
            }
            if ($params[staffID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.staff_id = $params[staffID]" : " AND app_tb.staff_id = $params[staffID]";
            if ($params[clientID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.client_id = $params[clientID]" : " AND app_tb.client_id = $params[clientID]";
            if ($params[serviceID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_ser_tb.service_id = $params[serviceID]" : " AND app_ser_tb.service_id = $$params[serviceID]";
            if (strlen($params[minPrice])>1){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  cost >= $params[minPrice]":" AND cost >= $params[minPrice]";
            }
            if (strlen($params[maxPrice])>1){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  cost < $params[maxPrice]":" AND cost < $params[maxPrice]";
            }
        }

        $total = array();
        $prev_app_id = 0;
        $contentList = $wpdb->get_results("SELECT app_tb.id, app_ser_tb.service_id, app_tb.date, app_tb.start_time, app_tb.end_time, app_tb.client_id, app_tb.staff_id, stff_tb.name as staff_name, ser_tb.name as service_name,cl_tb.name as client_name, ser_tb.code, ser_tb.cost, app_ser_tb.discount
        FROM `ms_ap_appointments` AS app_tb
        INNER JOIN ms_ap_appointment_service AS app_ser_tb ON app_ser_tb.appointment_id = app_tb.id
        LEFT JOIN ms_ap_services AS ser_tb ON ser_tb.id = app_ser_tb.service_id
        LEFT JOIN ms_ap_staff AS stff_tb ON stff_tb.id = app_tb.staff_id
        LEFT JOIN ms_ap_clients AS cl_tb ON cl_tb.id = app_tb.client_id
        LEFT JOIN ms_ap_cabinets AS cb_tb ON cb_tb.cabinet_id = app_tb.cabinet_id
        $queryFilter
        ",ARRAY_A);
        foreach($contentList as $key=>$singleList){
            $singleList[date] = DateTime::createFromFormat('Y-m-d', $singleList[date]);
            $contentList[$key][date] = $singleList[date]->format('d-m-Y');
            $contentList[$key][discount_price] = (strpos($singleList[discount],'%')!==false) ? round($singleList[cost]*(100-$singleList[discount])/100,2) : round($singleList[cost]-$singleList[discount]);;
        }
        //echo $wpdb->last_query;?>
        <table class="table table-hover table-content">
            <thead>
            <tr class="title_row">
                <td><strong><?php _e('Date','appointzilla');?></strong></td>
                <td><strong><?php _e('Service','appointzilla');?></strong></td>
                <td><strong><?php _e('Staff','appointzilla');?></strong></td>
                <td><strong><?php _e('Client','appointzilla');?></strong></td>
                <td><strong><?php _e('Full Price','appointzilla');?></strong></td>
                <td><strong><?php _e('Discount Price','appointzilla');?></strong></td>
            </tr>
            </thead>
            <?php foreach($contentList as $singleList):?>
                    <tr class="value_row">
                        <td><?php echo $singleList[date];?></td>
                        <td><?php echo $singleList[code].' '.$singleList[service_name];?></td>
                        <td><?php echo $singleList[staff_name];?></td>
                        <td><?php echo $singleList[client_name];?></td>
                        <td><?php echo $singleList[cost];?></td>
                        <td><?php echo $singleList[discount_price];?></td>
                    </tr>
            <?php endforeach;?>
        </table>
    <?php
    }

    function getContentAppointment($params = null)
    {
        global $wpdb;
        $queryFilter = '';
        if (isset($params[category])){
            //date
            if (strlen($params[startDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') >= str_to_date('$params[startDate]','%d-%m-%Y')";
            }
            if (strlen($params[endDate])>3){
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE  str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[endDate]','%d-%m-%Y')": " ANS str_to_date(app_tb.date,'%Y-%m-%d') <= str_to_date('$params[startDate]','%d-%endDate-%Y')";
            }

            if ($params[staffID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.staff_id = $params[staffID]" : " AND app_tb.staff_id = $params[staffID]";
            if ($params[clientID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.client_id = $params[clientID]" : " AND app_tb.client_id = $params[clientID]";
            if ($params[payment]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.payment_status = '$params[payment]'" : " AND app_tb.payment_status = '$params[payment]'";
            if ($params[appointmentID]!='all')
                $queryFilter .= (strlen($queryFilter)<3) ? " WHERE app_tb.id = $params[appointmentID]" : " AND app_tb.id = $params[appointmentID]";
        }

        $total = array();
        $prev_app_id = 0;
        $contentList = $wpdb->get_results("SELECT app_tb.id, app_tb.date, app_tb.start_time, app_tb.end_time, app_tb.cabinet_id, app_tb.client_id, app_tb.staff_id, app_tb.payment_status, stff_tb.name as staff_name,cb_tb.cabinet_name as cabinet_name, ser_tb.name as service_name,cl_tb.name as client_name, ser_tb.code, ser_tb.cost, app_ser_tb.discount, app_ser_tb.update_date, (select sum(ammount) FROM ms_ap_payment_transaction WHERE app_id =app_tb.id) as paid
        FROM `ms_ap_appointments` AS app_tb
        LEFT JOIN ms_ap_appointment_service AS app_ser_tb ON app_ser_tb.appointment_id = app_tb.id
        LEFT JOIN ms_ap_services AS ser_tb ON ser_tb.id = app_ser_tb.service_id
        LEFT JOIN ms_ap_staff AS stff_tb ON stff_tb.id = app_tb.staff_id
        LEFT JOIN ms_ap_clients AS cl_tb ON cl_tb.id = app_tb.client_id
        LEFT JOIN ms_ap_cabinets AS cb_tb ON cb_tb.cabinet_id = app_tb.cabinet_id
        $queryFilter
        ",ARRAY_A);
        foreach($contentList as $key=>$singleList){
            $contentList[$key][discount_price] = (strpos($singleList[discount],'%')!==false) ? round($singleList[cost]*(100-$singleList[discount])/100,2) : round($singleList[cost]-$singleList[discount]);

            $total[$singleList[id]][paid]            = $singleList[paid];
            $total[$singleList[id]][full_price]     += $singleList[cost];
            $total[$singleList[id]][discount_price] += $contentList[$key][discount_price];

            $singleList[date] = DateTime::createFromFormat('Y-m-d', $singleList[date]);
            $contentList[$key][date] = $singleList[date]->format('d-m-Y');
            if ($singleList[update_date]!=''){
                $singleList[update_date] = DateTime::createFromFormat('Y-m-d', $singleList[update_date]);
                $contentList[$key][update_date] = $singleList[update_date]->format('d-m-Y');
            }
        };?>
        <table class="table table-hover table-content">
            <?php foreach($contentList as $singleList):
                if ($prev_app_id!=$singleList[id]):?>
                    <tr class="value_row header-row">
                        <td><strong><?php _e('Date','appointzilla');?></strong>:<?php echo $singleList[date];?></td>
                        <td><strong><?php _e('Staff','appointzilla');?></strong>:<?php echo $singleList[staff_name];?></td>
                        <td><strong><?php _e('Client','appointzilla');?></strong>:<?php echo $singleList[client_name];?></td>
                        <td><strong><?php _e('Discount Price','appointzilla');?></strong>:<?php echo $total[$singleList[id]][discount_price];?></td>
                        <td><strong><?php _e('Paid','appointzilla');?></strong>:<?php echo $total[$singleList[id]][discount_price];?></td>
                        <td><strong><?php _e('Left','appointzilla');?></strong>:<?php echo $total[$singleList[id]][discount_price] - $total[$singleList[id]][paid];?></td>
                    </tr>
                    <tr class="title_row">
                        <td><strong><?php _e('Date','appointzilla');?></strong></td>
                        <td colspan="3"><strong><?php _e('Service','appointzilla');?></strong></td>
                        <td><strong><?php _e('Full Price','appointzilla');?></strong></td>
                        <td><strong><?php _e('Discount Price','appointzilla');?></strong></td>
                    </tr>
                <?php endif;?>
                    <tr class="value_row">
                        <td><?php echo $singleList[update_date];?></td>
                        <td colspan="3"><?php echo $singleList[code].' '.$singleList[service_name];?></td>
                        <td><?php echo $singleList[cost];?></td>
                        <td><?php echo $singleList[discount_price];?></td>
                    </tr>
                <?php $prev_app_id = $singleList[id];
            endforeach;?>
        </table>
    <?php
    }
}
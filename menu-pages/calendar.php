<?php
$DateFormat = get_option('apcal_date_format');
if($DateFormat == '') $DateFormat = "d-m-Y";
$TimeFormat = get_option('apcal_time_format');
if($TimeFormat == '') $TimeFormat = "H:i";

global $wpdb;
$AppointmentTableName = $wpdb->prefix."ap_appointments";
$client_table = $wpdb->prefix."ap_clients";
$staff_table = $wpdb->prefix."ap_staff";
$EventTableName = $wpdb->prefix."ap_events";
$Select = 0;

require_once('includes/appointzilla-class.php');

$AppointZilla = new Appointzilla();
//check 2 way sync enable
$GoogleCalendarTwoWaySync = get_option('google_calendar_twoway_sync');
if($GoogleCalendarTwoWaySync == 'yes') {
    // fetch google appointment
    require_once('sync-google-appointment.php');
}

// load appointment n event from last past month
$Current_Month_First_Date = strtotime(date("Y-m-01"));
$Load_From_Last_Month = date("Y-m-d", strtotime("-6 month", $Current_Month_First_Date));
//only for recurring app
$Load_Recurring_From_Last_Month = date("Y-m-d", strtotime("-6 month", $Current_Month_First_Date));
if(isset($_POST['filterbystaff']) && $_POST[bystaff]>0) {
    $SelectStaff = $_POST[bystaff];
    $query = " AND `staff_id` = '$_POST[bystaff]'";
}
if(isset($_POST['filterbystaff']) && $_POST[bycabinet]>0) {
    $SelectCabinet = $_POST[bycabinet];
    $query .= " AND `cabinet_id` = '$_POST[bycabinet]'";
}

$FetchAllApps_sql = "select $AppointmentTableName.`id`, $client_table.`name` as name, $staff_table.name as staff_name, `start_time`, `end_time`, `date` FROM `$AppointmentTableName`
    INNER JOIN $staff_table on $staff_table.id = $AppointmentTableName.staff_id
    INNER JOIN $client_table on $client_table.id = $AppointmentTableName.client_id
WHERE (`recurring` = 'no' OR recurring='') $query AND `date` >= '$Load_From_Last_Month'";
$FetchAllRApps_sql = "select * FROM `$AppointmentTableName` WHERE `recurring` = 'yes' $query AND `recurring_st_date` >= '$Load_Recurring_From_Last_Month'";
//Normal Event
$FetchAllEvent_sql = "SELECT `id`, `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` WHERE `repeat` = 'N' AND `start_date` >= '$Load_From_Last_Month' ";

// Recurring Event
$FetchAllREvent_sql = "select `id`, `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` where `repeat` != 'N' AND `start_date` >= '$Load_Recurring_From_Last_Month' "; ?>

<!---render fullcalendar--->
<script type='text/javascript'>
jQuery(document).ready(function() {
    jQuery('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: false,
        weekends: true,
        timeFormat: <?php if($TimeFormat == 'h:i') echo "'h:mmtt{-h:mmtt }'"; else echo "'H:mm{-H:mm }'"; ?>,
        axisFormat: <?php if($TimeFormat == 'h:i') echo "'hh:mm'"; else echo "'HH:mm'"; ?>,
        <?php $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings')); ?>
        firstDay: <?php if($AllCalendarSettings['calendar_start_day']) echo $AllCalendarSettings['calendar_start_day']; else echo "0";  ?>,
        slotMinutes: <?php echo $AllCalendarSettings['calendar_slot_time']; ?>,
        minTime: 0,
        defaultView: '<?php echo $AllCalendarSettings['calendar_view']; ?>',
        maxTime: 24,
        <?php
            if($DateFormat == 'd-m-Y') $DPFormat = 'dd-mm-yy';
            if($DateFormat == 'm-d-Y') $DPFormat = 'mm-dd-yy';
            if($DateFormat == 'Y-m-d') $DPFormat = 'yy-mm-dd'; //coz yy-mm-dd not parsing in a correct date ?>
        selectable: true,
        selectHelper: false,
        monthNames: ["<?php _e("January", "appointzilla"); ?>","<?php _e("February", "appointzilla"); ?>","<?php _e("March", "appointzilla"); ?>","<?php _e("April", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("June", "appointzilla"); ?>","<?php _e("July", "appointzilla"); ?>", "<?php _e("August", "appointzilla"); ?>", "<?php _e("September", "appointzilla"); ?>", "<?php _e("October", "appointzilla"); ?>", "<?php _e("November", "appointzilla"); ?>", "<?php _e("December", "appointzilla"); ?>" ],
        monthNamesShort: ["<?php _e("Jan", "appointzilla"); ?>","<?php _e("Feb", "appointzilla"); ?>","<?php _e("Mar", "appointzilla"); ?>","<?php _e("Apr", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("Jun", "appointzilla"); ?>","<?php _e("Jul", "appointzilla"); ?>","<?php _e("Aug", "appointzilla"); ?>","<?php _e("Sept", "appointzilla"); ?>","<?php _e("Oct", "appointzilla"); ?>","<?php _e("nov", "appointzilla"); ?>","<?php _e("Dec", "appointzilla"); ?>"],
        dayNames: ["<?php _e("Sunday", "appointzilla"); ?>","<?php _e("Monday", "appointzilla"); ?>","<?php _e("Tuesday", "appointzilla"); ?>","<?php _e("Wednesday", "appointzilla"); ?>","<?php _e("Thursday", "appointzilla"); ?>","<?php _e("Friday", "appointzilla"); ?>","<?php _e("Saturday", "appointzilla"); ?>"],
        dayNamesShort: ["<?php _e("Sun", "appointzilla"); ?>","<?php _e("Mon", "appointzilla"); ?>", "<?php _e("Tue", "appointzilla"); ?>", "<?php _e("Wed", "appointzilla"); ?>", "<?php _e("Thus", "appointzilla"); ?>", "<?php _e("Fri", "appointzilla"); ?>", "<?php _e("Sat", "appointzilla"); ?>"],
        buttonText: {
            today: "<?php _e("Today", "appointzilla"); ?>",
            day: "<?php _e("Day", "appointzilla"); ?>",
            week:"<?php _e("Week", "appointzilla"); ?>",
            month:"<?php _e("Month", "appointzilla"); ?>"
        },
        events: [
            <?php //Loading Appointments On Calendar Start
            $AllAppointments = $wpdb->get_results($FetchAllApps_sql, OBJECT);
            if($AllAppointments) {
                foreach($AllAppointments as $single) {
                    $start = date("H, i", strtotime($single->start_time));
                    $end= date("H, i", strtotime($single->end_time));

                    // subtract 1 from month digit coz calendar work on month 0-11
                    $y = date ( 'Y' , strtotime( $single->date ) );
                    $m = date ( 'n' , strtotime( $single->date ) ) - 1;
                    $d = date ( 'd' , strtotime( $single->date ) );
                    $date = "$y-$m-$d";
                    $date = str_replace("-",", ", $date);
                    $url = "?page=update-appointment&viewid=".$single->id."&from=calendar"; ?>
                    {
                        id :'<?php echo $single->id; ?>',
                        title: "<?php _e("Client",'appointzilla'); echo ": ".ucwords($single->name); ?>, <?php _e("Staff",'appointzilla'); echo ": ".ucwords($single->staff_name); ?>",
                        start: new Date(<?php echo "$date, $start"; ?>),
                        end: new Date(<?php echo "$date, $end"; ?>),
                        url: "<?php echo $url; ?>",
                        allDay: false,
                        backgroundColor : "#1FCB4A",
                        textColor: "black",
                        description: "appointment"
                    }, <?php
                }
            }
            //Loading Appointments On Calendar End-

            //Loading Recurring Appointments On Calendar Start
            $AllRecurringAppointments = $wpdb->get_results($FetchAllRApps_sql, OBJECT);
            if($AllRecurringAppointments) {
                foreach($AllRecurringAppointments as $single) {
                    $title = $single->name;
                    if($single->recurring_type != 'monthly') {
                        $start_time = date("H, i", strtotime($single->start_time));
                        $end_time= date("H, i", strtotime($single->end_time));
                        $start_date = $single->recurring_st_date;
                        $end_date =  $single->recurring_ed_date;

                        //if appointment type then calculate RTC(recurring date calculation)
                        if($single->recurring_type == 'PD')
                        $RDC = 1;
                        if($single->recurring_type == 'daily')
                        $RDC = 1;
                        if($single->recurring_type == 'weekly')
                        $RDC = 7;

                        //calculate all dates
                        $Alldates = array();
                        $st_dateTS = strtotime($start_date);
                        $ed_dateTS = strtotime($end_date);
                        for ($currentDateTS = $st_dateTS; $currentDateTS <= $ed_dateTS; $currentDateTS += (60 * 60 * 24 * $RDC)) {
                            $currentDateStr = date("Y-m-d",$currentDateTS);
                            $AlldatesArr[] = $currentDateStr;

                            $url = "?page=update-appointment&viewid=".$single->id."&from=calendar";
                            // subtract 1 from month digit coz calendar work on month 0-11
                            $y = date ( 'Y' , strtotime( $currentDateStr ) );
                            $m = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                            $d = date ( 'd' , strtotime( $currentDateStr ) );
                            $eachdate = "$y-$m-$d";
                            //change format
                            $eachdate = str_replace("-",", ", $eachdate); ?>
                            {
                                id :'<?php echo $single->id; ?>',
                                title: "<?php _e('Booked By: ','appointzilla'); echo ucwords($title); ?>",
                                start: new Date(<?php echo "$eachdate, $start_time"; ?>),
                                end: new Date(<?php echo "$eachdate, $end_time"; ?>),
                                url: "<?php echo $url; ?>",
                                allDay: false,
                                backgroundColor : "<?php if($single->staff_id =='1') echo "#DD75DD"; else echo "#6A6AFF"; ?>",
                                textColor: "",
                                description: "appointment"
                            }, <?php
                        }// end of date calculation for
                    } else {
                        $start_time = date("H, i", strtotime($single->start_time));
                        $end_time= date("H, i", strtotime($single->end_time));

                        $start_date = $single->recurring_st_date;
                        $end_date =  $single->recurring_ed_date;

                        $i = 0;
                        do {
                                $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($start_date)));
                                // subtract 1 from $startdate month digit coz calendar work on month 0-11
                                $y = date ( 'Y' , strtotime( $NextDate ) );
                                $m = date ( 'n' , strtotime( $NextDate ) ) - 1;
                                $d = date ( 'd' , strtotime( $NextDate ) );
                                $start_date2 = "$y-$m-$d";
                                $start_date2 = str_replace("-",", ", $start_date2);     //changing date format
                                $end_date2 = str_replace("-",", ", $start_date2); ?>
                                {
                                    id :'<?php echo $single->id; ?>',
                                    title: "<?php _e('Booked By: ','appointzilla'); echo ucwords($title); ?>",
                                    start: new Date(<?php echo "$start_date2, $start_time"; ?>),
                                    end: new Date(<?php echo "$end_date2, $end_time"; ?>),
                                    url: "<?php echo $url; ?>",
                                    allDay: false,
                                    backgroundColor : "<?php if($single->staff_id =='1') echo "#DD75DD"; else echo "#6A6AFF"; ?>",
                                    textColor: "",
                                    description: "appointment"
                                }, <?php
                                $i = $i+1;
                            } while(strtotime($end_date) != strtotime($NextDate));
                    }// end of else

                } // end of fetching single appointment foreach
            } // end of if
            //Loading Recurring Appointments On Calendar End

            // Loading Events On Calendar Start
            $AllEvents = $wpdb->get_results($FetchAllEvent_sql, OBJECT);
            if($AllEvents) {
                foreach($AllEvents as $Event) {
                    //convert time format H:i:s
                    $starttime = date("H:i", strtotime($Event->start_time));
                    $endtime = date("H:i", strtotime($Event->end_time));
                    //change time format according to calendar
                    $starttime = str_replace(":",", ", $starttime);
                    $endtime = str_replace(":", ", ", $endtime);

                    $startdate = $Event->start_date;
                    // subtract 1 from $startdate month digit coz calendar work on month 0-11
                    $y = date ( 'Y' , strtotime( $startdate ) );
                    $m = date ( 'n' , strtotime( $startdate ) ) - 1;
                    $d = date ( 'd' , strtotime( $startdate ) );
                    $startdate = "$y-$m-$d";
                    $startdate = str_replace("-",", ", $startdate);     //changing date format

                    $enddate = $Event->end_date;
                    // subtract 1 from $startdate month digit coz calendar work on month 0-11
                    $y2 = date ( 'Y' , strtotime( $enddate ) );
                    $m2 = date ( 'n' , strtotime( $enddate ) ) - 1;
                    $d2 = date ( 'd' , strtotime( $enddate ) );
                    $enddate = "$y2-$m2-$d2";

                    $enddate = str_replace("-",", ", $enddate);         //changing date format
                    $url = "?page=update-timeoff&update_timeoff=".$Event->id."&from=calendar"; ?>
                    {
                        id :'<?php echo $Event->id; ?>',
                        title: "<?php echo ucwords($Event->name); ?>",
                        start: new Date(<?php echo "$startdate, $starttime"; ?>),
                        end: new Date(<?php echo "$enddate, $endtime"; ?>),
                        url: "<?php echo $url; ?>",
                        allDay: false,
                        backgroundColor : "#FF7575",
                        textColor: "black",
                        description: "timeoff"
                    }, <?php
                }
            }
            //Loading Events On Calendar End

            //Loading Recurring Events On Calendar Start
            $AllREvents = $wpdb->get_results($FetchAllREvent_sql, OBJECT);
            //dont show event on filtering
            if($AllREvents) {
                foreach($AllREvents as $Event) {
                    if($Event->repeat != 'M') {
                        //convert time format H:i:s
                        $starttime = date("H:i", strtotime($Event->start_time));
                        $endtime = date("H:i", strtotime($Event->end_time));
                        //change time format according to calendar
                        $starttime = str_replace(":",", ", $starttime);
                        $endtime = str_replace(":", ", ", $endtime);

                        $startdate = $Event->start_date;
                        $enddate = $Event->end_date;

                        //if appointment type then calculate RTC(recurring date calculation)
                        if($Event->repeat == 'PD')
                        $RDC = 1;
                        if($Event->repeat == 'D')
                        $RDC = 1;
                        if($Event->repeat == 'W')
                        $RDC = 7;
                        if($Event->repeat == 'BW')
                        $RDC = 14;
                        /*if($Event->repeat == 'M')
                        $RDC = 31;*/

                        $Alldates = array();
                        $st_dateTS = strtotime($startdate);
                        $ed_dateTS = strtotime($enddate);
                        for ($currentDateTS = $st_dateTS; $currentDateTS <= $ed_dateTS; $currentDateTS += (60 * 60 * 24 * $RDC)) {
                            $currentDateStr = date("Y-m-d",$currentDateTS);
                            $AlldatesArr[] = $currentDateStr;

                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y = date ( 'Y' , strtotime( $currentDateStr ) );
                            $m = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                            $d = date ( 'd' , strtotime( $currentDateStr ) );
                            $startdate = "$y-$m-$d";
                            $startdate = str_replace("-",", ", $startdate);     //changing date format

                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y2 = date ( 'Y' , strtotime( $currentDateStr ) );
                            $m2 = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                            $d2 = date ( 'd' , strtotime( $currentDateStr ) );
                            $enddate = "$y2-$m2-$d2";

                            $enddate = str_replace("-",", ", $enddate);     //changing date format
                            $url = "?page=update-timeoff&update_timeoff=".$Event->id."&from=calendar"; ?>
                            {
                                id :'<?php echo $Event->id; ?>',
                                title: "<?php echo ucwords($Event->name); ?>",
                                start: new Date(<?php echo "$startdate, $starttime"; ?>),
                                end: new Date(<?php echo "$enddate, $endtime"; ?>),
                                url: "<?php echo $url; ?>",
                                allDay: false,
                                backgroundColor : "#FF7575",
                                textColor: "black",
                                description: "timeoff"
                            }, <?php
                        }
                    } else { // end of if
                        //convert time format H:i:s
                        $starttime = date("H:i", strtotime($Event->start_time));
                        $endtime = date("H:i", strtotime($Event->end_time));
                        //change time format according to calendar
                        $starttime = str_replace(":",", ", $starttime);
                        $endtime = str_replace(":", ", ", $endtime);
                        $startdate = $Event->start_date;
                        $enddate = $Event->end_date;
                        $i = 0;
                        do {
                            $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($startdate)));
                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y = date ( 'Y' , strtotime( $NextDate ) );
                            $m = date ( 'n' , strtotime( $NextDate ) ) - 1;
                            $d = date ( 'd' , strtotime( $NextDate ) );
                            $startdate2 = "$y-$m-$d";
                            $startdate2 = str_replace("-",", ", $startdate2);       //changing date format
                            $enddate2 = str_replace("-",", ", $startdate2); ?>
                            {
                                id :'<?php echo $Event->id; ?>',
                                title: "<?php echo ucwords($Event->name); ?>",
                                start: new Date(<?php echo "$startdate2, $starttime"; ?>),
                                end: new Date(<?php echo "$enddate2, $endtime"; ?>),
                                url: "<?php echo $url; ?>",
                                allDay: false,
                                backgroundColor : "#FF7575",
                                textColor: "black",
                                description: "timeoff"
                            }, <?php
                            $i = $i+1;
                        } while(strtotime($enddate) != strtotime($NextDate));
                    }// enf of else
                }// end of foreach
            }// end of if check
            // Loading Recurring Events On Calendar End ?>

        ],
        select: function(start, end, allDay) {
            var appdate = jQuery.datepicker.formatDate('<?php echo $DPFormat; ?>', new Date(start));
            jQuery('#appdate').val(appdate);
            jQuery("#datepicker").datepicker("setDate", appdate);
            var dayOfWeek = jQuery('#datepicker').datepicker('getDate').getUTCDay();
            jQuery('#AppFirstModal').show();
            jQuery('#loading-staff').show();
            jQuery.ajax({
                dataType : 'html',
                type: 'GET',
                url : location.href,
                cache: false,
                data : 'selectedDate='+appdate+'&dayOfWeek='+dayOfWeek,
                complete : function() {  },
                success: function(data) {
                    jQuery('#loading-staff').hide();
                    data = jQuery(data).find('div#tempStaffList');
                    jQuery('#stfflistdiv').html(data);
                    jQuery('#stafflist').bind('change',function() { staffChanged() });
                }
            });
        },
        eventClick: function(event) {
            if (event.id) {
                var AppId  = event.id;
                var description = event.description;
                var searchIn = 'ac_appointment';
                if(description == 'timeoff') {
                    searchIn = 'timeoff'; } else  { searchIn = 'appointment';
                }
                var dataStringfirst = "AppId=" + AppId + "&searchIn=" + searchIn;
                var url = "?page=appointment-calendar";
                jQuery.ajax({
                    dataType : 'html',
                    type: 'POST',
                    url : url,
                    data : dataStringfirst,
                    complete : function() { },
                    success: function(data) {
                        data = jQuery(data).find('div#UpdateModalFinal');
                        jQuery("#AppUpdateModalFinal").show();
                        jQuery('#AppUpdateModalFinal').html(data);
                        showdate();
                    }
                });
                return true;
            }
        }
    }); // end of full-calendar js code


    // Launch Modal Form
    //show first modal
    jQuery('#addappointment').click(function(){
        jQuery('#AppFirstModal').show();
    });

    //hide modal
    jQuery('#close').click(function(){
        jQuery('#AppFirstModal').hide();
    });

    //load date picekr on modal for
    document.firstmodal.appdate.value = jQuery.datepicker.formatDate("<?php echo $DPFormat; ?>", new Date());
    jQuery(function(){
        jQuery("#datepicker").datepicker({
            inline: true,
            minDate: 0,
            altField: '#alternate',
            dateFormat:'dd-mm-yy',
            dayNames:['Пон','Вт','Ср',"Чтв",'Пят','Суб','Вос'],
            firstDay: <?php if($AllCalendarSettings['calendar_start_day']) echo $AllCalendarSettings['calendar_start_day']; else echo "0";  ?>,
            onSelect: function(dateText, inst) {
                var dayOfWeek = jQuery(this).datepicker('getDate').getUTCDay();
                var dateAsString = dateText;
                jQuery('#appdate').val(dateText);
                jQuery('#loading-staff').show();
                jQuery.ajax({
                    dataType : 'html',
                    type: 'GET',
                    url : location.href,
                    cache: false,
                    data : 'selectedDate='+dateText+'&dayOfWeek='+dayOfWeek,
                    complete : function() {  },
                    success: function(data) {
                        jQuery('#loading-staff').hide();
                        data = jQuery(data).find('div#tempStaffList');
                        jQuery('#stfflistdiv').html(data);
                        jQuery('#stafflist').bind('change',function() { staffChanged() });
                    }
                });
            }
        });
    });
});
// end of document.ready()

function LoadSecondModal() {
    var AppDate = jQuery('#appdate').val();
    var StaffId = jQuery('#stafflist').val();
    var CabinetID =  jQuery('#cabinetlist').val();
    var SecondData = "AppDate=" + AppDate + "&StaffId=" + StaffId+'&CabinetID='+CabinetID;
    var url = "?page=appointment-calendar";
    jQuery('#loading1').show();     // loading button onclick next1 at first modal
    jQuery('#next1').hide();        //hide next button
    jQuery.ajax({
        dataType : 'html',
        type: 'GET',
        url : url,
        cache: false,
        data : SecondData,
        complete : function() {  },
        success: function(data) {
            data = jQuery(data).find('div#AppSecondModalData');
            jQuery('#loading1').hide();
            jQuery('#AppFirstModal').hide();
            jQuery('#AppSecondModal').show();
            jQuery('#AppSecondModal').html(data);
        }
    });
}

function LoadSecondModal2() {
    jQuery('#AppThirdModal').hide();
    jQuery('#buttondiv').show();
    jQuery('#AppSecondModal').show();
}

//load first modal on click back1
function LoadFirstModal() {
    jQuery('#AppSecondModal').hide()
    jQuery('#AppFirstModal').show();
    jQuery('#next1').show();
}

//load third modal on-click next2
function LoadThirdModal() {
    //validation on second modal form
    var AppDate = jQuery('#AppDate').val();
    var StaffId = jQuery('#StaffId').val();
    var CabinetID = jQuery('#CabinetID').val();
    var Start_Time = jQuery('select[name=start_time]').val();
    var End_Time = jQuery('select[name=end_time]').val();

    if(!Start_Time || !End_Time) {
        jQuery("#time_slot_box").after("<span style='width:auto; margin-left:5%;' class='error'><strong><?php _e('Select any time.', 'appointzilla'); ?></strong></span>");
        return false;
    }
    if (Start_Time>=End_Time){
        jQuery("#time_slot_box").after("<span style='width:auto; margin-left:5%;' class='error'><strong><?php _e('Start Time cannot be bigger than End Time.', 'appointzilla'); ?></strong></span>");
        return false;
    }

    var ThirdData = "AppDate=" + AppDate + "&StaffId=" + StaffId + "&StartTime=" + Start_Time + "&EndTime=" + End_Time + "&CabinetID=" + CabinetID;

    var url = "?page=appointment-calendar";
    jQuery('#buttondiv').hide();
    jQuery('#loading').show();
    jQuery.ajax({
        dataType : 'html',
        type: 'GET',
        url : url,
        cache: false,
        data : ThirdData,
        complete : function() {  },
        success: function(data) {
            data = jQuery(data).find('div#AppThirdModalData');
            jQuery('#loading').hide();
            jQuery('#AppSecondModal').hide();
            jQuery('#AppThirdModal').show();
            jQuery('#AppThirdModal').html(data);
        }
    });
}


function CloseModelform() {
    jQuery('#AppForthModalFinal').hide();
    jQuery('#AppSecondModalData').hide();
    jQuery('#AppThirdModalData').hide();
    location.href = location.href;
}

// hide all
function hideAll() {
    jQuery('#Nclr').css('color', '#FE0000');
    jQuery('#Dclr').css('color', '#000000');
    jQuery('#Wclr').css('color', '#000000');
    jQuery('#Mclr').css('color', '#000000');
    jQuery('#PDclr').css('color', '#000000');

    jQuery('#dailydiv').hide();
    jQuery('#weeklydiv').hide();
    jQuery('#monthlydiv').hide();
    jQuery('#PDdiv').hide();
}


//show daily div
function showdaily() {
    jQuery('#Nclr').css('color', '#000000');
    jQuery('#Dclr').css('color', '#FE0000');
    jQuery('#Wclr').css('color', '#000000');
    jQuery('#Mclr').css('color', '#000000');
    jQuery('#PDclr').css('color', '#000000');

    jQuery('#dailydiv').show();
    jQuery('#weeklydiv').hide();
    jQuery('#monthlydiv').hide();
    jQuery('#PDdiv').hide();
}


//show weekly div
function showweekly() {
    jQuery('#Nclr').css('color', '#000000');
    jQuery('#Dclr').css('color', '#000000');
    jQuery('#Wclr').css('color', '#FE0000');
    jQuery('#Mclr').css('color', '#000000');
    jQuery('#PDclr').css('color', '#000000');
    jQuery('#dailydiv').hide();

    jQuery('#weeklydiv').show();
    jQuery('#monthlydiv').hide();
    jQuery('#PDdiv').hide();
}


//show month div
function showmonthly() {
    jQuery('#Nclr').css('color', '#000000');
    jQuery('#Dclr').css('color', '#000000');
    jQuery('#Wclr').css('color', '#000000');
    jQuery('#Mclr').css('color', '#FE0000');
    jQuery('#PDclr').css('color', '#000000');

    jQuery('#dailydiv').hide();
    jQuery('#weeklydiv').hide();
    jQuery('#monthlydiv').show();
    jQuery('#PDdiv').hide();
}


function showParticularDate() {
    jQuery('#Nclr').css('color', '#000000');
    jQuery('#Dclr').css('color', '#000000');
    jQuery('#Wclr').css('color', '#000000');
    jQuery('#Mclr').css('color', '#000000');
    jQuery('#PDclr').css('color', '#FE0000');

    jQuery('#dailydiv').hide();
    jQuery('#weeklydiv').hide();
    jQuery('#monthlydiv').hide()
    jQuery('#PDdiv').show();
}

// check valid select start time and End time
function endtimechange() {
    var start_time = jQuery("select#start_time").val();
    var end_time =   jQuery("select#end_time").val();
    var startDate = new Date("1/1/1900 " + start_time);
    var endDate = new Date("1/1/1900 " + end_time);
    var difftime=endDate - startDate;
    if (difftime < 1) { alert("<?php echo __('Invalid time range.', 'appointzilla'); ?>");
        jQuery('#end_time_error').show();
        return false;
    } else {
        jQuery('#start_time_error').hide();
        jQuery('#end_time_error').hide();
    }
}

// check valid select start time and End time
function starttimechange() {
    var start_time = jQuery("select#start_time").val();
    var end_time =   jQuery("select#end_time").val();
    var startDate = new Date("1/1/1900 " + start_time);
    var endDate = new Date("1/1/1900 " + end_time);
    var difftime=endDate - startDate;

    if (difftime < 1) {
        alert("<?php echo __('Invalid time range.', 'appointzilla'); ?>");
        jQuery('#start_time_error').show();
          return false;
    } else {
         jQuery('#start_time_error').hide();
         jQuery('#end_time_error').hide();
    }
}
function TryAgainBooking() {
    jQuery("#check-email-result-div").hide();
    jQuery("#check-user").show();
    jQuery('#check-email-div-form').show();
    jQuery("#existing-user-form-btn").show();
}

function staffChanged()
{
    var StaffId = jQuery("select#stafflist").val();
    if(StaffId > 0) {
        jQuery('#loading-staff').show();
        jQuery('#cabinet').hide();
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            data : "StaffId=" + StaffId,
            complete : function() { },
            success: function(data) {
                data=jQuery(data).find('div#cabinetlistdiv');

                jQuery('#cabinet').show();
                jQuery('#loading-staff').hide();
                jQuery('#cabinet').html(data);
                jQuery('#cabinetlist').bind('change',function() { cabinetChanged() });
            }
        });
    } else {
        jQuery('#cabinet').hide();
    }
}

function cabinetChanged()
{
    var cabinetlist = jQuery("select#cabinetlist").val();
    if(cabinetlist > 0) {
        jQuery('.secondModal_button').show();
    }
}

function ExistingUserBtn() {
    jQuery('#new-user-div').hide();
    jQuery('#existing-user-div').show();
    jQuery('#check-email-div-form').show();
}


//on new user button click
function NewUserBtn() {
    jQuery('#new-user-div').show();
    jQuery('#existing-user-div').hide();
    jQuery('#check-email-result-div').hide();
}

//load forth final modal for confirm appointment
function CheckValidation(UserType,e) {

    jQuery('.error').hide();
    var AppDate = jQuery('#AppDate').val();
    var StaffId = jQuery('#StaffId').val();
    var CabinetId = jQuery('#CabinetID').val();
    var StartTime =  jQuery('#StartTime').val();
    var EndTime =  jQuery('#EndTime').val();

    /**
     * new user booking case
     */
    if(UserType == "NewUser") {
        var ClientEmail = jQuery(".client-email").val();
        var ClientFirstName = jQuery(".client-first-name").val();
        var ClientLastName = jQuery(".client-last-name").val();
        var ClientPhone = jQuery(".client-phone").val();
        var ClientOccupation = jQuery(".client-occupation").val();
        var ClientAddress = jQuery(".client-address").val();
        var ClientSi = jQuery(".client-si").val();

        //client first name
        if (ClientFirstName == "") {
            jQuery(".client-first-name").after("<span class='error'>&nbsp;<br><strong><?php _e('First name required.', 'appointzilla'); ?></strong></span>");
            return false;
        }

        //client last name
        if (ClientLastName == "") {
            jQuery(".client-last-name").after("<span class='error'>&nbsp;<br><strong><?php _e('Last name required.', 'appointzilla'); ?></strong></span>");
            return false;
        }

        //client phone
        if (ClientPhone == "") {
            jQuery(".client-phone").after("<span class='error'>&nbsp;<br><strong><?php _e("Phone required", "appointzilla"); ?></strong></span>");
            return false;
        }
        var data = {
            'action':'is_client_exists',
            'ClientFirstName': ClientFirstName,
            'ClientLastName': ClientLastName,
            'ClientPhone': ClientPhone
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (response==1){
                jQuery('#new-user-form-loading-img').hide();
                alert('<?php _e('Such Client Already Exists', 'appointzilla'); ?>');
                return false;
            }
            else{
                var PostData1 = "Action=BookAppointment"+ "&AppDate=" + AppDate + "&StaffId=" + StaffId+ "&CabinetId=" + CabinetId + "&StartTime=" + StartTime+"&EndTime="+EndTime;
                var PostData3 =  "&ClientFirstName=" + ClientFirstName + "&ClientLastName=" + ClientLastName + "&ClientPhone=" + ClientPhone + "&ClientEmail=" + ClientEmail + "&ClientNote=" + ClientSi + "&ClientOccupation=" + ClientOccupation + "&ClientAddress =" + ClientAddress;
                var PostData = PostData1 +  PostData3;

                jQuery('#new-user-form-rebtn-div').hide();
                jQuery('#new-user-form-loading-img').show();
                jQuery.ajax({
                    dataType : 'html',
                    type: 'POST',
                    url : location.href,
                    cache: false,
                    data : PostData,
                    complete : function() {  },
                    success: function(data) {
                        data = jQuery(data).find('div#AppForthModalData');
                        jQuery("#new-user-form-loading-img").hide();
                        jQuery("#check-email-div-form").hide();
                        jQuery("#AppThirdModal").hide();
                        jQuery('#AppForthModalFinal').show();
                        jQuery('#AppForthModalFinal').html(data);
                    }
                });
            }
        });
    }

    /**
     * existing user booking case
     */
    if(UserType == "ExUser") {
        var ClientEmail = jQuery(e).parents('tr').find(".ex-client-email").val();
        var ClientFirstName = jQuery(e).parents('tr').find(".ex-client-name").val();
        var ClientPhone = jQuery(e).parents('tr').find(".ex-client-phone").val();
        var ClientSi = jQuery(e).parents('tr').find(".ex-client-si").val();
        var ClientID = jQuery(e).parents('tr').find(".client-id").val();

        //client first name
        if (ClientFirstName == "") {
            jQuery("#ex-client-first-name").after("<span class='error'>&nbsp;<br><strong><?php _e('First name required.', 'appointzilla'); ?></strong></span>");
            return false;
        }

        //client phone
        if (ClientPhone == "") {
            jQuery("#ex-client-phone").after("<span class='error'>&nbsp;<br><strong><?php _e("Phone required. <br>Only Numbers 1234567890.", "appointzilla"); ?></strong></span>");
            return false;
        }

        var PostData1 = "Action=BookAppointment"+ "&AppDate=" + AppDate + "&StaffId=" + StaffId+ "&CabinetId=" + CabinetId + "&StartTime=" + StartTime+"&EndTime=" + EndTime;
        var PostData2 = "&UserType=" + UserType + "&ClientEmail=" + ClientEmail;
        var PostData3 =  "&ClientFirstName=" + ClientFirstName + "&ClientPhone=" + ClientPhone+'&clientID='+ClientID;
        var PostData = PostData1 + PostData2 + PostData3;
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : PostData,
            complete : function() {  },
            success: function(data) {
                data = jQuery(data).find('div#AppForthModalData');
                jQuery("#new-user-form-loading-img").hide();
                jQuery("#check-email-div-form").hide();
                jQuery("#AppThirdModal").hide();
                jQuery('#AppForthModalFinal').show();
                jQuery('#AppForthModalFinal').html(data);
            }
        });
        jQuery('#ex-user-form-btn-div').hide();
        jQuery('#ex-user-form-loading-img').show();
    }

    jQuery('#formbtndiv').hide();
    jQuery('#sheduling').show();
}

//check existing user
function CheckExistingUser() {
    jQuery(".error").hide();
    var ClientEmail = jQuery("#check-client-email").val();
    //client email
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (ClientEmail == "") {
        jQuery("#check-client-email").after("<span class='error'>&nbsp;<br><strong><?php _e('Email or Name required.', 'appointzilla'); ?></strong></span>");
        return false;
    }

    var PostData = "Action=CheckExistingUser" + "&ClientEmail=" + ClientEmail;
    jQuery("#existing-user-form-btn").hide();
    jQuery("#existing-user-loading-img").show();
    jQuery.ajax({
        dataType : 'html',
        type: 'POST',
        url : location.href,
        cache: false,
        data : PostData,
        complete : function() {  },
        success: function(Data) {
            Data = jQuery(Data).find('div#check-email-result');
            jQuery("#existing-user-loading-img").hide();
            jQuery("#check-user").hide();
            jQuery('#check-email-div-form').hide();
            jQuery("#check-email-result-div").show();
            jQuery("#check-email-result-div").html(Data);
        }
    });
}
</script>

<style type='text/css'>
.error{
    color:#FF0000;
}

#calendar {
    width: auto;
    margin: 4px 4px;;
}
#bkbtndiv{
    margin: 5px;
}
tr th
{
    text-align:left;
}
.inputwidth
{
    width:300px;
}
</style>

<div style=" border:#000000 solid 0px;" >
    <form action="" method="post" name="filter-form">
        <table width="100%" border="0">
            <tr>
                <td width="16%" valign="top" scope="row">&nbsp;</td>
                <td width="11%">&nbsp;</td>
                <td width="14%">&nbsp;</td>
                <td width="21%"></td>
                <td width="10%">&nbsp;</td>
                <td width="28%"><img src="<?php echo plugins_url( '/appointment-calendar-premium/images/green.jpg'); ?>" />&nbsp;<strong><?php _e('Appointments', 'appointzilla'); ?></strong></td>
            </tr>
            <tr>
                <td valign="top" scope="row"><strong><?php _e('Filter By Staff', 'appointzilla'); ?></strong></td>
                <td><strong><?php _e('Filter By Cabinet', 'appointzilla'); ?></strong></td>
                <td colspan="2" align="center"><button name="addappointment" class="btn btn-primary" type="button" id="addappointment">
                        <i class="icon-calendar icon-white"></i> <?php if(isset($AllCalendarSettings['booking_button_text'])) {
                            echo $AllCalendarSettings['booking_button_text'];
                        } else {
                            _e('Add New Appointment', 'appointzilla');
                        }?>
                    </button>
                </td>
                <td>&nbsp;</td>
                <td><img src="<?php echo plugins_url( '/appointment-calendar-premium/images/blue.jpg'); ?>" />&nbsp;<strong><?php _e('Recurring Appointments', 'appointzilla'); ?></strong></td>
            </tr>
            <tr>
                <td scope="row">
                    <select name="bystaff" id="bystaff">
                        <option value="NULL"><?php _e('Select Staff', 'appointzilla'); ?></option>
                        <?php
                        global $wpdb;
                        $StaffTableName = $wpdb->prefix . "ap_staff";
                        $AllStaff = $wpdb->get_results("SELECT * FROM `$StaffTableName`", OBJECT);
                        if($AllStaff) {
                            foreach($AllStaff as $Staff) { ?>
                            <option value="<?php echo $Staff->id; ?>" <?php if($SelectStaff == $Staff->id) echo "selected"; ?>>&nbsp;&nbsp;<?php echo ucwords($Staff->name); ?></option><?php
                            }
                        } ?>
                    </select>
                </td>
                <td><select name="bycabinet" id="bycabinet">
                        <option value="NULL"><?php _e('Select Cabinet', 'appointzilla'); ?></option>
                        <?php
                        global $wpdb;
                        $CabinetTableName = $wpdb->prefix . "ap_cabinets";
                        $AllCabinet = $wpdb->get_results("SELECT * FROM `$CabinetTableName`", OBJECT);
                        if($AllCabinet) {
                            foreach($AllCabinet as $singleCabinet) { ?>
                            <option value="<?php echo $singleCabinet->cabinet_id; ?>" <?php if($SelectCabinet == $singleCabinet->cabinet_id) echo "selected"; ?>>&nbsp;&nbsp;<?php echo ucwords($singleCabinet->cabinet_name); ?></option><?php
                            }
                        } ?>
                    </select></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><img src="<?php echo plugins_url( '/appointment-calendar-premium/images/pink.jpg'); ?>" />&nbsp;<strong><?php _e('Appointments (Default Staff Assigned)', 'appointzilla'); ?></strong></td>
            </tr>
            <tr>
                <td scope="row"></td>
                <td style="text-align:right;"><button name="filterbystaff" id="filterbystaff" class="btn btn-small btn-danger" type="submit"><i class="icon-ok icon-white"></i> <?php _e('Apply', 'appointzilla'); ?></button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><img src="<?php echo plugins_url( '/appointment-calendar-premium/images/red.jpg'); ?>" />&nbsp;<strong><?php _e('Time Off', 'appointzilla'); ?></strong></td>
            </tr>
        </table>
    </form>
</div>


<!---show full-calendar--->
<div id='calendar' style="margin:10px;"></div>

<!---AppFirstModal Start--->
<div id="AppFirstModal" style="display:none;">
    <div class="modal" id="FirstModal" >
    <form action="" method="post" name="firstmodal" id="firstmodal">
        <div class="modal-info">
            <div style="float:right; margin-top:18px; margin-right:40px;">
                <a id="close" ><i class="icon-remove"></i></a>
            </div>
            <div class="alert alert-info">
                <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p><?php _e('Step-1. Select Date & Staff', 'appointzilla'); ?>
            </div>
        </div>

        <div class="modal-body">
            <div id="firdiv" style="float:left;">
                <div id="datepicker"></div>
            </div>

            <div id="secdiv" style="float:right; margin-right:5%; width:40%" >
                <strong><?php _e('Your Appointment Date', 'appointzilla'); ?></strong>
                <input name="appdate" id="appdate" type="text" disabled="disabled" style="width:100%"/>
                <div id="stfflistdiv">
                </div>
                <div id="cabinet"></div>
                <br>
                <div id="loading-staff" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
            </div> <!--end secdiv-->
        </div><!--end modal-body-->
    </form>
    </div>
</div>
<!---AppFirstModal End--->

<!---AppSecondModal Start--->
<div id="AppSecondModal" style="display:none;"></div>
<!---AppSecondModal End--->


<!---AppThirdModal Start--->
<div id="AppThirdModal" style="display:none;"></div>
<!---AppThirdModal End--->


<!---AppForthModal Start--->
<div id="AppForthModalFinal" style="display:none;"></div>
<!---AppThirdModal ENd-->

<!---staff loading ajax return code--->
<?php if (isset($_GET[selectedDate])){?>
    <div id="tempStaffList">
        <?php
        $StaffTable = $wpdb->prefix . "ap_staff";
        $ReturnStaffList = array();
        $dayOfWeek = array('monday', 'tuesday',  'wednesday','thursday', 'friday', 'saturday', 'sunday');
        $StaffList = $wpdb->get_results("SELECT * FROM `$StaffTable`", ARRAY_A);
        foreach($StaffList as $singleStuff) {
            $singleStuff[staff_hours] = unserialize($singleStuff[staff_hours]);
            if (isset($singleStuff[staff_hours][$dayOfWeek[$_GET['dayOfWeek']].'_close'])!==false && $singleStuff[staff_hours][$dayOfWeek[$_GET['dayOfWeek']].'_close']=='no' && $AppointZilla->isCurrentDateActive($singleStuff[id],$_GET[selectedDate])==true){
                $ReturnStaffList[$singleStuff[id]] = $singleStuff[name];
            }
        }
        ?>
        <strong><?php _e('Select Staff:', 'appointzilla'); ?></strong><br>
        <select name='stafflist' id='stafflist' style="width:100%">
            <?php if(count($ReturnStaffList)) {
                echo "<option>".__("Select Staff")."</option>";
                foreach($ReturnStaffList as $staffID=>$singleName) {
                    echo "<option value='".$staffID."'>".ucwords($singleName)."</option>";
                }
            } else {
                echo "<option value='1'>".__("No Staff Assigned")."</option>";
            }?>
        </select>
    </div>
<?php
}
else if(isset($_GET['StaffId']) && !isset($_GET[AppDate])) { ?>
    <!---Loading staff ajax return code--->
    <div id="cabinetlistdiv">
    <?php
    $StaffID = $_GET['StaffId'];
    if($StaffID>0) { ?>
        <strong><?php _e('Select Cabinet:', 'appointzilla'); ?></strong><br>
        <select name='cabinetlist' id='cabinetlist' style="width:100%">
            <?php //get all staff id list by service id

            $CabinetTable = $wpdb->prefix . "ap_cabinets";
            $AllCabinetList = $wpdb->get_results("SELECT `ms_ap_cabinets`.cabinet_id, cabinet_name FROM `$CabinetTable`
LEFT JOIN ms_ap_cabinets_staff on `ms_ap_cabinets`.cabinet_id = ms_ap_cabinets_staff.cabinet_id
 WHERE  ms_ap_cabinets_staff.staff_id = '$StaffID'", ARRAY_A);
            if(count($AllCabinetList)>0) {
                echo "<option value=''>".__("Select Cabinet")."</option>";
                foreach($AllCabinetList as $single_cabinet) {
                    echo "<option value='".$single_cabinet[cabinet_id]."'>&nbsp;&nbsp;".ucwords($single_cabinet[cabinet_name])."</option>";
                }
            } else {
                echo "<option value='1'>".__("No Staff Assigned")."</option>";
            }
            ?>
        </select>
        <br/>
        <button type="button" class="apcal_btn secondModal_button" id="next1" style="display: none;" name="next1" onclick="LoadSecondModal()"><?php _e('Next', 'appointzilla'); ?> <i class="icon-arrow-right"></i></button>
        </div>
    <?php }
    else{?>
        <strong><?php _e('No Staff Assigned:', 'appointzilla'); ?></strong>
    <?php }?>
    <br>
    <button type="button" class="apcal_btn secondModal_button" id="next1" style="display: none;" name="next1" onclick="LoadSecondModal()"><?php _e('Next', 'appointzilla'); ?> <i class="icon-arrow-right"></i></button>
    <div id="loading1" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url("images/loading.gif", __FILE__); ?>" />
<?php
}?>
<!---loading second modal form ajax return code--->
<?php if( isset($_GET['AppDate']) && isset($_GET['StaffId'])  && !isset($_GET['StartTime'])) { ?>
        <?php require_once('includes/shortcode-time-slot-calculation.php'); ?>
<?php } ?>


<!---loading third modal form ajax return code--->
<?php if( isset($_GET['StartTime']) && isset($_GET['StaffId']) ) {
    include_once('view/calendar_user_selection.php');
    ?>
   <?php
} ?>


<!---saving appointments--->
<?php if( isset($_POST['Action']) ) {
    $Action = $_POST['Action'];
    if($Action == "BookAppointment") {
        $StaffId = $_POST['StaffId'];
        $AppDateNo = $_POST['AppDate'];
        $cabinetID = $_POST['CabinetId'];
        $clientID = $_POST['clientID'];

        $ClientEmail = $_POST['ClientEmail'];
        $ClientNote = $_POST['ClientNote'];
        $ClientName = sanitize_text_field($_POST['ClientFirstName']).' '.sanitize_text_field($_POST['ClientLastName']);
        $ClientPhone = $_POST['ClientPhone'];
        $ClientAddress = $_POST['ClientAddress'];
        $ClientOccupation = $_POST['ClientOccupation'];
        $AppointmentKey = md5(date("F j, Y, g:i a"));
        $AppDate = date("Y-m-d", strtotime($AppDateNo));
        $StartTime = $_POST['StartTime'];
        $EndTime = $_POST['EndTime'];

        //get cabinet name
        $cabinetTableName = $wpdb->prefix . "ap_cabinets";
        $cabinetRow = $wpdb->get_row("SELECT * FROM `$cabinetTableName` WHERE `cabinet_id` = '$cabinetID' ",ARRAY_A);

        if(isset($AllCalendarSettings['apcal_new_appointment_status'])) {
            $Status = $AllCalendarSettings['apcal_new_appointment_status'];
        } else {
            $Status = "pending";
        }
        $AppointmentBy = "user";
        $Recurring = "no";
        $RecurringType = "none";
        $RecurringStartDate = $AppDate;
        $RecurringEndDate = $AppDate;
        $PaymentStatus = "unpaid";

        global $wpdb;
        $AppointmentsTable = $wpdb->prefix ."ap_appointments";
        $CreateAppointments = "INSERT INTO `$AppointmentsTable` (`id` ,`name` ,`email` ,`service_id` ,`staff_id` ,`cabinet_id` ,`phone` ,`start_time` ,`end_time` ,`date` ,`note` , `appointment_key` ,`status` ,`recurring` ,`recurring_type` ,`recurring_st_date` ,`recurring_ed_date` ,`appointment_by`, `payment_status`) VALUES ('NULL', '$ClientName', '$ClientEmail', '0', '$StaffId','$cabinetID', '$ClientPhone', '$StartTime', '$EndTime', '$AppDate', '$ClientNote', '$AppointmentKey', '$Status', '$Recurring', '$RecurringType', '$RecurringStartDate', '$RecurringEndDate', '$AppointmentBy', '$PaymentStatus');";

        if($wpdb->query($CreateAppointments)) {
            $LastAppointmentId = mysql_insert_id(); ?>
            <div id="AppForthModalData">
            <?php global $wpdb;


            $ClientTable = $wpdb->prefix."ap_clients";
            $ExistClientDetails = $wpdb->get_row("SELECT * FROM `$ClientTable` WHERE `email` = '$ClientEmail' OR (`name` like '%$ClientName%' AND `phone` like '$ClientPhone')");
            if(count($ExistClientDetails)) {
                // update  exiting client deatils
                $ExistClientId = $ExistClientDetails->id;
                $LastClientId = $ExistClientId;
            } else {
                // insert new client deatils
                $InsertClient = "INSERT INTO `$ClientTable` (`id` ,`name` ,`email` ,`phone`,address,occupation ,`note`) VALUES ('NULL', '$ClientName', '$ClientEmail', '$ClientPhone','$ClientAddress', '$ClientOccupation', '$ClientNote');";
                if($wpdb->query($InsertClient)) {
                    $LastClientId = mysql_insert_id();
                }
            }
            $wpdb->update(
                $AppointmentsTable,
                array('client_id'=>$LastClientId),
                array(id=>$LastAppointmentId)
            );

            ?>


            <div class="modal" id="AppForthModal" style="z-index:10000;">
            <div class="modal-info">
                <div style="float:right; margin-top:5px; margin-right:10px;"></div>
                <div class="alert alert-info ">
                    <p><?php _e('Thank You. Your appointment has been scheduled.', 'appointzilla'); ?></p>
                </div><!--end modal-info-->
                <div class="modal-body">
                    <style>
                        .table th, .table td {
                            padding: 4px;;
                        }
                    </style>
                    <strong><?php _e('Your Appointment Details', 'appointzilla'); ?></strong>
                    <input type="hidden" id="appid" name="appid" value="<?php echo $LastAppointmentId; ?>" />
                    <table width="100%" class="table">
                        <tr>
                            <th width="26%" scope="row"><?php _e('Name', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo ucwords($ClientName);  ?></td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo $ClientEmail; ?></td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo $ClientPhone;  ?></td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Staff', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%">
                                <?php $StaffTableName = $wpdb->prefix . "ap_staff";
                                $StaffName = $wpdb->get_row("SELECT `name` FROM `$StaffTableName` WHERE `id` = '$StaffId' ");
                                echo ucwords($StaffName->name); ?>
                            </td>
                        </tr><tr>
                            <th width="26%" scope="row"><?php _e('Cabinet', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%">
                                <?php echo $cabinetRow['cabinet_name']; ?>
                            </td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Date', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo date($DateFormat, strtotime($AppDate));	?></td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Time', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo date($TimeFormat, strtotime($StartTime))." - ".date($TimeFormat, strtotime($EndTime));  ?></td>
                        </tr>
                        <tr>
                            <th width="26%" scope="row"><?php _e('Status', 'appointzilla'); ?></th>
                            <td width="1%"><strong>:</strong></td>
                            <td width="73%"><?php echo _e(ucfirst($Status),'appointzilla'); ?></td>
                        </tr>
                </table>
                <div style="float:right;">
                    <button type="button" onclick="CloseModelform()" name="close" id="close" value="Done" class="btn btn-primary"><?php _e('Done', 'appointzilla'); ?></button>
                </div>
            </div>
        </div>
    </div><?php
}
}
}
if(isset($_POST['Action'])) {
    $Action = $_POST['Action'];
    if($Action == "CheckExistingUser") {
        ?><div id="check-email-result">
        <table width="100%" class="table">
            <?php
            $ClientEmail = $_POST['ClientEmail'];
            $client_table = $wpdb->prefix . "ap_clients";
            //Search Client
            $client_list = $wpdb->get_results("SELECT * FROM `$client_table` WHERE `name` LIKE '%$ClientEmail%' OR email LIKE '$ClientEmail'",ARRAY_A);
            if (count($client_list)>=1){?>
                <tr>
                    <th><?php _e('Name', 'appointzilla'); ?></th>
                    <th><?php _e('Phone', 'appointzilla'); ?></th>
                    <th><?php _e('Email', 'appointzilla'); ?></th>
                    <th><?php _e('Occupation', 'appointzilla'); ?></th>
                    <th><?php _e('Action', 'appointzilla'); ?></th>
                </tr>
                <?php foreach($client_list as $single_client):?>
                    <tr>
                        <td>
                            <input type="hidden" class="client-id" name="client-id" value="<?php echo $single_client['id']; ?>">
                            <input name="ex-client-name" type="text" class="ex-client-name" style="height:30px;" value="<?php echo $single_client['name']; ?>" readonly="" /></td>
                        <td><input name="ex-client-phone" type="text" class="ex-client-phone" style="height:30px; width:150px" value="<?php echo $single_client['phone']; ?>" readonly="" /></td>
                        <td><input name="ex-client-email" type="text" class="ex-client-email" style="height:30px;width:150px" value="<?php echo $single_client['email']; ?>" readonly="" /></td>
                        <td><input name="ex-client-occupation" type="text" class="ex-client-occupation" style="height:30px;" value="<?php echo $single_client['occupation']; ?>" readonly="" /></td>
                        <td>
                            <div id="ex-user-form-btn-div">
                                <button type="button" class="apcal_btn btn apcal_btn-success" id="ex-book-now" name="ex-book-now" onclick="return CheckValidation('ExUser',this);"><i class="icon-ok icon-white"></i> <?php _e('Book Now', 'appointzilla'); ?></button>
                                <button type="button" class="apcal_btn btn apcal_btn-danger" id="ex-cancel-app" name="ex-cancel-app" onclick="return TryAgainBooking();"><i class="icon-remove icon-white"></i> <?php _e('Cancel', 'appointzilla'); ?></button>
                            </div>
                            <div id="ex-user-form-loading-img" style="display:none;"><?php _e('Scheduling appointment, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                            <div id="ex-canceling-img" style="display:none;"><?php _e('Refreshing, Please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <?php
            } else { ?>
                <tr>
                    <td colspan="3">
                        <?php  _e("Sorry! No record found.","appointzilla"); ?>
                        <button type="button" onclick="return TryAgainBooking();" class="apcal_btn apcal_btn-danger btn "><i class="fa fa-mail-reply"></i> Try Again</button>
                    </td>
                </tr>
            <?php
            }?>
        </table></div><?php
    }
}?>
<?php // short-code file with button and big calendar(full-calendar)

add_shortcode( 'APCAL', 'appointment_calendar_shortcode' );
function appointment_calendar_shortcode() {
    //ob_start();
    if(get_locale()) {
        $language = get_locale();
        if($language) { define('L_LANG',$language); }
    }

    $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings'));
    if($AllCalendarSettings['calendar_start_day'] != '') {
        $CalStartDay = $AllCalendarSettings['calendar_start_day'];
    } else {
        $CalStartDay = 1;
    }

    //disable closed business days on small datepicker
    global $wpdb;
    $ClosedDays = array();

    require_once( plugin_dir_path( __FILE__ ) . 'calendar/tc_calendar.php');
    // include appointzilla class file
    require_once('appointzilla-class.php');
    require_once(plugin_dir_path( __FILE__ ) . 'menu-pages/includes/AppointmentController.php');

    $AppointZilla = new Appointzilla();
    $CurrentDate = date("Y-m-d", time());

    $DatePicker2 = plugins_url('calendar/', __FILE__);
    $myCalendar = new tc_calendar("date1");
    foreach($ClosedDays as $Day) {
        $myCalendar->disabledDay("$Day");
    }
    $myCalendar->startDate($CalStartDay);
    $myCalendar->setIcon($DatePicker2."images/iconCalendar.gif");
    $myCalendar->setDate(date("d"), date("m"), date("Y"));
    $myCalendar->setPath($DatePicker2);
    $myCalendar->setYearInterval(2035,date('Y'));
    $StartCalendarFrom = date("Y-m-d", strtotime("-1 day", strtotime($CurrentDate)));
    $myCalendar->dateAllow($StartCalendarFrom, "2035-01-01", false);;
    $myCalendar->setOnChange("myChanged()");

    $DateFormat = get_option('apcal_date_format');
    if($DateFormat == '') $DateFormat = "d-m-Y";
    $TimeFormat = get_option('apcal_time_format');
    if($TimeFormat == '') $TimeFormat = "H:i";

    global $wpdb;
    $AppointmentTableName = $wpdb->prefix . "ap_appointments";
    $client_table = $wpdb->prefix . "ap_clients";
    $staff_table = $wpdb->prefix . "ap_staff";
    $EventTableName = $wpdb->prefix."ap_events";

    $current_month_first_date = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
    $laod_recurring_from = date("Y-m-d", strtotime("-3 month", strtotime($current_month_first_date)));  //only for recurring app

    //fetch all normal appointments
    $filterQuery = '';
    $filterCabinetID ='';
    if (isset($_POST[bycabinet]) && $_POST[bycabinet]>0){
        $filterQuery = "AND cabinet_id = $_POST[bycabinet]";
        $filterCabinetID = $_POST[bycabinet];
    }
    $FetchAllApps_sql = "select $client_table.`name` as name, $staff_table.`name` as staff_name, `start_time`, `end_time`, `date` FROM `$AppointmentTableName`
        INNER JOIN $client_table on $client_table.id = $AppointmentTableName.client_id
        INNER JOIN $staff_table on $staff_table.id = $AppointmentTableName.staff_id
    WHERE (`recurring` = 'no' OR recurring='') $filterQuery AND `date` >= '$current_month_first_date' AND `status` != 'cancelled'";
    //fetch all recurring appointments
    $FetchAllRApps_sql = "select * FROM `$AppointmentTableName` WHERE `recurring` = 'yes' AND `date` >= '$current_month_first_date' AND `recurring_st_date` >= '$laod_recurring_from' AND `status` != 'cancelled'";

    //fetch all normal events
    $FetchAllEvent_sql = "select `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` where `repeat` = 'N' AND `start_date` >= '$current_month_first_date' ";

    //fetch all recurring events
    $FetchAllREvent_sql = "select `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` where `repeat` != 'N' AND `start_date` >= '$laod_recurring_from' ";

    if($DateFormat == 'd-m-Y') $CalFormat = 'dd';
    if($DateFormat == 'm-d-Y') $CalFormat = 'dd';
    if($DateFormat == 'Y-m-d') $CalFormat = 'dd'; //coz yy-mm-dd not parsing in a correct date

    //Set Colors: Get Business Hours and open & close days
    $BusinessHoursTable = $wpdb->prefix . "ap_business_hours";
    $AllBusinessHours = $wpdb->get_results("SELECT * FROM `$BusinessHoursTable`");

    $TodayColor = ""; //"#FFFFCC";
    $HeaderColor = ""; //"#E3E3E3";
    $OpenDayColor  = ""; //"#72FE95";
    $CloseDayColor = ""; //"#FF4848";

    $MonColor = $OpenDayColor;
    $TueColor = $OpenDayColor;
    $WedColor = $OpenDayColor;
    $ThrColor = $OpenDayColor;
    $FriColor = $OpenDayColor;
    $SatColor = $OpenDayColor;
    $SunColor = $OpenDayColor;
    foreach($AllBusinessHours as $TodayHours) {
        if($TodayHours->id == 1 & $TodayHours->close == 'yes') { $MonColor = $CloseDayColor; } else { $MonColor = "";}
        if($TodayHours->id == 2 & $TodayHours->close == 'yes') { $TueColor = $CloseDayColor; } else { $TueColor = "";}
        if($TodayHours->id == 3 & $TodayHours->close == 'yes') { $WedColor = $CloseDayColor; } else { $WedColor = "";}
        if($TodayHours->id == 4 & $TodayHours->close == 'yes') { $ThrColor = $CloseDayColor; } else { $ThrColor = "";}
        if($TodayHours->id == 5 & $TodayHours->close == 'yes') { $FriColor = $CloseDayColor; } else { $FriColor = "";}
        if($TodayHours->id == 6 & $TodayHours->close == 'yes') { $SatColor = $CloseDayColor; } else { $SatColor = "";}
        if($TodayHours->id == 7 & $TodayHours->close == 'yes') { $SunColor = $CloseDayColor; } else { $SunColor = "";}
    } ?>

    <style>
     .fc-mon {
        background-color: <?php echo $MonColor; ?>;
     }
     .fc-tue {
        background-color: <?php echo $TueColor; ?>;
     }
     .fc-wed {
        background-color: <?php echo $WedColor; ?>;
     }
     .fc-thu {
        background-color: <?php echo $ThrColor; ?>;
     }
     .fc-fri {
        background-color: <?php echo $FriColor; ?>;
     }
     .fc-sat {
        background-color: <?php echo $SatColor; ?>;
     }
     .fc-sun {
        background-color: <?php echo $SunColor; ?>;
     }
     .fc-today
     {
        background-color: <?php echo $TodayColor; ?>;
     }
     .fc-widget-header{
        background-color:<?php echo $HeaderColor; ?>;
     }

     /* .fc-other-month .fc-day-number { display:none;} */

     .selected {
         outline:1px solid #FF0000; /* Firefox, Opera, Chrome, IE8+ */
         background-color:#FFFF99;
     }

     .error{
         color: #FF0000;
     }

         /*first modal- 2nd div conflicts css*/
     .entry form {
         text-align: left;
     }
    </style>

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
            firstDay: <?php echo $CalStartDay;  ?>,
            slotMinutes: <?php if($AllCalendarSettings['calendar_slot_time'] != '') echo $AllCalendarSettings['calendar_slot_time']; else echo "15"; ?>,
            defaultView: '<?php if($AllCalendarSettings['calendar_view'] != '') echo $AllCalendarSettings['calendar_view']; else echo "month"; ?>',
            minTime: <?php if($AllCalendarSettings['day_start_time'] != '') echo date("G", strtotime($AllCalendarSettings['day_start_time'])); else echo "8"; ?>,
            maxTime: <?php  if($AllCalendarSettings['day_end_time'] != '') echo date("G", strtotime($AllCalendarSettings['day_end_time'])); else echo "20"; ?>,
            monthNames: ["<?php _e("January", "appointzilla"); ?>","<?php _e("February", "appointzilla"); ?>","<?php _e("March", "appointzilla"); ?>","<?php _e("April", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("June", "appointzilla"); ?>","<?php _e("July", "appointzilla"); ?>", "<?php _e("August", "appointzilla"); ?>", "<?php _e("September", "appointzilla"); ?>", "<?php _e("October", "appointzilla"); ?>", "<?php _e("November", "appointzilla"); ?>", "<?php _e("December", "appointzilla"); ?>" ],
            monthNamesShort: ["<?php _e("Jan", "appointzilla"); ?>","<?php _e("Feb", "appointzilla"); ?>","<?php _e("Mar", "appointzilla"); ?>","<?php _e("Apr", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("Jun", "appointzilla"); ?>","<?php _e("Jul", "appointzilla"); ?>","<?php _e("Aug", "appointzilla"); ?>","<?php _e("Sept", "appointzilla"); ?>","<?php _e("Oct", "appointzilla"); ?>","<?php _e("nov", "appointzilla"); ?>","<?php _e("Dec", "appointzilla"); ?>"],
            dayNames: ["<?php _e("Sunday", "appointzilla"); ?>","<?php _e("Monday", "appointzilla"); ?>","<?php _e("Tuesday", "appointzilla"); ?>","<?php _e("Wednesday", "appointzilla"); ?>","<?php _e("Thursday", "appointzilla"); ?>","<?php _e("Friday", "appointzilla"); ?>","<?php _e("Saturday", "appointzilla"); ?>"],
            dayNamesShort: ["<?php _e("Sun", "appointzilla"); ?>","<?php _e("Mon", "appointzilla"); ?>", "<?php _e("Tue", "appointzilla"); ?>", "<?php _e("Wed", "appointzilla"); ?>", "<?php _e("Thus", "appointzilla"); ?>", "<?php _e("Fri", "appointzilla"); ?>", "<?php _e("Sat", "appointzilla"); ?>"],
            buttonText: {
                today: "<?php _e("Today", "appointzilla"); ?>",
                day: "<?php _e("Day", "appointzilla"); ?>",
                week:"<?php _e("Week", "appointzilla"); ?>",
                month:"<?php _e("Month", "appointzilla"); ?>"
            }, <?php
            if($DateFormat == 'd-m-Y') $DPFormat = 'dd-mm-yy';
            if($DateFormat == 'm-d-Y') $DPFormat = 'mm-dd-yy';
            if($DateFormat == 'Y-m-d') $DPFormat = 'yy-mm-dd'; //coz yy-mm-dd not parsing in a correct date ?>
            selectable: true,
            selectHelper: false,
            select: function(start, end, allDay) {

                    var appdate = jQuery.datepicker.formatDate('<?php echo $DPFormat; ?>', new Date(start));
                    var appdate2 = jQuery.datepicker.formatDate('dd-mm-yy', new Date(start));
                    var check = jQuery.fullCalendar.formatDate(start,'yyyy-MM-dd');
                    var today = jQuery.fullCalendar.formatDate(new Date(),'yyyy-MM-dd');
                    if(check < today) {
                        // Its a past date
                        alert("<?php _e("Sorry! Appointment cannot be booked for past dates.", "appointzilla"); ?>");
                    } else {
                        // Its a right date
                        jQuery('#appdate').val(appdate);
                        jQuery('#appdate2').val(appdate2);
                        jQuery('#AppFirstModal').show();
                        jQuery("#datepicker").datepicker("setDate", appdate);
                        var dayOfWeek = jQuery('#datepicker').datepicker('getDate').getUTCDay();
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
                        // date-picker tweaks
                        var i;
                        var startdate = jQuery.datepicker.formatDate('yymm', new Date(start));
                        for(i=1; i<=31; i++) {
                            if(i < 10) i = '0' + i;
                            var nextdate = startdate + i;
                            jQuery('#date1_frame').contents().find('#' + nextdate).removeClass('today select');
                        }
                        var todaydate = jQuery.datepicker.formatDate('yymmdd', new Date());
                        jQuery('#date1_frame').contents().find('#' + todaydate).removeClass('select');
                        var cnvtdate = jQuery.datepicker.formatDate('yymmdd', new Date(start));
                        jQuery('#date1_frame').contents().find('#' + cnvtdate).addClass('today select');
                    }
                },

            events: [
            <?php //Loading Normal Appointments On Calendar Start
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
                        $date = str_replace("-",", ", $date); ?>
                        {
                            title: "<?php echo $single->name; ?>",
                            start: new Date(<?php echo "$date, $start"; ?>),
                            end: new Date(<?php echo "$date, $end"; ?>),
                            allDay: false,
                            backgroundColor : '#1FCB4A',
                            textColor: 'black'
                        }, <?php
                    }
                }
                //Loading Appointments On Calendar End

                //Loading Recurring Appointments On Calendar Start
                $AllRecurringAppointments = $wpdb->get_results($FetchAllRApps_sql, OBJECT);
                if($AllRecurringAppointments) {
                    foreach($AllRecurringAppointments as $single) {
                        if($single->recurring_type != 'monthly') {
                            $start_time = date("H, i", strtotime($single->start_time));
                            $end_time= date("H, i", strtotime($single->end_time));
                            $start_date = $single->recurring_st_date;
                            $end_date =  $single->recurring_ed_date;

                            //if appointment type then calculate RTC(recutting date calulation)
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

                                // subtract 1 from month digit coz calendar work on month 0-11
                                $y = date ( 'Y' , strtotime( $currentDateStr ) );
                                $m = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                                $d = date ( 'd' , strtotime( $currentDateStr ) );
                                $eachdate = "$y-$m-$d";

                                //change format
                                $eachdate = str_replace("-",", ", $eachdate); ?>
                                {
                                    title: "<?php _e('Booked', 'appointzilla'); ?>",
                                    start: new Date(<?php echo "$eachdate, $start_time"; ?>),
                                    end: new Date(<?php echo "$eachdate, $end_time"; ?>),
                                    allDay: false,
                                    backgroundColor : "<?php if($single->staff_id =='1') echo "#DD75DD"; else echo "#6A6AFF"; ?>",
                                    textColor: ""
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
                                        title: "<?php _e('Booked', 'appointzilla'); ?>",
                                        start: new Date(<?php echo "$start_date2, $start_time"; ?>),
                                        end: new Date(<?php echo "$end_date2, $end_time"; ?>),
                                        allDay: false,
                                        backgroundColor : "<?php if($single->staff_id =='1') echo "#DD75DD"; else echo "#6A6AFF"; ?>",
                                        textColor: ""
                                    }, <?php
                                    $i = $i+1;
                                } while(strtotime($end_date) != strtotime($NextDate));
                        }// end of else

                    } // end of fetching single appointment foreach
                } // end of if
                //Loading Recurring Appointments On Calendar End


                //Loading Events On Calendar Start
                $AllEvents = $wpdb->get_results($FetchAllEvent_sql, OBJECT);
                if($AllEvents) {
                    foreach($AllEvents as $Event) {
                        //convert time foramt H:i:s
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
                        $startdate = str_replace("-",", ", $startdate); //changing date format

                        $enddate = $Event->end_date;
                        // subtract 1 from $startdate month digit coz calendar work on month 0-11
                        $y2 = date ( 'Y' , strtotime( $enddate ) );
                        $m2 = date ( 'n' , strtotime( $enddate ) ) - 1;
                        $d2 = date ( 'd' , strtotime( $enddate ) );
                        $enddate = "$y2-$m2-$d2";
                        //changing date format
                        $enddate = str_replace("-",", ", $enddate); ?>
                        {
                            title: "<?php echo ucwords($Event->name); ?>",
                            start: new Date(<?php echo "$startdate, $starttime"; ?>),
                            end: new Date(<?php echo "$enddate, $endtime"; ?>),
                            allDay: false,
                            backgroundColor : "#FF7575",
                            textColor: "black"
                        }, <?php
                    }
                }
                //Loading Events On Calendar End

                //Loading Recurring Events On Calendar Start
                $AllREvents = $wpdb->get_results($FetchAllREvent_sql, OBJECT);
                //don't show event on filtering
                if($AllREvents)	{
                    foreach($AllREvents as $Event) {
                        //convert time foramt H:i:s
                        $starttime = date("H:i", strtotime($Event->start_time));
                        $endtime = date("H:i", strtotime($Event->end_time));
                        //change time format according to calendar
                        $starttime = str_replace(":",", ", $starttime);
                        $endtime = str_replace(":", ", ", $endtime);
                        $startdate = $Event->start_date;
                        $enddate = $Event->end_date;

                        if($Event->repeat != 'M') {
                            //if event type then calculate RTC(recutting date calulation)
                            if($Event->repeat == 'PD')
                            $RDC = 1;
                            if($Event->repeat == 'D')
                            $RDC = 1;
                            if($Event->repeat == 'W')
                            $RDC = 7;
                            if($Event->repeat == 'BW')
                            $RDC = 14;

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
                                $startdate = str_replace("-",", ", $startdate); //changing date format

                                // subtract 1 from $startdate month digit coz calendar work on month 0-11
                                $y2 = date ( 'Y' , strtotime( $currentDateStr ) );
                                $m2 = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                                $d2 = date ( 'd' , strtotime( $currentDateStr ) );
                                $enddate = "$y2-$m2-$d2";

                                $enddate = str_replace("-",", ", $enddate);     //changing date format
                                ?>
                                {
                                    title: "<?php echo ucwords($Event->name); ?>",
                                    start: new Date(<?php echo "$startdate, $starttime"; ?>),
                                    end: new Date(<?php echo "$enddate, $endtime"; ?>),
                                    allDay: false,
                                    backgroundColor : "#FF7575",
                                    textColor: "black"
                                }, <?php
                            }
                        } else {
                            $i = 0;
                            do {
                                $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($startdate)));
                                // subtract 1 from $startdate month digit coz calendar work on month 0-11
                                $y = date ( 'Y' , strtotime( $NextDate ) );
                                $m = date ( 'n' , strtotime( $NextDate ) ) - 1;
                                $d = date ( 'd' , strtotime( $NextDate ) );
                                $startdate2 = "$y-$m-$d";
                                $startdate2 = str_replace("-",", ", $startdate2);   //changing date format
                                $enddate2 = str_replace("-",", ", $startdate2); ?>
                                    {
                                        title: "<?php echo ucwords($Event->name); ?>",
                                        start: new Date(<?php echo "$startdate2, $starttime"; ?>),
                                        end: new Date(<?php echo "$enddate2, $endtime"; ?>),
                                        allDay: false,
                                        backgroundColor : "#FF7575",
                                        textColor: "black"
                                    }, <?php
                                $i = $i+1;
                            } while(strtotime($enddate) != strtotime($NextDate));
                        }// enf of else
                    }// end of foreach
                }// end of if check
                //Loading Recurring Events On Calendar End
                ?>
                {
                }
                ]
        });

        jQuery("#datepicker").datepicker({
            inline: true,
            minDate: 0,
            altField: '#alternate',
            dateFormat:'dd-mm-yy',
            dayNames:['Пон','Вт','Ср',"Чтв",'Пят','Суб','Вос'],
            firstDay: <?php if($AllCalendarSettings['calendar_start_day']) echo $AllCalendarSettings['calendar_start_day']; else echo "0";  ?>,
            beforeShowDay: function(date) {
                var day = date.getDay();
                return [(day != 0), ''];
            },
            onSelect: function(dateText, inst) {
                var dayOfWeek = jQuery(this).datepicker('getDate').getUTCDay();
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


        //Modal Form Works
        //show first modal
        jQuery('#addappointment').click(function(){
            jQuery('#AppFirstModal').show();
        });
        //hide modal
        jQuery('#close').click(function(){
            jQuery('#AppFirstModal').hide();
        });

        <?php if($DateFormat == 'd-m-Y') $DPFormat = 'dd-mm-yy';
        if($DateFormat == 'm-d-Y') $DPFormat = 'mm-dd-yy';
        if($DateFormat == 'Y-m-d') $DPFormat = 'yy-mm-dd'; //coz yy-mm-dd not parsing in a correct date ?>

        //AppFirstModal Validation
        jQuery('#next1').click(function(){
            jQuery(".error").hide();
            if(jQuery('#stafflist').val() == 0)
            {
                jQuery("#stafflist").after("<span class='error'><br><strong><?php _e('Select Staff.', 'appointzilla'); ?></strong><br></span>");
                return false;
            }
        });

        //back button show first modal
        jQuery('#back').click(function(){
            jQuery('#AppFirstModal').show();
            jQuery('#AppSecondModal').hide();
        });
        jQuery('#bycabinet').change(function(){
            jQuery('#filter_form').submit();
        });

    });

   jQuery.datepicker.formatDate("<?php echo $DPFormat; ?>", new Date());
    //Modal Form Works
    function LoadSecondModal() {
        var AppDate = jQuery('#appdate').val();
        var StaffId = jQuery('#stafflist').val();
        var CabinetId = jQuery('#cabinetlist').val();
        var SecondData = "AppDate=" + AppDate + "&StaffId=" + StaffId+"&CabinetID=" + CabinetId;
        jQuery('#loading1').show(); // loading button onclick next1 at first modal
        jQuery('#next1').hide();    //hide next button
        jQuery('#stfflistdiv').html('');
        jQuery('#cabinet').html('');
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
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

    //load first modal on click back1
    function LoadFirstModal() {
        jQuery('#AppSecondModal').hide()
        jQuery('#AppFirstModal').show();
        jQuery('#next1').show();
        jQuery('#stfflistdiv').html('');
        jQuery('#cabinet').html('');
    }

    //load second modal on back2 click
    function LoadSecondModal2() {
        jQuery('#AppThirdModal').hide();
        jQuery('#buttondiv').show();
        jQuery('#AppSecondModal').show();
    }

   //load third modal on-click next2
   function LoadThirdModal() {
       //validation on second modal form
       jQuery('.error').hide();
       var AppDate = jQuery('#AppDate').val();
       var StaffId = jQuery('#StaffId').val();
       var Start_Time = jQuery('input[name=start_time]:radio:checked').val();
       if(!Start_Time) {
           jQuery("#time_slot_box").after("<span style='width:auto; margin-left:5%;' class='error'><strong><?php _e('Select any time.', 'appointzilla'); ?></strong></span>");
           return false;
       }
       var ThirdData = "AppDate=" + AppDate + "&StaffId=" + StaffId + "&CabinetID=" + jQuery('#CabinetID').val() + "&StartTime=" + Start_Time ;
       jQuery('#buttondiv').hide();
       jQuery('#loading').show();
       jQuery.ajax({
           dataType : 'html',
           type: 'GET',
           url : location.href,
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


    //on existing user button click
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

           var PostData1 = "Action=BookAppointment"+ "&AppDate=" + AppDate + "&StaffId=" + StaffId+ "&CabinetId=" + CabinetId + "&StartTime=" + StartTime+"&EndTime="+EndTime;
           var PostData3 =  "&ClientFirstName=" + ClientFirstName + "&ClientLastName=" + ClientLastName + "&ClientPhone=" + ClientPhone + "&ClientEmail=" + ClientEmail + "&ClientNote=" + ClientSi + "&ClientOccupation=" + ClientOccupation + "&ClientAddress =" + ClientAddress;
           var PostData = PostData1 +  PostData3;

           jQuery('#new-user-form-rebtn-div').hide();
           jQuery('#new-user-form-loading-img').show();
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

           jQuery('#ex-user-form-btn-div').hide();
           jQuery('#ex-user-form-loading-img').show();
       }

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
               jQuery("#AppForthModalFinal").show();
               jQuery("#AppForthModalFinal").html(data);
           }
       });
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

   function CloseModelform() {
       jQuery("#AppForthModalFinal").hide();
       jQuery("#AppSecondModalData").hide();
       jQuery("#AppThirdModalData").hide();
       jQuery("#ex-pay-canceling-img").show();
       location.href = location.href;
   }

   function highlightsradio(timeslotspanid) {
       jQuery('span').removeClass('selected');
       var spanid = "#" + timeslotspanid;
       jQuery(spanid).addClass("selected");
   }

    // failed appointment
    function failedappointment() {
        var appid = jQuery('#appid').val();
        var Datastring = "appstatus=cancel" + "&appid="+ appid;
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : Datastring,
            complete : function() {  },
            success: function(data) {
                jQuery('#AppForthModalFinal').hide();
                jQuery('#AppSecondModalData').hide();
                jQuery('#AppThirdModalData').hide();
                var CurrentUrl = location.href;
                CurrentUrl=CurrentUrl.replace('failed=failed&appointId='+appid, '');
                location.href = CurrentUrl;
            }
        });
    }

    //cancel appointment
    function CancelAppointment() {
        var appid = jQuery('#appid').val();
        var DataString = "appstatus=cancel" + "&appid="+ appid;
        jQuery("#paybuttondiv").hide();
        jQuery("#pay-canceling-img").show();
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : DataString,
            complete : function() {  },
            success: function() {
                jQuery('#AppForthModalFinal').hide();
                jQuery('#AppSecondModalData').hide();
                jQuery('#AppThirdModalData').hide();
                window.location.reload();
            }
        });
    }

    //apply coupon code
    function ApplyCoupon() {
        var CouponCode = jQuery("#coupon-code").val();
        if(CouponCode == "") {
            alert("<?php _e("Enter any coupon code.", "appointzilla"); ?>");
            jQuery("#coupon-code").focus();
            return false;
        } else {
            var PostData = "Action=apply-coupon" + "&CouponCode=" + CouponCode;
            jQuery("#loading-img").show();
            jQuery.ajax({
                dataType : 'html',
                type: 'POST',
                url : location.href,
                cache: false,
                data : PostData,
                complete : function() {  },
                success: function(ReturnedData) {
                    ReturnedData = jQuery(ReturnedData).find("div#coupon-result");
                    jQuery("#loading-img").hide();
                    jQuery("#apply-coupon-div").hide();
                    jQuery("#show-coupon-result").html(ReturnedData);
                    jQuery("#show-coupon-result").show();
                    var CouponCodeValue = jQuery("#coupon-code-div").text();
                    var DicountRateValue = jQuery("#discount-rate-div").text();
                    jQuery("input[name=custom]").val(CouponCodeValue);
                    jQuery("input[name=discount_rate]").val(DicountRateValue);
                }
            });
        }
    }

    //try another coupon code
    function TryAgain() {
        jQuery("#apply-coupon-div").show();
        jQuery("#show-coupon-result").hide();
    }

    //try again booking
    function TryAgainBooking() {
        jQuery("#check-email-result-div").hide();
        jQuery("#check-user").show();
        jQuery('#check-email-div-form').show();
        jQuery("#existing-user-form-btn").show();
    }

    //cancel appointment
    function Canceling() {
        jQuery("#ex-user-form-btn-div").hide();
        jQuery("#ex-canceling-img").show();
        location.reload();
    }

    // on paypal pay button click
    function PayWithPaypal(){
        jQuery('#show-redirecting-msg').show();
        jQuery('#paybuttondiv').hide();
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
    </script>
    <div id="ajax-loading-container">
        <img src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/big-loading.gif');?>"/>
    </div>
    <!---Display Booking Instruction--->
    <?php if($AllCalendarSettings['apcal_booking_instructions']) { ?>
    <div id="bookinginstructions" align="center">
        <?php echo $AllCalendarSettings['apcal_booking_instructions']; ?>
    </div>
    <?php } ?>

    <!---Schedule An Appointment Button--->
    <div id="bkbtndiv" align="center" style="padding:10px;">
        <button name="addappointment" class="apcal_btn apcal_btn-large apcal_btn-primary" type="submit" id="addappointment">
            <strong><i class="icon-calendar icon-white"></i> <?php if(isset($AllCalendarSettings['booking_button_text'])) {
                    echo $AllCalendarSettings['booking_button_text'];
                } else {
                    _e("Schedule New Appointment", 'appointzilla');
                } ?>
            </strong>
        </button>
    </div>
    <form method="post" id="filter_form">
        <select name="bycabinet" id="bycabinet">
            <option value=""><?php _e('All Cabinets', 'appointzilla'); ?></option>
            <?php
            global $wpdb;
            $CabinetTableName = $wpdb->prefix . "ap_cabinets";
            $AllCabinet = $wpdb->get_results("SELECT * FROM `$CabinetTableName`", ARRAY_A);
            if($AllCabinet) {
                foreach($AllCabinet as $singleCabinet) { ?>
                <option value="<?php echo $singleCabinet[cabinet_id]; ?>" <?php if($filterCabinetID == $singleCabinet[cabinet_id]) echo "selected"; ?>>&nbsp;&nbsp;<?php echo ucwords($singleCabinet[cabinet_name]); ?></option><?php
                }
            } ?>
        </select>
    </form>

    <div id='messagebox'>
    </div>
    <!---Show appointment calendar--->
    <div id='calendar'>
    </div>

    <!---AppFirstModal For Schedule New Appointment--->
    <div id="AppFirstModal" style="display:none;">
        <div class="apcal_modal" id="myModal" style="z-index:10000;">
            <form action="" method="post" name="addnewappointment" id="addnewappointment">
                <div class="apcal_modal-info">
                    <div class="apcal_alert apcal_alert-info">
                        <a href="#" style="float:right; margin-right:4px; margin-top:12px;" id="close"><i class="icon-remove"></i></a>
                        <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p>
                        <div><?php _e('Step 1. Select Date & Staff', 'appointzilla'); ?></div>
                    </div>
                </div>

                <div class="apcal_modal-body">
                    <div id="firdiv" style="float:left; height:210px; width:260px; padding-bottom:30px;">
                    <div id="datepicker"></div>
                    </div>

                    <div id="secdiv" style="float:right; margin-right:5%; width:40%" >
                        <strong><?php _e('Your Appointment Date:', 'appointzilla'); ?> </strong><br>
                        <input name="appdate" id="appdate" type="text" readonly="" style="height:30px; width:100%; padding-left: 15px;" value="<?php echo date($DateFormat); ?>" /><br>
                        <input name="appdate2" id="appdate2" type="hidden" readonly="" style="height:30px; width:100%" value="<?php echo date("d-m-Y"); ?>" />
                        <div id="stfflistdiv">
                        </div>
                        <br>
                        <div id="loading-staff" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
					<div id="cabinet"></div>
                    </div><!--#secdiv-->
                </div><!--#modal-body-->
            </form>
      </div>
    </div>
    <!---AppSecondModal For Schedule New Appointment--->

    <!---AppSecondModal For Schedule New Appointment--->
    <div id="AppSecondModal" style="display:none;"></div>
    <!---AppSecondModal For Schedule New Appointment End--->

    <!---AppThirdModal For Schedule New Appointment--->
    <div id="AppThirdModal" style="display:none;"></div>
    <!---AppThirdModal For Schedule New Appointment End--->
    <div id="AppForthModalFinal" style="display:none;"></div>
    <!---AppThirdModal For Schedule New Appointment End--->

    <!--date-picker js -->
    <script src="<?php echo plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__); ?>" type="text/javascript"></script>

    <?php if (isset($_GET[selectedDate])){?>
        <div id="tempStaffList">
        <?php $StaffTable = $wpdb->prefix . "ap_staff";
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
    }

    else if( isset($_GET['AppDate']) && isset($_GET['StaffId']) && !isset($_GET['StartTime'])) {?>
        <!---loading second modal form ajax return code--->
        <?php require_once('shortcode-time-slot-calculation.php');
    }

    else if( isset($_GET['StartTime']) && isset($_GET['StaffId']) ) {
        ?>
        <div id="AppThirdModalData">
            <div class="apcal_modal" id="AppThirdModal" style="z-index:10000;">
                <input name="StaffId" id="StaffId" type="hidden" value="<?php if(isset($_GET['StaffId'])) { echo $_GET['StaffId']; } ?>" />
                <input name="CabinetID" id="CabinetID" type="hidden" value="<?php if(isset($_GET['CabinetID'])) { echo $_GET['CabinetID']; } ?>" />
                <input name="AppDate" id="AppDate" type="hidden" value="<?php if(isset($_GET['AppDate'])) { echo $_GET['AppDate']; } ?>" />
                <input name="StartTime" id="StartTime" type="hidden" value="<?php if(isset($_GET['StartTime'])) { echo $_GET['StartTime']; } ?>" />
                <input name="EndTime" id="EndTime" type="hidden" value="<?php if(isset($_GET['StartTime'])) { echo date($TimeFormat, strtotime($_GET['StartTime'] . "+1 hour")); } ?>" />
                <div class="apcal_modal-info">
                    <a href="javascript:void(0)" onclick="CloseModelform()" style="float:right; margin-right:40px; margin-top:21px;" id="close" ><i class="icon-remove"></i></a>
                    <div class="apcal_alert apcal_alert-info">
                        <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p>
                        <?php _e('Step 3. Complete Your Booking', 'appointzilla'); ?>
                    </div>
                </div>

                <div class="apcal_modal-body">
                    <?php if($AllCalendarSettings['apcal_user_registration'] == "yes") { ?>
                    <!--check user div-->
                    <div id="check-user">
                        <table width="100%" class="table">
                            <tr>
                                <td colspan="3">
                                    <button id="new-user" name="new-user" class="apcal_btn apcal_btn-info" onclick="return NewUserBtn();"><i class="fa fa-user"></i> <?php _e("New User", "appointzilla"); ?></button>
                                    <button id="existing-user" name="existing-user" class="apcal_btn apcal_btn-info" onclick="return ExistingUserBtn();"><i class="fa fa-user"></i> <?php _e("Existing User", "appointzilla"); ?></button>
                                    <button type="button" class="apcal_btn" id="back2" name="back2" onclick="LoadSecondModal2()" style="float: right;"><i class="icon-arrow-left"></i>  <?php _e('Back', 'appointzilla'); ?></button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!--new user div-->
                    <div id="new-user-div" style="display: none;">
                        <table width="100%" class="table">
                            <tr>
                                <th scope="row"><?php _e('First Name', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-first-name" type="text" class="client-first-name" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Last Name', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-last-name" type="text" class="client-last-name" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-phone" type="text" class="client-phone" style="height:30px;"/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-email" type="text" class="client-email" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Occupation', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-occupation" type="text" class="client-occupation" style="height:30px;"  maxlength="14"/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Address', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-address" type="text" class="client-address" style="height:30px;"/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Special Instruction', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><textarea name="client-si" id="client-si"></textarea></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>
                                    <div id="new-user-form-btn-div">
                                        <button type="button" class="apcal_btn apcal_btn-success" id="book-now" name="book-now" onclick="return CheckValidation('NewUser',this)"><i class="icon-ok icon-white"></i>  <?php _e('Book Now', 'appointzilla'); ?></button>
                                    </div>
                                    <div id="new-user-form-loading-img" style="display:none;"><?php _e('Scheduling appointment, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!--existing user div-->
                    <div id="existing-user-div" style="display: none;">

                        <!--div for display existing user search details-->
                        <div id="check-email-div-form" style="display: none;">
                            <table width="100%" class="table">
                                <tr>
                                    <th scope="row"><?php _e('Email or Name', 'appointzilla'); ?></th>
                                    <td><strong>:</strong></td>
                                    <td><input name="check-client-email" type="text" id="check-client-email" style="height:30px;" /></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div id="existing-user-form-btn">
                                            <button type="button" class="apcal_btn apcal_btn-success" id="check-existing-user" name="check-existing-user" onclick="return CheckExistingUser();"><i class="icon-search icon-white"></i> <?php _e('Search', 'appointzilla'); ?></button>
                                        </div>
                                        <div id="existing-user-loading-img" style="display:none;"><?php _e('Searching, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!--div for display existing user search details-->
                        <div id="check-email-result-div" style="display: none;">

                        </div>
                    </div>
                    <?php } else { // end of if registration enable ?>
                    <!--user registration not enable-->
                    <div id="no-user-registration">
                        <table width="100%" class="table">
                            <tr>
                                <th scope="row"><?php _e('First Name', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-first-name" type="text" id="client-first-name" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Last Name', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-last-name" type="text" id="client-last-name" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-phone" type="text" id="client-phone" style="height:30px;"  maxlength="14"/></td>
                            </tr>
							<tr>
                                <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="client-email" type="text" id="client-email" style="height:30px;" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Special Instruction', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><textarea name="client-si" id="client-si"></textarea></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>
                                    <div id="new-user-form-btn-div">
                                        <button type="button" class="apcal_btn" id="back2" name="back2" onclick="LoadSecondModal2()"><i class="icon-arrow-left"></i>  <?php _e('Back', 'appointzilla'); ?></button>
                                        <button type="button" class="apcal_btn apcal_btn-success" id="book-now" name="book-now" onclick="return CheckValidation('NewUser',this)"><i class="icon-ok icon-white"></i>  <?php _e('Book Now', 'appointzilla'); ?></button>
                                    </div>
                                    <div id="new-user-form-loading-img" style="display:none;"><?php _e('Scheduling appointment, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php } ?>
                </div><!--end modal-body-->
            </div>
        </div><?php
    } ?>

    <!---saving appointments--->
    <?php if( isset($_POST['Action'])) {
        $Action = $_POST['Action'];
        if($Action == "BookAppointment") {?>
		<div id="AppForthModalData">
		<div class="apcal_modal" id="AppForthModal" style="z-index:10000;">
			<div class="apcal_modal-info">
		<?php
            $AppointmentController = new AppointmentController();
			$_POST['ClientFirstName'] = sanitize_text_field($_POST['ClientFirstName']).' '.sanitize_text_field($_POST['ClientLastName']);
            $_POST['AppDate'] = date("Y-m-d", strtotime($_POST['AppDate']));

            $newAppointmentID = $AppointmentController->createAppointment($_POST);
            if ($newAppointmentID>0):
                $appointment_table = $wpdb->prefix . "ap_appointments";
                $cabinetTableName = $wpdb->prefix . "ap_cabinets";
                $StaffTableName = $wpdb->prefix . "ap_staff";
                $singleAppointment = $wpdb->get_row("
                    SELECT app.id, app.name, app.email, app.phone, app.start_time, app.end_time, app.note, app.date, $StaffTableName.name AS staff_name, $cabinetTableName.cabinet_name
FROM $appointment_table AS app
INNER JOIN $StaffTableName ON $StaffTableName.id = app.staff_id
INNER JOIN $cabinetTableName ON $cabinetTableName.cabinet_id = app.cabinet_id
WHERE app.id =$newAppointmentID ",ARRAY_A);?>
			<div class="apcal_alert apcal_alert-info">
				<p><?php _e('Thank You. Your appointment has been scheduled.', 'appointzilla'); ?></p>
			</div><!--end modal-info-->
			<div class="apcal_modal-body">
				<style>
					.table th, .table td {
						padding: 4px;;
					}
				</style>
				<strong><?php _e('Your Appointment Details', 'appointzilla'); ?></strong>
				<input type="hidden" id="appid" name="appid" value="<?php echo $singleAppointment[id] ?>" />
				<table width="100%" class="table">
					<tr>
						<th width="26%" scope="row"><?php _e('Name', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo ucwords($singleAppointment[name]);  ?></td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Email', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo $singleAppointment[email]; ?></td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo $singleAppointment[phone];  ?></td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Staff', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%">
							<?php echo ucwords($singleAppointment[staff_name]); ?>
						</td>
					</tr><tr>
						<th width="26%" scope="row"><?php _e('Cabinet', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%">
							<?php echo $singleAppointment['cabinet_name']; ?>
						</td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Date', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo date($DateFormat, strtotime($singleAppointment[date]));	?></td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Time', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo date($TimeFormat, strtotime($singleAppointment[start_time]))." - ".date($TimeFormat, strtotime($singleAppointment[end_time]));  ?></td>
					</tr>
					<tr>
						<th width="26%" scope="row"><?php _e('Status', 'appointzilla'); ?></th>
						<td width="1%"><strong>:</strong></td>
						<td width="73%"><?php echo _e(ucfirst($singleAppointment[status]),'appointzilla'); ?></td>
					</tr>
				</table>
			</div>
                <?php
			else:?>
			<div class="apcal_alert apcal_alert-info">
				<p><?php _e('Sorry. Your appointment has not been scheduled.', 'appointzilla'); ?></p>
			</div>
            <?php endif;?>
			</div>
			</div>
		</div>
		<?php
        }
	}// saving appointment if


    // Payment success
    if( isset($_POST['address_status']) && isset($_POST['payer_id']) ) {
        global $wpdb;
        $AppointmentTableName = $wpdb->prefix . "ap_appointments";
        $PaymentTableName = $wpdb->prefix . 'ap_payment_transaction';
        $ClientTableName = $wpdb->prefix . 'ap_clients';
        $CouponsCodesTable = $wpdb->prefix ."apcal_pre_coupons_codes";
        //print_r($_POST);
        $PaymentDetails = serialize($_POST);
        $appId = $_POST['item_number'];

        //get client id by appointment id
        $ClientEmail = $wpdb->get_row("SELECT `email` FROM `$AppointmentTableName` WHERE `id` = '$appId'");
        $FetchClientEmail = $wpdb->get_row("SELECT `id` FROM `$ClientTableName` WHERE `email` = '$ClientEmail->email'");
        $clientId = $FetchClientEmail->id;

        $amount = $_POST['mc_gross'];
        $date = $_POST['payment_date'];
        $status = $_POST['address_status'];
        $txn_id = $_POST['txn_id'];
        $gateway = 'paypal';

        $wpdb->query("INSERT INTO `$PaymentTableName` (`id`, `app_id`, `client_id`, `ammount`, `date`, `status`, `txn_id`, `gateway`, `other_fields`) VALUES (NULL, '$appId', '$clientId', '$amount', '$date', '$status', '$txn_id', '$gateway', '$PaymentDetails');");
        $AppointmentId = $_POST['item_number'];
        $AppointmentRow = $wpdb->get_row("SELECT * FROM `$AppointmentTableName` WHERE `id` = '$AppointmentId'");
        if(count($AppointmentRow)) {
            $name = $AppointmentRow->name;
            $ServiceId = $AppointmentRow->service_id;
            $StaffId = $AppointmentRow->staff_id;
            $StartTime = $AppointmentRow->start_time;
            $EndTime = $AppointmentRow->end_time;
            $Client_name = $AppointmentRow->name;
            $Client_Email = $AppointmentRow->email;
            $Client_Phone = $AppointmentRow->phone;
            $Client_Note = $AppointmentRow->note;
            $AppDate = $AppointmentRow->date;
            $Status = 'approved';
            $AppointmentKey = $AppointmentRow->appointment_key;

            //update appointment status n payment status
            $PaymentStatus = $_POST['payment_status'];
            $AppointmentRow = $wpdb->query("UPDATE `$AppointmentTableName` SET `status` = '$Status', `payment_status` = 'paid' WHERE `id` = '$AppointmentId' ");

            $BlogName =  get_bloginfo();
            if($AppointmentId && $clientId) {
                $AppId = $AppointmentId;
                $ServiceId = $ServiceId;
                $StaffId = $StaffId;
                $ClientId = $clientId;
                //include notification class
                require_once('menu-pages/includes/notification-class.php');
                $Notification = new Notification();
                $Notification->notifyadmin($Status, $AppId, $ServiceId, $StaffId, $ClientId, $BlogName, $DateFormat, $TimeFormat);
                $Notification->notifyclient($Status, $AppId, $ServiceId, $StaffId, $ClientId, $BlogName, $DateFormat, $TimeFormat);
                if(get_option('staff_notification_status') == "on") {
                    $Notification->notifystaff($Status, $AppId, $ServiceId, $StaffId, $ClientId, $BlogName, $DateFormat, $TimeFormat);
                }
            }

            //if status is approved then sync appointment
            if($Status == 'approved') {

                //add service name with event title($name)
                //$ServiceTable = $wpdb->prefix . "ap_services";
                //$ServiceData = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$ServiceId'");
                //$name = $name."(".$ServiceData->name.")";

                $CalData = get_option('google_caelndar_settings_details');
                if($CalData['google_calendar_client_id'] != '' && $CalData['google_calendar_secret_key']  != '') {
                    $start_time = date("H:i", strtotime($StartTime));
                    $end_time = date("H:i", strtotime($EndTime));
                    $appointmentdate = date("Y-m-d", strtotime($AppDate));
                    $note = strip_tags($Client_Note);

                    $ClientId = $CalData['google_calendar_client_id'];
                    $ClientSecretId = $CalData['google_calendar_secret_key'];
                    $RedirectUri = $CalData['google_calendar_redirect_uri'];
                    require_once('menu-pages/google-appointment-sync-class.php');

                    //global $wpdb;
                    $AppointmentSyncTableName = $wpdb->prefix . "ap_appointment_sync";
                    // insert this appointment event on calendar
                    $GoogleAppointmentSync = new GoogleAppointmentSync($ClientId, $ClientSecretId, $RedirectUri);
                    $tag = "Appointment with: ";
                    $OAuth = $GoogleAppointmentSync->NormalSync($name, $appointmentdate, $start_time, $end_time, $note, $tag);
                    //insert appintment sync details
                    $OAuth = serialize($OAuth);
                    $wpdb->query("INSERT INTO `$AppointmentSyncTableName` ( `id` , `app_id` , `app_sync_details` )
                    VALUES ( NULL , '$AppointmentId', '$OAuth' );");
                } // end of google calendar setting

                //unset payment post variables
                unset($_POST['address_status']); unset($_POST['payer_id']);

            } // end of sync appointment is approved ?>
            <div class="apcal_modal" id="AppForthModal" style="z-index:10000;">
                <div class="apcal_modal-info">
                    <div style="float:right; margin-top:5px; margin-right:10px;">
                        <div align="center"><a href="javascript:void(0)" onclick="CloseModelform()" id="close" ><i class="icon-remove"></i></a></div>
                    </div>
                    <div class="apcal_alert apcal_alert-info">
                        <h4 ><?php _e('Payment Successfully Processed', 'appointzilla'); ?></h4>
                    </div>
                    <div class="apcal_modal-body">
                        <div class="apcal_alert apcal_alert-success">
                            <strong><?php _e('Payment received and your appointment has been confirmed.', 'appointzilla'); ?></strong><br />
                            <strong><?php _e('Thank you for scheduling appointment with us.', 'appointzilla'); ?></strong>
                            <?php
                            //if payment confirmed(done) then increment coupon used count value
                            if($status == "confirmed") {
                                $PaymentDetails = unserialize($PaymentDetails);
                                if(isset($PaymentDetails['custom'])) {
                                    $CouponCode = $PaymentDetails['custom'];
                                    //check coupon exist or not
                                    $CouponsData = $wpdb->get_row("SELECT * FROM `$CouponsCodesTable` WHERE `coupon_code` LIKE '$CouponCode'");
                                    if(count($CouponsData)) {
                                        //increment total used count
                                        $CouponId = $CouponsData->id;
                                        $UsedCount = $CouponsData->used_count + 1;
                                        $wpdb->query("UPDATE `$CouponsCodesTable` SET `used_count` = '$UsedCount' WHERE `id` = '$CouponId' ");
                                    }
                                }
                            }
                            ?>
                        </div>
                        <button type='button' onclick='CloseModelform()' name='close' id='close' value='Done' class='apcal_btn'><i class="icon-ok"></i> <?php _e('Done', 'appointzilla'); ?></button>
                    </div>
                    <div>
                </div>
            </div>
            <?php
        } // end of AppointmentRow
    } // end of Payment success


    // Payment process failed
    if( isset($_GET['failed']) && isset($_GET['appointId'])) { ?>
        <div class="apcal_modal" id="AppForthModal" style="z-index:10000;">
            <div class="apcal_modal-info" style="padding-bottom:20px;">
                <div style="float:right; margin-top:5px; margin-right:10px;">
                    <a href="#" onclick="CloseModelformfailed()" id="close" ><i class="icon-remove"></i></a>
                </div>
                <input type="hidden" name="appid" id="appid" value="<?php echo $_GET['appointId']; ?>" />
                <div class="apcal_alert apcal_alert-info">
                    <h4><?php _e('Payment Failed', 'appointzilla'); ?></h4>
                </div>
                <div style=" margin-left:20px; padding-right:20px;">
                    <div class="apcal_alert apcal_alert-error">
                        <?php _e('Sorry! Appointment booking was not successful.', 'appointzilla'); ?>
                    </div>
                    <button type='button' onclick='return failedappointment();' name='close' id='close' value='close' class='apcal_btn'><i class="icon-repeat"></i> <?php _e('Try Again', 'appointzilla'); ?></button>
                </div>
             </div>
        </div><?php
    }

    // cancel  appointment
    if(isset($_POST['appid']) && isset($_POST['appstatus']) ) {
        $appid = $_POST['appid'];
        global $wpdb;
        $apptabname = $wpdb->prefix."ap_appointments";
        $wpdb->query("UPDATE `$apptabname` SET `status` = 'cancelled' WHERE `id` = '$appid' ;");
    }

    //applying coupon code
    if(isset($_POST['Action'])) {
        $Action = $_POST['Action'];
        if($Action == "apply-coupon") {
            if(isset($_POST['CouponCode'])) {
                $CouponCode = strtolower($_POST['CouponCode']);
            } else {
                $CouponCode = "";
            }
            if($CouponCode){
                global $wpdb;
                $Discount = 0;
                $CouponsCodesTable = $wpdb->prefix . "apcal_pre_coupons_codes";
                //Search Coupon
                $CouponDetails = $wpdb->get_row("SELECT * FROM `$CouponsCodesTable` WHERE `coupon_code` LIKE '$CouponCode'");
                if(count($CouponDetails)) {
                    //check coupon expire
                    $DateTodayTs = strtotime(date("Y-m-d"));
                    $ExpireDateTs = strtotime(date("Y-m-d", strtotime($CouponDetails->expire)));
                    $TotalUses = $CouponDetails->total_uses;
                    $UsedCount = $CouponDetails->used_count;
                    $Discount = $CouponDetails->discount;
                    if($DateTodayTs > $ExpireDateTs) {
                        //coupon expired
                        ?><div id="coupon-result"><div id="discount-rate-div" style="display: none;"><?php echo $Discount = 0; ?></div><?php echo strtoupper("<strong>$CouponCode</strong> "); _e("coupon code expired.", "appointzilla"); ?> <a id="try-another" onclick="return TryAgain();"><?php _e("Try Another", "appointzilla"); ?></a></div><?php
                    } else if($UsedCount >= $TotalUses) {
                        //ckeck used count
                        ?><div id="coupon-result"><div id="discount-rate-div" style="display: none;"><?php echo $Discount = 0; ?></div><?php echo strtoupper("<strong>$CouponCode</strong> "); _e("coupon code expired.", "appointzilla"); ?> <a id="try-another" onclick="return TryAgain();"><?php _e("Try Another", "appointzilla"); ?></a></div><?php
                    } else {
                        //coupon valid and appied
                        ?><div id="coupon-result">
                            <div id="coupon-code-div" style="display: none;"><?php echo $CouponCode; ?></div>
                            <div id="discount-rate-div" style="display: none;"><?php echo $Discount; ?></div>
                            <?php echo strtoupper("<strong>$CouponCode</strong> "); _e("coupon code applied.", "appointzilla"); ?> <a id="try-another" onclick="return TryAgain();"><?php _e("Change", "appointzilla"); ?></a>
                        </div><?php
                    }
                } else {
                  ?>
                  <div id="coupon-result">
                      <div id="discount-rate-div" style="display: none;"><?php echo $Discount; ?></div>
                      <?php echo strtoupper("<strong>$CouponCode</strong> "); _e("coupon code is invalid.", "appointzilla"); ?>
                      <a id="try-another" onclick="return TryAgain();"><?php _e("Try Another", "appointzilla"); ?></a>
                  </div>
                  <?php
                }
            }
        }//end of if action
    }

    //check existing user
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
							<button type="button" class="apcal_btn apcal_btn-success" id="ex-book-now" name="ex-book-now" onclick="return CheckValidation('ExUser',this);"><i class="icon-ok icon-white"></i> <?php _e('Book Now', 'appointzilla'); ?></button>
							<button type="button" class="apcal_btn apcal_btn-danger" id="ex-cancel-app" name="ex-cancel-app" onclick="return TryAgainBooking();"><i class="icon-remove icon-white"></i> <?php _e('Cancel', 'appointzilla'); ?></button>
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
						<button type="button" onclick="return TryAgainBooking();" class="apcal_btn apcal_btn-danger"><i class="fa fa-mail-reply"></i> Try Again</button>
					</td>
				</tr>
            <?php
            }?>
            </table></div><?php
        }
    }
}//end of short code function ?>
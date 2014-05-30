<div class="modal" id="SecondModal" >
    <form action="" method="post" name="secondmodal" id="secondmodal">
        <input name="StaffId" id="StaffId" type="hidden" value="<?php echo $_GET['StaffId']; ?>" />
        <input name="AppDate" id="AppDate" type="hidden" value="<?php echo $_GET['AppDate']; ?>" />
        <input name="CabinetID" id="CabinetID" type="hidden" value="<?php echo $_GET['CabinetID']; ?>" />

        <div class="modal-info">
            <div style="float:right; margin-top:18px; margin-right:40px;">
                <a href="" onclick="CloseModelform()" id="close" ><i class="icon-remove"></i></a>
            </div>
            <div class="alert alert-info">
                <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p><?php _e('Step-2. Select Service Time & Repeat', 'appointzilla'); ?>
            </div>
        </div><!--end modal-info-->

        <div class="modal-body">
            <div id="time-slot-list">
<!---loading second modal form ajax return code and time slot calculation--->
<?php 
// time-slots calculation
global $wpdb;
$ToadyBusinessClose = 1; //open
$ToadyStaffNotAvailable = 1; //available
$AppointmentDate = date("Y-m-d", strtotime($_GET['AppDate']));
$StaffId =  $_GET['StaffId'];


$BusinessHours = $AppointZilla->GetBusiness($AppointmentDate, $StaffId);
if($BusinessHours['Biz_start_time'] == 'none' || $BusinessHours['Biz_end_time'] == 'none') {
    $ToadyBusinessClose = 0;
} else {
    $Biz_start_time = $BusinessHours['Biz_start_time'];
    $Biz_end_time = $BusinessHours['Biz_end_time'];
}
$timeSlotList = $AppointZilla->getActiveTimeSlots($StaffId, $AppointmentDate, $Biz_start_time, $Biz_end_time);
if($ToadyBusinessClose) {
    $TodaysAllDayEvent = $AppointZilla->CheckAllDayEvent($AppointmentDate, $StaffId);

    if($TodaysAllDayEvent) {
        $AllDayEvent = 1;
    } else {
        $AllDayEableSlots = 0;
        $AllDayDisableSlots = 0;
        $AllDayEvent = 0;

        //get service details
        $ServiceDuration = 60;

        //get staff details
        $StaffTableName = $wpdb->prefix."ap_staff";
        $StaffData = $wpdb->get_row("SELECT `name` , `staff_hours` FROM `$StaffTableName` WHERE `id` = '$StaffId'", OBJECT);

        echo "<div class='alert alert-info'>".__('Select Time For', 'appointzilla')."<strong> '".ucwords($ServiceData->name)."' </strong>".__('On', 'appointzilla')." <strong> ".date("F jS, l", strtotime($AppointmentDate))."</strong> ".__('With', 'appointzilla')." <strong>'".ucwords($StaffData->name)."'</strong></div><hr/>";


        $start = strtotime($Biz_start_time);
        $end = strtotime($Biz_end_time);?>
            <!--start time list-->
            <?php

            if($TimeFormat == "h:i") $SlotTimeFormat = "h:i A";
            else $SlotTimeFormat = "H:i";?>

            <div class="apcal_alert apcal_alert-block" id="time_slot_box" style="float:left; margin-bottom: 0px;">
                <?php
                foreach($timeSlotList as $singleTime) {
                    $removecln = "H".str_replace(":", "", date($TimeFormat,$singleTime));?>
                    <div style="width:90px; float:left; padding:0px; display:inline-block;">
                        <input name="start_time" class="start_time" type="radio"  style="margin: 0px 0 0; vertical-align: middle;" value="<?php echo date($TimeFormat, $singleTime); ?>"/>
                        <span id="<?php echo $removecln; ?>"><strong><?php echo date($TimeFormat, $singleTime); ?></strong></span>
                    </div>
                <?php }?>
            </div>  <!-----time slot list end ------->
            <?php
    }
} ?>
            <div id="buttondiv" align="center">
                <button type="button" class="btn" value="" id="back1" name="back1" onclick="loadfirstmodal()"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></button>
                <button type="button" class="btn" value="" id="next2" name="next2" onclick="loadthirdmodal()"><?php _e('Next', 'appointzilla');?> <i class="icon-arrow-right"></i></button>
            </div>
            <div id="loading" align="center" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url()."/appointment-calendar-premium/images/loading.gif"; ?>" /></div>
        <br>
        <div class='alert alert-error' align="center"><?php _e('Admin can create appointments at any time.', 'appointzilla'); ?></div>
    </form>
</div>
<!---loading second modal form ajax return code and time slot calculation--->
    <div id="AppSecondModalData">
        <div class="apcal_modal" id="SecondModal" style="z-index:10000;">
            <input name="StaffId" id="StaffId" type="hidden" value="<?php if(isset($_GET['StaffId'])) {  echo $_GET['StaffId']; } ?>" />
            <input name="AppDate" id="AppDate" type="hidden" value="<?php if(isset($_GET['AppDate'])) {  echo date('d-m-Y', strtotime($_GET['AppDate'])); } ?>" />
            <input name="CabinetID" id="CabinetID" type="hidden" value="<?php if(isset($_GET['CabinetID'])) {  echo $_GET['CabinetID']; } ?>" />

            <div class="apcal_modal-info">
                <a href="" style="float:right; float:right; margin-right:40px; margin-top:21px;" onclick="CloseModelform()" id="close" ><i class="icon-remove"></i></a>
                <div class="apcal_alert apcal_alert-info">
                    <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p>
                    <?php _e('Step 2. Select Service Time', 'appointzilla'); ?>
                </div>
            </div>

            <div class="apcal_modal-body" style="padding: 0px;">
                <div id="timesloatbox" class="apcal_alert apcal_alert-block" style="float:left; height:auto; width:-moz-available; padding-right: 15px;">
                    <?php // time-slots calculation
                    global $wpdb;
                    $ToadyBusinessClose = 1; //open
                    $ToadyStaffNotAvailable = 1; //available
                    $AppointmentDate = date("Y-m-d", strtotime($_GET['AppDate']));
                    $StaffId =  $_GET['StaffId'];

                    $BusinessHours = $AppointZilla->GetBusiness($AppointmentDate,$StaffId);
                    if($BusinessHours['Biz_start_time'] == 'none' || $BusinessHours['Biz_end_time'] == 'none') {
                        $ToadyBusinessClose = 0;
                    } else {
                        $Biz_start_time = $BusinessHours['Biz_start_time'];
                        $Biz_end_time = $BusinessHours['Biz_end_time'];
                    }
                    $timeSlotList = $AppointZilla->getActiveTimeSlots($StaffId, $AppointmentDate, $Biz_start_time, $Biz_end_time);
                    if($ToadyBusinessClose) {

                        //get service details
                        $ServiceDuration = '60';

                        //get staff details
                        $StaffTableName = $wpdb->prefix."ap_staff";
                        $StaffData = $wpdb->get_row("SELECT `name` , `staff_hours` FROM `$StaffTableName` WHERE `id` = '$StaffId'", OBJECT);

                        $DateFormat = get_option('apcal_date_format');
                        echo "<div class='apcal_alert apcal_alert-info'>".__('Select Time For', 'appointzilla')."<strong> '".ucwords($ServiceData->name)."' </strong>".__('On', 'appointzilla')." <strong> '".date($DateFormat, strtotime($AppointmentDate))."'</strong> ".__('With', 'appointzilla')." <strong>'".ucwords($StaffData->name)."'</strong></div>";

                        $DisableSlotsTimes = $AppointZilla->TimeSlotCalculation($AppointmentDate, $StaffId, $ServiceId, $Biz_start_time, $Biz_end_time);
                        $start = strtotime($Biz_start_time);
                        $end = strtotime($Biz_end_time);

                        //user Define time slot create
                        $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings'));
                        $BookingUserTimeSlot = $AllCalendarSettings['booking_user_timeslot'];
                        //start time list
                        ?>
                        <div class="apcal_alert apcal_alert-block" id="time_slot_box" style="float:left; margin-bottom: 0px;">
                        <?php
                        foreach($timeSlotList as $singleTime) {
                            $removecln = "H".str_replace(":", "", date($TimeFormat,$singleTime));?>
                            <div style="width:90px; float:left; padding:0px; display:inline-block;">
                                <input name="start_time" id="start_time" type="radio"  style="margin: 0px 0 0; vertical-align: middle;" onclick="highlightsradio('<?php echo $removecln; ?>')" value="<?php echo date($TimeFormat, $singleTime); ?>"/>
                                <span id="<?php echo $removecln; ?>"><strong><?php echo date($TimeFormat, $singleTime); ?></strong></span>
                            </div>
                        <?php }?>
                </div>  <!-----time slot list end ------->
                <!-- button and message box-->
                <div style="float:left; width:100%;">
                    <?php
                        if(count($timeSlotList)<1) {
                            echo "<p align='center' class='apcal_alert apcal_alert-error'><strong>".__('Sorry! Today selected staff is not available.', 'appointzilla')."</strong> <br>";
                            echo "<button type='button' class='apcal_btn' value='' id='back1' name='back1' onclick='LoadFirstModal()'><i class='icon-arrow-left'></i> ".__('Back', 'appointzilla')."</button></p>";
                        }
                        else {?>
                    <br/>
                    <div id="buttondiv" align="center">
                        <button type="button" class="apcal_btn" value="" id="back1" name="back1" onclick="LoadFirstModal()"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></button>
                        <button type="button" class="apcal_btn" value="" id="next2" name="next2" onclick="LoadThirdModal()"><?php _e('Next', 'appointzilla'); ?> <i class="icon-arrow-right"></i></button>
                    </div>

                    <div id="loading" align="center" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                        <?php }// end of else ?>
                    </div>
                </div><?php
                    } else {
                        // business close-day end
                        echo "<div style='float:left; width:530px;'>";
                        if($ToadyBusinessClose == 0) {
                            echo "<strong>".__('Sorry! Today selected staff is not available.', 'appointzilla')."</strong><br><br>";
                        }

                        if($ToadyStaffNotAvailable == 0) {
                            echo "<strong>".__('Sorry! Today this staff is not available for booking.', 'appointzilla')."</strong><br><br>";
                        }
                        echo "<button type='button' class='apcal_btn' value='' id='back1' name='back1' onclick='LoadFirstModal()'><i class='icon-arrow-left'></i> ".__('Back', 'appointzilla')."</button>";
                        echo "</div>";
                    } ?>
            </div> <!--end of apcal_modal-body-->
        </div>  <!--end of SecondModal-->
    </div>  <!--end AppSecondModalData-->
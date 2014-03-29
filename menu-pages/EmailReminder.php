<?php
    global $wpdb;
    $AppointmentTable = $wpdb->prefix . 'ap_appointments';
    $ServiceTable = $wpdb->prefix . 'ap_services';
    $StaffTable = $wpdb->prefix . 'ap_staff';
    $ClientTable = $wpdb->prefix . 'ap_clients';
    $ReminderTable = $wpdb->prefix . 'ap_reminders';

    //-check reminder settings ON
    //--if ON then fetch appointment rows according to frequency
    //----send reminder
    //------set status and retires

    //get reminder details
    $ReminaderDetails =  get_option('ap_reminder_details');
    if($ReminaderDetails['ap_reminder_status'] == 'yes') {
        $Frequency = $ReminaderDetails['ap_reminder_frequency'];
        $FrequencyDate = date("Y-m-d", strtotime("+$Frequency day"));
        $AllFutureAppointments = $wpdb->get_results("SELECT * FROM `$AppointmentTable` WHERE `date` = '$FrequencyDate' AND `status` != 'cancelled' AND `status` != 'done'");
        foreach($AllFutureAppointments as $Appointment) {
            // check if already reminder sent but failure
            $FindReminder = $wpdb->get_row("SELECT * FROM `$ReminderTable` WHERE `app_id` = '$Appointment->id' ");
            if($FindReminder->status == 'failure') {
                $retries = $FindReminder->retries + 1;
            }

            //get service details
            $ServiceDetails = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$Appointment->service_id' ");

            //get staff details
            $StaffDetails = $wpdb->get_row("SELECT * FROM `$StaffTable` WHERE `id` = '$Appointment->staff_id' ");

            //date & time format
            $DateFormat = get_option('apcal_date_format');
            if($DateFormat == '') $DateFormat = "d-m-Y";
            $TimeFormat = get_option('apcal_time_format');
            if($TimeFormat == '') $TimeFormat = "h:i";
            if($TimeFormat == "h:i") $TimeFormat = "h:i A"; else $TimeFormat = "H:i";

            //blog name
            $BlogName =  get_bloginfo();
            if($Appointment->recurring == 'no') {
                $AppDate = date($DateFormat, strtotime($Appointment->date));
            } else {
                $AppDate = date($DateFormat, strtotime($Appointment->recurring_st_date))." - ".date($DateFormat, strtotime($Appointment->recurring_ed_date));
            }
            $AppTime = date($TimeFormat, strtotime($Appointment->start_time))." - ".date($TimeFormat, strtotime($Appointment->end_time));
            update_option("reminder_status_in_your_language",__(ucwords($Appointment->status), 'appointzilla'));
            $RStatusLang = get_option("reminder_status_in_your_language");
            if($RStatusLang) $Appointment->status = $RStatusLang;

            //client subject
            $reminder_subject = $ReminaderDetails['ap_reminder_subject'];
            $reminder_subject = str_replace("[blog-name]", ucwords($BlogName), $reminder_subject);
            $reminder_subject = str_replace("[client-name]", ucwords($Appointment->name), $reminder_subject);
            $reminder_subject = str_replace("[client-email]", ucwords($Appointment->email), $reminder_subject);
            $reminder_subject = str_replace("[service-name]", ucwords($ServiceDetails->name), $reminder_subject);
            $reminder_subject = str_replace("[staff-name]", ucwords($StaffDetails->name), $reminder_subject);
            $reminder_subject = str_replace("[app-date]", $AppDate, $reminder_subject);
            $reminder_subject = str_replace("[app-status]", ucwords($Appointment->status), $reminder_subject);
            $reminder_subject = str_replace("[app-time]", $AppTime, $reminder_subject);
            $reminder_subject = str_replace("[app-key]", $Appointment->appointment_key, $reminder_subject);

            //client body
            $reminder_body = $ReminaderDetails['ap_reminder_body'];
            $reminder_body = str_replace("[blog-name]", ucwords($BlogName), $reminder_body);
            $reminder_body = str_replace("[client-name]", ucwords($Appointment->name), $reminder_body);
            $reminder_body = str_replace("[client-email]", ucwords($Appointment->email), $reminder_body);
            $reminder_body = str_replace("[service-name]", ucwords($ServiceDetails->name), $reminder_body);
            $reminder_body = str_replace("[staff-name]", ucwords($StaffDetails->name), $reminder_body);
            $reminder_body = str_replace("[app-date]", $AppDate, $reminder_body);
            $reminder_body = str_replace("[app-status]", ucwords($Appointment->status), $reminder_body);
            $reminder_body = str_replace("[app-time]", $AppTime, $reminder_body);
            $reminder_body = str_replace("[app-key]", $Appointment->appointment_key, $reminder_body);

            $AdminEmail = get_bloginfo( 'admin_email' );
            $headers = "From: Admin <$AdminEmail>";
            if(!$FindReminder || $FindReminder->status != 'success') {
                //send email reminder
                if($error = wp_mail( $Appointment->email, $reminder_subject, $reminder_body, $headers, $attachments = '' )) {
                    if($FindReminder) {
                        // update reminder success
                        $wpdb->query("UPDATE `$ReminderTable` SET `status` = 'success', `retries` = '$retries' WHERE `id` = '$FindReminder->id'; ");
                    } else {
                        // insert reminder success
                        $wpdb->query("INSERT INTO `$ReminderTable` (`id`, `app_id`, `reminder_type`, `status`, `retries`, `error`, `time_date`) VALUES (NULL, '$Appointment->id', 'email', 'success', '1', '$error', CURRENT_TIMESTAMP);");
                    }
                } else {
                    if($FindReminder) {
                        // update reminder failure
                        $wpdb->query("UPDATE `$ReminderTable` SET `status` = 'failure', `retries` = '$retries' WHERE `id` = '$FindReminder->id'; ");
                    } else if(!$FindReminder || $FindReminder->status != 'success') {
                        // insert reminder failure
                        $wpdb->query("INSERT INTO `$ReminderTable` (`id`, `app_id`, `reminder_type`, `status`, `retries`, `error`, `time_date`) VALUES (NULL, '$Appointment->id', 'email', 'failure', '1', '$error', CURRENT_TIMESTAMP);");
                    }
                }// end of else email failure
            }
        }// end of appointment foreach
    }// end of if reminder status = yes
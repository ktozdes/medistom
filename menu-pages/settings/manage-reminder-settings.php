<div class="bs-docs-example tooltip-demo">

    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
      <h3><?php _e('Manage Reminder Settings' ,'appointzilla'); ?></h3>
    </div>
    <form method="post" action="?page=app-calendar-settings&show=remindersettings">
        <table width="100%" class="table">
            <tr>
                <th width="17%" align="right" scope="row"><?php _e('Send Reminder' ,'appointzilla'); ?></th>
                <td width="2%" align="center"><strong>:</strong></td>
                <td width="81%">
                <?php $ReminaderDetails = get_option('ap_reminder_details' ,'appointzilla'); ?>
                  <select name="ap_reminder_status" id="ap_reminder_status">
                    <option value="no" <?php if($ReminaderDetails['ap_reminder_status'] == 'no') echo "selected"; ?>><?php _e('No' ,'appointzilla'); ?></option>
                    <option value="yes" <?php if($ReminaderDetails['ap_reminder_status'] == 'yes') echo "selected"; ?>><?php _e('Yes' ,'appointzilla'); ?></option>
                  </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Send Reminder' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>

            <tr>
                <th width="17%" align="right" scope="row"><?php _e('Reminder Type' ,'appointzilla'); ?></th>
                <td width="2%" align="center"><strong>:</strong></td>
                <td width="81%">
                <?php $ap_reminder_type = $ReminaderDetails['ap_reminder_type']; ?>
                  <select name="ap_reminder_type" id="ap_reminder_type">
                    <option value="0"><?php _e('Select Reminder Type' ,'appointzilla'); ?></option>
                    <option value="email" <?php if($ap_reminder_type == 'email') echo "selected"; ?>><?php _e('Email' ,'appointzilla'); ?></option>
                  </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Reminder Type' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>

            <tr>
                <th width="17%" align="right" scope="row"><?php _e('Reminder Frequency' ,'appointzilla'); ?></th>
                <td width="2%" align="center"><strong>:</strong></td>
                <td width="81%">
                    <?php $ap_reminder_frequency = $ReminaderDetails['ap_reminder_frequency']; ?>
                    <select name="ap_reminder_frequency" id="ap_reminder_frequency">
                        <option value="0"><?php _e('Select Reminder Frequency' ,'appointzilla'); ?></option>
                        <option value="1" <?php if($ap_reminder_frequency == '1') echo "selected"; ?>><?php _e('1 Day Before' ,'appointzilla'); ?></option>
                        <option value="2" <?php if($ap_reminder_frequency == '2') echo "selected"; ?>><?php _e('2 Day Before' ,'appointzilla'); ?></option>
                        <option value="3" <?php if($ap_reminder_frequency == '3') echo "selected"; ?>><?php _e('3 Day Before' ,'appointzilla'); ?></option>
                        <!--<option value="4" <?php //if($ap_reminder_frequency == '4') echo "selected"; ?>><?php _e('4 Day Before' ,'appointzilla'); ?></option>
                        <option value="5" <?php //if($ap_reminder_frequency == '5') echo "selected"; ?>><?php _e('5 Day Before' ,'appointzilla'); ?></option>
                        <option value="6" <?php //if($ap_reminder_frequency == '6') echo "selected"; ?>><?php _e('6 Day Before' ,'appointzilla'); ?></option>
                        <option value="daily" <?php //if($ap_reminder_frequency == 'daily') echo "selected"; ?>><?php _e('Daily' ,'appointzilla'); ?></option>
                        <option value="weekly" <?php //if($ap_reminder_frequency == 'weekly') echo "selected"; ?>><?php _e('Weekly' ,'appointzilla'); ?></option>
                        <option value="monthly" <?php //if($ap_reminder_frequency == 'monthly') echo "selected"; ?>><?php _e('Monthly' ,'appointzilla'); ?></option>-->
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Reminder Frequency' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>

            <tr>
                <th align="right" scope="row"><?php _e('Reminder Message Subject' ,'appointzilla'); ?></th>
                <td align="center"><strong>:</strong></td>
                <td>
                    <?php $ap_reminder_subject = $ReminaderDetails['ap_reminder_subject']; ?>
                    <input name="ap_reminder_subject" id="ap_reminder_subject" type="text" value="<?php echo $ap_reminder_subject; ?>" style="width: 500px;" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Reminder Message Subject' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th align="right" scope="row"><?php _e('Reminder Message Body' ,'appointzilla'); ?></th>
                <td align="center"><strong>:</strong></td>
                <td>
                    <?php $ap_reminder_body = $ReminaderDetails['ap_reminder_body']; ?>
                    <div style="float:left;">
                    <textarea name="ap_reminder_body" id="ap_reminder_body" style="height: 320px; width: 500px;"><?php echo $ap_reminder_body; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Reminder Message Body' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></div>
                    <div style="float:left; border:0px solid #000000; width:300px; height:auto; margin-right:0px;" ><br>
                        <button class="btn btn-small btn-inverse" type="button"><?php _e('Use These Tags in Message','appointzilla'); ?></button><br>
                        <?php _e('Client Name','appointzilla'); ?> - [client-name]<br>
                        <?php _e('Client Email','appointzilla'); ?> - [client-email]<br>
                        <?php _e('Service Name','appointzilla'); ?> - [service-name]<br>
                        <?php _e('Staff Name','appointzilla'); ?> - [staff-name]<br>
                        <?php _e('Appointment Date','appointzilla'); ?> - [app-date]<br>
                        <?php _e('Appointment Status','appointzilla'); ?> - [app-status]<br>
                        <?php _e('Appointment Time','appointzilla'); ?> - [app-time]<br>
                        <?php _e('Appointment Key','appointzilla'); ?> - [app-key]<br>
                        <?php _e('Appointment Note','appointzilla'); ?> - [app-note]<br>
                        <?php _e('Blog Name','appointzilla'); ?> - [blog-name]
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">&nbsp;</th>
                <td>&nbsp;</td>
                <td>
                    <?php if($ap_reminder_type && $ap_reminder_frequency) { ?>
                    <button name="updateremindersettings" class="btn" type="submit" id="updateremindersettings" data-loading-text="Saving Settings"><i class="icon-pencil"></i> <?php _e('Update Settings' ,'appointzilla'); ?></button>
                    <?php } else { ?>
                    <button name="saveremindersettings" class="btn" type="submit" id="saveremindersettings" data-loading-text="Saving Settings"><i class="icon-ok"></i> <?php _e('Save Settings' ,'appointzilla'); ?></button>
                    <?php } ?>
                    <a class="btn" href="?page=app-calendar-settings&show=remindersettings" ><i class="icon-remove"></i> <?php _e('Cancel' ,'appointzilla'); ?></a>
                </td>
            </tr>
      </table>
    </form>



    <style type="text/css">
    .error{  color:#FF0000; }
    </style>

    <!--validation js lib-->
    <script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

    <script type="text/javascript">
    jQuery(document).ready(function () {
        //save button click
        jQuery('#saveremindersettings').click(function(){
            jQuery(".error").hide();
            //send reminder is yes
            var ap_reminder_status = jQuery('#ap_reminder_status').val();
            if(ap_reminder_status == 'yes') {
                var ap_reminder_type = jQuery('#ap_reminder_type').val();
                if(ap_reminder_type == 0) {
                    jQuery("#ap_reminder_type").after('<span class="error">&nbsp;<br><strong><?php _e('Select reminder type.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }

                var ap_reminder_frequency = $('#ap_reminder_frequency').val();
                if(ap_reminder_frequency == 0) {
                    jQuery("#ap_reminder_frequency").after('<span class="error">&nbsp;<br><strong><?php _e('Select reminder frequency.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }
            }
        });

        //update button click
        jQuery('#updatepaymentsettings').click(function(){
            jQuery(".error").hide();
            //send reminder is yes
            var ap_reminder_status = jQuery('#ap_reminder_status').val();
            if(ap_reminder_status == 'yes') {
                var ap_reminder_type = jQuery('#ap_reminder_type').val();
                if(ap_reminder_type == 0) {
                    jQuery("#ap_reminder_type").after('<span class="error">&nbsp;<br><strong><?php _e('Select reminder type.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }

                var ap_reminder_frequency = jQuery('#ap_reminder_frequency').val();
                if(ap_reminder_frequency == 0) {
                    jQuery("#ap_reminder_frequency").after('<span class="error">&nbsp;<br><strong><?php _e('Select reminder frequency.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }
            }
        });
    });
    </script>
</div>
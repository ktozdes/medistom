<?php
// Update payment settings Form
if(isset($_GET['manageremindersettings']) == 'yes') {
    require_once('manage-reminder-settings.php');
} else  {
    //save settings
    if(isset($_POST['saveremindersettings'])) {
        $ReminderDetails = array(
            'ap_reminder_status' => $_POST['ap_reminder_status'],
            'ap_reminder_type' => $_POST['ap_reminder_type'],
            'ap_reminder_frequency' => $_POST['ap_reminder_frequency'],
            'ap_reminder_subject' => $_POST['ap_reminder_subject'],
            'ap_reminder_body' => $_POST['ap_reminder_body'],
            'ap_reminder_renewal' => $_POST['ap_reminder_renewal']
        );
        update_option('ap_reminder_details',$ReminderDetails);
        echo "<script>alert('".__('Reminder settings successfully saved','appointzilla')."')</script>";
        echo "<script>location.href='?page=app-calendar-settings&show=remindersettings'</script>";
    }

    //update  settings
    if(isset($_POST['updateremindersettings'])) {
        $ReminderDetails = array(
            'ap_reminder_status' => $_POST['ap_reminder_status'],
            'ap_reminder_type' => $_POST['ap_reminder_type'],
            'ap_reminder_frequency' => $_POST['ap_reminder_frequency'],
            'ap_reminder_subject' => $_POST['ap_reminder_subject'],
            'ap_reminder_body' => $_POST['ap_reminder_body'],
            'ap_reminder_renewal' => $_POST['ap_reminder_renewal']
        );
        update_option('ap_reminder_details',$ReminderDetails);
        echo "<script>alert('".__('Reminder settings successfully updated','appointzilla')."')</script>";
        echo "<script>location.href='?page=app-calendar-settings&show=remindersettings'</script>";
    }

    //get reminder details
    $ReminderDetails = get_option('ap_reminder_details');
?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
      <h3><?php _e('Reminder Settings','appointzilla');?></h3>
    </div>
    <table width="100%" class="table">
        <tr>
        <th width="17%" scope="row"><?php _e('Send Reminder','appointzilla');?></th>
        <td width="5%"><strong>:</strong></td>
        <td width="78%">
            <em>
                <?php
                    if($ReminderDetails['ap_reminder_status']) {
                        echo _e(ucfirst($ReminderDetails['ap_reminder_status']),'appointzilla');
                    } else {
                        echo _e('Not Available','appointzilla');
                    }
                ?>
            </em>
        </td>
        </tr>
        <tr>
            <th width="17%" scope="row"><?php _e('Reminder Type','appointzilla');?></th>
            <td width="5%"><strong>:</strong></td>
            <td width="78%">
                <em>
                    <?php
                        if($ReminderDetails['ap_reminder_type']) {
                            echo _e(ucfirst($ReminderDetails['ap_reminder_type']),'appointzilla');
                        } else {
                            echo _e('Not Available','appointzilla');
                        }
                    ?>
                </em>
            </td>
        </tr>
        <th scope="row"><?php _e('Appointment Renewal Reminder # month','appointzilla');?></th>
        <td><strong>:</strong></td>
        <td>
            <em>
                <?php
                if($ReminderDetails['ap_reminder_renewal']) {
                    echo $ReminderDetails['ap_reminder_renewal'];
                }  else {
                    echo _e('Not Available','appointzilla');
                }
                ?>
            </em>
        </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Reminder Frequency','appointzilla');?></th>
            <td><strong>:</strong></td>
            <td>
                <em>
                    <?php
                        if($ReminderDetails['ap_reminder_frequency']) {
                            if($ReminderDetails['ap_reminder_frequency'] == 1 ) echo _e('1 Day Before','appointzilla');
                            if($ReminderDetails['ap_reminder_frequency'] == 2 ) echo _e('2 Day Before','appointzilla');
                            if($ReminderDetails['ap_reminder_frequency'] == 3 ) echo _e('3 Day Before','appointzilla');
                        }  else {
                            echo _e('Not Available','appointzilla');
                        }
                    ?>
                </em>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Reminder Message Subject','appointzilla');?></th>
            <td><strong>:</strong></td>
            <td>
                <em>
                    <?php
                        if($ReminderDetails['ap_reminder_subject']) {
                            echo $ReminderDetails['ap_reminder_subject'];
                        } else {
                            echo _e('Not Available','appointzilla');
                        }
                    ?>
                </em>
            </td>
        </tr>
        <tr>
        <th scope="row"><?php _e('Reminder Message Body','appointzilla');?></th>
        <td><strong>:</strong></td>
        <td>
            <em>
                <?php
                    if($ReminderDetails['ap_reminder_body']) {
                        echo "<pre>".$ReminderDetails['ap_reminder_body']."</pre>";
                    }  else {
                        echo _e('Not Available','appointzilla');
                    }
                ?>
            </em>
        </td>
        </tr>
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td><a href="?page=app-calendar-settings&show=remindersettings&manageremindersettings=yes" class="btn btn-primary"><?php _e('Manage Settings','appointzilla');?></a></td>
        </tr>
    </table>
<?php
}
?>
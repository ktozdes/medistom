<?php
class AppointzillaWidgetController extends WP_Widget{
    private $classData = array();
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::WP_Widget(false, 'Appointment Reminder',array( 'description' =>'Displays reminder client list'));
    }
    /**
     * Front-end display of widget.
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    function widget( $args, $instance )
    {
        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $instance['title'] );
        $viewmode = apply_filters( 'widget_title', $instance['viewmode'] );
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
        if ($viewmode=='dropdown'){
            $this->dropDownView();
        }
        echo $args['after_widget'];
    }
    /**
     * Back-end widget form.
     * @param array $instance Previously saved values from database.
     */
    function form( $instance )
    {
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'New title', 'text_domain');
        $viewmode = isset($instance[ 'viewmode' ]) ? $instance[ 'viewmode' ]: 'link';
        ?>
        <table>
            <tr>
                <td>Title:</td>
                <td><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </tr>
            <tr>
                <td>Cart type:</td>
                <td><select id="<?php echo $this->get_field_id( 'viewmode' ); ?>" name="<?php echo $this->get_field_name( 'viewmode' ); ?>">
                        <option value="dropdown"  <?php echo ( $viewmode=='dropdown' )?'selected="selected"':''; ?>>Link to cart</option>
                        <option value="items" <?php echo ( $viewmode=='items')?'selected="selected"':''; ?>>View items</option>
                    </select>
                </td>
            </tr>
        </table>
    <?php
    }
    /**
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */
    function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['viewmode'] = ( ! empty( $new_instance['viewmode'] ) ) ? strip_tags( $new_instance['viewmode'] ) : 'link';
        return $instance;
    }

    function dropDownView()
    {
        global $wpdb, $uCartPluginURL;
        $appointment_table = $wpdb->prefix . "ap_appointments";
        $client_table = $wpdb->prefix . "ap_clients";
        $StaffTableName = $wpdb->prefix . "ap_staff";
        $ReminderDetails = get_option('ap_reminder_details');
        $reminderList = $wpdb->get_results(
            "SELECT app.id, app.name, app.email, app.phone, app.note, app.date, client.name AS client_name FROM $appointment_table as app
                LEFT JOIN $client_table as client ON client.id = app.client_id
            WHERE DATE_ADD( `date` , INTERVAL $ReminderDetails[ap_reminder_renewal] MONTH ) > DATE_SUB( CURDATE( ) , INTERVAL 1 WEEK ) AND DATE_ADD( `date` , INTERVAL $ReminderDetails[ap_reminder_renewal] MONTH ) <= CURDATE() AND recurring_type!='reminded' ORDER BY date ASC", ARRAY_A);
        if (count($reminderList)>0):?>
        <script>
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            jQuery(document).ready(function(){
            jQuery('.widget_client_name').click(function(){
                jQuery('.widget_client_info').fadeOut();
                jQuery(this).next().fadeIn();
            });
        });
        function reminded(appID,e)
        {
            jQuery('#ajax-loading-container').show();
            var data = {
                'action': 'remindment_reminded',
                'app_id': appID
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.get(ajaxurl, data, function(response) {
                jQuery('#ajax-loading-container').hide();
                if (response>0){
                    jQuery('.widget_appzilla_message').removeClass('alert-error').addClass('alert-success');
                    jQuery('.widget_appzilla_message').html('<strong>'+jQuery('.widget_client_name_'+appID).html() +'</strong> '+ '<?php _e(' was reminded','appointzilla');?>');
                    jQuery(e).parents('.widget_client_info').remove();
                    jQuery('.widget_client_name_'+appID).remove();
                }
                else{
                    jQuery('.widget_appzilla_message').html('<?php _e('Something went wrong','appointzilla');?>');
                    jQuery('.widget_appzilla_message').removeClass('alert-success').addClass('alert-error');
                }
                jQuery('.widget_appzilla_message').stop().fadeIn().delay(5000).fadeOut();
            });
        }
        </script>
            <div class="widget_appzilla_message alert alert-success"></div>
            <ul id="jquery-menu">
            <?php foreach($reminderList as $singleReminder ):?>
                <li class="widget_client_name widget_client_name_<?php echo $singleReminder[id];?>"><?php echo $singleReminder[client_name];?></li>
                <table class="widget_client_info">
                    <tr>
                        <td><?php _e('Email', 'appointzilla'); ?>:</td>
                        <td><?php echo $singleReminder[email];?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Phone', 'appointzilla'); ?>:</td>
                        <td><?php echo $singleReminder[phone];?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Reminded', 'appointzilla'); ?>:</td>
                        <td><a href="javascript:void(0)" onclick="reminded(<?php echo $singleReminder[id];?>,this)" class="apcal_btn">OK</a> </td>
                    </tr>
                </table>
            <?php endforeach;?>
        </ul>
    <?php else:?>
            <div><?php _e('No any reminder for future week','appointzilla');?></div>
    <?php endif;
    }
}
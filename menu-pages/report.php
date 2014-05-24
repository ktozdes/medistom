<?php global $wpdb;
include_once('includes/ReportController.php');
$ReportController = new ReportController();

if (isset($_GET[ajax]) && $_GET[ajax]=='true'){
    ?>
    <div id="ajax_container">
        <?php
        print_r($_GET);
        if ($_GET[category]=='staff'){
            $ReportController->getContentStaff($_GET);
        }
        else if ($_GET[category]=='service'){
            $ReportController->getContentService($_GET);
        }
        else if ($_GET[category]=='appointment'){
            $ReportController->getContentAppointment($_GET);
        }?>
    </div>
<?php
exit();
}

$staff_category_table = $wpdb->prefix . "ap_staff_groups";
$staff_table = $wpdb->prefix . "ap_staff";
$appointment_table = $wpdb->prefix . "ap_appointments";
$payment_table = $wpdb->prefix . "ap_appointments";
?>
<script>
jQuery(document).ready(function(){
    jQuery('.jquery-tab').tabs();
    jQuery(".datepicker").datepicker({
        inline: true,
        firstDay:1,
        changeMonth: true,
        dateFormat:'dd-mm-yy',
        dayNames:['Пон','Вт','Ср',"Чтв",'Пяц','Суб','Вос']
    });

    jQuery('.filterbutton').click(function(){

        category    = jQuery(this).parents('.filter_table').find('.filter_category').val();
        startDate   = jQuery(this).parents('.filter_table').find('.filter_start_date').val();
        endDate     = jQuery(this).parents('.filter_table').find('.filter_end_date').val();
        staffID     = jQuery(this).parents('.filter_table').find('.filter_staff').val();
        clientID    = jQuery(this).parents('.filter_table').find('.filter_client').val();
        thisData = new Array();
        if (category=='staff'){
            divContainer = '#staff_container .value_container';
            thisData = {
            'ajax':'true',
                'category':category,
                'startDate':startDate,
                'endDate':endDate,
                'staffID':staffID,
                'clientID':clientID
        }
        }
        else if (category=='service'){
            divContainer = '#service_container .value_container';
            serviceID    = jQuery(this).parents('.filter_table').find('.filter_service').val();
            minPrice     = jQuery(this).parents('.filter_table').find('.filter_min_numeric').val();
            maxPrice    = jQuery(this).parents('.filter_table').find('.filter_max_numeric').val();
            thisData = {
                'ajax':'true',
                'category':category,
                'startDate':startDate,
                'endDate':endDate,
                'minPrice':minPrice,
                'maxPrice':maxPrice,
                'serviceID':serviceID,
                'staffID':staffID,
                'clientID':clientID
            };
        }
        else if (category=='appointment'){
            divContainer = '#appointment_container .value_container';
            appointmentID = jQuery(this).parents('.filter_table').find('.filter_appointment').val();
            payment       = jQuery(this).parents('.filter_table').find('.filter_payment').val();
            thisData = {
                'ajax':'true',
                'category':category,
                'startDate':startDate,
                'endDate':endDate,
                'staffID':staffID,
                'clientID':clientID,
                'payment':payment,
                'appointmentID':appointmentID
            };
        }
        jQuery('#ajax_loading').fadeIn();
        jQuery('#messagebox').html(thisData);
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            cache: false,
            data : thisData,
            complete : function() {  },
            success: function(data) {
                data = jQuery(data).find('div#ajax_container');
                jQuery(divContainer).html(data);
                jQuery('#ajax_loading').fadeOut();
            }
        });
    });
});
</script>
<div class="bs-docs-example tooltip-demo">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-pencil"></i> <?php _e('Report','appointzilla'); ?></h3>
    </div>
    <div id="messagebox"></div>


    <div class="jquery-tab">
        <ul>
            <li><a href="#staff_container"><?php _e('Staff','appointzilla');?></a></li>
            <li><a href="#service_container"><?php _e('Service','appointzilla');?></a></li>
            <li><a href="#appointment_container"><?php _e('Appointment','appointzilla');?></a></li>
        </ul>
        <div id="ajax_loading" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url("images/loading.gif", __FILE__); ?>" /></div>
        <div id="staff_container">
            <?php $ReportController->getFilterStaff();?>
            <div class="value_container">
            <?php $ReportController->getContentStaff();?>
            </div>
        </div>
        <div id="service_container">
            <?php $ReportController->getFilterService();?>
            <div class="value_container">
                <?php $ReportController->getContentService();?>
            </div>
        </div>
        <div id="appointment_container">
            <?php $ReportController->getFilterAppointment();?>
            <div class="value_container">
                <?php $ReportController->getContentAppointment();?>
            </div>
        </div>
    </div>
</div>
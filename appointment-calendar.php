<?php
/**
 * Plugin Name: Appointment Calendar Premium
 * Version: 3.5.4
 * Description: Appointment Calendar Premium is a simple yet powerful plugin for accepting online appointments on your WordPress blog site.
 * Author: Scientech It Solutions
 * Author URI: http://www.appointzilla.com
 * Plugin URI: http://www.appointzilla.com
 */

//ini_set('error_reporting', !E_NOTICE & !E_WARNING);

// Run 'Install' script on plugin activation ###
register_activation_hook( __FILE__, 'InstallScript' );
function InstallScript() {
    require_once('install-script.php');
}

// Translate all text & labels of plugin ###
add_action('plugins_loaded', 'LoadPluginLanguage');
 
function LoadPluginLanguage() {
    load_plugin_textdomain('appointzilla', FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
}

// Admin dashboard Menu Pages For Booking Calendar Plugin
add_action('admin_menu','appointment_calendar_menu');

function appointment_calendar_menu() {
    //create new top-level menu 'appointment-calendar'
    $Menu = add_menu_page( 'Appointment Calendar', __('Appointment Calendar', 'appointzilla'), 'administrator', 'appointment-calendar', '', 'dashicons-calendar');

    // Calendar Page
    $SubMenu1 = add_submenu_page( 'appointment-calendar', __('Admin Calendar', 'appointzilla'), __('Admin Calendar', 'appointzilla'), 'administrator', 'appointment-calendar', 'display_calendar_page' );
    // Time stoat Page
    $SubMenu2 = add_submenu_page( '', 'Manage Time Slot', '', 'administrator', 'time_sloat', 'display_time_slot_page' );
    // Data Save Page
    $SubMenu3 = add_submenu_page( '', 'Data Save', '', 'administrator', 'data_save', 'display_data_save_page' );

    // Service Page
    $SubMenu4 = add_submenu_page( 'appointment-calendar', __('Services', 'appointzilla'), __('Services', 'appointzilla'), 'administrator', 'service', 'display_service_page' );
    // manage Service Page
    $SubMenu5 = add_submenu_page( '', 'Manage Service', '', 'administrator', 'manage-service', 'display_manage_service_page' );

    // Staff Page
    $SubMenu6 = add_submenu_page( 'appointment-calendar', 'Staffs', __('Staffs', 'appointzilla'), 'administrator', 'staff', 'display_staff_page' );
    // manage Staff Page
    $SubMenu7 = add_submenu_page( '', 'Manage Staff', '', 'administrator', 'manage-staff', 'display_manage_staff_page' );

    //staff-calendar
    $SubMenu8 = add_submenu_page( 'appointment-calendar', __('Staff Calendar', 'appointzilla'), __('Staff Calendar', 'appointzilla'), 'contributor', 'staff-appointment-calendar', 'display_staff_appointment_calendar_page' );
    $SubMenu20 = add_submenu_page( 'appointment-calendar', __('Manage Appointments', 'appointzilla'), __('Manage Appointments', 'appointzilla'), 'contributor', 'manage-staff-appointments', 'display_staff_appointments_page' );

    // Time-Off Page
    $SubMenu9 = add_submenu_page( 'appointment-calendar', 'Time Off', __('Time Off', 'appointzilla'), 'administrator', 'timeoff', 'display_timeoff_page' );
    // Update Time-Off Page
    $SubMenu10 = add_submenu_page( '', 'Update TimeOff', '', 'administrator', 'update-timeoff', 'display_update_timeoff_page' );

    // Client Page
    $SubMenu11 = add_submenu_page( 'appointment-calendar', __('Clients', 'appointzilla'), __('Clients', 'appointzilla'), 'administrator', 'client', 'display_client_page' );
    $SubMenu12 = add_submenu_page( '', 'Client Manage', '','administrator', 'client-manage', 'display_manage_client_page' );
    $SubMenu25 = add_submenu_page( 'appointment-calendar', 'Medical Cart Manage', 'Medical Cart','administrator', 'medical_cart', 'display_medical_cart_page' );

    // Manage Appointment Page
    $SubMenu13 = add_submenu_page( 'appointment-calendar', __('Admin Appointments', 'appointzilla'), __('Appointments', 'appointzilla'), 'administrator', 'manage-appointments', 'display_manage_appointment_page' );
    // Update Appointments Page
    $SubMenu14 = add_submenu_page( '', 'Update Appointment', '', 'administrator', 'update-appointment', 'display_update_appointment_page' );

    // Payment Transaction Page
    $SubMenu18 = add_submenu_page( 'appointment-calendar', __('Payment Transaction', 'appointzilla'), __('Payment Transaction', 'appointzilla'), 'administrator', 'manage-payment-transaction', 'display_payment_transaction_page' );

    //Export Appointments & Client List
    $SubMenu19 = add_submenu_page('appointment-calendar', __('Export Lists', 'appointzilla'), __('Export Lists', 'appointzilla'), 'administrator', 'export-lists', 'display_export_lists_page' );

    //Coupon Codes
    $SubMenu21 = add_submenu_page('appointment-calendar', __('Coupons Codes', 'appointzilla'), __('Coupons Codes', 'appointzilla'), 'administrator', 'apcal-coupons-codes', 'display_coupons_codes_page' );

    // Settings Page
    $SubMenu15 = add_submenu_page( 'appointment-calendar', __('Settings', 'appointzilla'), __('Settings', 'appointzilla'), 'administrator', 'app-calendar-settings', 'display_settings_page' );

    // Remove Plugin
    $SubMenu16 = add_submenu_page( 'appointment-calendar', __('Remove Plugin', 'appointzilla'), __('Remove Plugin', 'appointzilla'), 'administrator', 'uninstall-plugin', 'display_uninstall_plugin_page' );

    // Support & Help
    $SubMenu17 = add_submenu_page( 'appointment-calendar', __('Help & Support', 'appointzilla'), __('Help & Support', 'appointzilla'), 'administrator', 'support-n-help', 'display_support_n_help_page' );

    //client-calendar
    $SubMenu22 = add_submenu_page( 'appointment-calendar', __('Appointment Calendar', 'appointzilla'), __('Appointment Calendar', 'appointzilla'), 'subscriber', 'client-appointment-calendar', 'display_client_appointment_calendar_page' );
    $SubMenu23 = add_submenu_page( 'appointment-calendar', __('Your Appointments', 'appointzilla'), __('Your Appointments', 'appointzilla'), 'subscriber', 'manage-client-appointments', 'display_client_mange_appointments_page' );
    $SubMenu24 = add_submenu_page( '', 'Update Appointment', '', 'subscriber', 'update-client-appointment', 'display_update_client_appointment_page' );

    add_action( 'admin_print_styles-' . $Menu, 'calendar_css_js' );
    //calendar
    add_action( 'admin_print_styles-' . $SubMenu1, 'calendar_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu2, 'calendar_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu3, 'calendar_css_js' );
    //service
    add_action( 'admin_print_styles-' . $SubMenu4, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu5, 'other_pages_css_js' );
    //staff
    add_action( 'admin_print_styles-' . $SubMenu6, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu7, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu8, 'calendar_css_js' );
    //time-off
    add_action( 'admin_print_styles-' . $SubMenu9, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu10, 'other_pages_css_js' );
    //client
    add_action( 'admin_print_styles-' . $SubMenu11, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu12, 'other_pages_css_js' );
    //manage app
    add_action( 'admin_print_styles-' . $SubMenu13, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu14, 'other_pages_css_js' );
    //settings
    add_action( 'admin_print_styles-' . $SubMenu15, 'other_pages_css_js' );
    //remove plugin
    add_action( 'admin_print_styles-' . $SubMenu16, 'other_pages_css_js' );
    //support n help
    add_action( 'admin_print_styles-' . $SubMenu17, 'other_pages_css_js' );
    //payment txn
    add_action( 'admin_print_styles-' . $SubMenu18, 'other_pages_css_js' );
    //export lists
    add_action( 'admin_print_styles-' . $SubMenu19, 'other_pages_css_js' );
    //staff manage appointment
    add_action( 'admin_print_styles-' . $SubMenu20, 'other_pages_css_js' );
    //coupons codes
    add_action( 'admin_print_styles-' . $SubMenu21, 'other_pages_css_js' );
    //client calendar
    add_action( 'admin_print_styles-' . $SubMenu22, 'calendar_css_js' );
    //client appointments
    add_action( 'admin_print_styles-' . $SubMenu23, 'other_pages_css_js' );
    //client update appointment
    add_action( 'admin_print_styles-' . $SubMenu24, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu25, 'other_pages_css_js' );

}// end of menu function

function calendar_css_js() {
    wp_register_script( 'jquery-custom',plugins_url('menu-pages/fullcalendar-assets-new/js/jquery-ui-1.8.23.custom.min.js', __FILE__), array('jquery'), true );
    wp_enqueue_script('full-calendar',plugins_url('/menu-pages/fullcalendar-assets-new/js/fullcalendar.min.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('datepicker-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__),array('jquery','jquery-custom'));

    wp_enqueue_style('bootstrap-css',plugins_url('/bootstrap-assets/css/bootstrap.css', __FILE__));
    wp_enqueue_style('bootstrap-css');
    wp_enqueue_style('fullcalendar-css',plugins_url('/menu-pages/fullcalendar-assets-new/css/fullcalendar.css', __FILE__));
    wp_enqueue_style('datepicker-css',plugins_url('/menu-pages/datepicker-assets/css/jquery-ui-1.8.23.custom.css', __FILE__));
    wp_enqueue_style('apcal-css',plugins_url('/menu-pages/css/apcal-css.css', __FILE__));

}

function other_pages_css_js() {
    wp_register_style('bootstrap-css',plugins_url('/bootstrap-assets/css/bootstrap.css', __FILE__));
    wp_enqueue_style('bootstrap-css');
    wp_enqueue_style('datepicker-css',plugins_url('/menu-pages/datepicker-assets/css/jquery-ui-1.8.23.custom.css', __FILE__));
    wp_enqueue_style('fancybox-css',plugins_url('/bootstrap-assets/css/jquery.fancybox.css', __FILE__));
    wp_enqueue_style('fancybox-thumbs-css',plugins_url('/bootstrap-assets/css/jquery.fancybox-thumbs.css', __FILE__));

    wp_enqueue_script( 'jquery-ui',plugins_url('menu-pages/jquery-ui-custom/js/jquery-ui-1.10.4.custom.js', __FILE__), array('jquery'), true );
    wp_enqueue_script('datepicker-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('tooltip',plugins_url('/bootstrap-assets/js/bootstrap-tooltip.js', __FILE__),array('jquery'));
    wp_enqueue_script('bootstrap-affix',plugins_url('/bootstrap-assets/js/bootstrap-affix.js', __FILE__));
    wp_enqueue_script('bootstrap-application',plugins_url('/bootstrap-assets/js/application.js', __FILE__));
    wp_enqueue_script('fancybox-js',plugins_url('/bootstrap-assets/js/jquery.fancybox.js', __FILE__),array('jquery'));
    wp_enqueue_script('fancybox-thumbs-js',plugins_url('/bootstrap-assets/js/jquery.fancybox-thumbs.js', __FILE__),array('jquery'));

    //font-awesome js n css
    wp_enqueue_style(
        'font-awesome-css',
        plugins_url('/menu-pages/font-awesome-assets/css/font-awesome.css', __FILE__)
    );
    wp_enqueue_style('apcal-css',plugins_url('/menu-pages/css/apcal-css.css', __FILE__));
}

//short-code detect
function shortcode_detect() {
    global $wp_query;
    $posts = $wp_query->posts;
    $pattern = get_shortcode_regex();
    
    foreach ($posts as $post) {
        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( 'APCAL_BTN', $matches[2] ) || in_array( 'APCAL_MOBILE', $matches[2] ) || in_array( 'APCAL', $matches[2] ) ) {
            //wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
            //de-register script hook
            //remove_action('wp_enqueue_scripts', 'wp_foundation_js');
            //remove_action('wp_enqueue_scripts', 'modernize_it');

            wp_register_script( 'jquery-custom', plugins_url('menu-pages/fullcalendar-assets-new/js/jquery-ui-1.8.23.custom.min.js', __FILE__), 10, array('jquery'), false, true );
            wp_enqueue_script('apcal-full-calendar',plugins_url('/menu-pages/fullcalendar-assets-new/js/fullcalendar.min.js', __FILE__),array('jquery','jquery-custom'));
            wp_enqueue_script('datepicker-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__),array('jquery','jquery-custom'));
            wp_enqueue_script('apcal-calendar',plugins_url('calendar/calendar.js', __FILE__));
            wp_enqueue_script('apcal-moment-min',plugins_url('calendar/moment.min.js', __FILE__));
            wp_enqueue_style('apcal-bootstrap-apcal',plugins_url('bootstrap-assets/css/bootstrap-apcal.css', __FILE__));
            wp_enqueue_style('apcal-fullcalendar-css',plugins_url('/menu-pages/fullcalendar-assets-new/css/fullcalendar.css', __FILE__));
            wp_enqueue_style('apcal-datepicker-css',plugins_url('/menu-pages/datepicker-assets/css/jquery-ui-1.8.23.custom.css', __FILE__));
            //font-awesome js n css
            wp_enqueue_style(
                'font-awesome-css',
                plugins_url('/menu-pages/font-awesome-assets/css/font-awesome.css', __FILE__)
            );
            //localization js
            //wp_enqueue_script('apcal-datepicker-css',plugins_url('/menu-pages/datepicker-assets/jquery-ui-localization/jquery.ui.datepicker-ar.js', __FILE__));

            //jetpack tweak remove open graph tag if jetpack plugin activated
            remove_action('wp_head', 'jetpack_og_tags');
            break;
        }
    }
}
add_action( 'wp', 'shortcode_detect' );

// Rendering All appointment-calendar Menu Page

 //calendar page
 function display_calendar_page() {
     require_once('menu-pages/calendar.php');
 }
 
 //time slot page
 function display_time_slot_page() {
     require_once("menu-pages/appointment-form2.php");
 }
 
 //appointment save page
 function display_data_save_page() {
     require_once("menu-pages/data_save.php");
 }
 
 //service page
 function display_service_page() {
     require_once("menu-pages/service.php");
 }
 //manage service page
 function display_manage_service_page() {
     require_once("menu-pages/manage-service.php");
 }
 
 //staff page
 function display_staff_page() {
     require_once("menu-pages/staff.php");
 }
 function display_manage_staff_page() {
     require_once("menu-pages/manage-staff.php");
 }


 
 //time-off page
 function display_timeoff_page() {
     require_once("menu-pages/timeoff.php");
 }
 //update-time-off page
 function display_update_timeoff_page() {
     require_once("menu-pages/update-timeoff.php");
 }
 
 function display_staff_profile_page() {
     require_once("menu-pages/staff-profile.php");
 }
 
 //client page
 function display_client_page() {
     require_once("menu-pages/client.php");
 }
 function display_manage_client_page() {
     require_once("menu-pages/client_manage.php");
 }
function display_medical_cart_page() {
    require_once("menu-pages/medical_cart.php");
}

 //manage-appointment page
 function display_manage_appointment_page() {
     require_once("menu-pages/manage-appointments.php");
 }
//update appointment page
 function display_update_appointment_page() {
     require_once("menu-pages/update-appointments.php");
 }

 //payment transaction page
 function display_payment_transaction_page() {
     require_once("menu-pages/payment-transaction.php");
 }

//export appointments & clients lists
function display_export_lists_page() {
    require_once("menu-pages/export-lists.php");
}

//coupons codes page
function display_coupons_codes_page() {
    require_once("menu-pages/coupons-codes.php");
}
 
 //settings page
 function display_settings_page() {
     require_once("menu-pages/settings.php");
 }

 // Uninstall plugin
 function display_uninstall_plugin_page() {
     require_once("uninstall-plugin.php");
 }
 
 // Support & Help
 function display_support_n_help_page() {
     require_once("menu-pages/supportnhelp.php");
 }
 
 //staff calendar page
 function display_staff_appointment_calendar_page() {
     require_once("menu-pages/staff-appointment-calendar.php");
 }
//staff appointments page
function display_staff_appointments_page() {
    require_once("menu-pages/manage-staff-appointments.php");
}

//client calendar page
function display_client_appointment_calendar_page() {
    require_once("menu-pages/client-appointment-calendar.php");
}
//client appointments page
function display_client_mange_appointments_page() {
    require_once("menu-pages/manage-client-appointments.php");
}
//client update appointment page
function display_update_client_appointment_page() {
    require_once("menu-pages/update-client-appointments.php");
}

// Including Calendar Short-Code Page
require_once("appointment-calendar-shortcode.php");

// Including Calendar Button Short-Code Page
require_once("appointment-calendar-button-shortcode.php");

//Including Calendar Mobile Shortcode
require_once("appointment-calendar-mobile-shortcode.php");

//run cron reminder
add_action( 'plugins_loaded', 'load_apcal_reminder', 10 ); // this hook for wp_mail use after all plugins_loaded
function load_apcal_reminder() {

    // insert row every second 5 second on recurring visit by any user on site
    add_action('wp', 'apcal_reminder_activation');
    function apcal_reminder_activation() {
        if ( !wp_next_scheduled( 'apcal_reminder_event' ) ) {
            wp_schedule_event( time(), 'customrecurrence', 'apcal_reminder_event');
        }
    }

    add_action('apcal_reminder_event', 'send_apcal_reminders');
    function send_apcal_reminders() {
        // Including Email Reminder Class
        require_once('menu-pages/EmailReminder.php');
    }

    //custom recurrence
    function custom_recurrence_time( $schedules ) {
    $schedules['customrecurrence'] = array(
            'interval' => 60*60,
            'display' => __('Every Hour')
        );
        return $schedules;
    }
    add_filter( 'cron_schedules', 'custom_recurrence_time' );
}//end of load_apcal_reminder

add_action('admin_enqueue_scripts', 'my_admin_scripts');

function my_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'medical_cart') {
        wp_enqueue_media();
        wp_register_script('my-admin-js', WP_PLUGIN_URL.'/my-plugin/my-admin.js', array('jquery'));
        wp_enqueue_script('my-admin-js');
    }
}
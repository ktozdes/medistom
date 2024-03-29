<?php
/**
 * Plugin Name: Appointment Calendar Premium
 * Version: 3.5.4
 * Description: Appointment Calendar Premium is a simple yet powerful plugin for accepting online appointments on your WordPress blog site.
 * Author: Scientech It Solutions
 * Author URI: http://www.appointzilla.com
 * Plugin URI: http://www.appointzilla.com
 */
require_once('menu-pages/widget/AppointzillaWidgetController.php');
$pluginDIR='';
$DateFormat = (get_option('apcal_date_format') == '') ? "d-m-Y" : get_option('apcal_date_format');
$TimeFormat = (get_option('apcal_time_format') == '')?"h:i" : get_option('apcal_time_format');
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
//!!!!!!!!widget!!!!!!
add_action( 'widgets_init',  'initialize_widget');
function initialize_widget()
{
    register_widget( 'AppointzillaWidgetController' );
}
add_action( 'wp_ajax_get_diagnosis_service_ids', 'get_diagnosis_service_ids_callback' );
function get_diagnosis_service_ids_callback() {
    global $wpdb; // this is how you get access to the database
    $treatment_service_table    = $wpdb->prefix . "ap_treatment_service";
    $treatmentServiceList = $wpdb->get_results("SELECT service_id FROM $treatment_service_table WHERE treatment_id=$_GET[diagnosis_id]",ARRAY_A);
    $selectedServices = array();
    foreach($treatmentServiceList as $singleService){
        $selectedServices[] = $singleService[service_id];
    }
    echo json_encode($selectedServices);

    die(); // this is required to return a proper result
}

add_action( 'wp_ajax_remindment_reminded', 'remindment_reminded_callback' );
function remindment_reminded_callback() {
    global $wpdb; // this is how you get access to the database
    $appointment_table = $wpdb->prefix . "ap_appointments";
    $result = $wpdb->update($appointment_table,
        array('recurring_type'=>'reminded'),
        array(id =>$_GET[app_id])
    );
    echo $result;

    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_new_medical_cart_row', 'new_medical_cart_row_callback' );
function new_medical_cart_row_callback() {
    global $wpdb; // this is how you get access to the database
    $medical_cart_table = $wpdb->prefix . "ap_medical_cart";
    $result = $wpdb->insert($medical_cart_table,
        array(
			'medical_cart_date'=>$_POST[medical_cart_date],
			'medical_cart_code'=>$_POST[medical_cart_code],
			'medical_cart_tooth'=>$_POST[medical_cart_tooth],
			'medical_cart_note'=>$_POST[medical_cart_note],
			'medical_cart_image_ids'=>$_POST[medical_cart_image_ids],
			'medical_cart_appointment_id'=>$_POST[medical_cart_appointment_id],
		)
	);
    echo $result;
    die(); // this is required to return a proper result
}

add_action( 'wp_ajax_new_medical_cart_treatment', 'new_medical_cart_treatment_callback' );
function new_medical_cart_treatment_callback() {
    global $wpdb; // this is how you get access to the database
    $medical_cart_treatment_table = $wpdb->prefix . "ap_medical_cart_treatment";
    $result = $wpdb->insert($medical_cart_treatment_table,
        array(
            'treatment_id'=>$_POST[treatment_id],
            'medical_cart_id'=>$_POST[medical_cart_id],
            'medical_cart_treatment_date'=>date('d-m-Y'),
        )
    );
    echo $result;
    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_is_client_exists', 'is_client_exists_callback' );
function is_client_exists_callback() {
    global $wpdb; // this is how you get access to the database
    $clientTable = $wpdb->prefix . "ap_clients";
    $wpdb->get_row("SELECT * FROM $clientTable WHERE name LIKE '".trim($_POST[ClientFirstName])." ".trim($_POST[ClientLastName])."' AND phone = '$_POST[ClientPhone]'",ARRAY_A);
    echo $wpdb->num_rows>0?'1':'0';
    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_export_to_pdf', 'export_to_pdf_callback' );
function export_to_pdf_callback() {
    echo file_get_contents(plugins_url('appointment-calendar-premium/menu-pages/tcpdf/examples/example_005.php'));
    die(); // this is required to return a proper result
}

add_action('admin_menu','appointment_calendar_menu');

// Admin dashboard Menu Pages For Booking Calendar Plugin

function appointment_calendar_menu() {
    //create new top-level menu 'appointment-calendar'
    $Menu = add_menu_page( 'Appointment Calendar', __('Appointment Calendar', 'appointzilla'), 'appzilla_calendar', 'appointment-calendar', '', 'dashicons-calendar');

    // Calendar Page
    $SubMenu1 = add_submenu_page( 'appointment-calendar', __('Admin Calendar', 'appointzilla'), __('Admin Calendar', 'appointzilla'), 'appzilla_calendar', 'appointment-calendar', 'display_calendar_page' );

    // Service Page
    $SubMenu4 = add_submenu_page( 'appointment-calendar', __('Services', 'appointzilla'), __('Services', 'appointzilla'), 'appzilla_services', 'service', 'display_service_page' );
    // manage Service Page
    $SubMenu5 = add_submenu_page( 'appointment-calendar', 'Manage Service', '', 'appzilla_services', 'appzilla_services', 'display_manage_service_page' );

    $SubMenu31 = add_submenu_page( 'appointment-calendar', __('Treatment', 'appointzilla'), __('Treatment', 'appointzilla'), 'appzilla_treatment', 'treatment', 'display_treatment_page' );

    $SubMenu32 = add_submenu_page( 'appointment-calendar', __('Diagnosis', 'appointzilla'), __('Diagnosis', 'appointzilla'), 'appzilla_diagnosis', 'diagnosis', 'display_diagnosis_page' );

    // Staff Page
    $SubMenu6 = add_submenu_page( 'appointment-calendar', 'Staffs', __('Staffs', 'appointzilla'), 'appzilla_staff', 'staff', 'display_staff_page' );
    // manage Staff Page
    $SubMenu7 = add_submenu_page( 'appointment-calendar', 'Manage Staff', '', 'appzilla_staff', 'manage-staff', 'display_manage_staff_page' );
    //cabinets
    $SubMenu8 = add_submenu_page( 'appointment-calendar', 'Cabinets', __('Cabinets', 'appointzilla'), 'appzilla_staff', 'appzilla_cabinets', 'display_cabinet_page' );

    // Client Page
    $SubMenu11 = add_submenu_page( 'appointment-calendar', __('Clients', 'appointzilla'), __('Clients', 'appointzilla'), 'appzilla_client', 'client', 'display_client_page' );
    $SubMenu12 = add_submenu_page( 'appointment-calendar', 'Client Manage', '','appzilla_client', 'client-manage', 'display_manage_client_page' );
    $SubMenu25 = add_submenu_page( 'appointment-calendar', 'Medical Cart Manage', __('Medical Cart', 'appointzilla'),'appzilla_medical_cart', 'medical_cart', 'display_medical_cart_page' );

    // Manage Appointment Page
    $SubMenu13 = add_submenu_page( 'appointment-calendar', __('Admin Appointments', 'appointzilla'), __('Appointments', 'appointzilla'), 'appzilla_calendar', 'manage-appointments', 'display_manage_appointment_page' );
    // Update Appointments Page
    $SubMenu14 = add_submenu_page( 'appointment-calendar', 'Update Appointment', '', 'appzilla_appointment', 'update-appointment', 'display_update_appointment_page' );


    $SubMenu30 = add_submenu_page('appointment-calendar', __('Report', 'appointzilla'), __('Report', 'appointzilla'), 'appzilla_report', 'report', 'display_report_page' );

    //Export Appointments & Client List
    $SubMenu19 = add_submenu_page('appointment-calendar', __('Export Lists', 'appointzilla'), __('Export Lists', 'appointzilla'), 'appzilla_report', 'export-lists', 'display_export_lists_page' );
    // Time-Off Page
    $SubMenu9 = add_submenu_page( 'appointment-calendar', 'Time Off', __('Time Off', 'appointzilla'), 'administrator', 'appzilla_holiday', 'display_timeoff_page' );
    // Update Time-Off Page
    $SubMenu10 = add_submenu_page( 'appointment-calendar', 'Update TimeOff', '', 'appzilla_holiday', 'update-timeoff', 'display_update_timeoff_page' );

    // Settings Page
    $SubMenu15 = add_submenu_page( 'appointment-calendar', __('Settings', 'appointzilla'), __('Settings', 'appointzilla'), 'administrator', 'app-calendar-settings', 'display_settings_page' );

    add_action( 'admin_print_styles-' . $Menu, 'calendar_css_js' );
    //calendar
    add_action( 'admin_print_styles-' . $SubMenu1, 'calendar_css_js' );
    //service
    add_action( 'admin_print_styles-' . $SubMenu4, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu5, 'other_pages_css_js' );
    //staff
    add_action( 'admin_print_styles-' . $SubMenu6, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu7, 'other_pages_css_js' );
    //cabinet
    add_action( 'admin_print_styles-' . $SubMenu8, 'other_pages_css_js' );
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
    add_action( 'admin_print_styles-' . $SubMenu25, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu30, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu31, 'other_pages_css_js' );
    add_action( 'admin_print_styles-' . $SubMenu32, 'other_pages_css_js' );

}// end of menu function

function calendar_css_js() {
    wp_register_script( 'jquery-custom',plugins_url('menu-pages/fullcalendar-assets-new/js/jquery-ui-1.8.23.custom.min.js', __FILE__), array('jquery'), true );
    wp_enqueue_script('full-calendar',plugins_url('/menu-pages/fullcalendar-assets-new/js/fullcalendar.min.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('datepicker-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('menu-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.menu.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('mask-js',plugins_url('/menu-pages/js/jquery.maskedinput-1.2.2.js', __FILE__),array('jquery'));

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


    wp_enqueue_script( 'jquery-ui',plugins_url('menu-pages/jquery-ui-custom/js/jquery-ui-1.10.4.custom.min.js', __FILE__), array('jquery') );
    wp_enqueue_script('datepicker-js',plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__),array('jquery','jquery-custom'));
    wp_enqueue_script('tooltip',plugins_url('/bootstrap-assets/js/bootstrap-tooltip.js', __FILE__),array('jquery'));
    wp_enqueue_script('bootstrap-affix',plugins_url('/bootstrap-assets/js/bootstrap-affix.js', __FILE__));
    wp_enqueue_script('bootstrap-application',plugins_url('/bootstrap-assets/js/application.js', __FILE__));
    wp_enqueue_script('fancybox-js',plugins_url('/bootstrap-assets/js/jquery.fancybox.js', __FILE__),array('jquery'));
    wp_enqueue_script('fancybox-thumbs-js',plugins_url('/bootstrap-assets/js/jquery.fancybox-thumbs.js', __FILE__),array('jquery'));
    wp_enqueue_script('mask-js',plugins_url('/menu-pages/js/jquery.maskedinput-1.2.2.js', __FILE__),array('jquery'));

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
            wp_enqueue_style('bootstrap',plugins_url('bootstrap-assets/css/bootstrap.css', __FILE__));
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

 //service page
 function display_service_page() {
     require_once("menu-pages/service.php");
 }
 //diagnosis page
 function display_diagnosis_page() {
     require_once("menu-pages/diagnosis.php");
 }
 //diagnosis page
 function display_treatment_page() {
     require_once("menu-pages/treatment.php");
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
//cabinet page
 function display_cabinet_page() {
     require_once("menu-pages/cabinet.php");
 }
 
 //time-off page
 function display_timeoff_page() {
     require_once("menu-pages/timeoff.php");
 }
 //update-time-off page
 function display_update_timeoff_page() {
     require_once("menu-pages/update-timeoff.php");
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

function display_report_page() {
    require_once("menu-pages/report.php");
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
    if (isset($_GET['page']) && ($_GET['page'] == 'medical_cart' || $_GET['page'] == 'update-appointment')) {
        wp_enqueue_media();
		wp_enqueue_script( 'my-admin-js',WP_PLUGIN_URL.'/my-plugin/my-admin.js', array('jquery') );
    }
}
add_action('print_medical_cart', 'print_medical_cart_callback');

function print_medical_cart_callback()
{
    $PDFPrinter = new PDFPrinter();
    $PDFPrinter->printMedicalCart($_GET);
    exit();
}

add_action( 'admin_init', 'include_classes' );

function include_classes()
{
    global $pluginDIR;
    $pluginDIR = plugin_dir_path( __FILE__ );
    include_once($pluginDIR.'/menu-pages/includes/AppointmentController.php');
    include_once($pluginDIR.'/menu-pages/includes/TreatmentController.php');
    include_once($pluginDIR.'/menu-pages/includes/DiagnosisController.php');
    include_once($pluginDIR.'/menu-pages/includes/CabinetController.php');
    include_once($pluginDIR.'/menu-pages/includes/MedicalCartController.php');
    include_once($pluginDIR.'/menu-pages/includes/PrintView.php');
    include_once($pluginDIR.'/menu-pages/includes/ReportController.php');
    include_once($pluginDIR.'/menu-pages/includes/ReportController.php');
    include_once($pluginDIR.'/menu-pages/includes/PdfPrinter.php');
    if ($_GET[action]=='print' && $_GET[page]='medical_cart'){
        do_action('print_medical_cart');
    }
}
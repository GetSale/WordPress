<?php
/**
* Plugin Name:  GetSale
* Plugin URI:   https://getsale.io
* Description:  GetSale &mdash; professional tool for creating popup windows.
* Version:      1.0.2
* Requires at least: 4.1
* Tested up to: 4.7
* Author:       GetSale Team
* Author URI:   https://getsale.io
* Text Domain:  getsale-popup-tool
* Domain Path:  /languages
**/

// Creating the widget
include 'getsale_options.php';

add_action('plugins_loaded', 'getsale_load_textdomain');

function getsale_load_textdomain() {
    load_plugin_textdomain( 'getsale-popup-tool', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

add_action('wp_enqueue_scripts', 'getsale_scripts_method');
add_filter('plugin_action_links', 'getsale_plugin_action_links', 10, 2);
add_action('wc_ajax_add_to_cart', 'getsale_ajax_add_to_cart');
add_action('woocommerce_restore_cart_item', 'getsale_ajax_add_to_cart');

function getsale_ajax_add_to_cart() {
    setcookie('getsale_add', true, time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

add_action('woocommerce_cart_item_removed', 'getsale_del_from_cart');

function getsale_del_from_cart() {
    setcookie('getsale_del', 'true', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

function getsale_plugin_action_links($actions, $plugin_file) {
    if (!(strpos($plugin_file, basename(__FILE__)) === false)) {
        $actions[] = '<a href="' . add_query_arg(array('page' => 'getsale_settings'), admin_url('plugins.php')) . '">' . __('Settings') . '</a>';
    }
    return $actions;
}

add_filter('plugin_row_meta', 'getsale_plugin_description_links', 10, 4);

function getsale_plugin_description_links($actions, $plugin_file) {
    if (!(strpos($plugin_file, basename(__FILE__)) === false)) {
        $actions[] = '<a href="' . add_query_arg(array('page' => 'getsale_settings'), admin_url('plugins.php')) . '">' . __('Settings') . '</a>';
    }
    return $actions;
}

add_filter('wc_add_to_cart_message', 'getsale_add_filter', 10, 4);

function getsale_add_filter($product_id) {
    add_action('wp_enqueue_scripts', 'getsale_scripts_add');
    return $product_id;
}

function getsale_script_cookie() {
    if (isset($_COOKIE['getsale_add'])) {
        add_action('wp_enqueue_scripts', 'getsale_scripts_add');
        setcookie('getsale_add', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }

    if (isset($_COOKIE['getsale_del'])) {
        add_action('wp_enqueue_scripts', 'getsale_scripts_del');
        setcookie('getsale_del', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }
};

add_action('init', 'getsale_script_cookie');

add_action('admin_enqueue_scripts', 'getsale_script_translate');

function getsale_script_translate() {
    wp_enqueue_script( 'getsale-main-script', dirname( plugin_basename(__FILE__) ) . 'js/admin.js');
    wp_localize_script( 'getsale-main-script', 'gs', array(
        'authorization' => __( 'Authorization', 'getsale-popup-tool' ),
        'enter_value' => __( 'Please, enter your Email and API Key from your GetSale account', 'getsale-popup-tool'),
        'registration' => __( 'If you don’t have GetSale account, you can register it <a href=\'https://getsale.io\'>here</a>', 'getsale-popup-tool'),
        'support' => __( 'Contact Us: <a href=\'mailto:support@getsale.io\'>support@getsale.io</a>', 'getsale-popup-tool' ),
        'getsale_ver' => '1.0.2',
        'congrats' => __( 'Congratulations! Your website is successfully linked to your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool'),
        'widgets_create' => __( 'You can start creating widgets for your website using your <a href=\'https://getsale.io\'>GetSale account</a>!', 'getsale-popup-tool'),
        'api_key_success' => __( 'API Key is correct', 'getsale-popup-tool' ),
        'email_success' => __( 'Email is correct', 'getsale-popup-tool' ),
        'error403' => __( 'Attention! API Key is invalid. Please, check and enter API Key once again', 'getsale-popup-tool'),
        'error404' => __( 'Attention! This Email isn’t registered on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool'),
        'error500' => __( 'Attention! This website is already in use on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool'),
        'error0' => __( 'You don\'t have Curl support in your PHP!', 'getsale-popup-tool'),
        'desc' => __( 'powerful cutting edge tool to create widgets and popups for your website!', 'getsale-popup-tool'),
        'description' => __( 'GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool'),
        'getsale_name' => __( 'GetSale Popup Tool', 'getsale-popup-tool'),
        'path' => plugins_url('ok.png', __FILE__),
    ));
}

function getsale_scripts_method() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_handle', plugins_url('js/main.js', __FILE__), array('jquery'));

        $datatoBePassed = array('project_id' => $options['getsale_project_id']);
        wp_localize_script('getsale_handle', 'getsale_vars', $datatoBePassed);

        wp_enqueue_script('getsale_handle');
    }
}

function getsale_scripts_add() {
    function getsale_echo_before_text() {
        echo '<div id=\'before_install\' style=\'display:none;\'>' . __('GetSale Popup Tool has been successfully installed', 'getsale-popup-tool') . '<br/>' . __('To get started, you must enter Email and API Key, from from your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool') . '</div>
<div class="wrap" id="after_install" style="display:none;">
<p><b>' . __('GetSale Popup Tool', 'getsale-popup-tool') . '</b> &mdash; ' . __('professional tool for creating popup windows', 'getsale-popup-tool') . '</p>
<p>' . __('GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool') . '</p>
</div>
</div>
<script type=\'text/javascript\'>
    window.onload = function () {
        if (document.location.search == \'?option=com_installer&view=install\') {
            document.getElementById(\'before_install\').style.display = \'block\';
        } else document.getElementById(\'after_install\').style.display = \'block\';
    }
</script>';
    }

    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/add.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_reg($regDomain, $email, $key, $url) {
    $domain = $regDomain;
    if (($domain == '') OR ($email == '') OR ($key == '') OR ($url == '')) {
        return;
    }

    if (!function_exists('curl_init')) {
        $json_result = '';
        $json_result->status = 'error';
        $json_result->code = 0;
        $json_result->message = 'No Curl!';
        return $json_result;
    };

    $ch = curl_init();
    $jsondata = json_encode(array(
        'email' => trim($email),
        'key' => $key,
        'url' => $url,
        'cms' => 'wordpress'
    ));

    $options = array(CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'), CURLOPT_URL => $domain . '/api/registration.json', CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $jsondata, CURLOPT_RETURNTRANSFER => true);

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    curl_close($ch);
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))) {
        } elseif ($json_result->status = 'error') {
        }
    }
    return $json_result;
}


function getsale_scripts_del() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/del.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_set_default_code() {
    $options = get_option('getsale_option_name');
    if (is_bool($options)) {
        $options = array();
        $options['getsale_email'] = '';
        $options['getsale_api_key'] = '';
        $options['getsale_project_id'] = '';
        $options['getsale_reg_error'] = '';
        update_option('getsale_option_name', $options);
    }
}

register_activation_hook(__FILE__, 'getsale_admin_actions');
register_uninstall_hook( __FILE__, 'getsale_plugin_uninstall');

function getsale_plugin_uninstall(){
    delete_option('getsale_option_name');
}

add_action('admin_menu', 'getsale_admin_actions');

function getsale_admin_actions() {
    if (current_user_can('manage_options')) {
        if (function_exists('add_meta_box')) {
            add_menu_page('GetSale Settings', 'GetSale', 'manage_options', 'getsale_settings', 'getsale_custom_menu_page', plugin_dir_url(__FILE__) . '/img/logo.png', 100);
        }
    }
}

function getsale_custom_menu_page() {
    $getsale_settings_page = new getsaleSettingsPage();
    if (!isset($getsale_settings_page)) {
        wp_die(__('Plugin GetSale has been installed incorrectly.'));
    }
    if (function_exists('add_plugins_page')) {
        add_plugins_page('GetSale Settings', 'GetSale', 'manage_options', 'getsale_settings', array(&$getsale_settings_page, 'getsale_create_admin_page'));
    }
}

$options = get_option('getsale_option_name');

if (is_admin()) {
    $options = get_option('getsale_option_name');

    if (is_bool($options)) {
        getsale_set_default_code();
    }

    $reg_domain = 'https://getsale.io';
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['getsale_option_name']))) {
        $options = $_REQUEST['getsale_option_name'];
        if (($options['getsale_email'] !== '') && ($options['getsale_api_key'] !== '') && ($options['getsale_project_id'] == '')) {
            $reg_ans = getsale_reg($reg_domain, $options['getsale_email'], $options['getsale_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_project_id'] = $reg_ans->payload->projectId;
                    $getsale_options['getsale_reg_error'] = '';
                    $getsale_options['getsale_email'] = $options['getsale_email'];
                    $getsale_options['getsale_api_key'] = $options['getsale_api_key'];
                    update_option('getsale_option_name', $getsale_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                } elseif ($reg_ans->status = 'error') {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_reg_error'] = $reg_ans->code;
                    $getsale_options['getsale_project_id'] = '';
                    $getsale_options['getsale_email'] = $options['getsale_email'];
                    $getsale_options['getsale_api_key'] = $options['getsale_api_key'];
                    update_option('getsale_option_name', $getsale_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                }
            }
        }
    } else {
        $options = get_option('getsale_option_name');
        $my_settings_page = new getsaleSettingsPage();
    }
}


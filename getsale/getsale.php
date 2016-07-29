<?php
/*
Plugin Name: getSale
Plugin URI: http://getsale.io/
Description: getSale — профессиональный инструмент для создания popup-окон
Version: 1.0.0
Author: getSale Team
Author URI: http://getsale.io/
*/

// Creating the widget

include 'getsale_options.php';

add_action('wp_enqueue_scripts', 'getsale_scripts_method');
add_filter('plugin_action_links', 'getsale_plugin_action_links', 10, 2);

function getsale_plugin_action_links($actions, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $actions;
    $settings_link = '<a href="options-general.php?page=getsale_settings">Настройки</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'getsale_plugin_description_links', 10, 4);

function getsale_plugin_description_links($meta, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $meta;
    $meta[] = '<a href="options-general.php?page=getsale_settings">Настройки</a>';
    return $meta;
}

$options = get_option('getsale_option_name');

if (is_admin()) {
    $options = get_option('getsale_option_name');

    if (is_bool($options)) {
        getsale_set_default_code();
    }

    $reg_domain = 'http://edge.getsale.io';
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['getsale_option_name']))) {
        $options = $_REQUEST['getsale_option_name'];
        if (($options['getsale_email'] !== '') && ($options['getsale_api_key'] !== '') && ($options['getsale_id'] == '')) {
            $reg_ans = getsale_register($reg_domain, $options['getsale_email'], $options['getsale_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_id'] = $reg_ans->payload->projectId;
                    $getsale_options['getsale_reg_error'] = '';
                    $getsale_options['getsale_email'] = $options['getsale_email'];
                    $getsale_options['getsale_api_key'] = $options['getsale_api_key'];
                    update_option('getsale_option_name', $getsale_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                } elseif ($reg_ans->status = 'error') {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_reg_error'] = $reg_ans->code;
                    $getsale_options['getsale_id'] = '';
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


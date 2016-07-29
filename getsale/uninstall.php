<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
$option_name = 'getSale_option_name';

delete_option($option_name);
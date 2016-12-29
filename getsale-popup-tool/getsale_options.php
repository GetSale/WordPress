<?php

class getsaleSettingsPage {
    public $options;
    public $settings_page_name = 'getsale_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'getsale_add_plugin_page'));
        add_action('admin_init', array($this, 'getsale_page_init'));
        $this->options = get_option('getsale_option_name');
    }

    public function getsale_add_plugin_page() {
        add_options_page('GetSale Settings', 'GetSale', 'manage_options', $this->settings_page_name, array($this, 'getsale_create_admin_page'));
    }

    public function getsale_create_admin_page() {
        $this->options = get_option('getsale_option_name');

        //ToDo вывод email админа сайта поумолчанию
        //        if ((isset($this->options['getsale_email'])) && ('' !== $this->options['getsale_email'])) {
        //            $email = $this->options['getsale_email'];
        //        } else $email = get_option('admin_email');
        ?>
        <script type="text/javascript">
            <?php include('js/admin.js'); ?>
        </script>
        <div id='getsale_site_url' style='display: none'><?php echo get_site_url(); ?></div>
        <div class='wrap'>
            <div id='wrapper'>
                <form id='settings_form' method='post' action='options.php'>
                    <h1><?php _e('GetSale Popup Tool'); ?></h1>
                    <?php
                    getsale_echo_before_text();
                    settings_fields('getsale_option_group');
                    do_settings_sections('getsale_settings');
                    ?>
                    <input type='submit' name='submit_btn'>
                </form>
            </div>
        </div>
        <?php
    }

    public function getsale_page_init() {
        register_setting('getsale_option_group', 'getsale_option_name', array($this, 'getsale_sanitize'));

        add_settings_section('setting_section_id', '', // Title
            array($this, 'getsale_print_section_info'), $this->settings_page_name);

        add_settings_field('email', __('Email', 'getsale-popup-tool'), array($this, 'getsale_email_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_api_key', __('API Key', 'getsale-popup-tool'), array($this, 'getsale_api_key_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_reg_error', 'getsale_reg_error', array($this, 'getsale_reg_error_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_project_id', 'getsale_project_id', array($this, 'getsale_project_id_callback'), $this->settings_page_name, 'setting_section_id');
    }

    public function getsale_sanitize($input) {
        $new_input = array();

        if (isset($input['getsale_email'])) $new_input['getsale_email'] = trim($input['getsale_email']);

        if (isset($input['getsale_project_id'])) $new_input['getsale_project_id'] = $input['getsale_project_id'];

        if (isset($input['getsale_api_key'])) $new_input['getsale_api_key'] = trim($input['getsale_api_key']);

        if (isset($input['getsale_reg_error'])) $new_input['getsale_reg_error'] = $input['getsale_reg_error'];

        return $new_input;
    }

    public function getsale_print_section_info() {
    }

    public function getsale_email_callback() {
        printf('<input type="text" id="getsale_email" name="getsale_option_name[getsale_email]" value="%s" title="%s"/>', isset($this->options['getsale_email']) ? esc_attr(trim($this->options['getsale_email'])) : '', __('Enter Email', 'getsale-popup-tool'));
    }

    public function getsale_api_key_callback() {
        printf('<input type="text" id="getsale_api_key" name="getsale_option_name[getsale_api_key]" value="%s" title="%s" />', isset($this->options['getsale_api_key']) ? esc_attr(trim($this->options['getsale_api_key'])) : '', __('Enter API Key', 'getsale-popup-tool'));
    }

    public function getsale_reg_error_callback() {
        printf('<input type="text" id="getsale_reg_error" name="getsale_option_name[getsale_reg_error]" value="%s" />', isset($this->options['getsale_reg_error']) ? esc_attr($this->options['getsale_reg_error']) : '');
    }

    public function getsale_project_id_callback() {
        printf('<input type="text" id="getsale_project_id" name="getsale_option_name[getsale_project_id]" value="%s" />', isset($this->options['getsale_project_id']) ? esc_attr($this->options['getsale_project_id']) : '');
    }
}
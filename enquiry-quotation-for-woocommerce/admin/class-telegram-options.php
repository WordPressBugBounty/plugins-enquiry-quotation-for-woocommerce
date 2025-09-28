<?php

defined('ABSPATH') || exit;

class Class_Pi_Eqw_Telegram_Options {
    public $plugin_name;
    private $settings = array();
    private $active_tab;
    private $this_tab = 'telegram';
    private $tab_name = 'Telegram (PRO)';
    private $setting_key = 'pi_eqw_telegram_setting';
    private static $instance = null;

    public static function get_instance($plugin_name = null) {
        if (self::$instance == null) {
            self::$instance = new self($plugin_name);
        }
        return self::$instance;
    }

    private function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        add_action('init', array($this, 'init'));
        $this->active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'default';
        if ($this->this_tab == $this->active_tab) {
            add_action($this->plugin_name . '_tab_content', array($this, 'tab_content'));
        }
        add_action($this->plugin_name . '_tab', array($this, 'tab'), 4);
        $this->register_settings();
        if (defined('PI_EQW_DELETE_SETTING') && PI_EQW_DELETE_SETTING) {
            $this->delete_settings();
        }
    }

    function init() {
        $this->settings = array(
            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>'Receive Enquiry detail on your Telegram channel or group', 'type'=>'setting_category'),
            array(
                'field' => 'pi_eqw_telegram_bot_token',
                'label' => __('Telegram bot token', 'pisol-enquiry-quotation-woocommerce'),
                'type' => 'text',
                'desc' => __('Enter your Telegram bot token here. <br> <a href="https://www.piwebsolution.com/create-a-telegram-bot-using-botfather-and-get-the-api-token/" target="_blank">How to create bot and get token</a>', 'pisol-enquiry-quotation-woocommerce'),
                'default' => '',
                'pro' => true,
            ),
            array(
                'field' => 'pi_eqw_telegram_channel_ids',
                'label' => __('Telegram Channel or Group ids', 'pisol-enquiry-quotation-woocommerce'),
                'type' => 'text',
                'desc' => __('Add the channel or group id in which bot can send message, make sure the bot is assigned as a user to the group and if it is channel then bot should be assigned as an admin to the channel <br> <a href="https://www.piwebsolution.com/create-a-telegram-bot-using-botfather-and-get-the-api-token/#get-channel-id" target="_blank">How to get channel id where bot will send message</a>', 'pisol-enquiry-quotation-woocommerce'),
                'default' => '',
                'pro' => true,
            ),
        );
        $this->register_settings();
    }

    function delete_settings() {
        foreach ($this->settings as $setting) {
            delete_option($setting['field']);
        }
    }

    function register_settings() {
        foreach ($this->settings as $setting) {
            pisol_class_form_eqw::register_setting($this->setting_key, $setting);
        }
    }

    function tab() {
        ?>
        <a class="hide-pro px-3 text-light d-flex align-items-center border-left border-right <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . sanitize_text_field( $_GET['page'] ) . '&tab=' . $this->this_tab ) ); ?>">
            <span class="dashicons dashicons-megaphone"></span> <?php echo esc_html($this->tab_name); ?>
        </a>
        <a class="px-3 text-light d-flex align-items-center border-left border-right bg-secondary" href="<?php echo esc_url( 'https://www.piwebsolution.com/user-documentation-product-enquiry-for-woocommerce/' ); ?>" target="_blank" rel="noopener noreferrer">
           <span class="dashicons dashicons-book"></span> Documentation
        </a>
        <?php
    }

    function tab_content() {
        ?>
        <form method="post" action="options.php" class="pisol-setting-form">
            <?php settings_fields($this->setting_key); ?>
            <?php
            foreach ($this->settings as $setting) {
                new \pisol_class_form_eqw($setting, $this->setting_key);
            }
            ?>
            <input type="submit" class="my-3 btn btn-primary btn-md" value="<?php echo esc_attr__('Save Option', 'pisol-enquiry-quotation-woocommerce'); ?>" />
        </form>
        <?php
    }
}

Class_Pi_Eqw_Telegram_Options::get_instance($this->plugin_name);
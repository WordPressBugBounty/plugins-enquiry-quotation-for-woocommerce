<?php 
/**
 * v1.0.0
 */

defined('ABSPATH') || exit;

class Pi_Eqw_Tracker{

    private $plugin_slug;
    private $enable_tracking;
    private $enable_tracking_action;
    private $plugin_name;
    private $version;
    private $url;
    public function __construct($plugin_name, $plugin_slug, $version) {
        $this->plugin_slug = $plugin_slug;

        $this->enable_tracking = 'pisol_'.$this->plugin_slug;
        $this->enable_tracking_action = 'pisol_'.$this->plugin_slug.'_action';
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->url = 'https://www.piwebsolution.com/plugin-tracker/'; 
        
        add_action('admin_notices', array($this, 'show_tracker_notice'));

        add_action('admin_post_' . $this->enable_tracking_action, array($this, 'handle_tracker_action'));

        add_action('deactivated_' . $this->plugin_slug, array($this, 'send_deactivation_data'));
    }

    

    public function show_tracker_notice() {
        //delete_option($this->enable_tracking);
        if (!empty(get_option($this->enable_tracking, ''))) {
            return; 
        }

        $notice = '<div class="notice notice-error is-dismissible">';
        $notice .= '<h4>Help to Improve ' . esc_html($this->plugin_name) . ' plugin</h4>';
        $notice .= '<p>'.__("Hi, your support can make a big difference!", 'auto-assign-order-tags-for-woocommerce').'</p>';
        $notice .= '<p>'.__("By choosing to anonymously share <b>non-sensitive plugin usage data</b> related to this plugin, you’ll help to improve this plugin, shape new features, and deliver a better overall experience.", 'auto-assign-order-tags-for-woocommerce').'</p>';
        $notice .= '<p>'.__("Rest assured, no personal data is collected.", 'auto-assign-order-tags-for-woocommerce').'</p>';

        $notice .= '<p>';
        $notice .= sprintf(
            '<a href="%s" class="button button-primary" style="margin-right:20px;">%s</a>',
            esc_url(admin_url('admin-post.php?enable=1&action=' . $this->enable_tracking_action)),
            __('I Will Help', 'auto-assign-order-tags-for-woocommerce')
        );
        $notice .= sprintf(
            '<a href="%s" class="button button-primary">%s</a>',
            esc_url(admin_url('admin-post.php?enable=0&action=' . $this->enable_tracking_action)),
            __('I Don\'t Help', 'auto-assign-order-tags-for-woocommerce')
        );
        $notice .= '</p>';
        $notice .= '</div>';
        echo $notice;
        
    }

    public function handle_tracker_action() {
        if (isset($_GET['enable']) && in_array($_GET['enable'], array('1', '0'))) {
            $enable = $_GET['enable'] === '1' ? 'enable' : 'disable';
            update_option($this->enable_tracking, $enable);
            $redirect_url = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : admin_url();

            if ($enable === 'enable') {
                $this->send_activation_data('enable');
            } 

            wp_safe_redirect($redirect_url);
            exit;
        }
    }

    public function send_deactivation_data() {
        $tracking = get_option($this->enable_tracking, '');
        if ($tracking !== 'enable') {
            return; // Only send data if tracking was enabled
        }
        
        $this->send_activation_data('disable');
    }

    public function send_activation_data($action) {
        
        $data = array(
            'plugin_slug' => $this->plugin_slug,
            'version' => $this->version,
            'site_url' => get_site_url(),
            'action' => $action,
        );

        // Make the request non-blocking by setting 'blocking' => false
        wp_remote_post($this->url, array(
            'body' => wp_json_encode($data),
            'headers' => array('Content-Type' => 'application/json'),
            'blocking' => false,
        ));
    }
}

new Pi_Eqw_Tracker(
    'Enquiry Quotation for WooCommerce',
    'enquiry-quotation-for-woocommerce',
    PISOL_ENQUIRY_QUOTATION_WOOCOMMERCE_VERSION
);
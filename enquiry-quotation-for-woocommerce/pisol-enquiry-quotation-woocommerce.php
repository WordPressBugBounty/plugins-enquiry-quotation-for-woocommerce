<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              piwebsolution.com
 * @since             2.2.34.0
 * @package           Pisol_Enquiry_Quotation_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Enquiry Quotation for WooCommerce
 * Plugin URI:        piwebsolution.com
 * Description:       Product enquiry and quotation plugin for WooCommerce that can save enquiry and email the enquiry as well
 * Version:           2.2.34.0
 * Author:            PI Websolution
 * Author URI:        https://www.piwebsolution.com/faq-for-woocommerce-product-enquiry-quotation/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pisol-enquiry-quotation-woocommerce
 * Domain Path:       /languages
 * WC tested up to: 9.6.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!is_plugin_active( 'woocommerce/woocommerce.php')){
    function pi_eqw_free_notification_my_error_notice() {
        ?>
        <div class="error notice">
            <p><?php esc_html_e( 'Please Install and Activate WooCommerce plugin, without that this plugin cant work', 'pisol-enquiry-quotation-woocommerce' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_eqw_free_notification_my_error_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}

if(is_plugin_active( 'enquiry-quotation-for-woocommerce-pro/pisol-enquiry-quotation-woocommerce.php')){
    function pi_eqw_notification_my_pro_notice() {
        ?>
        <div class="error notice">
            <p><?php esc_html_e( 'You have the PRO version of Enquiry plugin active, deactivate it then you can use free version', 'pisol-enquiry-quotation-woocommerce'); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_eqw_notification_my_pro_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}else{

/**
 * Declare compatible with HPOS new order table 
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Currently plugin version.
 * Start at version 2.2.34.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PISOL_ENQUIRY_QUOTATION_WOOCOMMERCE_VERSION', '2.2.34.0' );
define( 'PI_EQW_PRICE', '$25' );
define( 'PI_EQW_BUY_URL', 'https://www.piwebsolution.com/cart/?add-to-cart=1734&variation_id=1735&utm_campaign=enquiry-cart&utm_source=website&utm_medium=direct-buy' );
define( 'PI_EQW_DELETE_SETTING', false);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pisol-enquiry-quotation-woocommerce-activator.php
 */
function activate_pisol_enquiry_quotation_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pisol-enquiry-quotation-woocommerce-activator.php';
	Pisol_Enquiry_Quotation_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pisol-enquiry-quotation-woocommerce-deactivator.php
 */
function deactivate_pisol_enquiry_quotation_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pisol-enquiry-quotation-woocommerce-deactivator.php';
	Pisol_Enquiry_Quotation_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pisol_enquiry_quotation_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_pisol_enquiry_quotation_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pisol-enquiry-quotation-woocommerce.php';

function pisol_eqw_plugin_link( $links ) {
	$links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=pisol-enquiry-quote' ) ) . '">' . __( 'Settings', 'pisol-enquiry-quotation-woocommerce' ) . '</a>',
        '<a style="color:#0a9a3e; font-weight:bold;" target="_blank" href="' . esc_url(PI_EQW_BUY_URL) . '">' . __( 'Buy PRO Version','pisol-enquiry-quotation-woocommerce' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pisol_eqw_plugin_link' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pisol_enquiry_quotation_woocommerce() {

	$plugin = new Pisol_Enquiry_Quotation_Woocommerce();
	$plugin->run();

}
run_pisol_enquiry_quotation_woocommerce();

}
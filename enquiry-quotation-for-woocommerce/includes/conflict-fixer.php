<?php 

namespace PISOL\EQW\ADMIN;
if ( ! defined( 'ABSPATH' ) ) exit;

class ConflictFixer{

protected static $instance = null;

public static function get_instance() {
    if ( is_null( self::$instance ) ) {
        self::$instance = new self();
    }
    return self::$instance;
}

function __construct(){
    add_action( 'admin_enqueue_scripts', array($this,'remove_conflict_causing_scripts'), 1000 );

    add_filter( "get_post_metadata", array($this,'make_safe_serialize'), 10, 4 );
}

function remove_conflict_causing_scripts(){
    if(isset($_GET['page']) && $_GET['page'] == 'pisol-enquiry-quote'){
        wp_dequeue_style( 'nasa_back_end-css' );

        /* color picker gets disabled because of this script */
        wp_dequeue_script( 'print-invoices-packing-slip-labels-for-woocommerce' );
    }
}

function make_safe_serialize($value, $object_id, $meta_key, $single){
    global $wpdb;
    if($meta_key == 'pi_products_info'){
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Needed direct query for serialized meta value
        $pi_products_info = $wpdb->get_var( $wpdb->prepare(
            "SELECT meta_value 
             FROM {$wpdb->postmeta} 
             WHERE post_id = %d AND meta_key = %s",
            $object_id, 'pi_products_info'
        ) );
        return is_serialized($pi_products_info) ? @unserialize($pi_products_info, ['allowed_classes' => false]) : [];
    }

    return $value;
}   

}

ConflictFixer::get_instance();
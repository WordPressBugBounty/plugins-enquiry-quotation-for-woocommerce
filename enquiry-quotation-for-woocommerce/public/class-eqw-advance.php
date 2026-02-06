<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class class_eqw_advance{

    public $hide_price;

    function __construct(){
        
        $this->hide_price = get_option('pi_eqw_hide_price','no'); // all -> hide for all, no->done hide, guest -> hide for guest visitors

        add_filter( 'woocommerce_is_purchasable', array($this,'remove_add_to_cart'), 10, 2);

        if( $this->hide_price != 'no'){

            /**
             * this can remove price from loop and single product page
             */
            add_filter( 'woocommerce_variable_sale_price_html', array($this,'removePrice'), 10, 2 );
            add_filter( 'woocommerce_variable_price_html', array($this,'removePrice'), 10, 2 );
            add_filter( 'woocommerce_get_price_html', array($this,'removePrice'), 10, 2 );
           
        }

        add_action( 'template_redirect', array($this,'redirectToEnquiryCart') );

    }

    function remove_add_to_cart($purchasable, $product ){
        $pi_eqw_remove_add_to_cart = 0;
        if($pi_eqw_remove_add_to_cart == 1 /*|| $this->hidePrice()*/){
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

            wp_register_style( 'pisol-dummy-handle', false );
            wp_enqueue_style( 'pisol-dummy-handle' );

            wp_add_inline_style( 'pisol-dummy-handle', '.single_variation_wrap, .woocommerce-variation-add-to-cart.variations_button{
                display:none;
            }' );

            return false;
        }
        return $purchasable;
    }

    function removePrice( $price, $product ) {
        if ( $this->hidePrice() ) $price = '';
        return $price;
    }

    function hidePrice(){
        switch($this->hide_price){
            case 'no':
                return false;
            break;

            case 'all':
                return true;
            break;

            case 'guest':
                if(is_user_logged_in()){
                    return false;
                }else{
                    return true;
                }
            break;
        }
        
        return false;
    }

    static function checkHidePrice(){
        $hide_price = get_option('pi_eqw_hide_price','no');
        switch($hide_price){
            case 'no':
                return false;
            break;

            case 'all':
                return true;
            break;

            case 'guest':
                if(is_user_logged_in()){
                    return false;
                }else{
                    return true;
                }
            break;
        }
        
        return false;
    }

    function redirectToEnquiryCart() {
        $pi_eqw_redirect_to_enquiry_cart = get_option('pi_eqw_redirect_to_enquiry_cart',0);

        if($pi_eqw_redirect_to_enquiry_cart == 1){
            if ( is_cart() || is_checkout() ){
            
                global $woocommerce;
                // Redirect to check out url
                $enquiry_cart = get_option('pi_eqw_enquiry_cart',"");
                if($enquiry_cart != ""){
                    $url = get_permalink($enquiry_cart);
                    wp_redirect( $url, '301' );
                    exit;
                }

            }
        }
        
    }
   
}

new class_eqw_advance();


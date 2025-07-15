<?php

class class_eqw_enquiry_cart{

    function __construct(){
        add_action('wc_ajax_pi_add_to_enquiry', array($this, 'add_to_enquiry') ); 
        add_action('wp_ajax_pi_add_to_enquiry', array($this, 'add_to_enquiry') ); 
        add_action('wp_ajax_nopriv_pi_add_to_enquiry', array($this, 'add_to_enquiry') );

        add_action('wc_ajax_pi_remove_product', array($this, 'pi_remove_product') ); 
        add_action('wp_ajax_pi_remove_product', array($this, 'pi_remove_product') ); 
        add_action('wp_ajax_nopriv_pi_remove_product', array($this, 'pi_remove_product') );

        add_action('wc_ajax_pi_update_products', array($this, 'pi_update_products') ); 
        add_action('wp_ajax_pi_update_products', array($this, 'pi_update_products') ); 
        add_action('wp_ajax_nopriv_pi_update_products', array($this, 'pi_update_products') );

        /**
         * This is needed as wc session is not created for non-loged in users
         */
        add_action( 'woocommerce_init',  array($this, 'startSession') );
    }

    function startSession(){
        //self::deleteProductsFromEnquirySession();
        if(isset(WC()->session)){
            if ( !is_admin() && !WC()->session->has_session() ) {
                WC()->session->set_customer_session_cookie( true );
            }
        }
    } 

    function add_to_enquiry(){
        if(isset($_POST['id']) && isset($_POST['variation_id'])){
            $id = absint($_POST['id']);
            $quantity = (int)(isset($_POST['quantity']) ? $_POST['quantity'] : 1);
            $variation = absint($_POST['variation_id']);
            $variation_detail = $this->sanitizeVariationDetail($_POST['variation_detail']);
            $products = $this->addProductToEnquirySession($id, $quantity, $variation, $variation_detail);
            wp_send_json($products);
        }
        die;
    }

    function sanitizeVariationDetail($variation_detail){
        if(is_array($variation_detail) && count($variation_detail) > 0){
            $sanitized_detail = array();
            foreach($variation_detail as $key => $val){
                $sanitized_detail[sanitize_text_field($key)] = sanitize_text_field($val);
            }
            return $sanitized_detail;
        }
        return 0;
    }

    function pi_remove_product(){
        $hash = sanitize_text_field($_POST['hash']);
        $products = $this->removeProductFromEnquirySession($hash);
        ob_start();
        pisol_table_row($products);
        $cart = ob_get_contents(); // read ob2 ("b")
        ob_end_clean();
        $data = array(
            'cart'=> $cart,
            'pisol_products'=> self::filter_message($products)
        );  
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        die;
    }

    static function filter_message($products){
        if(is_array($products) && count($products) > 0){
            foreach($products as $key => $product){
                $products[$key]['message'] = wp_unslash($products[$key]['message']);
            }
        }
        return $products;
    }

    static function get_image($product_id, $variation_id){
        if(empty($variation_id)){
            $product = wc_get_product($product_id);
        }else{
            $product = wc_get_product($variation_id);
        }

        $image_id = $product->get_image_id();

        $placeholder = wc_placeholder_img_src( 'thumbnail' );

        if(!empty($image_id)){
            $src = wp_get_attachment_image_src($image_id, 'thumbnail');
            $image_src = isset($src[0]) ? $src[0] : $placeholder;
        }else{
            $image_src = $placeholder;
        }
        return sprintf('<img src="%s" class="pi-eqw-product-thumb">',esc_url($image_src));
    }

    function pi_update_products(){
       
        $products = $this->addProductsToEnquirySession($_POST['products']);
        ob_start();
        pisol_table_row($products);
        $cart = ob_get_contents(); // read ob2 ("b")
        ob_end_clean();
        $data = array(
            'cart'=> $cart,
            'pisol_products'=> self::filter_message($products)
        );  
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        die;
    }

    function addProductsToEnquirySession($products){
        $products = $this->sanitizeProducts($products);
        if(isset(WC()->session)){
        WC()->session->set( 'pi_product_enquiries', $products );
        }
        return self::getProductsInEnquirySession();
    }

    function sanitizeProducts($products){
        if(is_array($products)){
            foreach($products as $key =>$product){
                $products[$key]['id'] = (int)$products[$key]['id'];
                $products[$key]['variation'] = (int)$products[$key]['variation'];
                $products[$key]['variation_detail'] = $this->sanitizeVariationDetail($products[$key]['variation_detail']);

                $products[$key]['quantity'] = (int) $products[$key]['quantity'];
                $products[$key]['message'] = sanitize_text_field($products[$key]['message']);
                if($products[$key]['quantity'] <= 0){
                    unset($products[$key]);
                }
            }
        }
        return $products;
    }

    static function deleteProductsFromEnquirySession(){
        if(isset(WC()->session)){
        WC()->session->__unset( 'pi_product_enquiries');
        }
    }

    function addProductToEnquirySession($id, $quantity, $variation, $variation_detail){
        $products = self::getProductsInEnquirySession();
        $message = '';

        if(self::is_variable($id) && $variation == false){
            return false;
        }

        $new_product = array(
            'id'=>(int)$id,
            'quantity'=>(int)$quantity,
            'variation'=>(int)$variation,
            'variation_detail'=>$variation_detail,
            'message'=>strip_tags($message)
        );

        $hash = self::hashGenerator($new_product['id'], $variation_detail);

        if($this->checkProductPresentInEnquirySession($hash)){
            /**
             * this will increment it by one, 
             * as we are not entering the new quantity variable
             */
            return $this->changeQuantityInEnquirySession($hash, $new_product['quantity']);

        }else{
            
            $products[$hash] = $new_product;
        }
        
        return $this->addProductsToEnquirySession($products);
    }

    static function is_variable($id){
        $product = wc_get_product($id);
        if($product->is_type('variable')){
            return true;
        }
        return false;
    }

    static function hashGenerator($id, $variation_details){
        $variation_value = "";
        if(is_array($variation_details) && count($variation_details) > 0){
            foreach($variation_details as $key => $variation_detail){
                $variation_value .= $variation_detail;
            }
        }
        $hash = md5($id.$variation_value);
        return $hash;
    }
    
    static function getProductsInEnquirySession(){
        $products = array();
        if(isset(WC()->session)){
            $products = WC()->session->get('pi_product_enquiries');
            $products = self::removeTrashedProduct($products);
            if($products === null) return array();
        }
        return $products;
    }

    static function removeTrashedProduct($products){
        if($products === null) return array();

        foreach($products as $key => $product){
            $product_status = get_post_status( $product['id'] );
            if('publish' !== $product_status){
                unset($products[$key]);
            }
        }
        return $products;
    }

    static function isThereProductsInEnquirySession(){
        $products = self::getProductsInEnquirySession();
        if(is_array($products) && count($products) > 0){
            return true;
        }
        return false;
    }

    /**
     * return true if product present in cart
     */
    function checkProductPresentInEnquirySession($hash){
        $products = self::getProductsInEnquirySession();
        $present = false;

        if(isset($products[$hash])){
            $present = true;
        }

        return $present;
        
    }

    /**
     * If $new_quantity is false will increment the existing quantity
     * if it is not false and is a number then will it will update existing quantity
     * if new quantity is zero it will remove the product from list
     */
    function changeQuantityInEnquirySession($hash, $new_quantity = false){
        $products = self::getProductsInEnquirySession();

        if($new_quantity === 0){
            return $this->removeProductFromEnquirySession($hash);
        }

        if(is_array($products) && count($products) > 0){
                    if($new_quantity){
                        $products[$hash]['quantity'] = $products[$hash]['quantity']+$new_quantity;
                    }else{
                        $products[$hash]['quantity'] = $products[$hash]['quantity']+1;
                    }
        }

        return $this->addProductsToEnquirySession($products);

    }

    function removeProductFromEnquirySession($hash){
        $products = self::getProductsInEnquirySession();
        if(is_array($products) && count($products) > 0){
            
            unset($products[$hash]);
               
        }

        return $this->addProductsToEnquirySession($products);
    }
}

new class_eqw_enquiry_cart();
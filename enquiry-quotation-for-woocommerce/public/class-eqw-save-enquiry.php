<?php

class class_eqw_save_enquiry{

    public $items;
    public $products;
    public $title;
    public $name;
    public $email;
    public $phone;
    public $subject;
    public $message;
    public $enquiry_id;
    
    function __construct($items){
        $this->items = $items;
        $this->products =  class_eqw_enquiry_cart::getProductsInEnquirySession();
    
    }

    function getOrderTitle(){
        $this->title = isset($_POST['pi_name']) ? sanitize_text_field($_POST['pi_name']) : __('Order');
        return $this->title;
    }

    function getName(){
        $this->name = isset($_POST['pi_name']) ? sanitize_text_field($_POST['pi_name']) : "";
        return $this->name;
    }

    function getEmail(){
        $this->email = isset($_POST['pi_email']) ? sanitize_email($_POST['pi_email']) : "";
        return $this->email;
    }

    function getPhone(){
        $this->phone = isset($_POST['pi_phone']) ? sanitize_text_field($_POST['pi_phone']) : "";
        return $this->phone;
    }

    function getSubject(){
        $this->subject = isset($_POST['pi_subject']) ? sanitize_text_field($_POST['pi_subject']) : "";
        return $this->subject;
    }

    function getMessage(){
        $this->message = isset($_POST['pi_message']) ? sanitize_text_field($_POST['pi_message']) : "";
        return $this->message;
    }

    function createEnquiry(){
        $products_info = $this->staticProducts();
        
        if(!is_serialized($products_info)) return false;
        
        $arg = $this->newOrderArgument();
        $return  =  wp_insert_post($arg);
        if($return  == 0 || is_wp_error($return) ){
            return false;
        }

        
        update_post_meta($return, 'pi_products_info', wp_slash($products_info));

        $products_id = $this->products_array();
        update_post_meta($return, 'pi_products_id', $products_id);
        $this->enquiry_id = $return;

        do_action('pisol_eqw_enquiry_saved', $return);
        
        return $return;
    }

    function newOrderArgument(){
        $arg = array(
            'post_title' => $this->getOrderTitle(),
            'post_type'=>'pisol_enquiry',
            'post_status'   => 'publish',
            'meta_input' => array(
                'pi_name' => $this->getName(),
                'pi_email' => $this->getEmail(),
                'pi_phone'=> $this->getPhone(),
                'pi_subject'=> $this->getSubject(),
                'pi_message'=> $this->getMessage()
            )
        );
        return $arg;
    }

    function staticProducts(){
        $static_products = array();
        foreach($this->products as $product){
            $static_products[] = $this->staticProduct($product);
        }
        return serialize($static_products);
    }

    function products_array(){
        $products_id = array();
        foreach($this->products as $product){
            $products_id[] = $product['id'];
        }
        return $products_id;
    }

    function staticProduct($product){
        $product_obj = wc_get_product($product['id']);
        $product_permalink = $product_obj->get_permalink();
        $image_id = $product_obj->get_image_id();
        $img = wp_get_attachment_thumb_url($image_id);
        $price = strip_tags(wc_price(class_eqw_enquiry_shortcode::get_price_simple_variation($product_obj, $product['variation'])));
        $variation_id = ($product['variation'] != false ? (int)$product['variation'] : false);
        $variation_detail = $this->variation_detail($product_obj, $product['variation_detail']);
        $return = array(
            'name' => $product_obj->get_name(),
            'img' => $img,
            'link'=> $product_permalink,
            'price'=> $price,
            'variation'=> $variation_id,
            'variation_detail'=> $variation_detail,
            'quantity' => $product['quantity'],
            'message' => strip_tags($product['message'])
        );
        return ($return);
    }

    function variation_detail($product_obj, $product_variation_detail){
        $variations_label = class_eqw_enquiry_shortcode::get_variations($product_obj, $product_variation_detail);
        $return = "";
        if(is_array($variations_label)){
            foreach ($variations_label as $key => $value){
                $return .= esc_html($key).'|'.esc_html($value).',';
            }
        }
        return $return;
    }

    function save(){
        return $this->createEnquiry();
    }
}
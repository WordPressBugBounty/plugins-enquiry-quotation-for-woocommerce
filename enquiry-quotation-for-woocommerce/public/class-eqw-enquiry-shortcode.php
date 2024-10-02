<?php

class class_eqw_enquiry_shortcode{

    public $products;

    function __construct(){
        
        add_shortcode('pisol_enquiry_cart',array($this, 'enquiry_cart_template'));
        /** this shortcode [enquiry_cart] is to support old sites */
        add_shortcode('enquiry_cart',array($this, 'enquiry_cart_template'));
        add_action('wp_ajax_get_cart_on_load', array($this,'cartOnFirstLoad'));
        add_action('wp_ajax_nopriv_get_cart_on_load', array($this,'cartOnFirstLoad'));
    }

    function enquiry_cart_template(){
        ob_start();  
        $this->products = class_eqw_enquiry_cart::getProductsInEnquirySession();
        include('partials/pisol-eqw-shortcode.php');
        $ret = ob_get_contents();  
        ob_end_clean();  
        return $ret;
    }

    function cartOnFirstLoad(){
        $this->products = class_eqw_enquiry_cart::getProductsInEnquirySession();
        ob_start();
        pisol_table_row($this->products);
        $cart = ob_get_contents(); // read ob2 ("b")
        ob_end_clean();
        $data = array(
            'cart'=> $cart,
            'pisol_products'=> class_eqw_enquiry_cart::filter_message($this->products)
        );  
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        die;  
    }
    
    static function get_price($product) {
        if( $product->is_on_sale() ) {
            return $product->get_sale_price();
        }
        return $product->get_regular_price();
    }

    static function get_price_simple_variation($product, $variation_id){
        if ($product->is_type( 'simple' )) {
            return self::get_price($product);
        }elseif($product->is_type('variable')){
            $variation_product = new WC_Product_Variation( $variation_id );
            return self::get_price($variation_product);
        }
    }

    static function get_variations($product, $variations_detail, $echo = false){
        // test if product is variable
        if($variations_detail == null || $variations_detail == "" || $variations_detail == false){ 
            return ;
        }
        
        $variations = "";
        $variations_label = array();
        if( $product->is_type( 'variable' ) ){
            // Loop through available product variation data
            foreach ( $variations_detail as $attribute => $term_slug ) {
                // Loop through the product attributes for this variation
                $taxonomy = str_replace( 'attribute_', '', $attribute  );
                $attr_label_name = wc_attribute_label( $taxonomy );

                $term_obj = get_term_by( 'slug', $term_slug, $taxonomy );
                $term_name = is_object($term_obj) ? $term_obj->name : $term_slug;
                $variations_label[$attr_label_name] = $term_name;
                
            }
        }
        if($echo){
            self::variation_html($variations_label);
        }else{
            return $variations_label;
        }
    }

    static function variation_html($variations_label){
        if(is_array($variations_label)){
            echo '<br>';
            foreach ($variations_label as $key => $value){
                echo '<strong class="pi-attribute-label">'.esc_html( $key ).'</strong> : <span>'.wp_kses_post( $value ).'</span><br>';
            }
        }
    }
    
}

new class_eqw_enquiry_shortcode();
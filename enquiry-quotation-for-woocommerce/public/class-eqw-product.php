<?php

class class_eqw_product{

    public $single_product_enquiry_position;
    public $loop_product_enquiry_position;
    public $add_to_enquiry_text_loop;
    public $add_to_enquiry_text_single;
    public $trouble_shoot_position;
    

    function __construct(){
        
        /**
         * https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/
         */
        $this->single_product_enquiry_position = 52;
        
        $this->loop_product_enquiry_position = 'woocommerce_after_shop_loop_item';

        $this->trouble_shoot_position = get_option('pi_eqw_trouble_shoot_position',0);

        if($this->trouble_shoot_position){
            add_action( 'woocommerce_single_product_summary', array($this,'add_enquiry_button'), $this->single_product_enquiry_position);
        }else{
            add_action( 'woocommerce_after_template_part', array($this,'add_enquiry_button_new'), 10,1);
        }

        add_action($this->loop_product_enquiry_position, array($this,'add_loop_enquiry_button'), 50 );

    }

    static function styleProductPage(){
        $button_width_single = get_option('pisol_eqw_button_size', '200');
        $button_font_single = get_option('pisol_eqw_button_font_size', '16');

        if(!empty($button_width_single)){
            $width = " width:{$button_width_single}px; ";
        }else{
            $width = '';
        }

        
        if(!empty($button_font_single)){
            $font = " font-size:{$button_font_single}px; ";
        }else{
            $font = '';
        }

        $style = $width.$font;
        return $style;
    }

    static function styleLoopPage(){
        $button_width_loop = get_option('pisol_eqw_loop_button_size', '');
        $button_font_loop = get_option('pisol_eqw_loop_button_font_size', '16');

        if(!empty($button_width_loop)){
            $width = " width:{$button_width_loop}px; ";
        }else{
            $width = '';
        }

        
        if(!empty($button_font_loop)){
            $font = " font-size:{$button_font_loop}px; ";
        }else{
            $font = '';
        }

        $style = $width.$font;
        return $style;
    }

    /** 
     * remove this after few releases as this is old way of adding 
     * button
     */
    function add_enquiry_button(){
        global $product;

        if(!$this->showButtonOnSinglePage($product)) return;

        $style = self::styleProductPage();

        $this->add_to_enquiry_text_single = get_option('pi_eqw_enquiry_single_button_text','Add to Enquiry');
        
        if($product->is_type('variable') ){
            echo '<button class="button pi-custom-button add-to-enquiry add-to-enquiry-single" href="javascript:void(0)" data-action="pi_add_to_enquiry" data-id="'.esc_attr($product->get_id()).'" style="'.esc_attr( $style ).'">' . esc_html($this->add_to_enquiry_text_single). '</button>';
        }else{
            echo '<button class="button pi-custom-button add-to-enquiry add-to-enquiry-single" href="javascript:void(0)" data-action="pi_add_to_enquiry" data-id="'.esc_attr($product->get_id()).'" style="'.esc_attr( $style ).'">' . esc_html($this->add_to_enquiry_text_single). '</button>';
        }
    }

    function add_enquiry_button_new($tmp_name){

        if(in_array($tmp_name, array('single-product/add-to-cart/simple.php'))){
            global $product;
            if(is_object($product)){
                if(!$this->showButtonOnSinglePage($product)) return;

                $style = self::styleProductPage();

                $this->add_to_enquiry_text_single = get_option('pi_eqw_enquiry_single_button_text','Add to Enquiry');
                
                if($product->is_type('variable') ){
                    echo '<button class="button pi-custom-button add-to-enquiry add-to-enquiry-single" href="javascript:void(0)" data-action="pi_add_to_enquiry" data-id="'.esc_attr( $product->get_id() ).'" style="'.esc_attr( $style ).'">' . esc_html($this->add_to_enquiry_text_single). '</button>';
                }else{
                    echo '<button class="button pi-custom-button add-to-enquiry add-to-enquiry-single" href="javascript:void(0)" data-action="pi_add_to_enquiry" data-id="'.esc_attr( $product->get_id() ).'" style="'.esc_attr( $style ).'">' . esc_html($this->add_to_enquiry_text_single). '</button>';
                }
            }
        }
    }

    function add_loop_enquiry_button() {
        global $product;

        if(!$this->showButtonOnLoopPage($product)) return;

        $style = self::styleLoopPage();

        $this->add_to_enquiry_text_loop = get_option('pi_eqw_enquiry_loop_button_text','Add to Enquiry');

        if($product->is_type('variable') ){
            echo '<div style="margin-bottom:10px; text-align:center; width:100%;">
            <a class="button pi-custom-button add-to-enquiry-loop" href="'.esc_url( $product->get_permalink() ).'" style="'.esc_attr( $style ).'">'.esc_html($this->add_to_enquiry_text_loop).'</a>
            </div>';
        }else{
            echo '<div style="margin-bottom:10px; text-align:center; width:100%;">
            <a class="button pi-custom-button add-to-enquiry add-to-enquiry-loop" href="javascript:void(0)" data-action="pi_add_to_enquiry" data-id="'.esc_attr( $product->get_id() ).'"  style="'.esc_attr( $style ).'">'.esc_html($this->add_to_enquiry_text_loop).'</a>
            </div>';
        }
    }

    function showButtonOnLoopPage($product){
        if( $product->is_type('grouped') || $product->is_type('variable') ) return false;


        /**
         * this show enquiry if product is out of stock and you want to show when product is out of stock
         */
        /*
        $pi_eqw_loop_show_on_out_of_stock = get_option('pi_eqw_loop_show_on_out_of_stock', 0);
        if($pi_eqw_loop_show_on_out_of_stock == 1){
            if(!$product->is_in_stock()){
                return true;
            }
        }
        */
        /**
         * global loop is off, 
         * but still you can enable it for single product from product overwrite
         * enable it for out of stocks
         */
        $pi_eqw_enquiry_loop = get_option('pi_eqw_enquiry_loop',0);
        if($pi_eqw_enquiry_loop != 1) return false;

        return true;
    }

    function showButtonOnSinglePage($product){
        if( $product->is_type('grouped') || $product->is_type('variable')) return false;


         /**
         * this show enquiry if product is out of stock and you want to show when product is out of stock
         */
        /*
        $pi_eqw_single_show_on_out_of_stock = get_option('pi_eqw_single_show_on_out_of_stock', 0);
        if($pi_eqw_single_show_on_out_of_stock == 1){
            if(!$product->is_in_stock()){
                return true;
            }
        }
        */
        /**
         * global single is off, 
         * but still you can enable it for single product from product overwrite
         * enable it for out of stocks
         */
        $pi_eqw_enquiry_single = get_option('pi_eqw_enquiry_single',1);
        if($pi_eqw_enquiry_single != 1) return false;

        return true;
    }
}

new class_eqw_product();


<?php 

class pisol_enq_dynamic_cart{
    private static $instance = null;
    private $cart_page_url = '';
    private $icon = '';

    public static function get_instance(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        $this->cart_page_url =  self::getCartUrl();
        $this->icon = self::getIcon();

        $dynamic_cart = get_option('pi_eqw_enable_cart', '1');

        if(empty($dynamic_cart) ){
            return;
        }

        add_action('wp_enqueue_scripts', array($this,'scripts'));
        add_action('wp_ajax_pi_get_cart_json', array($this, 'getCartJson') ); 
        add_action('wp_ajax_nopriv_pi_get_cart_json', array($this, 'getCartJson') );
        add_action('wc_ajax_pi_get_cart_json', array($this, 'getCartJson') ); 

        add_action('wp_footer', array($this,'addIcon'));

        add_action('wp_footer', array($this,'addMiniCart'));
    }

    function scripts(){
        
        wp_enqueue_script( 'pisol-eqw-cart', plugin_dir_url( __FILE__ ) . 'js/pisol-cart.js', array( 'jquery' ), PISOL_ENQUIRY_QUOTATION_WOOCOMMERCE_VERSION);
    }

    static function getCartUrl(){
        $cart_page = (int)get_option('pi_eqw_enquiry_cart','');
        return get_permalink( $cart_page);
    }

    static function getIcon(){
       return  plugin_dir_url( __FILE__ ).'img/cart.png';
    }

    function addIcon($relative = ""){
        $position = '';
        if(empty($relative)){
            $position =  get_option('pi_eqw_cart_position', 'bottom-right');
        }
        echo '<a href="'.$this->cart_page_url.'" id="pi-eqw-cart" class="'.$relative.' '.$position.'"><img src="'.$this->icon.'"><span class="pi-count"></span></a>';
    }

    function getCartJson(){
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('Pragma: no-cache');
        
        $products = class_eqw_enquiry_cart::getProductsInEnquirySession();
        $products = self::add_details($products);
        
        $count = self::getCount($products);
        
        $return  = array(
            'count' => $count,
            'products' => $products,
            'mini_cart' => self::miniCart($products)
        );

        $return = apply_filters('pisol_eqw_enquiry_cart_data', $return, $count, $products);
        echo json_encode($return);
        die;
    }

    static function add_details($products){

        if(!is_array($products) || empty($products)) return array();

        foreach($products as $key => $product){
            if(empty($product['variation'])){
               $product_obj = wc_get_product($product['id']);
            }else{
                $product_obj = wc_get_product($product['variation']);
            }

            $products[$key]['name'] = $product_obj->get_name();
            $products[$key]['permalink'] = $product_obj->get_permalink();
            $products[$key]['thumbnail'] = class_eqw_enquiry_cart::get_image($product['id'], $product['variation']);
            $products[$key]['price'] =  class_eqw_enquiry_shortcode::get_price_simple_variation($product_obj, $product['variation']);
        }

        return $products;
    }

    static function getCount($products){
        if(!is_array($products) || empty($products)) return 0;
        $count = 0;
        foreach($products as $product){
            $count = $count + $product['quantity'];
        }
        return $count;
    }

    static function miniCart($products){
        ob_start();
        include_once('partials/pisol-eqw-mini-cart.php');
        return ob_get_clean();
    }

    function addMiniCart(){
        $position = get_option('pi_eqw_cart_position', 'bottom-right');
        echo '<div id="pi-eqw-mini-cart" class="'.esc_attr($position).'">';
        echo '<header>'.__('Enquiry Cart','pisol-enquiry-quotation-woocommerce').'  <span class="close-mini-cart">&times;</span></header>';
        echo '<content>';
        echo 'Loading....';
        echo '</content>';
        echo '</div>';
    }
}

add_action('wp_loaded', function() {
    pisol_enq_dynamic_cart::get_instance();
});

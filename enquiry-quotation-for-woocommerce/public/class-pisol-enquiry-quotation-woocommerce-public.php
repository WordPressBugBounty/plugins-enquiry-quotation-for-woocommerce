<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Pisol_Enquiry_Quotation_Woocommerce_Public {

	
	private $plugin_name;

	
	private $version;

	
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	
	public function enqueue_styles() {

		

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pisol-enquiry-quotation-woocommerce-public.css', array(), $this->version, 'all' );

		$pi_eqw_enquiry_loop_bg_color = get_option('pi_eqw_enquiry_loop_bg_color', '#ee6443');
		$pi_eqw_enquiry_loop_text_color = get_option('pi_eqw_enquiry_loop_text_color', '#ffffff');

		$pi_eqw_enquiry_single_bg_color = get_option('pi_eqw_enquiry_single_bg_color', '#ee6443');
		$pi_eqw_enquiry_single_text_color = get_option('pi_eqw_enquiry_single_text_color', '#ffffff');

		$css = "
			.add-to-enquiry-loop{
				background-color: $pi_eqw_enquiry_loop_bg_color !important;
				color: $pi_eqw_enquiry_loop_text_color !important;
			}
			.add-to-enquiry-single{
				background-color: $pi_eqw_enquiry_single_bg_color !important;
				color: $pi_eqw_enquiry_single_text_color !important;
			}
		";

		wp_add_inline_style( $this->plugin_name, $css );

	}

	
	public function enqueue_scripts() {

		
		wp_enqueue_script( 'pisol-eqw-validation', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ));

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pisol-enquiry-quotation-woocommerce-public.js', array( 'jquery', 'jquery-blockui', 'pisol-eqw-validation' ), $this->version, false );

		$enquiry_cart_page_id = get_option('pi_eqw_enquiry_cart',0);
		if($enquiry_cart_page_id != 0 && $enquiry_cart_page_id != ""){
			$cart_page = get_permalink($enquiry_cart_page_id);
		}else{
			$cart_page = false;
		}

		wp_localize_script( $this->plugin_name, 'pi_ajax',
			array( 
				'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'loading'=> plugin_dir_url( __FILE__ ).'img/loading.svg',
				'cart_page'=>$cart_page,
				'view_enquiry_cart'=>__('View Enquiry Cart','pisol-enquiry-quotation-woocommerce')
			) 
		);
		$products = class_eqw_enquiry_cart::getProductsInEnquirySession();
		wp_localize_script( $this->plugin_name, 'pisol_products',
		$products
		);
	}

}

<?php

class Pisol_Enquiry_Quotation_Woocommerce_Activator {

	public static function activate() {
		add_option('pi_ewq_do_activation_redirect', true);
		self::createEnquiryCartPage();
	}

	public static function createEnquiryCartPage(){
		$page_saved = get_option('pi_eqw_enquiry_cart',0);
		if($page_saved == 0 || $page_saved == ""){
			$page  = array( 
					'post_title'     => __('Enquiry Cart'),
					'post_type'      => 'page',
					'post_content'   => '[pisol_enquiry_cart]',
					'post_status'    => 'publish',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					);
			$page_id = wp_insert_post($page, false);
			update_option('pi_eqw_enquiry_cart', $page_id);
		}
	}

}

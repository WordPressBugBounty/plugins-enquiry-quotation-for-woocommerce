<?php
class Pisol_Enquiry_Quotation_Woocommerce_Admin {


	private $plugin_name;


	private $version;


	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if(is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )){
			new Pi_Eqw_Menu($this->plugin_name, $this->version);
			new Class_Pi_Eqw_Option($this->plugin_name);
			new Class_Pi_Eqw_Advance($this->plugin_name);
			new Class_Pi_Eqw_Email($this->plugin_name);
			new Class_Pi_Eqw_Cart($this->plugin_name);
			new Class_Pi_Eqw_Form_Control($this->plugin_name);
		}
		

		add_action('admin_init', array($this,'plugin_redirect'));

		add_filter( 'display_post_states', array( $this, 'pageStatus' ), 10, 2 );
	}

	function plugin_redirect(){
		if (get_option('pi_ewq_do_activation_redirect', false)) {
			delete_option('pi_ewq_do_activation_redirect');
			if(!isset($_GET['activate-multi']) && (function_exists('is_multisite') && !is_multisite()))
			{
				wp_redirect("admin.php?page=pisol-enquiry-quote");
			}
		}
	}


	public function enqueue_styles() {

		

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pisol-enquiry-quotation-woocommerce-admin.css', array(), $this->version, 'all' );

	}


	public function enqueue_scripts() {


	}

	function pageStatus($post_states, $post){
        $enq_cart_page = get_option('pi_eqw_enquiry_cart',0);
        if($post->ID == $enq_cart_page){
            $post_states['enq_cart_page'] = __('Enquiry Cart','pisol-enquiry-quotation-woocommerce');
        }
        return $post_states;
    }

}

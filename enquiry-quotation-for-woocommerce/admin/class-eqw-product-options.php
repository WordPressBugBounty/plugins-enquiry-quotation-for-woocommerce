<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Pisol_eqw_product_options{

    public function __construct( ) {
		add_action( 'woocommerce_product_data_tabs', array($this,'productTab') );
		/** Adding order preparation days */
		add_action( 'woocommerce_product_data_panels', array($this,'enquirySetting') );
        
    }

    function productTab($tabs){
        $tabs['pisol_eqw'] = array(
            'label'    => 'Enquiry Option',
            'target'   => 'pisol_eqw',
            'priority' => 21,
            'class' => 'hide_if_grouped hide_if_external'
        );
        return $tabs;
    }
    
    function enquirySetting() {
    echo '<div id="pisol_eqw" class="panel woocommerce_options_panel hidden ">';
    ?>
    <div style="padding:10px; background:#ccc; color:#000; margin-bottom:10px;">
      Below features are only available in the PRO Version of the WooCommerce Enquiry plugin, <a href="<?php echo esc_url( PI_EQW_BUY_URL ); ?>" target="_blank">Click to Buy Now</a>
    </div>
    <?php
		woocommerce_wp_checkbox( array(
            'label' => __('Disable Enquiry', 'pisol-enquiry-quotation-woocommerce' ), 
            'id' => 'pisol_disable_enquiry', 
            'name' => 'pisol_disable_enquiry', 
            'description' => __('You can disable enquiry option for this product', 'pisol-enquiry-quotation-woocommerce' )
          ) );
		echo '</div>';
    }
    
    
    
}

new Pisol_eqw_product_options();
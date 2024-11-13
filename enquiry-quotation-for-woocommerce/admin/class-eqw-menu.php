<?php

class Pi_Eqw_Menu{

    public $plugin_name;
    public $version;
    public $menu;
    
    function __construct($plugin_name , $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action( 'admin_menu', array($this,'plugin_menu') );
        add_action($this->plugin_name.'_promotion', array($this,'promotion'));
    }

    function plugin_menu(){
        
        $this->menu = add_submenu_page(
            'edit.php?post_type=pisol_enquiry',
            __( 'Enquiry Setting'),
            __( 'Enquiry Setting'),
            'manage_options',
            'pisol-enquiry-quote',
            array($this, 'menu_option_page')
        );

        add_action("load-".$this->menu, array($this,"bootstrap_style"));
 
    }

    public function bootstrap_style() {

        wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full.min.js', array( 'jquery' ) );
       wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css');
        
		wp_enqueue_style( $this->plugin_name."_bootstrap", plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );

        wp_enqueue_script( $this->plugin_name."_quick_save", plugin_dir_url( __FILE__ ) . 'js/pisol-quick-save.js', array('jquery'), $this->version, 'all' );

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pisol-enquiry-quotation-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
		
	}

    function menu_option_page(){
        if(function_exists('settings_errors')){
            settings_errors();
        }
        ?>
        <div id="bootstrap-wrapper" class="pisol-setting-wrapper pisol-container-wrapper">
        <div class="pisol-container mt-2">
            <div class="pisol-row">
                    <div class="col-12">
                        <div class='bg-dark'>
                        <div class="pisol-row">
                            <div class="col-12 col-sm-2 py-2">
                                    <a href="https://www.piwebsolution.com/" target="_blank"><img class="img-fluid ml-2" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>img/pi-web-solution.png"></a>
                            </div>
                            <div class="col-12 col-sm-10 d-flex pisol-top-menu">
                                <?php do_action($this->plugin_name.'_tab'); ?>
                                <!--<a class=" px-3 text-light d-flex align-items-center  border-left border-right  bg-info " href="https://www.piwebsolution.com/documentation-for-live-sales-notifications-for-woocommerce-plugin/">
                                    Documentation
                                </a>-->
                            </div>
                        </div>
                        </div>
                    </div>
            </div>
            <div class="pisol-row">
                <div class="col-12">
                <div class="bg-light border pl-3 pr-3 pb-3 pt-0">
                    <div class="pisol-row">
                        <div class="col">
                        <?php do_action($this->plugin_name.'_tab_content'); ?>
                        </div>
                        <?php do_action($this->plugin_name.'_promotion'); ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>
        <?php
    }

    function promotion(){
        if(isset($_GET['tab']) &&  $_GET['tab'] == 'form_control') return;
        ?>
        <div class="col-12 col-sm-12 col-md-4 pt-3">
            <a href="javascript:void(0)" class="btn btn-primary btn-sm mr-2 mb-2" id="hid-pro-feature">Hide Pro Feature</a>
            <div class="bg-primary  text-center mb-3">
                <a class="" href="<?php echo esc_url( PI_EQW_BUY_URL ); ?>" target="_blank">
                <?php new pisol_promotion('pisol_enquiry_installation_date'); ?>
                </a>
            </div>

            <div class="text-center mb-3 pi-shadow ">
                <div class="pisol-row justify-content-center">
                    <div class="col-md-7">
                        <div class="p-2  text-center">
                            <img class="img-fluid" src="<?php echo esc_url(plugin_dir_url( __FILE__ )); ?>img/bg.svg">
                        </div>
                    </div>
                </div>
                <div class="text-center py-2">
                    <a class="btn btn-success btn-sm text-uppercase mb-2 " href="<?php echo esc_url(PI_EQW_BUY_URL); ?>&utm_ref=top_link" target="_blank">Buy Now !!</a>
                    <a class="btn btn-sm mb-2 btn-secondary text-uppercase" href="https://websitemaintenanceservice.in/enquiry_demo/" target="_blank">Try Demo</a>
                </div>
                <h2 id="pi-banner-tagline" class="mb-0">Get Pro for <?php echo esc_html(PI_EQW_PRICE); ?> Only</h2>
                <div class="inside">
                    <ul class="text-left pisol-pro-feature-list">
                        <li class="border-top  h6 "><span class="font-weight-bold ">Disable/Enable</span> enquiry for specific product category</li>
                        <li class="border-top  h6 ">Support <span class="font-weight-bold ">variable products</span></li>
                        <li class="border-top  h6 ">Show enquiry option only when the product is <span class="font-weight-bold ">out of stock</span></li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Change the position</span> of the enquiry button on the product loop page and single product page</li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Remove add to cart button</span> so you only receive enquiries</li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Remove add to cart button</span> for products with enquiry enabled</li>
                        <li class="border-top  h6 ">Add <span class="font-weight-bold ">multiple email id</span> to admin email list</li>
                        <li class="border-top  h6 ">Adding custom message in <span class="font-weight-bold ">customer email</span></li>
                        <li class="border-top  h6 ">Adding custom message in <span class="font-weight-bold ">admin email</span></li>
                        <li class="border-top  h6 ">Modify the <span class="font-weight-bold ">success message</span> on form submission</li>
                        <li class="border-top  h6 ">Making a form field as <span class="font-weight-bold ">Non required field</span></li>
                        <li class="border-top  h6 ">Show a <span class="font-weight-bold ">dynamic cart</span> (that show the product count added in the enquiry cart and link to enquiry cart page)</li>
                        <li class="border-top  h6 ">Insert inquiry cart on page using short code <span class="font-weight-bold ">[enquiry_cart_icon]</span></li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Remove price column</span> from the enquiry cart and enquiry email </li>
                        <li class="border-top  h6 ">Ask user to <span class="font-weight-bold ">accept terms and condition</span> before submitting the enquiry </li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Disable the field</span> that you don't want in the enquiry form</li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Change form field label</span> from within the plugin setting</li>
                        <li class="border-top  h6 "><span class="font-weight-bold ">Change sequence</span> of the field in the enquiry form</li>
                        <li class="border-top  h6 ">Remove <span class="font-weight-bold ">product specific message</span> column from enquiry cart</li>
                        <li class="border-top  h6 ">Open <span class="font-weight-bold ">enquiry cart in a popup</span>, so user can submit enquiry right from the product page</li>
                        <li class="border-top  h6 ">Customer can see submitted enquiries in <span class="font-weight-bold ">My enquiry section</span> (under my account of WooCommerce)</li>
                    </ul>
                    <a class="btn btn-primary mb-3" href="<?php echo esc_url( PI_EQW_BUY_URL ); ?>" target="_blank">Click to Buy Now</a>
                </div>
            </div>

        </div>
        <?php
    }

    function isWeekend() {
        return (date('N', strtotime(date('Y/m/d'))) >= 6);
    }

}
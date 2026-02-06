<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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
            __( 'Enquiry Setting', 'pisol-enquiry-quotation-woocommerce' ),
            __( 'Enquiry Setting', 'pisol-enquiry-quotation-woocommerce' ),
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
                            <div class="col-12 col-sm-2 py-2 d-flex align-items-center justify-content-center">
                                    <a href="https://www.piwebsolution.com/" target="_blank"><img  id="pi-logo" class="img-fluid ml-2" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>img/pi-web-solution.svg"></a>
                            </div>
                            <div class="col-12 col-sm-10 d-flex pisol-top-menu">
                                <nav id="pisol-navbar" class="navbar navbar-expand-lg navbar-light mr-0 ml-auto">
                                    <div>
                                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                            <?php do_action($this->plugin_name.'_tab'); ?>
                                        </ul>
                                    </div>
                                </nav>
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
                <div class="bg-light border pl-3 pr-3 pt-0">
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
        <div class="col-12 col-sm-12 col-md-4 pt-3 border-left">
            <div class="pi-shadow px-3 py-3 rounded">
                <h2 id="pi-banner-tagline" class="mb-0 mt-3" style="color:#ccc !important;">
                        <span class="d-block mb-4">⭐️⭐️⭐️⭐️⭐️</span>
                        <span class="d-block mb-2">🚀 Trusted by <span style="color:#fff;">2,000+</span> WooCommerce Stores</span>
                        <span class="d-block mb-2">Rated <span style="color:#fff;">4.9/5</span> – Users love it</span>
                </h2>
                <div class="inside">
                    <ul class="text-left pisol-pro-feature-list mb-3 mt-3">
                        <li class="h6 font-weight-bold"><b>🔧 Advanced Controls</b></li>
                        <li class="h6">✓ Disable enquiry by product category</li>
                        <li class="h6">✓ Show enquiry only when product is out of stock</li>
                        <li class="h6">✓ Change button position on product pages</li>
                        <li class="h6">✓ Remove Add to Cart to get only enquiries</li>
                    </ul>
                    <ul class="text-left pisol-pro-feature-list mb-3">
                        <li class="h6 font-weight-bold mt-3"><b>💬 Smart Communication</b></li>
                        <li class="h6">✓ Custom messages in customer/admin emails</li>
                        <li class="h6">✓ Support multiple admin emails</li>
                        <li class="h6">✓ Accept terms before submitting enquiry</li>
                        <li class="h6">✓ Fully customize enquiry fields & labels</li>
                    </ul>
                    <ul class="text-left pisol-pro-feature-list mb-3">
                        <li class="h6 font-weight-bold mt-3"><b>🛒 Enquiry Cart Boost</b></li>
                        <li class="h6">✓ Dynamic enquiry cart with popup support</li>
                        <li class="h6">✓ Shortcode to show enquiry cart</li>
                        <li class="h6">✓ Show submitted enquiries in My Account</li>
                        <li class="h6">✓ Get instant alerts in Telegram</li>
                    </ul>
                    <h4 class="pi-bottom-banner">💰 Only <?php echo esc_html(PI_EQW_PRICE); ?> <small>Billed yearly</small></h4>
                    <div class="text-center">
                    <a class="btn btn-primary btn-lg mb-3" href="<?php echo esc_url( PI_EQW_BUY_URL ); ?>" target="_blank">🔓 Unlock Pro Now – Limited Time Price!</a>
                    </div>
                </div>
            </div>

            <div class="bg-primary  text-center my-3">
                <a class="" href="<?php echo esc_url( PI_EQW_BUY_URL ); ?>" target="_blank">
                <?php new pisol_promotion('pisol_enquiry_installation_date'); ?>
                </a>
            </div>

        </div>
        <?php
        $this->support();
    }

    function isWeekend() {
        return (wp_date('N', strtotime(wp_date('Y/m/d'))) >= 6);
    }

    function support(){
        $website_url = home_url();
        $plugin_name = $this->plugin_name;
        ?>
        <form action="https://www.piwebsolution.com/quick-support/" method="post" target="_blank" style="display:inline; position:fixed; bottom:30px; right:30px; z-index:9999;" >
            <input type="hidden" name="website_url" value="<?php echo esc_attr( $website_url ); ?>">
            <input type="hidden" name="plugin_name" value="<?php echo esc_attr( $plugin_name ); ?>">
            <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;">
                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>img/chat.png" 
                    alt="Live Support" title="Quick Support" style="width:60px;height:60px;">
            </button>
        </form>
        <?php
    }

}
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

        add_action( 'admin_enqueue_scripts', array($this,'enqueue_styles') );
 
    }

    public function enqueue_styles() {
        $screen = get_current_screen();
        if(!isset($screen->id) || $screen->id != 'pisol_enquiry') return;
        
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/enquiry-detail-page.css', array(), $this->version, 'all' );
    }

    public function bootstrap_style() {

        wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css');
        
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pisol-enquiry-quotation-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

        wp_enqueue_script( $this->plugin_name."_quick_save", plugin_dir_url( __FILE__ ) . 'js/pisol-quick-save.js', array('jquery'), $this->version, 'all' );

        wp_enqueue_style( $this->plugin_name.'_admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

		
	}

    function menu_option_page(){
        if(function_exists('settings_errors')){
            settings_errors();
        }
        ?>
        <div class="pisol-container">
            <div class="pisol-header">
                <div id="pisol-header-bar">
                    <a href="https://www.piwebsolution.com/" target="_blank"><img id="pi-logo" class="pisol-img-fluid" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>img/pi-web-solution.svg"></a>
                </div>
            </div>

            <div class="pisol-left-sidebar">
                <div id="pisol-side-menu" class="mb-4 rounded py-3 ">
                    <?php do_action($this->plugin_name.'_tab'); ?>
                </div>
                <?php do_action($this->plugin_name.'_promotion'); ?>
            </div>

            <div class="pisol-content">
                <?php do_action($this->plugin_name.'_tab_content'); ?>
            </div>
        </div>
        <?php
    }

    function promotion(){
        if(isset($_GET['tab']) &&  $_GET['tab'] == 'form_control') return;
        ?>
            <!-- Pisol Enquiry Quote — Pro Upsell Side Banner -->
            <div class="peq-banner">

                <div class="peq-head">
                    <div class="peq-stars" aria-label="Rated 5 out of 5 stars">
                    <svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9L5.7 21l1.7-7-5.4-4.7 7.1-.6z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9L5.7 21l1.7-7-5.4-4.7 7.1-.6z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9L5.7 21l1.7-7-5.4-4.7 7.1-.6z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9L5.7 21l1.7-7-5.4-4.7 7.1-.6z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9L5.7 21l1.7-7-5.4-4.7 7.1-.6z"/></svg>
                    </div>
                    <p class="peq-trust">Trusted by <strong>2,000+</strong> WooCommerce Stores</p>
                    <p class="peq-rating">Rated <strong>4.9/5</strong> — Users love it</p>
                </div>

                <div class="peq-body">

                    <section class="peq-section">
                    <span class="peq-tag peq-tag-orange">
                        <svg viewBox="0 0 24 24"><path d="M21.7 15.9l-3.1-3.1a5 5 0 00-6.4-6.4L9 9.6 3.9 4.5 2.5 5.9 7.6 11l-3.2 3.2a5 5 0 006.4 6.4l3.1-3.1 3.2 3.2 1.4-1.4-3.2-3.2z"/></svg>
                        Advanced controls
                    </span>
                    <ul>
                        <li>Disable enquiry by product category</li>
                        <li>Show enquiry only when product is out of stock</li>
                        <li>Change button position on product pages</li>
                        <li>Remove Add to Cart to get only enquiries</li>
                        <li>Product manager to receive product enquiries</li>
                    </ul>
                    </section>

                    <section class="peq-section">
                    <span class="peq-tag peq-tag-teal">
                        <svg viewBox="0 0 24 24"><path d="M4 4h16a2 2 0 012 2v9a2 2 0 01-2 2H9l-5 4v-4a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        Smart communication
                    </span>
                    <ul>
                        <li>Custom messages in customer/admin emails</li>
                        <li>Support multiple admin emails</li>
                        <li>Accept terms before submitting enquiry</li>
                        <li>Fully customize enquiry fields &amp; labels</li>
                    </ul>
                    </section>

                    <section class="peq-section">
                    <span class="peq-tag peq-tag-purple">
                        <svg viewBox="0 0 24 24"><path d="M6 6h15l-1.5 8.5a2 2 0 01-2 1.5H8.5a2 2 0 01-2-1.6L4.3 3H1V1h4.3a1 1 0 011 .8L6 6zM8 20a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm9 0a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/></svg>
                        Enquiry cart boost
                    </span>
                    <ul>
                        <li>Dynamic enquiry cart with popup support</li>
                        <li>Shortcode to show enquiry cart</li>
                        <li>Show submitted enquiries in My Account</li>
                        <li>Get instant alerts in Telegram</li>
                    </ul>
                    </section>

                </div>

                <div class="peq-foot">
                    <div class="peq-ticket">
                    <span class="peq-ticket-amount"><?php echo esc_html(PI_EQW_PRICE); ?></span>
                    <span class="peq-ticket-word">only</span>
                    </div>
                    <a href="<?php echo esc_url( PI_EQW_PRODUCT_PAGE_URL ); ?>" class="peq-btn" target="_blank">
                    <svg viewBox="0 0 24 24"><path d="M6 10V8a6 6 0 1112 0v2h1a1 1 0 011 1v9a1 1 0 01-1 1H5a1 1 0 01-1-1v-9a1 1 0 011-1h1zm2 0h8V8a4 4 0 00-8 0v2z"/></svg>
                    Unlock the full potential
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
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_Pi_Eqw_Cart{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'cart';

    private $tab_name = "Enquiry Cart";

    private $setting_key = 'pi_eqw_cart_setting';
    public $tab;

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        add_action('init', array($this,'init'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

        if(PI_EQW_DELETE_SETTING){
            $this->delete_settings();
        }
    }

    function init(){
        $this->settings = array(

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>'Enquiry cart', 'type'=>'setting_category'),
            
            array('field'=>'pi_eqw_enable_cart', 'label'=>__('Enable cart','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>1,   'desc'=>__('This will show a dynamically updating cart button on each page in the corner','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_cart_position', 'label'=>__('Cart Position', 'pisol-enquiry-quotation-woocommerce'), 'type'=>'select', 'value'=>array('top-left' =>__('Top Left','pisol-enquiry-quotation-woocommerce'), 'top-right'=>__('Top Right','pisol-enquiry-quotation-woocommerce'),  'bottom-left'=>__('Bottom Left','pisol-enquiry-quotation-woocommerce'), 'bottom-right'=>__('Bottom Right','pisol-enquiry-quotation-woocommerce')), 'default'=>'bottom-right','desc'=>__('Position of the cart icon when it is auto inserted, this position will not be used when you insert it using short code','pisol-enquiry-quotation-woocommerce')), 

            array('field'=>'pi_eqw_use_shortcode', 'label'=>__('Insert icon using Short code [enquiry_cart_icon]','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will allow you to insert the icon using shortcode [enquiry_cart_icon], when you enable this auto insertion will be disabled','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pisol_eqw_cart_img', 'type'=>'image','label'=>__('Dynamic Cart icon','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Dynamic Cart icon','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
        );
        $this->register_settings();
    }

    function pages(){
        $pages = array(0 => 'Select page for Enquiry cart');
        $obj = get_pages();
        foreach($obj as $page){
            $pages[$page->ID] = $page->post_title;
        }
        return $pages;

    }

    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }

    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        $this->tab_name = __('Enquiry Mini-Cart','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class="hide-pro px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ) ); ?>">
            <span class="dashicons dashicons-cart"></span> <?php echo esc_html( $this->tab_name); ?>
        </a>
        <?php
    }

    function tab_content(){
        
       ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_eqw($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="my-3 btn btn-primary btn-md" value="<?php echo esc_attr__('Save Option', 'pisol-enquiry-quotation-woocommerce'); ?>" />
        </form>
       <?php
    }

    
}

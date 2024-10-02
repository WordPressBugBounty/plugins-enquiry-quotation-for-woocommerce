<?php

class Class_Pi_Eqw_Cart{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'cart';

    private $tab_name = "Dynamic Cart (PRO)";

    private $setting_key = 'pi_eqw_cart_setting';
    public $tab;

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        $this->settings = array(
            
            array('field'=>'pi_eqw_enable_cart', 'label'=>__('Enable cart icon','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>1,   'desc'=>__('This will show a dynamically updating cart button on each page in the corner','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_use_shortcode', 'label'=>__('Insert icon using Short code [enquiry_cart_icon]','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will allow you to insert the icon using shortcode [enquiry_cart_icon], when you enable this auto insertion will be disabled','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pisol_eqw_cart_img', 'type'=>'image','label'=>__('Dynamic Cart icon','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Dynamic Cart icon','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
        );
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),4);

       
        $this->register_settings();

        if(PI_EQW_DELETE_SETTING){
            $this->delete_settings();
        }
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
        $this->tab_name = __('Dynamic Cart (PRO)','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class="hide-pro px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ) ); ?>">
        <?php echo esc_html( $this->tab_name); ?>
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
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    
}

<?php

class Class_Pi_Eqw_Advance{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'advance';

    private $tab_name = "Advance option";

    private $setting_key = 'pi_eqw_advance_setting';
    
    public $tab;
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        add_action('init', array($this,'init'));
        
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

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

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>'Advance option', 'type'=>'setting_category'),
            
            array('field'=>'pi_eqw_remove_add_to_cart2', 'label'=>__('Remove add to cart button','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>'hide-if-enquiry',   'desc'=>__('This will remove the add to cart button from website<br>Dont Hide<br>Hide for All Products<br>Hide for product which has enquiry enabled','pisol-enquiry-quotation-woocommerce'), 'value'=>array('hide-if-enquiry'=>__('Hide if enquiry enabled','pisol-enquiry-quotation-woocommerce')), 'pro'=>true),

            array('field'=>'pi_eqw_hide_price', 'label'=>__('Hide price','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>'no',   'desc'=>__('This will hide the price based on the selections, If price is hidden then the add to cart button will also be hidden','pisol-enquiry-quotation-woocommerce'), 'value'=>array('no'=>__('Don\'t hide','pisol-enquiry-quotation-woocommerce'), 'all'=>__('Hide for all','pisol-enquiry-quotation-woocommerce'), 'guest'=>__('Hide for non log-in users','pisol-enquiry-quotation-woocommerce'))),

            array('field'=>'pi_eqw_hide_price_in_cart', 'label'=>__('Remove price columns from the enquiry cart and enquiry emails','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will remove the price columns from the cart page and enquiry emails','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_hide_message_in_cart_1', 'label'=>__('Remove product specific message columns from the enquiry cart and enquiry emails','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will remove the message columns from the cart page and enquiry emails','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),
            
            array('field'=>'pi_eqw_enquiry_cart', 'label'=>__('Select the page where to show the enquiry cart','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>0, 'value'=>$this->pages(),  'desc'=>__('Enquiry button position on shop / category page, If you make some other page as Enquiry make sure to put the short code <strong>[pisol_enquiry_cart]</strong> on that page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_redirect_to_enquiry_cart', 'label'=>__('Redirect WooCommerce cart and checkout page to enquiry cart page','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will redirect all the traffic on cart and checkout page to enquiry cart page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_success_message', 'label'=>__('Success message shown on form submission','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>__('Enquiry submitted'),   'desc'=>__('This is the message that is shown on successful submission of the enquiry','pisol-enquiry-quotation-woocommerce'),'pro'=>true),

            array('field'=>'pi_eqw_redirect_to_form', 'label'=>__('After Add to enquiry click','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>0, 'value'=>array('0'=> __('Product will be added to enquiry cart only','pisol-enquiry-quotation-woocommerce'), 1 =>__('User will be redirected to enquiry cart page once product is added','pisol-enquiry-quotation-woocommerce'), 'popup'=>__('Enquiry popup will get opened','pisol-enquiry-quotation-woocommerce')),  'desc'=>__('What happen when user click on add to enquiry (Product will be added to the enquiry cart in all the 3 cases),<br>
            Redirect to cart page<br>Open cart page in popup','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Webhook Integration",'pisol-enquiry-quotation-woocommerce'), 'type'=>"setting_category"),

            array('field'=>'pi_eqw_webhook_url', 'label'=>__('Webhook url','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>'',   'desc'=>__('Insert webhook url where the enquiry data will send, you can insert webhook url of zapier or Pabbly or any other platform that support webhook url','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Enquiry popup settings','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category', 'pro'=>true),

            array('field'=>'pi_eqw_show_products_in_cart_in_popup', 'label'=>__('Show products added to the enquiry cart in the popup','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show products added in the inquiry cart in the popup','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_popup_title', 'label'=>__('Popup title','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>__('Submit Enquiry','pisol-enquiry-quotation-woocommerce'),   'desc'=>__('Title shown above the popup','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_popup_title_bg_color', 'type'=>'color', 'default'=>'#FF0000','label'=>__('Popup Title Background color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Title background color','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_popup_title_text_color', 'type'=>'color', 'default'=>'#FFFFFF','label'=>__('Popup Title text color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Title text color','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

        );
        $this->register_settings();
    }

    function pages(){
        $pages = array(0 => 'Select page for Enquiry cart');
        $obj = get_posts(array('numberposts' => -1, 'post_type' => 'page'));
        if(!is_array($obj) || empty($obj)) return array(0 => 'Please create a Page and add shortcode [pisol_enquiry_cart]');
        foreach($obj as $page){
            if($page->post_status == 'publish'){
                $pages[$page->ID] = $page->post_title;
            }
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
        $page = sanitize_text_field(filter_input( INPUT_GET, 'page'));
        $this->tab_name = __('Advance','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ) ); ?>">
           <span class="dashicons dashicons-admin-tools"></span> <?php echo esc_html( $this->tab_name); ?> 
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

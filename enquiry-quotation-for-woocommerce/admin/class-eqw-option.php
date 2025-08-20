<?php

class Class_Pi_Eqw_Option{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'default';

    private $tab_name = 'Enquiry Button';

    private $setting_key = 'pi_eqw_basic_setting';
    
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
    }

    function init(){
        $this->settings = array(

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>'Enable enquiry for specific roles of users only', 'type'=>'setting_category'),

            array('field'=>'pi_eqw_show_enquiry_button_to_role2', 'type'=>'multiselect', 'default'=>array('guest'),'label'=>__('Show enquiry button for user with role'),'desc'=>__('select roles to whom the enquiry button will be shown'), 'value'=>$this->allUserRoles(), 'pro'=>true),
            
            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Enquiry button on shop / category page','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pi_eqw_enquiry_loop', 'label'=>__('Enquiry button on shop / category page', 'pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>1,   'desc'=>__('This will show enquiry button on loop product like shop, category page', 'pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_enquiry_loop_pro', 'label'=>__('Show button on Variable Product', 'pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will show enquiry button on variable product', 'pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_loop_show_on_out_of_stock_pro', 'label'=>__('Show enquiry option only when product is out of stock', 'pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('On shop / category product page', 'pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_enquiry_loop_position', 'label'=>__('Position on shop/category page', 'pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=> 'woocommerce_after_shop_loop_item', 'value'=>array('woocommerce_after_shop_loop_item'=>__('At the end of product', 'pisol-enquiry-quotation-woocommerce'), 'woocommerce_before_shop_loop_item'=>__('At the start of the product', 'pisol-enquiry-quotation-woocommerce'), 'woocommerce_before_shop_loop_item_title'=>__('Before product title', 'pisol-enquiry-quotation-woocommerce'), 'woocommerce_after_shop_loop_item_title'=>__('After product title', 'pisol-enquiry-quotation-woocommerce')),  'desc'=>__('Enquiry button position on shop / category page','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_enquiry_loop_bg_color', 'type'=>'color', 'default'=>'#ee6443','label'=>__('Background color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Background color of the button on the shop / category page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_enquiry_loop_text_color', 'type'=>'color', 'default'=>'#ffffff','label'=>__('Text color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Text color of the button on the shop / category page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pisol_eqw_loop_button_size','desc'=>'Enquiry button width on product page (PX), if left blank it will be 100% width ', 'label'=>__('Enquiry button width on product page'),'type'=>'number', 'default'=>'220', 'min'=>100, 'placeholder'=>'px'),

            array('field'=>'pisol_eqw_loop_button_font_size','desc'=>'Enquiry button font size (PX)', 'label'=>__('Enquiry button font size on product page'),'type'=>'number', 'default'=>'16', 'placeholder'=>'px', 'min'=>12),

            array('field'=>'pi_eqw_enquiry_loop_button_text', 'type'=>'text', 'default'=>__('Add to Enquiry','pisol-enquiry-quotation-woocommerce'),'label'=>__('Enquiry button text','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Text shown in the enquiry button','pisol-enquiry-quotation-woocommerce')),
            

            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Enquiry button on single product page','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pi_eqw_enquiry_single', 'label'=>__('Enquiry button on single product page','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>1,   'desc'=>__('This will show enquiry button on single product page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_enquiry_single_pro', 'label'=>__('Show button on Variable Product','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will show enquiry button on variable product','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_single_show_on_out_of_stock_pro', 'label'=>__('Show enquiry option Only when product is out of stock','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('On single product page','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_enquiry_single_position', 'label'=>__('Position on single product page','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=> 52, 'value'=> array(4 =>__('Before summary','pisol-enquiry-quotation-woocommerce'), 52 => __('After Summary','pisol-enquiry-quotation-woocommerce'), 36 => __('After add to cart button','pisol-enquiry-quotation-woocommerce'), 29 => __('Before add to cart button','pisol-enquiry-quotation-woocommerce')),  'desc'=>__('Enquiry button position on single product page','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_enquiry_single_bg_color', 'type'=>'color', 'default'=>'#ee6443','label'=>__('Background color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Background color of the button on the shop / category page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_enquiry_single_text_color', 'type'=>'color', 'default'=>'#ffffff','label'=>__('Text color','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Text color of the button on the shop / category page','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pisol_eqw_button_size','desc'=>'Enquiry button width on product page (PX), if left blank it will be 100% width ', 'label'=>__('Enquiry button width on product page'),'type'=>'number', 'default'=>'220', 'min'=>100, 'placeholder'=>'px'),

            array('field'=>'pisol_eqw_button_font_size','desc'=>'Enquiry button font size (PX)', 'label'=>__('Enquiry button font size on product page'),'type'=>'number', 'default'=>'16', 'placeholder'=>'px', 'min'=>12),

            array('field'=>'pi_eqw_enquiry_single_button_text', 'type'=>'text', 'default'=>__('Add to Enquiry','pisol-enquiry-quotation-woocommerce'),'label'=>__('Enquiry button text','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Text shown in the enquiry button','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('If you notice change in enquiry button after upgrading to v2.1.2, then enable the below option to fall back to old way of position','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pi_eqw_trouble_shoot_position', 'label'=>__('Fall back to old position hook','pisol-enquiry-quotation-woocommerce'),'type'=>'switch', 'default'=>0,   'desc'=>__('We have changed the single product page hook in v2.1.2 this is done to improve compatibility, but if you face any issue like enquiry button not showing after upgrading to v2.1.2 then enable this option to fall back to old hook','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Processing image','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pisol_eqw_loading_img', 'type'=>'image','label'=>__('Processing image','pisol-enquiry-quotation-woocommerce'),'desc'=>__('Image is shown as loading image','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),
           

        );
        $this->register_settings();

        if(PI_EQW_DELETE_SETTING){
            $this->delete_settings();
        }
    }

    function allUserRoles(){
        $wp_roles = new WP_Roles();
        $roles = array();
       foreach($wp_roles->roles as $key => $role){
        $roles[$key] = $role['name'];
       }
       $roles['guest'] = 'Guest Customer';
       return $roles;
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
        $this->tab_name = __('Enquiry Button','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ) ); ?>">
            <span class="dashicons dashicons-format-chat"></span> <?php echo esc_html( $this->tab_name); ?>
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


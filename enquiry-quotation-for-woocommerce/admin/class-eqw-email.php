<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_Pi_Eqw_Email{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'email';

    private $tab_name = "Email setting";

    private $setting_key = 'pi_eqw_email_setting';

    public $tab;
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        add_action('init', array($this,'init'));
        
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

        if(PI_EQW_DELETE_SETTING){
            $this->delete_settings();
        }

        add_action( 'admin_notices', [$this, 'library_warning'] );
    }

    function init(){
        $this->settings = array(

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>'Email settings', 'type'=>'setting_category'),
            
            array('field'=>'pi_eqw_email', 'label'=>__('Email id','pisol-enquiry-quotation-woocommerce'),'type'=>'text',   'desc'=>__('Email id that will receive the enquiry, <strong class="text-danger">In PRO version you can add multiple email separated with comma like this text@email.com, text2@email.com </strong>','pisol-enquiry-quotation-woocommerce'),'default'=> get_option('admin_email')),

            array('field'=>'pi_eqw_email_subject', 'label'=>__('Subject of the email','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>__('New enquiry received', 'pisol-enquiry-quotation-woocommerce'),  'desc'=>__('subject of the email', 'pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_show_message_as_row', 'label'=>__('Show product message as row in email','pisol-enquiry-quotation-woocommerce'),'type'=>'switch','default'=> 1, 'desc'=>__('Show message as row in the email else it will be shown as a columns in the product row','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_email_template', 'label'=>__('Use WooCommerce email template','pisol-enquiry-quotation-woocommerce'),'type'=>'switch','default'=> 1, 'desc'=>__('Use Woocommerce email template all the colors will be as per woocommerce email template ','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_enq_add_img_url', 'label'=>__('Enable this option If product and logo image are not shown in the email or You dont want image attached in email','pisol-enquiry-quotation-woocommerce'),'type'=>'switch','default'=>0, 'desc'=>__('Enable this option only if you are having issue in seeing image inside the enquiry email or You dont want image to be send as attachment in the email','pisol-enquiry-quotation-woocommerce')),


            array('field'=>'pi_eqw_email_to_customer', 'label'=>__('Send enquiry email to customer as well','pisol-enquiry-quotation-woocommerce'),'type'=>'switch','default'=>1, 'desc'=>__('Will send the enquiry email copy to customer as well','pisol-enquiry-quotation-woocommerce'), 'pro'=>true),

            array('field'=>'pi_eqw_customer_email_subject', 'label'=>__('Subject of the email to customer','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>__('Your enquiry is submitted', 'pisol-enquiry-quotation-woocommerce'),  'desc'=>__('Subject of the enquiry email send to customer', 'pisol-enquiry-quotation-woocommerce'), 'pro'=>true),
            array('field'=>'pi_eqw_company_logo', 'label'=>__('Logo added in the email','pisol-enquiry-quotation-woocommerce'),'type'=>'image', 'desc'=>__('This is the image that will be added inside the email copy, sent to you and the customer', 'pisol-enquiry-quotation-woocommerce'),'pro'=>true),

            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Spam protection",'pisol-enquiry-quotation-woocommerce'), 'type'=>"setting_category"),

            array('field'=>'pi_eqw_enable_honeypot', 'label'=>__('Use honeypot for spam protection','pisol-enquiry-quotation-woocommerce'),'type'=>'switch','default'=> 1, 'desc'=>__('This will add an hidden field which user will not fill but spam bot will fill and so the form will not be submitted','pisol-enquiry-quotation-woocommerce')),

            array('field'=>'pi_eqw_captcha', 'label'=>__('Use captcha','pisol-enquiry-quotation-woocommerce'),'type'=>'select','default'=> '', 'desc'=>__('Select the type of captcha to add','pisol-enquiry-quotation-woocommerce'), 'value'=> array(''=>'None','captcha'=>__('Captcha','pisol-enquiry-quotation-woocommerce'))),

            array('field'=>'pi_eqw_captcha_characters','desc'=>'Type of string used in captcha', 'label'=>__('Select type of string to use in the captcha','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>'mix', 'value'=>array('capital_letter'=>'Capital letter','small_letter'=>'Small letter','numbers'=>'Numbers','mix'=>'Mix')),

            array('field'=>'pi_eqw_captcha_length','desc'=>'', 'label'=>__('Captcha string length','pisol-enquiry-quotation-woocommerce'),'type'=>'select', 'default'=>'6', 'value'=>array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6)),

            array('field'=>'pi_eqw_captcha_placeholder','desc'=>'', 'label'=>__('Captcha field placeholder','pisol-enquiry-quotation-woocommerce'),'type'=>'text', 'default'=>'Enter the CAPTCHA'),
            
            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Custom message for customer email', 'pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pi_eqw_customer_email_above_product_table', 'label'=>__('Above product table','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear above the product table','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
            
            array('field'=>'pi_eqw_customer_email_below_product_table', 'label'=>__('Below product table','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear below the product table','pisol-enquiry-quotation-woocommerce'),'pro'=>true),

            array('field'=>'pi_eqw_customer_email_below_customer_detail', 'label'=>__('Below customer detail','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear below the customer detail table','pisol-enquiry-quotation-woocommerce'),'pro'=>true),

            array('field'=>'title', 'class'=> 'hide-pro bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Custom message for admin email', 'pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),

            array('field'=>'pi_eqw_admin_email_above_product_table', 'label'=>__('Above product table','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear above the product table','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
            
            array('field'=>'pi_eqw_admin_email_below_product_table', 'label'=>__('Below product table','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear below the product table','pisol-enquiry-quotation-woocommerce'),'pro'=>true),

            array('field'=>'pi_eqw_admin_email_below_customer_detail', 'label'=>__('Below customer detail','pisol-enquiry-quotation-woocommerce'),'type'=>'textarea', 'default'=>"",   'desc'=>__('This message will appear below the customer detail table','pisol-enquiry-quotation-woocommerce'),'pro'=>true)
            
            
        );
        $this->register_settings();
    }

    function library_warning(){
        $captcha = get_option('pi_eqw_captcha');
        if(!\PISOL_ENQ_CaptchaGenerator::image_library_available() && $captcha == 'captcha'){
            ?>
            <div class="notice notice-error is-dismissible">
                <h3>Enquiry form Captcha issue</h3>
                <p>Captcha requires an image generation module, but neither GD nor Imagick is installed on your server. Please install one of these PHP libraries for Captcha to work, or disable the Captcha setting by visiting the settings page <a href="<?php echo esc_url(admin_url('admin.php?page=pisol-enquiry-quote&tab=email#row_pi_eqw_captcha')); ?>" target="_blank">here</a></p>
                
            </div>
            <?php
        }
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
        $this->tab_name = __('Email','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ) ); ?>">
            <span class="dashicons dashicons-email-alt"></span> <?php echo esc_html( $this->tab_name); ?>
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


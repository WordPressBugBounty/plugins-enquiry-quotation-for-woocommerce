<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_Pi_Eqw_Form_Control{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'form_control';

    private $tab_name = "Form builder (PRO)";

    private $setting_key = 'pi_eqw_form_control';

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

        
    }

    function init(){
        $this->settings = array(
            
            array('field'=>'title', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Making enquiry form field as required.','pisol-enquiry-quotation-woocommerce'), 'type'=>'setting_category'),
            array('field'=>'pi_eqw_name_required','type'=>'switch','label'=>__('Name Field','pisol-enquiry-quotation-woocommerce'),'default'=>1,'pro'=>true),
            array('field'=>'pi_eqw_email_required','type'=>'switch','label'=>__('Email Field','pisol-enquiry-quotation-woocommerce'),'default'=>1,'pro'=>true),
            array('field'=>'pi_eqw_phone_required','type'=>'switch','label'=>__('Phone Field','pisol-enquiry-quotation-woocommerce'),'default'=>1,'pro'=>true),
            array('field'=>'pi_eqw_subject_required','type'=>'switch','label'=>__('Subject Field','pisol-enquiry-quotation-woocommerce'),'default'=>1,'pro'=>true),
            array('field'=>'pi_eqw_message_required','type'=>'switch','label'=>__('Message Field','pisol-enquiry-quotation-woocommerce'),'default'=>1,'pro'=>true),

            array('field'=>'title2', 'class'=> 'bg-dark opacity-75 text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Terms & Condition option','pisol-enquiry-quotation-woocommerce'), 'type'=>"setting_category"),
            array('field'=>'pi_eqw_enable_tandc','type'=>'switch','label'=>__('Enable Terms & Condition','pisol-enquiry-quotation-woocommerce'),'default'=>0, 'desc'=>__('This will show the terms and condition selection option on the enquiry form, user must select this option to submit the enquiry','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
            array('field'=>'pi_eqw_tandc_label','type'=>'textarea','label'=>__('Terms and Condition Text','pisol-enquiry-quotation-woocommerce'),'default'=>"We accept Terms & Conditions", 'desc'=>__('This will be the text shown next to the terms and condition text','pisol-enquiry-quotation-woocommerce'),'pro'=>true),
            
        );
        
        $this->register_settings();
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
        $this->tab_name = __('Form (PRO)','pisol-enquiry-quotation-woocommerce');
        ?>
        <a class="hide-pro px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ) ); ?>">
            <span class="dashicons dashicons-feedback"></span> <?php echo esc_html( $this->tab_name); ?>
        </a>
        <?php
    }

    function tab_content(){
        ?>
        <div class="free-version">
        <img class="img-fluid" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>img/form-control.png">
        </div>
       <?php
    }
}

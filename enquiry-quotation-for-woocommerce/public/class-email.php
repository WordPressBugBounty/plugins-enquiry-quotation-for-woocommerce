<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class class_pisol_eqw_email{

    public $items;
    public $enq_id;
    public $email;
    public $subject;
    public $message;
    public $customer_email;
    public $customer_subject;   
    public $send_copy_to_customer;  
    public $products;

    function __construct($items, $enq_id){

        $this->items = $items;
        $this->enq_id = $enq_id;

        $this->email = $this->explodeEmail(get_option('pi_eqw_email', get_option('admin_email')));


        $this->subject = str_replace('{enquiry_no}',  $this->enq_id, get_option('pi_eqw_email_subject','New Enquiry'));

        $this->message = $this->message();

        $this->customer_email = sanitize_email(wp_unslash($_POST['pi_email']));
        $this->customer_subject = str_replace('{enquiry_no}',  $this->enq_id,__('Your enquiry is submitted, your enquiry no is {enquiry_no}', 'pisol-enquiry-quotation-woocommerce'));
        $this->send_copy_to_customer = 1;

        add_action('phpmailer_init',array($this,'attachInlineImage'));

    }

    function explodeEmail($email_ids){
        $array = explode(',',$email_ids);
        return $array[0];
    }

    function sendEmail(){
        $this->send();
        if($this->send_copy_to_customer == 1){
            $this->sendCustomer();
        }
    }

    function message(){
        ob_start();  

        $woo_template = get_option('pi_eqw_email_template',1);
        if(empty($woo_template)){
            include_once('partials/email-template.php');
        }else{
            include_once('partials/email-template-woo.php');
        }

        $template = ob_get_contents();  
        ob_end_clean();  

        ob_start();  
        $this->products = class_eqw_enquiry_cart::getProductsInEnquirySession();
        include_once('partials/pisol-eqw-email.php');
        $content = ob_get_contents();  
        ob_end_clean();  

        $logo = $this->logo();

        $find = array('{content}','{logo}', '{enquiry_no}');

        $replace = array($content, $logo, $this->enq_id);

        $message = str_replace( $find, $replace, $template);
        
        return $message;
    }

    function send(){
        $headers = array('Content-Type: text/html; charset=UTF-8', 'Reply-To: '.$this->customer_email);
         
        if(wp_mail($this->email, $this->subject, $this->message, $headers)){
           return true;
        }
        return false;
    }

    function sendCustomer(){
        $headers = array('Content-Type: text/html; charset=UTF-8');
         
        if(wp_mail($this->customer_email, $this->customer_subject, $this->message, $headers)){
           return true;
        }
        return false;
    }
    
    function attachInlineImage() {  
        global $phpmailer;  

        $add_img_url  = get_option('pi_enq_add_img_url','');

        if(!empty($add_img_url)) return;

        /** attach logo */
        $file = plugin_dir_path( dirname( __FILE__ ) ) . '/public/img/Logo.png'; //phpmailer will load this file  
        $uid = 'pi_logo'; //will map it to this UID  
        $name = 'Logo.png'; //this will be the file name for the attachment  
        if (is_file($file)) {  
          $phpmailer->AddEmbeddedImage($file, $uid, $name);  
        }  
        /* end attach logo */

        $this->attachProductImages($phpmailer);
    }  

    function attachImage($image_id, $phpmailer){
        $file = get_attached_file($image_id);
        $uid = 'image_'.$image_id;
        $name = basename(get_attached_file($image_id));
        if (is_file($file)) {  
            $phpmailer->AddEmbeddedImage($file, $uid, $name);  
        }  
    }

    function attachProductImages($phpmailer){
        foreach($this->products as $key => $product){
            $prod = wc_get_product($product['id']);
            $image_id = $prod->get_image_id();
            $this->attachImage($image_id, $phpmailer);
        }
    }

    function logo(){
        $show_logo = get_option('pi_eqw_email_disable_logo',1);

        if(empty($show_logo)) return '';

        $add_img_url  = get_option('pi_enq_add_img_url','');

        if(!empty($add_img_url)){
            $image_id = plugin_dir_url( dirname( __FILE__ ) ) . 'public/img/Logo.png';
        }else{
            $image_id = 'cid:pi_logo';
        }

        return '<img alt="Image" border="0" src="'.$image_id.'" style="max-width:100%; width:300px; height:auto;">';
       
    }

    static function imageUrl( $image_id ){
        $img = 'image_'.$image_id;
        $url = 'cid:'.$img;
        $add_img_url  = get_option('pi_enq_add_img_url','');
        if(!empty($add_img_url)){
            $url = wp_get_attachment_url( $image_id );
        }
        $src = apply_filters('pi_eqw_image_src', $url, $image_id);
        return $src;
    }
}



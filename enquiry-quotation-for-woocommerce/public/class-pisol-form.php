<?php

class class_pisol_form{

    public $items;
    public $template;
    public $errors;

    function __construct($items){
        $this->items = $items;
        $this->template = 'template1';
        $this->errors = array();
        if($this->enquiryPresent()){
            $this->form_page();
        }
    }

    function enquiryPresent(){
        return true; //(class_eqw_enquiry_cart::isThereProductsInEnquirySession());
    }

    function form_page(){
       if(!empty($_POST) && count($_POST) > 0 ){
            $this->validation();
            $this->error();
            if($this->submit_form()){
                 $this->success_msg();
            }else{
                $this->form();
            }

       }else{
            $this->form();
       }
    }

    function form(){
        echo '<form method="post" id="pi-eqw-enquiry-form">';
            $this->items();    
        echo '</form>';
    }

    function success_msg(){
            echo '<div class="woocommerce-notices-wrapper">';
            echo '<div class="woocommerce-message"><span id="pi-form-submitted-success"></span>';
            echo esc_html__('Enquiry submitted','pisol-enquiry-quotation-woocommerce');
            echo '</div>';
            echo '</div>';
    }

    function validation(){
        foreach($this->items as $item){
            $this->required($item);
        }
    }

    function required($item){
        $enable_honey_pot = get_option('pi_eqw_enable_honeypot', 1);

        if(!empty($enable_honey_pot) && $item['type'] == 'honeypot'){
            if(isset($_POST[$item['name']]) && $_POST[$item['name']] != ""){
                $this->errors[] = array(
                    'error'=> sprintf(__('Form submitted','pisol-enquiry-quotation-woocommerce'))
                );
                $this->clearSession();
            }
        }

        if($item['type'] == 'captcha' && PISOL_ENQ_CaptchaGenerator::captcha_enabled()){
            if(isset($_POST['captcha_field']) && !empty($_POST['captcha_field'])){
                if( !PISOL_ENQ_CaptchaGenerator::validateCaptcha($_POST['captcha_field']) ){
                    $this->errors['captcha-error'] = array(
                        'error'=> __('Captcha code does not match','pisol-enquiry-quotation-woocommerce')
                    );
                }
            }else{
                $this->errors['captcha-error'] = array(
                    'error'=> __('Captcha cant be left empty','pisol-enquiry-quotation-woocommerce')
                );
            }
        }

        if(isset($item['required']) && $item['required'] == 'required'){
            if(isset($_POST[$item['name']]) && $_POST[$item['name']] != ""){
                return true;
            }else{
                $this->errors[] = array(
                    'error'=> sprintf(__('Cant leave %s empty','pisol-enquiry-quotation-woocommerce'), $item['placeholder'])
                );
            }
        }
        return true;
    }

    function submit_form(){
        if( count($this->errors) <= 0 && !empty($_POST)){
            $save = $this->saveEnquiry();
            if($save !== false){
                $email = $this->sendEmail($save);
                $clear_product = $this->clearSession();
                return true;
            }
        }
        return false;
    }

    function saveEnquiry(){
        $obj  = new class_eqw_save_enquiry($this->items);
        return $obj->save();
    }

    function sendEmail($enq_id){
        $email_obj = new class_pisol_eqw_email($this->items, $enq_id);
        $email_obj->sendEmail();
        return true;
    }
    
    function clearSession(){
        class_eqw_enquiry_cart::deleteProductsFromEnquirySession();
        return true;
    }

    function error(){
        $error_msg = "";
        if(is_array($this->errors) && count($this->errors) > 0){
            foreach($this->errors as $key => $error){
                $error_msg .= '<li data-error-id="'.esc_attr($key).'">'.esc_html($error['error']).'</li>';
            }
        }

        if($error_msg  != ""){
            echo '<div class="woocommerce-notices-wrapper">';
            echo '<div class="woocommerce-error">';
            echo wp_kses_post( $error_msg );
            echo '</div>';
            echo '</div>';
        }
    }

    function items(){
        foreach($this->items as $item){
            $this->item($item);
        }
    }

    function item($item){
        if(method_exists($this,$item['type'])){
            $this->{$item['type']}($item);
        }
    }

    function errorMsgLabel($item){
        $error_label = '';
        if($item['name'] === 'pi_phone'){
            $error_label .= ' data-msg-digits="'.esc_attr__('Please enter only digits.','pisol-enquiry-quotation-woocommerce').'" ';
        }

        if(isset($item['required']) && $item['required'] === 'required'){
            $error_label .= ' data-msg-required="'.esc_attr__('This field is required.','pisol-enquiry-quotation-woocommerce').'" ';
        }

        if($item['name'] === 'pi_email'){
            $error_label .= ' data-msg-email="'.esc_attr__('Please enter a valid email address','pisol-enquiry-quotation-woocommerce').'" ';
        }

        return $error_label;
    }

    function text($item){

        if($item['name'] === 'pi_phone'){
            $rule = ' data-rule-digits ';
        }

        $error_label = $this->errorMsgLabel($item);

        if($item['required'] == 'required'){
            $placeholder = $item['placeholder'].' *';
        }else{
            $placeholder = $item['placeholder'];
        }

       $field = '<input type="text" name="'.esc_attr($item['name']).'" '.($item['required'] == 'required' ? 'required' : '').' placeholder="'.esc_attr($placeholder).'" class="%s"  '.(isset($rule) ? $rule : '').'  '.$error_label.'/>';

       $this->{$this->template}($field, $item);
    }

    function email($item){
        $error_label = $this->errorMsgLabel($item);

        if($item['required'] == 'required'){
            $placeholder = $item['placeholder'].' *';
        }else{
            $placeholder = $item['placeholder'];
        }

        $field = '<input type="email" name="'.$item['name'].'" '.($item['required'] == 'required' ? 'required' : '').' placeholder="'.esc_attr($placeholder).'" class="%s"  '.$error_label.'/>';
 
        $this->{$this->template}($field, $item);
    }

     function submit($item){
        $field = '<input type="submit" value="'.$item['value'].'" class="%s" />';
 
        $this->{$this->template}($field, $item);
     }

     function textarea($item){
        $error_label = $this->errorMsgLabel($item);

        if($item['required'] == 'required'){
            $placeholder = $item['placeholder'].' *';
        }else{
            $placeholder = $item['placeholder'];
        }

        $field = '<textarea name="'.$item['name'].'" '.($item['required'] == 'required' ? 'required' : '').' placeholder="'.esc_attr($placeholder).'" class="%s" '.$error_label.'></textarea>';
 
        $this->{$this->template}($field, $item);
     }

    function template1($field, $item){
        $class_name = isset($item['type']) ? "type-".$item['type'] : "no-type";
        $id = isset($item['name']) ? 'field-container-'.$item['name'] : "";
        echo '<div class="pi-row '.esc_attr($class_name).'" id="'.esc_attr($id).'">';
        echo '<div class="pi-col-12">';
        if($item['type'] == 'submit'){
            printf($field, 'pi-btn pi-btn-primary pi-submit-enq-button');
        }else{
            printf($field, 'pi-form-control');
        }
        echo '</div>';
        echo '</div>';
    }

    function honeypot($item){
        $field = '<span style="display:none; visibility:hidden;"><input type="text" name="'.esc_attr($item['name']).'" style="display:none;"/></span>';

        echo $field;
    }

    function captcha(){
        do_action('pi_eqw_add_captcha_field');
    }
}
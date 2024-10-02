<?php 

class pisol_eqw_webhook{

    static $instance = null;

    static function instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_action('pisol_eqw_enquiry_saved', [$this, 'sendWebhook']);
    }

    function sendWebhook($enq_id){
        $webhook_url = get_option('pi_eqw_webhook_url', '');

        if(empty($webhook_url)){
            return;
        }

        $meta_data = get_post_meta($enq_id);

        if(empty($meta_data) || !is_array($meta_data)){
            return;
        }

        foreach ($meta_data as $key => $values) {
            // Check if the value is an array and has at least one element
            if (is_array($values) && count($values) > 0) {
                // Assign the first value directly to a variable with the meta key as the variable name
                $meta_data[$key] = $values[0];
            }
        }

        $meta_data['pi_products_info'] = unserialize(get_post_meta($enq_id, 'pi_products_info', true));
        
        if(!empty($meta_data)){
            try{
                $response = wp_remote_post($webhook_url, array(
                    'body' => json_encode($meta_data),
                    'headers' => array('Content-Type' => 'application/json'),
                ));
            }catch(Exception $e){
                error_log($e->getMessage());
                return;
            }
        }
    }
}

pisol_eqw_webhook::instance();
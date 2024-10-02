<?php

class Pi_Eqw_Enquiry{

    public $plugin_name;
    public $version;
    public $menu;
    public $enquiry_type;
    
    function __construct($plugin_name , $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->enquiry_type = 'pisol_enquiry';

        add_action( 'init', array($this, 'create_enquiry_type') );
        add_action( 'init', array($this, 'remove_post_type_title') );

        add_action( 'add_meta_boxes', array($this,'user_detail') );

        add_filter( 'manage_edit-pisol_enquiry_columns', array($this,'columnsToList') ) ;

        add_action( 'manage_pisol_enquiry_posts_custom_column', array($this,'columnsContent'), 10, 2 );
    }

    function create_enquiry_type() {
        register_post_type( $this->enquiry_type,
          array(
            'labels' => array(
              'name' => __( 'Enquiries' ),
              'singular_name' => __( 'Enquiry' ),
              'add_new_item' =>__('Enquiry')
            ),
            'public' => false,
            'exclude_from_search' => true,
            'publicaly_queryable' => false,
            'show_ui'=>true,
            'rewrite'=>false,
            'show_in_nav_menus' => false,
            'query_var' => false,
            'has_archive' => false,
            'supports'=>array('title'),
            'menu_icon'=>plugins_url( 'enquiry-quotation-for-woocommerce/admin/img/pi.svg' ),
            /** this hides add post option */
            'capability_type' => 'post',
            'capabilities' => array(
              'create_posts' => 'do_not_allow'
            ),
            'map_meta_cap' => true,
          )
        );
    }

    function remove_post_type_title() {
        remove_post_type_support( 'pisol_enquiry', 'title' );
        remove_post_type_support( 'pisol_enquiry', 'slugdiv' );
    }

    function user_detail(){
      add_meta_box(
        'pisol_enquiry_detail',
        __( 'Enquiry Detail'),
        array($this,'enquiry_detail'),
        $this->enquiry_type
      );
    }

    function enquiry_detail($enquiry){
      include_once('partials/enquiry_detail.php');
    }

    function variation_detail($variation_detail){
      $attributes_group = explode(',', $variation_detail);
      if(is_array($attributes_group) && count($attributes_group) > 0){
        foreach($attributes_group as $attribute){
          if($attribute != ""){
            $pair = explode('|',$attribute);
            echo isset($pair[0]) ? '<strong>'.esc_html($pair[0]).'</strong> : ' : "";
            echo isset($pair[1]) ? '<span>'.esc_html($pair[0]).'</span><br>' : "";
          }
        }
      }
    }

    function columnsToList( $columns ) {

      $columns = array(
        'cb' => '<input type="checkbox" />',
        'id' => __( 'Enq no.', 'pisol-enquiry-quotation-woocommerce' ),
        'title' => __( 'Name', 'pisol-enquiry-quotation-woocommerce'  ),
        'pi_email' => __( 'Email', 'pisol-enquiry-quotation-woocommerce'  ),
        'pi_phone' => __( 'Phone', 'pisol-enquiry-quotation-woocommerce'  ),
        'pi_subject' => __( 'Subject', 'pisol-enquiry-quotation-woocommerce'  ),
        'pi_message' => __( 'Message', 'pisol-enquiry-quotation-woocommerce'  ),
        'date' => __( 'Date', 'pisol-enquiry-quotation-woocommerce'  )
      );
    
      return $columns;
    }

    function columnsContent( $column, $post_id ) {
      global $post;
    
      switch( $column ) {
        case 'id' :
          echo '#'.esc_html( $post_id );
        break;

        case 'pi_email' :
        $pi_email = get_post_meta( $post_id, 'pi_email', true );
        echo esc_html($pi_email);
        break;
    
        case 'pi_phone' :
        $pi_phone = get_post_meta( $post_id, 'pi_phone', true );
        echo esc_html($pi_phone);
        break;
    
        case 'pi_subject' :
        $pi_subject = get_post_meta( $post_id, 'pi_subject', true );
        echo esc_html($pi_subject);
        break;
    
        case 'pi_message' :
        $pi_message = get_post_meta( $post_id, 'pi_message', true );
        echo esc_html($pi_message);
        break;
      }
    }
    
}

new Pi_Eqw_Enquiry($this->plugin_name, $this->version);
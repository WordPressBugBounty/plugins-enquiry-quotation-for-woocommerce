<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function pisol_table_row($products){
    if(is_array($products) && count($products) >0){
        ?>
        
        <?php
        foreach($products as $key => $product){ 
            $product_obj = wc_get_product($product['id']);
            $product_status = get_post_status( $product['id'] );
            if('publish' !== $product_status) continue;

            $product_permalink = $product_obj->get_permalink();
            ?>
        <tr class="woocommerce-cart-form__cart-item" id="<?php echo esc_attr( $key ); ?>">
            <td class="product-remove">
                <a href="javascript:void(0)" class="remove pi-remove-product"  data-id="<?php echo esc_attr( $key ); ?>">&times;</a>
                <input type="hidden" name="products[<?php echo esc_attr( $key ); ?>][id]" value="<?php echo esc_attr( $product['id'] ); ?>"/>
            </td>
            <td class="product-thumbnail pi-thumbnail">
            <?php
				$thumbnail = class_eqw_enquiry_cart::get_image($product['id'], $product['variation']);
                printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );	
                
			?>
            </td>
            <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                <?php printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), esc_html( $product_obj->get_name() ) ); 
                class_eqw_enquiry_shortcode::get_variations($product_obj, $product['variation_detail'], true);
                ?>
            </td>
            <?php if(!class_eqw_advance::checkHidePrice()): ?>
            <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                <?php echo wp_kses_post( wc_price(class_eqw_enquiry_shortcode::get_price_simple_variation($product_obj, $product['variation'])) ); ?>
            </td>
            <?php endif; ?>
            <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                <input type="number" class="input-text qty text pi-quantity" value="<?php echo esc_attr( $product['quantity'] ); ?>" name="products[<?php echo esc_attr( $key ); ?>][quantity]" data-hash="<?php echo esc_attr( $key ); ?>"/>
                <input type="hidden" value="<?php echo (isset($product['variation']) && $product['variation'] != "" && is_array($product['variation'])) ? json_encode($product['variation']) : ''; ?>" data-hash="<?php echo esc_attr( $key ); ?>" name="products[<?php echo esc_attr( $key ); ?>][variation]" />
            </td>
            <td class="product-message" data-title="<?php esc_attr_e( 'Message', 'woocommerce' ); ?>">
                <textarea name="message" class="pi-message" name="products[<?php echo esc_attr( $key ); ?>][message]" data-hash="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( wp_unslash(esc_html($product['message'])) ); ?></textarea>
            </td>
        </tr>
        <?php } ?>
        <tr style="display:none;">
            <td colspan="6" align="right" data-title="<?php echo esc_attr__('Update enquiry','pisol-enquiry-quotation-woocommerce'); ?>">
                <button href="javascript:void(0)" id="pi-update-enquiry" class="button" disabled="disabled">Update enquiry</button>
            </td>
        </tr>
        
        <?php
    }else{
        echo '<tr>';
        echo '<td colspan="6" align="center">';
        echo esc_html__('There are no product added in the enquiry cart','pisol-enquiry-quotation-woocommerce');
        echo '</td>';
        echo '</tr>';
    }
}
<?php

function pisol_email_table_row($products){
    $show_message_as_row = get_option('pi_eqw_show_message_as_row', 1);
    $col_count = 5;
    ?>
<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0" width="100%" border="0" >
		<thead style="background-color:#ccc;">
			<tr>
                <th class="product-image" ></th>
				<th class="product-name"  nowrap><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<?php if(!class_eqw_advance::checkHidePrice()): 
                     $col_count++;    
                ?>
				<th class="product-price"  nowrap><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<?php endif; ?>
                <th class="product-sku"  nowrap><?php esc_html_e( 'SKU', 'woocommerce' ); ?></th>
				<th class="product-quantity"  nowrap><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                <?php if(empty( $show_message_as_row)): ?>
				<th class="product-msg"  nowrap><?php esc_html_e( 'Message', 'woocommerce' ); ?></th>
                <?php endif; ?>
			</tr>
		</thead>
        <tbody id="pi-enquiry-list-row">
    <?php
    if(is_array($products) && count($products) >0){
        ?>
        
        <?php
        foreach($products as $key => $product){ 
            $product_obj = wc_get_product($product['id']);
            $product_permalink = $product_obj->get_permalink();
            $image_id = class_pisol_eqw_email::imageUrl( $product_obj->get_image_id() );
            ?>
        <tr class="woocommerce-cart-form__cart-item" id="<?php echo esc_attr( $key ); ?>">
            <td>
                <img alt="" width="70" height="70" border="0" src="<?php echo ( $image_id ); ?>" style="max-width:70px; width:70px; height:auto;">
            </td>
            <td class="product-name"  style="padding:6px 6px;" nowrap>
                <?php printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), esc_html( $product_obj->get_name() ) ); 
                class_eqw_enquiry_shortcode::get_variations($product_obj, $product['variation_detail'],true);
                ?>
            </td>
            <?php if(!class_eqw_advance::checkHidePrice()): ?>
            <td class="product-price"  style="padding:10px 0; text-align:center;" nowrap>
                <?php echo wp_kses_post( wc_price(class_eqw_enquiry_shortcode::get_price_simple_variation($product_obj, $product['variation'])) ); ?>
            </td>
            <?php endif; ?>
            <td class="product-sku"  style="padding:6px 6px;" nowrap>
                <?php echo esc_html( $product_obj->get_sku() ); ?>
            </td>
            <td class="product-quantity"  style="padding:10px 0; text-align:center;"  nowrap>
                <?php echo esc_html($product['quantity']); ?>
                <input type="hidden" value="<?php echo (isset($product['variation']) && $product['variation'] != "" && is_array($product['variation'])) ? json_encode($product['variation']) : ''; ?>" data-hash="<?php echo esc_attr( $key ); ?>" name="products[<?php echo esc_attr( $key ); ?>][variation]" />
            </td>
            <?php if(empty( $show_message_as_row)): ?>
            <td class="product-message"  style="padding:10px 0; text-align:center;"  nowrap>
            <?php echo wp_kses_post( wp_unslash(esc_html($product['message'])) ); ?>
            </td>
            <?php endif; ?>
        </tr>
            <?php if(!empty( $show_message_as_row) && !empty($product['message'])): ?>
                <tr>
                <td class="product-message"  style="padding:12px; text-align:left;" colspan="<?php echo esc_attr($col_count); ?>">
                <?php echo wp_kses_post( wp_unslash(esc_html($product['message'])) ); ?>
                </td>
                <tr>
            <?php endif; ?>
        <?php } ?>
        
        <?php
    }else{
        echo '<tr>';
        echo '<td colspan="6" align="center">';
        echo esc_html__('There are no product added in the enquiry cart');
        echo '</td>';
        echo '</tr>';
    }
    ?>
</tbody>
</table>
    <?php
}

function pisol_email_form_detail($items){
    $message = '<table class="pi-customer-detail" cellspacing="0" border="0" width="100%">';
    foreach($items as $item){
        if(isset($_POST[$item['name']]) && $_POST[$item['name']] != ""){
            if(isset($item['placeholder'])){
                $val = str_replace("\\","",esc_html($_POST[$item['name']]));
                $message .= '<tr><th  nowrap>'.esc_html($item['placeholder']) ."</th><td>".wp_kses_post( $val ).'</td></tr>';
            }
        }
    }
    $message .='</table>';
    return $message;
}
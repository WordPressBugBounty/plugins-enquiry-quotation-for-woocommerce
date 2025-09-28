
<?php if(empty($products)): ?>
    <p><?php esc_html_e('No products in enquiry cart','pisol-enquiry-quotation-woocommerce'); ?></p>
<?php else: ?>
        <?php foreach($products as $key => $product): ?>
            <div class="pi-mini-cart-item">
                <div class="thumbnail">
                    <?php echo wp_kses_post( $product['thumbnail'] ); ?>
                </div>
                <div class="details">
                    <div class="name-qty"><span class="name"><a href="<?php echo esc_url($product['permalink']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $product['name'] ); ?></a></span> &times; <span class="quantity"><?php echo esc_html( $product['quantity'] ); ?></span></div>
                    <div class="price"><?php echo wp_kses( wc_price( $product['price'] ), array(
                        'span' => array(
                            'class' => array(),
                        ),
                        'bdi'  => array(),
                    ) ); ?></div>
                </div>
                <div class="remove">
                    <a href="javascript:void(0)" data-id="<?php echo esc_attr($key); ?>" class="remove pi-remove-product" title="<?php esc_attr_e('Remove this item','pisol-enquiry-quotation-woocommerce'); ?>"><img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'img/remove.svg' ); ?>" alt="<?php esc_attr_e( 'Remove', 'pisol-enquiry-quotation-woocommerce' ); ?>" class="remove"></a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php 
        $enquiry_cart_page_id = get_option('pi_eqw_enquiry_cart',0);
		if($enquiry_cart_page_id != 0 && $enquiry_cart_page_id != ""){
			$cart_page = apply_filters('pisol_enq_add_to_enquiry_redirect_url',get_permalink($enquiry_cart_page_id));
		}else{
			$cart_page = false;
		}
        echo '<footer>';
        if($cart_page){
            printf('<a href="%1$s" class="button">%2$s</a>', esc_url($cart_page), esc_html__('Submit enquiry','pisol-enquiry-quotation-woocommerce'));
        }
        echo '</footer>';
        ?>
<?php endif; ?>
    
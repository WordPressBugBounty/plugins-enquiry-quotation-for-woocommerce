
<?php if(empty($products)): ?>
    <p><?php _e('No products in enquiry cart','pisol-enquiry-quotation-woocommerce'); ?></p>
<?php else: ?>
        <?php foreach($products as $key => $product): ?>
            <div class="pi-mini-cart-item">
                <div class="thumbnail">
                    <?php echo $product['thumbnail']; ?>
                </div>
                <div class="details">
                    <div class="name-qty"><span class="name"><a href="<?php echo esc_url($product['permalink']); ?>" target="_blank"><?php echo $product['name']; ?></a></span> &times; <span class="quantity"><?php echo $product['quantity']; ?></span></div>
                    <div class="price"><?php echo wc_price($product['price']); ?></div>
                </div>
                <div class="remove">
                    <a href="javascript:void(0)" data-id="<?php echo esc_attr($key); ?>" class="remove pi-remove-product" title="<?php _e('Remove this item','pisol-enquiry-quotation-woocommerce'); ?>"><img src="<?php echo plugin_dir_url( __DIR__ ).'img/remove.svg'; ?>" alt="remove" class="remove"></a>
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
            echo '<a href="'.$cart_page.'" class="button">'.__('Submit enquiry','pisol-enquiry-quotation-woocommerce').'</a>';
        }
        echo '</footer>';
        ?>
<?php endif; ?>
    
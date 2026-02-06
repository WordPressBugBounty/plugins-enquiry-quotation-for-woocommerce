<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pisol-enquiry-detail-container">
    <h2>Enquiry #<?php echo esc_html( $enquiry->ID ); ?> detail</h2>
    <hr>
    <h3>Personal Detail</h3>
    <table class="pi-personal-detail">
        <tr>
            <td><strong><?php echo esc_html__('Name','pisol-enquiry-quotation-woocommerce'); ?></strong> : <?php echo esc_html($enquiry->pi_name); ?></td>
            <td><strong><?php echo esc_html__('Email','pisol-enquiry-quotation-woocommerce'); ?></strong> : <a href="mailto:<?php echo esc_attr($enquiry->pi_email); ?>"><?php echo esc_html($enquiry->pi_email); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php echo esc_html__('Phone','pisol-enquiry-quotation-woocommerce'); ?></strong> : <?php echo esc_html($enquiry->pi_phone); ?></td>
            <td><strong><?php echo esc_html__('Subject','pisol-enquiry-quotation-woocommerce'); ?></strong> : <?php echo esc_html($enquiry->pi_subject); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <strong><?php echo esc_html__('Message','pisol-enquiry-quotation-woocommerce'); ?></strong> : <?php echo esc_html($enquiry->pi_message); ?>
            </td>
        </tr>
    </table>
    <hr>
    <h3>Product Detail</h3>
    <table class="pi-product-table" cellspacing="0">
        <thead>
        <tr>
            <th class="pi-img-col">Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Message</th>
        </tr>
        </thead>
        <tbody>
        <?php $pi_products_info = unserialize(get_post_meta($enquiry->ID, 'pi_products_info', true), ['allowed_classes' => false]); 
        
        ?>
        <?php if(is_array($pi_products_info)): ?>
        <?php  foreach($pi_products_info as $product): ?>
        <tr>
            <td class="pi-thumb-col">
                <?php if($product['img'] != ""): ?>
                <a href="<?php echo esc_url($product['link']); ?>" target="_blank"><img class="pi-thumb" src="<?php echo esc_url($product['img']); ?>"></a>
                <?php endif; ?> 
            </td>
            <td>
            <a href="<?php echo esc_url($product['link']); ?>" target="_blank"><?php echo esc_html($product['name']); ?></a><br>
            <?php $this->variation_detail($product['variation_detail']); ?>
            </td>
            <td class="pi-bold"><?php echo esc_html($product['price']); ?></td>
            <td class="pi-bold"><?php echo esc_html($product['quantity']); ?></td>
            <td><?php echo wp_kses_post( wp_unslash(esc_html($product['message'])) ); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
</tbody>
    </table>
</div>

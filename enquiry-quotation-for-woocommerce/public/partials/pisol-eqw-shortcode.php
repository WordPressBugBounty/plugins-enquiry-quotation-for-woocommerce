<?php
//old style 'shop_table shop_table_responsive cart woocommerce-cart-form__contents' 
$table_style = apply_filters('pisol_eqw_table_style', 'enquiry-cart-content-table');
?>
<div class="woocommerce" id="pi-enquiry-container">
<table class="<?php echo esc_attr($table_style); ?>" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<?php if(!class_eqw_advance::checkHidePrice()): ?>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<?php endif; ?>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th class="product-message"><?php esc_html_e( 'Message', 'pisol-enquiry-quotation-woocommerce' ); ?></th>
			</tr>
		</thead>
        <tbody id="pi-enquiry-list-row">
        <?php //pisol_table_row($this->products); ?>
        </tbody>
</table>
<?php 
/**
 * Placeholder is needed as it is used for label in email
 */
$items = array(
	array('type'=>'text', 'name'=>'pi_name', 'required'=>'required', 'placeholder'=>__('Name','pisol-enquiry-quotation-woocommerce')),
	array('type'=>'email', 'name'=>'pi_email', 'required'=>'required', 'placeholder'=>__('Email','pisol-enquiry-quotation-woocommerce')),
	array('type'=>'text', 'name'=>'pi_phone', 'required'=>'required', 'placeholder'=>__('Phone','pisol-enquiry-quotation-woocommerce')),
	array('type'=>'text', 'name'=>'pi_subject', 'required'=>'required', 'placeholder'=>__('Subject','pisol-enquiry-quotation-woocommerce')),
	array('type'=>'textarea', 'name'=>'pi_message', 'required'=>'required', 'placeholder'=>__('Message','pisol-enquiry-quotation-woocommerce')),
);

$honey_pot = get_option('pi_eqw_enable_honeypot', 1);
if(!empty($honey_pot)){
	$items[] = array('type'=>'honeypot', 'name'=>'name_yenoh');
}

$captcha_enabled = PISOL_ENQ_CaptchaGenerator::captcha_enabled();
if($captcha_enabled){
	$items[] = array('type'=>'captcha', 'name'=>'captcha_input');
}

$items[] = array('type'=>'submit', 'name'=>'pi_submit',  'value'=>__('Submit Enquiry','pisol-enquiry-quotation-woocommerce'));

new class_pisol_form($items); 
?>
</div>

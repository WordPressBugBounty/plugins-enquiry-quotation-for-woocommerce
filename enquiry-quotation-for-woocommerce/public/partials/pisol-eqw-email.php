<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pisol_Enquiry_Quotation_Woocommerce
 * @subpackage Pisol_Enquiry_Quotation_Woocommerce/public/partials
 */

//print_r($this->products);
?>
<div class="woocommerce" id="pi-enquiry-container">
<?php pisol_email_table_row($this->products); ?>
<table class="pisol-form-detail-container" cellspacing="0" width="100%" border="0">
			<tr>
				<td>
					<?php 
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already sanitized in pisol_email_form_detail()
						echo pisol_email_form_detail($this->items); 
					?>
				</td>
			</tr>
</table>
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

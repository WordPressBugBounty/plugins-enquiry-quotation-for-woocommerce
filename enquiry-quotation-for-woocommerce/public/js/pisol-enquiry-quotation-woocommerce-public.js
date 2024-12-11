(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(function ($) {

		var add_to_enquiry = {

			init: function () {

				this.initialLoad();

				$(document).on('click', '.add-to-enquiry', function (event) {
					event.preventDefault();
					add_to_enquiry.sendData(this);
				});

				$(document).on('click', '.pi-remove-product', function () {
					var hash = $(this).data('id');
					add_to_enquiry.removeProduct(hash);
				});

				$(document).on('click', '#pi-update-enquiry', function () {
					var products = window.pisol_products;
					add_to_enquiry.updateProduct(products);
				});

				$(document).on('change', '.pi-quantity', function () {
					add_to_enquiry.enableUpdate();
					var new_quantity = $(this).val();
					var hash = $(this).data('hash');
					window.pisol_products[hash]['quantity'] = new_quantity;
					var products = window.pisol_products;
					add_to_enquiry.updateProduct(products);
				});

				$(document).on('change', '.pi-message', function () {
					add_to_enquiry.enableUpdate();
					var new_message = $(this).val();
					var hash = $(this).data('hash');
					window.pisol_products[hash]['message'] = new_message;
					var products = window.pisol_products;
					add_to_enquiry.updateProduct(products);
				});

				add_to_enquiry.successSubmit();
			},

			successSubmit: function () {
				/** this hides the cart on success full submition */
				if ($("#pi-form-submitted-success").length) {
					$(".shop_table_responsive").css("display", "none");
				}
			},

			getData: function (button) {
				var action = $(button).data('action');
				var id = $(button).data('id');
				var variation_id = add_to_enquiry.variationSelected();
				var variation_detail = add_to_enquiry.variationDetail();
				var quantity = add_to_enquiry.quantity(id);
				return { action: action, id: id, variation_id: variation_id, quantity: quantity, variation_detail: variation_detail };
			},

			variationSelected: function () {
				var variation_selected = $("form.variations_form input[name='variation_id']").val();

				if (typeof variation_selected != "undefined" && variation_selected != 0) {
					return parseInt(variation_selected);
				}
				return 0;
			},

			variationDetail: function () {
				var variation_selected = $("form.variations_form input[name='variation_id']").val();
				var variation_detail = {};
				if (typeof variation_selected != "undefined" && variation_selected != 0) {
					jQuery('select[name^=attribute_]').each(function (ind, obj) {
						variation_detail[jQuery(this).attr('name')] = jQuery(this).val();
					});
				}
				if (jQuery.isEmptyObject(variation_detail)) {
					return 0;
				}
				return variation_detail;
			},

			quantity: function (id) {

				var quantity = $('form.cart input[name="quantity"]').val();

				if (typeof quantity != "undefined") {
					return quantity;
				}
				return 1;
			},

			sendData: function (button) {
				add_to_enquiry.data = add_to_enquiry.getData(button);

				if (add_to_enquiry.alertIfVariationNotSelected()) {
					add_to_enquiry.addingToCart(button);
					var action = 'pi_add_to_enquiry';
					jQuery.post(pi_ajax.wc_ajax_url.toString().replace('%%endpoint%%', action), add_to_enquiry.data, function (response) {
						add_to_enquiry.addedToCart(button);
					});
				}
			},

			removeProduct: function (hash) {
				add_to_enquiry.showLoading();
				var action = 'pi_remove_product';
				jQuery.post(pi_ajax.wc_ajax_url.toString().replace('%%endpoint%%', action), { hash: hash }, function (response) {
					add_to_enquiry.dataLoaded(response);
				});
			},

			updateProduct: function (products) {
				add_to_enquiry.showLoading();
				var action = 'pi_update_products';
				jQuery.post(pi_ajax.wc_ajax_url.toString().replace('%%endpoint%%', action), {  products: products }, function (response) {
					add_to_enquiry.dataLoaded(response);
				});
			},

			initialLoad: function () {
				if(jQuery("#pi-enquiry-container").length <= 0) return;

				add_to_enquiry.showLoading();
				var action = 'get_cart_on_load';
				jQuery.post(pi_ajax.wc_ajax_url.toString().replace('%%endpoint%%', action) ,{}, function (response) {
					add_to_enquiry.dataLoaded(response);
				});
			},

			dataLoaded: function (response) {
				var decoded = JSON.parse(response);
				$("#pi-enquiry-list-row").html(decoded.cart);
				window.pisol_products = decoded.pisol_products;
				add_to_enquiry.hideLoading();
				add_to_enquiry.formVisibilityCheck(decoded.pisol_products);
			},

			formVisibilityCheck: function (products) {
				if (products == null || products.length == 0) {
					jQuery("#pi-eqw-enquiry-form").css("display", "none");
				} else {
					jQuery("#pi-eqw-enquiry-form").css("display", "block");
					add_to_enquiry.cacheBusting();
				}
			},

			cacheBusting: function () {
				var date = new Date();
				var timestamp = date.getTime();
				jQuery("#pi-eqw-enquiry-form").attr('action', '?cache_bust=' + timestamp);
			},

			enableUpdate: function () {
				$('#pi-update-enquiry').removeAttr('disabled');
			},

			showLoading: function () {
				$('#pi-enquiry-container').block({
					message: '<img src="' + pi_ajax.loading + '" />',
					css: {
						width: '40px',
						height: '40px',
						top: '50%',
						left: '50%',
						border: '0px',
						backgroundColor: "transparent"
					},
					overlayCSS: {
						background: "#fff",
						opacity: .7
					}
				});
			},

			hideLoading: function () {
				$('#pi-enquiry-container').unblock();
			},

			alertIfVariationNotSelected: function () {
				if (jQuery('.variation_id').length > 0 && jQuery('.variation_id').val() == '' || jQuery('.variation_id').val() == 0) {
					alert('Variation not selected');
					return false;
				}
				return true;
			},

			addingToCart: function (button) {
				$(button).addClass('loading');
			},

			addedToCart: function (button) {
				$(button).removeClass('loading');
				$(button).addClass('added');
				add_to_enquiry.viewEnquiryCart(button);
			},

			viewEnquiryCart: function (button) {
				var url = pi_ajax.cart_page;
				if (url != false) {
					$(".pisol-view-cart").remove();
					$(button).after('<a class="pisol-view-cart"  href="' + url + '">' + pi_ajax.view_enquiry_cart + '</a>');
				}
			}


		}

		add_to_enquiry.init();


		function formValidation() {
			this.init = function () {
				jQuery("#pi-eqw-enquiry-form").validate();
				this.submit();
			}

			this.submit = function () {
				jQuery(document).on('submit', "#pi-eqw-enquiry-form", function (e) {
					jQuery(".pi-submit-enq-button").prop('disabled', 'disabled');
				});
			}
		}

		var formValidationObj = new formValidation();
		formValidationObj.init();

	})

})(jQuery);

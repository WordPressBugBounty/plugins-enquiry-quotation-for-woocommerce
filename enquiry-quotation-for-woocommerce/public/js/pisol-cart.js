(function ($) {
    'use strict';
    jQuery(function ($) {

        function pisol_eqw_cart() {
            this.init = function () {
                this.registerEvent();
                this.delayedInitialTrigger();
                this.openPopup();
                this.closeMiniCart();
                this.miniCartProcessing();
            }

            /**
             * we are delaying initial load as this was causing get_refreshed_fragments request to time out as it has time out time of 5000 so delaying this we will make get_refreshed_fragments fire before this request
             */
            this.delayedInitialTrigger = function () {
                setTimeout(function () {
                    jQuery(document).trigger("pisol_update_enquiry");
                }, 20);
            }

            this.registerEvent = function () {
                var parent = this;
                jQuery(document).on("pisol_update_enquiry pisol_enquiry_product_removed", function () {
                    parent.getResponse();
                });
            }

            this.getResponse = function () {
                var parent = this;
                var action = 'pi_get_cart_json';
                jQuery(document).trigger("pisol_eqw_loading_mini_cart");
                jQuery.ajax({
                    url: pi_ajax.wc_ajax_url.toString().replace('%%endpoint%%', action),
                    dataType: "json",
                    type: 'POST',
                    success: function (response) {
                        jQuery("#pi-eqw-cart .pi-count").html(response.count);
                        jQuery("#pi-eqw-mini-cart content").html(response.mini_cart);

                        if(pi_ajax.count_selector != ''){
                            jQuery(pi_ajax.count_selector).html(response.count);
                        }
                        
                        jQuery(document).trigger("pisol_enquiry_cart_count", [response.count, response]);

                        if( typeof response.products != "undefined") {
                            parent.tick(response.products);
                        }
                    }
                });
            }

            this.tick = function (response) {
				if (typeof response !== 'object' || response === null || Array.isArray(response)) {
					jQuery(".added-to-enq-cart").removeClass("added-to-enq-cart");
					return;
				}

				jQuery(".added-to-enq-cart").removeClass("added-to-enq-cart");
				for (const key in response) {
					if ( response.hasOwnProperty(key) && response[key] && typeof response[key].id !== 'undefined') {
						jQuery('.pi-enq-product-' + response[key].id).addClass('added-to-enq-cart');
					}
				}
			}

            this.openPopup = function () {
                jQuery(document).on('click', '#pi-eqw-cart', function (e) {
                    
                        e.preventDefault();
                        pisol_eqw_cart.showMiniCart();
                    
                });
            }

            this.showMiniCart = function () {
                jQuery("#pi-eqw-mini-cart").css("display", "grid");
            }

            this.closeMiniCart = function () {
                jQuery(document).on('click', '#pi-eqw-mini-cart .close-mini-cart', function (e) {
                    e.preventDefault();
                    jQuery("#pi-eqw-mini-cart").css("display", "none");
                });
            }

            this.processing = function (show) {
                if (show) {
                    jQuery("#pi-eqw-mini-cart content").addClass("processing");
                }else{
                    jQuery("#pi-eqw-mini-cart content").removeClass("processing");
                }
            }

            this.miniCartProcessing = function () {
                jQuery(document).on('pisol_eqw_removing_product pisol_eqw_loading_mini_cart', function(){
                    pisol_eqw_cart.processing(true);
                });

                jQuery(document).on('pisol_enquiry_product_removed pisol_enquiry_cart_count', function(){
                    pisol_eqw_cart.processing(false);
                });
            }

        }


        var pisol_eqw_cart = new pisol_eqw_cart();
        pisol_eqw_cart.init();

    });
})(jQuery);
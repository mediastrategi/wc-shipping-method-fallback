if (
    typeof(jQuery) !== 'undefined'
        && typeof(WC_Shipping_Method_Fallback) !== 'undefined'
) {
    /* global jQuery WC_Shipping_Method_Fallback */
    (function($) {
        "use strict";
        $(document).ready(function() {
            if ($('#woocommerce_wc-shipping-method-fallback_shipping_method').length) {
                // Load shipping methods via AJAX
                $('#mainform').addClass('ajax-spinner');
                $.ajax(
                    {
                        cache: false,
                        data: {
                            action: 'wc_shipping_method_fallback_shipping_methods'
                        },
                        dataType: 'json',
                        error: function(response) {
                            console.error(response);
                            $('#mainform').removeClass('ajax-spinner');
                            alert('Failed to load shipping methods! See console for more information.');
                        },
                        method: 'POST',
                        success: function(shippingMethods) {
                            $('#mainform').removeClass('ajax-spinner');
                            var selected = $('#woocommerce_wc-shipping-method-fallback_shipping_method').val();
                            var selectHtml = '<select id="woocommerce_wc-shipping-method-fallback_shipping_method_select">', selectKey, select;
                            for (selectKey in shippingMethods) {
                                if (shippingMethods.hasOwnProperty(selectKey)) {
                                    select = shippingMethods[selectKey];
                                    selectHtml += '<option'
                                        + ((select == selected) ? ' selected="selected"' : '')
                                        + '>' + select + '</option>';
                                }
                            }
                            selectHtml += '</select>';
                            $('#woocommerce_wc-shipping-method-fallback_shipping_method').before($(selectHtml));
                            $('#woocommerce_wc-shipping-method-fallback_shipping_method_select').change(function() {
                                $('#woocommerce_wc-shipping-method-fallback_shipping_method').val(
                                    $('option:checked', this).val()
                                );
                            });
                            $('#woocommerce_wc-shipping-method-fallback_shipping_method_select').trigger('change');
                        },
                        url: WC_Shipping_Method_Fallback.AjaxUrl
                    }
                );
            }
        });
    })(jQuery);
}

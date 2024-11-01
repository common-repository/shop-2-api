//bol-get-best-price.php javascript

(function ($) {
    $(document).ready(function () {
        $("input[type='number']").on("click", function () {
            $(this).select();
        });

        retrieve_from_shop2api();

        // Save Order data
        $(document).on('click', '#save-order-data', function (event) {
            event.preventDefault();
            let data = {
                'action': 'set_order_data',
                'nonce': settings.nonce
            };
            let form = document.querySelector('#orders-form');
            let form_data = serialize(new FormData(form));
            data = {...data, ...form_data}
            $.post(settings.ajaxurl, data, function (response) {
                $(".error-message").hide();
                if (response.success === true) {
                    if (response.data && response.data.response) {
                        if (response.data.response.code === 200) {
                            location.reload();
                        } else {
                            $("#registration-error").text("There was an issue updating the data.");
                            console.log("There was an error updating the data:" + response.data);
                        }
                    }
                } else {
                    json_data = JSON.parse(response.data);
                    $.each(json_data, function (k, v) {
                        $('#' + k).text(v);
                        $('#' + k).show();
                    });
                }
            });
        });

        // Select change
        $(document).on( 'change', '#orders-ean', handle_cat_change_ean);
        $(document).on( 'change', '#orders-combideal-ean', handle_cat_change_ean_combi);
    })

    function handle_cat_change_ean() {
        $('#orders-ean-value').children().remove()
        if ($(this).val() === 'Product') {
            populate_product_dropdown($('#orders-ean-value'));
        }
        if ($(this).val() === 'MetaData') {
            populate_metadata_dropdown($('#orders-ean-value'));
        }
    }

    function handle_cat_change_ean_combi() {
        $('#orders-combideal-ean-value').children().remove()
        if ($(this).val() === 'Product') {
            populate_product_dropdown($('#orders-combideal-ean-value'));
        }
        if ($(this).val() === 'MetaData') {
            populate_metadata_dropdown($('#orders-combideal-ean-value'));
        }
    }

    function serialize(data) {
        let obj = {};
        for (let [key, value] of data) {
            if (obj[key] !== undefined) {
                if (!Array.isArray(obj[key])) {
                    obj[key] = [obj[key]];
                }
                obj[key].push(value);
            } else {
                obj[key] = value;
            }
        }
        return obj;
    }

    function populate_product_dropdown(_select_ean) {
        let woocommerce_option_properties = settings.wc_field_options.success.schema.properties;

        for (const item in woocommerce_option_properties) {
            let obj = woocommerce_option_properties[item];

            if (obj.type === "array") continue;
            if (obj.type === 'object') {
                for (const sub_item in obj.properties) {
                    let value_text = item + '|' + sub_item;
                    let dropdown_values = {"value": value_text, "text": value_text}
                    $('<option />', dropdown_values).appendTo(_select_ean);
                }
            } else {
                let dropdown_values = {"value": item, "text": item}
                $('<option />', dropdown_values).appendTo(_select_ean);
            }
        }

    }

    function retrieve_from_shop2api() {
        let json_data = settings.orders_data;
        if (json_data.length !== 0) {
            let order_data = json_data[0];

            $('#orders-active').prop('checked', order_data.active);
            $('#orders-combideals-active').prop('checked', order_data.combi_deal_active);
            $('#orders-status').val(order_data.status);
            $('#orders-paid').prop('checked', order_data.paid);
            $('#orders-use-bol-price').prop('checked', order_data.use_bol_price);
            $('#orders-stock-sync').prop('checked', order_data.sync_stock);
            $('#orders-email').val(order_data.order_email);
            $('#orders-alert-email').val(order_data.alert_email);
            $('#orders-alert-on-order-fail').prop('checked', order_data.alert_on_order_fail);
            $('#orders-bol-price-include-tax').prop('checked', order_data.bol_price_include_tax);

            document.getElementById("orders-ean").value = order_data.woocommerce_ean_category;
            document.getElementById("orders-combideal-ean").value = order_data.combi_deal_ean_category;

            if (order_data.woocommerce_ean_category === 'Product') {
                populate_product_dropdown($('#orders-ean-value'));

            } else {
                populate_metadata_dropdown($('#orders-ean-value'));
            }
            if (order_data.combi_deal_ean_category === 'Product') {
                populate_product_dropdown($('#orders-combideal-ean-value'));
            } else {
                populate_metadata_dropdown($('#orders-combideal-ean-value'));
            }

            document.getElementById("orders-ean-value").value = order_data.woocommerce_ean_field;
            document.getElementById("orders-combideal-ean-value").value = order_data.combi_deal_ean_field;
            return
        }

        populate_product_dropdown($('#orders-ean-value'));
        document.getElementById("orders-ean-value").value = 'sku';
        populate_product_dropdown($('#orders-combideal-ean-value'));
        document.getElementById("orders-combideal-ean-value").value = 'sku';
    }

    function populate_metadata_dropdown(_select_ean_dropdown) {
        for (let i in settings.wc_metadata_dropdown_values.success) {
            let wc_metadata_val = settings.wc_metadata_dropdown_values.success[i];

            let dropdown_values = {
                'value': wc_metadata_val['bol_category'],
                'text': wc_metadata_val['woocommerce_field']
            }

            $('<option />', dropdown_values).appendTo(_select_ean_dropdown);
        }
    }
})(jQuery);

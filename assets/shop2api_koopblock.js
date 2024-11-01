//bol-get-best-price.php javascript

(function ($) {
    $(document).ready(function () {
        $("input[type='number']").on("click", function () {
            $(this).select();
        });

        retrieve_from_shop2api();
        $(document).on( 'change', '#orders-ean', handle_cat_change_ean);

        //Save Koopblok data
        $(document).on('click', '#save-koopblok-data', function (event) {
            event.preventDefault();
            let data = {
                'action': 'set_koopblok_data',
                'nonce': settings.nonce
            };
            let form = document.querySelector('#koopblok-form');
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
    })

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

    function retrieve_from_shop2api() {

        let json_data = settings.koopblok_data;
        if (json_data.length !== 0) {
            let koopblok_data = json_data[0];
            $('#koopblok-active').prop('checked', koopblok_data.active);
            $('#koopblok-update-wc').prop('checked', koopblok_data.on_success_update_woocommerce);
            $('#wc-price-field').val(koopblok_data.woocommerce_price_field);
            $('#wc-ean-field').val(koopblok_data.woocommerce_ean_field);
            $('#koopblok-price-increments').val(koopblok_data.price_increments);
            $('#koopblok-price-limit').val(koopblok_data.minimum_price_limit);
            $('#koopblok-price-limit-max').val(koopblok_data.maximum_price_limit);
            $('#koopblok-seller-id').val(koopblok_data.seller_id);

            document.getElementById("orders-ean").value = koopblok_data.woocommerce_ean_category;

            if (koopblok_data.woocommerce_ean_category === 'Product') {
                populate_product_dropdown($('#orders-ean-value'));

            } else {
                populate_metadata_dropdown($('#orders-ean-value'));
            }

            document.getElementById("orders-ean-value").value = koopblok_data.woocommerce_ean_field;
            return
        }

        populate_product_dropdown($('#orders-ean-value'));
        document.getElementById("orders-ean-value").value = 'sku';
    }

    function handle_cat_change_ean() {
        $('#orders-ean-value').children().remove()
        if ($(this).val() === 'Product') {
            populate_product_dropdown($('#orders-ean-value'));
        }
        if ($(this).val() === 'MetaData') {
            populate_metadata_dropdown($('#orders-ean-value'));
        }
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
})(jQuery);

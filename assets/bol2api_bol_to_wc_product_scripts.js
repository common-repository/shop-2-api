(function ($) {
    $(document).ready(function () {
        $('#submit-data').hide();
        $('#search-ean-number').on('click', search_ean_number);

        // Select change
        $(document).on( 'change', '#bol-wc-field-type', handle_cat_change_ean);
        $( '.shop-2-api-connect-submit-bol-to-wc' ).on( 'click', submit_wc_bol_data);
    });

    function add_attribute_row(item, index, arr) {
        let type_field_type = '<select name="bol-wc-field-type" id="bol-wc-field-type"><option value="Exclude" selected>Not Mapped</option><option value="Product">WooCommerce Product Field</option><option value="MetaData">WooCommerce Metadata Field</option></select><span id="sku" className="error-message"></span>';
        let type_field_value = '<select name="bol-wc-field-value" id="bol-wc-field-value" hidden></select>';

        let tableBody = document.getElementById("wc-to-bol-table-body");
        let newRow = tableBody.insertRow();
        newRow.insertCell().innerHTML = item.id ;
        let description = ""
        for (let i = 0; i < item.values.length; i++) {
            description = description.concat(item.values[i].value).concat(' ')
        }

        newRow.insertCell().innerHTML = "<label>" +  description + "</label>";
        newRow.insertCell().innerHTML = type_field_type;
        newRow.insertCell().innerHTML = type_field_value;
    }

    function search_ean_number() {
        let data = {
            'ean_number': $("#ean-number").val(),
            'action': 'get_shop_2_api_bol_product_data',
            'nonce': settings.nonce,
        };

        $.post(settings.ajaxurl, data, function (response) {
            if (response.success === true) {
                $('#submit-data').show();
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200 || response.data.response.code === 201) {
                        const obj = JSON.parse(response.data.body)
                        let attributes = obj.attributes
                        attributes.forEach(add_attribute_row);


                        $('#common-success').text('Search Completed').show().fadeOut(5000);
                    } else {
                        alert("There was an error saving the data:" + response.data);
                    }
                }
            } else {
                $('#common-error').text('Could get the data from Bol.com').show()
            }
        });
    }

    function handle_cat_change_ean() {
        let wc_field_value_dropdown = $(this).closest('tr').find('#bol-wc-field-value');
        wc_field_value_dropdown.children().remove()
        if ($(this).val() === 'Product') {
            populate_product_dropdown(wc_field_value_dropdown);
        }
        if ($(this).val() === 'MetaData') {
            populate_metadata_dropdown(wc_field_value_dropdown);
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
                    $('<option />', dropdown_values).appendTo(_select_ean).hide();
                }
            } else {
                let dropdown_values = {"value": item, "text": item}
                $('<option />', dropdown_values).appendTo(_select_ean);
            }
        }
        _select_ean.show();
    }

    function populate_metadata_dropdown(_select_ean_dropdown) {
        for (let i in settings.wc_metadata_dropdown_values.success) {
            let wc_metadata_val = settings.wc_metadata_dropdown_values.success[i];

            let dropdown_values = {
                'value': wc_metadata_val['bol_category'],
                'text': wc_metadata_val['woocommerce_field']
            }

            $('<option />', dropdown_values).appendTo(_select_ean_dropdown).hide();
        }
        _select_ean_dropdown.show();
    }

    function submit_wc_bol_data() {
        let data = {
            'action' : 'set_shop_2_api_wc_to_bol_submit',
            'nonce'  : settings.nonce,
            'map_data': convert_mapping_to_json()
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success === true ) {
                if (response.data) {
                    $('#common-success').text('Save Completed')
                    $('#common-success').show().fadeOut(5000);
                    window.scrollTo(0, 0);
                } else {
                    alert("There was an error saving the data:" + response.data);
                }
            } else {
                let error_data = JSON.parse(response.data)
                $('#common-error').text('Error Saving: ' + error_data.error).show()
            }
        });
    }

    function convert_mapping_to_json() {
        // Get a reference to the table
        let table = document.getElementById("wc-to-bol-data-table");
        let data = [];
        // Loop through the rows
        for (let i = 1; i < table.rows.length; i++) {
            let row = table.rows[i];
            if ($(row).find('td #bol-wc-field-type').val() !== 'Exclude') {
                data.push(
                    {
                        "bol-field": $(row).find('td label').html(),
                        "bol-field-type": $(row).find('td #bol-wc-field-type').val(),
                        "bol-field-value": $(row).find('td #bol-wc-field-value').val(),
                    }
                )
            }
        }
        return data;
    }
})(jQuery);

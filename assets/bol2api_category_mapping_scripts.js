(function ($) {
    $(document).ready(function () {
        // Get all the categories and if there is any saved populate saved_bol_category_data
        set_wc_category_field_dropdown();
        // Save Data
        $('#category-save').on('click', set_bol_wc_mapping_info);
    });

    function get_table_data() {
        let tableData = [];
        $("#the-list tr").each(function() {
            let rowData = {};
            $(this).find('input, select').each(function() {
                if ($(this).is(":checkbox")) {
                    rowData[this.name] = $(this).is(":checked");
                } else {
                    rowData[this.name] = $(this).val();
                }
                $.extend(rowData, $(this).data());
            });
            tableData.push(rowData);

        });

        return tableData;
    }


    // This will set the Top dropdown with the data from woocommerce_option_data
    function set_wc_category_field_dropdown() {
        let data_container = $('span[name="wc-data-container"]');
        let categories = {
            "categories": "All Categories",
            "main_categories": "Main Categories",
            "tags": "Tags",
            "meta_data": "Metadata",
            "attributes": "Attributes",

        }
        data_container.children().remove();

        let _select = $('<select />', {name: "wc_cat_dropdown", id: "wc_cat_dropdown"});

        // Added select option
        $('<option />', {
            value: "", text: "-- select an option --", hidden: "hidden", disabled: "disabled", selected: "selected"
        }).appendTo(_select);
        let map_category = settings.map_category
        for (let category in categories) {
            let option_data = {
                value: category,
                text: categories[category]
            }
            if (category === map_category) {
                option_data["selected"] = "selected";
            }
            $('<option />', option_data).appendTo(_select);
        }
        _select.change(set_bol_wc_mapping_info);
        _select.appendTo(data_container);
    }

    function set_bol_wc_mapping_info(e) {
        e.preventDefault();
        let table_data = get_table_data();
        let data = {
            'woocommerce_category_field': $("#wc_cat_dropdown").val(),
            'map_data': table_data,
            'action': 'set_shop_2_api_bol_wc_offer_mapping',
            'nonce': settings.nonce,
        };

        $.post(settings.ajaxurl, data, function (response) {
            if (response.success === true) {
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200 || response.data.response.code === 201) {
                        location.reload();
                        return true;
                    }
                    alert("There was an error saving the data:" + response.data);
                }
            } else {
                alert("There was an error saving the data:" + response.data);
            }
        });
    }


    // let default_mapping = {
    //     "ean": {
    //         "type": "Product",
    //         "value": "sku"
    //     },
    //     // "Name": {
    //     //     "type": "Product",
    //     //     "value": "name"
    //     // },
    //     // "Description": {
    //     //     "type": "Product",
    //     //     "value": "description"
    //     // },
    //     "stock.amount": {
    //         "type": "OwnValue",
    //         "value": "0"
    //     },
    //     "condition.name": {
    //         "type": "OwnValue",
    //         "value": "NEW"
    //     },
    //     "fulfilment.method": {
    //         "type": "OwnValue",
    //         "value": "FBR"
    //     },
    //     "Internal Reference": {
    //         "type": "Product",
    //         "value": "sku"
    //     },
    //     "unknownProductTitle": {
    //         "type": "Product",
    //         "value": "name"
    //     },
    //     "fulfilment.deliveryCode": {
    //         "type": "OwnValue",
    //         "value": "1-2d"
    //     },
    //     "stock.managedByRetailer": {
    //         "type": "OwnValue",
    //         "value": "true"
    //     },
    //     "pricing.bundlePrices.quantity": {
    //         "type": "OwnValue",
    //         "value": "1"
    //     },
    //     "pricing.bundlePrices.unitPrice": {
    //         "type": "Product",
    //         "value": "price"
    //     }
    // }
})(jQuery);

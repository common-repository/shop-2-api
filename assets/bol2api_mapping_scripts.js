( function( $ ) {
    let woocommerce_metadata_option_data = [];
    let woocommerce_attribute_option_data = [];

    $( document ).ready( function() {
        $('#all-tabs').hide();
        // // Attached change event to top dropdown
        $(document).on( 'change', '#wc_cat_dropdown', handle_cat_change);

        // // Attaching the save function to the button.
        $('.shop-2-api-connect').on( 'click', '.shop-2-api-connect-save', submit_wc_bol_data);
        $('.shop-2-api-connect').on( 'click', '.shop-2-api-connect-save-sync', submit_wc_bol_data);

        // Attach change event to section-to-map Dropdown
        $(document).on( 'change', 'select[name="section-to-map"]', handle_sec_to_map_change);
        // tabs
        $(document).on('click', '.nav-tab-wrapper a', handle_tab_click);


        get_woocommerce_attribute_option_data();

    });

    function handle_tab_click(event) {
        event.preventDefault();
        $("#offer-table").hide();
        $("#product-table").hide();
        $(".nav-tab").removeClass("nav-tab-active");

        let clicked_tab = $(this).attr('href');
        $(clicked_tab).show(800);
        $(this).addClass('nav-tab-active');
        
    }

    function handle_sec_to_map_change() {
        let current_selection = $(this).val();
        // map container before populating new one
        let data_container = $(this).parents('tr').find('span[name="wc-data-container"]');
        data_container.children().remove();
        let generated_field = null;

        switch (current_selection) {
            case 'OwnValue':
                generated_field = set_wc_custom_field($(this).data());
                break;
            case 'Attribute':
                generated_field = set_wc_attribute_field($(this).data());
                break;
            case 'MetaData':
                generated_field = set_wc_metadata_field($(this).data());
                break;
            case 'Product':
                generated_field = set_wc_product_dropdown($(this).data());
                break;
        }
        if (generated_field!==null) {
            generated_field.appendTo(data_container); 
        }
        sortDropDownListByText();
    }

    // Get the Own Value Field value and show textbox/drowdown
    function set_wc_custom_field(data) {
        if (typeof(data['dropdown_value'] !== 'undefined') && data['dropdown_value'] !== "") {
            let enum_values = data['dropdown_value'].split(',');
            if (enum_values.length > 0) {
                let _select = $('<select />', {
                    "data-field_name": data['field_name'],
                    "data-field_type": 'OwnValue' 
                });
                $.each(enum_values, function(key, val) {
                    let dropdown_values = {
                        'value':val, 
                        'text':val
                    }
                    if (data['saved_value'] == val) {
                        dropdown_values['selected'] = 'selected'
                    }
                    
                    $('<option />', dropdown_values).appendTo(_select);                    
                }) 
                return _select;
            }
        }

        if ( data['field_name'] === 'pricing.bundlePrices.quantity') {
            return $('<input />', {
                "value": data['saved_value'],
                "data-field_name": data['field_name'],
                "data-field_type": 'OwnValue',
                "readonly": "readonly"
            })
        }
        return $('<input />', {
            "value": data['saved_value'],
            "data-field_name": data['field_name'],
            "data-field_type": 'OwnValue'
        })
    }

    // Populate attribute dropdown
    function set_wc_attribute_field(data) {
        let _select = $('<select />', {
            "data-field_name": data['field_name'],
            "data-field_type": 'Attribute' 
        });

        $.each(woocommerce_attribute_option_data, function(key, val) {
            let dropdown_values = {
                'value':val['bol_category'], 
                'text':val['woocommerce_field']
            }
            if (data['saved_value'] == val['bol_category']) {
                dropdown_values['selected'] = 'selected'
            }
            
            $('<option />', dropdown_values).appendTo(_select);
        })

        return _select;
    }

    // Populate Metadata Dropdown
    function set_wc_metadata_field(data) {
        let _select = $('<select />', {
            "data-field_name": data['field_name'],
            "data-field_type": 'MetaData' 
        });

        $.each(woocommerce_metadata_option_data, function(key, val) {
            let dropdown_values = {
                'value':val['bol_category'], 
                'text':val['woocommerce_field']
            }
            if (data['saved_value'] == val['bol_category']) {
                dropdown_values['selected'] = 'selected'
            }
            
            $('<option />', dropdown_values).appendTo(_select);
        })

        return _select;
    }

    function set_wc_product_dropdown(data) {
        let woocommerce_option_properties = settings.wc_field_options.success.schema.properties;

        let _select = $('<select />', {
            "data-field_name": data['field_name'],
            "data-field_type": 'Product' 
        });

        for (const item in woocommerce_option_properties) {
            obj = woocommerce_option_properties[item];

            if (obj.type === "array") {
                if (item === "images") {
                    for (const sub_item in obj.items.properties) {
                        if (sub_item !== 'src') {
                            continue;
                        }

                        let value_text = item + '|' + sub_item;
                        let dropdown_values = {"value": value_text, "text": value_text}

                        if (data['saved_value'] === value_text) {
                            dropdown_values['selected'] = 'selected';
                        }

                        $('<option />', dropdown_values).appendTo(_select);

                        for (let i = 1; i <= 20; i++) {
                            let value_text = item + '|' + sub_item + '|' + String(i);
                            let dropdown_values = {"value": value_text, "text": value_text}

                            if (data['saved_value'] === value_text) {
                                dropdown_values['selected'] = 'selected';
                            }

                            $('<option />', dropdown_values).appendTo(_select);
                        }
                    }
                }
                continue;
            }
            if (obj.type=='object') {
                for (const sub_item in obj.properties) {
                    let value_text = item + '|' + sub_item;
                    let dropdown_values = {"value": value_text, "text": value_text}

                    if (data['saved_value'] === value_text) {
                        dropdown_values['selected'] = 'selected';
                    }
                    
                    $('<option />', dropdown_values).appendTo(_select);
                }
            } else {
                let dropdown_values = {"value": item, "text": item}
                if (data['saved_value'] === item) {
                    dropdown_values['selected'] = 'selected';
                }

                $('<option />', dropdown_values).appendTo(_select);
            }
        }
        
        return _select;
    }

    function get_section_to_map_dropdown(name, mapped_value, dropdown_values) {
        let _select = $('<select />', {
            "name": "section-to-map",
            "data-field_name": name, 
            "data-saved_value": (typeof(mapped_value) !== "undefined") ? mapped_value['value'] : '',
            "data-dropdown_value": (typeof(dropdown_values) !== "undefined") ? dropdown_values : ''
        });
        $('<option />', { value:'NotMapped', text:'Field Excluded'}).appendTo(_select);
        $('<option />', { value:'OwnValue', text:'My Own Value'}).appendTo(_select);
        $('<option />', { value:'Attribute', text:'WooCommerce Attribute'}).appendTo(_select);
        $('<option />', { value:'MetaData', text:'WooCommerce Metadata Field'}).appendTo(_select);
        $('<option />', { value:'Product', text:'WooCommerce Product Field'}).appendTo(_select);

        // Set Selected value
        if (typeof(mapped_value) !== 'undefined') {
            _select.val(mapped_value['type'])
        }

        // Return Select Object
        return _select;
    }

    function add_product_mapping_row(product_dict, is_measurement = false, product_response_data = {}) {
        let description = product_dict.name;
        let id = product_dict.id;
        let required_val = product_dict.required;
        let mapped_value = product_response_data[product_dict.id];
        let tooltip = product_dict.tooltip;
        let dropdown_values = product_dict.ENUM;
        let product_type = product_dict.type;

        let dropdown_element = $('<td class="product-icon"></td>')
        let measurement_image_element = $('<td class="product-icon"></td>');

        let table_row = $('<tr/>');
    
        let required_html = "";
        if (required_val) required_html= "required";
        if (product_type !== 'LABEL' && is_measurement === false) {
            measurement_image_element.html('<i class = "material-icons straighten" style="font-size: 20px; cursor:pointer;" title="Add Measurement unit (cm, mm)">straighten</i>');
        }

        if (product_type === 'LABEL') {
            measurement_image_element.html('<i class = "material-icons mapping-icons" style="font-size: 20px;" title="This is a image URL">add_a_photo</i>');
        }

        if (dropdown_values) {
            dropdown_element.html('<i class = "material-icons mapping-icons" style="font-size: 20px;" title="There is a dropdown for this item if you choose `MY OWN VALUE`.">fact_check</i>');
        }

        // Populate Section Dropdown
        let section_to_map_dropdown = get_section_to_map_dropdown(id, mapped_value, dropdown_values);

        table_row.append(dropdown_element);
        table_row.append(measurement_image_element);
        table_row.append($('<td class="product-icon" title="' + tooltip + '"><span class="dashicons dashicons-info-outline"></span></td>'));
        table_row.append($('<td class = "' + required_html + '">' + description + '</td>'));
        table_row.append($('<td class = "offer-table-bol-data"></td>'));
        table_row.append($('<td>').append(section_to_map_dropdown));
        table_row.append($('<td><span class="dashicons dashicons-arrow-right-alt"></span></td>'));
        table_row.append($('<td />').append($('<span />', {name: 'wc-data-container'})));

        
        $('#product-data-body').append(table_row);

        if (typeof(mapped_value) === "undefined" && is_measurement) {
            $(table_row).hide();
        }

        // Set the retrieved saved values.
        section_to_map_dropdown.change();
    }

    function add_offer_mapping_row(product_dict, product_response_data) {
        let description = product_dict.name;
        let id = product_dict.id;
        let required_val = product_dict.required;
        let mapped_value = product_response_data[product_dict.id];
        let tooltip = product_dict.tooltip;
        let dropdown_values = product_dict.ENUM;

        let dropdown_element = $('<td class="product-icon"></td>')
        if (dropdown_values) {
            dropdown_element.html('<i class = "material-icons mapping-icons" style="font-size: 20px;" title="There is a dropdown for this item if you choose `MY OWN VALUE`.">fact_check</i>');
        }

        let table_row = $('<tr/>');
        let required_html = "";
        if (required_val) required_html= "required";

        
        // Populate Section Dropdown
        let section_to_map_dropdown = get_section_to_map_dropdown(id, mapped_value, dropdown_values);

        table_row.append(dropdown_element);
        table_row.append($('<td title="' + tooltip + '" class="product-icon"><span class="dashicons dashicons-info-outline" style="padding-top:10px;"></span></td>'));
        table_row.append($('<td class = "' + required_html + '">' + description + '</td>'));
        table_row.append($('<td class = "offer-table-bol-data"></td>'));
        table_row.append($('<td>').append(section_to_map_dropdown));
        table_row.append($('<td><span class="dashicons dashicons-arrow-right-alt"></span></td>'));
        table_row.append($('<td />').append($('<span />', {name: 'wc-data-container'})));

        
        $('#offer-data-body').append(table_row);

        // Set the retrieved saved values.
        section_to_map_dropdown.change();
    }

    function convert_mapping_to_json(){
        let return_data = {};
        let container_data = $('#offer-data-body').find('[name="wc-data-container"]');

        // Go through all the form data
        $.each(container_data, function(input_name, container_element) {
            let input_element = $(container_element.firstChild);

            if (typeof(input_element.val()) === "undefined") return;

            let input_data = input_element.data();
            return_data[input_data['field_name']] = {
                value: input_element.val(),
                type: input_data['field_type']
            }            
        })

        container_data = $('#product-data-body').find('[name="wc-data-container"]');

        // Go through all the form data
        $.each(container_data, function(input_name, container_element) {
            let input_element = $(container_element.firstChild);

            if (typeof(input_element.val()) === "undefined") return;

            let input_data = input_element.data();
            return_data[input_data['field_name']] = {
                value: input_element.val(),
                type: input_data['field_type']
            }            
        })

        return return_data;
    }
    
    function submit_wc_bol_data() {
        let converted_data = convert_mapping_to_json();

        let data = {
            'action' : 'set_shop_2_api_wc_to_bol_submit',
            'nonce'  : settings.nonce,
            'map_data': converted_data,
            'bol_category_field': $("#wc_cat_dropdown").val()
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success == true ) {
                if (response.data) {
                    $('#common-success').text('Save Completed')
                    $('#common-success').show().fadeOut(5000);
                    window.scrollTo(0, 0);
                } else {
                    alert("There was an error saving the data:" + response.data);
                }
            }
        });
    }

    // Populate Dropdown and check if it must be selected.
    function set_wc_category_field_dropdown() {
        let items = settings.map_data;
        let dropdown_items = [];

        let data_container = $('span[name="wc-data-container"]');
        data_container.children().remove();

        let _select = $('<select />', {name:"wc_cat_dropdown", id:"wc_cat_dropdown", class:"bol-cat-dropdown"});
        items.forEach(
            function(currentValue) {
                let bol_category_code = currentValue.bol_category_code;
                let already_added = dropdown_items.includes(bol_category_code);
                if (already_added === false) {
                    let select_value = {
                        value: bol_category_code, text: currentValue.bol_category_name
                    }
                    $('<option />', select_value).appendTo(_select);
                    dropdown_items.push(bol_category_code)
                }
            }
        )
        _select.appendTo(data_container);
        _select.trigger('change');
    }

    // Populate the data of the table
    function populate_table(product_response_data)
    {
        // Add offer mapping rows
        $.each(settings.offer_data, function(key, value){
            add_offer_mapping_row(value, product_response_data.map_data);
        })

        // Add Product mapping rows
        let enriched_data = product_response_data.woocommerce_enriched_model_data;
        $.each(enriched_data, function(index, value){
            if (value['id'] === 'EAN') return;
            add_product_mapping_row(value, false, product_response_data.map_data);

            if (value.type !== 'LABEL') {
                // Unit Product Row
                let unit_value = Object.assign({}, value);
                unit_value.name = value.name + ' (Unit)';
                unit_value.id = value.id + '_unit';
                add_product_mapping_row(unit_value,true, product_response_data.map_data);
            }
        });
        $('.straighten').on('click', function(){
            $(this).parent().parent().next("tr").toggle();
        });
    }

    // If the top category change.
    function handle_cat_change()
    {
        $('#product-data-body').children().remove();
        $('#offer-data-body').children().remove();

        get_bol_category_info_by_value($(this).val());
        $('#all-tabs').show();
        $('.nav-tab-active').click();
    }

    // Populate the table
    function get_bol_category_info_by_value(cat_code)
    {
        let items = settings.map_data;
        for (let i in items) {
            let item = items[i]
            if (item.bol_category_code === cat_code) {
                populate_table(item);
                return;
            }
        }
    }

    // Populate Metadata Dropdown Value
    function get_woocommerce_metadata_option_data()
    {
        let data = {
            'action': 'get_woocommerce_values',
            'value' : 'meta_data'
        };

        $.post(settings.ajaxurl, data, function(response) {
            if (response.success === true) {
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200) {
                        let response_data = JSON.parse(response.data.body);
                        woocommerce_metadata_option_data = Object.values(response_data.success);
                        set_wc_category_field_dropdown()
                    } else {
                        alert("There was an error retrieving the data:" + response);
                    }
                }
            }
        });
    }

    // Populate Attribute Dropdown Value
    function get_woocommerce_attribute_option_data()
    {
        let data = {
            'action': 'get_woocommerce_values',
            'value' : 'attributes'
        };

        $.post(settings.ajaxurl, data, function(response) {
            debugger;
            if (response.success === true) {
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200) {
                        let response_data = JSON.parse(response.data.body);
                        woocommerce_attribute_option_data = Object.values(response_data.success);
                        get_woocommerce_metadata_option_data();
                    } else {
                        alert("There was an error retrieving the data:" + response);
                    }
                }
            }
        });
    }

    function sortDropDownListByText() {
        // Loop for each select element on the page.
        $("select").each(function() {
    
            // Keep track of the selected option.
            let selectedValue = $(this).val();
    
            // Sort all the options by text. I could easily sort these by val.
            $(this).html($("option", $(this)).sort(function(a, b) {
                return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
            }));
    
            // Select one option.
            $(this).val(selectedValue);
        });
    }
})( jQuery );

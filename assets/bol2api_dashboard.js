(function ($) {
    $(document).ready(function () {
        // Default Hidden
        $('#register-free-account-modal').hide();

        // Check connection, if connection in error state show connection modal.
        check_connection_status();

        // WC Connection Reroute URL
        woocommerce_reroute();

        // Bol Connection Modal
        $("#bol-connect-modal").hide();
        bol_connect_modal();

        //Populate information if there is any.
        populate_api_information_bol();

        $(document).on('click', '#register-free-account', function (event) {
            event.preventDefault();
            let data = {
                'action': 'register_shop_2_api_free_account',
                'nonce': settings.nonce,
                'email': $('#shop2api-email').val()
            };

            $.post(settings.ajaxurl, data, function (response) {
                if (response.success == true) {
                    if (response.data && response.data.response) {
                        if (response.data.response.code === 200) {
                            location.reload();
                        } else {
                            $("#registration-error").text("There was an issue updating the data.");
                            console.log("There was an error updating the data:" + response.data);
                        }
                    }
                }
            });
        });

        //Show Connect Div
        $(document).on('click', '#connection-completed', function () {
            $('#get-started-card').show(1000);
        })

        // Order Checks
        populate_api_information_orders()
    })

    function check_connection_status() {
        // This will check if the connection is good on Shop 2 API 
        // If the connection is good, it will do Nothing else show connect popup.
        if (!settings.connected) {
            $('#register-free-account-modal').modal({
                escapeClose: false,
                clickClose: false,
                showClose: false
            });
            $("#shop2api-email").val(settings.shop2api_email);
            add_connection_error();
        }
        // IF WC Connected and Boll connected then show STEP DATA.
        if (!settings.wc_connected || !settings.bol_connected) {
            $('#get-started-card-completed').hide();
        }

        if (settings.wc_connected && settings.bol_connected) {
            $('#get-started-card').hide();
        } else {
            //dashicons-yes-alt item-completed
            let $item = $('#connection-completed h1 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
            $item.addClass('dashicons-dismiss').addClass('item-not-completed');
        }

        let wc_categories_mapped = false;
        let wc_fields_mapped = false;
        if (settings.mapped_rows.length > 0) {
            wc_categories_mapped = true;
            wc_fields_mapped = true;
        }

        if (!wc_categories_mapped) {
            let $item = $('#map-wc-to-bol h3 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
            $item.addClass('dashicons-dismiss').addClass('item-not-completed');
        }

        if (!wc_fields_mapped) {
            let $item = $('#map-wc-fields-to-bol h3 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
            $item.addClass('dashicons-dismiss').addClass('item-not-completed');
            $('#force-sync-card').hide();
        } else {
            $('#force-sync-card').show();
        }
        get_sync_summary();
    }

    function add_connection_error() {
        $('#setting-error-shop2api_token_error').hide();
        let error_text = $('#setting-error-shop2api_token_error').text()
        if (error_text !== "") {
            $('#token-error').text(error_text);
            $('#token-error').show();
        }
    }

    function woocommerce_reroute() {
        // If WC is connected change to green class
        if (settings.wc_connected) {
            let wc_connection = $("#connect-to-wc")
            wc_connection.find(".connect-error").removeClass("connect-error").addClass("connect-success");
            wc_connection.find(".dashicons-admin-plugins").removeClass("dashicons-admin-plugins").addClass("dashicons-plugins-checked");
        }

        $('#connect-to-wc').on('click', function () {
            window.location.href = settings.wc_auth_url;
        });
    }

    //Open boll modal id bol is connected.
    function bol_connect_modal() {
        if (settings.bol_connected) {
            let bol_connection = $("#connect-to-bol")
            bol_connection.find(".connect-error").removeClass("connect-error").addClass("connect-success");
            bol_connection.find(".dashicons-admin-plugins").removeClass("dashicons-admin-plugins").addClass("dashicons-plugins-checked");
        }
        ;
        $('#connect-to-bol').on('click', function () {
            $("#bol-connect-modal").modal();
        });

        // Attach to the save button and call save functions.
        $('#save-bol-data').on('click', save_bol_data);
    }

    // This will populate the text boxes (BOL)
    function populate_api_information_bol() {
        if (settings.bol_info && settings.bol_info.length > 0) {
            $("#bol-client-id").val(settings.bol_info[0].client_id);
            $("#bol-client-secret").val(settings.bol_info[0].client_secret);
        }
    }

    // This will check/uncheck order boxes
    function populate_api_information_orders() {
        if (settings.bol_info && settings.bol_info.length > 0) {
            if (settings.bol_info[0].order_active === "False") {
                let $item = $('#shop2api-map-order-sync h3 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
                $item.addClass('dashicons-dismiss').addClass('item-not-completed');
            }
            if (settings.bol_info[0].sync_stock_active === "False") {
                let $item = $('#shop2api-map-order-sync-stock h3 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
                $item.addClass('dashicons-dismiss').addClass('item-not-completed');
            }
            if (settings.bol_info[0].combi_deal_active === "False") {
                let $item = $('#shop2api-map-order-sync-combi-deal h3 span').first().removeClass('dashicons-yes-alt').removeClass('item-completed');
                $item.addClass('dashicons-dismiss').addClass('item-not-completed');
            }
        }
    }

    // This will save the bol data and trigger the save of WC data.
    function save_bol_data() {
        let data = {
            'action': 'set_shop_2_api_information_bol',
            'nonce': settings.nonce,
            'client_id': $('#bol-client-id').val(),
            'client_secret': $('#bol-client-secret').val()
        };

        $.post(settings.ajaxurl, data, handle_save_bol_response, "json");
    }

    function handle_save_bol_response(response_data, status) {
        if (response_data.success == false || status == false) {
            $("#bol-connection-error").text("There was an issue updating the data.");
        } else {
            location.reload();
        }
    }

    function get_sync_summary() {
        let data = {
            'action': 'get_wc_summary_report_offer'
        };

        $.get(settings.ajaxurl, data, sync_summary_offer_response, "json");

        data = {
            'action': 'get_wc_summary_report_product'
        };

        $.get(settings.ajaxurl, data, sync_summary_product_response, "json");

        data = {
            'action': 'get_wc_summary_report_order'
        };

        $.get(settings.ajaxurl, data, sync_summary_order_response, "json");

    }

    function sync_summary_product_response(data) {
        if (data.success) {
            $('#sync-summary-card').show();
            let json_data = JSON.parse(data.data.body);
            for (let i in json_data) {
                if (typeof (json_data[i].counter) === 'undefined') continue;
                if (json_data[i].status == null) {
                    json_data[i].status = 'N/A'
                }

                $('#sync-summary-product-tbody').append(
                    '<tr><td>' + json_data[i].status + '</td><td>' + json_data[i].counter + '</td></tr>'
                );
            }
        }
    }

    function sync_summary_offer_response(data) {
        if (data.success) {
            $('#sync-summary-card').show();
            let json_data = JSON.parse(data.data.body);
            for (let i in json_data) {
                if (typeof (json_data[i].counter) === 'undefined') continue;
                if (json_data[i].status == null) {
                    json_data[i].status = 'N/A'
                }

                $('#sync-summary-offer-tbody').append(
                    '<tr><td>' + json_data[i].status + '</td><td>' + json_data[i].counter + '</td></tr>'
                );
            }
        }
    }

    function sync_summary_order_response(data) {
        if (data.success) {
            $('#sync-summary-card').show();
            let json_data = JSON.parse(data.data.body);
            for (let i in json_data) {
                if (typeof (json_data[i].counter) === 'undefined') continue;
                if (json_data[i].status == null) {
                    json_data[i].status = 'N/A'
                }

                if (json_data[i].status === 'SYNCED') {
                    $('#sync-summary-order-tbody').append(
                        '<tr class="order-summary-success-status"><td>' + json_data[i].status + '</td><td>' + json_data[i].counter + '</td><td></td></tr>'
                    );
                } else {
                    let url = window.location.pathname + '?page=shop2api_wc_to_bol_reports_order_page&s=ERROR';

                    $('#sync-summary-order-tbody').append(
                        '<tr class="order-summary-error-status"><td>' + json_data[i].status + '</td><td>' + json_data[i].counter + '</td><td title="Click here to go to the report and you can retry any orders or remove them to not be synced."><a href="' + url + '" class="order-summary-link"><span class="dashicons  dashicons-image-rotate" id="force-sync"></span></a></td></tr>'
                    );
                }
            }
        }
    }

    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(draw_charts);
    function draw_charts() {
        data = {
            'action': 'get_wc_detail_report_order'
        };

        $.get(settings.ajaxurl, data, sync_detail_order_response, "json");
    }

    function sync_detail_order_response(data) {
        if (data.success) {
            $('#sync-chart-card').show();
            let json_data = JSON.parse(data.data.body);

            let orders = json_data.results;
            let order_chart_data = process_order_chart_data(orders)

            var data = google.visualization.arrayToDataTable(order_chart_data);

            let options = {
                width: 450,
                bar: { groupWidth: "90%" },
                animation: {duration: 666, easing: 'inAndOut', startup: true},
            };

            let chart = new google.charts.Bar(document.getElementById('chart_div'));

            chart.draw(data,  google.charts.Bar.convertOptions(options));

        }
    }

    function format_date(dd, mm, yyyy) {
        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        return  dd + '/' + mm + '/' + yyyy;

    }

    function process_order_chart_data(orders) {
        let order_data = {};

        for (let i in orders) {
            let order = orders[i]
            if (order.date_modified === undefined) {
                continue;
            }

            let order_date = new Date(order.date_modified)
            let formatted_date = format_date(
                order_date.getDate(),  order_date.getMonth() + 1, order_date.getFullYear()
            )

            if (formatted_date in order_data) {
                if (order.status === 'SYNCED') {
                    order_data[formatted_date][0] += 1;
                } else {
                    order_data[formatted_date][1] += 1;
                }
            } else {
                if (order.status === 'SYNCED') {
                    order_data[formatted_date] = [1, 0];
                } else {
                    order_data[formatted_date] = [0, 1];
                }
            }
        }

        let flat_arr = [['Date', 'Synced', 'Error']];
        for (let i in order_data){
            flat_arr.push([].concat([i],order_data[i]))
        }

        return flat_arr;
    }

})(jQuery);

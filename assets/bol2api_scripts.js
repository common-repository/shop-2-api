( function( $ ) {
    $( document ).ready( function() {
        // Populate API Information
        populate_api_information_bol();

        // Attach to the save button and call save functions.
        $( '.shop-2-api-connect' ).on( 'click', '.shop-2-api-connect-save', function( event ) {
            save_bol_data();
        });
    });

    // This will save the bol data and trigger the save of WC data.
    function save_bol_data() {
        let data = {
            'action' : 'set_shop_2_api_information_bol',
            'nonce'  : settings.nonce,
            'client_id': $('#bol-client-id').val(),
            'client_secret': $('#bol-client-secret').val()
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success == true ) {
                    save_wc_data();
            } else {
                alert("There was an error retrieving the data:" + response.data);
            }
            
        });
    }

    // This will save the wc data
    function save_wc_data() {
        let data = {
            'action' : 'set_shop_2_api_information_wc',
            'nonce'  : settings.nonce,
            'client_key': $('#wc-client-key').val(),
            'client_secret': $('#wc-client-secret').val()
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success == true ) {
                console.log('Save Success') 
            } else {
                alert("There was an error saving the data:" + response.data);
            }
        });
    }


    // This will populate the textboxes (BOL)
    function populate_api_information_bol() {
        let data = {
            'action': 'get_shop_2_api_information_bol',
        };

        $.post(settings.ajaxurl, data, function(response) {
            if (response.success == true) {
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200) {
                        response_data = JSON.parse(response.data.body);
                        if (response_data && response_data.length > 0) {
                            $("#bol-client-id").val(response_data[0].client_id);                      
                            $("#bol-client-secret").val(response_data[0].client_secret);
                        }
                        
                    } else {
                        alert("There was an error retrieving the data:" + response.data);
                    }
                }
            }
            populate_api_information_wc()  
        });
    }

    // This will populate the textboxes (WC)
    function populate_api_information_wc() {
        let data = {
            'action': 'get_shop_2_api_information_wc',
        };

        $.post(settings.ajaxurl, data, function(response) {
            if (response.success == true) {
                if (response.data && response.data.response) {
                    if (response.data.response.code === 200) {
                        response_data = JSON.parse(response.data.body);
                        if (response_data && response_data.length > 0) {
                            $("#wc-client-key").val(response_data[0].consumer_key);                      
                            $("#wc-client-secret").val(response_data[0].consumer_secret); 
                        } 
                    } else {
                        alert("There was an error retrieving the data:" + response.data);
                    }
                }
            }
        });
    }



})( jQuery );

( function( $ ) {
    $( document ).ready( function() {
        // Attach to the save button and call save functions.
        $(document).on( 'click', '#shop2api-remove-ean', function( event ) {
            event.preventDefault();
            remove_ean_from_bol($(this).data('wc_id'));
        });

        $('#the-list').on('click', '.editinline', function(){

            /**
             * Extract metadata and put it as the value for the custom field form
             */
            inlineEditPost.revert();

            let post_id = $(this).closest('tr').attr('id').replace("post-", "");

            let $cfd_inline_data = jQuery('#shop2api_sync_to_bol_saved_inline_' + post_id)

            $('#shop2api_sync_to_bol', '.inline-edit-row').prop(
                "checked", $cfd_inline_data.find("#shop2api_sync_to_bol_saved_val").text() === 'yes'
            );
        });

        setTimeout(function() {
            $('.woocommerce-message').fadeOut('slow');
        }, 6000); // <-- time in mseconds
    });

    // This will save the bol data and trigger the save of WC data.
    function remove_ean_from_bol(woocommerce_id) {
        let data = {
            'action' : 'set_shop_2_api_remove_ean_from_bol',
            'nonce'  : settings.nonce,
            'woocommerce_id': woocommerce_id
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success === true ) {
                alert("Offer Removal is queued.");
            } else {
                alert("There was an error saving the data.");
            }
            
        });
    }
})( jQuery );

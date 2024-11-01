( function( $ ) {
    $( document ).ready( function() {
        $( '#sync-wc-product' ).on( 'click', sync_woocommerce_to_bol);
        $( '#refresh-dropdowns' ).on( 'click', refresh_dropdown);
        $( '.shop-2-api-connect-save-sync' ).on( 'click', sync_woocommerce_to_bol);
        $('.content').show("slow");
    })

    Array.prototype.remove = function() {
        let what, a = arguments, L = a.length, ax;
        while (L && this.length) {
            what = a[--L];
            while ((ax = this.indexOf(what)) !== -1) {
                this.splice(ax, 1);
            }
        }
        return this;
    };
    
    function sync_woocommerce_to_bol() {
        let data = {
            'action' : 'sync_woocommerce_to_bol',
            'nonce'  : settings.nonce
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success === true ) {
                alert('Sync to WooCommerce started, see reports for progress.')
            } else {
                alert("There was an error retrieving the data:" + response.data);
            }
        });
    }

    function refresh_dropdown() {
        let data = {
            'action' : 'refresh_wc_dropdowns',
            'nonce'  : settings.nonce
        };

        $.post( settings.ajaxurl, data, function( response ) {
            if ( response.success === true ) {
                alert('Dropdowns should be refreshed.')
            } else {
                alert("There was an error retrieving the data:" + response.data);
            }
        });
    }
})( jQuery );

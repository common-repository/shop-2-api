( function( $ ) {
    $( document ).ready( function() { 
        $('#openModal').modal();
        $('.show-more').on( 'click',  show_modal_for_data);
        $.modal.close();

        $(document).on('click', '#start_stop_sync',start_stop_sync);
    });

    function start_stop_sync() {
        let data = {
            'action' : 'start_sync_for_wc_id',
            'nonce'  : settings.nonce,
            'wc_id': $(this).data('wc_id')
        };

        $.post( settings.ajaxurl, data, handle_save_start_stop_sync, "json");
    }

    function handle_save_start_stop_sync(data) {
        if (data.success) {
            location.reload();
        } else {
            alert("There was an issue updating the data.")
        }
    }

    function show_modal_for_data() {
        let req_data_table = $("#table-req_data");
        req_data_table.empty();

        let req_info = $(this).data('req_info').replace(/\+/g, '%20');
        req_info = JSON.parse(decodeURIComponent(req_info));

        let new_div1 = document.createElement( "pre" );
        new_div1.textContent = JSON.stringify(req_info, null, 4);
        req_data_table.append(new_div1);

        let res_data_table = $("#table-res_data");
        res_data_table.empty();
        let res_info = $(this).data('res_info').replace(/\+/g, '%20');
        res_info = JSON.parse(decodeURIComponent(res_info));

        let new2_div = document.createElement( "pre" );
        new2_div.textContent = JSON.stringify(res_info, null, 4);
        res_data_table.append(new2_div);

        $('#openModal').modal();
    }

})( jQuery );

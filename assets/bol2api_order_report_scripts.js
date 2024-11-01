( function( $ ) {
    $( document ).ready( function() { 
        $('#openModal').modal();
        $('.show-more').on( 'click',  show_modal_for_data);
        $('.order-report-icon-retry').on( 'click',  retry_order);
        $('.order-report-icon-stop').on( 'click',  stop_order);
        $.modal.close();

    });

    function show_modal_for_data() {
        let res_data_table = $("#table-res_data");
        res_data_table.empty();
        let res_info = $(this).data('res_info').replace(/\+/g, '%20');
        res_info = JSON.parse(decodeURIComponent(res_info));

        let new2_div = document.createElement( "pre" );
        new2_div.style.cssText = 'width:100%;'
        new2_div.textContent = JSON.stringify(res_info, null, 4);
        res_data_table.append(new2_div);

        $('#openModal').modal();
    };

    function retry_order() {
        data = {
            'action': 'set_wc_order_status',
            'status': 'PENDING',
            'reference': this.dataset.reference ,
        };

        $.post(settings.ajaxurl, data, status_change_response, "json");
    }

    function stop_order() {
        data = {
            'action': 'set_wc_order_status',
            'status': 'STOPPED',
            'reference': this.dataset.reference ,
        };

        $.post(settings.ajaxurl, data, status_change_response, "json");
    }

    function status_change_response(data) {
        if (data.success) {
            location.reload();
        } else {
            alert('There was an error updating the status.')
        }
    }

})( jQuery );

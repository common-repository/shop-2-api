<?php

/**
 *
 * @package Shop2API
 *
 * This is the Reporting page for admin.php?page=shop2api_wc_to_bol_reports
 * Lists all the syncing transactions.
 *
 */

require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';

class Shop2Api_Bol2WcStatusReport extends WP_List_Table
{
    protected array $report_data;

    function get_columns(): array
    {
        return array(
            'ean_number' => __('Ean Number'),
            'product_name' => __('Product Name'),
            'offer_last_sync_date' => __('Offer Sync Date'),
            'offer_last_sync_status' => __('Offer Sync Status'),
            'product_last_sync_date' => __('Product Sync Date'),
            'product_last_sync_status' => __('Product Sync Status'),
            'koopblok_status' => __('Koopblok Status'),
            'sync_to_bol' => __('Actions'),
        );
    }

    function get_data()
    {
        $search = '';
        if (!empty($_GET['s'])) {
            $search = sanitize_title_for_query(esc_attr($_GET['s']));
        }

        $sort = $this->get_sorting_str();
        $current_page = $this->get_pagenum();

        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();

        $api_response = $shop_2_api_connection->get_wc_product_data_report($search, $sort, $current_page);
        $api_response_data = json_decode(wp_remote_retrieve_body($api_response), true);
        $this->report_data = $api_response_data  ? $api_response_data['results'] : [];

        $this->set_pagination_args(
            array(
                'total_items' =>  $api_response_data  ? $api_response_data['count'] : 0,
                'per_page' => 10
            )
        );
    }

    function prepare_items()
    {
        $this->get_data();

        // Set data
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);


        $this->items = $this->report_data;
        $this->echo_modal();
    }

    function column_default($item, $column_name)
    {
        $allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

        switch ($column_name) {
            case 'date_created':
                $run_time = strtotime($item['date_created']);
                return wp_kses(date("Y-m-d", $run_time) . '</br>' . date("H:i:s", $run_time), $allowed_html);
            case 'koopblok_status':
            case 'product_last_sync_status':
            case 'offer_last_sync_status':
                if ($item[$column_name] == 'ERROR' || $item[$column_name] == 'FAIL') {
                    $display_data = '<span class="report-error-status">'.$item[$column_name].'</span>';
                } elseif (strpos($item[$column_name],'PENDING') !== false) {
                    $display_data = '<span class="report-warning-status">'.$item[$column_name].'</span>';
                } elseif (!$item[$column_name]) {
                    $display_data = '<span class="report-warning-status">N/A</span>';
                } else {
                    $display_data =  '<span class="report-success-status">'.$item[$column_name].'</span>';
                }
                return wp_kses($display_data, $allowed_html);
            case 'sync_to_bol':
                $sync_to_bol = get_post_meta($item['woocommerce_id'] , 'shop2api_sync_to_bol', true );
                if ($sync_to_bol == 'yes') {
                    $display_data = '<span id="start_stop_sync" class="material-icons stop-icon rpt-icons" title="This will pause the syncing to bol.com." data-wc_id = "'.$item['woocommerce_id'].'">stop</span>';
                } else {
                    $display_data = '<span id="start_stop_sync" class="dashicons dashicons-controls-play rpt-icons" title="This will enable syncing to bol.com." data-wc_id = "'.$item['woocommerce_id'].'"></span>';
                }

                $display_data .= "<a href=' " . admin_url('admin') . ".php?page=shop2api_wc_to_bol_reports_detail&s=".$item['ean_number']."'>";
                $display_data .= '<span class="dashicons dashicons-info rpt-icons" id="rpt-more-info" data-wc_ean="'.$item['ean_number'].'"></span>';
                $display_data .= "</a>";

                return wp_kses($display_data, $allowed_html);
            case 'offer_last_sync_date':
            case 'product_last_sync_date':
            case 'ean_number':
            case 'product_name':
                return wp_kses($item[$column_name], array());
        }
        return "";
    }

    function get_sortable_columns(): array
    {
        return array(
            'ean_number' => array('ean_number', false),
            'offer_last_sync_date' => array('offer_last_sync_date', false),
            'product_last_sync_date' => array('product_last_sync_date', false),
        );
    }

    function get_sorting_str(): string
    {
        // Sanitize Get Values and Empty Value Check
        // Order By: Column
        // Order: Asc/Desc

        if (empty($_GET['orderby']) || empty($_GET['order'])) {
            return '';
        }

        if (sanitize_sql_orderby($_GET['orderby'] . ' ' . $_GET['order']) == false) {
            return '';
        }

        // Get Sorting Column
        $order_by = sanitize_text_field(esc_attr($_GET['orderby']));
        // If no order, default to asc
        $order = ($_GET['order'] == 'asc') ? '-' : '';

        return $order . $order_by;
    }

    function echo_modal()
    {
        $allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();
        $empty_table = '<table class="modal-table"><thead><tr><th>Request</th><th>Response</th></tr></thead><tbody><tr><td id="table-req_data"></td><td id="table-res_data"></td></tr></tbody></table>';
        echo(wp_kses('<div id="openModal" class="modalDialog">'.$empty_table.'<div id="report-modal-data"></div></div>', $allowed_html));
    }
}

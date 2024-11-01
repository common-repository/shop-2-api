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

class Shop2Api_Bol2WcOrderReport extends WP_List_Table
{
    protected array $report_data;

    function get_columns(): array
    {
        return array(
            'date_created' => __('Date Created'),
            'reference' => __('Bol Order Number'),
            'status' => __('Status'),
            'error_description' => __('Description'),
            'bol_data' => __('Bol Data'),
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

        $api_response = $shop_2_api_connection->get_wc_order_data_report($search, $sort, $current_page);
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
            case 'bol_data':
                $last_html = $this->echo_debug_button($item['bol_data']);
                if ($item['status'] == 'ERROR') {
                    $last_html .= '<i data-reference="'.$item['reference'].'" id="order-report-icon-stop"  class = "material-icons order-report-icon-stop" style="font-size: 22px; cursor:pointer;" title="Do Not Sync (Remove from dashboard)">stop</i>';
                    $last_html .= '<i data-reference="'.$item['reference'].'" id="order-report-icon-retry" class = "material-icons order-report-icon-retry" style="font-size: 22px; cursor:pointer;" title="This will remove the order and will be retried.">retry</i>';
                }
                return wp_kses($last_html, $allowed_html);
            case 'reference':
            case 'status':
            case 'error_description':
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
        $empty_table = '<table class="modal-table"><thead><tr><th>Response</th></tr></thead><tbody><tr><td id="table-res_data"></td></tr></tbody></table>';
        echo(wp_kses('<div id="openModal" class="modalDialog">'.$empty_table.'<div id="report-modal-data"></div></div>', $allowed_html));
    }

    function echo_debug_button($request_data): string
    {
        $data_res_str = ' data-res_info="' . urlencode(wp_json_encode($request_data)) . '" ';

        return '<button  class="show-more" ' . $data_res_str . '>Bol Data</button>';
    }
}

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

class Shop2Api_Bol2WcStatusReportDetail extends WP_List_Table
{
    protected array $report_data;

    function get_columns(): array
    {
        return array(
            'date_created' => 'Run Date',
            'ean_number' => 'ean_number',
//            'run_id' => 'Run Id',
            'status' => 'Status',
            'description' => 'Description',
            'debug' => 'Debug Info'
//            'request_data' => 'Data Sent to Bol',
//            'response_data' => 'Data Received from Bol'
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

        $api_response = $shop_2_api_connection->get_bol_connection_report($search, $sort, $current_page);
        $api_response_data = json_decode(wp_remote_retrieve_body($api_response), true);
        if ($api_response_data != null) {
            $this->report_data = $api_response_data['results'];
            $total_items = $api_response_data['count'];
        } else {
            $this->report_data = [];
            $total_items = 0;
        }

        $this->set_pagination_args(
            array(
                'total_items' => $total_items ,
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
            case 'status':
                if ($item[$column_name] == 'ERROR' || $item[$column_name] == 'FAIL') {
                    $display_data = '<span class="report-error-status">'.$item[$column_name].'</span>';
                } else {
                    $display_data =  '<span class="report-success-status">'.$item[$column_name].'</span>';
                }
                return wp_kses($display_data, $allowed_html);
            case 'run_id':
            case 'description':
            case 'ean_number':
                return wp_kses($item[$column_name], array());
            case 'debug':
                return wp_kses($this->echo_debug_button($item['request_data'], $item['response_data']), $allowed_html);
        }
        return "";
    }

    function get_sortable_columns(): array
    {
        return array(
            'date_created' => array('date_created', false),
            'ean_number' => array('ean_number', false),
            'description' => array('description', false),
            'status' => array('status', false),
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

    function echo_debug_button($request_data, $response_data): string
    {
        $data_req_str = ' data-req_info="' . urlencode(wp_json_encode($request_data)) . '" ';
        $data_res_str = ' data-res_info="' . urlencode(wp_json_encode($response_data)) . '" ';

        return '<button  class="show-more" ' . $data_req_str . $data_res_str . '>Debug Info</button>';
    }
}

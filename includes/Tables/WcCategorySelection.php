<?php

/**
 *
 * @package Shop2API
 *
 * This populates the page wp-admin/admin.php
 * This is the Group Mapping
 *
 */

require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

class Shop2Api_WcCategorySelection extends WP_List_Table
{
    protected array $report_data;
    protected array $bol_category_dropdown_data = [];
    protected array $saved_bol_values = [];
    protected array $allowed_html;

    function __construct()
    {

        $shop_2_api_connection = new Shop2ApiConnect();
        $api_response_data = $shop_2_api_connection->get_bol_category_info();
        if (!(is_array($api_response_data))) {
            $this->bol_category_dropdown_data = [];
        } else {
            if (array_key_exists('body', $api_response_data)) {
                $this->bol_category_dropdown_data = json_decode($api_response_data['body'], true);
            }
        }
        $api_response_data = $shop_2_api_connection->get_bol_wc_mapping_info();
        if (!(is_array($api_response_data))) {
            $this->saved_bol_values = [];
        } else {
            if (array_key_exists('body', $api_response_data)) {
                $this->saved_bol_values = json_decode($api_response_data['body'], true);
            }
        }

        require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
        $this->allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

        parent::__construct();
    }

    function get_columns(): array
    {
        $shipping_cost_detail = '<span class="tooltiptext">This is the <b>amount</b> that will be added to the price field on Bol.com</span>';
        $shipping_cost = '<div class="tooltip"><span class="dashicons dashicons-info-outline"></span> ' . $shipping_cost_detail . ' Shipping Cost (Optional)</div>';
        $commission_cost_detail = '<span class="tooltiptext">This is a <b>percentage</b> that will be added to the price field on Bol.com</span>';
        $commission_cost = '<div class="tooltip"><span class="dashicons dashicons-info-outline"></span> ' . $commission_cost_detail . 'Commission Percentage (Optional)</div>';
        return array(
            'woocommerce_field' => 'WooCommerce Field',
            'bol_category' => 'Bol Category',
            'bol_commission' => wp_kses($commission_cost, $this->allowed_html),
            'bol_shipping_cost' => wp_kses($shipping_cost, $this->allowed_html)
        );
    }

    function get_data()
    {
        $search_val = '';
        if (!empty($_GET['s'])) {
            $search_val = sanitize_title_for_query(esc_attr($_GET['s']));
        }

        $current_page = $this->get_pagenum();
        $per_page = 10;
        $total_count = 0;
        $woocommerce_category_field = count($this->saved_bol_values) > 0 ? $this->saved_bol_values[0]['woocommerce_category_field'] : '';

        $shop_2_api_connection = new Shop2ApiConnect();
        $api_response = $shop_2_api_connection->get_wc_product_values($woocommerce_category_field);
        $api_response_data = json_decode(wp_remote_retrieve_body($api_response), true);

        if (array_key_exists('success', $api_response_data)) {
            $data = $api_response_data['success'];

            // Searching
            if ($search_val != '') {
                $filtered_wc_data = preg_grep('/.*' . $search_val . '.*/', array_column($data, 'woocommerce_field'));
                $data = array_filter($data, function ($key) use ($filtered_wc_data) {
                    return in_array($key['woocommerce_field'], $filtered_wc_data);
                });
            }

            $paginated_data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
            $this->report_data = $paginated_data;
            $total_count = count($data);
        } else {
            $this->report_data = [];
        }

        $this->set_pagination_args(
            array(
                'total_items' => $total_count,
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
    }

    function column_default($item, $column_name): string
    {
        switch ($column_name) {
            case 'woocommerce_field':
                return esc_attr($item[$column_name]);
            case 'bol_category':
                return wp_kses($this->get_bol_category_dropdown($item[$column_name]), $this->allowed_html);
            case 'bol_shipping_cost':
                $val = "0";
                foreach ($this->bol_category_dropdown_data as $key => $value) {
                    $data = $this->get_saved_map_data_by_bol_slug($key, $item['bol_category']);
                    if (count($data) > 0) {
                        $val = empty($data['bol_shipping']) ? "0" : $data['bol_shipping'];
                        break;
                    }
                }
//                return var_dump($data);
                return wp_kses('<input type="number" value="'.esc_attr($val).'" class="wc-category-table-fields"/> &euro;', $this->allowed_html);
            case 'bol_commission':
                $val = "0";
                foreach ($this->bol_category_dropdown_data as $key => $value) {
                    $data = $this->get_saved_map_data_by_bol_slug($key, $item['bol_category']);
                    if (count($data) > 0) {
                        $val = empty($data['bol_commission']) ? "0" : $data['bol_commission'];
                        break;
                    }
                }
                return wp_kses('<input type="number" value="'.esc_attr($val).'"  class="wc-category-table-fields"/> %', $this->allowed_html);
        }
        return "";
    }

    function get_sortable_columns(): array
    {
        return array();
    }

    private function get_bol_category_dropdown($bol_cat_slug)
    {
        // Sanitized in column_default function
        $return_string = "<select data-bol_cat_slug = " . $bol_cat_slug . " name='wc-category' class='wc-category-table-fields'>";
        $return_string .= '<option value=""> -- No Category Mapped -- </option>';
        try {
            if (count($this->bol_category_dropdown_data) > 0) {
                foreach ($this->bol_category_dropdown_data as $key => $value) {
                    $selected = count($this->get_saved_map_data_by_bol_slug($key, $bol_cat_slug)) > 0 ? ('selected' ?: '') : '';
                    $return_string = $return_string . "<option value='" . $key . "' " . $selected . ">" . $value . "</option>";
                }
                $return_string .= "</select>";
                return $return_string;
            } else {
                return "Error Retrieving Data";
            }
        } catch (Exception $e) {
            echo('Error Retrieving Data');
            error_log('Caught exception: ' . $e->getMessage());
        }
        return "";
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav($which)
    {
        if ($which == "top") {
            echo wp_kses('<form">', $this->allowed_html);
            echo(wp_kses('<span name="wc-data-container"></span>', $this->allowed_html));
        }
    }

    // Overwrite table nav to add the search bar.
    function display_tablenav($which)
    {
        echo(wp_kses('<form action="" method="GET">', $this->allowed_html));
        if ('top' === $which) {
            $this->search_box(__('Search'), 'search-box-id');
        }
        parent::display_tablenav($which);
        echo(wp_kses('<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '"/>', $this->allowed_html));
        echo(wp_kses('</form>', $this->allowed_html));
    }

    private function get_saved_map_data_by_bol_slug($bol_code, $bol_cat_slug)
    {
        if (count($this->saved_bol_values) > 0) {
            if (array_key_exists('map_data', $this->saved_bol_values[0]) && $this->saved_bol_values[0]['map_data'] != NULL) {
                if (array_key_exists($bol_code, $this->saved_bol_values[0]['map_data'])) {
                    if (array_key_exists('wc_field_values', $this->saved_bol_values[0]['map_data'][$bol_code])) {
                        if (in_array($bol_cat_slug, $this->saved_bol_values[0]['map_data'][$bol_code]['wc_field_values'])) {
                            return $this->saved_bol_values[0]['map_data'][$bol_code];
                        }
                    }
                }
            }
        }
        return [];
    }
}

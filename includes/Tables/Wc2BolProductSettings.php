<?php

/**
 *
 * @package Shop2API
 *
 * This populates the page wp-admin/admin.php
 * This is Mapping of the WooCommerce fields to bol categories.
 *
 */

require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

class Shop2Api_Wc2BolProductSettings extends WP_List_Table
{
    protected array $report_data;
    protected array $bol_category_dropdown_data = [];
    protected array $saved_bol_values = [];
    protected array $allowed_html;

    private function set_bol_category_dopdown(): void
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
    }

    private function set_saved_bol_values(): void
    {
        $shop_2_api_connection = new Shop2ApiConnect();
        $api_response_data = $shop_2_api_connection->get_bol_wc_mapping_info();
        if (!(is_array($api_response_data))) {
            $this->saved_bol_values = [];
        } else {
            if (array_key_exists('body', $api_response_data)) {
                $this->saved_bol_values = json_decode($api_response_data['body'], true);
            }
        }
    }
    function __construct()
    {
        $this-> set_bol_category_dopdown();
        $this-> set_saved_bol_values();

        require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
        $this->allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

        parent::__construct();
    }

    function get_columns(): array
    {
        $tooltip_increase_amount_by = '<span class="tooltiptext">You can choose if you want your Bol.com price to be increased with a percentage or fixed amount.</span>';
        $increase_amount_by = '<div class="tooltip"><span class="dashicons dashicons-info-outline"></span> ' . $tooltip_increase_amount_by . ' Increase Amount By</div>';
        $tooltip_increase_amount = '<span class="tooltiptext">This is a <b>percentage/amount</b> that will be added to the price field on Bol.com</span>';
        $increase_amount = '<div class="tooltip"><span class="dashicons dashicons-info-outline"></span> ' . $tooltip_increase_amount . 'Value (Optional)</div>';
        return array(
            'active' => 'Active',
            'woocommerce_field' => 'WooCommerce Field',
            'woocommerce_product_map_data' => 'Bol Category',
            'increase_offer_amount_by' => wp_kses($increase_amount_by, $this->allowed_html),
            'increase_amount' => wp_kses($increase_amount, $this->allowed_html)
        );
    }

    function get_data(): void
    {
        $search_val = '';
        if (!empty($_POST['s'])) {
            $search_val = sanitize_title_for_query(esc_attr($_POST['s']));
        }

        $current_page = $this->get_pagenum();
        $per_page = 10;
        $total_count = 0;
        $woocommerce_category_field = get_option('woocommerce_category_field', 'main_categories');
        if (!($woocommerce_category_field)) {
            $woocommerce_category_field = 'main_categories';
        }

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

    function prepare_items(): void
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
            case 'active':
                $saved_value = $this->getSavedValue($item['bol_category'], $column_name);
                $selected = $saved_value  ? 'checked' : "";
                return '<input type="checkbox" name="setting_active" '.$selected.'/>';
            case 'woocommerce_field':
                return esc_attr($item[$column_name]);
            case 'woocommerce_product_map_data':
                return wp_kses($this->get_bol_category_dropdown($item['bol_category']), $this->allowed_html);
            case 'increase_offer_amount_by':
                return wp_kses($this->get_increase_by_dropdown($item['bol_category']), $this->allowed_html);
            case 'increase_amount':
                $saved_amount = $this->getSavedValue($item['bol_category'], $column_name);
                if (is_numeric($saved_amount)) {
                    $saved_value = round($saved_amount, 2);
                } else {
                    $saved_value = 0;
                }
                return wp_kses('<input name="increase-amount" min="0" max="100" in type="number" step="1" value="'.esc_attr($saved_value).'"  class="wc-category-table-fields"/>', $this->allowed_html);
        }
        return "";
    }

    function get_sortable_columns(): array
    {
        return array();
    }

    private function get_increase_by_dropdown($bol_cat_slug):string
    {
        $selected_value = $this->getSavedValue($bol_cat_slug, 'increase_offer_amount_by');
        $dropdown_values['VALUE'] = "Value";
        $dropdown_values['PERCENTAGE'] = "Percentage";

        $return_string = "<select name='wc-increase-by' class='wc-category-table-fields'>";
        foreach ($dropdown_values as $key => $value) {
            $selected = ($selected_value == $key) ? 'selected' : '';
            $return_string = $return_string . "<option value='" . $key . "' " . $selected . ">" . $value . "</option>";
        }
        $return_string .= "</select>";
        return $return_string;
    }

    private function get_bol_category_dropdown($bol_cat_slug): string
    {
        $selected_value = $this->getSavedValue($bol_cat_slug, 'woocommerce_product_map_data');
        // Sanitized in column_default function
        $return_string = "<select data-bol_cat_slug = " . $bol_cat_slug . " name='bol-category' class='wc-category-table-fields'>";
        $return_string .= '<option value=""> -- No Category Mapped -- </option>';
        try {
            if (count($this->bol_category_dropdown_data) > 0) {
                foreach ($this->bol_category_dropdown_data as $key => $value) {
                    $selected = ($selected_value == $key) ? 'selected' : '';
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
    function extra_tablenav($which): void
    {
        if ($which == "top") {
            echo wp_kses('<form">', $this->allowed_html);
            echo(wp_kses('<span name="wc-data-container"></span>', $this->allowed_html));
        }
    }

    // Overwrite table nav to add the search bar.
//    function display_tablenav($which): void
//    {
//        echo(wp_kses('<form action="" method="GET">', $this->allowed_html));
//        if ('top' === $which) {
//            echo('<form>');
//            echo wp_kses('<form">', $this->allowed_html);
//            $this->search_box(__('Search'), 'search-box-id');
//            echo('<div>qwe</div>');
//            echo('</form>');
//        }
//        parent::display_tablenav($which);
//        echo(wp_kses('<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '"/>', $this->allowed_html));
//
//        echo(wp_kses('</form>', $this->allowed_html));
//    }

    private function get_saved_map_data_by_bol_slug($bol_cat_slug)
    {
        foreach ($this->saved_bol_values as $saved_bol_value) {
            if ($bol_cat_slug == $saved_bol_value['woocommerce_field']) {
                return $saved_bol_value;
            }
        }
        return [];
    }

    /**
     * @param $bol_category
     * @param $column_name
     * @return string
     */
    private function getSavedValue($bol_category, $column_name): string
    {
        $saved_row = $this->get_saved_map_data_by_bol_slug($bol_category);
        $saved_value = "";
        if ($saved_row) {
//            var_dump($saved_row);
            if (array_key_exists($column_name, $saved_row)) {
                $saved_value = $saved_row[$column_name];
            }
        }
        return $saved_value;
    }
}

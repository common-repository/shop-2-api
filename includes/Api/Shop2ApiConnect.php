<?php
/**
 *
 * @package Shop2API
 *
 * Description: This is the main methods handling connections with a Shop 2 Api Server
 * Constructor is initialized with the connection to the server, SHOP_2_API_URL env var can be set for testing.
 * Authorization is done via token which is provided by Shop 2 Api.
 *
 */


class Shop2ApiConnect
{
    public $shop_2_api_url = "";
    protected $header = [];

    function __construct()
    {
        $token = esc_attr(get_option('shop2api_token'));
        $this->shop_2_api_url = getenv('SHOP_2_API_URL', true) ? getenv('SHOP_2_API_URL') : "https://www.shop2apidevelopment.com";
        $this->header = ['Authorization' => 'Token ' . $token];
        if (get_site_url() == 'http://localhost:8000' || get_site_url() == 'http://127.0.0.1:8000') {
            $this->header = array_merge($this->header, ['authurl' => "https://shop2api.com/testshop"]);
        } else {
            $this->header = array_merge($this->header, ['authurl' => get_site_url()]);
        }
    }

    public function get_user_message()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/api/user_message/', ['headers' => $this->header]
        );
    }

    public function check_connection($token = NULL): bool
    {
        if ($token != NULL) {
            $this->header = ['Authorization' => 'Token ' . $token];
        }

        $response = wp_remote_get(
            $this->shop_2_api_url . '/api/orders/', ['headers' => $this->header]
        );

        update_option('shop2api_connection_succeeded', wp_remote_retrieve_response_code($response) == 200);
        return wp_remote_retrieve_response_code($response) == 200;
    }

    // Check if bol can connect
    public function check_bol_connection(): bool
    {
        $response = wp_remote_get(
            $this->shop_2_api_url . '/bol/test-connection/', ['headers' => $this->header]
        );
        $response_data = json_decode(wp_remote_retrieve_body($response), true);
        if (!$response_data) return False;
        update_option('shop2api_bol_connection_succeeded', array_key_exists('success', $response_data));
        return array_key_exists('success', $response_data);
    }

    // Check if wc can connect
    public function check_wc_connection(): bool
    {
        $response = wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/test-connection/', ['headers' => $this->header]
        );
        $response_data = json_decode(wp_remote_retrieve_body($response), true);
        if (!$response_data) return False;
        update_option('shop2api_wc_connection_succeeded', array_key_exists('success', $response_data));
        return array_key_exists('success', $response_data);
    }

    private function get_orders()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/api/orders/', ['headers' => $this->header]
        );
    }

    // Activate/Deactivate Connections
    public function activate_deactivate_connection($active_status)
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/deactivate-activate-account/?active=' . $active_status,
            ['headers' => $this->header]
        );
    }

    // This will get the order and if there is no order it will die (because it is either expired or not ordered)
    // The order also translate the product of the user.
    public function get_order()
    {
        $orders = wp_remote_retrieve_body($this->get_orders());
        $json_orders = json_decode($orders, true);
        if (gettype($json_orders) != 'array')
        {
            return [];
        }
        if (count($json_orders) == 0 || $json_orders == NULL) return [];
        if (array_key_exists('detail', $json_orders) && str_contains($json_orders["detail"], 'Invalid token')) return [];
        if (array_key_exists('detail', $json_orders) && str_contains($json_orders["detail"], 'inactive or deleted')) return [];

        return $json_orders[0];
    }

    public function get_bol_connection_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/api/connection/?dropdown=True', ['headers' => $this->header]
        );
    }

    public function get_bol_connection_report($search = '', $order = '', $page = 1, $size = 10)
    {
        $parameters = array(
            'page' => $page,
            'page_size' => strval($size),
            'ordering' => $order,
            'search' => $search
        );
        $url = $this->shop_2_api_url . '/bol/api/connection/report/';
        $url = add_query_arg($parameters, $url);

        return wp_remote_get($url, ['headers' => $this->header]);
    }

    public function get_wc_order_data_report($search = '', $order = '', $page = 1, $size = 10)
    {
        $parameters = array(
            'page' => $page,
            'page_size' => strval($size),
            'ordering' => $order,
            'search' => $search
        );
        $url = $this->shop_2_api_url . '/bol/api/connection/order-report/';
        $url = add_query_arg($parameters, $url);

        return wp_remote_get($url, ['headers' => $this->header]);
    }

    public function get_wc_product_data_report($search = '', $order = '', $page = 1, $size = 10)
    {
        $parameters = array(
            'page' => $page,
            'page_size' => strval($size),
            'ordering' => $order,
            'search' => $search
        );
        $url = $this->shop_2_api_url . '/woocommerce/api/product-data/0/';
        $url = add_query_arg($parameters, $url);

        return wp_remote_get($url, ['headers' => $this->header]);
    }

    public function get_bol_category_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/get-content/?dropdown=True', ['headers' => $this->header]
        );
    }

    public function get_bol_offer_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/get-offer-content/', ['headers' => $this->header]
        );
    }

    public function get_bol_category_info_by_value($value)
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/get-content/?id=' . $value, ['headers' => $this->header]
        );
    }

    public function get_wc_connection_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/api/connection/', ['headers' => $this->header]
        );
    }

    public function get_wc_product_data_report_offer()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/api/grouped-summary-offer/', ['headers' => $this->header]
        );
    }

    public function get_wc_product_data_report_product()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/api/grouped-summary-product/', ['headers' => $this->header]
        );
    }

    public function get_wc_product_data_report_order()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/api/connection/order-report/', ['headers' => $this->header]
        );
    }

    public function set_wc_order_to_new_status($order_reference, $new_status)
    {
        $data = [
            "status" => $new_status,
        ];

        return wp_remote_post(
            $this->shop_2_api_url . '/bol/api/order-status/'.$order_reference.'/',
            [
                'headers' => $this->header,
                'body' => $data,
                'method' =>'PATCH'
            ],

        );
    }

    public function get_wc_product_data_order_summary()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/api/connection/order-summary/', ['headers' => $this->header]
        );
    }

    public function get_bol_product_data($ean_number)
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/bol/product-per-ean/' . $ean_number . '/', ['headers' => $this->header]
        );
    }

    public function set_bol_connection_info($client_id, $client_secret)
    {

        $order = $this->get_order();
        $conn_url = '/bol/api/connection/';
        $method = 'POST';

        //Check if there is connections
        $connections = wp_remote_retrieve_body($this->get_bol_connection_info());
        $json_connections = json_decode($connections, true);
        if (count($json_connections) != 0) {
            $conn_url = $conn_url . $json_connections[0]["id"] . '/';
            $method = 'PATCH';
        }

        $data = [
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "customer_product" => $order["id"]
        ];

        return wp_remote_post(
            $this->shop_2_api_url . $conn_url,
            [
                'headers' => $this->header,
                'body' => $data,
                'method' => $method
            ]
        );
    }

    public function set_wc_connection_info($client_key, $client_secret)
    {

        $order = $this->get_order();
        $conn_url = '/woocommerce/api/connection/';
        $method = 'POST';

        //Check if there is connections
        $connections = wp_remote_retrieve_body($this->get_wc_connection_info());
        $json_connections = json_decode($connections, true);

        if (count($json_connections) != 0) {
            $conn_url = $conn_url . $json_connections[0]["id"] . '/';
            $method = 'PATCH';
        }

        $data = [
            "consumer_key" => $client_key,
            "consumer_secret" => $client_secret,
            "customer_product" => $order["id"],
            "url" => get_site_url()
        ];

        return wp_remote_post(
            $this->shop_2_api_url . $conn_url,
            [
                'headers' => $this->header,
                'body' => $data,
                'method' => $method
            ]
        );
    }

    public function get_bol_wc_mapping_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/map/api/wc-product-sync-options/', ['headers' => $this->header]
        );
    }

    public function get_bol_wc_mapping_detail_info()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/map/api/wc-product-sync-data/', ['headers' => $this->header]
        );
    }

    public function get_wc_field_options()
    {

        $connections = wp_remote_retrieve_body($this->get_wc_connection_info());
        $json_connections = json_decode($connections, true);

        if (count($json_connections) > 0) {
            return wp_remote_get(
                $this->shop_2_api_url . '/woocommerce/options/' . $json_connections[0]["id"] . '/',
                ['headers' => $this->header]
            );
        }
        return null;
    }

    public function get_wc_product_values($value)
    {

        $connections = wp_remote_retrieve_body($this->get_wc_connection_info());
        $json_connections = json_decode($connections, true);

        if (!(is_array($json_connections))) {
            wp_redirect(admin_url('/admin.php?page=shop2api_plugin'));
            exit;
        }

        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/product_values/' . $json_connections[0]["id"] . '/',
            [
                'headers' => $this->header,
                'body' => ["value" => $value]
            ]
        );
    }

    public function sync_woocommerce_to_bol()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/start_product_sync/',
            [
                'headers' => $this->header
            ]
        );
    }
    public function refresh_wc_dropdowns()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/refresh-dropdowns/',
            [
                'headers' => $this->header
            ]
        );
    }

    public function sync_woocommerce_to_bol_with_id($woocommerce_id)
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/start_product_sync/' . $woocommerce_id . '/',
            [
                'headers' => $this->header
            ]
        );
    }

    public function set_bol_wc_mapping($woocommerce_category_field, $map_data)
    {
        update_option('woocommerce_category_field', $woocommerce_category_field);

        $mapping_url = $this->shop_2_api_url . '/map/save_product_map/';
        $method = 'POST';

        $data = ["map_data" => $map_data,"woocommerce_category_field" => $woocommerce_category_field];

        return wp_remote_post(
            $mapping_url,
            [
                'headers' => $this->header,
                'body' => $data,
                'method' => $method
            ]
        );
    }

    public function set_wc_to_bol_submit($bol_category_field, $map_data)
    {
        $mapping_url = '/map/api/wc-product-map-data/' . $bol_category_field . '/';
        $method = 'PATCH';

        return wp_remote_post(
            $this->shop_2_api_url . $mapping_url,
            [
                'headers' => $this->header,
                'body' => ["map_data" => $map_data],
                'method' => $method
            ]
        );
    }

    // Send sync for tags
    public function update_tag_cache()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/handle-tag-web-hook/',
            [
                'headers' => $this->header
            ]
        );
    }

    // Send sync for cat
    public function update_cat_cache()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/handle-cat-web-hook/',
            [
                'headers' => $this->header
            ]
        );
    }

    // Send sync for attr
    public function update_attr_cache()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/handle-attr-web-hook/',
            [
                'headers' => $this->header
            ]
        );
    }

    // Send sync for metadata
    public function update_metadata_cache()
    {
        return wp_remote_post(
            $this->shop_2_api_url . '/woocommerce/handle-metadata-web-hook/',
            [
                'headers' => $this->header
            ]
        );
    }

    // Get Sync info for display purposes
    public function get_sync_info($product_id)
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/api/product-data/' . $product_id . '/',
            [
                'headers' => $this->header
            ]
        );
    }

    // Register Free account on shop2api API CALL
    public function register_shop_2_api_free_account($email)
    {
        update_option('shop2api_email', $email);

        return wp_remote_post(
            $this->shop_2_api_url . '/register-free-account/',
            [
                'headers' => $this->header,
                'body' => ["email" => $email],
                'method' => 'POST'
            ]
        );
    }

    // Get Order data
    public function get_order_data()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/map/api/wc-order/', ['headers' => $this->header]
        );
    }

    // Set Order data
    public function set_order_data(
        $active, $status, $paid, $woocommerce_ean_field, $woocommerce_ean_category, $combideal_active,
        $combideal_ean_field, $combideal_ean_category, $order_email, $use_bol_price, $stock_sync, $alert_on_order_fail,
        $alert_email, $bol_price_inc_tax
    )
    {
        $order = $this->get_order();
        $request_url = '/map/api/wc-order/';
        $method = 'POST';

        $order_data = wp_remote_retrieve_body($this->get_order_data());
        $json_data = json_decode($order_data, true);

        if (count($json_data) != 0) {
            $request_url = $request_url . $json_data[0]["id"] . '/';
            $method = 'PATCH';
        }

        $data = [
            'active' => ($active != ''),
            'paid' => ($paid != ''),
            'status' => $status,
            "product" => $order["id"],
            "woocommerce_ean_field" => $woocommerce_ean_field,
            "woocommerce_ean_category" => $woocommerce_ean_category,
            "combi_deal_active" => ($combideal_active != ''),
            "combi_deal_ean_field" => $combideal_ean_field,
            "combi_deal_ean_category" => $combideal_ean_category,
            "order_email" => $order_email,
            "use_bol_price" => ($use_bol_price != ''),
            "sync_stock" => ($stock_sync != ''),
            "alert_on_order_fail" => ($alert_on_order_fail != ''),
            "alert_email" => $alert_email,
            "bol_price_include_tax" => ($bol_price_inc_tax != ''),
        ];

        return wp_remote_post(
            $this->shop_2_api_url . $request_url,
            [
                'headers' => $this->header,
                'body' => $data,
                'method' => $method
            ]
        );
    }

    // Get koopblok data
    public function get_koopblok_data()
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/map/api/wc-koopblok/', ['headers' => $this->header]
        );
    }

    // Set koopblok data
    public function set_koopblok_data(
        $active, $woocommerce_price_field, $price_increments, $days_increment_price,
        $minimum_price_limit,  $on_success_update_woocommerce, $seller_id,
        $woocommerce_ean_field, $maximum_price_limit,  $woocommerce_ean_category
    )
    {
        $order = $this->get_order();
        $request_url = '/map/api/wc-koopblok/';
        $method = 'POST';

        $koopblok_data = wp_remote_retrieve_body($this->get_koopblok_data());
        $json_data = json_decode($koopblok_data, true);

        if (count($json_data) != 0) {
            $request_url = $request_url . $json_data[0]["id"] . '/';
            $method = 'PATCH';
        }

        $data = [
            'active' => ($active != ''),
            'woocommerce_price_field' => $woocommerce_price_field,
            'price_increments' => $price_increments,
            'days_increment_price' => $days_increment_price,
            'minimum_price_limit' => $minimum_price_limit,
            'on_success_update_woocommerce' => $on_success_update_woocommerce,
            "product" => $order["id"],
            "seller_id" => $seller_id,
            "woocommerce_ean_field" => $woocommerce_ean_field,
            "maximum_price_limit" => $maximum_price_limit,
            "woocommerce_ean_category" => $woocommerce_ean_category,
        ];

        return wp_remote_post(
            $this->shop_2_api_url . $request_url,
            [
                'headers' => $this->header,
                'body' => $data,
                'method' => $method
            ]
        );
    }

    // Remove offer from bol
    public function remove_offer_from_bol($woocommerce_id)
    {
        $request_url = '/bol/remove-offer/' . $woocommerce_id . '/';
        $method = 'POST';

        return wp_remote_post(
            $this->shop_2_api_url . $request_url,
            [
                'headers' => $this->header,
                'method' => $method
            ]
        );
    }

    public function start_sync_orders($order_id)
    {
        return wp_remote_get(
            $this->shop_2_api_url . '/woocommerce/start-order-sync/' . $order_id . '/', ['headers' => $this->header]
        );
    }
}

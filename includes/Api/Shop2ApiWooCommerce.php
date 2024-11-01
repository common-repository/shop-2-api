<?php
/**
 * 
 * @package Shop2API
 * 
 * Description: This is the functions to get/set data for WC in shop 2 api
 *
 */


class Shop2ApiWooCommerce
{
    public static function get_auth_url() {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $connection = new Shop2ApiConnect();
        $order = $connection -> get_order();
        if (!$order) return "";

        $store_url = get_site_url();
        $endpoint = '/wc-auth/v1/authorize';
        $callback = $connection->shop_2_api_url . '/woocommerce/connect/';

        $params = [
            'app_name' => 'Shop 2 API',
            'scope' => 'read_write',
            'user_id' => $order['product_uuid'],
            'return_url' => get_site_url() . '/wp-admin/admin.php?page=shop2api_plugin',
            'callback_url' => $callback
        ];
        $query_string = http_build_query( $params );

        return $store_url . $endpoint . '?' . $query_string;
    }
}

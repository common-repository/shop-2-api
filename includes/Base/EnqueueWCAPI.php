<?php

/**
 *
 * @package Shop2API
 *
 * This is the function to enqueue the Custom API Calls for WooCommerce
 *
 */

class Shop2Api_EnqueueWCAPI
{

    public function register(): void
    {
        // Custom API Endpoint for getting Metadata
        add_action( 'rest_api_init', array($this, 'register_shop2api_woocommerce_endpoints' ));
    }


    //Add Custom API End-Point
    function register_shop2api_woocommerce_endpoints(): void
    {
        register_rest_route( 'shop-2-api/v1', '/search-meta-contains', array(
            'methods'  => 'GET',
            'callback' => array($this, 'meta_field_search_callback'),
            'permission_callback' => '__return_true',
        ));
    }

    function meta_field_search_callback($request) {
        try {
            $meta_key = $request->get_param('meta_key');
            $meta_value = esc_sql($request->get_param('meta_value'));
            $compare_operator = $request->get_param('compare_operator');
            $page = max(1, $request->get_param('page'));
            $posts_per_page = 100;

            $args = array(
                'post_type' => array('product', 'product_variation'),
                'meta_query' => array(
                    array(
                        'key' => $meta_key,
                        'value' => $meta_value,
                        'compare' => $compare_operator,
                        'posts_per_page' => -1,
                    )
                ),
                'paged' => $page,
                'posts_per_page' => $posts_per_page,
            );

            $query = new WP_Query($args);
            if ($query->have_posts()) {
                $products = $this->getProducts($query, $meta_key, $meta_value);
                return rest_ensure_response($products);
            } else {
                return new WP_Error('no_products_found', 'No products found. (' . $meta_value . ')', array('status' => 402));
            }
        } catch (Exception $e) {
            return new WP_Error('internal_server_error', $e->getMessage(), array('status' => 500));
        }
    }

    /**
     * @param WP_Query $query
     * @param string $meta_key
     * @param string $meta_value
     * @return array
     */
    public function getProducts($query, $meta_key, $meta_value): array
    {
        $products = array();
        while ($query->have_posts()) {
            $query->the_post();
            $current_post_id = get_the_ID();
            $current_post_type = get_post_type($current_post_id);
            if ($current_post_type == 'product_variation') {
                $product = new WC_Product_Variation($current_post_id);
            } else {
                $product = new WC_Product($current_post_id);
            }

            $products[] = array(
                'id' => $current_post_id,
                'name' => get_the_title(),
                'price' => $product->get_price(),
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'stock_quantity' => $product->get_stock_quantity()
            );
        }
        return $products;
    }
}

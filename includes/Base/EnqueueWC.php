<?php

/**
 *
 * @package Shop2API
 *
 * This is the function to enqueue the JS and CSS files for WooCommerce
 *
 */

class Shop2Api_EnqueueWC
{
    protected array $allowed_html;

    public function version_id() {
        if ( WP_DEBUG )
            return time();
        return VERSION;
    }

    function __construct()
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
        $this->allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();
    }

    public function register(): void
    {
        // Actions
        add_action('woocommerce_product_options_general_product_data', array($this, 'woocommerce_general_product_data_custom_field'));
        add_action('woocommerce_process_product_meta', array($this, 'woocommerce_process_product_meta_fields_save'));
        add_action('manage_product_posts_custom_column', array($this, 'add_columns_into_product_list_action'), 10, 2);
        add_action('pre_get_posts', array($this, 'event_column_orderby'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_items'));
//        add_action( 'woocommerce_product_quick_edit_end',  array($this, 'woocommerce_product_quick_edit_end'), 10, 0 );
//        add_action( 'woocommerce_product_quick_edit_save',  array($this, 'woocommerce_product_quick_edit_save'), 10, 1 );
        add_action( 'woocommerce_product_after_variable_attributes', array($this, 'variation_settings_fields'), 10, 3 );
        add_action( 'woocommerce_save_product_variation', array($this, 'save_variation_settings_fields'), 10, 2 );

        // Filters
        add_filter('manage_edit-product_columns', array($this, 'add_columns_into_product_list_filter'));
        add_filter('manage_edit-product_sortable_columns', array($this, "sortable_columns"));

        // Custom API Endpoint for getting Metadata
        add_action( 'rest_api_init', array($this, 'register_meta_field_search_endpoint' ));

        // Add Bol Order Id in the Order List
        add_filter( 'manage_edit-shop_order_columns', array($this, 'add_custom_metadata_column_to_order_list' ));
        add_filter( 'woocommerce_shop_order_search_fields', array($this, 'make_order_id_searchable' ));
        add_action( 'manage_shop_order_posts_custom_column',  array($this, 'display_custom_metadata_column_value'), 10, 2 );

        // Hook into the 'woocommerce_order_status_changed' action
        add_action( 'woocommerce_order_status_changed',  array($this,'woocommerce_send_order'), 10, 3 );
    }

    // Make searchable
    function make_order_id_searchable( $meta_keys ) {
        $meta_keys[] = 'bol_order_id';
        return $meta_keys;
    }

    /**
     * Custom function to be executed when order status changes
     *
     * @param int     $order_id   The order ID
     * @param string  $old_status The old order status
     * @param string  $new_status The new order status
     */
    function woocommerce_send_order( $order_id, $old_status, $new_status ) {
        // Perform custom actions based on the order status change
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_connection->start_sync_orders($order_id);
    }

    // Add Bol Number in Order List
    function add_custom_metadata_column_to_order_list( $columns ) {
        $columns['bol_order_id'] = 'Bol Order Id';
        return $columns;
    }
    function display_custom_metadata_column_value( $column, $post_id ) {
        if ( $column == 'bol_order_id' ) {
            $order = wc_get_order( $post_id );
            $custom_metadata_value = $order->get_meta( 'bol_order_id', true );
            echo $custom_metadata_value;
        }
    }

    //Enqueue Scripts
    function enqueue_items($hook)
    {
        if ($hook == 'post.php' || $hook == 'edit.php') {
            wp_enqueue_script(
                'shop2api_wc', SHOP2API_PLUGIN_URL . '/assets/bol2api_wc_scripts.js', [], $this->version_id()
            );

            wp_localize_script(
                'bol2api_scripts_common',
                'settings',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ajax-nonce')
                )
            );
        }
    }

    // Display Fields using WooCommerce Action Hook
    function woocommerce_general_product_data_custom_field(): void
    {
        global $post;
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $product = wc_get_product( get_the_ID() );
        $shop_2_api_connection = new Shop2ApiConnect();
        echo wp_kses('<div class="options_group">', $this->allowed_html);
        // Sync to koopblok field
        $shop2api_koopblok_service = get_post_meta( $post->ID, 'shop2api_koopblok_service', true);
        woocommerce_wp_checkbox(
            array(
                'id'            => 'shop2api_koopblok_service',
                'wrapper_class' => 'checkbox_class',
                'label'         => __('Repricer Service', 'woocommerce' ),
                'description'   => __( 'If checked it will enable repricer service for this product.', 'woocommerce' ),
                'value'         => $shop2api_koopblok_service ? $shop2api_koopblok_service : 'no'
            )
        );


        // Sync to bol field
        $shop2api_sync_to_bol = get_post_meta($post->ID, 'shop2api_sync_to_bol', true);
        woocommerce_wp_checkbox(
            array(
                'id' => 'shop2api_sync_to_bol',
                'wrapper_class' => 'checkbox_class',
                'label' => __('Sync Product to Bol', 'woocommerce'),
                'description' => __('If checked it will sync product to Bol.com', 'woocommerce'),
                'value' => $shop2api_sync_to_bol ? $shop2api_sync_to_bol : 'no'
            )
        );

        if (!$product->is_type('variable')) {
            // Add EAN Number
            $shop2api_ean_number = get_post_meta($post->ID, 'shop2api_ean_number', true);
            woocommerce_wp_text_input(
                array(
                    'id' => 'shop2api_ean_number',
                    'wrapper_class' => 'cfwc-custom-field',
                    'label' => __('Bol EAN Number', 'woocommerce'),
                    //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                    'value' => $shop2api_ean_number
                )
            );
            // Add COMBI EAN Number
            $shop2api_combi_ean_number = get_post_meta($post->ID, 'shop2api_combi_ean_number', true);
            woocommerce_wp_text_input(
                array(
                    'id' => 'shop2api_combi_ean_number',
                    'wrapper_class' => 'cfwc-custom-field',
                    'label' => __('Combideal EAN Number', 'woocommerce'),
                    //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                    'value' => $shop2api_combi_ean_number
                )
            );

            $connect_info = $shop_2_api_connection->get_sync_info($post->ID);
            if (is_wp_error($connect_info)) {
                echo '';
            } elseif (in_array(wp_remote_retrieve_response_code($connect_info), [200, 201])) {
                $connect_info_data = json_decode(wp_remote_retrieve_body($connect_info), true);
                if ($connect_info_data && array_key_exists('results', $connect_info_data)) {
                    $results = $connect_info_data['results'];
                    if (count($results) > 0) {
                        echo wp_kses('<p class="form-field">', $this->allowed_html);
                        echo wp_kses('<label>#' . $results[0]['woocommerce_id'] . '</label>', $this->allowed_html);
                        echo wp_kses('<button class="button-primary" data-wc_id="' . $results[0]['woocommerce_id'] . '" id="shop2api-remove-ean">' . __("Remove Offer") . '</button>', $this->allowed_html);
                        echo wp_kses('</p>', $this->allowed_html);

                        echo wp_kses('<p class="form-field">', $this->allowed_html);
                        echo wp_kses('<label>Product: </label><b>' . $results[0]['product_last_sync_status'] . ' - ' . $results[0]['product_last_sync_date'] . '</b>', $this->allowed_html);
                        echo wp_kses('</p>', $this->allowed_html);

                        echo wp_kses('<p class="form-field">', $this->allowed_html);
                        echo wp_kses('<label>Offer: </label><b>' . $results[0]['offer_last_sync_status'] . ' - ' . $results[0]['offer_last_sync_date'] . '</b>', $this->allowed_html);
                        echo wp_kses('</p>', $this->allowed_html);

                    } else {
                        echo wp_kses('<p class="form-field shop2api_sync_to_bol_field">', $this->allowed_html);
                        echo wp_kses('<label>No Sync Data Found</label>', $this->allowed_html);
                        echo wp_kses('<p>', $this->allowed_html);
                    }
                }
            }
        } else {
            // Small update to ensure on the main products the EAN is empty.
            delete_post_meta($post->ID, 'shop2api_ean_number');
            delete_post_meta($post->ID, 'shop2api_combi_ean_number');
        }
        echo wp_kses('</div>', $this->allowed_html);
    }

    // Add the checked option to the quick edit function
    function woocommerce_product_quick_edit_end()
    {
        // Something to look at https://www.jclabs.co.uk/adding-woocommerce-products-custom-fields/
        global $post;
        $shop2api_sync_to_bol = get_post_meta($post->ID, 'shop2api_sync_to_bol', true);
        echo wp_kses('<div class="inline-edit-group">', $this->allowed_html);
        woocommerce_wp_checkbox(
            array(
                'id' => 'shop2api_sync_to_bol',
                'wrapper_class' => 'checkbox_class',
                'label' => '',
                'description' => __('If checked it will sync product to Bol.com', 'woocommerce'),
                'value' => $shop2api_sync_to_bol ? $shop2api_sync_to_bol : 'no'
            )
        );
        echo wp_kses('</div>', $this->allowed_html);

        $shop2api_koopblok_service = get_post_meta( $post->ID, 'shop2api_koopblok_service', true);
        echo wp_kses('<div class="inline-edit-group">', $this->allowed_html);
        woocommerce_wp_checkbox(
            array(
                'id'            => 'shop2api_koopblok_service',
                'wrapper_class' => 'checkbox_class',
                'label'         => '',
                'description'   => __( 'If checked it will enable koopblok service for this product.', 'woocommerce' ),
                'value'         => $shop2api_koopblok_service ? $shop2api_koopblok_service : 'no'
            )
        );
        echo wp_kses('</div>', $this->allowed_html);

        // Add EAN Number
        $shop2api_ean_number = get_post_meta( $post->ID, 'shop2api_ean_number', true);
        woocommerce_wp_text_input(
            array(
                'id'            => 'shop2api_ean_number',
                'wrapper_class' => 'cfwc-custom-field',
                'label'         => __('Bol EAN Number', 'woocommerce'),
                //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                'value'         => $shop2api_ean_number
            )
        );

        // Add COMBI EAN Number
        $shop2api_combi_ean_number = get_post_meta( $post->ID, 'shop2api_combi_ean_number', true);
        woocommerce_wp_text_input(
            array(
                'id'            => 'shop2api_combi_ean_number',
                'wrapper_class' => 'cfwc-custom-field',
                'label'         => __('Combideal EAN Number', 'woocommerce'),
                //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                'value'         => $shop2api_combi_ean_number
            )
        );
    }

    // Save Fields using WooCommerce Action Hook
    function woocommerce_product_quick_edit_save($product)
    {
        $post_id = $product->id;
        $this->shop2api_set_meta_for_post_id($post_id);
    }

    function woocommerce_process_product_meta_fields_save($post_id)
    {
        $this->shop2api_set_meta_for_post_id($post_id);
    }

    function shop2api_set_meta_for_post_id($post_id){
        $previous_sync_to_bol = get_post_meta( $post_id, 'shop2api_sync_to_bol', true );
        $previous_koopblok_service = get_post_meta( $post_id, 'shop2api_koopblok_service', true );

        // Sync to Bol Field
        $woo_checkbox_sync = isset($_POST['shop2api_sync_to_bol']) ? 'yes' : 'no';
        update_post_meta($post_id, 'shop2api_sync_to_bol', $woo_checkbox_sync);

        // Sync to Koopblok Field
        $woo_checkbox_koopblok = isset($_POST['shop2api_koopblok_service']) ? 'yes' : 'no';
        update_post_meta($post_id, 'shop2api_koopblok_service', $woo_checkbox_koopblok);

        //Save Set Variations to "Y" if parent is set to "Y"
        $parent_product = wc_get_product($post_id);
        if ($parent_product->parent_id == 0 and $woo_checkbox_sync == "yes" and $previous_sync_to_bol == "no")
        {
            foreach ($parent_product->get_children() as $child_id) {
                update_post_meta($child_id, 'shop2api_sync_to_bol',"yes");
            }
        }

        if ($parent_product->parent_id == 0 and $woo_checkbox_koopblok == "yes" and $previous_koopblok_service == "no")
        {
            foreach ($parent_product->get_children() as $child_id) {
                update_post_meta($child_id, 'shop2api_koopblok_service',"yes");
            }
        }
        if (!$parent_product->is_type('variable')) {
            $this->set_ean_numbers_per_post($post_id);
        }
    }

    //Save Variation Fields
    function save_variation_settings_fields( $variation_id, $loop ) {
        // Sync to Bol Field
        $woo_checkbox = isset($_POST['shop2api_sync_to_bol']) ? 'yes' : 'no';
        update_post_meta($variation_id, 'shop2api_sync_to_bol', $woo_checkbox);

        // Sync to Koopblok Field
        $woo_checkbox = isset($_POST['shop2api_koopblok_service']) ? 'yes' : 'no';
        update_post_meta($variation_id, 'shop2api_koopblok_service', $woo_checkbox);

        $this->set_ean_numbers_per_post($variation_id);
    }

    //Add custom column into Product Page
    function add_columns_into_product_list_filter($defaults)
    {
        $defaults['shop2api_sync_to_bol'] = 'Sync Bol';
        return $defaults;
    }


    // Add rows into product variant page
    function variation_settings_fields( $loop, $variation_data, $variation ) {
        $shop2api_sync_to_bol = get_post_meta($variation->ID, 'shop2api_sync_to_bol', true);
        echo wp_kses('<div class="inline-edit-group">', $this->allowed_html);
        woocommerce_wp_checkbox(
            array(
                'id' => 'shop2api_sync_to_bol',
                'wrapper_class' => 'checkbox_class',
                'label' => '',
                'description' => __('If checked it will sync product to Bol.com', 'woocommerce'),
                'value' => $shop2api_sync_to_bol ? $shop2api_sync_to_bol : 'no'
            )
        );
        echo wp_kses('</div>', $this->allowed_html);

        $shop2api_koopblok_service = get_post_meta( $variation->ID, 'shop2api_koopblok_service', true);
        echo wp_kses('<div class="inline-edit-group">', $this->allowed_html);
        woocommerce_wp_checkbox(
            array(
                'id'            => 'shop2api_koopblok_service',
                'wrapper_class' => 'checkbox_class',
                'label'         => '',
                'description'   => __( 'If checked it will enable koopblok service for this product.', 'woocommerce' ),
                'value'         => $shop2api_koopblok_service ? $shop2api_koopblok_service : 'no'
            )
        );
        echo wp_kses('</div>', $this->allowed_html);

        // Add EAN Number
        $shop2api_ean_number = get_post_meta( $variation->ID, 'shop2api_ean_number', true);
        woocommerce_wp_text_input(
            array(
                'id'            => 'shop2api_ean_number',
                'wrapper_class' => 'cfwc-custom-field',
                'label'         => __('Bol EAN Number', 'woocommerce'),
                //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                'value'         => $shop2api_ean_number ? $shop2api_ean_number : ''
            )
        );

        // Combi EAN Number
        $shop2api_combi_ean_number = get_post_meta( $variation->ID, 'shop2api_combi_ean_number', true);
        woocommerce_wp_text_input(
            array(
                'id'            => 'shop2api_combi_ean_number',
                'wrapper_class' => 'cfwc-custom-field',
                'label'         => __('Combideal EAN Number', 'woocommerce'),
                //'description'   => __( 'An optional EAN Number that you can map on Shop-2-api or use for orders.', 'woocommerce' ),
                'value'         => $shop2api_combi_ean_number ? $shop2api_combi_ean_number : ''
            )
        );
    }

    //Add rows value into Product Page
    function add_columns_into_product_list_action($column, $post_id)
    {
        switch ($column) {
            case 'shop2api_sync_to_bol':
                $shop2api_sync_to_bol = get_post_meta($post_id, 'shop2api_sync_to_bol', true);
                echo wp_kses('<div id="shop2api_sync_to_bol_saved_inline_'.$post_id.'">', $this->allowed_html);
                echo wp_kses('<div id="shop2api_sync_to_bol_saved_val">', $this->allowed_html);
                echo wp_kses($shop2api_sync_to_bol ? $shop2api_sync_to_bol : 'no', $this->allowed_html);
                echo wp_kses('</div>', $this->allowed_html);
                echo wp_kses('</div>', $this->allowed_html);
                break;
        }
    }

    // Make these columns sortable
    function sortable_columns(): array
    {
        return array(
            'shop2api_sync_to_bol' => 'shop2api_sync_to_bol'
        );
    }

    function event_column_orderby($query)
    {
        if (!is_admin()) return;

        $orderby = $query->get('orderby');
        if ('product_checkbox' == $orderby) {
            $query->set('meta_key', 'shop2api_sync_to_bol');
            $query->set('orderby', 'meta_value');
        }
    }

    //Add Custom API End-Point


    function register_meta_field_search_endpoint(): void
    {
        register_rest_route( 'shop-2-api/v1', '/search-meta', array(
            'methods'  => 'GET',
            'callback' => array($this, 'meta_field_search_callback'),
            'permission_callback' => '__return_true',
        ) );
    }

    function meta_field_search_callback( $request ) {
        $meta_key   = $request->get_param( 'meta_key' );
        $meta_value = $request->get_param( 'meta_value' );
        $args = array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => '='
                )
            )
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            $products = array();
            while ( $query->have_posts() ) {
                $query->the_post();
                $product = new WC_Product(get_the_ID());
                $products[] = array(
                    'id' => get_the_ID(),
                    'name' => get_the_title(),
                    'price' => $product->get_price(),
                    'meta_key' => $meta_key,
                    'meta_value' => $meta_value,
                );
            }
            return rest_ensure_response( $products );
        } else {
            return new WP_Error( 'no_products_found', 'No products found.', array( 'status' => 402 ) );
        }
    }

    /**
     * @param $post_id
     * @return void
     */
    private function set_ean_numbers_per_post($post_id): void
    {
        $shop2api_ean_number = $_POST['shop2api_ean_number'] ?? '';
        if ($shop2api_ean_number == '') {
            delete_post_meta($post_id, 'shop2api_ean_number');
        } else {
            update_post_meta($post_id, 'shop2api_ean_number', $shop2api_ean_number);
        }

        $shop2api_combi_ean_number = $_POST['shop2api_combi_ean_number'] ?? '';
        if ($shop2api_combi_ean_number == '') {
            delete_post_meta($post_id, 'shop2api_combi_ean_number');
        } else {
            update_post_meta($post_id, 'shop2api_combi_ean_number', $shop2api_combi_ean_number);
        }
    }
}

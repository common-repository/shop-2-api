<?php

/**
 *
 * @package Shop2API
 *
 * This is the function to enqueue the JS and CSS files for shop2api
 *
 */

class Shop2Api_Enqueue
{
    public function version_id(): string
    {
        if (WP_DEBUG)
            return time();
        return VERSION;
    }

    public function register(): void
    {
        //Add filter so that query will return products and variations
        add_filter("woocommerce_rest_product_object_query",
            function (array $args){ $args["post_type"] = array( 'product', 'product_variation' ); return $args; },
            10, 1
        );

        add_action('admin_notices', array($this, 'get_server_user_message'));

        add_action('admin_enqueue_scripts', array($this, 'enqueue_items'));
        // Add events for Tags and Categories
        add_action('create_term', array($this, 'fire_update_events'), 10, 3);
        add_action('edited_term', array($this, 'fire_update_events'), 10, 3);
        add_action('delete_term', array($this, 'fire_update_events'), 10, 3);
        // Add events for Attributes
        add_action('woocommerce_attribute_added', array($this, 'fire_update_events_attr_added'), 10, 2);
        add_action('woocommerce_attribute_updated', array($this, 'fire_update_events_attr'), 10, 3);
        add_action('woocommerce_attribute_deleted', array($this, 'fire_update_events'), 10, 3);
        // Add events for product update
        add_action('woocommerce_update_product', array($this, 'fire_sync_event'), 10, 2);
        add_action('woocommerce_update_product_variation', array($this, 'fire_sync_event'), 10, 2);
        // Order Status Completed.
        add_action( 'woocommerce_order_status_completed',  array($this, 'action_woocommerce_order_status_completed'), 10, 1 );
    }

    // Update WooCommerce on Order Status completed
    function action_woocommerce_order_status_completed( $order_id ): void
    {
        // The WC_Order instance Object
        $order = wc_get_order( $order_id );

        if ( is_a( $order, 'WC_Order' ) ) {
            // Loop through order items
            foreach ( $order->get_items() as $key => $item ) {
                // The WC_Product instance Object
                $product = $item->get_product();
                if (isset($_POST['shop2api_sync_called'])) {
                    //Prevent running the action twice
                    return;
                }

                $_POST['shop2api_sync_called'] = true;
                require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
                $shop_2_api_connection = new Shop2ApiConnect();
                $shop_2_api_connection->sync_woocommerce_to_bol_with_id($product->get_id());
                $shop_2_api_connection->update_metadata_cache();
            }
        }
    }

    /**
     * This is the function to update the cache on Shop2api
     * @param int $term_id Term ID
     * @param int $tt_id Taxonomy ID
     * @param string $taxonomy Taxonomy Slug
     */
    public function fire_update_events($term_id, $tt_id, $taxonomy): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        if ($taxonomy == 'product_tag') {
            $shop_2_api_connection->update_tag_cache();
        }
        if ($taxonomy == 'product_cat') {
            $shop_2_api_connection->update_cat_cache();
        }
    }

    public function fire_update_events_attr($id, $data, $old_slug): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_connection->update_attr_cache();
    }

    public function fire_update_events_attr_added($id, $data): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_connection->update_attr_cache();
    }

    function start_sync_update_notice(): string
    {
        $ret_url = wp_kses('<div>', $this->allowed_html);
        $ret_url .= wp_kses(__('Sync Started on Shop 2 Api', 'shop2api'), $this->allowed_html);
        $ret_url .= wp_kses('</div>', $this->allowed_html);
        return $ret_url;
    }

    public function fire_sync_event($product_id, $product): void
    {
        if (isset($_POST['shop2api_sync_called'])) {
            //Prevent running the action twice
            return;
        }

        $_POST['shop2api_sync_called'] = true;
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_connection->sync_woocommerce_to_bol_with_id($product_id);
        $shop_2_api_connection->update_metadata_cache();
    }

    function get_server_user_message(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $shop_2_api_connection = new Shop2ApiConnect();

        $allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

        $response = $shop_2_api_connection->get_user_message();
        if (!is_wp_error($response))
        {
            $message = json_decode(wp_remote_retrieve_body($response), true);
            if ($message != '') {
                echo wp_kses('<div class="notice notice-warning is-dismissible" id="shop2api-user-message"><p>' . $message . '</p></div>', $allowed_html);
            }
        }
    }

    public function enqueue_items($hook): void
    {
        $this->add_wc_style_scripts();
        $this->add_common_styles_scripts();

        if ($hook == 'toplevel_page_shop2api_plugin') {
            $this->add_dashboard_localize();
            wp_enqueue_script('shop2api_google_chart', 'https://www.gstatic.com/charts/loader.js', [], $this->version_id());
            wp_enqueue_script('shop2api_dashboard', SHOP2API_PLUGIN_URL . '/assets/bol2api_dashboard.js', [], $this->version_id());
            wp_enqueue_script('jquery-modal-min', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.js', [], $this->version_id());
            wp_enqueue_style('jquery-modal-min-css', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.css', [], $this->version_id());
        } elseif ($hook == 'shop-2-api_page_shop2api_woocommerce_category') {
            $this->add_other_localize();
            wp_enqueue_script('bol2api_scripts', SHOP2API_PLUGIN_URL . '/assets/bol2api_category_mapping_scripts.js', [], $this->version_id());
        } elseif ($hook == 'shop-2-api_page_shop2api_bol_mapping') {
            $this->add_mapping_localize();
            wp_enqueue_script('bol2api_scripts', SHOP2API_PLUGIN_URL . '/assets/bol2api_mapping_scripts.js', [], $this->version_id());
        } elseif ($hook == 'shop-2-api_page_shop2api_wc_to_bol_reports') {
            $this->add_other_localize();
            wp_enqueue_script('jquery-modal-min', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.js', [], $this->version_id());
            wp_enqueue_style('jquery-modal-min-css', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.css', [], $this->version_id());
            wp_enqueue_script('bol2api_scripts', SHOP2API_PLUGIN_URL . '/assets/bol2api_report_scripts.js', [], $this->version_id());
        } elseif ($hook == 'admin_page_shop2api_wc_to_bol_reports_detail') {
            $this->add_other_localize();
            wp_enqueue_script('jquery-modal-min', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.js', [], $this->version_id());
            wp_enqueue_style('jquery-modal-min-css', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.css', [], $this->version_id());
            wp_enqueue_script('bol2api_scripts', SHOP2API_PLUGIN_URL . '/assets/bol2api_report_scripts.js', [], $this->version_id());
        } elseif ($hook == 'shop-2-api_page_shop2api_wc_to_bol_reports_order_page') {
            $this->add_other_localize();
            wp_enqueue_script('jquery-modal-min', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.js', [], $this->version_id());
            wp_enqueue_style('jquery-modal-min-css', SHOP2API_PLUGIN_URL . '/assets/external/jquery/jquery.modal.min.css', [], $this->version_id());
            wp_enqueue_script('bol2api_scripts', SHOP2API_PLUGIN_URL . '/assets/bol2api_order_report_scripts.js', [], $this->version_id());
        } elseif ($hook == 'shop-2-api_page_shop2api_bol_koopblok_service') {
            $this->add_koopblock_localize();
            wp_enqueue_script('shop2api_koopblock', SHOP2API_PLUGIN_URL . '/assets/shop2api_koopblock.js', [], $this->version_id());
            wp_enqueue_script(
                'bol2api_jquery-mask-min',
                SHOP2API_PLUGIN_URL . '/assets/external/jquery_mask/jquery.mask.min.js', array('jquery'),
                [], $this->version_id()
            );
        } elseif ($hook == 'shop-2-api_page_shop2api_bol_order_service') {
            $this->add_orders_localize();
            wp_enqueue_script('shop2api_koopblock', SHOP2API_PLUGIN_URL . '/assets/shop2api_order.js', [], $this->version_id());
            wp_enqueue_script(
                'bol2api_jquery-mask-min',
                SHOP2API_PLUGIN_URL . '/assets/external/jquery_mask/jquery.mask.min.js', array('jquery'),
                [], $this->version_id()
            );
        } elseif ($hook == 'shop-2-api_page_shop2api_bol_to_wc_product') {
            $this->add_bol_to_wc_localize();
            wp_enqueue_script('shop2api_bol_to_wc_product', SHOP2API_PLUGIN_URL . '/assets/bol2api_bol_to_wc_product_scripts.js', [], $this->version_id());
        }
    }

    private function add_wc_style_scripts(): void
    {
        wp_enqueue_style('bol2api_styles_wc', SHOP2API_PLUGIN_URL . '/assets/bol2api_styles_wc.css', [], $this->version_id());
    }

    private function add_common_styles_scripts(): void
    {
        wp_enqueue_style('google-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined', [], $this->version_id());
        wp_enqueue_style('bol2api_styles', SHOP2API_PLUGIN_URL . '/assets/bol2api_styles.css', [], $this->version_id());
        wp_enqueue_script('bol2api_scripts_common', SHOP2API_PLUGIN_URL . '/assets/bol2api_common_scripts.js', [], $this->version_id());
    }

    private function add_dashboard_localize(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiWooCommerce.php';

        $shop_2_api_connection = new Shop2ApiConnect();
        $connection_succeeded = $shop_2_api_connection->check_connection();

        $map_data = $this->handle_api_response($shop_2_api_connection->get_bol_wc_mapping_detail_info());
        $bol_information = $this->handle_api_response($shop_2_api_connection->get_bol_connection_info());
        $registration_email = get_option('shop2api_email');

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'connected' => $connection_succeeded,
            'wc_auth_url' => Shop2ApiWooCommerce::get_auth_url(),
            'bol_connected' => $shop_2_api_connection->check_bol_connection(),
            'wc_connected' => $shop_2_api_connection->check_wc_connection(),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'mapped_rows' => $map_data,
            'bol_info' => $bol_information,
            'shop2api_email' => $registration_email
        ));
    }

    private function add_other_localize(): void
    {
        $shop_2_api_connection = new Shop2ApiConnect();
        $map_data = $this->handle_api_response($shop_2_api_connection->get_bol_wc_mapping_detail_info());

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'map_data' => $map_data,
            'map_category' => get_option('woocommerce_category_field', 'main_categories'),
        ));
    }

    private function add_mapping_localize(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $shop_2_api_connection = new Shop2ApiConnect();
        // Get Field Options
        $wc_field_options = $this->handle_api_response($shop_2_api_connection->get_wc_field_options());
        //Get Product Data
        $offer_data = $this->handle_api_response($shop_2_api_connection->get_bol_offer_info());
        $map_data = $this->handle_api_response($shop_2_api_connection->get_bol_wc_mapping_detail_info());

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'wc_field_options' => $wc_field_options,
            'offer_data' => $offer_data,
            'map_data' => $map_data,
        ));
    }

    private static function handle_api_response($shop_2_api_response)
    {
        if (in_array(wp_remote_retrieve_response_code($shop_2_api_response), [200, 201])) {
            return json_decode(wp_remote_retrieve_body($shop_2_api_response), true);
        }

        return [];
    }

    private function add_orders_localize(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $shop_2_api_connection = new Shop2ApiConnect();
        // Get Field Options
        $wc_field_options = $this->handle_api_response($shop_2_api_connection->get_wc_field_options());
        //Get Product Data
        $offer_data = $this->handle_api_response($shop_2_api_connection->get_bol_offer_info());
        $orders_data = $this->handle_api_response($shop_2_api_connection->get_order_data());
        // Get Dropdown Values
        $wc_metadata_dropdown_values = $this->handle_api_response($shop_2_api_connection->get_wc_product_values('meta_data'));
        $wc_attributes_dropdown_values = $this->handle_api_response($shop_2_api_connection->get_wc_product_values('attributes'));

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'wc_field_options' => $wc_field_options,
            'offer_data' => $offer_data,
            'orders_data' => $orders_data,
            'wc_metadata_dropdown_values' => $wc_metadata_dropdown_values,
            'wc_attributes_dropdown_values' => $wc_attributes_dropdown_values
        ));
    }

    private function add_koopblock_localize(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $shop_2_api_connection = new Shop2ApiConnect();
        // Get Field Options
        $wc_field_options = $this->handle_api_response($shop_2_api_connection->get_wc_field_options());
        $wc_metadata_dropdown_values = $this->handle_api_response($shop_2_api_connection->get_wc_product_values('meta_data'));
        //Get Product Data
        $offer_data = $this->handle_api_response($shop_2_api_connection->get_bol_offer_info());
        $koopblok_data = $this->handle_api_response($shop_2_api_connection->get_koopblok_data());

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'wc_field_options' => $wc_field_options,
            'offer_data' => $offer_data,
            'koopblok_data' => $koopblok_data,
            'wc_metadata_dropdown_values' => $wc_metadata_dropdown_values,
        ));
    }

    private function add_bol_to_wc_localize(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';

        $shop_2_api_connection = new Shop2ApiConnect();
        // Get Field Options
        $wc_field_options = $this->handle_api_response($shop_2_api_connection->get_wc_field_options());
        //Get Product Data
        $offer_data = $this->handle_api_response($shop_2_api_connection->get_bol_offer_info());
        $orders_data = $this->handle_api_response($shop_2_api_connection->get_order_data());
        // Get Dropdown Values
        $wc_metadata_dropdown_values = $this->handle_api_response($shop_2_api_connection->get_wc_product_values('meta_data'));
        $wc_attributes_dropdown_values = $this->handle_api_response($shop_2_api_connection->get_wc_product_values('attributes'));

        wp_localize_script('bol2api_scripts_common', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'wc_field_options' => $wc_field_options,
            'offer_data' => $offer_data,
            'orders_data' => $orders_data,
            'wc_metadata_dropdown_values' => $wc_metadata_dropdown_values,
            'wc_attributes_dropdown_values' => $wc_attributes_dropdown_values
        ));
    }
}

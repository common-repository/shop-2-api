<?php

/**
 * 
 * @package Shop2API
 *
 * This class is all the AJAX calls triggered from the front-end
 * 
 */


class Shop2Api_AjaxButtonActions 
{
	public $shop_2_api_connections;
	function __construct() 
	{
		require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
		$this->shop_2_api_connections = new Shop2ApiConnect();
	}

	public function register(): void
    {
		//Bol Api Information
		add_action( 'wp_ajax_get_shop_2_api_information_bol', array( $this, 'get_shop_2_api_information_bol' ) );
		add_action( 'wp_ajax_set_shop_2_api_information_bol', array( $this, 'set_shop_2_api_information_bol' ) );

		// WooCommerce Api Information
		add_action( 'wp_ajax_get_shop_2_api_information_wc', array( $this, 'get_shop_2_api_information_wc' ) );
		add_action( 'wp_ajax_set_shop_2_api_information_wc', array( $this, 'set_shop_2_api_information_wc' ) );

		// Bol to WooCommerce Offer Mapping
		add_action( 'wp_ajax_set_shop_2_api_bol_wc_offer_mapping', array( $this, 'set_shop_2_api_bol_wc_offer_mapping' ) );

        // Bol to WooCommerce Offer Mapping
        add_action( 'wp_ajax_set_shop_2_api_wc_to_bol_submit', array( $this, 'set_shop_2_api_wc_to_bol_submit' ) );

		// Get all the values to be mapped
		add_action ('wp_ajax_get_woocommerce_values', array($this, 'get_woocommerce_values'));

		// Get all the bol category info
		add_action ('wp_ajax_get_bol_category_info', array($this, 'get_bol_category_info'));
		// Get all the bol offer info
		add_action ('wp_ajax_get_bol_offer_info', array($this, 'get_bol_offer_info'));
		// Get all the connection report
		add_action ('wp_ajax_get_bol_connection_report', array($this, 'get_bol_connection_report'));		

		// Get the saved bol category info
		add_action ('wp_ajax_get_bol_wc_mapping_info', array($this, 'get_bol_wc_mapping_info'));
		// Get bol category info by value
		add_action ('wp_ajax_get_bol_category_info_by_value', array($this, 'get_bol_category_info_by_value'));

		// Sync Woocommerce to Bol (sync_woocommerce_to_bol)
		add_action('wp_ajax_sync_woocommerce_to_bol', array($this, 'sync_woocommerce_to_bol'));

        // Refresh WooCommerce Dropdowns
        add_action('wp_ajax_refresh_wc_dropdowns', array($this, 'refresh_wc_dropdowns'));

		// Register a free account on shop2api
		add_action('wp_ajax_register_shop_2_api_free_account', array($this, 'register_shop_2_api_free_account'));

		//Koopblock Get and Submit functions
		add_action('wp_ajax_get_koopblok_data', array($this, 'get_koopblok_data'));
		add_action('wp_ajax_set_koopblok_data', array($this, 'set_koopblok_data'));

        //Koopblock Get and Submit functions
        add_action('wp_ajax_get_order_data', array($this, 'get_order_data'));
        add_action('wp_ajax_set_order_data', array($this, 'set_order_data'));

        // Remove Ean From Bol
        add_action('wp_ajax_set_shop_2_api_remove_ean_from_bol', array($this, 'remove_ean_from_bol'));

        // Start/Stop the sync
        add_action('wp_ajax_start_sync_for_wc_id', array($this, 'start_stop_sync_for_wc_id'));

        // Summary WC Data
        add_action('wp_ajax_get_wc_summary_report_offer', array($this, 'get_wc_summary_report_offer'));
        add_action('wp_ajax_get_wc_summary_report_product', array($this, 'get_wc_summary_report_product'));
        add_action('wp_ajax_get_wc_summary_report_order', array($this, 'get_wc_summary_report_order'));
        add_action('wp_ajax_get_wc_detail_report_order', array($this, 'get_wc_order_report_data'));

        // Order Actions
        add_action('wp_ajax_set_wc_order_status', array($this, 'set_wc_order_status'));

        // Bol to WC Products
        add_action( 'wp_ajax_get_shop_2_api_bol_product_data', array( $this, 'get_shop_2_api_bol_product_data' ) );
	}

	private function check_nonce(): void
    {
		// check the nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) 
		{
			die ( 'INVALID NONCE!');
		}
	}

    public function get_wc_summary_report_offer(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_response = $shop_2_api_connection -> get_wc_product_data_report_offer();
        $this->handle_api_response($shop_2_api_response);
    }

    public function get_wc_summary_report_product(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_response = $shop_2_api_connection -> get_wc_product_data_report_product();
        $this->handle_api_response($shop_2_api_response);
    }

    public function get_wc_summary_report_order(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_response = $shop_2_api_connection -> get_wc_product_data_order_summary();
        $this->handle_api_response($shop_2_api_response);
    }

    public function get_wc_order_report_data(): void
    {
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_response = $shop_2_api_connection -> get_wc_product_data_report_order();
        $this->handle_api_response($shop_2_api_response);
    }

    public function set_wc_order_status(): void
    {
        $new_status = $_POST['status'];
        $order_reference = $_POST['reference'];

        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_response = $shop_2_api_connection -> set_wc_order_to_new_status($order_reference, $new_status);
        $this->handle_api_response($shop_2_api_response);
    }

    public function get_shop_2_api_bol_product_data(): void
    {
        $this->check_nonce();
        $shop_2_api_response = $this->shop_2_api_connections->get_bol_product_data($_POST['ean_number']);
        $this->handle_api_response($shop_2_api_response);
    }

    public function start_stop_sync_for_wc_id(): void
    {
        $this->check_nonce();
        $product_id = $_POST['wc_id'];
        $sync_to_bol = get_post_meta( $product_id, 'shop2api_sync_to_bol', true );
        if ($sync_to_bol == 'no' or  !($sync_to_bol))
        {
            update_post_meta($product_id, 'shop2api_sync_to_bol', 'yes');
            require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
            $shop_2_api_connection = new Shop2ApiConnect();
            $shop_2_api_connection -> sync_woocommerce_to_bol_with_id($product_id);
            $shop_2_api_connection -> update_metadata_cache();
        } else {
            update_post_meta($product_id, 'shop2api_sync_to_bol', 'no');
        }
        $time = current_time('mysql');

        wp_update_post(
            array (
                'ID'            => $product_id,
                'post_date'     => $time,
                'post_date_gmt' => get_gmt_from_date( $time )
            )
        );

        $sync_to_bol = get_post_meta( $product_id, 'shop2api_sync_to_bol', true );
        wp_send_json_success($sync_to_bol);
    }

    public function remove_ean_from_bol(): void
    {
        $this->check_nonce();
        $shop_2_api_response = $this->shop_2_api_connections->remove_offer_from_bol($_POST['woocommerce_id']);
        $this->handle_api_response($shop_2_api_response);
    }

	public function set_shop_2_api_information_bol(): void
    {
		$this->check_nonce();

		// Set Bol Information
		$shop_2_api_response = $this->shop_2_api_connections->set_bol_connection_info
		(
			$_POST['client_id'], $_POST['client_secret']
		);

		$this->handle_api_response($shop_2_api_response);
	}

	public function set_shop_2_api_information_wc(): void
    {
		$this->check_nonce();

		// Set Bol Information
		$shop_2_api_response = $this->shop_2_api_connections->set_wc_connection_info
		(
			$_POST['client_key'], $_POST['client_secret']
		);

		$this->handle_api_response($shop_2_api_response);
	}

	public function get_shop_2_api_information_bol(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_bol_connection_info();
		$this->handle_api_response($shop_2_api_response);
	}

	public function get_shop_2_api_information_wc(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_wc_connection_info();
		$this->handle_api_response($shop_2_api_response);
	}

	public function set_shop_2_api_bol_wc_offer_mapping(): void
    {
		$this->check_nonce();

		$shop_2_api_response = $this->shop_2_api_connections->set_bol_wc_mapping
		(
			$_POST['woocommerce_category_field'], json_encode($_POST['map_data'])
		);
		$this->handle_api_response($shop_2_api_response);
	}

    public function set_shop_2_api_wc_to_bol_submit(): void
    {
        $this->check_nonce();

        $shop_2_api_response = $this->shop_2_api_connections->set_wc_to_bol_submit
        (
            $_POST['bol_category_field'], json_encode($_POST['map_data'])
        );
        $this->handle_api_response($shop_2_api_response);
    }

	public function get_woocommerce_values(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_wc_product_values($_POST['value']);
		$this->handle_api_response($shop_2_api_response);
	}

	public function get_bol_offer_info(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_bol_offer_info();
		$this->handle_api_response($shop_2_api_response);
	}

	public function get_bol_category_info(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_bol_category_info();
		$this->handle_api_response($shop_2_api_response);
	}

	public function get_bol_category_info_by_value(): void
    {
		$this->check_nonce();

		$shop_2_api_response = $this->shop_2_api_connections->get_bol_category_info_by_value($_POST['value']);
		$this->handle_api_response($shop_2_api_response);
	}

	public function register_shop_2_api_free_account(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->register_shop_2_api_free_account($_POST['email']);

		if ( is_wp_error( $shop_2_api_response ) ) 
		{
			$return = 'Error: ' . $shop_2_api_response->get_error_message();
		} elseif (in_array(wp_remote_retrieve_response_code($shop_2_api_response), [200, 201])) 
		{
			// Set token if all good
			$json_response = json_decode(wp_remote_retrieve_body( $shop_2_api_response ), true);
			update_option('shop2api_token', $json_response['token']);
			wp_send_json_success($shop_2_api_response);
		} else {
			$return = wp_remote_retrieve_body( $shop_2_api_response );
		}		
		wp_send_json_error($return);
	}

	public function get_bol_wc_mapping_info(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_bol_wc_mapping_info();
		$this->handle_api_response($shop_2_api_response);
	}

	public function sync_woocommerce_to_bol(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->sync_woocommerce_to_bol();
		$this->handle_api_response($shop_2_api_response);		
	}

    public function refresh_wc_dropdowns(): void
    {
        $shop_2_api_response = $this->shop_2_api_connections->refresh_wc_dropdowns();
        $this->handle_api_response($shop_2_api_response);
    }

	public function get_bol_connection_report(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_bol_connection_report();
		$this->handle_api_response($shop_2_api_response);		
	}

	public function get_koopblok_data(): void
    {
		$shop_2_api_response = $this->shop_2_api_connections->get_koopblok_data();
		$this->handle_api_response($shop_2_api_response);
	}

	public function set_koopblok_data(): void
    {
		$this->check_nonce();
		$shop_2_api_response = $this->shop_2_api_connections->set_koopblok_data(
			$_POST['koopblok-active'],
			$_POST['wc-price-field'],
			$_POST['koopblok-price-increments'],
			$_POST['koopblok-price-increment-days'],
			$_POST['koopblok-price-limit'],
			$_POST['koopblok-update-wc'],
			$_POST['koopblok-seller-id'],
			$_POST['orders-ean-value'],
			$_POST['koopblok-price-limit-max'],
			$_POST['orders-ean'],
		);

		$this->handle_api_response($shop_2_api_response);
	}

    public function get_order_data(): void
    {
        $shop_2_api_response = $this->shop_2_api_connections->get_order_data();
        $this->handle_api_response($shop_2_api_response);
    }

    public function set_order_data(): void
    {
        $this->check_nonce();
        $shop_2_api_response = $this->shop_2_api_connections->set_order_data(
            $_POST['orders-active'], $_POST['orders-status'], $_POST['orders-paid'], $_POST['orders-ean-value'],
            $_POST['orders-ean'], $_POST['orders-combideals-active'], $_POST['orders-combideal-ean-value'],
            $_POST['orders-combideal-ean'], $_POST['orders-email'], $_POST['orders-use-bol-price'],
            $_POST['orders-stock-sync'], $_POST['orders-alert-on-order-fail'], $_POST['orders-alert-email'],
            $_POST['orders-bol-price-include-tax']
        );

        $this->handle_api_response($shop_2_api_response);
    }

    public function get_user_message(): void
    {
        $shop_2_api_response = $this->shop_2_api_connections->get_user_message();
        $this->handle_api_response($shop_2_api_response);
    }

	private static function handle_api_response($shop_2_api_response): void
    {
        $return = 'Error Reaching the Server';
		if ( is_wp_error( $shop_2_api_response ) ) 
		{
			$return = 'Error: ' . $shop_2_api_response->get_error_message();
		} elseif (in_array(wp_remote_retrieve_response_code($shop_2_api_response), [200, 201])) 
		{
			wp_send_json_success($shop_2_api_response);
		} else {
			$return = wp_remote_retrieve_body( $shop_2_api_response );
		}		
		wp_send_json_error($return);
	}
}

<?php

/**
 * 
 * @package Shop2API
 *
 */

if ( ! class_exists( 'Admin' ) ) :
	class Admin 
	{
		protected array $allowed_html;

		function __construct() 
		{
		  require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
		  $this->allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();
		}

		public function register() 
		{
			require_once SHOP2API_PLUGIN_PATH . '/includes/Api/DashboardFields.php';
			$dashboard_fields = new Shop2Api_DashboardFields();

			add_action('admin_menu', array($this, 'add_admin_pages'));
			add_action('admin_init', [$dashboard_fields, 'register_custom_fields']);
		}

        public function mp_add_product_screen_options()
        {
            $options = 'custom_data_per_page';

            $args = array(
                'label' => 'custom data Per Page',
                'default' => 20,
                'option' =>'data_per_page'
            );
            add_screen_option($options, $args);
        }

		public function add_admin_pages() 
		{
			add_menu_page(
				'Shop 2 API', // Page Title
				'Shop 2 API',  // Menu Title
				'manage_options',  // Capability
				'shop2api_plugin',  // Slug
				$function = array($this, 'dashboard_page'), 
				$icon_url = 'dashicons-store', 
				$position = 30 
			);

			// Check if the connection is ok before adding other menu items
			require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
			$shop_2_api_connection = new Shop2ApiConnect();

			// Check Main Connection
			$connection_succeeded = get_option('shop2api_connection_succeeded');
			if (!$connection_succeeded) {
				$connection_succeeded = $shop_2_api_connection->check_connection();
			}

			// Check boll connection data
			$bol_connected = get_option('shop2api_bol_connection_succeeded');
			if (!$bol_connected) {
				$bol_connected = $shop_2_api_connection->check_bol_connection();
			}
			// Check wc connection data
			$wc_connected = get_option('shop2api_wc_connection_succeeded');
			if (!$wc_connected) {
				$wc_connected = $shop_2_api_connection->check_wc_connection();
			}
			

			// If above checks out you can show the connection
			if ($connection_succeeded && $bol_connected && $wc_connected) 
			{
				// Add sub menus for the plugin
				add_submenu_page( 
					'shop2api_plugin', //Parent Slug
					'Map Bol Category', // Page Title
					'Map Bol Category', // Menu Title
					'manage_options', // Capability 
					'shop2api_woocommerce_category', // Menu Slug
					array($this, 'bol_2_wc_product_settings'), // Function
				);

				add_submenu_page(
					'shop2api_plugin', //Parent Slug
					'Map WooCommerce to Bol', // Page Title
					'Map WooCommerce to Bol', // Menu Title
					'manage_options', // Capability 
					'shop2api_bol_mapping', // Menu Slug
					array($this, 'wc_to_bol_mapping_page') // Function
				);

				add_submenu_page(
					'shop2api_plugin', //Parent Slug
					'Repricer Service', // Page Title
					'Repricer Service', // Menu Title
					'manage_options', // Capability 
					'shop2api_bol_koopblok_service', // Menu Slug
					array($this, 'bol_koopblok_service') // Function
				);

                add_submenu_page(
                    'shop2api_plugin', //Parent Slug
                    'Bol Order Sync Service', // Page Title
                    'Bol Order Sync Service', // Menu Title
                    'manage_options', // Capability
                    'shop2api_bol_order_service', // Menu Slug
                    array($this, 'bol_order_service') // Function
                );

//                add_submenu_page(
//                    'shop2api_plugin', //Parent Slug
//                    'Sync Bol Product to WooCommerce', // Page Title
//                    'Sync Bol Product to WooCommerce', // Menu Title
//                    'manage_options', // Capability
//                    'shop2api_bol_to_wc_product', // Menu Slug
//                    array($this, 'bol_to_wc_product_sync_page') // Function
//                );

				$hook = add_submenu_page(
					'shop2api_plugin', //Parent Slug
					'Sync Report', // Page Title
					'Sync Reports', // Menu Title
					'manage_options', // Capability 
					'shop2api_wc_to_bol_reports', // Menu Slug
					array($this, 'wc_to_bol_reports_page'), // Function
				);
                add_action("load-".$hook, array($this,'mp_add_product_screen_options'));

                $hook = add_submenu_page(
                    'shop2api_wc_to_bol_reports', //Parent Slug
                    'Sync Detail Report', // Page Title
                    'Sync Detail Report', // Menu Title
                    'manage_options', // Capability
                    'shop2api_wc_to_bol_reports_detail', // Menu Slug
                    array($this, 'wc_to_bol_reports_detail_page'), // Function
                );
                add_action("load-".$hook, array($this,'mp_add_product_screen_options'));

                $hook = add_submenu_page(
                    'shop2api_plugin', //Parent Slug
                    'Sync Order Report', // Page Title
                    'Sync Order Report', // Menu Title
                    'manage_options', // Capability
                    'shop2api_wc_to_bol_reports_order_page', // Menu Slug
                    array($this, 'wc_to_bol_reports_order_page'), // Function
                );
                add_action("load-".$hook, array($this,'mp_add_product_screen_options'));
			}
		}

		public function dashboard_page() 
		{
			require_once SHOP2API_PLUGIN_PATH . 'templates/dashboard.php';
		}

		public function wc_to_bol_mapping_page() 
		{
			require_once SHOP2API_PLUGIN_PATH . 'templates/wc-to-bol-mapping.php';
		}

        public function bol_to_wc_product_sync_page()
        {
            require_once SHOP2API_PLUGIN_PATH . 'templates/bol-to-wc-product-sync.php';
        }

        public function wc_to_bol_reports_detail_page()
        {
            require_once SHOP2API_PLUGIN_PATH . '/includes/Tables/Bol2WcStatusReportDetail.php';
            $ListTable = new Shop2Api_Bol2WcStatusReportDetail();

            echo wp_kses('<div class="wrap">', $this->allowed_html);
            // Common Header to be included everywhere and js should also be in the common.js
            $shop2api_header = "Bol to Woocommerce Detail Report";
            require_once SHOP2API_PLUGIN_PATH . 'templates/common-header.php';

            $ListTable->prepare_items();
            // Searching
            echo wp_kses('<form method="GET">', $this->allowed_html);
            echo wp_kses('<input type="hidden" name="page" value="shop2api_wc_to_bol_reports" />', $this->allowed_html);
            $ListTable->search_box('search', 'search_id');
            echo wp_kses('</form>', $this->allowed_html);
            $ListTable->display();
            echo wp_kses('</div>', $this->allowed_html);
        }

        public function wc_to_bol_reports_order_page()
        {
            require_once SHOP2API_PLUGIN_PATH . '/includes/Tables/Bol2WcOrderReport.php';
            $ListTable = new Shop2Api_Bol2WcOrderReport();

            echo wp_kses('<div class="wrap">', $this->allowed_html);
            // Common Header to be included everywhere and js should also be in the common.js
            $shop2api_header = "Bol to Woocommerce Order Report";
            require_once SHOP2API_PLUGIN_PATH . 'templates/common-header.php';

            $ListTable->prepare_items();
            // Searching
            echo wp_kses('<form method="GET">', $this->allowed_html);
            echo wp_kses('<input type="hidden" name="page" value="shop2api_wc_to_bol_reports_order_page" />', $this->allowed_html);
            $ListTable->search_box('search', 'search_id');
            echo wp_kses('</form>', $this->allowed_html);
            $ListTable->display();
            echo wp_kses('</div>', $this->allowed_html);
        }

		public function wc_to_bol_reports_page() 
		{
			require_once SHOP2API_PLUGIN_PATH . '/includes/Tables/Bol2WcStatusReport.php';
			$ListTable = new Shop2Api_Bol2WcStatusReport();

            echo wp_kses('<div class="wrap">', $this->allowed_html);
            // Common Header to be included everywhere and js should also be in the common.js
            $shop2api_header = "Bol to Woocommerce Sync Report";
            require_once SHOP2API_PLUGIN_PATH . 'templates/common-header.php';

			$ListTable->prepare_items(); 
			// Searching 
			echo wp_kses('<form method="GET">', $this->allowed_html);
			echo wp_kses('<input type="hidden" name="page" value="shop2api_wc_to_bol_reports" />', $this->allowed_html);
			$ListTable->search_box('search', 'search_id');
			echo wp_kses('</form>', $this->allowed_html);
			$ListTable->display();
            echo wp_kses('</div>', $this->allowed_html);
        }

		public function wc_category_selection_page(): void
        {
			require_once SHOP2API_PLUGIN_PATH . '/includes/Tables/WcCategorySelection.php';
			$ListTable = new Shop2Api_WcCategorySelection();
			echo wp_kses('<div class="wrap">', $this->allowed_html);
            echo wp_kses('<form method="post">', $this->allowed_html);
            // Common Header to be included everywhere and js should also be in the common.js
            $shop2api_header = "WooCommerce To Bol Category Mapping";
            $shop2api_header_detail = "To map your products to bol, we need to know which Category in WooCommerce will
            map to your category in Bol.com. </br>
            <b>For Example: You have a category in WooCommerce 'HOUT' which you can map to 'HOUTKACHEL' a category
            on bol.com</b> </br>
            Bol Commission and Bol Shipping Costs will be added to the price field which is mapped on the next step.
            <div class='disclaimer1'>* When you complete your mapping remember to save your changes.</div>";
            require_once SHOP2API_PLUGIN_PATH . 'templates/common-header.php';

			echo wp_kses('<div class="content" style="padding: 10px 20px">', $this->allowed_html);

			$ListTable->prepare_items();
			// Render Table
			$ListTable->display();
            echo wp_kses('<button id="category-save" class="shop-2-api-connect-save">Save</button>', $this->allowed_html);
            echo wp_kses('</form">', $this->allowed_html);
			echo wp_kses('</div>', $this->allowed_html);
			echo wp_kses('</div>', $this->allowed_html); 
		}

        public function bol_2_wc_product_settings(): void
        {
            require_once SHOP2API_PLUGIN_PATH . '/includes/Tables/Wc2BolProductSettings.php';
            $ListTable = new Shop2Api_Wc2BolProductSettings();
            echo wp_kses('<div class="wrap">', $this->allowed_html);
//            echo wp_kses('<form method="post">', $this->allowed_html);
            // Common Header to be included everywhere and js should also be in the common.js
            $shop2api_header = "WooCommerce To Bol Category Mapping";
            $shop2api_header_detail = "To map your products to bol, we need to know which Category in WooCommerce will
            map to your category in Bol.com. </br>
            <b>For Example: You have a category in WooCommerce 'HOUT' which you can map to 'HOUTKACHEL' a category
            on bol.com</b> </br>
            Bol Commission and Bol Shipping Costs will be added to the price field which is mapped on the next step.
            <div class='disclaimer1'>* When you complete your mapping remember to save your changes.</div>";
            require_once SHOP2API_PLUGIN_PATH . 'templates/common-header.php';

            echo wp_kses('<div class="content" style="padding: 10px 20px">', $this->allowed_html);

            $ListTable->prepare_items();
            // Render Table
            echo wp_kses('<form method="POST">', $this->allowed_html);
            $ListTable->search_box(__('Search'), 'search-box-id');
            echo wp_kses('</form>', $this->allowed_html);
            $ListTable->display();
            echo wp_kses('<button id="category-save" class="shop-2-api-connect-save">Save</button>', $this->allowed_html);
//            echo wp_kses('</form">', $this->allowed_html);
            echo wp_kses('</div>', $this->allowed_html);
            echo wp_kses('</div>', $this->allowed_html);
        }

		public function bol_koopblok_service()
		{
			require_once SHOP2API_PLUGIN_PATH . 'templates/bol-get-best-price.php';
		}

        public function bol_order_service()
        {
            require_once SHOP2API_PLUGIN_PATH . 'templates/bol-order-sync.php';
        }
	}
endif;

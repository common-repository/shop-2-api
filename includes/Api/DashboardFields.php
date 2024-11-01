<?php

/**
 * 
 * @package Shop2API
 * 
 * This is the main token popup.
 *
 */


class Shop2Api_DashboardFields
{
	public function register_custom_fields() 
	{
		register_setting( 
			$option_group = 'dashboard_group', $option_name = 'shop2api_token', $args = [$this, 'set_dashboard_group'] 
		);
		
		add_settings_section( 
			$id = 'dashboard_token_section', 
			$title = 'Enter token here which you have received from shop2api', 
			$callback = [$this, 'set_dashboard_section'],
			$page = 'shop2api_plugin'
		);

		add_settings_field( 
			$id = 'shop2api_token', // Must be the same as the group 
			$title = 'Shop2Api Token', 
			$callback = [$this, 'set_dashboard_field'], 
			$page = 'shop2api_plugin',
			$section = 'dashboard_token_section', 
			$args = [
				'label_for' => 'token'
			] );
	}

	public function set_dashboard_group($input) 
	{
		require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
		$shop_2_api_connection = new Shop2ApiConnect();
		$connection_succeeded = $shop_2_api_connection->check_connection($input);

		if ($connection_succeeded) 
		{
			add_settings_error( 'shop2api_token', 'shop2api_token_error', 'Connection to Shop 2 API Succeeded.', "success");
		} else {
			add_settings_error( 'shop2api_token', 'shop2api_token_error', 'Could not connect to Shop 2 API, please check your token.', "error");
		}
		return $input;
	}

	public function set_dashboard_section() 
	{
		return '';
	}
	
	public function set_dashboard_field() 
	{
		require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
		$allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();
		
		$value = esc_attr( get_option('shop2api_token') );
		echo wp_kses(
			'<input type"text" class="regular-text" name="shop2api_token" value="'.$value.'" placeholder="Your API Token">', $allowed_html
		);
	}
}

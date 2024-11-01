<?php 

/**
 * 
 * @package Shop2API
 * 
 * This gets called on activation of the plugin
 *
 */

Class Shop2Api_Activate
{
	public static function activate() 
	{
		flush_rewrite_rules();

		require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
		$shop_2_api_connection = new Shop2ApiConnect();
		$shop_2_api_connection -> update_tag_cache();
		$shop_2_api_connection -> update_cat_cache();
		$shop_2_api_connection -> update_attr_cache();
		$shop_2_api_connection -> update_metadata_cache();
        $shop_2_api_connection -> activate_deactivate_connection('True');
	}	
}

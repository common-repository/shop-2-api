<?php 

/**
 * 
 * @package Shop2API
 *
 * This is called on deactivation of the plugin
 */


Class Shop2Api_Deactivate
{
	public static function deactivate() 
	{
		flush_rewrite_rules();
        require_once SHOP2API_PLUGIN_PATH . '/includes/Api/Shop2ApiConnect.php';
        $shop_2_api_connection = new Shop2ApiConnect();
        $shop_2_api_connection->activate_deactivate_connection('False');
	}	
}

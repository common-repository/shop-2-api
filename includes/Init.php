<?php 
/**
 * 
 * @package Shop2API
 *
 */


final class Shop2Api_Init 
{
	/*
	This will just call register and init all the classes.
	@return Null
	*/
	public static function register_services() 
	{
		require_once plugin_dir_path( __FILE__ ). 'Pages/Admin.php';
		require_once plugin_dir_path( __FILE__ ). 'Base/Enqueue.php';
		require_once plugin_dir_path( __FILE__ ). 'Base/EnqueueWC.php';
		require_once plugin_dir_path( __FILE__ ). 'Base/EnqueueWCAPI.php';
		require_once plugin_dir_path( __FILE__ ). 'Base/SettingsLinks.php';
		require_once plugin_dir_path( __FILE__ ). 'Base/AjaxButtonActions.php';

		// Admin Pages
		$cls = new Admin();
		$cls->register();

		//CSS/JS Files
		$cls = new Shop2Api_Enqueue();
		$cls->register();

		//Add WooCommerce fields
		$cls = new Shop2Api_EnqueueWC();
		$cls->register();

        //Add WooCommerce API Custom Calls
        $cls = new Shop2Api_EnqueueWCAPI();
        $cls->register();

		// Settings Links
		$cls = new Shop2Api_SettingsLinks();
		$cls->register();

		// Ajax Button Links
		$cls = new Shop2Api_AjaxButtonActions();
		$cls->register();
	}
}

<?php

/**
 * 
 * @package Shop2API
 * 
 * Add settings links so that user can go to the settings page from the plugin page
 *
 */

 class Shop2Api_SettingsLinks {
 	public function register() {
 		add_filter("plugin_action_links_" . SHOP2API_PLUGIN, array($this, 'settings_link'));
 	}

	public function settings_link(array $links) {
		require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
		$allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

		array_push($links, wp_kses('<a href="admin.php?page=shop2api_plugin">Settings</a>', $allowed_html));
		return $links;
	}	
 }

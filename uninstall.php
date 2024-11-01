<?php

/**
 * This file will trigger on uninstall
 * 
 * @package Shop2API
 *
 */

 if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
 }

// Clear Database option
delete_option('shop2api_token');
delete_option('shop2api_connection_succeeded');
delete_option('shop2api_bol_connection_succeeded');
delete_option('shop2api_wc_connection_succeeded');
delete_option('shop2api_sync_to_bol');

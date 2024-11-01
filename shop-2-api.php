<?php

/*
Plugin Name: Shop 2 API
Plugin URI: https://wordpress.org/plugins/shop-2-api/
Description: The plugin Shop2Api will sync products between e-Commerce platforms. The current supported e-Commerce platforms are WooCommerce to Bol.com, and we are working on Amazon, Shopify and others.  We added a koopblok service so that you can check if you lower your price can you get koopblok.
Version: 1.0.31.1
Requires at least: 5.0
Requires PHP:      7.2
Author: Adriaan Coetzee
Author URI: https://shop2API.com/
License: GPLv2 or later
Text Domain: shop2API
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/


// Some security checks.
defined('ABSPATH') or die('Security Statement');

// Define some constants.
define('SHOP2API_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('SHOP2API_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('SHOP2API_PLUGIN', plugin_basename( __FILE__ ));
define ('VERSION', '1.0.31.1');

// Register items in the project.
require_once plugin_dir_path( __FILE__ ). 'includes/Init.php';
Shop2Api_Init::register_services();

//Activation
require_once plugin_dir_path( __FILE__ ). 'includes/Base/Activate.php';
register_activation_hook(__FILE__, array( 'Shop2Api_Activate', 'activate'));

//Deactivation
require_once plugin_dir_path( __FILE__ ). 'includes/Base/Deactivate.php';
register_deactivation_hook(__FILE__, array( 'Shop2Api_Deactivate', 'deactivate'));

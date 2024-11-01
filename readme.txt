=== Shop 2 API ===
Plugin Name: Shop 2 API
Plugin URI: https://wordpress.org/plugins/shop-2-api/
Tags: WooCommerce, Bol
Stable Tag: 1.0.30.0
Requires at least: 5.0
Requires PHP: 7.2
Tested up to: 6.4
Author: Adriaan Coetzee
Author URI: https://shop2API.com/
License: GPLv2 or later
Domain: shop2API
Contributors: ascendedcrow

=== Description ===
Shop2API is a service where you can connect your WooCommerce web-shop to Bol.com.

The main purpose of this plugin is to integrate Bol.com into WooCommerce and Bol.com into WooCommerce, it aims to
integrate as much as possible services between the platforms.

Current Services:
- Sync Offers to Bol.com
- Sync Products to Bol.com
- Sync Orders from Bol.com to WooCommerce
- Sync Stock from WooCommerce to Bol.com
- Re-pricer service

== Changelog ==
= 1.0.20 =
1) Added an EAN Number to the order sync page
2) Small WooCommerce API Changes

= 1.0.21 =
1) Implementation of Combo Deals

= 1.0.22 =
1) Fix bug where the EAN numbers gets overwritten on quick-save.

= 1.0.23 =
1) Added Bol Order Id in the Order List
2) Added Use Bol.com Price on the Order Service Screen (Use the price from Bol.com)
3) Added WooCommerce Order field to the Order Service Screen (Overwrite the E-Mail received from Bol.com)

= 1.0.24 =
1) Fixed a issue on multi-line order stock sync

= 1.0.25 =
1) Added Sync stock on the sync order service.
2) Removed items from Quick Edit

= 1.0.26 =
1) Added Sync Bol Product to WooCommerce page
2) Updated User Registration Process

= 1.0.27 =
1) Update Readme with new process and url update
2) Fixed bug with the custom WooCommerce Updates.
3) Updated dashboard.
4) Added hook for update product variations.
5) Fix bug which can cause a php error.

= 1.0.28 =
1) Added Order Id For Searching
2) Fix bug with category mapping
3) Dashboard Changes
4) Added Alert on Order Failing Option
5) Fix bug where product variants is not retrieved

= 1.0.29 =
1) Reworked Bol.com Product and Offer Mapping
2) Dropdown Fixes
3) Remove Ean number on main product
4) Fix issue on empty EAN Number
5) Fix issue on category search
6) Added `Subtract Vat From Bol.com Price` to the bol order sync page.

= 1.0.30 =
1) Update repricer server for updating.
2) Fix some Order screen bugs.

= 1.0.31 =
1) Added Sync Dropdown and Sync products Button.
2) Some user management improvements

== Instructions ==
1) After installing Shop2Api, a new menu item will be available in your WordPress installation, you can continue and click on it and enter your e-mail address.
2) You can use the trail account where start using Shop 2 API without restrictions for 6 months and cancel at anytime by uninstalling the plugin, the plugin will mark the account as inactive if no payment is done after 6 months.
2) After you clicked on the connect button the URL for your WooCommerce connection will be linked to your e-mail. If you need to unlink it, you can mail support@shop2api.com
3) If the connection with the red icon still needs to be connected to your web shop, clicking on the icons will open the connection screens.
4) You can click on the WooCommerce icon, and you will be rerouted to the WooCommerce permission screen as below.
5) When you click on the Bol Icon the above screen will open where you can enter your client id and secret.
6) Map Woocommerce Sections to Bol
WooCommerce have Categories, Tags, Attributes and Metadata, this is to group your data logically and will be used for groupings in Bol.

Here you would want to map which WooCommerce Grouping to Bol’s Grouping
Ex. Behang Tag on WC ⇒ Behang on Bol

7) Map Woocommerce Fields to Bol Fields
This is the field mappings you want to map to bol

== Frequently Asked Questions ==
https://shop2api.com/manual/faq.php

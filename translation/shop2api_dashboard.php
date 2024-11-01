<?php
require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
$allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();

$check_con_tooltip = wp_kses(__("Check that the connection to the Shop2Api server can be completed"), $allowed_html);
$no_token = wp_kses(__("If you have not bought a token yet, you can go to the shop and buy one."), $allowed_html);
$use_free = wp_kses(__("Use Free Account"), $allowed_html);
$register_free_account = wp_kses(__("Register for the free shop2api version."), $allowed_html);
$register_free_account_1 = wp_kses(__("Trial Account is for 6 Months."), $allowed_html);
$register_free_account_2 = wp_kses(__("Once connected the token will be linked to the url"), $allowed_html);
$register_free_account_3 = wp_kses(__("Please use a different e-mail for each shop."), $allowed_html);
$register_free_account_4 = wp_kses(__("If your trial expired click <a href='https://shop2api.com/products/checkout?add-to-cart=18' target='_blank'>here</a>"), $allowed_html);
$bol_connection_info_hdr = wp_kses(__("Bol Connection Information"), $allowed_html);
$bol_connection_info = wp_kses(__("You can find how to get/create your Bol Api information"), $allowed_html);
$bol_connection_client_id = wp_kses(__("Client Id"), $allowed_html);
$bol_connection_client_secret = wp_kses(__("Client Secret"), $allowed_html);
$check_conn = wp_kses(__("Check that the connection to the Shop2Api server can be completed."), $allowed_html);
$connect_wc = wp_kses(__("You can connect your WooCommerce by clicking on the icon below."), $allowed_html);
$connect_wc_info = wp_kses(__("Setup a connection with WooCommerce."), $allowed_html);
$connect_bol = wp_kses(__("You can click on the bol logo to connect you bol account for syncing."), $allowed_html);
$connect_bol_info = wp_kses(__("Enter Connection information you received from bol."), $allowed_html);
$no_use_data = wp_kses(__("* Shop-2-API does not Save or Use any Customer/Order data."), $allowed_html);
$reconnect = wp_kses(__("You can click here if you want to reconnect your connections."), $allowed_html);
$reconnect_link = wp_kses(__("(Reconnect)"), $allowed_html);
$click_here = wp_kses(__("(Click Here)"), $allowed_html);
$web_shop_connect_info = wp_kses(__("WebShop Connection Information"), $allowed_html);
$wc_to_bol_tooltip = wp_kses(__("Here you must map your WooCommerce Categories to Bol.com Categories."), $allowed_html);
$wc_to_bol_map_cat = wp_kses(__("Map your WooCommerce Category to Categories on Bol.com"), $allowed_html);
$wc_to_bol_tooltip_2 = wp_kses(__("Here you can choose WooCommerce or Default fields to send over to Bol.com."), $allowed_html);
$wc_to_bol_mapping = wp_kses(__("Map WooCommerce fields to Bol.com Fields"), $allowed_html);
$sync_summary_product = wp_kses(__("Sync Summary Offers"), $allowed_html);
$sync_summary_offer = wp_kses(__("Sync Summary Products"), $allowed_html);
$sync_summary_order = wp_kses(__("Sync Summary Orders"), $allowed_html);
$orders_synced = wp_kses(__("Orders Synced"), $allowed_html);
$offer_product_heading = wp_kses(__("Sync Products/Offers"), $allowed_html);
$order_card_heading = wp_kses(__("Sync Orders/Stock"), $allowed_html);

$order_card_enabled_tooltip = wp_kses(__("Link to the page to enable orders or stock"), $allowed_html);
$order_card_active = wp_kses(__("Sync Orders to Bol.com"), $allowed_html);

$order_card_combi_deal_tooltip = wp_kses(__("Combi Deals checked on stock update."), $allowed_html);
$order_card_combi_deal_active = wp_kses(__("Combi Deals Syncing Active"), $allowed_html);

$order_card_stock_sync_tooltip = wp_kses(__("Sync stock after order was synced,"), $allowed_html);
$order_card_stock_sync_active = wp_kses(__("Sync Stock After Order Synced"), $allowed_html);

//Support Buttons
$support_card_heading = wp_kses(__("Support"), $allowed_html);
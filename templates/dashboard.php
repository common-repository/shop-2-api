<div class="wrap">
    <!--  Common Header to be included everywhere and js should also be in the common.js  -->
    <?php require_once("common-header.php"); ?>
    <?php require_once SHOP2API_PLUGIN_PATH . '/translation/shop2api_dashboard.php'; ?>

    <!-- This is the register free account modal  -->
    <div id='register-free-account-modal'>
        <form>
            <table>
                <tr>
                    <td>
                        <label><b>E-Mail:</b></label>
                    </td>
                    <td>
                        <input type="email" name="shop2api-email" id="shop2api-email" style="width: 300px;" required />
                    </td>
                    <td>
                        <button href="#" class='btn' title="<?php echo $register_free_account; ?>"
                                id='register-free-account' value="">
                            <i class="dashicons dashicons-admin-plugins"></i></span>
                        </button>
                    </td>
            </table>
        </form>
        <div><b>- <?php echo $register_free_account_2; ?>: <?php echo(get_site_url()) ?></b></div>
        <div><b>- <?php echo $register_free_account_1; ?></b></div>
        <div><b>- <?php echo $register_free_account_3; ?></b></div>
        <div><b>- <?php echo $register_free_account_4; ?></b></div>
    </div>

    <!-- This is modal for bol connection -->
    <div id="bol-connect-modal">
        <div id='bol-connection-error' class='error-message'></div>
        <h2><?php echo $bol_connection_info_hdr; ?></h2>
        <p>
            <?php echo $bol_connection_info; ?>
            <a href="https://www.shop2api.com/bol_api_info.php"><?php echo(__("here")); ?></a>
        </p>

        <form method="post" action="options.php">
            <table>
                <tr>
                    <td>
                        <label><?php echo $bol_connection_client_id; ?></label>
                    </td>
                    <td>
                        <input type=text name="bol-client-id" id="bol-client-id" style="width: 300px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $bol_connection_client_secret; ?></label>
                    </td>
                    <td>
                        <input type=text name="bol-client-secret" id="bol-client-secret" style="width: 300px;"/>
                    </td>
                </tr>
            </table>
        </form>
        <br/>
        <button type='submit' class='btn' title="<?php echo $check_conn; ?>"
                id="save-bol-data">
            <i class="dashicons dashicons-admin-plugins"></i></span>
        </button>
    </div>

    <div class="content-holders-holder">
        <div class="content-holders">
            <div class="card inline" id='get-started-card'>
                <h1 style="font-weight: bold;">Webshop Connection Information</h1>
                <div class="container">
                    <table>
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <h2><?php echo $connect_wc; ?></h2>
                            </td>
                        </tr>
                        <tr id="connect-to-wc" title="<?php echo $connect_wc_info; ?>" style="cursor: pointer;">
                            <td>
                                <span class="dashicons dashicons-admin-plugins connect-error"></span>
                            </td>
                            <td>
                                <img class='woocommerce-logo'
                                     src='<?php echo(SHOP2API_PLUGIN_URL . "/assets/woocommerce-icon.png"); ?>'/>
                            </td>
                        <tr>
                        <tr>
                            <td colspan="2">
                                <h2><?php echo $connect_bol ?></h2>
                            </td>
                        </tr>
                        <tr id="connect-to-bol" title="<?php echo $connect_bol_info; ?>"
                            style="cursor: pointer;">
                            <td>
                                <span class="dashicons dashicons-admin-plugins connect-error"></span>
                            </td>
                            <td>
                                <img class='bol-logo'
                                     src='<?php echo(SHOP2API_PLUGIN_URL . "/assets/bol-icon.png"); ?>'/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="disclaimer1"><?php echo $no_use_data; ?></div>
                </div>
            </div>

            <div class="card inline" id='get-started-card-completed' style="min-height: 350px">
                <h1 style="font-weight: bold;"><?php echo $offer_product_heading; ?></h1>
                <div id="connection-completed" class="tooltip">
                    <span class="tooltiptext tooltip-right"><?php echo $reconnect; ?></span>
                    <h3>
                        <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                        <?php echo $web_shop_connect_info ?> <span
                                style="color: #008CBA"><?php echo $reconnect_link; ?></span>
                    </h3>
                </div>
                <div id="map-wc-to-bol" class="tooltip">
                    <span class="tooltiptext tooltip-right">
                        <?php echo $wc_to_bol_tooltip ?>
                    </span>
                    <a href='<?php echo admin_url('admin') ?>.php?page=shop2api_woocommerce_category'>
                        <h3>
                            <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                            <?php echo $wc_to_bol_map_cat; ?> <span
                                    style="color: #008CBA"><?php echo $click_here; ?></span>
                        </h3>
                    </a>
                </div>
                <div id="map-wc-fields-to-bol" class="tooltip">
                    <span class="tooltiptext tooltip-right">
                        <?php echo $wc_to_bol_tooltip_2; ?>
                    </span>
                    <a href='<?php echo admin_url('admin'); ?>.php?page=shop2api_bol_mapping'>
                        <h3>
                            <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                            <?php echo $wc_to_bol_mapping ?> <span
                                    style="color: #008CBA"><?php echo $click_here; ?></span>
                        </h3>
                    </a>
                </div>
            </div>

            <div class="card inline" id='order-card' style="min-height: 350px">
                <h1 style="font-weight: bold;"><?php echo $order_card_heading ; ?></h1>
                <div id="shop2api-map-order-sync" class="tooltip">
                    <span class="tooltiptext tooltip-right">
                        <?php echo $order_card_enabled_tooltip; ?>
                    </span>
                    <a href='<?php echo admin_url('admin'); ?>.php?page=shop2api_bol_order_service'>
                        <h3>
                            <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                            <?php echo $order_card_active ?> <span
                                    style="color: #008CBA"><?php echo $click_here; ?></span>
                        </h3>
                    </a>
                </div>
                <div id="shop2api-map-order-sync-stock" class="tooltip">
                    <span class="tooltiptext tooltip-right">
                        <?php echo $order_card_stock_sync_tooltip; ?>
                    </span>
                    <a href='<?php echo admin_url('admin'); ?>.php?page=shop2api_bol_order_service'>
                        <h3>
                            <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                            <?php echo $order_card_stock_sync_active ?> <span
                                    style="color: #008CBA"><?php echo $click_here; ?></span>
                        </h3>
                    </a>
                </div>
                <div id="shop2api-map-order-sync-combi-deal" class="tooltip">
                    <span class="tooltiptext tooltip-right">
                        <?php echo $order_card_combi_deal_tooltip; ?>
                    </span>
                    <a href='<?php echo admin_url('admin'); ?>.php?page=shop2api_bol_order_service'>
                        <h3>
                            <span class="dashicons dashicons-yes-alt item-completed list-icons"></span>
                            <?php echo $order_card_combi_deal_active ?>
                            <span style="color: #008CBA"><?php echo $click_here; ?></span>
                        </h3>
                    </a>
                </div>
            </div>

            <div class="card inline" id='support-buttons' style="min-height: 350px">
                <h1 style="font-weight: bold;"><?php echo $support_card_heading ; ?></h1>
                <div>
                    <p>
                    <label for="sync-wc-product">Sync Products/Offers/Stock Manually</label>
                    </p>
                    <button id="sync-wc-product" class="shop-2-api-connect-save">Sync</button>
                </div>
                <div>
                    <p>
                        <label for="refresh-dropdowns">Refresh Category/Attribute Dropdowns</label>
                    </p>
                    <button id="refresh-dropdowns" class="shop-2-api-connect-save">Refresh</button>
                </div>
            </div>

            <div class="card inline" id='sync-summary-product-card'>
                <h1>
                    <?php echo $sync_summary_product ?>
                </h1>
                <div class="container">
                    <table class="sync-summary-table">
                        <thead>
                        <tr>
                            <th>
                                Status
                            </th>
                            <th>
                                #
                            </th>
                        </tr>
                        </thead>
                        <tbody id="sync-summary-product-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card inline" id='sync-summary-offer-card' >
                <h1>
                    <?php echo $sync_summary_offer ?>
                </h1>
                <div class="container">
                    <table class="sync-summary-table">
                        <thead>
                        <tr>
                            <th>
                                Status
                            </th>
                            <th>
                                #
                            </th>
                        </tr>
                        </thead>
                        <tbody id="sync-summary-offer-tbody">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card inline" id='sync-summary-offer-card'>
                <h1>
                    <?php echo $sync_summary_order ?>
                </h1>
                <div class="container">
                    <table class="sync-summary-table">
                        <thead>
                        <tr>
                            <th>
                                Status
                            </th>
                            <th>
                                #
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="sync-summary-order-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card inline" id='sync-summary-offer-card'>
                <h1>
                    <?php echo $orders_synced ?>
                </h1>
                <div class="container">
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>
    </div>
</div>

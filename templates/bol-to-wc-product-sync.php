<div class="wrap">
    <?php require_once("common-header.php"); ?>
    <div class="content">
        <table class="wc-to-bol-table" id="wc-to-bol-table" style="width: auto;">
            <tr>
            <td>
                <p>
                    <strong>Ean Number: </strong>
                    <input type="text" name="ean-number" id="ean-number" size="45" value="<?php echo get_option('ean-number'); ?>"/>
                </p>
            </td>
            <td>
                <div class="shop-2-api-connect">
                    <button class="shop-2-api-connect-save" id="search-ean-number">
                        Search
                    </button>
                </div>
            </td>
                <td>
                    <div class="shop-2-api-connect">
                        <button class="shop-2-api-connect-submit-bol-to-wc" id="submit-data">
                            Save to WooCommerce
                        </button>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div  class="content" style="margin-top: 20px">
        <table style="max-width: 80%; min-width: 70%" class="wc-to-bol-table" id="wc-to-bol-data-table">
            <thead>
            <tr>
                <th>
                    Bol Field
                </th>
                <th>
                    Bol Field Value
                </th>
                <th colspan="2">
                    WooCommerce Field
                </th>
            </tr>
            </thead>
            <tbody id="wc-to-bol-table-body">

            </tbody>
        </table>
    </div>
</div>

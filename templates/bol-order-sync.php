<div class="wrap">
    <!--  Common Header to be included everywhere and js should also be in the common.js  -->
    <?php require_once("common-header.php"); ?>

    <div id='bol-connection-error' class='error-message'></div>
    <h2>Sync Bol Orders</h2>
    <form method="post" action="#" id="orders-form">
        <table>
            <tbody id="offer-data-body">
            <tr>
                <td title="Order Service Active" style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-active">Active</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-active" id="orders-active"/>
                    <span id="active" class="error-message"></span>
                </td>
                <td></td>
            </tr>
            <tr>
                <td title="EAN Number Field in WC" style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-ean">EAN Field</label>
                </td>
                <td>
                    <select name="orders-ean" id="orders-ean">
                        <option value="Product" selected>WooCommerce Product Field</option>
                        <option value="MetaData">WooCommerce Metadata Field</option>
                    </select>
                    <span id="sku" class="error-message"></span>
                </td>
                <td><select name="orders-ean-value" id="orders-ean-value"></select></td>
            </tr>
            <tr>
                <td title="Sync Stock to Bol.com, keep your stock in sync with Bol.com"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-active">Sync Stock to Bol.com</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-stock-sync" id="orders-stock-sync"/>
                    <span id="stock-sync" class="error-message"></span>
                </td>
                <td></td>
            </tr>
            <tr>
                <td title="Set Incoming Orders as Paid, this will set the status in processing and update stock items."
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-paid">Incoming Orders Is Paid (Reduce Stock Items)</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-paid" id="orders-paid"/>
                    <span id="paid" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="Set the status of the incoming order to a pre-defined WC status."
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-status">Incoming Orders Status</label>
                </td>
                <td>
                    <select name="orders-status" id="orders-status">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="on-hold">On-Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                    <span id="status" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="Activate the Combi Checks, when orders come in a check will be done if there is any combi deals related to the ean number"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-combideals-active">Combi Deal Service Active</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-combideals-active" id="orders-combideals-active"/>
                    <span id="combideals-active" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="EAN Number Field in WC" style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-combideal-ean">Combi EAN Field</label>
                </td>
                <td>
                    <select name="orders-combideal-ean" id="orders-combideal-ean">
                        <option value="Product" selected>WooCommerce Product Field</option>
                        <option value="MetaData">WooCommerce Metadata Field</option>
                    </select>
                    <span id="orders-combideal-ean" class="error-message"></span>
                </td>
                <td><select name="orders-combideal-ean-value" id="orders-combideal-ean-value"></select></td>
            </tr>
            <tr>
                <td title="The E-Mail that will be on the order (E-Mails will be sent to this address)"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-email">WooCommerce Order E-Mail</label>
                </td>
                <td>
                    <input name="orders-email" id="orders-email" type="email"/>
                    <span id="orders-email" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="Use the price on the orders as it is on Bol.com and not as it is in WooCommerce"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-use-bol-price">Use Bol.com Price</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-use-bol-price" id="orders-use-bol-price"/>
                    <span id="use-bol-price" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="This option will subtract VAT from the bol price (only works if use Bol.com price is enabled)"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-bol-price-include-tax">Subtract Vat From Bol.com Price</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-bol-price-include-tax" id="orders-bol-price-include-tax"/>
                    <span id="orders-bol-price-include-tax-error" class="error-message"></span>
                </td>
            </tr>
            <tr>
                <td title="If there is a failure on the order is will send an e-mail with the detail of the order."
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-active">Alert on Order Failing</label>
                </td>
                <td>
                    <input type="checkbox" name="orders-alert-on-order-fail" id="orders-alert-on-order-fail"/>
                    <span id="stock-sync" class="error-message"></span>
                </td>
                <td></td>
            </tr>
            <tr>
                <td title="The E-Mail that that will be used when sending order alerts"
                    style="padding: 15px 0px 0px 0px;">
                    <span class="dashicons dashicons-info-outline"></span>
                </td>
                <td>
                    <label for="orders-email">Order Alert E-Mail</label>
                </td>
                <td>
                    <input name="orders-alert-email" id="orders-alert-email" type="email"/>
                    <span id="orders-alert-email" class="error-message"></span>
                </td>
            </tr>

            </tbody>
        </table>
        <br/>
        <button type='submit' class='btn primary' title="Save the data to Shop-2-API" id="save-order-data">
            Save
        </button>
    </form>
</div>

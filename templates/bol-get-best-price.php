<div class="wrap">
    <!--  Common Header to be included everywhere and js should also be in the common.js  -->
    <?php require_once("common-header.php"); ?>

	<div id='bol-connection-error' class='error-message'></div>
		<h2>Bol Repricer Service</h2>
		<p>
			You can get more information about the koopblok
            <a href="https://partnerplatform.bol.com/nl/hulp-nodig/aanbod/het-koopblok/">here</a>
		</p>
        <p>
            Once you are happy, you can set the koopblok flag on the products that should be checked, this gives you
            more flexibility so that you can turn this feature on/off on product level.
        </p>

		<form method="post" action="#" id="koopblok-form">
			<table>
				<tbody id="offer-data-body">
					<tr>
						<td title="Activate koopblok service" style="padding: 15px 0px 0px 0px;">
							<span class="dashicons dashicons-info-outline"></span>
						</td>
						<td>
							<label>Active</label>
						</td>
						<td>
							<input type="checkbox" name="koopblok-active" id="koopblok-active"/>
							<span id="active" class="error-message"></span>
						</td>
					</tr>
					<tr>
						<td title="Sellers Id" style="padding: 15px 0px 0px 0px;">
							<span class="dashicons dashicons-info-outline"></span>
						</td>
						<td>
							<label>Sellers Id</label>
						</td>
						<td>
							<input type="number" name="koopblok-seller-id" id="koopblok-seller-id"
							min="0" value="0" step="0" class="integer" />
							<span id="seller_id" class="error-message"></span>
						</td>
                        <td>You can get the seller id under your profile (<a href="<?php echo(SHOP2API_PLUGIN_URL . "/assets/CredentialsBol.png") ?>" target="_blank" >Example</a>)</td>
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
						<td title="The amount the price will decrease by to determine koopblok." style="padding: 15px 0px 0px 0px;">
							<span class="dashicons dashicons-info-outline"></span>
						</td>
						<td>
							<label>Decrease/Increase Amount By</label>
						</td>
						<td>
							<input
                                    type="number"  min="0" value="0.20" step="0.01" class="money2"
                                    name="koopblok-price-increments" id="koopblok-price-increments"
                                    readonly
                            />
							<span id="price_increments" class="error-message"></span>
						</td>
					</tr>
<!--					<tr>-->
<!--						<td title="Check if increasing sales amount will still keep koopblok." style="padding: 15px 0px 0px 0px;">-->
<!--							<span class="dashicons dashicons-info-outline"></span>-->
<!--						</td>-->
<!--						<td>-->
<!--							<label>Days to Start Increasing Price after koopblok (0 don't increase price)</label>-->
<!--						</td>-->
<!--						<td>-->
<!--							<input type="number"  min="0" value="0" step="1" class="integer"-->
<!--							name="koopblok-price-increment-days" id="koopblok-price-increment-days"-->
<!--							style="width: 300px;"/> Days-->
<!--							<span id="days_increment_price" class="error-message"></span>-->
<!--						</td>-->
<!--					</tr>-->
					<tr>
						<td title="The percentage of the price which will be used as a lower limit, once it has been reached the amount will be reverted to the original price." style="padding: 15px 0px 0px 0px;">
							<span class="dashicons dashicons-info-outline"></span>
						</td>
						<td>
							<label>Lower Price Limit (%)</label>
						</td>
						<td>
							<input type="number"  min="0" value="0" step="0.01" class="percent"
							name="koopblok-price-limit" id="koopblok-price-limit"/> %
							<span id="minimum_price_limit" class="error-message"></span>
						</td>
					</tr>
                    <tr>
                        <td title="The percentage of the price which will be used as a upper limit, once it has been reached the amount will be reverted to the original price." style="padding: 15px 0px 0px 0px;">
                            <span class="dashicons dashicons-info-outline"></span>
                        </td>
                        <td>
                            <label>Upper Price Limit (%)</label>
                        </td>
                        <td>
                            <input type="number"  min="0" value="0" step="0.01" class="percent"
                                   name="koopblok-price-limit-max" id="koopblok-price-limit-max"/> %
                            <span id="maximum_price_limit" class="error-message"></span>
                        </td>
                    </tr>
<!--					<tr>-->
<!--						<td title="Once koopblok has been reached update woocommerce price field as selected above." style="padding: 15px 0px 0px 0px;">-->
<!--							<span class="dashicons dashicons-info-outline"></span>-->
<!--						</td>-->
<!--						<td>-->
<!--							<label>On Success Update Woocommerce</label>-->
<!--						</td>-->
<!--						<td>-->
<!--							<input type="checkbox" name="koopblok-update-wc" id="koopblok-update-wc"/>-->
<!--							<span id="on_success_update_woocommerce" class="error-message"></span>-->
<!--						</td>-->
<!--					</tr>-->
				</tbody>
			</table>
			<br/>
			<button type='submit' class='btn primary'
					title="Save the data to Shop-2-API" id="save-koopblok-data">
					Save
			</button>
		</form>


</div>

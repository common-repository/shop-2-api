<div id="overlay">
	<div class="loader">
		<ul>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
			<li><div></div></li>
		</ul>
		<h4 id="loader_text">Processing</h4>
	</div>
</div>

<div class="wrap"> 
	<h1>Capture API Information</h1>
    <p>This data is required by Shop 2 Api to syncronize your data. </p>
    <hr>
    <form>
        <h2>WooCommerce</h2>
        <p>You how to create your WooCommerce Api information <a href="https://docs.woocommerce.com/document/woocommerce-rest-api/">here</a></p>
        <label>Client Key</label>
        <input type=text name="wc-client-key" id="wc-client-key"></input>
        <label>Client Secret</label>
        <input type=text name="wc-client-secret" id="wc-client-secret"></input>
        <hr>
        <h2>Bol</h2>
        <p>You can find how to get/create your Bol Api information <a href="https://developers.bol.com/apiv3credentials/">here</a></p>
        <label>Client Id</label>
        <input type=text name="bol-client-id" id="bol-client-id"></input>
        <label>Client Secret</label>
        <input type=text name="bol-client-secret" id="bol-client-secret"></input>
    </form>
    <hr>
    <div class="shop-2-api-connect">
        <button class="shop-2-api-connect-save" data-nonce=>Save</button>
    </div>
</div>

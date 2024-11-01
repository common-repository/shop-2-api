<div class="wrap">
    <?php require_once("common-header.php"); ?>
	<div class="content" style="padding: 10px 20px">
		<div>
			<p><b style='font-size:larger;'>Select a Bol Category:</b> <span name="wc-data-container"><span></p>
		</div>
		
		<div id="all-tabs">
			<h2 class="nav-tab-wrapper">
				<a href="#offer-table" class="nav-tab nav-tab-active">Offer Mapping</a>
				<a href="#product-table" class="nav-tab">Product Mapping</a>
			</h2>

			<form id="offer-data">
				<table id="offer-table">
					<thead>
						<th colspan="4" class="offer-table-bol-data">
							<h1>Bol Fieldset</h1>
						</th>
						<th>
							<h1 style="padding-left:20px">WooCommerce Fieldset</h1>
						</th>
					</thead>
					<tbody id="offer-data-body">
					</tbody>
				</table>
				<table id="product-table">
					<thead>
						<th colspan="5" class="offer-table-bol-data">
							<h1>Bol Fieldset</h1>
						</th>
						<th>
							<h1 style="padding-left:20px">WooCommerce Fieldset</h1>
						</th>
					</thead>
					<tbody id="product-data-body">
					</tbody>
				</table>   
			</form>
			<br/>
			<div class="shop-2-api-connect">
				<button class="shop-2-api-connect-save">
					Save
				</button>
				<button class="shop-2-api-connect-save-sync">
					Save & Sync Data
				</button>
			</div>
		</div>
	</div>
</div>

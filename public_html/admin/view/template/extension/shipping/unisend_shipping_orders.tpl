<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<?php if ($activeTab == 'new-orders') { ?>
				<button type="submit" form="form-shipping" name="formShipments" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_form_shipment; ?>" class="btn btn-primary">
					<?php echo $text_shipping_unisend_shipping_button_form_shipment; ?>
				</button>
				<button type="submit" form="form-shipping" name="deleteParcels" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_delete; ?>" class="btn btn-danger">
					<?php echo $text_shipping_unisend_shipping_button_delete; ?>
				</button>
				<?php } else { ?>
				<button type="submit" form="form-shipping" name="cancelShipments" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_cancel_shipment; ?>" class="btn btn-primary">
					<?php echo $text_shipping_unisend_shipping_button_cancel_shipment; ?>
				</button>
				<button type="submit" form="form-shipping" name="callCourier" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_call_courier; ?>" class="btn btn-primary">
					<?php echo $text_shipping_unisend_shipping_button_call_courier; ?>
				</button>
				<button type="submit" form="form-shipping" name="printLabel" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_print_label; ?>" class="btn btn-primary">
					<?php echo $text_shipping_unisend_shipping_button_print_label; ?>
				</button>
				<button type="submit" form="form-shipping" name="printManifest" data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_button_print_manifest; ?>" class="btn btn-primary">
					<?php echo $text_shipping_unisend_shipping_button_print_manifest; ?>
				</button>
				<?php } ?>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li>
					<a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">

		<?php if (isset($errors)) { ?>

		<?php foreach ($errors as $error) { ?>
		<div class="alert alert-danger alert-dismiss">
			<?php echo $error; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php } ?>
		<div class="row">
			<div id="filter-order" class="col-md-3 col-md-push-9 col-sm-12 hidden-sm hidden-xs">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-filter"></i> <?php echo $text_shipping_unisend_shipping_order_filter; ?></h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="control-label" for="input-order-id"><?php echo $text_shipping_unisend_shipping_order_id; ?></label>
							<input type="text" name="filter_order_id" value="<?php echo $filter_order_id ?? null; ?>" placeholder="<?php echo $text_shipping_unisend_shipping_order_id; ?>" id="input-order-id" class="form-control" />
						</div>
						<div class="form-group">
							<label class="control-label" for="input-order-status"><?php echo $text_shipping_unisend_shipping_order_shipping_status; ?></label>
							<select name="filter_shipping_status" id="input-order-status" class="form-control">
								<option value=""></option>
								<?php foreach ($shipping_statuses as $shipping_status) { ?>
								<?php if ($shipping_status['id'] == ($filter_shipping_status ?? null)) { ?>
								<option value="<?php echo $shipping_status['id']; ?>" selected="selected"><?php echo $shipping_status['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $shipping_status['id']; ?>"><?php echo $shipping_status['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label class="control-label" for="input-order-id"><?php echo $text_shipping_unisend_shipping_order_barcode; ?></label>
							<input type="text" name="filter_barcode" value="<?php echo $filter_barcode ?? null; ?>" placeholder="<?php echo $text_shipping_unisend_shipping_order_barcode; ?>" id="input-order-id" class="form-control" />
						</div>
						<div class="form-group">
							<label class="control-label" for="input-date-created-from"><?php echo $entry_date_created_from; ?></label>
							<div class="input-group date">
								<input type="text" name="filter_date_created_from" value="<?php echo $filter_date_created_from ?? null; ?>" placeholder="<?php echo $entry_date_created_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-created-from" class="form-control" />
								<span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span> </div>
						</div>
						<div class="form-group">
							<label class="control-label" for="input-date-created-to"><?php echo $entry_date_created_to; ?></label>
							<div class="input-group date">
								<input type="text" name="filter_date_created_to" value="<?php echo $filter_date_created_to ?? null; ?>" placeholder="<?php echo $entry_date_created_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-created-to" class="form-control" />
								<span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span> </div>
						</div>
						<div class="form-group text-right">
							<button type="button" id="button-filter" class="btn btn-default"><i class="fa fa-filter"></i> <?php echo $text_shipping_unisend_shipping_button_filter; ?></button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-9 col-md-pull-3 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?php echo $text_shipping_unisend_shipping_orders; ?></h3>
					</div>
					<div class="panel-body">
						<form method="post" enctype="multipart/form-data" id="form-shipping">
							<ul class="nav nav-tabs">
								<li <?php if ($activeTab == 'new-orders') { ?> class="active" <?php } ?>>
								<a href="<?php echo $url; ?>&activeTab=new-orders&page=1"><?php echo $text_shipping_unisend_shipping_order_new; ?></a>
								</li>
								<li <?php if ($activeTab == 'orders') { ?> class="active" <?php } ?>>
								<a href="<?php echo $url; ?>&activeTab=orders&page=1"><?php echo $text_shipping_unisend_shipping_order_formed; ?></a>
								</li>
								<li <?php if ($activeTab == 'processed-orders') { ?> class="active" <?php } ?>>
								<a href="<?php echo $url; ?>&activeTab=processed-orders&page=1"><?php echo $text_shipping_unisend_shipping_order_processed; ?></a>
								</li>
							</ul>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
									<tr>
										<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/></td>

										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_id; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_shipping_status; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_barcode; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_terminal; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_size; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_weight; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_part_count; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_plan; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_parcel_type; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_parcel_shipping_address; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_cod_amount; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_order_date; ?></td>
									</tr>
									</thead>
									<tbody>
									<?php if (isset($orders)) { ?>
									<?php foreach ($orders as $order) { ?>
									<tr>
										<td class="text-center">
											<input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>"/>
										</td>
										<td class="text-left">
											<a href="<?php echo $order['edit']; ?>" data-toggle="tooltip">
												<?php echo $order['order_id']; ?>
											</a>
										</td>
										<td class="text-left"><?php echo $order['shipping_status'] ?></td>
										<td class="text-left"><?php echo $order['barcode'] ?></td>
										<td class="text-left"><?php echo $order['terminal'] ?></td>
										<td class="text-left"><?php echo $order['size'] ?></td>
										<td class="text-left"><?php echo $order['weight'] ?></td>
										<td class="text-left"><?php echo $order['part_count'] ?></td>
										<td class="text-left"><?php echo $order['plan_code'] ?></td>
										<td class="text-left"><?php echo $order['parcel_type'] ?></td>
										<td class="text-left"><?php echo $order['shipping_address'] ?></td>
										<?php if ($order['cod_selected'] == true) { ?>
										<td class="text-left"><?php echo $order['cod_amount'] ?></td>
										<?php } else { ?>
										<td class="text-left"></td>
										<?php } ?>
										<td class="text-left"><?php echo $order['created'] ?></td>
									</tr>
									<?php } ?>
									<?php } else { ?>
									<tr>
										<td class="text-center" colspan="2"><?php echo $text_no_results; ?></td>
									</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
						</form>
						<div class="row">
							<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
							<div class="col-sm-6 text-right"><?php echo $results; ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
<script type="text/javascript"><!--

	const userTokenParam = '<?php echo $userTokenParam; ?>';

	$('#button-filter').on('click', function() {
		url = '';

		var filter_order_id = $('input[name=\'filter_order_id\']').val();

		if (filter_order_id) {
			url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
		}

		var filter_barcode = $('input[name=\'filter_barcode\']').val();

		if (filter_barcode) {
			url += '&filter_barcode=' + encodeURIComponent(filter_barcode);
		}

		var filter_customer = $('input[name=\'filter_customer\']').val();

		if (filter_customer) {
			url += '&filter_customer=' + encodeURIComponent(filter_customer);
		}

		var filter_shipping_status = $('select[name=\'filter_shipping_status\']').val();

		if (filter_shipping_status !== '') {
			url += '&filter_shipping_status=' + encodeURIComponent(filter_shipping_status);
		}

		var filter_total = $('input[name=\'filter_total\']').val();

		if (filter_total) {
			url += '&filter_total=' + encodeURIComponent(filter_total);
		}

		var filter_date_created_from = $('input[name=\'filter_date_created_from\']').val();

		if (filter_date_created_from) {
			url += '&filter_date_created_from=' + encodeURIComponent(filter_date_created_from);
		}

		var filter_date_created_to = $('input[name=\'filter_date_created_to\']').val();

		if (filter_date_created_to) {
			url += '&filter_date_created_to=' + encodeURIComponent(filter_date_created_to);
		}
		location = 'index.php?route=extension/shipping/unisend_shipping/orders&' + userTokenParam + '&activeTab=<?php echo $activeTab; ?>' + url;
	});
	//--></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
	$('.date').datetimepicker({
		language: '<?php echo $datepicker ?? null; ?>',
		pickTime: false
	});
	//--></script>
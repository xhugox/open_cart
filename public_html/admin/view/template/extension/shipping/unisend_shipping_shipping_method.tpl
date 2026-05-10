<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-shipping" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
					<i class="fa fa-save"></i>
				</button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
					<i class="fa fa-reply"></i>
				</a>
				<?php if (isset($editedShippingMethod) && $editedShippingMethod != null) { ?>
					<button type="button" form="form-shipping" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? deleteShippingMethod('<?php echo $delete; ?>'): false;"><i class="fa fa-trash-o"></i></button>
				<?php } ?> </div>
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
	<div class="container-fluid">
		<?php if (isset($error_warning)): ?>
		<div class="alert alert-danger alert-dismissible">
			<i class="fa fa-exclamation-circle"></i>
			<?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php endif; ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-pencil"></i>
					<?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-shipping" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_name">
							<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_name; ?>
						</label>
						<div class="col-sm-10">
							<input type="text" name="unisend_shipping_method_name" value="<?php echo $unisend_shipping_method_name ?? null; ?>" id="input-shipping_unisend_shipping_method_name" class="form-control"/>
						</div>
					</div>
					<?php if (isset($shippingPlans)): ?>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_plan_code">
								<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_plan_code; ?>
							</label>
							<div class="col-sm-10">
								<select id="input-shipping_unisend_shipping_method_plan_code" name="unisend_shipping_method_plan_code" class="form-control">
									<?php foreach ($shippingPlans as $shippingPlan) { ?>
									<?php if ($shippingPlan->code == ($unisend_shipping_method_plan_code ?? null)) { ?>
									<option value="<?php echo $shippingPlan->code; ?>" selected="selected"><?php echo $shippingPlan->name; ?></option>
									<?php } else { ?>
									<option value="<?php echo $shippingPlan->code; ?>"><?php echo $shippingPlan->name; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
					<?php if (isset($parcelTypes)): ?>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_parcel_type">
								<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_type; ?>
							</label>
							<div class="col-sm-10">
								<select id="input-shipping_unisend_shipping_method_parcel_type" name="unisend_shipping_method_parcel_type" class="form-control">
									<?php foreach ($parcelTypes->shipping as $parcelType) { ?>
										<?php if ($parcelType->parcelType == ($unisend_shipping_method_parcel_type ?? null)) { ?>
										<option value="<?php echo $parcelType->parcelType; ?>" selected="selected"><?php echo $parcelType->name; ?></option>
										<?php } else { ?>
										<option value="<?php echo $parcelType->parcelType; ?>"><?php echo $parcelType->name; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_rate_type">
							<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_rate_type; ?>
						</label>
						<div class="col-sm-10">
							<select id="input-shipping_unisend_shipping_method_rate_type" name="unisend_shipping_method_rate_type" class="form-control">
								<?php foreach ($rateTypes as $rateType) { ?>
								<?php if ($rateType == ($unisend_shipping_method_rate_type ?? null)): ?>
								<option id="<?php echo $rateType['code']; ?>" value="<?php echo $rateType['code']; ?>" selected="selected"><?php echo $rateType['name']; ?></option>
								<?php else: ?>
								<option id="<?php echo $rateType['code']; ?>" value="<?php echo $rateType['code']; ?>"><?php echo $rateType['name']; ?></option>
								<?php endif; ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group rate-type-size">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_sizes">
							<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_size; ?>
						</label>
						<div class="col-sm-10">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
									<tr>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_size; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_price; ?></td>
									</tr>
									</thead>
									<tbody class="parcel-type-sizes"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="form-group rate-type-weight">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_weight">
							<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight; ?>
						</label>
						<div class="col-sm-10">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
									<tr>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_from; ?></td>
										<td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_to; ?></td>
										<td class="text-left" colspan="3"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_price; ?></td>
									</tr>
									</thead>
									<tbody class="parcel-type-weights"></tbody>
									<tfoot>
									<tr>
										<td colspan="3"></td>
										<td class="text-right">
											<button type="button" onclick="addSize();" data-toggle="tooltip" title="<?php echo $button_attribute_add; ?>" class="btn btn-primary">
												<i class="fa fa-plus-circle"></i>
											</button>
										</td>
									</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_free_shipping_from">
							<?php echo $text_shipping_unisend_shipping_shipping_method_free_shipping_from; ?>
						</label>
						<div class="col-sm-10">
							<input type="text" name="unisend_shipping_method_free_shipping_from" value="<?php echo $unisend_shipping_method_free_shipping_from ?? null; ?>" id="input-shipping_unisend_shipping_method_free_shipping_from" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-shipping_unisend_shipping_method_country">
							<?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_country; ?>
						</label>
						<div class="col-sm-10">
							<input type="text" name="product" value="" id="input-shipping_unisend_shipping_method_country" class="form-control"/>
							<div id="method_country" class="well well-sm" style="height: 150px; overflow: auto;"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	const shippingPlans = <?php echo isset($shippingPlans) ? json_encode($shippingPlans) : [] ?>;
	const editedShippingMethod = <?php echo isset($editedShippingMethod) ? json_encode($editedShippingMethod) : '[]' ?>;
	const editedShippingMethodSizes = <?php echo isset($sizes) ? json_encode($sizes) : '[]' ?>;
	const editedShippingMethodWeights = <?php echo isset($weights) ? json_encode($weights) : '[]' ?>;
	const editedShippingMethodCountries = <?php echo isset($countries) ? json_encode($countries) : '[]' ?>;
	const userTokenParam = '<?php echo $userTokenParam; ?>';
	const textButtonRemove = '<?php echo $button_remove; ?>';
</script>
<script type="text/javascript" src="view/javascript/unisend_shipping/unisend-shipping-shipping-method.js"></script>
<?php echo $footer; ?>

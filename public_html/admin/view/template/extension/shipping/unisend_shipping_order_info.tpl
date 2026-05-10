<div class="tab-content unisendshipping-container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $text_shipping_unisend_shipping_shipping; ?></h3>
        </div>
        <div class="panel-body">
            <div class="lps-panel">
                <div class="lps-form-div">
                    <form action="<?php echo $unisendSaveShipmentAction; ?>" method="post" id="unisendshipping_order_submit_form">
                        <div class="form-horizontal">

                            <div class="row form-group">
                                <?php if (!empty($unisendLastError)) { ?>
                                <div class="col-md-12">
                                    <div class="unisend-alert alert-danger">
                                        <strong><?php echo $text_shipping_unisend_shipping_error; ?></strong> <?php echo $unisendLastError; ?>
                                    </div>
                                </div>
                                <?php } ?>

                                <!-- First column -->
                                <div class="col-md-6">
                                    <div class="col-md-12 field-group" <?php if (!$unisendSavedParcelPartCount): ?> style="display: none" <?php endif; ?>>
                                    <label for="unisendshipping-partCount" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_part_count; ?> *</label>
                                    <input id="unisendshipping-partCount" class="input-group form-control" type="text" name="partCount"
                                           value="<?php echo $unisendSavedParcelPartCount; ?>" <?php if ($unisendIsShipmentFormed == true): ?> disabled <?php endif; ?>/>
                                </div>

                                <div class="col-md-12 field-group" <?php if (!isset($unisendParcelWeightAvailable)): ?> style="display: none" <?php endif; ?>>
                                <label for="unisendshipping-weight"
                                       class="control-label required"><?php echo $text_shipping_unisend_shipping_order_weight; ?> *</label>
                                <input id="unisendshipping-weight" type="text" name="weight" value="<?php echo $unisendSavedParcelWeight; ?>"
                                       class="form-control"  <?php if ($unisendIsShipmentFormed == true): ?> disabled <?php endif; ?>/>
                            </div>
                            <div class="col-md-12 field-group" <?php if (!isset($unisendParcelSizeAvailable)): ?> style="display: none" <?php endif; ?>>
                            <label for="size" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_size; ?>
                                *</label>
                            <select name="size" class="form-control" id="unisendshipping-size-select" <?php if ($unisendIsShipmentFormed == true): ?> disabled <?php endif; ?>>
                            <?php foreach ($unisendParcelSizes as $size) { ?>
                            <option value="<?php echo $size; ?>"
                            <?php if ($size == $unisendSavedParcelSize): ?>selected="selected"<?php endif; ?>><?php echo $size; ?>
                            </option>
                            <?php } ?>
                            </select>

                        </div>

                        <?php if ($unisendCodAvailable): ?>
                        <div class="col-md-12 field-group">
                            <label for="codSelected" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_cod_flag; ?></label>
                            <select name="codSelected" id="unisendshipping-cod-select" autocomplete="off" class="form-control" <?php if ($unisendIsShipmentFormed  == true) { ?> disabled <?php } ?>>
                            <option value="0"><?php echo $text_shipping_unisend_shipping_select_no; ?></option>
                            <option value="1" <?php if ($unisendCodSelected): ?> selected="selected" <?php endif; ?>><?php echo $text_shipping_unisend_shipping_select_yes; ?></option>
                            </select>
                        </div>

                        <div class="col-md-12 field-group" id="unisendshipping-cod-amount-box">
                            <label for="codAmount" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_cod_amount; ?></label>
                            <input type="text" name="codAmount" value="<?php echo $unisendCodAmount; ?>" class="form-control" <?php if ($unisendIsShipmentFormed == true): ?> disabled <?php endif; ?> />
                        </div>
                        <?php endif; ?>


                </div>
                <!-- Second column -->
                <div class="col-md-6">
                    <div class="col-md-12 field-group">
                        <label for="planCode" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_plan; ?>
                            *</label>
                        <select name="plan_code" class="form-control" id="unisendshipping-plan-code-select" <?php if ($unisendIsShipmentFormed  == true) { ?> disabled <?php } ?>>

                        <?php foreach ($unisendSavedParcelPlanCodes as $planCode => $planName) { ?>
                            <option value="<?php echo $planCode; ?>"
                            <?php if ($planCode == $unisendSavedParcelPlanCode): ?>selected="selected"<?php endif; ?>><?php echo $planName; ?></option>
                        <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12 field-group">
                        <label for="parcelType"
                               class="control-label required"><?php echo $text_shipping_unisend_shipping_order_parcel_type; ?>
                            *</label>
                        <select name="parcel_type" class="form-control" id="unisendshipping-parcel-type-select" <?php if ($unisendIsShipmentFormed  == true) { ?> disabled <?php } ?>>

                        <?php foreach ($unisendSavedParcelTypes as $parcelType => $parcelTypeName) { ?>
                        <option value="<?php echo $parcelType; ?>"<?php if ($parcelType == $unisendSavedParcelType): ?>selected="selected"<?php endif; ?>><?php echo $parcelTypeName; ?></option>
                        <?php } ?>

                        </select>
                    </div>

                    <div class="col-md-12 field-group unisendshipping-terminals-select-container">
                        <label for="terminalId"
                               class="control-label required"><?php echo $text_shipping_unisend_shipping_order_terminals; ?>
                            *</label>
                        <select class="unisendshipping-terminals-select form-control" name="terminalId" <?php if ($unisendIsShipmentFormed  == true) { ?> disabled <?php } ?>>
                        <?php if ($terminalId): ?> <option value="<?php echo $terminalId; ?>"><?php echo $terminal; ?></option> <?php endif; ?>
                        </select>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <div class="col-md-12 lps-form-btns">
                    <?php if ($unisendIsShipmentFormed == false): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="saveUnisendShippingOrder">
                        <?php if ($unisendIsOrderSaved == false): ?>
                        <?php echo $text_shipping_unisend_shipping_button_save; ?>
                        <?php else: ?>
                        <?php echo $text_shipping_unisend_shipping_button_update; ?>
                        <?php endif; ?>
                    </button>

                    <?php if ($unisendIsOrderSaved == true): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="formShipments" style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_form_shipment; ?>
                    </button>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($unisendIsCancellable == true): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="cancelShipments" style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_cancel_shipment; ?>
                    </button>
                    <?php endif; ?>

                    <?php if ($unisendIsCallCourierAvailable == true): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="callCourier" style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_call_courier; ?>
                    </button>
                    <?php endif; ?>

                    <?php if ($unisendAreDocumentsPrintable): ?>
                    <?php if ($unisendIsLabelPrintable): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="printLabel" style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_print_label; ?>
                    </button>
                    <?php endif; ?>

                    <?php if ($unisendIsManifestPrintable): ?>
                    <button type="submit" class="btn btn-primary pull-right" name="printManifest"
                            style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_print_manifest; ?>
                    </button>
                    <?php endif; ?>

                    <?php endif; ?>

                    <?php if ($unisendCnRequired && $unisendIsShipmentFormed != true) { ?>
                    <button type="button" class="btn btn-primary pull-right" style="margin-right: 8px" data-toggle="modal" data-target="#declarationsModal">
                        <?php echo $text_shipping_unisend_shipping_button_cn_fill; ?>
                    </button>
                    <?php } ?>

                    <?php if (isset($unisendIsDeleteAvailable) && $unisendIsDeleteAvailable == true) { ?>
                    <button type="submit" class="btn btn-danger pull-left" name="deleteParcels" style="margin-right: 8px">
                        <?php echo $text_shipping_unisend_shipping_button_delete; ?>
                    </button>
                    <?php } ?>
                </div>
            </div>

            <input type="hidden" name="barcode" value="<?php echo $unisendBarcode; ?>" />
            <input type="hidden" name="parcelId" value="<?php echo $unisendParcelId; ?>" />
            <input id="unisend-terminal" type="hidden" name="terminal" value="<?php echo $terminal; ?>" />

        </div>

        <?php if ($unisendCnRequired): ?>
        <!-- Modal edit action content for CN23/CN22 declarations -->
        <div class="modal fade" id="declarationsModal" tabindex="-1" role="dialog" aria-labelledby="declarationsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="declarationsModalLabel"><?php echo $text_shipping_unisend_shipping_button_cn_fill; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="unisendshipping-declaration-edit-modal">
                            <!-- Select parcel type -->
                            <div class="parcel-type">
                                <label for="cnForm[contentType]" class="control-label required"><?php echo $text_shipping_unisend_shipping_order_parcel_type; ?></label>
                                <select name="cnForm[contentType]" class="form-control" id="unisendshipping-parcel-type">

                                    <?php foreach ($unisendDeclarationParcelTypes as $key => $type) { ?>
                                        <?php if (parcelData.documents.cn.contentType == $key): ?>
                                            <option value="<?php echo $key; ?>" selected><?php echo $type; ?></option>
                                            <?php else: ?>
                                            <option value="<?php echo $key; ?>"><?php echo $type; ?></option>
                                        <?php endif; ?>
                                    <?php } ?>
                                </select>
                            </div>


                            <!-- Parcel description -->
                            <div class="parcel-description">
                                <label for="cnForm[contentDescription]"
                                       class="control-label required"><?php echo $text_shipping_unisend_shipping_order_cn_description; ?></label>
                                <input type="text" name="cnForm[contentDescription]"
                                       value="{{parcelData.documents.cn.contentDescription}}" class="form-control"/>
                            </div>

                            <!-- CN Parts -->

                            <table class="data-table admin__table-primary edit-order-table">
                                <thead class="lp-cn-parts">
                                <tr class="headings">
                                    <th class="col-cn-part col-summary">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_summary; ?><span style="color:red">*</span>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-amount">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_amount; ?><span style="color:red">*</span>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-currency">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_currency; ?><span style="color:red">*</span>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-weight">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_weight; ?> (g) <span style="color:red">*</span>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-quantity">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_quantity; ?> <span style="color:red">*</span>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-country">
                                                <span>
                                                    <?php echo $text_shipping_unisend_shipping_order_cn_origin_country; ?>
                                                </span>
                                    </th>
                                    <th class="col-cn-part col-hs-code">
                                        <span><?php echo $text_shipping_unisend_shipping_order_cn_hs; ?></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="even">
                                <?php foreach ($unisendSavedCnParts as $cnPartIndex => $item) { ?>
                                <tr>
                                    <td class="col-cn-part col-summary">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][summary]"
                                               type="text"
                                               id="summary"
                                               class="admin__control-text"
                                               value="<?php echo $item['summary']; ?>"
                                               required/>
                                    </td>
                                    <td class="col-cn-part col-amount">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][amount]"
                                               type="number"
                                               value="<?php echo $item['amount']; ?>"
                                               step="0.01" id="amount"
                                               class="admin__control-text"
                                               required/>
                                    </td>
                                    <td class="col-cn-part col-currency">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][currencyCode]"
                                               value="<?php echo $item['currencyCode']; ?>"
                                               id="currencyCode" type="text"
                                               class="admin__control-text"
                                               required/>
                                    </td>
                                    <td class="col-cn-part col-weight">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][weight]"
                                               type="number"
                                               value="<?php echo $item['weight']; ?>"
                                               step="1" id="weight"
                                               class="admin__control-text"
                                               required/>
                                    </td>
                                    <td class="col-cn-part col-quantity">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][quantity]"
                                               type="number"
                                               value="<?php echo $item['quantity']; ?>"
                                               step="1" id="quantity"
                                               class="admin__control-text"
                                               required/>
                                    </td>
                                    <td class="col-cn-part col-country">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][countryCode]"
                                               value="<?php echo $item['countryCode']; ?>"
                                               id="originCountryCode" type="text"
                                               class="admin__control-text"/>
                                    </td>
                                    <td class="col-cn-part col-hs-code">
                                        <input class="form-control" name="cnForm[parts][<?php echo $cnPartIndex; ?>][hsCode]"
                                               id="hsCode"
                                               value="<?php echo $item['hsCode']; ?>" type="text"
                                               class="admin__control-text"/>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $text_shipping_unisend_shipping_button_close; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        </form>
    </div>
</div>
</div>
<script>
    const countryCode = '<?php echo $shippingCountryCode; ?>';
    const userTokenParam = '<?php echo $userTokenParam; ?>';
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="view/javascript/unisend_shipping/unisend-shipping-order-info.js"></script>
<style>
    .unisend-alert {
        padding: 10px;
        margin-bottom: 18px;
        border: 1px solid transparent;
        border-radius: 3px;
    }
    .col-cn-part {
        max-width:75px;
    }
    .col-cn-part.col-summary {
        max-width:160px;
    }
    .col-cn-part.col-amount {
        max-width:80px;
    }
    .select2-selection {
        padding-left: 7px;
        height: 34px !important;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
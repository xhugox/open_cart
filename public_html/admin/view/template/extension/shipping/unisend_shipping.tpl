<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-shipping" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                </button>
                <?php if ($cancel) { ?>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
                    <i class="fa fa-reply"></i>
                </a>
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
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-pencil"></i>
                    <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-shipping" class="form-horizontal">
                    <input type="hidden" name="unisend_shipping_settings_active_tab" value="<?php echo $unisend_shipping_settings_active_tab; ?>" id="unisend_shipping_settings_active_tab"/>
                    <ul id='unisend_shipping_settings_tabs' class="nav nav-tabs">
                        <li data-value="tab-general" <?php if ($unisend_shipping_settings_active_tab == 'tab-general') { ?> class="active" <?php } ?>>
                        <a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a>
                        </li>
                        <li data-value="tab-address" <?php if ($unisend_shipping_settings_active_tab == 'tab-address') { ?> class="active" <?php } ?>>
                        <a href="#tab-address" data-toggle="tab"><?php echo $text_shipping_unisend_shipping_settings_tab_address; ?></a>
                        </li>
                        <li data-value="tab-shipping-methods" <?php if ($unisend_shipping_settings_active_tab == 'tab-shipping-methods') { ?> class="active" <?php } ?>>
                        <a href="#tab-shipping-methods" data-toggle="tab"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods; ?></a>
                        </li>
                        <li data-value="tab-options" <?php if ($unisend_shipping_settings_active_tab == 'tab-options') { ?> class="active" <?php } ?>>
                        <a href="#tab-options" data-toggle="tab"><?php echo $text_shipping_unisend_shipping_settings_tab_options; ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php if ($unisend_shipping_settings_active_tab == 'tab-general') { ?> active <?php } ?>" id="tab-general">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <?php echo $entry_status; ?>
                                </label>

                                <div class="col-sm-10 col-5">
                                    <select name="unisend_shipping_status" id="input-status" class="form-control">
                                        <?php if ($unisend_shipping_status) { ?>
                                        <option value="1" selected="selected">
                                            <?php echo $text_enabled; ?>
                                        </option>
                                        <option value="0">
                                            <?php echo $text_disabled; ?>
                                        </option>
                                        <?php } else { ?>
                                        <option value="1">
                                            <?php echo $text_enabled; ?>
                                        </option>
                                        <option value="0" selected="selected">
                                            <?php echo $text_disabled; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <?php echo $text_shipping_unisend_shipping_settings_tab_general_mode; ?>
                                </label>

                                <div class="col-sm-10 col-5">
                                    <select name="unisend_shipping_settings_mode_live" id="input-status" class="form-control">
                                        <?php if ($unisend_shipping_settings_mode_live) { ?>
                                        <option value="1" selected="selected">
                                            <?php echo $text_shipping_unisend_shipping_settings_tab_general_mode_production; ?>
                                        </option>
                                        <option value="0">
                                            <?php echo $text_shipping_unisend_shipping_settings_tab_general_mode_test; ?>
                                        </option>
                                        <?php } else { ?>
                                        <option value="1">
                                            <?php echo $text_shipping_unisend_shipping_settings_tab_general_mode_production; ?>
                                        </option>
                                        <option value="0" selected="selected">
                                            <?php echo $text_shipping_unisend_shipping_settings_tab_general_mode_test; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_settings_username">
                                    <?php echo $text_shipping_unisend_shipping_settings_username; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_username" value="<?php echo $unisend_shipping_username ?? null; ?>" id="input-unisend_shipping_settings_username" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_settings_password">
                                    <?php echo $text_shipping_unisend_shipping_settings_password; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="password" name="unisend_shipping_password" value="<?php echo $unisend_shipping_password ?? null; ?>" id="input-unisend_shipping_settings_password" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $text_shipping_unisend_shipping_tax_class; ?></label>
                                <div class="col-sm-10">
                                    <select name="unisend_shipping_tax_class_id" id="input-tax-class" class="form-control">
                                        <option value="0"><?php echo $text_none; ?></option>
                                        <?php foreach ($tax_classes as $tax_class) { ?>
                                        <?php if ($tax_class['tax_class_id'] == ($unisend_shipping_tax_class_id ?? null)) { ?>
                                        <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if ($unisend_shipping_settings_active_tab == 'tab-address') { ?> active <?php } ?>" id="tab-address">
                            <h3><?php echo $text_shipping_unisend_shipping_sender_title; ?></h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_name">
                                    <?php echo $text_shipping_unisend_shipping_sender_name; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_name" value="<?php echo $unisend_shipping_sender_name ?? null; ?>" id="input-unisend_shipping_sender_name" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_contact_tel">
                                    <?php echo $text_shipping_unisend_shipping_sender_contact_tel; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_phone" value="<?php echo $unisend_shipping_sender_phone ?? null; ?>" id="input-unisend_shipping_sender_contact_tel" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_contact_email">
                                    <?php echo $text_shipping_unisend_shipping_sender_contact_email; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="email" name="unisend_shipping_sender_email" value="<?php echo $unisend_shipping_sender_email ?? null; ?>" id="input-unisend_shipping_sender_contact_email" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_country">
                                    <?php echo $text_shipping_unisend_shipping_sender_country; ?>
                                </label>
                                <div class="col-sm-10">
                                    <select name="unisend_shipping_sender_country_code" id="input-unisend_shipping_sender_country" class="form-control">
                                        <option></option>
                                        <option value="LT" <?php if (($unisend_shipping_sender_country_code ?? null) == 'LT') { ?> selected <?php } ?>>LT</option>
                                        <option value="LV" <?php if (($unisend_shipping_sender_country_code ?? null) == 'LV') { ?> selected <?php } ?>>LV</option>
                                        <option value="EE" <?php if (($unisend_shipping_sender_country_code ?? null) == 'EE') { ?> selected <?php } ?>>EE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_city">
                                    <?php echo $text_shipping_unisend_shipping_sender_city; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_city" value="<?php echo $unisend_shipping_sender_city ?? null; ?>" id="input-unisend_shipping_sender_city" class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_address">
                                    <?php echo $text_shipping_unisend_shipping_sender_street; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_street" value="<?php echo $unisend_shipping_sender_street ?? null; ?>" id="input-unisend_shipping_sender_address" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_building">
                                    <?php echo $text_shipping_unisend_shipping_sender_building; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_building" value="<?php echo $unisend_shipping_sender_building ?? null; ?>" id="input-unisend_shipping_sender_building" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_flat">
                                    <?php echo $text_shipping_unisend_shipping_sender_flat; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_flat" value="<?php echo $unisend_shipping_sender_flat ?? null; ?>" id="input-unisend_shipping_sender_flat" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-unisend_shipping_sender_postcode">
                                    <?php echo $text_shipping_unisend_shipping_sender_postcode; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="unisend_shipping_sender_post_code" value="<?php echo $unisend_shipping_sender_post_code ?? null; ?>" id="input-unisend_shipping_sender_postcode" class="form-control" minlength="4" maxlength="5"/>
                                </div>
                            </div>
                            <h3><?php echo $text_shipping_unisend_shipping_pickup_title; ?></h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="unisend_shipping_pickup_enabled">
                                    <?php echo $text_shipping_unisend_shipping_pickup_enabled; ?>
                                </label>
                                <div class="col-sm-10">
                                    <select name="unisend_shipping_pickup_enabled" id="unisend_shipping_pickup_enabled" class="form-control">
                                        <?php if ($unisend_shipping_pickup_enabled) { ?>
                                        <option value="1" selected="selected">
                                            <?php echo $text_shipping_unisend_shipping_select_yes; ?>
                                        </option>
                                        <option value="0">
                                            <?php echo $text_shipping_unisend_shipping_select_no; ?>
                                        </option>
                                        <?php } else { ?>
                                        <option value="1">
                                            <?php echo $text_shipping_unisend_shipping_select_yes; ?>
                                        </option>
                                        <option value="0" selected="selected">
                                            <?php echo $text_shipping_unisend_shipping_select_no; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="unisend_shipping_pickup_address_container">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_name">
                                        <?php echo $text_shipping_unisend_shipping_pickup_name; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_name" value="<?php echo $unisend_shipping_pickup_name ?? null; ?>" id="input-unisend_shipping_pickup_name" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_contact_tel">
                                        <?php echo $text_shipping_unisend_shipping_pickup_contact_tel; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_phone" value="<?php echo $unisend_shipping_pickup_phone ?? null; ?>" id="input-unisend_shipping_pickup_contact_tel" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_contact_email">
                                        <?php echo $text_shipping_unisend_shipping_pickup_contact_email; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="email" name="unisend_shipping_pickup_email" value="<?php echo $unisend_shipping_pickup_email ?? null; ?>" id="input-unisend_shipping_pickup_contact_email" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_country">
                                        <?php echo $text_shipping_unisend_shipping_pickup_country; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <select name="unisend_shipping_pickup_country_code" id="input-unisend_shipping_pickup_country" class="form-control">
                                            <option></option>
                                            <option value="LT" <?php if (($unisend_shipping_pickup_country_code ?? null) == 'LT') { ?> selected <?php } ?>>LT</option>
                                            <option value="LV" <?php if (($unisend_shipping_pickup_country_code ?? null) == 'LV') { ?> selected <?php } ?>>LV</option>
                                            <option value="EE" <?php if (($unisend_shipping_pickup_country_code ?? null) == 'EE') { ?> selected <?php } ?>>EE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_city">
                                        <?php echo $text_shipping_unisend_shipping_pickup_city; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_city" value="<?php echo $unisend_shipping_pickup_city ?? null; ?>" id="input-unisend_shipping_pickup_city" class="form-control"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_address">
                                        <?php echo $text_shipping_unisend_shipping_pickup_street; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_street" value="<?php echo $unisend_shipping_pickup_street ?? null; ?>" id="input-unisend_shipping_pickup_address" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_building">
                                        <?php echo $text_shipping_unisend_shipping_pickup_building; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_building" value="<?php echo $unisend_shipping_pickup_building ?? null; ?>" id="input-unisend_shipping_pickup_building" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_flat">
                                        <?php echo $text_shipping_unisend_shipping_pickup_flat; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_flat" value="<?php echo $unisend_shipping_pickup_flat ?? null; ?>" id="input-unisend_shipping_pickup_flat" class="form-control" />
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_pickup_postcode">
                                        <?php echo $text_shipping_unisend_shipping_pickup_postcode; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_pickup_post_code" value="<?php echo $unisend_shipping_pickup_post_code ?? null; ?>" id="input-unisend_shipping_pickup_postcode" class="form-control" minlength="4" maxlength="5"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if ($unisend_shipping_settings_active_tab == 'tab-shipping-methods') { ?> active <?php } ?>" id="tab-shipping-methods">
                            <div class="table-responsive">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-unisend_shipping_method_sort_order">
                                        <?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_sort_order; ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unisend_shipping_method_sort_order" value="<?php echo $unisend_shipping_method_sort_order ?? null; ?>" id="input-unisend_shipping_method_sort_order" class="form-control" />
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_name; ?></td>
                                        <td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_sort_order; ?></td>
                                        <td class="text-left"><?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_action; ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($unisend_shipping_methods) { ?>
                                    <?php foreach ($unisend_shipping_methods as $shipping_method) { ?>
                                    <tr>
                                        <td class="text-left"><?php echo $shipping_method['title']; ?></td>
                                        <td class="text-left">
                                            <input type="number" name="unisend_shipping_shipping_method_sort_order[]" value="<?php echo $shipping_method['sort_order']; ?>" class="form-control" />
                                            <input type="hidden" name="unisend_shipping_shipping_method_id[]" value="<?php echo $shipping_method['unisend_shipping_method_id']; ?>" class="form-control" />
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo $shipping_method['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="<?php echo $shipping_method['delete']; ?>" title="" class="btn btn-danger" ><i class="fa fa-minus-circle"></i></a>
                                        </td>
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
                            <div class="text-right">
                                <a href="<?php echo $add; ?>" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i>
                                    <?php echo $text_shipping_unisend_shipping_settings_tab_shipping_methods_add ?></a>
                            </div>
                        </div>
                        <div class="tab-pane <?php if ($unisend_shipping_settings_active_tab == 'tab-options') { ?> active <?php } ?>" id="tab-options">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for=""><span data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_settings_tab_options_dimensions_help; ?>"><?php echo $text_shipping_unisend_shipping_settings_tab_options_dimensions; ?></span></label>
                                <div class="col-sm-2">
                                    <label class="col-sm-1 control-label" for="input-shipping_unisend_settings_dimension_length"><?php echo $text_shipping_unisend_shipping_settings_tab_options_dimensions_length; ?></label>
                                    <input type="text"
                                           name="unisend_shipping_settings_dimension_length"
                                           value="<?php echo $unisend_shipping_settings_dimension_length; ?>"
                                           class="form-control" placeholder="10"/>
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-sm-2 control-label" for="input-shipping_unisend_settings_dimension_width"><?php echo $text_shipping_unisend_shipping_settings_tab_options_dimensions_width; ?></label>
                                    <input type="text"
                                           name="unisend_shipping_settings_dimension_width"
                                           value="<?php echo $unisend_shipping_settings_dimension_width; ?>"
                                           class="form-control" placeholder="10"/>
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-sm-2 control-label" for="input-shipping_unisend_settings_dimension_height"><?php echo $text_shipping_unisend_shipping_settings_tab_options_dimensions_height; ?></label>
                                    <input type="text"
                                           name="unisend_shipping_settings_dimension_height"
                                           value="<?php echo $unisend_shipping_settings_dimension_height; ?>"
                                           class="form-control" placeholder="10"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for=""><?php echo $text_shipping_unisend_shipping_settings_tab_options_label; ?></label>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label" for="input-shipping_unisend_settings_sticker_layout"><?php echo $text_shipping_unisend_shipping_settings_tab_options_label_layout; ?></label>
                                    <select name="unisend_shipping_settings_sticker_layout" id="input-shipping_unisend_settings_sticker_layout" class="form-control">
                                        <option value="LAYOUT_10x15" <?php if ($unisend_shipping_settings_sticker_layout == 'LAYOUT_10x15') { ?> selected="selected" <?php } ?>>LAYOUT_10x15</option>
                                        <option value="LAYOUT_MAX" <?php if ($unisend_shipping_settings_sticker_layout == 'LAYOUT_MAX') { ?> selected="selected" <?php } ?>>LAYOUT_MAX</option>
                                        <option value="LAYOUT_A4" <?php if ($unisend_shipping_settings_sticker_layout == 'LAYOUT_A4') { ?> selected="selected" <?php } ?>>LAYOUT_A4</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label" for="input-shipping_unisend_settings_sticker_orientation"><?php echo $text_shipping_unisend_shipping_settings_tab_options_label_orientation; ?></label>
                                    <select name="unisend_shipping_settings_sticker_orientation" id="input-shipping_unisend_settings_sticker_orientation" class="form-control">
                                        <option value="LANDSCAPE" <?php if ($unisend_shipping_settings_sticker_orientation == 'LANDSCAPE') { ?> selected="selected" <?php } ?>>LANDSCAPE</option>
                                        <option value="PORTRAIT" <?php if ($unisend_shipping_settings_sticker_orientation == 'PORTRAIT') { ?> selected="selected" <?php } ?>>PORTRAIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row unisend_shipping_courier">
                                    <label class="col-sm-2 control-label" for=""><span data-toggle="tooltip" title="<?php echo $text_shipping_unisend_shipping_settings_tab_options_courier_help; ?>"><?php echo $text_shipping_unisend_shipping_settings_tab_options_courier; ?></span></label>
                                    <div class="col-sm-8">
                                        <select name="unisend_shipping_settings_courier_enabled" id="unisend_shipping_settings_courier_enabled" class="form-control">
                                            <?php if ($unisend_shipping_settings_courier_enabled) { ?>
                                            <option value="1" selected="selected">
                                                <?php echo $text_enabled; ?>
                                            </option>
                                            <option value="0">
                                                <?php echo $text_disabled; ?>
                                            </option>
                                            <?php } else { ?>
                                            <option value="1">
                                                <?php echo $text_enabled; ?>
                                            </option>
                                            <option value="0" selected="selected">
                                                <?php echo $text_disabled; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-6 control-label" for="input-shipping_unisend_settings_sticker_layout"><?php echo $text_shipping_unisend_shipping_settings_tab_options_courier_days; ?></label>
                                        <div class="col-sm-10">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;"> <?php foreach ($unisend_shipping_settings_courier_available_days as $courier_day) { ?>
                                                <div class="checkbox">
                                                    <label> <?php if ($unisend_shipping_settings_courier_days && in_array($courier_day['id'], $unisend_shipping_settings_courier_days)) { ?>
                                                        <input type="checkbox" name="unisend_shipping_settings_courier_days[]" value="<?php echo $courier_day['id']; ?>" checked="checked" />
                                                        <?php echo $courier_day['name']; ?>
                                                        <?php } else { ?>
                                                        <input type="checkbox" name="unisend_shipping_settings_courier_days[]" value="<?php echo $courier_day['id']; ?>" />
                                                        <?php echo $courier_day['name']; ?>
                                                        <?php } ?> </label>
                                                </div>
                                                <?php } ?> </div>
                                            <?php if (isset($error_processing_status)) { ?>
                                                <div class="text-danger"><?php echo $error_processing_status; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="col-sm-2 control-label" for="input-shipping_unisend_settings_courier_hour"><?php echo $text_shipping_unisend_shipping_settings_tab_options_courier_hour; ?></label>
                                        <select name="unisend_shipping_settings_courier_hour" id="input-shipping_unisend_settings_courier_hour" class="form-control">
                                            <?php foreach ($unisend_shipping_settings_courier_available_hours as $courier_hour) { ?>
                                            <option value="<?php echo $courier_hour ?>" <?php if ($unisend_shipping_settings_courier_hour == $courier_hour) { ?> selected="selected" <?php } ?>><?php echo $courier_hour ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .unisend_shipping_courier {
        margin-left: 0px;
        margin-right: 0px;
    }
</style>
<script src="view/javascript/unisend_shipping/unisend-shipping-settings.js"></script>
<?php echo $footer; ?>
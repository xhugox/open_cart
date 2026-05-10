<?php

namespace unisend_shipping\cons;

class UnisendShippingConst
{
    const DEFAULT_BOX_SIZE = 'S';

    const SETTING_KEY_API_URL = 'unisend_shipping_settings_api_url';
    const SETTING_KEY_API_TEST_URL = 'unisend_shipping_settings_api_test_url';
    const SETTING_KEY_API_TOKEN = 'unisend_shipping_api_token';
    const SETTING_KEY_MODE_LIVE = 'unisend_shipping_settings_mode_live';
    const SETTING_KEY_SHIPPING_STATUS = 'unisend_shipping_status';
    const SETTING_KEY_SHIPPING_METHOD_SORT = 'unisend_shipping_method_sort_order';
    const SETTING_KEY_COURIER_CALL_PENDING_OFFSET = 'unisend_shipping_courier_offset';
    const SETTING_KEY_DEFAULT_WEIGHT_CLASS_ID = 'unisend_shipping_settings_weight_class_id';
    const SETTING_KEY_DEFAULT_LENGTH_CLASS_ID = 'unisend_shipping_settings_length_class_id';
    const SETTING_KEY_DEFAULT_STATUS_ID_TO_CREATE_PARCEL = 'unisend_shipping_settings_status_id_to_create_parcel';
    const SETTING_KEY_DEFAULT_DIMENSION_WIDTH = 'unisend_shipping_settings_dimension_width';
    const SETTING_KEY_DEFAULT_DIMENSION_LENGTH = 'unisend_shipping_settings_dimension_length';
    const SETTING_KEY_DEFAULT_DIMENSION_HEIGHT = 'unisend_shipping_settings_dimension_height';
    const SETTING_KEY_ADDRESS_PICKUP_ID = 'unisend_shipping_pickup_id';
    const SETTING_KEY_PLUGIN_ID = 'unisend_shipping_plugin_id';
    const SETTING_KEY_STICKER_LAYOUT = 'unisend_shipping_settings_sticker_layout';
    const SETTING_KEY_STICKER_ORIENTATION = 'unisend_shipping_settings_sticker_orientation';
    const SETTING_KEY_COURIER_ENABLED = 'unisend_shipping_settings_courier_enabled';
    const SETTING_KEY_COURIER_DAYS = 'unisend_shipping_settings_courier_days';
    const SETTING_KEY_COURIER_HOUR = 'unisend_shipping_settings_courier_hour';
    const SETTING_KEY_COURIER_SCHEDULED_TIME = 'unisend_shipping_settings_courier_scheduled_time';
    const SETTING_KEY_USERNAME = 'unisend_shipping_username';
    const SETTING_KEY_PASSWORD = 'unisend_shipping_password';
    const SETTING_KEY_TAX_CLASS_ID = 'unisend_shipping_tax_class_id';
    const SETTING_KEY_ACTIVE_TAB = 'unisend_shipping_settings_active_tab';
    const SETTING_KEY_LAST_ERROR = 'unisend_shipping_error';
    const SETTING_KEY_TRACKING_TOKEN = 'unisend_shipping_tracking_token';
    const SETTING_KEY_TRACKING_TOKEN_SALT = 'unisend_shipping_tracking_salt';
    const SETTING_KEY_SENDER_PHONE = 'unisend_shipping_sender_phone';
    const SETTING_KEY_SENDER_COUNTRY = 'unisend_shipping_sender_country_code';
    const SETTING_KEY_SENDER_STREET = 'unisend_shipping_sender_street';
    const SETTING_KEY_SENDER_EMAIL = 'unisend_shipping_sender_email';
    const SETTING_KEY_SENDER_CITY = 'unisend_shipping_sender_city';
    const SETTING_KEY_SENDER_POST_CODE = 'unisend_shipping_sender_post_code';
    const SETTING_KEY_SENDER_ADDRESS1 = 'unisend_shipping_sender_address1';
    const SETTING_KEY_SENDER_ADDRESS2 = 'unisend_shipping_sender_address2';
    const SETTING_KEY_SENDER_BUILDING = 'unisend_shipping_sender_building';
    const SETTING_KEY_SENDER_FLAT = 'unisend_shipping_sender_flat';
    const SETTING_KEY_SENDER_NAME = 'unisend_shipping_sender_name';
    const SETTING_KEY_SENDER_COMPANY_NAME = 'unisend_shipping_sender_company_name';
    const SETTING_KEY_PICKUP_ENABLED = 'unisend_shipping_pickup_enabled';
    const SETTING_KEY_PICKUP_PHONE = 'unisend_shipping_pickup_phone';
    const SETTING_KEY_PICKUP_COUNTRY = 'unisend_shipping_pickup_country_code';
    const SETTING_KEY_PICKUP_STREET = 'unisend_shipping_pickup_street';
    const SETTING_KEY_PICKUP_EMAIL = 'unisend_shipping_pickup_email';
    const SETTING_KEY_PICKUP_CITY = 'unisend_shipping_pickup_city';
    const SETTING_KEY_PICKUP_POST_CODE = 'unisend_shipping_pickup_post_code';
    const SETTING_KEY_PICKUP_ADDRESS1 = 'unisend_shipping_pickup_address1';
    const SETTING_KEY_PICKUP_ADDRESS2 = 'unisend_shipping_pickup_address2';
    const SETTING_KEY_PICKUP_BUILDING = 'unisend_shipping_pickup_building';
    const SETTING_KEY_PICKUP_FLAT = 'unisend_shipping_pickup_flat';
    const SETTING_KEY_PICKUP_NAME = 'unisend_shipping_pickup_name';
    const SETTING_KEY_PICKUP_COMPANY_NAME = 'unisend_shipping_pickup_company_name';
    const SETTING_KEY_VERSION = 'unisend_shipping_version';
    const ORDER_STATUS_SAVED = 'ORDER_STATUS_SAVED';
    const ORDER_STATUS_NOT_SAVED = 'ORDER_STATUS_NOT_SAVED';
    const ORDER_STATUS_FORMED = 'ORDER_STATUS_FORMED';
    const ORDER_STATUS_NOT_FORMED = 'ORDER_STATUS_NOT_FORMED';
    const ORDER_STATUS_LABEL_GENERATED = 'ORDER_STATUS_LABEL_GENERATED';
    const ORDER_STATUS_COURIER_CALLED = 'ORDER_STATUS_COURIER_CALLED';
    const ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED = 'ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED';
    const ORDER_STATUS_COMPLETED = 'ORDER_STATUS_COMPLETED';
    const COURIER_CRON_LAST_EXECUTION = 'unisend_shipping_courier_last';
}
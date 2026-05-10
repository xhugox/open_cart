<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendParcelApi extends UnisendApi
{
    /**
     * Authentication gateways
     */
    const PARCEL_URI = 'parcel';
    const PARCEL_IDREF_URI = 'parcel/idref/';
    const PARCEL_VALIDATE_URI = 'parcel/validate';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function createParcel($create_parcel_request)
    {
        $instance = self::getInstance();
        return $instance->post(self::PARCEL_URI, $create_parcel_request);
    }

    public static function validateParcel($validate_parcel_request)
    {
        $instance = self::getInstance();
        return $instance->post(self::PARCEL_VALIDATE_URI, $validate_parcel_request);
    }

    public static function getParcel($order_id)
    {
        $instance = self::getInstance();
        return $instance->get(self::PARCEL_IDREF_URI . $order_id);
    }

    public static function updateParcel($order_id, $update_parcel_request)
    {
        $instance = self::getInstance();
        return $instance->put(self::PARCEL_IDREF_URI . $order_id, $update_parcel_request);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendParcelApi();
        }
        return self::$instance;
    }
}

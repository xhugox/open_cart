<?php

namespace unisend_shipping\api;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendAddressApi extends UnisendApi
{

    const ADDRESS_SENDER = 'address/sender';
    const ADDRESS_BY_ID_SENDER = 'address/sender/';
    const ADDRESS_VALIDATE = 'address/validate';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getSenderAddress()
    {
        $instance = self::getInstance();
        return $instance->get(self::ADDRESS_SENDER);
    }

    public static function getPickupAddress()
    {
        $instance = self::getInstance();
        $addressId = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID);
        if (!$addressId) {
            return false;
        }
        $response = $instance->get(self::ADDRESS_BY_ID_SENDER . $addressId);
        if ($response === false) {
            UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID, '');
        }
        return $response;
    }

    public static function updateSenderAddress()
    {
        $instance = self::getInstance();
        $request = $instance->createUpdateSenderAddressRequest();
        return $instance->put(self::ADDRESS_SENDER, $request);
    }

    public static function savePickupAddress()
    {
        $instance = self::getInstance();
        $addressId = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID);
        if (!$addressId) {
            return $instance->createPickupAddress();
        }
        return $instance->put(self::ADDRESS_BY_ID_SENDER . $addressId, $instance->createPickupAddressRequest());
    }

    public static function createPickupAddress()
    {
        $instance = self::getInstance();
        return $instance->post(self::ADDRESS_SENDER, $instance->createPickupAddressRequest());
    }

    public static function validateAddress($address, callable $errorCallback)
    {
        $instance = self::getInstance();
        return $instance->post(self::ADDRESS_VALIDATE, $address, $errorCallback);
    }

    public static function createUpdateAddressRequest(string $prefix): array
    {
        $instance = self::getInstance();
        $name = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'name'));
        $companyName = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'company_name'));
        $phone = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'phone'));
        $email = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'email'));
        $country = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'country_code'));
        $locality = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'city'));
        $street = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'street'));
        $building = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'building'));
        $flat = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'flat'));
        $postalCode = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'post_code'));
        $address1 = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'address1'));
        $address2 = $instance->nullIfEmpty(UnisendShippingConfigService::get($prefix . 'address2'));
        $request = [];
        $instance->applyIfValueNotNull($request, 'name', $name);
        $instance->applyIfValueNotNull($request, 'companyName', $companyName);
        $instance->applyIfValueNotNull($request['contacts'], 'phone', $phone);
        $instance->applyIfValueNotNull($request['contacts'], 'email', $email);
        $instance->applyIfValueNotNull($request['address'], 'countryCode', $country);
        $instance->applyIfValueNotNull($request['address'], 'locality', $locality);
        $instance->applyIfValueNotNull($request['address'], 'street', $street);
        $instance->applyIfValueNotNull($request['address'], 'building', $building);
        $instance->applyIfValueNotNull($request['address'], 'flat', $flat);
        $instance->applyIfValueNotNull($request['address'], 'postalCode', $postalCode);
        $instance->applyIfValueNotNull($request['address'], 'address1', $address1);
        $instance->applyIfValueNotNull($request['address'], 'address2', $address2);
        return $request;
    }

    public static function createUpdateSenderAddressRequest(): array
    {
        $instance = self::getInstance();
        return $instance->createUpdateAddressRequest('unisend_shipping_sender_');
    }

    public static function createPickupAddressRequest(): array
    {
        $instance = self::getInstance();
        return $instance->createUpdateAddressRequest('unisend_shipping_pickup_');
    }

    private static function applyIfValueNotNull(&$arr, $key, $value): void
    {
        if ($value != null) {
            $arr[$key] = $value;
        }
    }

    private static function nullIfEmpty($value)
    {
        if ($value == "") return null;
        return $value;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendAddressApi();
        }
        return self::$instance;
    }
}

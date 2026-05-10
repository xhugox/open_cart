<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendEstimateShippingApi extends UnisendApi
{
    const PLAN_URI = 'shipping/estimate/plan';
    const PLAN_COUNTRIES_URI = 'shipping/estimate/plan/countries';
    const ESTIMATE_PRICE_LIST_URI = 'shipping/estimate/price/list';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getPlans(string $countryCode, array $params = [])
    {
        $instance = self::getInstance();
        $allParams['receiverCountryCode'] = $countryCode;
        if ($params) {
            $allParams += $params;
        }
        return $instance->get(self::PLAN_URI, $allParams);
    }

    public static function getEstimatedPrices(array $items = [])
    {
        $instance = self::getInstance();
        return $instance->post(self::ESTIMATE_PRICE_LIST_URI, ['items' => $items]);
    }

    public static function getCountries(string $planCodes, string $parcelTypes)
    {
        $instance = self::getInstance();
        $allParams['planCodes'] = $planCodes;
        $allParams['parcelTypes'] = $parcelTypes;

        return $instance->get(self::PLAN_COUNTRIES_URI, $allParams);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendEstimateShippingApi();
        }
        return self::$instance;
    }

    public function isResponseAsArray(): bool
    {
        return true;
    }
}

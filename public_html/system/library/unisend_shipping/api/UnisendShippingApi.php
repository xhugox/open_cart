<?php

namespace unisend_shipping\api;


use unisend_shipping\api\request\UnisendIdRefListRequest;

require_once(dirname(__FILE__) . '/UnisendApi.php');

class ShippingStatus
{
    const IN_PROGRESS = "IN_PROGRESS";
    const SUCCESSFUL = "SUCCESSFUL";
    const PARTIALLY_SUCCESSFUL = "PARTIALLY_SUCCESSFUL";
    const ERROR = "ERROR";

    public static function isStatusOk(string $status): bool
    {
        return $status == ShippingStatus::SUCCESSFUL || $status == ShippingStatus::PARTIALLY_SUCCESSFUL;
    }
}

class ShippingItemStatus
{
    const OK = "OK";
    const FAILED = "FAILED";
    const COURIER_PENDING = "COURIER_PENDING";
    const COURIER_CALLED = "COURIER_CALLED";

    public static function isStatusOk(string $status): bool
    {
        return $status == ShippingItemStatus::OK || $status == ShippingItemStatus::COURIER_PENDING || $status == ShippingItemStatus::COURIER_CALLED;
    }
}

/**
 * Singleton class to make calls to API
 */
class UnisendShippingApi extends UnisendApi
{
    const INITIATE_URI = 'shipping/initiate?processAsync=false';
    const STATUS_URI = 'shipping/status/';
    const CANCEL_URI = 'shipping/cancel';
    const AVAILABLE_URI = 'shipping/available';
    const AVAILABLE_LIST_URI = 'shipping/available/list';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function initiate(UnisendIdRefListRequest $request)
    {
        $instance = self::getInstance();
        return $instance->post(self::INITIATE_URI, $request);
    }

    public static function getStatus(string $requestId)
    {
        $instance = self::getInstance();
        $shippingStatusResponse = $instance->get(self::STATUS_URI . $requestId);
        return $shippingStatusResponse;
    }

    public static function cancel(array $orderIds)
    {
        $instance = self::getInstance();
        $request = [];
        $request['idRefs'] = $orderIds;
        $shippingStatusResponse = $instance->post(self::CANCEL_URI, $request);
        return $shippingStatusResponse;
    }

    public static function isShippingAvailable(array $params): bool
    {
        $instance = self::getInstance();
        $shippingAvailableResponse = $instance->get(self::AVAILABLE_URI, $params, self::DEFAULT_ACCEPT, 5);
        if (!$shippingAvailableResponse) return false;
        return @$shippingAvailableResponse->available ?? false;
    }

    public static function getShippingAvailability(array $params)
    {
        $instance = self::getInstance();
        return $instance->post(self::AVAILABLE_LIST_URI, $params);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingApi();
        }
        return self::$instance;
    }
}

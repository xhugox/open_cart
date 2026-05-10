<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendTrackingApi extends UnisendApi
{

    const GET_EVENTS_BY_BARCODE_URI = 'tracking/%s/events';
    const GET_EVENTS_BY_BARCODES_URI = 'tracking/events';
    const CONFIGURE_URI = 'tracking/configurations';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getTrackingEvents(string $barcode)
    {
        $instance = self::getInstance();
        return $instance->get(sprintf(self::GET_EVENTS_BY_BARCODE_URI, $barcode));
    }

    public static function getTrackingEventsByBarcodes(array $barcodes, $datetime)
    {
        $instance = self::getInstance();
        return $instance->post(self::GET_EVENTS_BY_BARCODES_URI . ($datetime ? '?dateFrom=' . $datetime : null), $barcodes);
    }

    public static function configure($baseUrl, $token)
    {
        $instance = self::getInstance();
        return $instance->post(self::CONFIGURE_URI, [
            "url" => $baseUrl."index.php?route=extension/shipping/unisend_shipping/tracking",
            "authToken" => $token,
            "maxCount" => 100
        ]);
    }

    public function disableErrorHandling(): bool
    {
        return true;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendTrackingApi();
        }
        return self::$instance;
    }
}

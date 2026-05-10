<?php

namespace unisend_shipping\api;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendCourierApi extends UnisendApi
{

    const CALL_REQUIRED_URI = 'courier/call/required';
    const PENDING_CALL_URI = 'courier/pending/call';
    const CALL_URI = 'courier/call';
    const GET_MANIFEST_URI = 'courier/manifest/list';
    const GET_MANIFEST_PDF_URI = 'courier/manifest/pdf';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }


    public static function isCallRequired(): bool
    {
        $instance = self::getInstance();
        $response = $instance->get(self::CALL_REQUIRED_URI);
        if ($response != null) return $response === true;
        return false;
    }

    public static function pendingCall(): array
    {
        $instance = self::getInstance();
        $offset = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_COURIER_CALL_PENDING_OFFSET) ?: 1;

        $response = $instance->post(self::PENDING_CALL_URI . '?createdBefore=' . $offset);
        if ($response != null) {
            return $response;
        }
        return [];
    }

    public static function getManifests(array $orderIds)
    {
        $instance = self::getInstance();
        $param['idRefs'] = implode(',', $orderIds);
        return $instance->get(self::GET_MANIFEST_URI, $param);
    }

    public static function downloadManifests(array $orderIds): bool
    {
        $instance = self::getInstance();
        $param['idRefs'] = implode(',', $orderIds);
        $responseBody = $instance->get(self::GET_MANIFEST_PDF_URI, $param, 'application/pdf');
        if (!$responseBody) {
            return false;
        }
        $filename = sprintf('lp_manifests_%s.pdf',
            date('Y-m-d H:i:s')
        );
        header('Content-type: application/pdf');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
        echo $responseBody;
        return true;
    }

    public static function call(array $orderIds)
    {
        $instance = self::getInstance();
        $body['idRefs'] = $orderIds;
        return $instance->post(self::CALL_URI, $body);
    }

    public static function getManifest(int $orderId)
    {
        $instance = self::getInstance();
        return $instance->getManifests([$orderId]);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendCourierApi();
        }
        return self::$instance;
    }

    public function isResponseAsArray(): bool
    {
        return true;
    }
}

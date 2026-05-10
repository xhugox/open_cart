<?php

namespace unisend_shipping\repository;

use unisend_shipping\context\UnisendShippingContextHolder;

class UnisendShippingRequestRepository
{

    public static function saveShippingRequest(string $requestId, string $status)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $created = date('Y-m-d H:i:s');

        $db->query("INSERT INTO `" . DB_PREFIX . "unisend_shipping_request` (`request_id`, `status`, `created`) VALUES ('" . $requestId . "','" . $status . "','" . $created . "')");
    }
}
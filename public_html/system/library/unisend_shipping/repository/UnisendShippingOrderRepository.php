<?php

namespace unisend_shipping\repository;


use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\services\LpOrderStatus;
use unisend_shipping\services\UnisendShippingCarrierService;
use unisend_shipping\services\UnisendShippingSizeService;

/**
 * Singleton class
 */
class UnisendShippingOrderRepository
{

    private static $instance = null;

    public function __construct()
    {
    }


    public static function update($orderData)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $orderInfo = $orderData['orderInfo'] ?? [];
        $orderId = (int)$orderData['order_id'];
        $savedOrder = self::getById($orderId);
        if (!$savedOrder) {
            return false;
        }
        $weight = isset($orderInfo['weight']) ? (int)$orderInfo['weight'] : $savedOrder['weight'];
        $parcelType = isset($orderInfo['parcelType']) ? $db->escape($orderInfo['parcelType']) : $savedOrder['parcel_type'];
        $size = isset($orderInfo['size']) ? $db->escape($orderInfo['size']) : $savedOrder['size'];
        $partCount = isset($orderInfo['partCount']) ? (int)$orderInfo['partCount'] : $savedOrder['part_count'];
        $planCode = isset($orderInfo['planCode']) ? $db->escape($orderInfo['planCode']) : $savedOrder['plan_code'];
        $codSelected = isset($orderInfo['codSelected']) ? (bool)$orderInfo['codSelected'] : (bool)$savedOrder['cod_selected'];
        $codAmount = isset($orderInfo['codAmount']) ? $db->escape($orderInfo['codAmount']) : $savedOrder['cod_amount'];
        $status = isset($orderData['status']) ? $orderData['status'] : $savedOrder['status'];
        $shippingStatus = isset($orderData['shippingStatus']) ? $orderData['shippingStatus'] : $savedOrder['shipping_status'];
        $parcelId = isset($orderData['parcelId']) ? (int)$orderData['parcelId'] : $savedOrder['parcel_id'];
        $barcode = isset($orderData['barcode']) ? $orderData['barcode'] : $savedOrder['barcode'];
        $requestId = isset($orderData['requestId']) ? $orderData['requestId'] : $savedOrder['request_id'];
        $terminalId = isset($orderData['unisend_selected_terminal_id']) ? $orderData['unisend_selected_terminal_id'] : $savedOrder['terminal_id'];
        $terminal = isset($orderData['terminal']) ? $orderData['terminal'] : $savedOrder['terminal'];
        $updated = date('Y-m-d H:i:s');
        $db->query("UPDATE " . DB_PREFIX . "unisend_shipping_order SET `terminal_id` = '" . $terminalId . "', `terminal` = '" . $terminal . "', `weight` = " . $weight . ",`parcel_type` = '" . $parcelType . "',`size` = '" . $size . "',`part_count` = " . $partCount . ",`plan_code` = '" . $planCode . "',`cod_selected` = '" . $codSelected . "',`cod_amount` = " . $codAmount . ",`status` = '" . $status . "',`shipping_status` = '" . $shippingStatus . "',`parcel_id` = " . $parcelId . ",`updated` = '" . $updated . "',`request_id` = '" . $requestId . "',`barcode` = '" . $barcode . "' WHERE order_id = " . $orderId);
        return true;
    }

    public static function create($orderData)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $order = $orderData['orderInfo'];
        if ($order && !self::getById($order['order_id'])) {
            $codAmount = $order['payment_code'] == 'cod' ? $order['total'] : null;
            $codSelected = $codAmount ? true : false;
            $shippingCode = $order['shipping_code'];
            $orderId = $order['order_id'];
            $size = UnisendShippingSizeService::resolveSize($orderData);
            $status = UnisendShippingConst::ORDER_STATUS_NOT_SAVED;
            $shippingStatus = LpOrderStatus::$PARCEL_CREATE_PENDING->name;
            $weight = $order['weight'];

            $shippingCodeParts = explode(':', $order['shipping_code']);
            $carrierId = intval($shippingCodeParts[1]) ?? null;
            $unisendCarrier = UnisendShippingCarrierService::getById($carrierId);

            if ($carrierId) {
                $carrier = UnisendShippingCarrierService::getById($carrierId);
                if ($carrier) {
                    $planCode = $unisendCarrier['plan_code'];
                    $parcelType = $unisendCarrier['parcel_type'];

                    if ($parcelType === 'H2T' || $parcelType === 'T2T' || $parcelType === 'T2S') {
                        $terminalId = $orderData['unisend_selected_terminal_id'] ?? null;
                        $terminalName = $orderData['unisend_selected_terminal_name'] ?? null;
                    } else {
                        $terminalId = null;
                        $terminalName = null;
                    }
                }
            }

            $db->query("INSERT INTO `" . DB_PREFIX . "unisend_shipping_order` (`order_id`, `carrier_id`, `shipping_code`, `plan_code`, `parcel_type`, `weight`, `size`, `part_count`, `status`, `shipping_status`, `terminal_id`, `terminal`, `cod_amount`, `cod_selected`, `created`) VALUES (" . $orderId . "," . $carrierId . ",'" . $shippingCode . "','" . $planCode . "','" . $parcelType . "'," . $weight . ",'" . $size . "'," . '1' . ",'" . $status . "','" . $shippingStatus . "','" . $terminalId . "','" . $terminalName . "','" . $codAmount . "','" . $codSelected . "',NOW() )");
        }
    }

    public static function getOrders($data = array())
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $sql = "SELECT o.* FROM `" . DB_PREFIX . "unisend_shipping_order` o";

        $sql .= self::generateFilterSql($data);

        $sort_data = array(
            'o.order_id',
            'status',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.order_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $db->query($sql);

        return $query->rows;
    }

    public static function getTotalOrders($data = array())
    {

        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "unisend_shipping_order` o";

        $sql .= self::generateFilterSql($data);

        $query = $db->query($sql);

        return $query->row['total'];
    }

    private static function generateFilterSql($data = array())
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $sql = '';
        if (!empty($data['filter_order_status'])) {
            $implode = array();

            $order_statuses = explode(',', $data['filter_order_status']);

            foreach ($order_statuses as $order_status) {
                $implode[] = "o.status = '" . $order_status . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            }
        }
        if (!empty($data['filter_shipping_status'])) {
            $implode = array();

            $shipping_statuses = explode(',', $data['filter_shipping_status']);

            foreach ($shipping_statuses as $shipping_status) {
                $implode[] = "o.shipping_status = '" . $shipping_status . "'";
            }

            if ($implode) {
                $sql .= " AND (" . implode(" OR ", $implode) . ")";
            }
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_barcode'])) {
            $sql .= " AND o.barcode = '" . $data['filter_barcode'] . "'";
        }

        $filterDateFrom = null;
        $filterDateTo = null;
        if (!empty($data['filter_date_created_from'])) {
            $filterDateFrom = $db->escape($data['filter_date_created_from']);
        }
        if (!empty($data['filter_date_created_to'])) {
            $filterDateTo = $db->escape($data['filter_date_created_to']);
            if (!$filterDateFrom) {
                $filterDateFrom = date('Y-m-d', strtotime("-12 months"));
            }
        } else if ($filterDateFrom) {
            $filterDateTo = date('Y-m-d');
        }
        if ($filterDateFrom && $filterDateTo) {
            $sql .= " AND DATE(o.created) BETWEEN DATE('" . $filterDateFrom . "') AND DATE('" . $filterDateTo . "')";
        }
//
//        if (!empty($data['filter_total'])) {
//            $sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
//        }
        return $sql;
    }

    private static function getOrderWeight($products)
    {
        $totalWeight = 0;
        foreach ($products as $product) {
            $virtual = $product['shipping'] != true;
            if ($virtual) continue;
            $weight = $product['weight'] ?? 0;
            $totalWeight += $weight;
        }
        return max($totalWeight, 1);
    }

    public static function getById($orderId)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "unisend_shipping_order` WHERE order_id = '$orderId'");
        return $query->row;
    }

    public static function getByBarcode($barcode)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "unisend_shipping_order` WHERE barcode = '$barcode'");
        return $query->row;
    }

    public static function getByIds(array $orderIds)
    {
        $orderIdsQuery = implode("','", $orderIds);
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "unisend_shipping_order` WHERE order_id in ('$orderIdsQuery')");
        return $query->rows;
    }

    public static function deleteByIds(array $orderIds)
    {
        $orderIdsQuery = implode("','", $orderIds);
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $db->query("DELETE FROM `" . DB_PREFIX . "unisend_shipping_order` WHERE order_id in ('$orderIdsQuery')");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingOrderRepository();
        }
        return self::$instance;
    }
}

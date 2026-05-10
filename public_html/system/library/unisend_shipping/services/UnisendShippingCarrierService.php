<?php

namespace unisend_shipping\services;


use unisend_shipping\api\UnisendEstimateShippingApi;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\api\UnisendShippingApi;
use UnisendShippingRequestErrorHandler;

/**
 * Singleton class
 */
class UnisendShippingCarrierService
{

    private static $instance = null;

    public function __construct()
    {
    }

    public static function getById($id)
    {
        $shippingMethod = self::getShippingMethod($id);
        if ($shippingMethod) {
            return self::toBasicCarrier($shippingMethod);
        }
        return false;
    }

    public static function getAvailableCarriers($products, $orderInfo, $totalAmount)
    {
        $shippingCountryCode = $orderInfo['shipping_iso_code_2'] ?? null;
        if (!$shippingCountryCode) return [];

        $carriers = self::getCarriersByCountry($shippingCountryCode);

        //filter carriers via unisend API
        $availableCarriers = self::filterAvailableCarriers($carriers, $orderInfo, $products);

        $carrierPrices = self::calcPrices($availableCarriers, $orderInfo, $totalAmount, $products);

        //filter carriers by calculated price
        $availableCarriers = array_filter($availableCarriers, function ($carrier) use ($carrierPrices) {
            return array_search($carrier['unisend_shipping_method_id'], array_column($carrierPrices, 'id')) !== false;
        });

        return array_map(function ($carrier) use ($totalAmount, $orderInfo, $carrierPrices) {
            return [
                'id' => (int)$carrier['unisend_shipping_method_id'],
                'code' => self::toCarrierCode($carrier),
                'title' => $carrier['title'],
                'plan_code' => $carrier['plan_code'],
                'parcel_type' => $carrier['parcel_type'],
                'sort_order' => $carrier['sort_order'],
                'price' => $carrierPrices[array_search($carrier['unisend_shipping_method_id'], array_column($carrierPrices, 'id'))]['price'],
            ];
        }, $availableCarriers);
    }

    private static function toBasicCarrier($carrier)
    {
        return [
            'id' => (int)$carrier['unisend_shipping_method_id'],
            'code' => self::toCarrierCode($carrier),
            'title' => $carrier['title'],
            'plan_code' => $carrier['plan_code'],
            'parcel_type' => $carrier['parcel_type'],
        ];
    }

    private static function toCarrierCode($carrier)
    {
        if ($carrier['plan_code'] === 'TERMINAL') {
            return 'unisend_shipping_terminal:' . $carrier['unisend_shipping_method_id'];
        }
        return 'unisend_shipping_shipping:' . $carrier['unisend_shipping_method_id'];
    }

    private static function calcPricesByCarrier($carriers, $orderInfo, $products)
    {
        $requestItems = [];
        foreach ($carriers as $carrier) {
            $requestItems[] = [
                'id' => (int)$carrier['unisend_shipping_method_id'],
                'receiverCountryCode' => $orderInfo['shipping_iso_code_2'],
                'receiverPostalCode' => $orderInfo['shipping_postcode'],
                'size' => UnisendShippingSizeService::resolveSize(['orderInfo' => $orderInfo, 'unisendCarrier' => $carrier, 'products' => $products]),
                'weight' => $orderInfo['weight'],
                'planCode' => $carrier['plan_code'],
                'parcelType' => $carrier['parcel_type'],
            ];
        }
        $estimatedPrices = UnisendEstimateShippingApi::getEstimatedPrices($requestItems);
        if (!$estimatedPrices || (is_array($estimatedPrices) && isset($estimatedPrices['success']) && $estimatedPrices['success'] !== true)) {
            return [];
        }
        return array_map(function ($estimatedPrice) {
            return ['id' => $estimatedPrice['id'], 'price' => $estimatedPrice['price']['amount']];
        }, $estimatedPrices);
    }

    private static function calcPricesBySize($carriers, $orderInfo, $products)
    {
        $prices = [];
        foreach ($carriers as $carrier) {
            $size = UnisendShippingSizeService::resolveSize(['orderInfo' => $orderInfo, 'unisendCarrier' => $carrier, 'products' => $products]);
            $price = self::getPriceBySize($carrier['unisend_shipping_method_id'], $size);
            if ($price && count($price) > 0) {
                $prices[] = ['id' => $carrier['unisend_shipping_method_id'], 'price' => $price['price']];
            }
        }
        return $prices;
    }

    private static function calcPricesByWeight($carriers, $orderInfo, $products)
    {
        $prices = [];
        $orderWeight = $orderInfo['weight'];
        foreach ($carriers as $carrier) {
            $currentPrice = null;
            $weights = self::getWeights($carrier['unisend_shipping_method_id']);
            if ($weights && is_array($weights)) {
                foreach ($weights as $weight) {
                    if ($orderWeight >= (float)$weight['weight_from'] && $orderWeight <= (float)$weight['weight_to'] && (float)$weight['price'] && (!$currentPrice || $currentPrice > (float)$weight['price'])) {
                        $currentPrice = (float)$weight['price'];
                    }
                }
            }
            if ($currentPrice) {
                $prices[] = ['id' => $carrier['unisend_shipping_method_id'], 'price' => $currentPrice];
            }
        }
        return $prices;
    }

    private static function calcPrices($carriers, $orderInfo, $totalAmount, $products)
    {
        $freeShippingCarriers = array_filter($carriers, function ($carrier) use ($totalAmount) {
            return isset($carrier['free_shipping_from']) && (float)$carrier['free_shipping_from'] <= $totalAmount;
        });
        $prices = [];
        $freeShipping = array_map(function ($carrier) {
            return ['id' => $carrier['unisend_shipping_method_id'], 'price' => 0.0];
        }, $freeShippingCarriers);

        $prices = array_merge($freeShipping, $prices);

        $carriersToProcess = array_filter($carriers, function ($carrier) use ($freeShippingCarriers) {
            return array_search($carrier['unisend_shipping_method_id'], array_column($freeShippingCarriers, 'unisend_shipping_method_id')) === false;
        });

        if (!empty($carriersToProcess)) {
            $carriersByCarrier = array_filter($carriersToProcess, function ($carrier) {
                return $carrier['rate_type'] === 'carrier';
            });
            if (!empty($carriersByCarrier)) {
                $carrierPrices = self::calcPricesByCarrier($carriersByCarrier, $orderInfo, $products);
                $prices = array_merge($prices, $carrierPrices);
            }

            $carriersBySize = array_filter($carriersToProcess, function ($carrier) {
                return $carrier['rate_type'] === 'size';
            });
            if (!empty($carriersBySize)) {
                $sizePrices = self::calcPricesBySize($carriersBySize, $orderInfo, $products);
                $prices = array_merge($prices, $sizePrices);
            }

            $carriersByWeight = array_filter($carriersToProcess, function ($carrier) {
                return $carrier['rate_type'] === 'weight';
            });
            if (!empty($carriersByWeight)) {
                $weightPrices = self::calcPricesByWeight($carriers, $orderInfo, $products);
                $prices = array_merge($prices, $weightPrices);
            }
        }
        return $prices;
    }

    public static function getAllCarriers()
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method where is_deleted IS NULL OR is_deleted = false")->rows;
    }

    public static function getCarriersByCountry($countryCode)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method shipping_method INNER JOIN " . DB_PREFIX . "unisend_shipping_method_countries country on country.unisend_shipping_method_id = shipping_method.unisend_shipping_method_id WHERE (shipping_method.is_deleted = false OR isnull(shipping_method.is_deleted)) AND (country.code = '" . $countryCode . "' OR  country.code = 'ALL')")->rows;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingCarrierService();
        }
        return self::$instance;
    }

    public static function getShippingMethod($id)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method WHERE unisend_shipping_method_id =" . $id)->row;
    }

    public static function create($name, $planCode, $parcelType, $rateType, $sizes, $weights, $freeShippingFrom, $countries)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $freeShippingParam = is_numeric($freeShippingFrom) ? $db->escape($freeShippingFrom) : 'NULL';
        $db->query("INSERT INTO " . DB_PREFIX . "unisend_shipping_method SET title = '" . $db->escape($name) . "', plan_code = '" . $db->escape($planCode) . "', parcel_type='" . $db->escape($parcelType) . "', rate_type='" . $db->escape($rateType) . "', free_shipping_from = " . $freeShippingParam);

        $methodId = $db->getLastId();

        self::insertSizes($methodId, $sizes);
        self::insertWeights($methodId, $weights);
        self::insertCountries($methodId, $countries);
    }

    public static function update($id, $name, $planCode, $parcelType, $rateType, $sizes, $weights, $freeShippingFrom, $countries)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $freeShippingParam = is_numeric($freeShippingFrom) ? $db->escape($freeShippingFrom) : 'NULL';

        $db->query("UPDATE " . DB_PREFIX . "unisend_shipping_method SET title = '" . $db->escape($name) . "', plan_code = '" . $db->escape($planCode) . "', parcel_type='" . $db->escape($parcelType) . "', rate_type='" . $db->escape($rateType) . "', free_shipping_from = " . $freeShippingParam . " WHERE unisend_shipping_method_id=" . $id);

        $db->query("DELETE FROM " . DB_PREFIX . "unisend_shipping_method_sizes WHERE unisend_shipping_method_id=" . $db->escape($id));
        $db->query("DELETE FROM " . DB_PREFIX . "unisend_shipping_method_weights WHERE unisend_shipping_method_id=" . $db->escape($id));
        $db->query("DELETE FROM " . DB_PREFIX . "unisend_shipping_method_countries WHERE unisend_shipping_method_id=" . $db->escape($id));

        self::insertSizes($id, $sizes);
        self::insertWeights($id, $weights);
        self::insertCountries($id, $countries);
    }

    public static function delete($id)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $db->query("UPDATE " . DB_PREFIX . "unisend_shipping_method SET is_deleted = 1 WHERE unisend_shipping_method_id=" . $db->escape($id));
    }

    public static function getCountries($id)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method_countries WHERE unisend_shipping_method_id='" . $id . "'")->rows;
    }

    public static function getWeights($id)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method_weights WHERE unisend_shipping_method_id='" . $id . "'")->rows;
    }

    public static function insertCountries($id, $countries)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        foreach ($countries as $country) {
            $db->query("INSERT INTO " . DB_PREFIX . "unisend_shipping_method_countries SET code = '" . $db->escape($country) . "', unisend_shipping_method_id='" . $id . "'");
        }
    }

    public static function getSizes($id)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT * FROM " . DB_PREFIX . "unisend_shipping_method_sizes WHERE unisend_shipping_method_id='" . $id . "'")->rows;
    }

    public static function getPriceBySize($id, $size)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        return $db->query("SELECT price FROM " . DB_PREFIX . "unisend_shipping_method_sizes WHERE unisend_shipping_method_id='" . $id . "' AND size='" . $size . "'")->row;
    }

    public static function insertSizes($id, $sizes)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        foreach ($sizes as $key => $value) {
            $db->query("INSERT INTO " . DB_PREFIX . "unisend_shipping_method_sizes SET size = '" . $db->escape($key) . "', price = '" . $db->escape($value) . "', unisend_shipping_method_id='" . $id . "'");
        }
    }

    public static function insertWeights($id, $weights)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        foreach ($weights as $weight) {
            $db->query("INSERT INTO " . DB_PREFIX . "unisend_shipping_method_weights SET weight_from = '" . $db->escape($weight['from']) . "', weight_to = '" . $db->escape($weight['to']) . "', price = '" . $db->escape($weight['price']) . "', unisend_shipping_method_id='" . $id . "'");
        }
    }

    public static function updateSortOrder(int $id, $sortOrder)
    {
        $db = UnisendShippingContextHolder::getInstance()->getDb();
        $sortOrder = !empty($sortOrder) && !is_nan($sortOrder) ? $db->escape($sortOrder) : NULL;
        $sqlFormat = "UPDATE %sunisend_shipping_method SET sort_order = %d WHERE unisend_shipping_method_id=%s";
        $sql = sprintf($sqlFormat, DB_PREFIX, $sortOrder, $id);
        $db->query($sql);
    }

    private static function filterAvailableCarriers($carriers, $orderInfo, $products)
    {
        $items = array_map(function ($carrier) use ($orderInfo, $products) {
            return [
                'id' => (int)$carrier['unisend_shipping_method_id'],
                'receiverCountryCode' => $orderInfo['shipping_iso_code_2'],
                'receiverPostalCode' => $orderInfo['shipping_postcode'],
                'size' => UnisendShippingSizeService::resolveSize(['orderInfo' => $orderInfo, 'unisendCarrier' => $carrier, 'products' => $products]),
                'weight' => $orderInfo['weight'],
                'planCode' => $carrier['plan_code'],
                'parcelType' => $carrier['parcel_type'],
                //'sort_order' => isset($carrier['sort_order']) ? $carrier['sort_order'] : null,
            ];
        }, $carriers);

        $request = [
            'items' => $items,
            'includeErrors' => true
        ];

        $availableCarriers = UnisendShippingApi::getShippingAvailability($request);

        if (!UnisendShippingRequestErrorHandler::getInstance()->isRequestCompletedSuccessfully($availableCarriers)) {
            return [];
        }

        return array_filter($carriers, function ($carrier) use ($availableCarriers) {
            return current(array_filter(get_object_vars($availableCarriers), function ($element) use ($carrier) {
                return $element->id == $carrier['unisend_shipping_method_id'];
            }))->available;
        });
    }
}

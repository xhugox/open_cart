<?php

namespace unisend_shipping\services;


use unisend_shipping\api\UnisendTerminalApi;

/**
 * Singleton class
 */
class UnisendShippingTerminalService
{

    private static $instance = null;

    public function getTerminalsData($controller, $countryCode)
    {
        $cacheKey = 'unisend_shipping_terminals.' . $countryCode;
        $cachedData = $controller->cache->get($cacheKey);

        if ($cachedData) {
            $expireTime = $cachedData['expireTime'];
            if ($expireTime && time() > $expireTime) {
                $controller->cache->delete($cacheKey);
            } else {
                if (isset($cachedData['terminalsData']) && !empty($cachedData['terminalsData']['terminals'])) {
                    return $cachedData['terminalsData'];
                }
                return;
            }
        }
        $terminals = $this->getTerminalsByCountryCode($countryCode);

        $terminalsData['terminals'] = $terminals;
        $terminalsData['translations'] = ['text_shipping_unisend_shipping_checkout_select_parcel_locker_placeholder'=> $controller->language->get('text_shipping_unisend_shipping_checkout_select_parcel_locker_placeholder')];
        $cacheData['terminalsData'] = $terminalsData;
        $cacheData['expireTime'] = time() + (30 * 60);

        $controller->cache->set($cacheKey, $cacheData);

        return $terminalsData;
    }

    public function getTerminalsByCountryCode(string $shippingCountryCode): array
    {
        $formattedList = [];

        // Terminal cities at top
        $topList = [
            'Vilnius',
            'Kaunas',
            'Klaipėda',
            'Šiauliai',
            'Panevežys',
            'Alytus',
            'Marijampolė',
            'Utena',
            'Telšiai',
            'Tauragė'
        ];

        $terminals = UnisendTerminalApi::getTerminals($shippingCountryCode) ?? [];

        foreach ($terminals as $terminal) {
            // Add city groups
            if (!array_key_exists($terminal->city, $formattedList)) {
                $formattedList [$terminal->city] = [];
            }

            // Formatted grouped list by city
            $formattedList[$terminal->city][$terminal->id]['id'] = $terminal->id;
            $formattedList[$terminal->city][$terminal->id]['name'] = $terminal->name;
            $formattedList[$terminal->city][$terminal->id]['address'] = $terminal->address;
        }

        // Sort terminals alphabetically
        foreach ($formattedList as $key => $list) {
        usort($formattedList[$key], function ($a, $b) {
                $nameComparison = strcmp(trim($a['name']), trim($b['name']));
                if ($nameComparison !== 0) {
                    return $nameComparison;
                }

                return $a['address'] <=> $b['address'];
            });
        }

        // Top sort cities
        $ordered = [];

        foreach ($topList as $key) {
            if (array_key_exists($key, $formattedList)) {
                $ordered [$key] = $formattedList [$key];
                // Unset top listed cities
                unset ($formattedList [$key]);
            }
        }

        // Sort cities alphabetically
        ksort($formattedList);

        // Concat
        $formattedList = $ordered + $formattedList;

        $result = [];

        foreach ($formattedList as $city => $cityTerminals) {
            if ($city) {
                $result[] = (object)[
                    'name' => $city,
                    'terminals' => array_values($cityTerminals)
                ];
            }
        }

        return $result;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingTerminalService();
        }
        return self::$instance;
    }
}

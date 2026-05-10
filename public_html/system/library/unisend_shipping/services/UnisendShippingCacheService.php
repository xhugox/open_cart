<?php

namespace unisend_shipping\services;


use unisend_shipping\context\UnisendShippingContextHolder;

/**
 * Singleton class
 */
class UnisendShippingCacheService
{


    private $cache;
    private $datasource;

    private static $instance = null;

    public function __construct()
    {
    }

    public static function save($cacheKey, $data, $expireTime = (30 * 60))
    {
        $instance = UnisendShippingCacheService::getInstance();
        if (!$instance->cache) {
            return false;
        }
        $cacheData['data'] = $data;
        $cacheData['expireTime'] = time() + $expireTime;

        return $instance->cache->set($cacheKey, $cacheData);
    }

    public static function get($cacheKey)
    {
        $instance = UnisendShippingCacheService::getInstance();
        if (!$instance->cache) {
            return null;
        }
        $cachedData = $instance->cache->get($cacheKey);

        if ($cachedData) {
            $expireTime = $cachedData['expireTime'];
            if ($expireTime && time() > $expireTime) {
                $instance->cache->delete($cacheKey);
            } else {
                if (isset($cachedData['data']) && !empty($cachedData['data'])) {
                    return $cachedData['data'];
                }
            }
        }
        return null;
    }

    public static function delete($cacheKey)
    {
        $instance = UnisendShippingCacheService::getInstance();
        if (!$instance->cache) {
            return false;
        }
        return $instance->cache->delete($cacheKey);
    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingCacheService();
            self::$instance->datasource = UnisendShippingContextHolder::getInstance()->getDatasource();
            self::$instance->cache = UnisendShippingContextHolder::getInstance()->getCache();
        }
        return self::$instance;
    }
}

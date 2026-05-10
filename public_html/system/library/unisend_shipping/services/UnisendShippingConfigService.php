<?php

namespace unisend_shipping\services;


use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;

/**
 * Singleton class
 */
class UnisendShippingConfigService
{

    private static $CODE;
    private static $correctCode = false;

    private $datasource;
    private $data = [];

    private static $instance = null;

    public function __construct()
    {
        $this->data = [
            UnisendShippingConst::SETTING_KEY_API_URL => 'https://api-manosiuntos.post.lt/',
            UnisendShippingConst::SETTING_KEY_API_TEST_URL => 'https://api-manosiuntostst.post.lt/'
        ];
        self::$CODE = $this->resolveCode();
    }

    private function resolveCode()
    {
        if (version_compare(VERSION, '3.0.0', '>=')) {
            self::$correctCode = true;
            return 'shipping_unisend_shipping';
        } else {
            return 'unisend_shipping';
        }
    }
    
    public static function updateValue($code, $data, $key = null)
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        if (!property_exists($instance->datasource, 'editSetting')) {
            return null;
        }
        if (self::$correctCode) {
            $code = self::correctCode($code);
            if ($key) {
                $key = self::correctCode($key);
            }
        }
        if (!$key) {
            $key = $code;
        }
        $dataToStore[$key] = $data;
        $instance->datasource->editSetting($code, $dataToStore);
    }

    public static function updateValues($data)
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        if (!property_exists($instance->datasource, 'editSetting')) {
            return null;
        }
        if (self::$correctCode) {
            self::correctDataKey($data);
        }
        $instance->datasource->editSetting(self::$CODE, $data);
    }

    public static function get($key)
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        if (isset($instance->data[$key])) return $instance->data[$key];
        if (self::$correctCode) {
            $key = self::correctCode($key);
        }
        if (!property_exists($instance->datasource, 'getSettingValue')) {
            $setting = $instance->datasource->getSetting($key);
            if ($setting) {
                return $setting[$key];
            }
            return false;
        }
        return $instance->datasource->getSettingValue($key);
    }

    public static function getAll()
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        return $instance->datasource->getSetting(self::$CODE);
    }

    public static function deleteAllByCode($code)
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        if (!property_exists($instance->datasource, 'deleteSetting')) {
            return null;
        }
        if (self::$correctCode) {
            $code = self::correctCode($code);
        }
        $instance->datasource->deleteSetting($code);
    }

    public static function uninstall()
    {
        self::deleteAllByCode(self::$CODE);
    }

    public static function install($data = [])
    {
        $instance = UnisendShippingConfigService::getInstance();
        if (!$instance->datasource) return null;
        if (!property_exists($instance->datasource, 'editSetting')) {
            return null;
        }
        $data = self::getDefaultSettings($data);
        if (self::$correctCode) {
            self::correctDataKey($data);
        }
        $instance->datasource->editSetting(self::$CODE, $data);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingConfigService();
            self::$instance->datasource = UnisendShippingContextHolder::getInstance()->getDatasource();
        }
        return self::$instance;
    }

    private static function getDefaultSettings($data = [])
    {
        return [
            UnisendShippingConst::SETTING_KEY_MODE_LIVE => false,
            UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS => true,
            UnisendShippingConst::SETTING_KEY_COURIER_ENABLED => true,
            UnisendShippingConst::SETTING_KEY_STICKER_LAYOUT => 'LAYOUT_MAX',
            UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_WIDTH => 10,
            UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_LENGTH => 10,
            UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_HEIGHT => 10,
            UnisendShippingConst::SETTING_KEY_DEFAULT_WEIGHT_CLASS_ID => $data['weight_class_id'] ?: '2',
            UnisendShippingConst::SETTING_KEY_DEFAULT_LENGTH_CLASS_ID => $data['length_class_id'] ?: '1',
            UnisendShippingConst::SETTING_KEY_DEFAULT_STATUS_ID_TO_CREATE_PARCEL => $data['status_id_to_create_parcel'],
        ];
    }

    private static function correctCode($code)
    {
        return 'shipping_' . $code;
    }

    private static function correctDataKey(&$data)
    {
        foreach ($data as $key => $dataItem) {
            $modifiedKey = self::correctCode($key);
            $data[$modifiedKey] = $dataItem;
            $data[$key] = null;
        }
    }
}

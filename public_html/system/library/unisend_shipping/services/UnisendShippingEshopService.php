<?php

namespace unisend_shipping\services;


use Exception;
use Throwable;
use unisend_shipping\api\UnisendEshopApi;
use unisend_shipping\cons\UnisendShippingConst;

/**
 * Singleton class
 */
class UnisendShippingEshopService
{

    private static $instance = null;

    public function __construct()
    {

    }

    public static function onInstalled()
    {
        try {
            UnisendEshopApi::install();
        } catch (Exception|Throwable $e) {
            //ignore
        }
    }

    public static function onUninstalled()
    {
        try {
            UnisendEshopApi::uninstall();
        } catch (Exception|Throwable $e) {
            //ignore
        }
    }

    public static function onAuthenticated()
    {
        try {
            UnisendEshopApi::login();
        } catch (Exception|Throwable $e) {
            //ignore
        }
    }

    public static function onUpdate()
    {
        try {
            UnisendEshopApi::update();
        } catch (Exception|Throwable $e) {
            //ignore
        }
    }

    public static function onSettingsSave()
    {
        if (!isset($_POST[UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS])) return;
        $currentStatus = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS);
        $nextStatus = $_POST[UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS];

        if ($currentStatus == $nextStatus) return;
        try {
            if ($nextStatus == false) {
                UnisendEshopApi::deactivate();
            } else if ($nextStatus == true) {
                UnisendEshopApi::activate();
            }
        } catch (Exception|Throwable $e) {
            //ignore
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingEshopService();
        }
        return self::$instance;
    }
}

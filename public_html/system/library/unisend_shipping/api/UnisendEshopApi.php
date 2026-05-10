<?php

namespace unisend_shipping\api;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendEshopApi extends UnisendApi
{

    const BASE_URI = 'eshop/plugin';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function install()
    {
        $request['shop'] = HTTPS_SERVER ?: HTTP_SERVER;
        $request['name'] = 'Opencart';

        $instance = self::getInstance();
        $request['version'] = $instance->getVersion();
        $response = $instance->post(self::BASE_URI, $request);
        return self::handleInstallResponse($response);
    }

    public static function uninstall()
    {
        $request['pluginId'] = self::getPluginId();
        if (!isset($request['pluginId']) || !$request['pluginId']) return;
        $request['name'] = 'UNINSTALL';
        $instance = self::getInstance();
        $instance->post(self::BASE_URI . '/event', $request);
        self::cleanup();
    }

    public static function update()
    {
        $request['pluginId'] = self::getPluginId();
        if (!isset($request['pluginId']) || !$request['pluginId']) return;
        $request['name'] = 'UPDATE';
        $instance = self::getInstance();
        $request['arg'] = $instance->getVersion();
        $instance->post(self::BASE_URI . '/event', $request);
    }

    public static function login()
    {
        $request['pluginId'] = self::getPluginId();
        if (!isset($request['pluginId']) || !$request['pluginId']) return;
        $request['name'] = 'LOGIN';
        $request['arg'] = self::getUsername();
        $instance = self::getInstance();
        $instance->post(self::BASE_URI . '/event', $request);
    }

    public static function activate()
    {
        $pluginId = self::getPluginId();
        if ($pluginId) {
            $request['pluginId'] = $pluginId;
            $request['name'] = 'ACTIVATE';
            $instance = self::getInstance();
            $instance->post(self::BASE_URI . '/event', $request);
        } else {
            self::install();
        }
    }

    public static function deactivate()
    {
        $request['pluginId'] = self::getPluginId();
        if (!isset($request['pluginId']) || !$request['pluginId']) return;
        $request['name'] = 'DEACTIVATE';
        $instance = self::getInstance();
        $instance->post(self::BASE_URI . '/event', $request);
    }

    protected function isAuthRequired()
    {
        return false;
    }

    private static function cleanup()
    {
        UnisendShippingConfigService::deleteAllByCode(UnisendShippingConst::SETTING_KEY_PLUGIN_ID);
    }

    private static function handleInstallResponse($response)
    {
        if ($response && isset($response->id)) {
            UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_PLUGIN_ID, $response->id);
            return $response->id;
        }
        return false;
    }

    private static function getUsername()
    {
        return UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_USERNAME);
    }

    private static function getPluginId()
    {
        $pluginId = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_PLUGIN_ID);
        if (!$pluginId) {
            return self::install();
        }
        return $pluginId;
    }

    private function getVersion()
    {
        $version = VERSION;

        return 'OC: [' . $version . '], unisend: [' . UNISEND_SHIPPING_VERSION . ']';
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendEshopApi();
        }
        return self::$instance;
    }
}

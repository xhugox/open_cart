<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendTerminalApi extends UnisendApi
{
    const TERMINAL_BY_RECEIVER_COUNTRY_CODE = 'terminal?receiverCountryCode=';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getTerminals(string $receiverCountryCode)
    {
        $instance = self::getInstance();
        return $instance->get(self::TERMINAL_BY_RECEIVER_COUNTRY_CODE . $receiverCountryCode);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendTerminalApi();
        }
        return self::$instance;
    }

    public function isResponseAsArray(): bool
    {
        return false;
    }


}

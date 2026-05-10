<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendShippingPlanApi extends UnisendApi
{
    const PLAN_URI = 'shipping/plan';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }


    public static function getPlans()
    {
        $instance = self::getInstance();
        return $instance->get(self::PLAN_URI);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingPlanApi();
        }
        return self::$instance;
    }
}

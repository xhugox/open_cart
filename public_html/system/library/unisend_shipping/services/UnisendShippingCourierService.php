<?php

namespace unisend_shipping\services;


use unisend_shipping\api\UnisendCourierApi;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\repository\UnisendShippingOrderRepository;

/**
 * Singleton class
 */
class UnisendShippingCourierService
{

    private static $instance = null;

    public function __construct()
    {
    }

    public function scheduleNextCourierCall()
    {
        $callCourierActive = $_POST[UnisendShippingConst::SETTING_KEY_COURIER_ENABLED] ?? UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_COURIER_ENABLED) ?? false;
        $cronDaysOfWeek = $_POST[UnisendShippingConst::SETTING_KEY_COURIER_DAYS] ?? $this->safeUnserialize(UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_COURIER_DAYS)) ?? false;
        $cronTime = $_POST[UnisendShippingConst::SETTING_KEY_COURIER_HOUR] ?? UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_COURIER_HOUR) ?? false;
        if (!$callCourierActive || !$cronDaysOfWeek || !$cronTime) {
            $this->cleanScheduledTime();
            return;
        }

        $timeParts = explode(":", $cronTime);
        if (!$timeParts || count($timeParts) != 2) {
            return;
        }

        $nextScheduledTime = $this->getNextScheduledTime($cronDaysOfWeek, intval($timeParts[0]), intval($timeParts[1]));
        if (!$nextScheduledTime) {
            return;
        }
        $this->saveScheduledTime($nextScheduledTime);

    }

    private function getCacheKey()
    {
        return 'UnisendShipping_schedule_call_courier';
    }

    private function getScheduledTime()
    {
        $cacheKey = $this->getCacheKey();
        $cachedScheduledTime = UnisendShippingCacheService::get($cacheKey);
        if ($cachedScheduledTime) return $cachedScheduledTime;
        return UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_COURIER_SCHEDULED_TIME) ?? false;
    }

    private function cleanScheduledTime()
    {
        UnisendShippingConfigService::deleteAllByCode(UnisendShippingConst::SETTING_KEY_COURIER_SCHEDULED_TIME);
        $cacheKey = $this->getCacheKey();
        UnisendShippingCacheService::delete($cacheKey);
    }

    private function saveScheduledTime($nextScheduledTime)
    {
        $cacheKey = $this->getCacheKey();
        UnisendShippingCacheService::save($cacheKey, $nextScheduledTime, $nextScheduledTime);
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_COURIER_SCHEDULED_TIME, $nextScheduledTime);
    }

    public function handleAutoCourierCall()
    {
        $scheduledTime = $this->getScheduledTime();
        if ($scheduledTime && intval($scheduledTime) < time()) {
            $this->cleanScheduledTime();
            $this->scheduleNextCourierCall();
            $pendingCalls = UnisendCourierApi::pendingCall();
            if ($pendingCalls) {
                foreach ($pendingCalls as $courierCall) {
                    if (isset($courierCall['idRef']) && $courierCall['idRef']) {
                        $order = UnisendShippingOrderRepository::getById($courierCall['idRef']);
                        if ($order) {
                            if ($order['status'] === UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED) {
                                $order['status'] = UnisendShippingConst::ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED;
                            } else {
                                $order['status'] = UnisendShippingConst::ORDER_STATUS_COURIER_CALLED;
                            }
                            $order['shippingStatus'] = LpOrderStatus::$COURIER_CALLED->name;
                            UnisendShippingOrderRepository::update($order);
                        }
                    }
                }
            }
        }
    }

    private function findDay($courierDays, $allowSameDay)
    {
        $currentDay = intval(date('w'));
        $earliestDayValue = 7;
        $smallestDaysDiff = 7;
        $nextDay = null;
        foreach ($courierDays as $callCourierDay) {
            $dayValue = intval($callCourierDay);
            if ($currentDay == $dayValue) {
                if ($allowSameDay)
                    return $callCourierDay;
            } else if ($currentDay < $dayValue) {
                $daysDiff = $dayValue - $currentDay;
                if ($daysDiff < $smallestDaysDiff) {
                    $smallestDaysDiff = $daysDiff;
                    $nextDay = $callCourierDay;
                }
            }
            if ($earliestDayValue > $dayValue) {
                $earliestDayValue = $dayValue;
            }
        }
        if ($nextDay == null) {
            return $earliestDayValue;
        }
        return $nextDay;
    }

    public function getNextScheduledTime(array $daysArr, int $callCourierHour, int $callCourierMinute)
    {
        if (empty($daysArr)) return null;
        $currentHour = date('G');
        $currentMinute = date('i');
        $currentDay = date('w');
        $allowSameDay = $currentHour < $callCourierHour || ($currentHour == $callCourierHour && $currentMinute < $callCourierMinute);
        $nextDayValue = $this->findDay($daysArr, $allowSameDay);
        if ($nextDayValue < $currentDay || ($nextDayValue == $currentDay && !$allowSameDay)) {
            $nextDayValue = $nextDayValue + 7;
        }

        $hoursDiff = $callCourierHour - $currentHour;
        $daysDiff = $nextDayValue - $currentDay;
        $minutesDiff = $callCourierMinute - $currentMinute;
        $secondsDiff = (($daysDiff * 24 + $hoursDiff) * 60 + $minutesDiff) * 60;
        return time() + $secondsDiff;
    }

    private function safeUnserialize($value)
    {
        if ($value) {
            return unserialize((string)$value);
        }
        return null;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingCourierService();
        }
        return self::$instance;
    }
}

<?php

namespace unisend_shipping\services;


use Log;
use unisend_shipping\api\UnisendTrackingApi;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\repository\UnisendShippingOrderRepository;

/**
 * Singleton class
 */
class UnisendShippingTrackingService
{

    private static $instance = null;

    private $groupedTrackingEvents;

    /**
     * @param $groupedTrackingEvents
     */
    public function __construct()
    {
        $this->groupedTrackingEvents = $this->createTrackingEventGroup();
    }

    public function subscribe($baseUrl)
    {
        $token = token(rand(10, 18));
        $salt = token(9);
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_TRACKING_TOKEN, $token);
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_TRACKING_TOKEN_SALT, $salt);
        $encodedToken = sha1($salt . sha1($salt . sha1($token)));
        UnisendTrackingApi::configure($baseUrl, $encodedToken);
    }

    public function validateToken($token)
    {
        if (!$token) {
            $this->die('Token required');
        }
        $savedToken = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_TRACKING_TOKEN);
        $salt = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_TRACKING_TOKEN_SALT);
        if (!$savedToken || !$salt) {
            $this->die('Token not saved');
        }
        $encodedToken = sha1($salt . sha1($salt . sha1($savedToken)));
        if ($encodedToken != $token) {
            $this->die('Invalid token');
        }
    }

    private function die($message)
    {
        http_response_code(400);
        die($message);
    }

    private function createTrackingEventGroup()
    {
        $groupedEvents = [];
        $groupedEvents['DELIVERY_TRANSFER2'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['TRANSFERRED_FOR_DELIVERY_COURIER'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_DESTROY'] = LpOrderStatus::$PARCEL_RETURNING;
        $groupedEvents['DELIVERY_TRANSFER'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['RECEIVED_SENDER_LC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['RECEIVED_RECEIVER_LC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EME'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DEPARTED_RECEIVER_LC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['RECEIVED_DELIVERY_POST'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['LABEL_CANCELLED'] = LpOrderStatus::$PARCEL_CANCELED;
        $groupedEvents['LABEL_CREATED'] = LpOrderStatus::$PARCEL_CREATED;
        $groupedEvents['RECEIVED_LC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['RECEIVED_TERMINAL'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['ACCEPTED_TERMINAL'] = LpOrderStatus::$PARCEL_RECEIVED;
        $groupedEvents['RECEIVED_TERMINAL_OUT'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_PARCEL_LOST'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['ORDERS_PARCEL_DEMAND'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_HOLDING'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EXA'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EXB'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EXC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EXD'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EXX'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EDA'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EDB'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_EDC'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EDD'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EDE'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EDF'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['TRANSFERRED_FOR_DELIVERY_POSTMAN'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EDH'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['TRANSIT_EMK'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['TRANSIT_EMJ'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['ACCEPTED'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['RECEIVED'] = LpOrderStatus::$PARCEL_RECEIVED;
        $groupedEvents['DELIVERY_RETURNED'] = LpOrderStatus::$PARCEL_RETURNING;
        $groupedEvents['DELIVERY_UNSUCCESSFUL_DELIVERY'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['NOTIFICATIONS_INFORMED'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_DELIVERED'] = LpOrderStatus::$PARCEL_DELIVERED;
        $groupedEvents['DELIVERY_REFUNDING'] = LpOrderStatus::$PARCEL_RETURNING;
        $groupedEvents['DELIVERY_REDIRECTING'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_STORING'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['HANDED_TO_GOVERNMENT'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['DELIVERY_EXPORTED'] = LpOrderStatus::$ON_THE_WAY;
        $groupedEvents['CUSTOMS_ON_HOLD'] = LpOrderStatus::$ON_THE_WAY;
        return $groupedEvents;
    }

    public function updateTrackingStatus($trackingEvents)
    {
        if ($trackingEvents) {
            $barcodeTrackingEvents = [];
            foreach ($trackingEvents as $trackingEvent) {
                $barcodeTrackingEvents[$trackingEvent['MailBarcode']][] = $trackingEvent;
            }

            foreach ($barcodeTrackingEvents as $barcode => $barcodeEvents) {
                if ($barcode && $barcodeEvents) {
                    $lastEvent = $this->findWithGreatestDate($barcodeEvents);
                    if ($lastEvent) {
                        $status = $this->groupedTrackingEvents[$lastEvent['PublicEventType']];
                        if ($status) {
                            $lpOrder = UnisendShippingOrderRepository::getByBarcode($barcode);
                            if ($lpOrder) {
                                if ($status == LpOrderStatus::$PARCEL_DELIVERED) {
                                    $lpOrder['status'] = UnisendShippingConst::ORDER_STATUS_COMPLETED;
                                } else if ($lpOrder['status'] == UnisendShippingConst::ORDER_STATUS_SAVED || $lpOrder['status'] == UnisendShippingConst::ORDER_STATUS_NOT_SAVED || $lpOrder['status'] == UnisendShippingConst::ORDER_STATUS_NOT_FORMED) {
                                    $lpOrder['status'] = UnisendShippingConst::ORDER_STATUS_FORMED;
                                }
                                if ($status != LpOrderStatus::$PARCEL_CREATED) {
                                    $lpOrder['shippingStatus'] = $status->name;
                                }
                                $lpOrder['updated'] = date('Y-m-d H:i:s');
                                $success = UnisendShippingOrderRepository::update($lpOrder);
                                if (!$success) {
                                    $log = new Log('unisend_shipping.log');
                                    $log->write('Failed to update status by tracking events for order ID -' . $lpOrder['order_id']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function findWithGreatestDate($trackingEvents)
    {
        $maxTime = 0;
        $selectedEvent = null;
        foreach ($trackingEvents as $event) {
            $eventDateValue = $event['eventDate'] ?? $event['EventDate'];
            $eventTime = strtotime($eventDateValue);
            if ($eventTime > $maxTime || $eventTime == $maxTime) {
                $selectedEvent = $event;
                $maxTime = $eventTime;
            }
        }
        return $selectedEvent;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingTrackingService();
        }
        return self::$instance;
    }
}

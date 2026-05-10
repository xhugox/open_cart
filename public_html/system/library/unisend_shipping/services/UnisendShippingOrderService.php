<?php
namespace unisend_shipping\services;

use Exception;
use unisend_shipping\api\request\UnisendIdRefListRequest;
use unisend_shipping\api\request\UnisendParcelRequest;
use unisend_shipping\api\ShippingItemStatus;
use unisend_shipping\api\ShippingStatus;
use unisend_shipping\api\UnisendCourierApi;
use unisend_shipping\api\UnisendParcelApi;
use unisend_shipping\api\UnisendShippingApi;
use unisend_shipping\api\UnisendStickerApi;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\repository\UnisendShippingOrderRepository;
use unisend_shipping\repository\UnisendShippingRequestRepository;
use UnisendShippingRequestErrorHandler;

class LpOrderStatus
{
    public static $PARCEL_CREATED;
    public static $PARCEL_CREATE_PENDING;
    public static $PARCEL_FAILED;
    public static $COURIER_PENDING;
    public static $COURIER_CALLED;
    public static $SHIPPING_INITIATED;
    public static $ON_THE_WAY;
    public static $PARCEL_DELIVERED;
    public static $PARCEL_RECEIVED;
    public static $PARCEL_CANCELED;
    public static $PARCEL_PENDING;
    public static $PARCEL_RETURNING;

    public $value;
    public $name;

    /**
     * @param $value
     * @param $name
     */
    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function toOrderStatus(): string
    {
        return $this->value;
    }

    public function isShippingInitiated(): bool
    {
        return $this == LpOrderStatus::$SHIPPING_INITIATED || $this == LpOrderStatus::$COURIER_PENDING || $this == LpOrderStatus::$COURIER_CALLED || $this == LpOrderStatus::$PARCEL_DELIVERED || $this == LpOrderStatus::$ON_THE_WAY;
    }

    public function isReadyToInitiated(): bool
    {
        return $this == LpOrderStatus::$PARCEL_CREATED;
    }

    public static function findByValue(string $value): ?LpOrderStatus
    {
        return LpOrderStatus::findBy($value, 'value');
    }

    public static function findByName(string $name): ?LpOrderStatus
    {
        return LpOrderStatus::findBy($name, 'name');
    }

    private static function findBy(string $argToFind, string $findByKey): ?LpOrderStatus
    {
        $allStatuses = [self::$PARCEL_CREATED, self::$PARCEL_FAILED, self::$COURIER_PENDING, self::$COURIER_CALLED, self::$SHIPPING_INITIATED, self::$ON_THE_WAY, self::$PARCEL_DELIVERED, self::$PARCEL_CANCELED];
        $foundValue = array_search($argToFind, array_column($allStatuses, $findByKey));

        if ($foundValue !== false) {
            return $allStatuses[$foundValue];
        }
        return null;
    }
}

LpOrderStatus::$PARCEL_CREATED = new LpOrderStatus("lp-parcel-created", "PARCEL_CREATED");
LpOrderStatus::$PARCEL_CREATE_PENDING = new LpOrderStatus("lp-parcel-pending", "PARCEL_CREATE_PENDING");
LpOrderStatus::$PARCEL_FAILED = new LpOrderStatus("lp-parcel-failed", "PARCEL_FAILED");
LpOrderStatus::$COURIER_PENDING = new LpOrderStatus("lp-courier-await", "COURIER_PENDING");
LpOrderStatus::$COURIER_CALLED = new LpOrderStatus("lp-courier-called", "COURIER_CALLED");
LpOrderStatus::$SHIPPING_INITIATED = new LpOrderStatus("lp-label-created", "SHIPPING_INITIATED");
LpOrderStatus::$ON_THE_WAY = new LpOrderStatus("lp-on-the-way", "ON_THE_WAY");
LpOrderStatus::$PARCEL_DELIVERED = new LpOrderStatus("lp-delivered", "PARCEL_DELIVERED");
LpOrderStatus::$PARCEL_RECEIVED = new LpOrderStatus("lp-received", "PARCEL_RECEIVED");
LpOrderStatus::$PARCEL_CANCELED = new LpOrderStatus("lp-cancelled", "PARCEL_CANCELED");
LpOrderStatus::$PARCEL_PENDING = new LpOrderStatus("lp-parcel-await", "PARCEL_PENDING");
LpOrderStatus::$PARCEL_RETURNING = new LpOrderStatus("lp-parcel-returning", "PARCEL_RETURNING");

class LpOrderActionErrorKey
{
    const FAILED_INITIATE = "Failed to initiate shipping";
    const ACTION_NOT_AVAILABLE = "Action is not available";
}

class LpOrderAction
{
    const CALL_COURIER = "CALL_COURIER";
    const PRINT_MANIFEST = "PRINT_MANIFEST";
    const INIT_SHIPPING = "INIT_SHIPPING";
    const CANCEL_SHIPPING = "CANCEL_SHIPPING";
    const CREATE_PARCEL = "CREATE_PARCEL";
    const DELETE_PARCEL = "DELETE_PARCEL";
    const PRINT_LABEL = "PRINT_LABEL";
    const NONE = "NONE";
}


/**
 * UnisendShippingOrderService is for helper methods with order service
 */
class UnisendShippingOrderService
{
    const LABEL_NOT_CREATED_ERROR = 'Label has not been created yet';

    private $errors = [];
    private static $instance = null;

    public function __construct()
    {
    }


    /**
     * Check if order is initiated by order status
     *
     * @param string $orderStatus
     *
     * @return bool
     */
    public function isShipmentFormed($orderStatus)
    {
        if (
            $orderStatus == UnisendShippingConst::ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED ||
            $orderStatus == UnisendShippingConst::ORDER_STATUS_COURIER_CALLED ||
            $orderStatus == UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED ||
            $orderStatus == UnisendShippingConst::ORDER_STATUS_FORMED
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if order is saved
     *
     * @param string $orderStatus
     *
     * @return bool
     */
    public function isParcelCreated($orderStatus)
    {
        return $orderStatus != UnisendShippingConst::ORDER_STATUS_NOT_SAVED;
    }


    /**
     * Check if user is available to call courier
     *
     * @param array $order UnisendShippingConst
     *
     * @return bool
     */
    public function isCallCourierAvailable(array $order)
    {
        return $this->isOrderActionAvailable($order, LpOrderAction::CALL_COURIER);
    }

    /**
     * @param string $action LpOrderAction
     * @param array $orders
     * @return array[] validOrders, invalidOrdersIds
     */
    public function validateAction(string $action, array $orders): array
    {
        $invalidOrdersIds = [];
        $validOrders = [];
        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            if (!$this->isOrderActionAvailable($order, $action)) {
                $invalidOrdersIds[] = $orderId;
            } else {
                $validOrders[] = $order;
            }
        }
        return [
            'validOrders' => $validOrders,
            'invalidOrdersIds' => $invalidOrdersIds
        ];
    }


    /**
     * Checks if documents can be printed
     *
     * @param array $order
     *
     * @return bool
     */
    public function canPrintManifest(array $order)
    {
        if ($this->isOrderActionAvailable($order, LpOrderAction::PRINT_MANIFEST)) {
            return true;
        }

        return false;
    }


    /**
     * Save UnisendShipping order information
     *
     * @param array $orderData
     *
     * @return Tools::redirectAdmin back
     */
    public function saveUnisendShippingOrder(array $orderData)
    {
        $parcelRequest = UnisendParcelRequest::fromOrderData($orderData);
        if (is_array($orderData) && key_exists('parcel_id', $orderData) && $orderData['parcel_id']) {
            unset($parcelRequest->overwriteIdRef);
            unset($parcelRequest->idRef);
            $parcelResponse = UnisendParcelApi::updateParcel($orderData['order_id'], $parcelRequest);
        } else {
            $parcelRequest->overwriteIdRef = true;
            $parcelRequest->source = 'opencart';
            $parcelResponse = UnisendParcelApi::createParcel($parcelRequest);
        }

        if (!$this->isRequestSuccessful($parcelResponse)) {
            return $parcelResponse;
        }

        if ($parcelResponse) {
            if ($parcelResponse->parcelId) {
                $orderData['parcelId'] = strval($parcelResponse->parcelId);
            }

            $orderData['status'] = UnisendShippingConst::ORDER_STATUS_SAVED;
            $orderData['shippingStatus'] = LpOrderStatus::$PARCEL_CREATED->name;
            $orderData['order_id'] = $orderData['orderInfo']['order_id'];
            UnisendShippingOrderRepository::update($orderData);
        }

        return true;
    }

    public function createParcels(array $orderIds, &$controller)
    {
        $errors = [];
        foreach ($orderIds as $orderId) {
            $createParcelResult = $this->createParcel($orderId, $controller);
            if ($createParcelResult !== true) {
                $errors[UnisendShippingRequestErrorHandler::getInstance()->toErrorMessage($createParcelResult)][] = $orderId;
            }
        }
        return $errors;
    }

    public function createParcel($orderId, &$controller)
    {
        try {
            $controller->load->model('sale/order');
            $orderInfo = $controller->model_sale_order->getOrder($orderId);
            $products = $controller->model_sale_order->getOrderProducts($orderId);
        } catch (Exception $exception) {
            $controller->load->model('checkout/order');
            $orderInfo = $controller->model_checkout_order->getOrder($orderId);
            $products = $controller->model_checkout_order->getOrderProducts($orderId);
        }
        $controller->load->model('catalog/product');

        $productsInfoList = [];
        foreach ($products as $product) {
            $productInfo = $controller->model_catalog_product->getProduct($product['product_id']);
            if ($productInfo) {
                $productsInfoList[] = $productInfo;
            }
        }
        if ($_REQUEST['route'] === 'extension/shipping/unisend_shipping/orders' || $_REQUEST['route'] === 'api/order/history') {
            $savedOrder = UnisendShippingOrderRepository::getById($orderId);
            $orderInfo['weight'] = $savedOrder['weight'];
            $orderInfo['size'] = $savedOrder['size'];
            $orderInfo['partCount'] = $savedOrder['part_count'];
            $orderInfo['codSelected'] = $savedOrder['cod_selected'];
            $orderInfo['codAmount'] = $savedOrder['cod_amount'];
            $orderInfo['parcelType'] = $savedOrder['parcel_type'];
            $orderInfo['planCode'] = $savedOrder['plan_code'];
            if ($orderInfo['planCode'] === 'TERMINAL' && !isset($orderData['unisend_selected_terminal_id'])) {
                $orderData['unisend_selected_terminal_id'] = $savedOrder['terminal_id'];
            }
        } else {
            $orderInfo['weight'] = isset($_REQUEST['weight']) && is_numeric($_REQUEST['weight']) ? $_REQUEST['weight'] * 1000 : null;
            $orderInfo['size'] = $_REQUEST['size'];
            $orderInfo['partCount'] = $_REQUEST['partCount'];
            $orderInfo['codSelected'] = $_REQUEST['codSelected'];
            $orderInfo['codAmount'] = $_REQUEST['codAmount'];
            $orderInfo['parcelType'] = $_REQUEST['parcel_type'];
            $orderInfo['planCode'] = $_REQUEST['plan_code'];
            if ($orderInfo['planCode'] === 'TERMINAL' && !isset($orderData['unisend_selected_terminal_id'])) {
                if (isset($_REQUEST['terminalId'])) {
                    $orderData['unisend_selected_terminal_id'] = $_REQUEST['terminalId'];
                    $orderData['terminal'] = $_REQUEST['terminal'] ?? null;
                } else {
                    $savedOrder = UnisendShippingOrderRepository::getById($orderId);
                    $orderData['unisend_selected_terminal_id'] = $savedOrder['terminal_id'];
                }
            }
        }
        $orderData['orderInfo'] = $orderInfo;
        $orderData['orderProducts'] = $products;
        $orderData['products'] = $productsInfoList;

        return $this->saveUnisendShippingOrder($orderData);
    }

    public function toObject($arr)
    {
        if (is_array($arr)) {
            return (object)array_map([$this, 'toObject'], $arr);
        }
        return $arr;
    }

    public function getOrderWeight($orderData)
    {
        $totalWeight = 0;
        foreach ($orderData['products'] as $product) {
            $virtual = isset($product['shipping']) && $product['shipping'] != true;
            if ($virtual) continue;
            $quantity = isset($product['total']) && (isset($product['weight']) && $product['weight']) ? 1 : $product['quantity'];
            $weight = $this->getProductWeight($product, $quantity) ?? 1;
            $totalWeight += $weight;
        }
        return max($totalWeight, 1);
    }


    public function getProductWeight($product, $quantity)
    {
        $minWeight = 1;
        $weightConverter = UnisendShippingContextHolder::getInstance()->getWeight();
        $weight = $product['weight'];
        $weight = $weightConverter->convert($weight, $product['weight_class_id'], UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_WEIGHT_CLASS_ID) ?: 2);
        $weightInGrams = max($weight, $minWeight);
        return $weightInGrams * max($quantity, 1);
    }

    public function handleInitiateShipping($orderIds, &$controller)
    {
        $orders = UnisendShippingOrderRepository::getByIds($orderIds);
        $ordersToCreate = array_filter($orders, function ($order) {
            return $order['status'] === UnisendShippingConst::ORDER_STATUS_NOT_SAVED;
        });
        $errors = [];
        $orderIdsToProcess = $orderIds;
        if (!empty($ordersToCreate)) {
            $ordersToCreateIds = array_column($ordersToCreate, 'order_id');
            $creteParcelsResult = $this->createParcels($ordersToCreateIds, $controller);
            if (!empty($creteParcelsResult)) {
                $errors = $creteParcelsResult;
                $orderIdsToProcess = array_filter($orderIdsToProcess, function ($orderId) use ($creteParcelsResult) {
                    return !in_array($orderId, array_merge(...array_values($creteParcelsResult)));
                });
            }
        }

        if (empty($orderIdsToProcess)) {
            UnisendShippingRequestErrorHandler::getInstance()->updateLastError(null);
            return $errors;
        }

        $shippingResponse = UnisendShippingApi::initiate(UnisendIdRefListRequest::from($orderIdsToProcess));
        if (!$this->isRequestSuccessful($shippingResponse)) {
            foreach ($orderIdsToProcess as $orderId) {
                $errors[LpOrderActionErrorKey::FAILED_INITIATE][] = $orderId;
            }
        } else {
            $orderStatuses = $this->onInitiateSuccess($shippingResponse, $this->getShippingOrders($orderIdsToProcess));
            foreach ($orderStatuses as $orderId => $status) {
                if (!ShippingItemStatus::isStatusOk($status)) {
                    $errors[LpOrderActionErrorKey::FAILED_INITIATE][] = $orderId;
                }
            }
        }
        return $errors;
    }

    private function onInitiateSuccess($shippingResponse, $ordersToProcess): array
    {
        $shippingRequestId = $shippingResponse->requestId;
        $shippingStatusResponse = UnisendShippingApi::getStatus($shippingRequestId);
        if ($shippingStatusResponse) {
            UnisendShippingRequestRepository::saveShippingRequest($shippingRequestId, "OK");
            return $this->processShippingStatus($shippingStatusResponse, $ordersToProcess, $shippingRequestId);
        } else {
            UnisendShippingRequestRepository::saveShippingRequest($shippingRequestId, "FAILED");
        }
        return [];
    }

    private function processShippingStatus($shippingStatusResponse, $ordersToProcess, $shippingRequestId): array
    {
        $orderStatuses = [];
        if (ShippingStatus::isStatusOk($shippingStatusResponse->status)) {
            foreach ($shippingStatusResponse->items as $shippingItemStatus) {
                $orderStatuses[$shippingItemStatus->idRef] = $shippingItemStatus->status;
                $orderId = intval($shippingItemStatus->idRef);
                if (ShippingItemStatus::isStatusOk($shippingItemStatus->status)) {
                    $shippingItemOrder = array_search($shippingItemStatus->idRef, array_column($ordersToProcess, 'order_id'));
                    if ($shippingItemOrder !== false) {
                        if ($shippingItemStatus->status == ShippingItemStatus::COURIER_PENDING) {
                            $orderData['shippingStatus'] = LpOrderStatus::$COURIER_PENDING->name;
                        } else if ($shippingItemStatus->status == ShippingItemStatus::COURIER_CALLED) {
                            $orderData['shippingStatus'] = LpOrderStatus::$COURIER_CALLED->name;
                        } else {
                            $orderData['shippingStatus'] = LpOrderStatus::$SHIPPING_INITIATED->name;
                        }
                        $orderData['barcode'] = $shippingItemStatus->barcode;
                        $orderData['requestId'] = $shippingRequestId;
                        $orderData['order_id'] = $orderId;
                        $orderData['status'] = UnisendShippingConst::ORDER_STATUS_FORMED;

                        UnisendShippingOrderRepository::update($orderData);

//                        $psOrder = new Order($orderId);//TODO implement?
//                        $orderCarrier = new OrderCarrier($psOrder->getIdOrderCarrier());
//                        $orderCarrier->tracking_number = $shippingItemStatus->barcode;
//                        $orderCarrier->update();

//                        UnisendShippingTrackingRepository::create($orderId, $shippingItemStatus->barcode);//TODO implement?
                    }
                } else {
                    $orderData['status'] = UnisendShippingConst::ORDER_STATUS_NOT_FORMED;
                    $orderData['order_id'] = $orderId;
                    UnisendShippingOrderRepository::update($orderData);
                }
            }
        }
        return $orderStatuses;
    }

    private function getShippingOrders($orderIds): array
    {
        return UnisendShippingOrderRepository::getByIds($orderIds);
    }

    public function parcelIdExists($orderData)
    {
        if (is_array($orderData) && key_exists('parcel_id', $orderData) && !empty(trim($orderData['parcel_id']))) {
            return true;
        }
        return false;
    }

    /**
     * Initiate shipping process in LP API, it differs from one item initialization because there can be many items initialized at once
     *
     * @param array $orderIds
     *
     * @return array|true|string
     */
    public function formShipmentByIds(array $orderIds, &$controller)
    {
        $errors = $this->handleInitiateShipping($orderIds, $controller);
        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }

    public function groupBy(array $array, string $key)
    {
        $newArray = [];

        foreach($array as $item) {
            $newArray[$item[$key]] = $item;
        }

        return $newArray;
    }

    /**
     * Cancel initiated shipment in LP API
     *
     * @param string $orderId
     */
    public function cancelInitiatedShipping($orderId)
    {
        $order = UnisendShippingOrderRepository::getById($orderId);
        if ($this->parcelIdExists($order)) {
            UnisendShippingApi::cancel([$orderId]);

            $order['parcel_id'] = '';
            $order['status'] = UnisendShippingConst::ORDER_STATUS_NOT_SAVED;
            $order['shippingStatus'] = LpOrderStatus::$PARCEL_CANCELED->name;
            $order['barcode'] = '';
            UnisendShippingOrderRepository::update($order);
        }
    }

    /**
     * Cancel initiated shipment in LP API
     *
     * @param array $orderIds
     */
    public function cancelInitiatedShippingBulk(array $ids)
    {
        foreach ($ids as $id) {
            $this->cancelInitiatedShipping($id);
        }
    }

    public function deleteOrders(array $ids)
    {
        $orderIdsToDelete = [];
        $orders = UnisendShippingOrderRepository::getByIds($ids);
        foreach ($orders as $order) {
            if ($this->isOrderActionAvailable($order, LpOrderAction::DELETE_PARCEL)) {
                $orderIdsToDelete[] = $order['order_id'];
            }
        }
        if (!empty($orderIdsToDelete)) {
            UnisendShippingOrderRepository::deleteByIds($orderIdsToDelete);
        }
    }

    /**
     * Call courier bulk process in LP API
     *
     * @param array $ids
     */
    public function handleCallCourier(array $ids)
    {
        $orders = UnisendShippingOrderRepository::getByIds($ids);
        $ordersToProcess = [];
        $errors = [];
        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            if ($this->isCallCourierAvailable($order)) {
                $ordersToProcess[] = $order;
            } else {
                $errors[LpOrderActionErrorKey::ACTION_NOT_AVAILABLE][] = $orderId;
            }
        }
        if (!empty($ordersToProcess)) {
            $idsToProcess = array_column($ordersToProcess, 'order_id');
            $result = UnisendCourierApi::call($idsToProcess);
            if (!$this->isRequestSuccessful($result)) {
                return $errors;
            }
            foreach ($ordersToProcess as $order) {
                if ($order['status'] === UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED) {
                    $order['status'] = UnisendShippingConst::ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED;
                } else {
                    $order['status'] = UnisendShippingConst::ORDER_STATUS_COURIER_CALLED;
                }
                $order['shippingStatus'] = LpOrderStatus::$COURIER_CALLED->name;
                $order['order_id'] = $orderId;
                UnisendShippingOrderRepository::update($order);
            }
        }
        return $errors;
    }

    /**
     * Print labels if they are present in order, if not return order ids which does not have label
     *
     * @param array $orderIds
     *
     * @return readfile>download>exit|null
     */
    public function printLabels(array $orderIds, bool $includeCn = true, bool $includeManifest = false)
    {
        if ($this->downloadLabels($orderIds, $includeCn, $includeManifest)) {
            $orders = UnisendShippingOrderRepository::getByIds($orderIds);
            foreach ($orders as $order) {
                if ($order['status'] === UnisendShippingConst::ORDER_STATUS_COMPLETED || $order['status'] === UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED) {
                    continue;
                }
                $order['status'] = UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED;
                UnisendShippingOrderRepository::update($order);
            }
        }
    }


    /**
     * Print manifest (printable for couriers delivery type only)
     *
     * @param array $orderData
     *
     * @return readfile>download>exit|null
     */
    public function printManifests(array $orderIds)
    {
        UnisendCourierApi::downloadManifests($orderIds);
        return true;
    }

    /**
     * download and save labels if they are present in order, if not return order ids which does not have label
     *
     * @param array $orderIds
     *
     * @return string|array path to file or missing labels array
     */
    private function downloadLabels(array $orderIds, bool $includeCn, bool $includeManifest)
    {
        return UnisendStickerApi::downloadStickersPdf($orderIds, $includeCn, $includeManifest);
    }

    /**
     * Save file to hard disk
     *
     * @param string $path - path to directory where file will be stored with ending right slash
     * @param string $fileName only file name
     * @param string $content content to write to file
     */
    public function saveFile($path, $fileName, $content)
    {
        if (file_exists($path . $fileName)) {
            unlink($path . $fileName);
        }

        // open and write to it
        $fHandle = fopen($path . $fileName, 'w');
        fwrite($fHandle, $content);
        fclose($fHandle);
    }

    /**
     * Is Request from LP API successful
     *
     * @return bool
     */
    public function isRequestSuccessful($result)
    {
        return UnisendShippingRequestErrorHandler::getInstance()->isRequestCompletedSuccessfully($result);
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        $errors = $this->errors;
        unset($this->errors);

        return $errors;
    }

    public function isOrderActionAvailable($order, string $action)
    {
        $availableActions = $this->getAvailableOrderActions($order);
        return in_array($action, $availableActions);
    }

    public function isOrderActionAvailableById($orderId, string $action)
    {
        $order = UnisendShippingOrderRepository::getById($orderId);
        return $this->isOrderActionAvailable($order, $action);
    }

    public function getAvailableOrderActionsById($orderId): array
    {
        $order = UnisendShippingOrderRepository::getById($orderId);
        return $this->getAvailableOrderActions($order);
    }

    public function isActionAvailable(string $action, array $availableActions): bool
    {
        return in_array($action, $availableActions);
    }

    public function getAvailableOrderActions($order): array
    {
        $actions = [];
        if (isset($order['barcode']) && $order['barcode']) {
            $shippingStatusValue = $order['shipping_status'];
            $orderStatus = $order['status'];
            $actions[] = LpOrderAction::PRINT_LABEL;
            $lpOrderStatus = $shippingStatusValue ? LpOrderStatus::findByName($shippingStatusValue) : LpOrderStatus::findByName($orderStatus);
            if ($lpOrderStatus) {
                if ($lpOrderStatus == LpOrderStatus::$COURIER_PENDING) {
                    $actions[] = LpOrderAction::CALL_COURIER;
                } else if ($lpOrderStatus == LpOrderStatus::$COURIER_CALLED || $orderStatus == UnisendShippingConst::ORDER_STATUS_COURIER_CALLED || $orderStatus == UnisendShippingConst::ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED) {//TODO save courier_called_date?
                    $actions[] = LpOrderAction::PRINT_MANIFEST;
                }
                if ($lpOrderStatus != LpOrderStatus::$ON_THE_WAY && $lpOrderStatus != LpOrderStatus::$PARCEL_DELIVERED && $lpOrderStatus != LpOrderStatus::$PARCEL_CANCELED) {
                    $actions[] = LpOrderAction::CANCEL_SHIPPING;
                }
            }
        } else {
            if (isset($order['id_lp_internal_order']) && $order['id_lp_internal_order']) {
                $actions[] = LpOrderAction::INIT_SHIPPING;
            } else {
                $actions[] = LpOrderAction::CREATE_PARCEL;
            }
            $actions[] = LpOrderAction::DELETE_PARCEL;
        }
        return $actions;
    }

    public function getOrders($filterData, &$controller)
    {
        $results = UnisendShippingOrderRepository::getOrders($filterData);
        if (empty($results)) {
            return [];
        }
        $controller->load->model('sale/order');

        foreach ($results as &$result) {
            $orderInfo = $controller->model_sale_order->getOrder($result['order_id']);
            $result['shopOrder'] = $orderInfo ?? [];
        }
        return $results;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingOrderService();
        }
        return self::$instance;
    }
}
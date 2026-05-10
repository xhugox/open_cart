<?php

namespace unisend_shipping\services;

use Exception;
use Throwable;
use unisend_shipping\api\request\UnisendParcelRequest;
use unisend_shipping\api\UnisendEstimateShippingApi;
use unisend_shipping\api\UnisendParcelApi;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\repository\UnisendShippingOrderRepository;
use UnisendShippingRequestErrorHandler;

class UnisendShippingOCMODService
{
    public static function addMenu(&$data, &$controller)
    {
        if (!$controller->user->hasPermission('access', 'extension/shipping/unisend_shipping')) {
            return;
        }
        if (!self::loadContext($controller)) {
            return;
        }
        $controller->load->language('extension/shipping/unisend_shipping');
        $data['menus'][] = array(
            'id'       => 'menu-unisend',
            'icon'       => 'fa-truck',
            'name'       => $controller->language->get('text_shipping_unisend_shipping_shipping'),
            'href'     => '',
            'children' => [
                [
                    'name' => $controller->language->get('text_order'),
                    'href' => $controller->url->link('extension/shipping/unisend_shipping/orders', self::getTokenParam($controller), true),
                    'children' => [],
                ],
                [
                    'name' => $controller->language->get('text_shipping_unisend_shipping_order_settings'),
                    'href' => $controller->url->link('extension/shipping/unisend_shipping', self::getTokenParam($controller), true),
                    'children' => [],
                ]
            ]
        );
    }

    public static function checkout(&$controller)
    {
        $controller->document->addStyle('https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
        $controller->document->addScript('catalog/view/javascript/unisend_shipping/unisend_shipping.js');
        $controller->document->addStyle('catalog/view/theme/stylesheet/unisend-shipping/unisend_shipping.css');
        $controller->document->addScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js');
    }

    public static function order(&$data, &$controller)
    {
        if (!$controller->user->hasPermission('access', 'extension/shipping/unisend_shipping')) {
            return;
        }
        if (!self::loadContext($controller)) {
            return;
        }
        $orderId = $data['order_id'];

        // Check if there is order id already saved in database
        $order = UnisendShippingOrderRepository::getById($orderId);
        if ($order == null || empty($order)) {
            return;
        }
        $controller->load->language('extension/shipping/unisend_shipping');

        $tokenParam = self::getTokenParam($controller);
        $data['userTokenParam'] = $tokenParam;

        $data['unisendSaveShipmentAction'] = $controller->url->link('extension/shipping/unisend_shipping/unisendOrderAction', 'order_id=' . $orderId . '&' . $tokenParam, true);
        $data['unisendOrderInfoTitle'] = 'UNISEND';

        $lpOrderService = new UnisendShippingOrderService();

        $data['declarations'] = [];
        $data['declarations']['parcelTypes'] = [];

        $lastError = UnisendShippingRequestErrorHandler::getInstance()->getLastError();
        if ($lastError && isset($lastError['message'])) {
            $data['unisendLastError'] = $lastError['message'];
        }


        $parcelCreated = $lpOrderService->isParcelCreated($order['status']);
        $shippingInitiated = $lpOrderService->isShipmentFormed($order['status']);
        if ($parcelCreated === true) {
            $savedParcel = UnisendParcelApi::getParcel($orderId);
            if (is_array($savedParcel) && isset($savedParcel['status_code']) && $savedParcel['status_code'] === 404) {
                UnisendShippingRequestErrorHandler::getInstance()->updateLastError(null);
                return;
            }
            $data['parcelData'] = $savedParcel;
        } else {
            $controller->load->model('sale/order');
            $controller->load->model('catalog/product');

            $orderInfo = $controller->model_sale_order->getOrder($orderId);
            $products = $controller->model_sale_order->getOrderProducts($orderId);
            $productsInfoList = [];
            foreach ($products as $product) {
                $productInfo = $controller->model_catalog_product->getProduct($product['product_id']);
                if ($productInfo) {
                    $productsInfoList[] = $productInfo;
                }
            }
            $unisendCarrier = UnisendShippingCarrierService::getById($order['carrier_id']);

            $orderInfo['weight'] = $order['weight'];
            $orderInfo['size'] = $order['size'];

            $orderData['orderInfo'] = $orderInfo;
            $orderData['orderProducts'] = $products;
            $orderData['products'] = $productsInfoList;
            $orderData['unisendCarrier'] = $unisendCarrier;

            $parcelRequest = UnisendParcelRequest::fromOrderData($orderData);
            $data['parcelData'] = $parcelRequest;
        }
        $parcelData = $data['parcelData'];
        $data['shippingCountryCode'] = $parcelData->receiver->address->countryCode ?? null;
        $data['terminalId'] = $order['terminal_id'] ?? null;
        $data['terminal'] = $order['terminal'] ?? null;
        if (isset($parcelData->parcel->weight)) {
            $data['unisendSavedParcelWeight'] = $parcelData->parcel->weight / 1000.0;
            if (self::isWeightAvailable($parcelData)) {
                $data['unisendParcelWeightAvailable'] = true;
            }
        }
        if (isset($parcelData->parcel->size)) {
            $data['unisendSavedParcelSize'] = $parcelData->parcel->size;
            if (self::isSizeAvailable($parcelData)) {
                $data['unisendParcelSizeAvailable'] = true;
            }
        }
        $planCode = $parcelData->plan->code;
        $data['sizes'] = $planCode == 'TERMINAL' || $planCode == 'HANDS' ? ['XS', 'S', 'M', 'L', 'XL'] : ['XS', 'S', 'M', 'L'];
        $parcelEstimation = self::estimateParcel($parcelData);
        if ($parcelEstimation && count($parcelEstimation) > 0) {
            foreach ($parcelEstimation as $plan) {
                $data['unisendSavedParcelPlanCodes'][$plan['code']] = $controller->language->get('text_shipping_unisend_shipping_plan_' . $plan['code']);
            }
        } else {
            $data['unisendSavedParcelPlanCodes'][$parcelData->plan->code] = $controller->language->get('text_shipping_unisend_shipping_plan_' . $parcelData->plan->code);
        }

        if (self::isTypeChangeAvailable($parcelData)) {
            foreach ($parcelEstimation as $estimatedPlan) {
                foreach ($estimatedPlan['shipping'] as $shipping) {
                    $data['unisendSavedParcelTypes'][$shipping['parcelType']] = $controller->language->get('text_shipping_unisend_shipping_parcel_type_' . $shipping['parcelType']);
                }
            }
        } else {
            $data['unisendSavedParcelTypes'][$parcelData->parcel->type] = $controller->language->get('text_shipping_unisend_shipping_parcel_type_' . $parcelData->parcel->type);
        }

        $cnRequired = $parcelEstimation[0]['shipping'][0]['requirements']['cnDocument'] === true;

        if (self::isMultiPartAvailable($parcelData)) {
            $data['unisendSavedParcelPartCount'] = $parcelData->parcel->partCount ?? 1;
        }
        $data['unisendSavedParcelType'] = $parcelData->parcel->type;
        $data['unisendSavedParcelPlanCode'] = $parcelData->plan->code;
        $planCode = $parcelData->plan->code;
        $data['unisendParcelSizes'] = $planCode == 'TERMINAL' || $planCode == 'HANDS' ? ['XS', 'S', 'M', 'L', 'XL'] : ['XS', 'S', 'M', 'L'];
        $data['unisendCnRequired'] = $cnRequired;

        $data['unisendBarcode'] = $order['barcode'];
        $data['unisendPartCount'] = $order['part_count'];
//        $data['unisendCodAvailable'] = $order['cod_available'];
        $data['unisendCodAvailable'] = true;//TODO implement
        $data['unisendCodAmount'] = $order['cod_amount'];
        $data['unisendCodSelected'] = (bool)$order['cod_selected'];
        $data['unisendParcelId'] = $order['parcel_id'];
        $data['unisendIsShipmentFormed'] = $shippingInitiated;
        $data['unisendIsOrderSaved'] = $parcelCreated;
        $availableActions = $lpOrderService->getAvailableOrderActionsById($data['order_id']);

        if ($shippingInitiated) {
            $data['unisendAreDocumentsPrintable'] = true;
            $data['unisendIsLabelPrintable'] = true;
            $data['unisendIsManifestPrintable'] = $lpOrderService->isActionAvailable(LpOrderAction::PRINT_MANIFEST, $availableActions);
            $data['unisendIsCallCourierAvailable'] = $lpOrderService->isActionAvailable(LpOrderAction::CALL_COURIER, $availableActions);
            $data['unisendIsCancellable'] = $lpOrderService->isActionAvailable(LpOrderAction::CANCEL_SHIPPING, $availableActions);
            unset($parcelData->documents);
        } else {
            $data['unisendIsDeleteAvailable'] = $lpOrderService->isActionAvailable(LpOrderAction::DELETE_PARCEL, $availableActions);
            $data['unisendAreDocumentsPrintable'] = false;
            $data['unisendIsLabelPrintable'] = false;
            $data['unisendIsManifestPrintable'] = false;
            $data['unisendIsCallCourierAvailable'] = false;
            $data['unisendIsCancellable'] = false;
            if ($cnRequired) {
                $data['unisendSavedCnParts'] = (array) $parcelData->documents->cn->parts;
                $data['unisendDeclarationParcelTypes'] = self::getParcelPossibleTypes($order);
            } else {
                unset($parcelData->documents);
            }
        }

        if (isset($_REQUEST['sourcePage']) && $_REQUEST['sourcePage'] === 'unisendShippingOrders') {
            $data['cancel'] = $controller->url->link('extension/shipping/unisend_shipping/orders', self::getTokenParam($controller) . '&activeTab=' . $_REQUEST['activeTab'] ?? 'new-orders', true);
        }
        self::applyText($data, $controller);
        $data['unisendOrderInfoView'] = $controller->load->view('extension/shipping/unisend_shipping_order_info', $data);
    }

    private static function applyText(&$data, $controller)
    {
        $data['text_shipping_unisend_shipping_shipping'] = $controller->language->get('text_shipping_unisend_shipping_shipping');
        $data['text_shipping_unisend_shipping_order_part_count'] = $controller->language->get('text_shipping_unisend_shipping_order_part_count');
        $data['text_shipping_unisend_shipping_order_plan'] = $controller->language->get('text_shipping_unisend_shipping_order_plan');
        $data['text_shipping_unisend_shipping_order_weight'] = $controller->language->get('text_shipping_unisend_shipping_order_weight');
        $data['text_shipping_unisend_shipping_order_parcel_type'] = $controller->language->get('text_shipping_unisend_shipping_order_parcel_type');
        $data['text_shipping_unisend_shipping_order_size'] = $controller->language->get('text_shipping_unisend_shipping_order_size');
        $data['text_shipping_unisend_shipping_order_cod_flag'] = $controller->language->get('text_shipping_unisend_shipping_order_cod_flag');
        $data['text_shipping_unisend_shipping_order_cod_amount'] = $controller->language->get('text_shipping_unisend_shipping_order_cod_amount');
        $data['text_shipping_unisend_shipping_select_yes'] = $controller->language->get('text_shipping_unisend_shipping_select_yes');
        $data['text_shipping_unisend_shipping_select_no'] = $controller->language->get('text_shipping_unisend_shipping_select_no');
        $data['text_shipping_unisend_shipping_button_save'] = $controller->language->get('text_shipping_unisend_shipping_button_save');
        $data['text_shipping_unisend_shipping_button_delete'] = $controller->language->get('text_shipping_unisend_shipping_button_delete');
        $data['text_shipping_unisend_shipping_button_update'] = $controller->language->get('text_shipping_unisend_shipping_button_update');
        $data['text_shipping_unisend_shipping_button_form_shipment'] = $controller->language->get('text_shipping_unisend_shipping_button_form_shipment');
        $data['text_shipping_unisend_shipping_button_cancel_shipment'] = $controller->language->get('text_shipping_unisend_shipping_button_cancel_shipment');
        $data['text_shipping_unisend_shipping_button_print_label'] = $controller->language->get('text_shipping_unisend_shipping_button_print_label');
        $data['text_shipping_unisend_shipping_order_terminals'] = $controller->language->get('text_shipping_unisend_shipping_order_terminals');
        $data['text_shipping_unisend_shipping_button_print_manifest'] = $controller->language->get('text_shipping_unisend_shipping_button_print_manifest');
        $data['text_shipping_unisend_shipping_button_call_courier'] = $controller->language->get('text_shipping_unisend_shipping_button_call_courier');
    }

    public static function estimateParcel($parcel)
    {
        if (!$parcel) return false;
        $requestParams = [];
        $requestParams['planCodes'] = $parcel->plan->code;
        if ($parcel->plan->code == 'TERMINAL') {
            $requestParams['planCodes'] .= ',HANDS';
        }
        $requestParams['size'] = $parcel->parcel->size ?? null;
        $requestParams['weight'] = $parcel->parcel->weight ?? null;
        return UnisendEstimateShippingApi::getPlans($parcel->receiver->address->countryCode, $requestParams);
    }

    /**
     * Get order possible parcel types
     *      * @return array
     */
    public static function getParcelPossibleTypes(array $orderData)
    {
//        $lpOrderService = new UnisendShippingOrderService();

        $types = [
            'GIFT' => 'a Gift',
            'DOCUMENT' => 'a Document',
            'SAMPLE' => 'a Sample of an item',
            'SELL' => 'Goods for sale',
            'RETURN' => 'Goods to be returned',
        ];

//        if ($lpOrderService->getOrderServiceType($orderData) == $lpOrderService::LP_SERVICE) {
//            $types['OTHER'] = $this->l('Other');
//        }

        return $types;
    }

    private static function getToken($controller)
    {
        if (isset($controller->session->data['user_token'])) {
            return $controller->session->data['user_token'];
        }
        return $controller->session->data['token'];
    }

    private static function getTokenParam($controller)
    {
        if (version_compare(VERSION, '3.0.0', '>=')) {
            return 'user_token=' . self::getToken($controller);
        } else {
            return 'token=' . self::getToken($controller);
        }
    }

    private static function isTypeChangeAvailable($parcelResponse): bool
    {
        return $parcelResponse->plan->code == 'TERMINAL';
    }

    private static function isSizeAvailable($parcelResponse): bool
    {
        $planCode = $parcelResponse->plan->code;
        $parcelType = $parcelResponse->parcel->type;
        return $planCode != 'HANDS' || $parcelType == 'T2H';
    }

    private static function isWeightAvailable($parcelResponse): bool
    {
        $planCode = $parcelResponse->plan->code;
        $parcelType = $parcelResponse->parcel->type;
        return $planCode != 'TERMINAL' && $parcelType != 'T2H';
    }

    private static function isMultiPartAvailable($parcelResponse): bool
    {
        $planCode = $parcelResponse->plan->code;
        return $planCode == 'TERMINAL' || $planCode == 'HANDS';
    }

    private static function isCnRequired($parcelResponse, $estimatedPlans): bool
    {
        $parcelContainsCn = $parcelResponse->documents->cn ?? false;
        $cnRequired = null;
        if (!empty($estimatedPlans)) {
            $cnRequired = $estimatedPlans[0]->shipping[0]->requirements->cnDocument === true;
        }
        return $parcelContainsCn && ($cnRequired === null || $cnRequired === true);
    }

    private static function loadContext(&$controller)
    {
        try {
            UnisendShippingContextHolder::load($controller);
        } catch (Exception|Throwable $e) {
            return false;
        }
        return true;
    }
}

<?php

if (!defined('UNISEND_SHIPPING_VERSION')) {
    define('UNISEND_SHIPPING_VERSION', '1.0.5');
}

use unisend_shipping\api\request\UnisendParcelRequest;
use unisend_shipping\api\UnisendParcelApi;
use unisend_shipping\api\UnisendTerminalApi;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\repository\UnisendShippingOrderRepository;
use unisend_shipping\services\UnisendShippingCarrierService;
use unisend_shipping\services\UnisendShippingConfigService;
use unisend_shipping\services\UnisendShippingOrderService;
use unisend_shipping\services\UnisendShippingTerminalService;
use unisend_shipping\services\UnisendShippingTrackingService;

require_once(DIR_SYSTEM . 'library/unisend_shipping/vendor/autoload.php');

class ControllerExtensionShippingUnisendShipping extends Controller {

    public function index() {
        UnisendShippingContextHolder::load($this);
    }

    public function save_selected_terminal()
    {
        if (isset($_POST['id'])) {
            $this->session->data['unisend_selected_terminal_id'] = $_POST['id'];
            $this->session->data['unisend_selected_terminal_name'] = $_POST['name'];
        }
    }

    public function validate()
    {
        UnisendShippingContextHolder::load($this);
        if (isset($_POST['shipping_method'])) {
            $shippingCodeParts = explode('.', $_POST['shipping_method']);
            if (!empty($shippingCodeParts) && $shippingCodeParts[0] == 'unisend_shipping') {
                $carrierId = explode(':', $_POST['shipping_method'])[1];
                if (!$carrierId) {
                    $json['error'] = 'Please select a shipping method';
                    $this->response->addHeader('Content-Type: application/json');
                    $this->response->setOutput(json_encode($json));
                }
                $unisendCarrier = UnisendShippingCarrierService::getById($carrierId);
                $orderData['unisendCarrier'] = $unisendCarrier;

                $this->load->model('catalog/product');

                $products = $this->cart->getProducts();
                $orderData['products'] = $products;
                $orderData['orderProducts'] = $products;
                $orderData['orderInfo'] = $this->toOrderInfo();
                $orderData['unisend_selected_terminal_id'] = $_POST['terminalId'] ?? null;
                $orderData['unisend_selected_terminal_name'] = $_POST['terminalName'] ?? null;
                $orderData['orderInfo']['size'] = 'M';//to skip real size validation
                $parcelValidationRequest = UnisendParcelRequest::fromOrderData($orderData);
                if ($parcelValidationRequest) {
                    $validationResponse = UnisendParcelApi::validateParcel($parcelValidationRequest);
                    if ($validationResponse && is_array($validationResponse) && isset($validationResponse['success']) && !$validationResponse['success']) {
                        if (ob_get_level()) {
                            ob_end_clean();
                        }
                        $json['error'] = UnisendShippingRequestErrorHandler::getInstance()->toErrorMessage($validationResponse);
                        $this->response->addHeader('Content-Type: application/json');
                        $this->response->setOutput(json_encode($json));
                    }
                }
            }
        }
    }

    private function getShippingAddress()
    {
        if (isset($_REQUEST['shipping_address']) && (!isset($_REQUEST['shipping_address']['shipping_address']) || $_REQUEST['shipping_address']['shipping_address'] !== 'new')) {// !== 'new' check for montonio payments request from checkout
            return $_REQUEST['shipping_address'];
        }
        return $this->session->data['shipping_address'] ?? $this->session->data['unisend_shipping']['shipping_address'];
    }

    private function getPaymentAddress()
    {
        return $_REQUEST['payment_address'] ?? $this->session->data['payment_address'];
    }

    private function toOrderInfo()
    {
        $orderInfo = [];
        $shippingAddress = $this->getShippingAddress();
        foreach ($shippingAddress as $key => $value) {
            if ($value) {
                $orderInfo['shipping_' . $key] = $value;
            }
        }

        if (isset($orderInfo['shipping_country_id']) && !isset($orderInfo['shipping_iso_code_2'])) {
            $this->load->model('localisation/country');
            $countryInfo = $this->model_localisation_country->getCountry($orderInfo['shipping_country_id']);
            $orderInfo['shipping_iso_code_2'] = $countryInfo['iso_code_2'];
        }

        $paymentAddress = $this->getPaymentAddress();
        foreach ($paymentAddress as $key => $value) {
            if ($value) {
                $addressKey = 'shipping_' . $key;
                if (!isset($orderInfo[$addressKey])) {
                    $orderInfo[$addressKey] = $value;
                }
            }
        }
        $orderInfo['telephone'] = $this->nullIfEmpty($orderInfo, 'shipping_telephone') ??
            (isset($this->session->data['unisend_shipping']['shipping_address']['telephone']) ? $this->session->data['unisend_shipping']['shipping_address']['telephone'] : null) ??
            $this->customer->getTelephone() ??
            (isset($this->session->data['guest']) ? $this->session->data['guest']['telephone'] ??
            null : null) ??
            $_POST['telephone'];

        $orderInfo['email'] = $this->nullIfEmpty($orderInfo, 'shipping_email') ??
            (isset($this->session->data['unisend_shipping']['shipping_address']['email']) ? $this->session->data['unisend_shipping']['shipping_address']['email'] : null) ??
            $this->customer->getEmail() ??
            (isset($this->session->data['guest']) ? $this->session->data['guest']['email'] ??
            null : null) ??
            $_POST['email'];
        return $orderInfo;
    }

    private function nullIfEmpty($data, $key)
    {
        if (isset($data[$key]) && !empty($data[$key])) {
            return $data[$key];
        }
        return null;
    }

    public function tracking()
    {
        UnisendShippingContextHolder::load($this);
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? $this->getAuthHeader();
        UnisendShippingTrackingService::getInstance()->validateToken($token);

        $events = json_decode(file_get_contents('php://input'), true);
        UnisendShippingTrackingService::getInstance()->updateTrackingStatus($events);
        exit;
    }

    public function terminals()
    {
        UnisendShippingContextHolder::load($this);

        $this->load->model('localisation/country');
        $this->load->language('extension/shipping/unisend_shipping');

        if (isset($_POST['selectedCountryId']) && $_POST['selectedCountryId']) {
            $countryInfo = $this->model_localisation_country->getCountry($_POST['selectedCountryId']);
            if (!$countryInfo) {
                return;
            }
            $countryCode = $countryInfo['iso_code_2'];
        } else if (isset($_POST['selectedAddressId']) && $_POST['selectedAddressId']) {
            $this->load->model('account/address');

            $addressInfo = $this->model_account_address->getAddress($_POST['selectedAddressId']);
            if ($addressInfo) {
                $countryCode = $addressInfo['iso_code_2'];
            }
        } else {
            if (isset($this->session->data['shipping_address'])) {
                $shippingAddress = $this->session->data['shipping_address'];
            } else {
                $shippingAddress = $this->session->data['unisend_shipping']['shipping_address'];
            }
            if (!$shippingAddress) {
                return;
            }
            if (isset($shippingAddress['iso_code_2'])) {
                $countryCode = $shippingAddress['iso_code_2'];
            } else {
                $countryInfo = $this->model_localisation_country->getCountry($shippingAddress['country_id']);
                $countryCode = $countryInfo['iso_code_2'];
            }
        }

        $terminalsData = UnisendShippingTerminalService::getInstance()->getTerminalsData($this, $countryCode);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($terminalsData));
    }

    private function getAuthHeader()
    {
        $headers = apache_request_headers();
        if ($headers) {
            foreach ($headers as $header => $value) {
                if (strcasecmp($header, 'Authorization') === 0) {
                    return $value;
                }
            }
        }
        return null;
    }

    private function isStatusChangeRequest()
    {
        return isset($_POST['order_status_id']) && isset($_REQUEST['order_id']) && isset($_REQUEST['route']) && $_REQUEST['route'] === 'api/order/history';
    }

    private function isNewOrderRequest()
    {
        return !isset($_POST['order_status_id']);//TODO find better solution to check source of event trigger
    }

    public function afterOrderAdd(&$route, &$data, &$output)
    {
        UnisendShippingContextHolder::load($this);

        if ($this->isNewOrderRequest()) {
            $this->createNewOrder($route, $data);
            return;
        }
        if ($this->isStatusChangeRequest()) {
            $this->onStatusChanged($route, $data);
        }
    }

    private function onStatusChanged($route, $data)
    {
        $orderId = $_REQUEST['order_id'];
        $statusId = $_POST['order_status_id'];
        if ($orderId && $statusId) {
            $statusIdsToCreteParcels = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_STATUS_ID_TO_CREATE_PARCEL);
            if ($statusIdsToCreteParcels) {
                $statusIdsToCreteParcelsArr = explode(',', $statusIdsToCreteParcels);
                if (!empty($statusIdsToCreteParcelsArr) && in_array($statusId, $statusIdsToCreteParcelsArr)) {
                    $shippingOrder = UnisendShippingOrderRepository::getById($orderId);
                    if ($shippingOrder && $shippingOrder['status'] === UnisendShippingConst::ORDER_STATUS_NOT_SAVED) {
                        UnisendShippingOrderService::getInstance()->createParcel($orderId, $this);
                    }
                }
            }
        }
    }

    private function createNewOrder(&$route, &$data)
    {
        $orderId = $data[0];
        $paymentStatusId = $data[1];
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($orderId);
        if ($order && isset($order['shipping_code'])) {
            $shippingCodeParts = explode('.', $order['shipping_code']);
            if (!empty($shippingCodeParts) && $shippingCodeParts[0] == 'unisend_shipping') {
                $this->load->model('catalog/product');

                $products = $this->cart->getProducts();
                $orderData['products'] = $products;

                $order['weight'] = UnisendShippingOrderService::getInstance()->getOrderWeight($orderData);
                $orderData['orderInfo'] = $order;
                $orderData['unisend_selected_terminal_id'] = $this->session->data['unisend_selected_terminal_id'] ?? null;
                $orderData['unisend_selected_terminal_name'] = $this->session->data['unisend_selected_terminal_name'] ?? null;

                $carrierId = explode(':', $order['shipping_code'])[1];
                if ($carrierId) {
                    $unisendCarrier = UnisendShippingCarrierService::getById($carrierId);
                    $orderData['unisendCarrier'] = $unisendCarrier;
                }

                UnisendShippingOrderRepository::create($orderData);

                $this->session->data['unisend_selected_terminal_id'] = null;
                $this->session->data['unisend_selected_terminal_name'] = null;
            }
        }
    }
}

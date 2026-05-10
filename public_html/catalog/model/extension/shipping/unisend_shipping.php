<?php

if (!defined('UNISEND_SHIPPING_VERSION')) {
    define('UNISEND_SHIPPING_VERSION', '1.0.5');
}

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\services\UnisendShippingCarrierService;
use unisend_shipping\services\UnisendShippingConfigService;
use unisend_shipping\services\UnisendShippingService;

require_once(DIR_SYSTEM . 'library/unisend_shipping/vendor/autoload.php');

class ModelExtensionShippingUnisendShipping extends Model
{
    public function index() {
        UnisendShippingContextHolder::load($this);
        UnisendShippingService::update($this->db);
    }

    function getQuote($address)
    {
        $addressToSave = $this->session->data['shipping_address'] ?? $address;
        $orderData = $this->getOrderData();
        if ($orderData && isset($orderData['telephone'])) {
            $addressToSave['telephone'] = $orderData['telephone'] ?? null;
            $addressToSave['email'] = $orderData['email'] ?? null;
        }
        $this->session->data['unisend_shipping']['shipping_address'] = $addressToSave;
        $checkoutPage = $_REQUEST['route'] == 'checkout/shipping_method';
        UnisendShippingContextHolder::load($this);
        UnisendShippingService::update($this->db);

        $this->load->language('extension/shipping/unisend_shipping');

        $products = $this->cart->getProducts();
        $availableCarriers = UnisendShippingCarrierService::getAvailableCarriers($products, $this->toOrderInfo(), $this->cart->getTotal());
        if (empty($availableCarriers)) {
            return false;
        }
        usort($availableCarriers, function ($shippingMethodA, $shippingMethodB) {
            return ($shippingMethodA['sort_order'] ?: 0) - ($shippingMethodB['sort_order'] ?: 0);
        });
        foreach ($availableCarriers as $carrier) {
            $quote_data[$carrier['code']] = array(
                'code' => 'unisend_shipping.' . $carrier['code'],
                'title' => $carrier['title'],
                'cost' => $carrier['price'],
                'tax_class_id' => UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_TAX_CLASS_ID),
                'text' => $this->currency->format($this->tax->calculate($carrier['price'], UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_TAX_CLASS_ID), $this->config->get('config_tax')), $this->session->data['currency'])
            );
        }
        $method_data = array(
            'code' => 'unisend_shipping',
            'title' => 'Unisend Shipping',
            'quote' => $quote_data,
            'sort_order' => UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_SHIPPING_METHOD_SORT),
            'error' => false
        );
        return $method_data;
    }

    private function toOrderInfo()
    {
        $orderInfo = [];
        $shippingAddress = $this->session->data['shipping_address'];
        foreach ($shippingAddress as $key => $value) {
            $orderInfo['shipping_' . $key] = $value;
        }
        $orderInfo['telephone'] = $shippingAddress['phone'] ?? $this->customer->getTelephone();
        $orderInfo['email'] = $shippingAddress['email'] ?? $this->customer->getEmail();
        $weight = $this->cart->getWeight();
        $weight = $this->weight->convert($weight, $this->config->get('config_weight_class_id'), (UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_WEIGHT_CLASS_ID) ?: 2));
        $orderInfo['weight'] = max($weight, 1);
        return $orderInfo;
    }

    private function getOrderData() {
        $keys = ['order_data', 'signup'];
        foreach ($keys as $key) {
            if(isset($_POST[$key]) && is_array($_POST[$key])) {
                return $_POST[$key];
            }
        }
        return false;
    }
}
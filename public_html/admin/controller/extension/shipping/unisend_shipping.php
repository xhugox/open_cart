<?php

if (!defined('UNISEND_SHIPPING_VERSION')) {
    define('UNISEND_SHIPPING_VERSION', '1.0.5');
}

use unisend_shipping\api\UnisendApi;
use unisend_shipping\api\UnisendAddressApi;
use unisend_shipping\api\UnisendCourierApi;
use unisend_shipping\api\UnisendShippingPlanApi;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;
use unisend_shipping\api\UnisendEstimateShippingApi;
use unisend_shipping\repository\UnisendShippingOrderRepository;
use unisend_shipping\services\LpOrderStatus;
use unisend_shipping\services\UnisendShippingConfigService;
use unisend_shipping\services\UnisendShippingCourierService;
use unisend_shipping\services\UnisendShippingEshopService;
use unisend_shipping\services\UnisendShippingOrderService;
use unisend_shipping\services\UnisendShippingService;
use unisend_shipping\services\UnisendShippingTerminalService;
use unisend_shipping\services\UnisendShippingTrackingService;
use unisend_shipping\services\UnisendShippingCarrierService;

require_once(DIR_SYSTEM . 'library/unisend_shipping/vendor/autoload.php');

class ControllerExtensionShippingUnisendShipping extends Controller {
	private $error = array();

	public function install() {
        UnisendShippingContextHolder::load($this);

        UnisendShippingService::install($this->db);


        $this->applyWeightSettings($data);
        $this->applyLengthSettings($data);
        $this->applyStatusSettings($data);

        UnisendShippingConfigService::install($data);
        $this->unregisterEvents();
        $this->registerEvents();
        UnisendShippingEshopService::onInstalled();
	}

    private function subscribeTracking()
    {
        $url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
        UnisendShippingTrackingService::getInstance()->subscribe($url);
    }

    private function applyLengthSettings(&$data)
    {
        $this->load->model('localisation/length_class');

        $lengthClasses = $this->model_localisation_length_class->getLengthClasses();
        $preferredLengthClassIndex = array_search('cm', array_column($lengthClasses, 'unit'));
        $data['length_class_id'] = $preferredLengthClassIndex === false ? 1 : $lengthClasses[$preferredLengthClassIndex]['length_class_id'];
    }

    private function applyWeightSettings(&$data)
    {
        $this->load->model('localisation/weight_class');

        $weightClasses = $this->model_localisation_weight_class->getWeightClasses();
        $preferredWeightClassIndex = array_search('g', array_column($weightClasses, 'unit'));
        $data['weight_class_id'] = $preferredWeightClassIndex === false ? 2 : $weightClasses[$preferredWeightClassIndex]['weight_class_id'];
    }

    private function applyStatusSettings(&$data)
    {
        $this->load->model('localisation/order_status');

        $orderStatuses = $this->model_localisation_order_status->getOrderStatuses();
        $statusesIdsToCreateParcel = array_filter($orderStatuses, function ($orderStatus) {
            return $orderStatus['name'] === 'Processed' || $orderStatus['name'] === 'Processing' || $orderStatus['name'] === 'Shipped';
        });
        if (empty($statusesIdsToCreateParcel)) {
            $data['status_id_to_create_parcel'] = '15,2,3';
        } else {
            $data['status_id_to_create_parcel'] = implode(',', array_column($statusesIdsToCreateParcel, 'order_status_id'));
        }
    }

	public function uninstall() {
        UnisendShippingContextHolder::load($this);

        UnisendShippingService::uninstall($this->db);
        UnisendShippingConfigService::uninstall();
		$this->unregisterEvents();
        UnisendShippingEshopService::onUninstalled();
	}

    public function index() {
        UnisendShippingContextHolder::load($this);
        UnisendShippingService::update($this->db);

        $this->load->language('extension/shipping/unisend_shipping');

		$this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'] = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->onSubmit($data);
        } else {
            $data[UnisendShippingConst::SETTING_KEY_PASSWORD] = $this->getParam(UnisendShippingConst::SETTING_KEY_PASSWORD);
        }
        $this->applyAddressSettings($data);

        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->getTokenParam(), true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', $this->getTokenParam() . '&type=shipping', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam(), true)
		);
        $data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['action'] = $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam(), true);

		$data[UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS] = $this->getParam(UnisendShippingConst::SETTING_KEY_SHIPPING_STATUS) ?? false;
        $data[UnisendShippingConst::SETTING_KEY_USERNAME] = $this->getParam(UnisendShippingConst::SETTING_KEY_USERNAME);

        //pickup address
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_ENABLED] = $this->getParam(UnisendShippingConst::SETTING_KEY_PICKUP_ENABLED);

        $data[UnisendShippingConst::SETTING_KEY_COURIER_ENABLED] = $this->getParam(UnisendShippingConst::SETTING_KEY_COURIER_ENABLED);
        $data[UnisendShippingConst::SETTING_KEY_MODE_LIVE] = $this->getParam(UnisendShippingConst::SETTING_KEY_MODE_LIVE);

		$data[UnisendShippingConst::SETTING_KEY_TAX_CLASS_ID] = $this->getParam(UnisendShippingConst::SETTING_KEY_TAX_CLASS_ID);

        $data[UnisendShippingConst::SETTING_KEY_ACTIVE_TAB] = isset($this->request->post[UnisendShippingConst::SETTING_KEY_ACTIVE_TAB]) ? $this->request->post[UnisendShippingConst::SETTING_KEY_ACTIVE_TAB] :
            $this->request->get['activeTab'] ?? 'tab-general';

        //options
        $data[UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_LENGTH] = $this->getParam(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_LENGTH);
        $data[UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_WIDTH] = $this->getParam(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_WIDTH);
        $data[UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_HEIGHT] = $this->getParam(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_HEIGHT);
        $data[UnisendShippingConst::SETTING_KEY_STICKER_LAYOUT] = $this->getParam(UnisendShippingConst::SETTING_KEY_STICKER_LAYOUT);
        $data[UnisendShippingConst::SETTING_KEY_STICKER_ORIENTATION] = $this->getParam(UnisendShippingConst::SETTING_KEY_STICKER_ORIENTATION);
        $courierDaysParam = $this->getParam(UnisendShippingConst::SETTING_KEY_COURIER_DAYS);
        if ($courierDaysParam && !is_array($courierDaysParam)) {
            $courierDaysParam = json_decode($courierDaysParam);
        }
        $data[UnisendShippingConst::SETTING_KEY_COURIER_DAYS] = $courierDaysParam;
        $data[UnisendShippingConst::SETTING_KEY_COURIER_HOUR] = $this->getParam(UnisendShippingConst::SETTING_KEY_COURIER_HOUR);
        $data[UnisendShippingConst::SETTING_KEY_SHIPPING_METHOD_SORT] = $this->getParam(UnisendShippingConst::SETTING_KEY_SHIPPING_METHOD_SORT);
        $data['unisend_shipping_settings_courier_available_days'] = [
            ['name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_day_' . 'MONDAY'), 'id' => '1'],
            ['name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_day_' . 'TUESDAY'), 'id' => '2'],
            ['name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_day_' . 'WEDNESDAY'), 'id' => '3'],
            ['name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_day_' . 'THURSDAY'), 'id' => '4'],
            ['name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_day_' . 'FRIDAY'), 'id' => '5']
        ];
        for ($i = 7; $i <= 14; $i++) {
            for ($j = 0; $j <= 45; $j += 15) {
                $hour = ($i > 9 ? $i : '0' . $i) . ':' . ($j > 9 ? $j : '0' . $j);
                $data['unisend_shipping_settings_courier_available_hours'][] = $hour;
            }
        }
        $shippingMethods = UnisendShippingCarrierService::getAllCarriers();
        $data['unisend_shipping_methods'] = array_map(function ($method) {
            $method['edit'] = $this->url->link('extension/shipping/unisend_shipping/edit', 'id=' . $method['unisend_shipping_method_id'] . '&' . $this->getTokenParam(), true);
            $method['delete'] = $this->url->link('extension/shipping/unisend_shipping/delete', 'id=' . $method['unisend_shipping_method_id'] . '&' . $this->getTokenParam(), true);
            return $method;
		}, $shippingMethods);

        $data['add'] = $this->url->link('extension/shipping/unisend_shipping/add', $this->getTokenParam(), true);

        if (!isset($data['error_warning']) || !$data['error_warning']) {
            $lastError = UnisendShippingRequestErrorHandler::getInstance()->getLastError();
            if ($lastError && isset($lastError['message']) && $lastError['message']) {
                $errorMessage = $lastError['message'];
                $messageKey = 'text_shipping_unisend_shipping_error_' . $errorMessage;
                $translatedErrorMessage = $this->language->get($messageKey);
                $data['error_warning'] = $messageKey == $translatedErrorMessage ? $errorMessage : $translatedErrorMessage;
            }
        }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('localisation/weight_class');
		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
		$this->load->model('localisation/length_class');
		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
        $data['cancel'] = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] . '&type=shipping' : null;

        $this->applyText($data);

		$this->response->setOutput($this->load->view('extension/shipping/unisend_shipping', $data));
	}

    private function applyText(&$data)
    {
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['button_attribute_add'] = $this->language->get('button_attribute_add');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['tab_general'] = $this->language->get('tab_general');
        $data['text_shipping_unisend_shipping_settings_tab_address'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_address');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods');
        $data['text_shipping_unisend_shipping_settings_tab_options'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options');
        $data['text_shipping_unisend_shipping_sender_title'] = $this->language->get('text_shipping_unisend_shipping_sender_title');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods');
        $data['text_shipping_unisend_shipping_settings_tab_general_mode'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_general_mode');
        $data['text_shipping_unisend_shipping_settings_username'] = $this->language->get('text_shipping_unisend_shipping_settings_username');
        $data['text_shipping_unisend_shipping_settings_password'] = $this->language->get('text_shipping_unisend_shipping_settings_password');
        $data['text_shipping_unisend_shipping_tax_class'] = $this->language->get('text_shipping_unisend_shipping_tax_class');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['text_shipping_unisend_shipping_settings_tab_general_mode_production'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_general_mode_production');
        $data['text_shipping_unisend_shipping_settings_tab_general_mode_test'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_general_mode_test');
        $data['text_shipping_unisend_shipping_sender_name'] = $this->language->get('text_shipping_unisend_shipping_sender_name');
        $data['text_shipping_unisend_shipping_sender_contact_tel'] = $this->language->get('text_shipping_unisend_shipping_sender_contact_tel');
        $data['text_shipping_unisend_shipping_sender_contact_email'] = $this->language->get('text_shipping_unisend_shipping_sender_contact_email');
        $data['text_shipping_unisend_shipping_sender_country'] = $this->language->get('text_shipping_unisend_shipping_sender_country');
        $data['text_shipping_unisend_shipping_sender_city'] = $this->language->get('text_shipping_unisend_shipping_sender_city');
        $data['text_shipping_unisend_shipping_sender_address1'] = $this->language->get('text_shipping_unisend_shipping_sender_address1');
        $data['text_shipping_unisend_shipping_sender_address2'] = $this->language->get('text_shipping_unisend_shipping_sender_address2');
        $data['text_shipping_unisend_shipping_sender_postcode'] = $this->language->get('text_shipping_unisend_shipping_sender_postcode');
        $data['text_shipping_unisend_shipping_pickup_title'] = $this->language->get('text_shipping_unisend_shipping_pickup_title');
        $data['text_shipping_unisend_shipping_pickup_enabled'] = $this->language->get('text_shipping_unisend_shipping_pickup_enabled');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_name'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_name');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_action'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_action');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_sort_order'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_sort_order');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_add'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_add');
        $data['text_shipping_unisend_shipping_settings_tab_options_dimensions'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_dimensions');
        $data['text_shipping_unisend_shipping_settings_tab_options_label'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_label');
        $data['text_shipping_unisend_shipping_settings_tab_options_courier'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier');
        $data['text_shipping_unisend_shipping_settings_tab_options_courier_days'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_days');
        $data['text_shipping_unisend_shipping_settings_tab_options_courier_hour'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_hour');
        $data['text_shipping_unisend_shipping_settings_tab_options_label_layout'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_label_layout');
        $data['text_shipping_unisend_shipping_settings_tab_options_label_orientation'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_label_orientation');
        $data['text_shipping_unisend_shipping_settings_tab_options_dimensions_length'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_dimensions_length');
        $data['text_shipping_unisend_shipping_settings_tab_options_dimensions_width'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_dimensions_width');
        $data['text_shipping_unisend_shipping_settings_tab_options_dimensions_height'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_dimensions_height');
        $data['text_shipping_unisend_shipping_settings_tab_options_size'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_size');
        $data['text_shipping_unisend_shipping_select_yes'] = $this->language->get('text_shipping_unisend_shipping_select_yes');
        $data['text_shipping_unisend_shipping_select_no'] = $this->language->get('text_shipping_unisend_shipping_select_no');
        $data['text_shipping_unisend_shipping_pickup_name'] = $this->language->get('text_shipping_unisend_shipping_pickup_name');
        $data['text_shipping_unisend_shipping_pickup_contact_tel'] = $this->language->get('text_shipping_unisend_shipping_pickup_contact_tel');
        $data['text_shipping_unisend_shipping_pickup_contact_email'] = $this->language->get('text_shipping_unisend_shipping_pickup_contact_email');
        $data['text_shipping_unisend_shipping_pickup_country'] = $this->language->get('text_shipping_unisend_shipping_pickup_country');
        $data['text_shipping_unisend_shipping_pickup_city'] = $this->language->get('text_shipping_unisend_shipping_pickup_city');
        $data['text_shipping_unisend_shipping_pickup_address1'] = $this->language->get('text_shipping_unisend_shipping_pickup_address1');
        $data['text_shipping_unisend_shipping_pickup_address2'] = $this->language->get('text_shipping_unisend_shipping_pickup_address2');
        $data['text_shipping_unisend_shipping_pickup_postcode'] = $this->language->get('text_shipping_unisend_shipping_pickup_postcode');
        $data['text_shipping_unisend_shipping_sender_street'] = $this->language->get('text_shipping_unisend_shipping_sender_street');
        $data['text_shipping_unisend_shipping_sender_building'] = $this->language->get('text_shipping_unisend_shipping_sender_building');
        $data['text_shipping_unisend_shipping_sender_flat'] = $this->language->get('text_shipping_unisend_shipping_sender_flat');
        $data['text_shipping_unisend_shipping_pickup_street'] = $this->language->get('text_shipping_unisend_shipping_pickup_street');
        $data['text_shipping_unisend_shipping_pickup_building'] = $this->language->get('text_shipping_unisend_shipping_pickup_building');
        $data['text_shipping_unisend_shipping_pickup_flat'] = $this->language->get('text_shipping_unisend_shipping_pickup_flat');
        $data['text_shipping_unisend_shipping_settings_tab_options_courier_help'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_courier_help');
        $data['text_shipping_unisend_shipping_settings_tab_options_dimensions_help'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_options_dimensions_help');

        //orders texts
        $data['text_shipping_unisend_shipping_button_form_shipment'] = $this->language->get('text_shipping_unisend_shipping_button_form_shipment');
        $data['text_shipping_unisend_shipping_button_delete'] = $this->language->get('text_shipping_unisend_shipping_button_delete');
        $data['text_shipping_unisend_shipping_button_cancel_shipment'] = $this->language->get('text_shipping_unisend_shipping_button_cancel_shipment');
        $data['text_shipping_unisend_shipping_button_call_courier'] = $this->language->get('text_shipping_unisend_shipping_button_call_courier');
        $data['text_shipping_unisend_shipping_button_print_label'] = $this->language->get('text_shipping_unisend_shipping_button_print_label');
        $data['text_shipping_unisend_shipping_button_print_manifest'] = $this->language->get('text_shipping_unisend_shipping_button_print_manifest');
        $data['text_shipping_unisend_shipping_order_filter'] = $this->language->get('text_shipping_unisend_shipping_order_filter');
        $data['text_shipping_unisend_shipping_order_id'] = $this->language->get('text_shipping_unisend_shipping_order_id');
        $data['text_shipping_unisend_shipping_order_shipping_status'] = $this->language->get('text_shipping_unisend_shipping_order_shipping_status');
        $data['text_shipping_unisend_shipping_order_barcode'] = $this->language->get('text_shipping_unisend_shipping_order_barcode');
        $data['text_shipping_unisend_shipping_button_filter'] = $this->language->get('text_shipping_unisend_shipping_button_filter');
        $data['text_shipping_unisend_shipping_orders'] = $this->language->get('text_shipping_unisend_shipping_orders');
        $data['text_shipping_unisend_shipping_order_new'] = $this->language->get('text_shipping_unisend_shipping_order_new');
        $data['text_shipping_unisend_shipping_order_formed'] = $this->language->get('text_shipping_unisend_shipping_order_formed');
        $data['text_shipping_unisend_shipping_order_processed'] = $this->language->get('text_shipping_unisend_shipping_order_processed');
        $data['text_shipping_unisend_shipping_order_id'] = $this->language->get('text_shipping_unisend_shipping_order_id');
        $data['text_shipping_unisend_shipping_order_shipping_status'] = $this->language->get('text_shipping_unisend_shipping_order_shipping_status');
        $data['text_shipping_unisend_shipping_order_barcode'] = $this->language->get('text_shipping_unisend_shipping_order_barcode');
        $data['text_shipping_unisend_shipping_order_terminal'] = $this->language->get('text_shipping_unisend_shipping_order_terminal');
        $data['text_shipping_unisend_shipping_order_size'] = $this->language->get('text_shipping_unisend_shipping_order_size');
        $data['text_shipping_unisend_shipping_order_weight'] = $this->language->get('text_shipping_unisend_shipping_order_weight');
        $data['text_shipping_unisend_shipping_order_part_count'] = $this->language->get('text_shipping_unisend_shipping_order_part_count');
        $data['text_shipping_unisend_shipping_order_plan'] = $this->language->get('text_shipping_unisend_shipping_order_plan');
        $data['text_shipping_unisend_shipping_order_parcel_type'] = $this->language->get('text_shipping_unisend_shipping_order_parcel_type');
        $data['text_shipping_unisend_shipping_order_parcel_shipping_address'] = $this->language->get('text_shipping_unisend_shipping_order_parcel_shipping_address');
        $data['text_shipping_unisend_shipping_order_cod_amount'] = $this->language->get('text_shipping_unisend_shipping_order_cod_amount');
        $data['text_shipping_unisend_shipping_order_date'] = $this->language->get('text_shipping_unisend_shipping_order_date');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['entry_date_created_from'] = $this->language->get('entry_date_created_from');
        $data['entry_date_created_to'] = $this->language->get('entry_date_created_to');
        $data['heading_title'] = $this->language->get('heading_title');
    }

    private function apply(&$data)
    {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->getTokenParam(), true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', $this->getTokenParam() . '&type=shipping', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam(), true)
        );
        $data['heading_title'] = $this->language->get('heading_title');
        $data['userTokenParam'] = $this->getTokenParam();
    }

	public function add()
	{
		UnisendShippingContextHolder::load($this);
		$this->load->language('extension/shipping/unisend_shipping');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $name = $this->request->post['unisend_shipping_method_name'] ?? null;
            $planCode = $this->request->post['unisend_shipping_method_plan_code'] ?? null;
            $parcelType = $this->request->post['unisend_shipping_method_parcel_type'] ?? null;
            $rateType = $this->request->post['unisend_shipping_method_rate_type'] ?? null;
            $freeShippingFrom = $this->request->post['unisend_shipping_method_free_shipping_from'] ?? null;
            $countries = $this->request->post['method_country'] ?? null;

			$sizes = array();
			array_walk(
				$this->request->post,
				function (&$val, $key) use (&$sizes) {
                    if ($this->str_starts_with($key, 'unisend_shipping_size_'))
					{
                        $updatedKey = substr($key, strlen('unisend_shipping_size_'), strlen($key));
						$sizes[$updatedKey] = $val;
					}
				}
			);

			$weightCount = count(array_filter($this->request->post, function($val, $key){
                return $this->str_starts_with($key, 'unisend_shipping_weight_price_');
			},ARRAY_FILTER_USE_BOTH));
			$post = $this->request->post;
			$weights = array_map(function ($i) use ($post) {
                return ['from' => $post['unisend_shipping_weight_from_' . $i], 'to' => $post['unisend_shipping_weight_to_' . $i], 'price' => $post['unisend_shipping_weight_price_' . $i]];
			}, range(0, $weightCount - 1));
			
			UnisendShippingCarrierService::create($name, $planCode, $parcelType, $rateType, $sizes, $weights, $freeShippingFrom, $countries);

            $this->response->redirect($this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam(), true));
            return;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['cancel'] = $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam() . '&activeTab=tab-shipping-methods', true);

        $data['shippingPlans'] = $this->getShippingPlans();

        $data['parcelTypes'] = !empty($data['shippingPlans']) ? $data['shippingPlans'][0] : [];
		$data['rateTypes'] = [
			['code' => "carrier", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_carrier') ],
			['code' => "size", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_size')],
			['code' => "weight", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_weight')],
		];
        $data['action'] = $this->url->link('extension/shipping/unisend_shipping/add', $this->getTokenParam(), true);

        $this->apply($data);
        $this->applyText($data);
        $this->applyShippingMethodsText($data);
		$this->response->setOutput($this->load->view('extension/shipping/unisend_shipping_shipping_method', $data));
	}

    private function str_starts_with($text, $find)
    {
        $length = strlen($find);
        return substr($text, 0, $length) == $find;
    }

    private function applyShippingMethodsText(&$data)
    {
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_name'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_name');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_plan_code'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_plan_code');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_type'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_type');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_rate_type'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_rate_type');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_size'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_size');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_from'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_from');
        $data['text_shipping_unisend_shipping_shipping_method_free_shipping_from'] = $this->language->get('text_shipping_unisend_shipping_shipping_method_free_shipping_from');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_country'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_country');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_size'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_size');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_price'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_price');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_to'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_to');
        $data['text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_price'] = $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_size_price');
    }

    private function getTokenParam()
    {
        if (version_compare(VERSION, '3.0.0', '>=')) {
            return 'user_token=' . $this->getToken();
        } else {
            return 'token=' . $this->getToken();
        }
    }

	public function edit()
	{
		$this->load->language('extension/shipping/unisend_shipping');

		$this->document->setTitle($this->language->get('heading_title'));

		UnisendShippingContextHolder::load($this);

		$id = $this->request->get['id'];

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $name = $this->request->post['unisend_shipping_method_name'];
            $planCode = $this->request->post['unisend_shipping_method_plan_code'];
            $parcelType = $this->request->post['unisend_shipping_method_parcel_type'] ?? null;
            $rateType = $this->request->post['unisend_shipping_method_rate_type'];
            $freeShippingFrom = $this->request->post['unisend_shipping_method_free_shipping_from'];
			$countries = $this->request->post['method_country'];

			$sizes = array();
			array_walk(
				$this->request->post,
				function (&$val, $key) use (&$sizes) {
                    if ($this->str_starts_with($key, 'unisend_shipping_size_'))
					{
                        $updatedKey = substr($key, strlen('unisend_shipping_size_'), strlen($key));
						$sizes[$updatedKey] = $val;
					}
				}
			);

			$weightCount = count(array_filter($this->request->post, function ($val, $key) {
                return $this->str_starts_with($key, 'unisend_shipping_weight_price_');
			}, ARRAY_FILTER_USE_BOTH));
			$post = $this->request->post;
			$weights = array_map(function ($i) use ($post) {
                return ['from' => $post['unisend_shipping_weight_from_' . $i], 'to' => $post['unisend_shipping_weight_to_' . $i], 'price' => $post['unisend_shipping_weight_price_' . $i]];
			}, range(0, $weightCount - 1));

			UnisendShippingCarrierService::update($id, $name, $planCode, $parcelType, $rateType, $sizes, $weights, $freeShippingFrom, $countries);

            $this->response->redirect($this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam() . '&activeTab=tab-shipping-methods&id=' . $id, true));
		}

		$shippingMethod = UnisendShippingCarrierService::getShippingMethod($id);
        $data['unisend_shipping_method_plan_code'] = $shippingMethod['plan_code'];
        $data['unisend_shipping_method_parcel_type'] = $shippingMethod['parcel_type'];
        $data['editedShippingMethod'] = [
            'unisend_shipping_method_id' => $shippingMethod['unisend_shipping_method_id'],
            'planCode' => $shippingMethod['plan_code'],
            'parcelType' => $shippingMethod['parcel_type'],
            'rateType' => $shippingMethod['rate_type'],
            'freeShippingFrom' => $shippingMethod['free_shipping_from'],
            'title' => $shippingMethod['title']
        ];
		$data['sizes'] = UnisendShippingCarrierService::getSizes($id);
		$data['weights'] = UnisendShippingCarrierService::getWeights($id);
        $availableCountries = UnisendEstimateShippingApi::getCountries($shippingMethod['plan_code'], $shippingMethod['parcel_type']);
        if ($availableCountries) {
            $availableCountries[] = ['code' => 'ALL', 'name' => 'All'];
        }
		$selectedCountries = UnisendShippingCarrierService::getCountries($id);
		$data['countries'] = array_filter($availableCountries, function($item) use ($selectedCountries){
			$countryCodes = array_map(function($item){
				return $item['code'];
			}, $selectedCountries);
			return in_array($item['code'], $countryCodes);

		});
        $data['unisend_shipping_method_name'] = $shippingMethod['title'];
        $data['unisend_shipping_method_rate_type'] = $shippingMethod['rate_type'];
        $data['unisend_shipping_method_free_shipping_from'] = $shippingMethod['free_shipping_from'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['cancel'] = $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam() . '&activeTab=tab-shipping-methods', true);
        $data['delete'] = $this->url->link('extension/shipping/unisend_shipping/delete', $this->getTokenParam(), true);
        $data['action'] = $this->url->link('extension/shipping/unisend_shipping/edit', $this->getTokenParam() . '&id=' . $id, true);


        $data['shippingPlans'] = $this->getShippingPlans();
        $data['parcelTypes'] = !empty($data['shippingPlans']) ? $data['shippingPlans'][0] : [];
        $data['userTokenParam'] = $this->getTokenParam();

		$data['rateTypes'] = [
			['code' => "carrier", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_carrier') ],
			['code' => "size", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_size')],
			['code' => "weight", 'name' => $this->language->get('text_shipping_unisend_shipping_settings_tab_shipping_methods_parcel_weight_weight')],
		];
        $this->apply($data);
        $this->applyText($data);
        $this->applyShippingMethodsText($data);
		$this->response->setOutput($this->load->view('extension/shipping/unisend_shipping_shipping_method', $data));
	}

    public function delete()
	{
        UnisendShippingContextHolder::load($this);
        
        $id = $this->request->get['id'];
        UnisendShippingCarrierService::delete($id);

        $this->response->redirect($this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam() . '&activeTab=tab-shipping-methods', true));
	}

	public function planCountries() {
		UnisendShippingContextHolder::load($this);

		$json  = UnisendEstimateShippingApi::getCountries(
		$this->request->get['planCodes'],
		$this->request->get['parcelTypes']);

		$this->response->addHeader('Content-Type: application/json');
        $allCountries[] = ['name' => 'All', 'code' => 'ALL'];
        $allCountries = array_merge($allCountries, $json);
        $this->response->setOutput(json_encode($allCountries));
	}

    public function terminals()
    {
        UnisendShippingContextHolder::load($this);

        $this->load->model('localisation/country');
        $this->load->language('extension/shipping/unisend_shipping');

        $terminalsData = UnisendShippingTerminalService::getInstance()->getTerminalsData($this, $_GET['countryCode']);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($terminalsData));
    }

    private function saveSenderAddress()
    {
        if (UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_SENDER_COUNTRY)) {
            $response = UnisendAddressApi::updateSenderAddress();
            if (!is_array($response) && isset($response->id)) {
                return true;
            }
        }
        return false;
    }

    private function savePickupAddress()
    {
        if (UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_PICKUP_ENABLED) == true) {
            $pickupAddressResponse = UnisendAddressApi::savePickupAddress();
            if (!is_array($pickupAddressResponse) && isset($pickupAddressResponse->id)) {
                UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID, $pickupAddressResponse->id);
                return true;
            }
        }
        return false;
    }

    private function handleActionResult(&$data, $result)
    {
        if ($result !== true) {
            foreach ($result as $error => $orderIds) {
                $data['errors'][] = $error . ': ' . implode(',', $orderIds);
            }
        }
    }

	public function orders()
	{
        UnisendShippingContextHolder::load($this);

        $this->load->language('extension/shipping/unisend_shipping');

		$this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $selected = $this->request->post['selected'] ?? null;
            if (!empty($selected)) {
                if (array_key_exists('formShipments', $_REQUEST)) {
                    $result = UnisendShippingOrderService::getInstance()->formShipmentByIds($selected, $this);
                    $this->handleActionResult($data, $result);
                } else if (array_key_exists('printLabel', $_REQUEST)) {
                    UnisendShippingOrderService::getInstance()->printLabels($selected);
                } else if (array_key_exists('callCourier', $_REQUEST)) {
                    $result = UnisendShippingOrderService::getInstance()->handleCallCourier($selected);
                    $this->handleActionResult($data, $result);
                } else if (array_key_exists('printManifest', $_REQUEST)) {
                    UnisendShippingOrderService::getInstance()->printManifests($selected);
                } else if (array_key_exists('cancelShipments', $_REQUEST)) {
                    UnisendShippingOrderService::getInstance()->cancelInitiatedShippingBulk($selected);
                } else if (array_key_exists('deleteParcels', $_REQUEST)) {
                    UnisendShippingOrderService::getInstance()->deleteOrders($selected);
                }
            } else {
                //TODO show error?
            }
		}
        $lastError = UnisendShippingRequestErrorHandler::getInstance()->getLastError();
        if ($lastError && isset($lastError['message'])) {
            $data['errors'][] = $lastError['message'];
        }

        $filter_data = [];
        $url = '';
        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            $filter_data['filter_order_id'] = $this->request->get['filter_order_id'];
            $data['filter_order_id'] = $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_shipping_status'])) {
            $url .= '&filter_shipping_status=' . $this->request->get['filter_shipping_status'];
            $filter_data['filter_shipping_status'] = $this->request->get['filter_shipping_status'];
            $data['filter_shipping_status'] = $this->request->get['filter_shipping_status'];
        }

        if (isset($this->request->get['filter_barcode'])) {
            $url .= '&filter_barcode=' . $this->request->get['filter_barcode'];
            $filter_data['filter_barcode'] = $this->request->get['filter_barcode'];
            $data['filter_barcode'] = $this->request->get['filter_barcode'];
        }

        if (isset($this->request->get['filter_date_created_from'])) {
            $url .= '&filter_date_created_from=' . $this->request->get['filter_date_created_from'];
            $filter_data['filter_date_created_from'] = $this->request->get['filter_date_created_from'];
            $data['filter_date_created_from'] = $this->request->get['filter_date_created_from'];
        }

        if (isset($this->request->get['filter_date_created_to'])) {
            $url .= '&filter_date_created_to=' . $this->request->get['filter_date_created_to'];
            $filter_data['filter_date_created_to'] = $this->request->get['filter_date_created_to'];
            $data['filter_date_created_to'] = $this->request->get['filter_date_created_to'];
        }

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->getTokenParam(), true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', $this->getTokenParam() . '&type=shipping', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/unisend_shipping', $this->getTokenParam(), true)
		);

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}


		$this->load->model('sale/order');


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        $activeTab = $this->request->get['activeTab'] ?? 'new-orders';
        $data['activeTab'] = $activeTab;
        $url .= '&activeTab=' . $activeTab;

        if ($activeTab === 'new-orders') {
            $statuses = [UnisendShippingConst::ORDER_STATUS_NOT_FORMED, UnisendShippingConst::ORDER_STATUS_NOT_SAVED, UnisendShippingConst::ORDER_STATUS_SAVED];
        } else if ($activeTab === 'processed-orders') {
            $statuses = [UnisendShippingConst::ORDER_STATUS_LABEL_GENERATED, UnisendShippingConst::ORDER_STATUS_COURIER_CALLED_LABEL_GENERATED, UnisendShippingConst::ORDER_STATUS_COMPLETED];
        } else {
            $statuses = [UnisendShippingConst::ORDER_STATUS_COURIER_CALLED, UnisendShippingConst::ORDER_STATUS_FORMED];
        }

        $filter_data = array_merge($filter_data, array(
            'filter_order_status' => implode(',', $statuses),
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin'),
            'sort'                   => $sort,
            'order'                  => $order,
        ));
        $results = UnisendShippingOrderService::getInstance()->getOrders($filter_data, $this);
        $orderTotal = UnisendShippingOrderRepository::getTotalOrders($filter_data);

		foreach ($results as $result) {

			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'barcode'      => $result['barcode'],
				'terminal'      => $result['terminal'],
				'size'      => $result['size'],
                'weight' => ($result['weight'] ? $result['weight'] / 1000.0 : null),
				'part_count'      => $result['part_count'],
				'created'      => $result['created'],
                'shipping_status' => isset($result['shipping_status']) ? $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . $result['shipping_status']) : $this->language->get('text_missing'),
                'plan_code' => $this->language->get('text_shipping_unisend_shipping_plan_' . $result['plan_code']),
                'parcel_type' => $this->language->get('text_shipping_unisend_shipping_parcel_type_' . $result['parcel_type']),
                'shipping_address' => $this->toShippingAddress($result['shopOrder'] ?? null),
                'cod_amount' => $result['cod_amount'] . ' EUR',
                'cod_selected' => $result['cod_selected'],
                'edit' => $this->url->link('sale/order/info', $this->getTokenParam() . '&order_id=' . $result['order_id'] . '&sourcePage=unisendShippingOrders&activeTab=' . $activeTab, true)
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_status'] = $this->language->get('column_status');

        $pagination = new Pagination();
        $pagination->total = $orderTotal;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/shipping/unisend_shipping/orders', $this->getTokenParam() . $url . '&page={page}' . '&activeTab=' . $activeTab, true);

        $data['shipping_statuses'] = $this->getShippingStatuses($activeTab);
        $data['userTokenParam'] = $this->getTokenParam();
        $data['url'] = $pagination->url;
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($orderTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($orderTotal - $this->config->get('config_limit_admin'))) ? $orderTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $orderTotal, ceil($orderTotal / $this->config->get('config_limit_admin')));

        $this->applyText($data);

		$this->response->setOutput($this->load->view('extension/shipping/unisend_shipping_orders', $data));
	}

    private function toShippingAddress($shopOrder)
    {
        if (!$shopOrder) return '';//TODO missing text
        $name = $shopOrder['shipping_firstname'] . ' ' . $shopOrder['shipping_lastname'] . ' ' . $shopOrder['shipping_company'];
        $address = $shopOrder['shipping_address_1'] . ' ' . $shopOrder['shipping_address_2'] . ' ' . $shopOrder['shipping_postcode'] . ', ' . $shopOrder['shipping_city'] . ', ' . $shopOrder['shipping_iso_code_2'];

        return $name . ' ' . $address;
    }

    private function getShippingStatuses($activeTab)
    {
        if ($activeTab === 'new-orders') {
            return [
                ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CANCELED->name), 'id' => LpOrderStatus::$PARCEL_CANCELED->name],
                ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CREATED->name), 'id' => LpOrderStatus::$PARCEL_CREATED->name],
                ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CREATE_PENDING->name), 'id' => LpOrderStatus::$PARCEL_CREATE_PENDING->name],
                ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_FAILED->name), 'id' => LpOrderStatus::$PARCEL_FAILED->name],
            ];
        }
        return [
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$COURIER_CALLED->name), 'id' => LpOrderStatus::$COURIER_CALLED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$COURIER_PENDING->name), 'id' => LpOrderStatus::$COURIER_PENDING->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$ON_THE_WAY->name), 'id' => LpOrderStatus::$ON_THE_WAY->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CANCELED->name), 'id' => LpOrderStatus::$PARCEL_CANCELED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CREATED->name), 'id' => LpOrderStatus::$PARCEL_CREATED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_CREATE_PENDING->name), 'id' => LpOrderStatus::$PARCEL_CREATE_PENDING->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_DELIVERED->name), 'id' => LpOrderStatus::$PARCEL_DELIVERED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_FAILED->name), 'id' => LpOrderStatus::$PARCEL_FAILED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_PENDING->name), 'id' => LpOrderStatus::$PARCEL_PENDING->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_RECEIVED->name), 'id' => LpOrderStatus::$PARCEL_RECEIVED->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$PARCEL_RETURNING->name), 'id' => LpOrderStatus::$PARCEL_RETURNING->name],
            ['name' => $this->language->get('text_shipping_unisend_shipping_order_shipping_status_' . LpOrderStatus::$SHIPPING_INITIATED->name), 'id' => LpOrderStatus::$SHIPPING_INITIATED->name],
        ];
    }

    public function unisendOrderAction()
    {
        UnisendShippingContextHolder::load($this);
        $orderId = $this->request->get['order_id'];

        if (array_key_exists('saveUnisendShippingOrder', $_REQUEST)) {

            UnisendShippingOrderService::getInstance()->createParcels([$orderId], $this);
        } else if (array_key_exists('formShipments', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->formShipmentByIds([$orderId], $this);
        } else if (array_key_exists('printLabel', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->printLabels([$orderId]);
        } else if (array_key_exists('callCourier', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->handleCallCourier([$orderId]);
        } else if (array_key_exists('printManifest', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->printManifests([$orderId]);
        } else if (array_key_exists('cancelShipments', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->cancelInitiatedShippingBulk([$orderId]);
        } else if (array_key_exists('deleteParcels', $_REQUEST)) {
            UnisendShippingOrderService::getInstance()->deleteOrders([$orderId]);
        }
        $this->response->redirect($this->url->link('sale/order/info', 'order_id=' . $orderId . '&' . $this->getTokenParam(), true));
    }

    public function afterHeader(&$route, &$data, &$output)
    {
        UnisendShippingContextHolder::load($this);
        try {
            UnisendShippingCourierService::getInstance()->handleAutoCourierCall();
        } catch (Exception|Throwable $e) {
        }
    }

	protected function getParam($key) {
		return isset($this->request->post[$key])
			? $this->request->post[$key]
			: UnisendShippingConfigService::get($key);
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/unisend_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    private function registerEvents()
    {
        $this->loadEventModel();
        $this->addEvent('unisend_activity_order_add_after', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/shipping/unisend_shipping/afterOrderAdd');
        $this->addEvent('unisend_header_after', 'admin/view/common/header/after', 'extension/shipping/unisend_shipping/afterHeader');
    }

    private function unregisterEvents()
    {
        $this->loadEventModel();
        $this->deleteEvent('unisend_activity_order_add_after');
        $this->deleteEvent('unisend_header_after');
    }

    private function deleteEvent($code)
    {
        if ($this->model_setting_event) {
            $this->model_setting_event->deleteEventByCode($code);
        } else {
            $this->model_extension_event->deleteEvent($code);
        }
    }

    private function addEvent($code, $trigger, $action)
    {
        if ($this->model_setting_event) {
            $this->model_setting_event->addEvent($code, $trigger, $action);
        } else {
            $this->model_extension_event->addEvent($code, $trigger, $action);
        }
    }

    private function loadEventModel()
    {
        if (version_compare(VERSION, '3.0.0', '>=')) {
            $this->load->model('setting/event');
        } else {
            $this->load->model('extension/event');
        }
    }

    private function onSubmit(&$data)
    {
        UnisendShippingEshopService::onSettingsSave();
        $userChanged = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_USERNAME) !== $this->getParam(UnisendShippingConst::SETTING_KEY_USERNAME);
        $data[UnisendShippingConst::SETTING_KEY_USERNAME] = $this->getParam(UnisendShippingConst::SETTING_KEY_USERNAME);
        $data[UnisendShippingConst::SETTING_KEY_PASSWORD] = $this->getParam(UnisendShippingConst::SETTING_KEY_PASSWORD);
        $live = $this->getParam(UnisendShippingConst::SETTING_KEY_MODE_LIVE);

        $unisendApi = UnisendApi::getInstance();
        $unisendApi->setApiUrl($live);
        $authenticated = $unisendApi->authenticate($data[UnisendShippingConst::SETTING_KEY_USERNAME], $data[UnisendShippingConst::SETTING_KEY_PASSWORD]);
        if ($authenticated) {

            if ($userChanged) {
                UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID, '');
            }

            $currentSettings = UnisendShippingConfigService::getAll();
            unset($currentSettings[UnisendShippingConst::SETTING_KEY_COURIER_DAYS]);

            $allSettings = array_merge($currentSettings, $this->request->post);
            $allSettings = array_merge($allSettings, $data);
            UnisendShippingConfigService::updateValues($allSettings);

            $this->saveSenderAddress();
            $this->savePickupAddress();
            $this->subscribeTracking();
            UnisendShippingEshopService::onAuthenticated();
        } else {
            unset($data[UnisendShippingConst::SETTING_KEY_PASSWORD]);
            $currentSettings = UnisendShippingConfigService::getAll();
            $allSettings = array_merge($currentSettings, $this->request->post);
            unset($allSettings[UnisendShippingConst::SETTING_KEY_PASSWORD]);
            UnisendShippingConfigService::updateValues($allSettings);
        }
        $this->session->data['success'] = $this->language->get('text_success');
        if (isset($_POST['unisend_shipping_shipping_method_id']) && is_array($_POST['unisend_shipping_shipping_method_id'])) {
            foreach ($_POST['unisend_shipping_shipping_method_id'] as $index => $shipping_method_id) {
                if (isset($_POST['unisend_shipping_shipping_method_sort_order']) && isset($_POST['unisend_shipping_shipping_method_sort_order'][$index])) {
                    $id = $_POST['unisend_shipping_shipping_method_id'][$index];
                    $sortOrder = $_POST['unisend_shipping_shipping_method_sort_order'][$index];
                    UnisendShippingCarrierService::updateSortOrder($id, $sortOrder);
                }
            }
        }
        UnisendShippingCourierService::getInstance()->scheduleNextCourierCall();
    }

    private function applyAddressSettings(&$data)
    {
        $unisendApi = UnisendApi::getInstance();
        if (!$unisendApi->doTokenExists()) {
            return;
        }

        $senderAddress = UnisendAddressApi::getSenderAddress();
        if (!$senderAddress || ((is_array($senderAddress) && isset($senderAddress['success']) && $senderAddress['success'] !== true))) {
            return;
        }

        $data[UnisendShippingConst::SETTING_KEY_SENDER_NAME] = $senderAddress->name;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_EMAIL] = $senderAddress->contacts->email ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_PHONE] = $senderAddress->contacts->phone ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_COUNTRY] = $senderAddress->address->countryCode;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_CITY] = $senderAddress->address->locality;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_STREET] = $senderAddress->address->street ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_FLAT] = $senderAddress->address->flat ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_BUILDING] = $senderAddress->address->building ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_POST_CODE] = $senderAddress->address->postalCode;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_ADDRESS1] = $senderAddress->address->address1 ?? null;
        $data[UnisendShippingConst::SETTING_KEY_SENDER_ADDRESS2] = $senderAddress->address->address2 ?? null;

        $pickupAddress = UnisendAddressApi::getPickupAddress();
        if (!$pickupAddress || (is_array($pickupAddress) && isset($pickupAddress['success']) && $pickupAddress['success'] !== true)) {
            $pickupAddress = $senderAddress;
        }
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_NAME] = $pickupAddress->name;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_EMAIL] = $pickupAddress->contacts->email ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_PHONE] = $pickupAddress->contacts->phone ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_COUNTRY] = $pickupAddress->address->countryCode;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_CITY] = $pickupAddress->address->locality;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_STREET] = $pickupAddress->address->street ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_FLAT] = $pickupAddress->address->flat ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_BUILDING] = $pickupAddress->address->building ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_POST_CODE] = $pickupAddress->address->postalCode;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_ADDRESS1] = $pickupAddress->address->address1 ?? null;
        $data[UnisendShippingConst::SETTING_KEY_PICKUP_ADDRESS2] = $pickupAddress->address->address2 ?? null;
    }

    private function getShippingPlans()
    {
        $plansResponse = UnisendShippingPlanApi::getPlans();
        if (!UnisendShippingRequestErrorHandler::getInstance()->isRequestCompletedSuccessfully($plansResponse)) {
            return [];
        }
        $allShippingPlans = get_object_vars($plansResponse);
        $shippingPlans = [];

        foreach ($allShippingPlans as $plan) {
            if ($plan->code === 'PROCESSES_DOCUMENTS') continue;
            $plan->name = $this->language->get('text_shipping_unisend_shipping_plan_' . $plan->code);
            foreach ($plan->shipping as $shipping) {
                $shipping->name = $this->language->get('text_shipping_unisend_shipping_parcel_type_' . $shipping->parcelType);
            }
            $shippingPlans[] = $plan;
        }
        return $shippingPlans;
    }

    private function getToken()
    {
        if (isset($this->session->data['user_token'])) {
            return $this->session->data['user_token'];
        }
        return $this->session->data['token'];
    }
}




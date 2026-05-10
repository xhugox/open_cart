<?php
class ControllerExtensionModuleAdvancedoption extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/advancedoption');

		$this->document->setTitle(strip_tags($this->language->get('heading_inner_title')));

		$this->load->model('setting/setting');
		
		if(isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		}else{
			$data['store_id']	= 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_advancedoption', $this->request->post,$data['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');
			
			if($this->request->post['stay']==1){
				$this->response->redirect($this->url->link('extension/module/advancedoption', '&store_id='.$data['store_id'].'&user_token=' . $this->session->data['user_token'] , true));
			}else{
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}
		
		
		$data['advoptionimportexport'] = $this->url->link('extension/advoption_export', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['user_token'] = $this->session->data['user_token'];

		$data['heading_title'] = $this->language->get('heading_inner_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_default'] = $this->language->get('text_default');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_color'] = $this->language->get('text_color');
		$data['text_image'] = $this->language->get('text_image');
		$data['text_squre'] = $this->language->get('text_squre');
		$data['text_round'] = $this->language->get('text_round');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_front'] = $this->language->get('tab_front');
		$data['tab_language'] = $this->language->get('tab_language');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => strip_tags($this->language->get('heading_inner_title')),
			'href' => $this->url->link('extension/module/advancedoption', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['store_action'] =  $this->url->link('extension/module/advancedoption','user_token=' . $this->session->data['user_token'], 'SSL');
		
		$data['action'] = $this->url->link('extension/module/advancedoption', 'user_token=' . $this->session->data['user_token']. '&store_id='. $data['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$store_info = $this->model_setting_setting->getSetting('module_advancedoption', $data['store_id']);

		if (isset($this->request->post['module_advancedoption_status'])) {
			$data['module_advancedoption_status'] = $this->request->post['module_advancedoption_status'];
		}elseif(isset($store_info['module_advancedoption_status'])){
			$data['module_advancedoption_status'] = $store_info['module_advancedoption_status'];
		}  else {
			$data['module_advancedoption_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_model_status'])) {
			$data['module_advancedoption_model_status'] = $this->request->post['module_advancedoption_model_status'];
		}elseif(isset($store_info['module_advancedoption_model_status'])){
			$data['module_advancedoption_model_status'] = $store_info['module_advancedoption_model_status'];
		} else {
			$data['module_advancedoption_model_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_model'])) {
			$data['module_advancedoption_model'] = $this->request->post['module_advancedoption_model'];
		}elseif(isset($store_info['module_advancedoption_model'])){
			$data['module_advancedoption_model'] = $store_info['module_advancedoption_model'];
		} else {
			$data['module_advancedoption_model'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_sku'])) {
			$data['module_advancedoption_sku'] = $this->request->post['module_advancedoption_sku'];
		}elseif(isset($store_info['module_advancedoption_sku'])){
			$data['module_advancedoption_sku'] = $store_info['module_advancedoption_sku'];
		} else {
			$data['module_advancedoption_sku'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_upc'])) {
			$data['module_advancedoption_upc'] = $this->request->post['module_advancedoption_upc'];
		}elseif(isset($store_info['module_advancedoption_upc'])){
			$data['module_advancedoption_upc'] = $store_info['module_advancedoption_upc'];
		} else {
			$data['module_advancedoption_upc'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_ean'])) {
			$data['module_advancedoption_ean'] = $this->request->post['module_advancedoption_ean'];
		}elseif(isset($store_info['module_advancedoption_ean'])){
			$data['module_advancedoption_ean'] = $store_info['module_advancedoption_ean'];
		} else {
			$data['module_advancedoption_ean'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_jan'])) {
			$data['module_advancedoption_jan'] = $this->request->post['module_advancedoption_jan'];
		}elseif(isset($store_info['module_advancedoption_jan'])){
			$data['module_advancedoption_jan'] = $store_info['module_advancedoption_jan'];
		} else {
			$data['module_advancedoption_jan'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_customer_group'])) {
			$data['module_advancedoption_customer_group'] = $this->request->post['module_advancedoption_customer_group'];
		}elseif(isset($store_info['module_advancedoption_customer_group'])){
			$data['module_advancedoption_customer_group'] = $store_info['module_advancedoption_customer_group'];
		} else {
			$data['module_advancedoption_customer_group'] = '';
		}

		if (isset($this->request->post['module_advancedoption_default_status'])) {
			$data['module_advancedoption_default_status'] = $this->request->post['module_advancedoption_default_status'];
		}elseif(isset($store_info['module_advancedoption_default_status'])){
			$data['module_advancedoption_default_status'] = $store_info['module_advancedoption_default_status'];
		} else {
			$data['module_advancedoption_default_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_image'])) {
			$data['module_advancedoption_image'] = $this->request->post['module_advancedoption_image'];
		}elseif(isset($store_info['module_advancedoption_image'])){
			$data['module_advancedoption_image'] = $store_info['module_advancedoption_image'];
		} else {
			$data['module_advancedoption_image'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_sku_status'])) {
			$data['module_advancedoption_sku_status'] = $this->request->post['module_advancedoption_sku_status'];
		}elseif(isset($store_info['module_advancedoption_sku_status'])){
			$data['module_advancedoption_sku_status'] = $store_info['module_advancedoption_sku_status'];
		} else {
			$data['module_advancedoption_sku_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_upc_status'])) {
			$data['module_advancedoption_upc_status'] = $this->request->post['module_advancedoption_upc_status'];
		}elseif(isset($store_info['module_advancedoption_upc_status'])){
			$data['module_advancedoption_upc_status'] = $store_info['module_advancedoption_upc_status'];
		} else {
			$data['module_advancedoption_upc_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_ean_status'])) {
			$data['module_advancedoption_ean_status'] = $this->request->post['module_advancedoption_ean_status'];
		}elseif(isset($store_info['module_advancedoption_ean_status'])){
			$data['module_advancedoption_ean_status'] = $store_info['module_advancedoption_ean_status'];
		} else {
			$data['module_advancedoption_ean_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_image_status'])) {
			$data['module_advancedoption_image_status'] = $this->request->post['module_advancedoption_image_status'];
		}elseif(isset($store_info['module_advancedoption_image_status'])){
			$data['module_advancedoption_image_status'] = $store_info['module_advancedoption_image_status'];
		} else {
			$data['module_advancedoption_image_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_description_status'])) {
			$data['module_advancedoption_description_status'] = $this->request->post['module_advancedoption_description_status'];
		}elseif(isset($store_info['module_advancedoption_description_status'])){
			$data['module_advancedoption_description_status'] = $store_info['module_advancedoption_description_status'];
		} else {
			$data['module_advancedoption_description_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_live_change_status'])) {
			$data['module_advancedoption_live_change_status'] = $this->request->post['module_advancedoption_live_change_status'];
		}elseif(isset($store_info['module_advancedoption_live_change_status'])){
			$data['module_advancedoption_live_change_status'] = $store_info['module_advancedoption_live_change_status'];
		} else {
			$data['module_advancedoption_live_change_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_qty_status'])) {
			$data['module_advancedoption_qty_status'] = $this->request->post['module_advancedoption_qty_status'];
		}elseif(isset($store_info['module_advancedoption_qty_status'])){
			$data['module_advancedoption_qty_status'] = $store_info['module_advancedoption_qty_status'];
		} else {
			$data['module_advancedoption_qty_status'] = '';
		}
		
		if (isset($this->request->post['module_advancedoption_model_prefix'])) {
			$data['module_advancedoption_model_prefix'] = $this->request->post['module_advancedoption_model_prefix'];
		}elseif(isset($store_info['module_advancedoption_model_prefix'])){
			$data['module_advancedoption_model_prefix'] = $store_info['module_advancedoption_model_prefix'];
		} else {
			$data['module_advancedoption_model_prefix'] = '';
		}
		
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->request->post['module_advancedoption_model_title' . $language['language_id']])) {
				$data['module_advancedoption_model_title'][$language['language_id']] = $this->request->post['module_advancedoption_model_title' . $language['language_id']];
			} elseif(isset($store_info['module_advancedoption_model_title'. $language['language_id']])){
				$data['module_advancedoption_model_title'][$language['language_id']] = $store_info['module_advancedoption_model_title'. $language['language_id']];
			} else {
				$data['module_advancedoption_model_title'][$language['language_id']] = '';
			}
			
			if (isset($this->request->post['module_advancedoption_sku_title' . $language['language_id']])) {
				$data['module_advancedoption_sku_title'][$language['language_id']] = $this->request->post['module_advancedoption_sku_title' . $language['language_id']];
			} elseif(isset($store_info['module_advancedoption_sku_title'. $language['language_id']])){
				$data['module_advancedoption_sku_title'][$language['language_id']] = $store_info['module_advancedoption_sku_title'. $language['language_id']];
			} else {
				$data['module_advancedoption_sku_title'][$language['language_id']] = '';
			}
			
			if (isset($this->request->post['module_advancedoption_upc_title' . $language['language_id']])) {
				$data['module_advancedoption_upc_title'][$language['language_id']] = $this->request->post['module_advancedoption_upc_title' . $language['language_id']];
			} elseif(isset($store_info['module_advancedoption_upc_title'. $language['language_id']])){
				$data['module_advancedoption_upc_title'][$language['language_id']] = $store_info['module_advancedoption_upc_title'. $language['language_id']];
			} else {
				$data['module_advancedoption_upc_title'][$language['language_id']] = '';
			}
			
			if (isset($this->request->post['module_advancedoption_ean_title' . $language['language_id']])) {
				$data['module_advancedoption_ean_title'][$language['language_id']] = $this->request->post['module_advancedoption_ean_title' . $language['language_id']];
			} elseif(isset($store_info['module_advancedoption_ean_title'. $language['language_id']])){
				$data['module_advancedoption_ean_title'][$language['language_id']] = $store_info['module_advancedoption_ean_title'. $language['language_id']];
			} else {
				$data['module_advancedoption_ean_title'][$language['language_id']] = '';
			}
			
			if (isset($this->request->post['module_advancedoption_jan_title' . $language['language_id']])) {
				$data['module_advancedoption_jan_title'][$language['language_id']] = $this->request->post['module_advancedoption_jan_title' . $language['language_id']];
			} elseif(isset($store_info['module_advancedoption_jan_title'. $language['language_id']])){
				$data['module_advancedoption_jan_title'][$language['language_id']] = $store_info['module_advancedoption_jan_title'. $language['language_id']];
			} else {
				$data['module_advancedoption_jan_title'][$language['language_id']] = '';
			}
		}

		$data['languages'] = $languages;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/advancedoption', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/advancedoption')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
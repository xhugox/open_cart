<?php
class ControllerExtensionAdvancedoptionadvancedoption extends Controller {
	public function index() {
		$json=array();
		$this->load->language('product/product');
		$this->load->model('catalog/product');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_discount'] = $this->language->get('text_discount');
		
		if(isset($this->request->post['product_id'])){
			$product_id = $this->request->post['product_id'];
		}else{
			$product_id = 0;
		}
		
		$this->load->model('tool/image');
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		if($product_info){
			 if($this->config->get('module_advancedoption_model_status')!=2){	
				$json['model'] = $product_info['model'];
			 }else{
				 $json['model'] = '';
			 }
			 
			 $json['upc'] = '';
			 $json['ean'] = '';
			 $json['jan'] = '';
		}
		
		//Live Price Calculation
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$this->request->post['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
		
		if ($product_query->num_rows && ($this->request->post['quantity'] > 0)):
			$option_price = 0;
			$option_points = 0;
			if(isset($this->request->post['option'])):
				foreach ($this->request->post['option'] as $product_option_id => $value):
					if($value):
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$this->request->post['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($option_query->num_rows) {
								if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
									
									if($this->getproductoptionvaluedata('model',$value) && ($this->config->get('module_advancedoption_model_status')==1 || $this->config->get('module_advancedoption_model_status')==2)){
										$json['model'] .= $this->getproductoptionvaluedata('model',$value);
									}

									if($this->getproductoptionvaluedata('upc',$value) && ($this->config->get('module_advancedoption_upc') && $this->config->get('module_advancedoption_upc_status'))){
										$json['upc'] .= $this->getproductoptionvaluedata('upc',$value);
									}

									if($this->getproductoptionvaluedata('ean',$value) &&  ($this->config->get('module_advancedoption_ean') && $this->config->get('module_advancedoption_ean_status'))){
										$json['ean'] .= $this->getproductoptionvaluedata('ean',$value);
									}

									if($this->getproductoptionvaluedata('jan',$value) && ($this->config->get('module_advancedoption_jan') && $this->config->get('module_advancedoption_jan_status'))){
										$json['jan'] .= $this->getproductoptionvaluedata('jan',$value);
									}
									
									$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

									if ($option_value_query->num_rows) {
										if ($option_value_query->row['price_prefix'] == '+') {
											$option_price += $option_value_query->row['price'];
										} elseif ($option_value_query->row['price_prefix'] == '-') {
											$option_price -= $option_value_query->row['price'];
										}
										
										if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
										} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
										}
									}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								
								if($this->getproductoptionvaluedata('model',$product_option_value_id) && ($this->config->get('module_advancedoption_model_status')==1 || $this->config->get('module_advancedoption_model_status')==2)){

									$json['model'] .= $this->getproductoptionvaluedata('model',$product_option_value_id);

								}

								if($this->getproductoptionvaluedata('upc',$product_option_value_id) && ($this->config->get('module_advancedoption_upc') && $this->config->get('module_advancedoption_upc_status'))){
									$json['upc'] .= $this->getproductoptionvaluedata('upc',$product_option_value_id);
								}

								if($this->getproductoptionvaluedata('ean',$product_option_value_id) &&  ($this->config->get('module_advancedoption_ean') && $this->config->get('module_advancedoption_ean_status'))){

								  $json['ean'] .= $this->getproductoptionvaluedata('ean',$product_option_value_id);

								}

								if($this->getproductoptionvaluedata('jan',$product_option_value_id) && ($this->config->get('module_advancedoption_jan') && $this->config->get('module_advancedoption_jan_status'))){
									$json['jan'] .= $this->getproductoptionvaluedata('jan',$product_option_value_id);
								}
								
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}
									
									if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
									}
								}
							}
						}
					}
						
					
					endif;
				endforeach;
			endif;
			$price = $product_query->row['price'];
			
			// Product Discounts
			$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_query->row['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$this->request->post['quantity'] . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

			if ($product_discount_query->num_rows) {
				$price = $product_discount_query->row['price'];
			}

			// Product Specials
			$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_query->row['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

			$isspecialprice = false; 
			if ($product_special_query->num_rows) {
				$isspecialprice = true;
				$price = $product_special_query->row['price'];
			}
			
			
			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->post['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate(($discount['price']+$option_price), $product_query->row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}
			
			
			
			
			
			if($this->config->get('module_advancedoption_qty_status')){
				$data['tax'] = $this->currency->format(($price + $option_price)*$this->request->post['quantity'],$this->session->data['currency']);
				$data['points'] = ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $this->request->post['quantity'] : 0);
				if($isspecialprice){
					$data['special'] = $this->currency->format(($this->tax->calculate($price + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*$this->request->post['quantity'],$this->session->data['currency']);
				
					$data['price'] = $this->currency->format(($this->tax->calculate($product_query->row['price'] + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*$this->request->post['quantity'],$this->session->data['currency']);
				}else{
					$data['price'] = $this->currency->format(($this->tax->calculate($price + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*$this->request->post['quantity'],$this->session->data['currency']);
					$data['special'] = '';
				}
				
			}else{
				
				$data['tax'] = $this->currency->format(($price + $option_price)*1,$this->session->data['currency']);
				$data['points'] = ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $this->request->post['quantity'] : 0);
				if($isspecialprice){
					$data['special'] = $this->currency->format(($this->tax->calculate($price + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*1,$this->session->data['currency']);
				
					$data['price'] = $this->currency->format(($this->tax->calculate($product_query->row['price'] + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*1,$this->session->data['currency']);
				}else{
					$data['price'] = $this->currency->format(($this->tax->calculate($price + $option_price,$product_query->row['tax_class_id'], $this->config->get('config_tax')))*$this->request->post['quantity'],$this->session->data['currency']);
					$data['special'] = '';
				}
			}
			
			
			
			$json['liveprice'] =  $this->load->view('extension/advancedoption/liveprice', $data,true);
			
		endif;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getproductoptionvaluedata($key,$product_option_value_id){

		$query = $this->db->query("SELECT $key FROM ".DB_PREFIX."product_option_value_data WHERE product_option_value_id = '".(int)$product_option_value_id."'");



	  return (isset($query->row[$key]) ? $query->row[$key] : '');

	}
}
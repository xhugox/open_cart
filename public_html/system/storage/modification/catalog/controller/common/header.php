<?php
class ControllerCommonHeader extends Controller {
	public function index() {

					// XD Stickers start
						$this->load->model('setting/setting');
						$xdstickers = $this->config->get('xdstickers');
						$data['xdstickers_status'] = $this->config->get('module_xdstickers_status');
						if ($data['xdstickers_status']) {
							$data['xdstickers'] = array();
							$data['xdstickers_position'] = $xdstickers['position'];
							$data['xdstickers_inline_styles'] = $xdstickers['inline_styles'];
							$data['xdstickers'][] = array(
								'id'			=> 'xdsticker_sale',
								'bg'			=> $xdstickers['sale']['bg'],
								'color'			=> $xdstickers['sale']['color'],
								'status'		=> $xdstickers['sale']['status'],
							);
							$data['xdstickers'][] = array(
								'id'			=> 'xdsticker_bestseller',
								'bg'			=> $xdstickers['bestseller']['bg'],
								'color'			=> $xdstickers['bestseller']['color'],
								'status'		=> $xdstickers['bestseller']['status'],
							);
							$data['xdstickers'][] = array(
								'id'			=> 'xdsticker_novelty',
								'bg'			=> $xdstickers['novelty']['bg'],
								'color'			=> $xdstickers['novelty']['color'],
								'status'		=> $xdstickers['novelty']['status'],
							);
							$data['xdstickers'][] = array(
								'id'			=> 'xdsticker_last',
								'bg'			=> $xdstickers['last']['bg'],
								'color'			=> $xdstickers['last']['color'],
								'status'		=> $xdstickers['last']['status'],
							);
							$data['xdstickers'][] = array(
								'id'			=> 'xdsticker_freeshipping',
								'bg'			=> $xdstickers['freeshipping']['bg'],
								'color'			=> $xdstickers['freeshipping']['color'],
								'status'		=> $xdstickers['freeshipping']['status'],
							);

							if (isset($xdstickers['stock']) && !empty($xdstickers['stock'])) {
								foreach($xdstickers['stock'] as $key => $value) {
									if (isset($value['status']) && $value['status'] == '1') {
										$data['xdstickers'][] = array(
											'id'			=> 'xdsticker_stock_' . $key,
											'bg'			=> $value['bg'],
											'color'			=> $value['color'],
											'status'		=> $value['status'],
										);
									}
								}
							}

							// CUSTOM stickers
							$this->load->model('extension/module/xdstickers');
							$custom_xdstickers = $this->model_extension_module_xdstickers->getCustomXDStickers();
							if (!empty($custom_xdstickers)) {
								foreach ($custom_xdstickers as $custom_xdsticker) {
									$custom_sticker_id = 'xdsticker_' . $custom_xdsticker['xdsticker_id'];
									$data['xdstickers'][] = array(
										'id'			=> $custom_sticker_id,
										'bg'			=> $custom_xdsticker['bg_color'],
										'color'			=> $custom_xdsticker['color_color'],
										'status'		=> $custom_xdsticker['status'],
									);
								}
							}
						}
					// XD Stickers end
				
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);
	}
}

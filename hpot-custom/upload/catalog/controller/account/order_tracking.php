<?php

class ControllerAccountOrderTracking extends Controller
{
	private $error = [];

	public function index()
	{
		$this->load->language('account/order_tracking');

		$heading_title = $this->config->get('module_hp_tracking_heading_title_' . $this->config->get('config_language_id'));
		$data['module_hp_tracking_color_scheme'] = $this->config->get('module_hp_tracking_color_scheme');
		$data['text_instruction'] = html_entity_decode($this->config->get('module_hp_tracking_text_instruction_' . $this->config->get('config_language_id')));

		$this->document->setTitle($heading_title);

		$this->load->model('account/order_tracking');

		$data['text_form_status_order'] = $this->language->get('text_form_status_order');

		$this->document->addStyle('catalog/view/javascript/shipment-tracking.css');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$order_info = $this->model_account_order_tracking->cekStatusOrder($this->request->post['invoice_no'], $this->request->post['email']);

			if (!empty($order_info)) {

				$order_id = $order_info['order_id'];
				$data['order_info'] = $order_info;

				if (isset($order_info['no_receipt'])) {
					$data['no_receipt'] = $order_info['no_receipt'];
				} else {
					$data['no_receipt'] = "";
				}

				$data['text_resi_pengiriman'] = sprintf($this->language->get('text_resi_pengiriman'), $order_info['courier']);
				$data['text_order_detail'] = $this->language->get('text_order_detail');
				$data['text_invoice_no'] = $this->language->get('text_invoice_no');
				$data['text_order_id'] = $this->language->get('text_order_id');
				$data['text_date_added'] = $this->language->get('text_date_added');
				$data['text_shipping_method'] = $this->language->get('text_shipping_method');
				$data['text_shipping_address'] = $this->language->get('text_shipping_address');
				$data['text_payment_method'] = $this->language->get('text_payment_method');
				$data['text_payment_address'] = $this->language->get('text_payment_address');
				$data['text_history'] = $this->language->get('text_history');
				$data['text_comment'] = $this->language->get('text_comment');
				$data['text_after_confirm'] = $this->language->get('text_after_confirm');
				$data['text_before_confirm'] = $this->language->get('text_before_confirm');

				$data['text_order_id'] = $this->language->get('text_order_id');
				$data['text_order_status'] = $this->language->get('text_order_status');
				$data['text_unconfirmed'] = $this->language->get('text_unconfirmed');

				$data['column_status_konfirmasi'] = $this->language->get('column_status_konfirmasi');
				$data['column_status_order'] = $this->language->get('column_status_order');
				$data['column_resi_pengiriman'] = $this->language->get('column_resi_pengiriman');

				$data['text_order_detail'] = $this->language->get('text_order_detail');
				$data['text_invoice_no'] = $this->language->get('text_invoice_no');
				$data['text_status'] = $this->language->get('text_status');
				$data['text_date_added'] = $this->language->get('text_date_added');
				$data['text_customer'] = $this->language->get('text_customer');
				$data['text_products'] = $this->language->get('text_products');
				$data['text_total'] = $this->language->get('text_total');
				$data['text_empty'] = $this->language->get('text_empty');
				$data['text_status_order'] = $this->language->get('text_status_order');
				$data['text_date_confirm'] = $this->language->get('text_date_confirm');
				$data['tabel_konfirmasi'] = $this->model_account_order_tracking->tableExist('confirm');

				if ($order_info['date_added']) {
					//$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
					$data['date_added'] = $this->formatDateId($order_info['date_added']);
				} else {
					$order_info['date_added'] = '';
				}

				if (isset($order_info['tgl_konfirm'])) {
					//$data['date_confirm'] = date($this->language->get('date_format_short'), strtotime($order_info['tgl_konfirm']));
					$data['date_confirm'] = $this->formatDateId($order_info['tgl_konfirm']);
				} else {
					$data['date_confirm'] = '';
				}

				$this->load->model('account/order');

//		          $order_info = $this->model_account_order_tracking->getOrder($order_id);

				$data['payment_method'] = $order_info['payment_method'];

				$data['shipping_method'] = $order_info['shipping_method'];


				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = [
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				];

				$replace = [
					'firstname' => $order_info['payment_firstname'],
					'lastname' => $order_info['payment_lastname'],
					'company' => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city' => $order_info['payment_city'],
					'postcode' => $order_info['payment_postcode'],
					'zone' => $order_info['payment_zone'],
					'country' => $order_info['payment_country']
				];

				$data['payment_address'] = str_replace(["\r\n", "\r", "\n"], '<br />', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '<br />', trim(str_replace($find, $replace, $format))));

				$data['payment_method'] = $order_info['payment_method'];

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = [
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				];

				$replace = [
					'firstname' => $order_info['shipping_firstname'],
					'lastname' => $order_info['shipping_lastname'],
					'company' => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city' => $order_info['shipping_city'],
					'postcode' => $order_info['shipping_postcode'],
					'zone' => $order_info['shipping_zone'],
					'country' => $order_info['shipping_country']
				];

				$data['shipping_address'] = str_replace(["\r\n", "\r", "\n"], '<br />', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '<br />', trim(str_replace($find, $replace, $format))));


				// hpwd
				$data['text_no_receipt'] = $this->config->get('module_hp_tracking_no_receipt_' . $this->config->get('config_language_id'));


				$used_order_statuses = $this->config->get('module_hp_tracking_order_status');

				if ($used_order_statuses) {

					$this->load->language('account/order_tracking');

					$data['text_shipping_number'] = $this->language->get('text_shipping_number');
					$data['text_shipping'] = $this->language->get('text_shipping');

					$this->load->model('localisation/order_status');

					$order_statuses = $this->model_localisation_order_status->getOrderStatuses();

					$order_status_icons = gettype($this->config->get('module_hp_tracking_icons')) == "string" ? json_decode($this->config->get('module_hp_tracking_icons'), true) : $this->config->get('module_hp_tracking_icons');

					$sort_orders = [];

					$order_history_date = $this->model_account_order_tracking->getOrderHistoryDate($order_id);

					$hp_tracking_sort_order = gettype($this->config->get('module_hp_tracking_sort_order')) == "string" ? json_decode($this->config->get('module_hp_tracking_sort_order'), true) : $this->config->get('module_hp_tracking_sort_order');

					foreach ($order_statuses as $order_status) {
						if (in_array($order_status['order_status_id'], $used_order_statuses)) {
							$sort_orders[$order_status['order_status_id']] = [
								'order_status_id' => $order_status['order_status_id'],
								'name' => $order_status['name'],
								'date' => isset($order_history_date[$order_status['order_status_id']]) ? date("d-m-Y H:m", strtotime($order_history_date[$order_status['order_status_id']])) : '-',
								'icon' => $order_status_icons[$order_status['order_status_id']],
								'sort_order' => $hp_tracking_sort_order[$order_status['order_status_id']],
							];
						}
					}

					$sort_order = [];

					foreach ($sort_orders as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $sort_orders);

					$data['order_status_queue'] = $sort_orders;

					$shipment_info = $this->model_account_order_tracking->getShippingOrder($order_id);

					$data['shipment_profile'] = [];

					$data['shipment_manifest'] = [];


					if ($shipment_info && $shipment_info['shipping_number']) {

						$tracking = $this->model_account_order_tracking->getTracking($order_id);

						$data['shipment_profile'] = $tracking['profile'];

						$data['shipment_manifest'] = $tracking['manifest'];
					}


					$data['current_order_status'] = isset($hp_tracking_sort_order[$order_info['order_status_id']]) ? $hp_tracking_sort_order[$order_info['order_status_id']] : 0;

				}  // end hpwd

			} // order info
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false
		];

		$data['breadcrumbs'][] = [
			'text' => $heading_title,
			'href' => $this->url->link('account/order_tracking', '', 'SSL'),
			'separator' => $this->language->get('text_separator')
		];

		$data['heading_title'] = $heading_title;

		$data['entry_invoice_no'] = $this->language->get('entry_invoice_no');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['button_send'] = $this->language->get('button_send');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['invoice_no'])) {
			$data['error_invoice_no'] = $this->error['invoice_no'];
		} else {
			$data['error_invoice_no'] = '';
		}

		if (isset($this->error['fake_invoice'])) {
			$data['error_fake_invoice'] = $this->error['fake_invoice'];
		} else {
			$data['error_fake_invoice'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		$data['action'] = $this->url->link('account/order_tracking', '', 'SSL');

		if (isset($this->request->post['invoice_no'])) {
			$data['invoice_no'] = $this->request->post['invoice_no'];
		} else {
			$data['invoice_no'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		$data['order_histories'] = [];
		$order_histories = $this->db->query("SELECT os.name, oh.comment, oh.date_added FROM `" . DB_PREFIX . "order_history` oh INNER JOIN `" . DB_PREFIX . "order_status` os ON oh.order_status_id = os.order_status_id where order_id = " . (int)$order_id . " AND language_id = " . (int)$this->config->get('config_language_id') . " GROUP BY name")->rows;
		foreach ($order_histories as $order_history) {
			$order_history['date_added'] = $this->formatDateId($order_history['date_added']);
			$data['order_histories'][] = $order_history;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/order_tracking', $data));

	}

	public function success()
	{
		$this->load->language('account/order_tracking');

		$this->document->setTitle($heading_title);

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false
		];

		$data['breadcrumbs'][] = [
			'text' => $heading_title,
			'href' => $this->url->link('account/order_tracking'),
			'separator' => $this->language->get('text_separator')
		];

		$data['heading_title'] = $heading_title;
		$data['text_tgl_beli'] = $this->language->get('text_tgl_beli');
		$data['text_status_order'] = $this->language->get('text_status_order');
		$data['module_hp_tracking_color_scheme'] = $this->config->get('module_hp_tracking_color_scheme');

		$data['continue'] = $this->url->link('common/home');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));

	}

	private function formatDateId($date)
	{
		$format = [
			'Sun' => 'Minggu',
			'Mon' => 'Senin',
			'Tue' => 'Selasa',
			'Wed' => 'Rabu',
			'Thu' => 'Kamis',
			'Fri' => 'Jumat',
			'Sat' => 'Sabtu',
			'Jan' => 'Januari',
			'Feb' => 'Februari',
			'Mar' => 'Maret',
			'Apr' => 'April',
			'May' => 'Mei',
			'Jun' => 'Juni',
			'Jul' => 'Juli',
			'Aug' => 'Agustus',
			'Sep' => 'September',
			'Oct' => 'Oktober',
			'Nov' => 'November',
			'Dec' => 'Desember'
		];

		$date = date('D, j M Y H:m', strtotime($date));
		$date = strtr($date, $format);

		return $date;
	}

	private function validate()
	{
		if (utf8_strlen($this->request->post['invoice_no']) < 1) {
			$this->error['invoice_no'] = $this->language->get('error_invoice_no');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->model_account_order_tracking->getOrder($this->request->post['invoice_no'], $this->request->post['email'])) {
			$this->error['fake_invoice'] = $this->language->get('error_fake_invoice');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
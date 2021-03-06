<?php
class ControllerExtensionModuleCallBack extends Controller {
	private $errors = [];
	private $user_token;
	private $version = '1.0';
	
	public function __construct($registry) {
		parent::__construct($registry);
		
		$this->user_token = 'user_token=' . $this->session->data['user_token'];
	}
	
	// Install
	public function install() {
		$this->createTables();
		$this->fillTables();
	}
	// Uninstall
	public function uninstall() {
		$this->removeTables();
	}
	
	// Index. Settings page
	public function index() {
		$this->load->language('extension/module/callback/callback_settings');
		$this->document->setTitle($this->language->get('heading_title'));
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateData()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_callback', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('title_success');
			
			// If button apply
			if (isset($this->request->post['apply']) && $this->request->post['apply']) {
				$this->response->redirect($this->generateLink('extension/module/callback'));
			}
			
			// Go to list modules
			$this->response->redirect($this->generateLink('marketplace/extension'));
		}
		
		$this->getData();
	}
	
	// List. List of callback items
	public function list() {
		$this->load->language('extension/module/callback/callback_list');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->getList();
	}
	
	/* Ajax */
	// Get items
	public function items() {
		$this->response->setOutput($this->viewListItems());
	}
	// Remove items
	public function remove() {
		$this->load->language('extension/module/callback/callback_list');
		$this->load->model('extension/module/callback');
		
		$selected = $this->request->post['selected'];
		$ids = implode(',', $selected);
		
		$status = $this->model_extension_module_callback->remove($ids);
		
		if ($status) {
			$data['status'] = 'success';
			$data['title'] = $this->language->get('title_success');
			$data['text'] = $this->language->get('success_delete');
		} else {
			$data['status'] = 'danger';
			$data['title'] = $this->language->get('title_error');
			$data['text'] = $this->language->get('error_delete');
		}
		
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($data));
	}
	// Get item by id
	public function get() {
		$this->load->model('extension/module/callback');
		
		$data['callback'] = $this->model_extension_module_callback->getById($this->request->get['callback_id']);
		$data['statuses'] = $this->model_extension_module_callback->statuses();
		
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($data));
	}
	// Update item
	public function update() {
		$this->load->language('extension/module/callback/callback_list_modal');
		$this->load->model('extension/module/callback');
		$status = $this->model_extension_module_callback->update($this->request->post);
		
		if ($status) {
			$data['status'] = 'success';
			$data['title'] = $this->language->get('title_success');
			$data['text'] = $this->language->get('success_update');
		} else {
			$data['status'] = 'danger';
			$data['title'] = $this->language->get('title_error');
			$data['text'] = $this->language->get('error_update');
		}
		
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($data));
	}
	
	/* Get data */
	// For index
	private function getData() {
		$data['data_version'] = $this->version;
		$data['user_token'] = $this->session->data['user_token'];
		
		// Success
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		// Errors
		$data['errors'] = $this->errors;
		$data['warning'] = $this->warning;
		
		// Breadcrumbs
		$data['breadcrumbs'] = $this->generateBreadcrumbs('extension/module/callback');
		
		// Buttons
		$data['url_action'] = $this->generateLink('extension/module/callback');
		$data['url_list'] = $this->generateLink('extension/module/callback/list');
		$data['url_cancel'] = $this->generateLink('marketplace/extension', ['type' => 'module']);
		
		// Templates
		// Tabs
		$data['data_tab_info'] = $this->load->controller('extension/module/callback/callback_tab_info', ['data_version' => $this->version]);
		$data['data_tab_general'] = $this->load->controller('extension/module/callback/callback_tab_general');
		// Main
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/callback/callback_settings', $data));
	}
	// For list
	private function getList() {
		$data['data_version'] = $this->version;
		$data['user_token'] = $this->session->data['user_token'];
		
		// Success
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		// Errors
		$data['errors'] = $this->errors;
		$data['warning'] = $this->warning;
		
		// Breadcrumbs
		$data['breadcrumbs'] = $this->generateBreadcrumbs('extension/module/callback/list');
		
		// Buttons
		$data['url_settings'] = $this->generateLink('extension/module/callback');
		$data['url_cancel'] = $this->generateLink('marketplace/extension', ['type' => 'module']);
		$data['url_update'] = $this->generateLink('extension/module/callback/items');
		$data['url_remove'] = $this->generateLink('extension/module/callback/remove');
		$data['url_test'] = $this->generateLink('extension/module/callback/test');
		
		// Data
		$data['view_modal'] = $this->viewListModal();
		$data['view_items'] = $this->viewListItems();
		
		// Main
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/callback/callback_list', $data));
	}
	
	/* Additional */
	// View of modal
	private function viewListModal() {
		$this->load->language('extension/module/callback/callback_list_modal');
		
		$data['url_action'] = $this->generateLink('extension/module/callback/update');
		
		return $this->load->view('extension/module/callback/callback_list_modal', $data);
	}
	// View of items with paginate
	private function viewListItems() {
		$this->load->language('extension/module/callback/callback_list_items');
		
		$data['url_get'] = $this->generateLink('extension/module/callback/get');
		
		// Prepare paginate
		$this->load->model('extension/module/callback');
		$total = (int)$this->model_extension_module_callback->total();
		$path = $this->generateLink('extension/module/callback/items');
		$page = (isset($this->request->get['page']) && $this->request->get['page'] > 1) ? (int)$this->request->get['page'] : 1;
		$paginate = $this->generatePagination($total, $path, $page);
		
		$data['result'] = sprintf($this->language->get('text_pagination'), $paginate['from'] + 1, $paginate['current_page'] == $paginate['last_page'] ? $paginate['total'] : $paginate['to'], $paginate['total']);
		
		$filter_data = array(
			'start' => $paginate['from'],
			'limit' => $paginate['per_page']
		);
		
		$data['list'] = $this->model_extension_module_callback->list($filter_data);
		$data['view_pagination'] = $this->viewListPagination($paginate);
		
		return $this->load->view('extension/module/callback/callback_list_items', $data);
	}
	// View of paginate
	private function viewListPagination($data) {
		$this->load->language('extension/module/callback/callback_list_pagination');
		
		return $this->load->view('extension/module/callback/callback_list_pagination', $data);
	}
	
	/* Validate */
	private function validateData() {
		if (!$this->user->hasPermission('modify', 'extension/module/callback')) {
			$this->errors['permission'] = $this->language->get('error_permission');
		}
		
		if (count($this->errors) > 0) {
			$this->warning = $this->language->get('error_warning');
		}
		
		return !$this->errors;
	}
	
	/* Generate */
	// Breadcrumbs
	private function generateBreadcrumbs($module) {
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->generateLink('common/dashboard')
			],
			[
				'text' => $this->language->get('text_extension'),
				'href' => $this->generateLink('marketplace/extension', ['type' => 'module'])
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->generateLink($module)
			]
		];
	}
	// Paginate
	private function generatePagination($total, $path, $current_page = 1) {
		$pagination = new Pagination();
		$pagination->prepare($total, $path, $current_page);
		
		return $pagination->get();
	}
	// Link
	private function generateLink($module, $params = [])
	{
		$url = '';
		foreach ($params as $key => $value) {
			$url .= '&' . $key . '=' . $value;
		}
		return $this->url->link($module, $this->user_token . $url, true);
	}
	
	// Install
	// Create tables
	private function createTables() {
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "callback_status` (
			  `callback_status_id` int(11) NOT NULL AUTO_INCREMENT,
			  `language_id` int(11) NOT NULL,
			  `name` varchar(32) NOT NULL,
			  PRIMARY KEY (`callback_status_id`,`language_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;"
		);
		
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "callback` (
			    `callback_id` int(11) NOT NULL AUTO_INCREMENT,
			    `store_id` int(11) NOT NULL DEFAULT '0',
			    `language_id` int(11) NOT NULL,
			    `callback_status_id` int(11) NOT NULL DEFAULT '1',
				`url` varchar(255) NULL,
			    `name` varchar(32) NULL,
				`email` varchar(96) NULL,
				`phone` varchar(32) NULL,
				`message` text NULL,
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
			    PRIMARY KEY (`callback_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;"
		);
	}
	// Fill tables
	private function fillTables() {
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			switch ($language['code']) {
				case 'ru-ru':
					$status_wait = 'Ожидание';
					$status_work = 'Обработка';
					$status_complete = 'Выполнен';
					break;
				default:
					$status_wait = 'Waiting';
					$status_work = 'Processing';
					$status_complete = 'Completed';
			}
			
			$this->db->query(
				"INSERT INTO `" . DB_PREFIX . "callback_status` (`callback_status_id`, `language_id`, `name`) VALUES
				('1', '" . $language['language_id'] . "', '" . $status_wait . "'),
				('2', '" . $language['language_id'] . "', '" . $status_work . "'),
				('3', '" . $language['language_id'] . "', '" . $status_complete . "');"
			);
		}
	}
	// Uninstall
	// Remove tables
	private function removeTables() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "callback_status`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "callback`;");
	}
}

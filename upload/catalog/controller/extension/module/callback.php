<?php
class ControllerExtensionModuleCallback extends Controller {
	private $errors = [];
	
	public function index() {
		$this->document->addStyle('catalog/view/theme/default/stylesheet/callback.css');
		$this->document->addScript('catalog/view/javascript/callback.js');
		
		$this->load->language('extension/module/callback/callback');
		
		$data['view_modal'] = $this->getViewModal();
		$data['view_alert'] = $this->getViewAlert();

		return $this->load->view('extension/module/callback/callback', $data);
	}

	public function store() {
		$this->load->language('extension/module/callback/callback');
		$this->load->model('extension/module/callback');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$callbackData = [
				'store_id' => $this->config->get('config_store_id'),
				'language_id' => $this->config->get('config_language_id'),
				'url' => htmlspecialchars($this->request->post['url'], ENT_QUOTES),
				'name' => htmlspecialchars($this->request->post['name'], ENT_QUOTES),
				'email' => htmlspecialchars($this->request->post['email'], ENT_QUOTES),
				'phone' => htmlspecialchars($this->request->post['phone'], ENT_QUOTES),
				'message' => htmlspecialchars($this->request->post['message'], ENT_QUOTES)
			];
			
			$json['callback_id'] = $this->model_extension_module_callback->addCallback($callbackData);
			$json['status'] = 'success';
		} else {
			$json['status'] = 'error';
			$json['errors'] = $this->errors;
		}
		
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($json));
	}
	
	private function getViewModal()
	{
		$this->load->language('extension/module/callback/callback_modal');
		$data['url_store'] = $this->url->link('extension/module/callback/store', '', true);
		
		return $this->load->view('extension/module/callback/callback_modal', $data);
	}
	
	private function getViewAlert()
	{
		$this->load->language('extension/module/callback/callback_alert');
		
		return $this->load->view('extension/module/callback/callback_alert');
	}
	
	private function validateForm() {
		if (!isset($this->request->post['phone']) || (isset($this->request->post['phone']) && utf8_strlen($this->request->post['phone']) == 0)) {
			$this->errors['phone'] = $this->language->get('error_phone');
		}
		
		if (!isset($this->request->post['email']) || (isset($this->request->post['email']) && utf8_strlen($this->request->post['email']) == 0)) {
			$this->errors['email'] = $this->language->get('error_name');
		}

		if (!isset($this->request->post['name']) || (isset($this->request->post['name']) && utf8_strlen($this->request->post['name']) == 0)) {
			$this->errors['name'] = $this->language->get('error_name');
		}
		
		if ($this->errors && !isset($this->errors['warning'])) {
			$this->errors['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->errors;
	}
}

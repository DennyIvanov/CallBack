<?php
class ControllerExtensionModuleCallBackCallbackTabGeneral extends Controller {
	public function index() {
		$this->load->language('extension/module/callback/callback_tab_general');
		
		// Data form
		$data['module_callback_status'] = isset($this->request->post['module_callback_status'])
			? $this->request->post['module_callback_status']
			: $this->config->get('module_callback_status');
		
		return $this->load->view('extension/module/callback/callback_tab_general', $data);
	}
}

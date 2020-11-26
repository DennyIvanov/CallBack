<?php
class ControllerExtensionModuleCallBackCallbackTabInfo extends Controller {
	public function index($data) {
		$this->load->language('extension/module/callback/callback_tab_info');
		
		return $this->load->view('extension/module/callback/callback_tab_info', $data);
	}
}

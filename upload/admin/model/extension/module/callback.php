<?php
class ModelExtensionModuleCallBack extends Model {
	public function list($data = []) {
		$query = "";
		$query .= " SELECT cb.*, cbs.name as status_name";
		$query .= " FROM " . DB_PREFIX . "callback cb";
		$query .= " LEFT JOIN " . DB_PREFIX . "callback_status cbs ON (cb.callback_status_id = cbs.callback_status_id  AND cbs.language_id = '" . (int)$this->config->get('config_language_id') . "')";
		$query .= " ORDER BY cb.callback_id DESC";
		$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		
		return $this->db->query($query)->rows;
	}
	
	public function total() {
		$query = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "callback";
		
		return $this->db->query($query)->row['total'];
	}
	
	public function update($data) {
		$query = "";
		$query .= " UPDATE " . DB_PREFIX . "callback cb SET";
		$query .= " callback_status_id = '" . (int)$data['callback_status_id'] . "',";
		$query .= " name = '" . $data['name'] . "',";
		$query .= " email = '" . $data['email'] . "',";
		$query .= " phone = '" . $data['phone'] . "',";
		$query .= " message = '" . $data['message'] . "',";
		$query .= " date_modified = NOW()";
		$query .= " WHERE cb.callback_id = '" . (int)$data['callback_id'] . "'";
		
		return $this->db->query($query);
	}
	
	public function remove($ids) {
		$query = "DELETE FROM " . DB_PREFIX . "callback WHERE callback_id IN (" . $ids . ")";
		
		return $this->db->query($query);
	}
	
	public function getById($callback_id) {
		$query = "";
		$query .= " SELECT cb.*";
		$query .= " FROM " . DB_PREFIX . "callback cb";
		$query .= " WHERE cb.callback_id = '" . (int)$callback_id . "'";
		
		return $this->db->query($query)->row;
	}
	
	public function statuses() {
		$query = "";
		$query .= " SELECT cbs.*";
		$query .= " FROM " . DB_PREFIX . "callback_status cbs";
		$query .= " WHERE cbs.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		return $this->db->query($query)->rows;
	}
}

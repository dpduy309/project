<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Blacklist extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');

		
			$this->load->model('mdl_blacklist');
			

		}


		public function get_where($id)
		{
			$query = $this->mdl_blacklist->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_blacklist->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_blacklist->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_blacklist->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_blacklist->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_blacklist->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_blacklist->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_blacklist->_custom_query($mysql_query);
			return $query;
		}

	}
?>
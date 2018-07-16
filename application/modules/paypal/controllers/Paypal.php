<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Paypal extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('shipping');

			$this->load->model('mdl_paypal');
			

		}

		public function _is_on_test_mode()
		{
			return TRUE;
		}

		public function _draw_checkout_btn($query)
		{
			foreach ($query->result() as $row) {
				$session_id = $row->session_id;
			}

			$on_test_mode = $this->_is_on_test_mode();

			if($on_test_mode == TRUE)
			{
				$data['form_location'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}else
			{
				$data['form_location'] = 'https://www.paypal.com/cgi-bin/webscr';
			}

			$data['shipping'] = $this->shipping->_get_shipping();
			$data['custom'] = $this->site_security->_encrypt_string($session_id);
			$data['paypal_email'] = $this->site_settings->_get_paypal_email();
			$data['currency_code'] = $this->site_settings->_get_currency_code();
			$data['query'] = $query;
			$this->load->view('checkout_btn',$data);
		}


		public function get_where($id)
		{
			$query = $this->mdl_paypal->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_paypal->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_paypal->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_paypal->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_paypal->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_paypal->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_paypal->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_paypal->_custom_query($mysql_query);
			return $query;
		}

	}
?>
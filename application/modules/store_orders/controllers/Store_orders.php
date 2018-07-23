<?php  
	class Store_orders extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('store_shoppertrack');

			$this->load->model('mdl_store_orders');
			
		}

		public function _auto_generate_order($max_id, $customer_session_id)
		{
			//this gets called form the Paypal IPN listener, when an order is place
			$order_ref = $this->site_security->generate_random_string(6);
			$order_ref = strtoupper($order_ref);

			$data['order_ref'] = $order_ref;
			$data['date_created'] = time();
			$data['paypal_id'] = $paypal_id;
			$data['session_id'] = $customer_session_id;
			$data['opened'] = 0;
			$data['order_status'] = 0;
			$this->_insert($data);

			//transfers from store_basket to store_shoppertrack
			$this->store_shoppertrack->_transfer_from_basket($customer_session_id);
		}

	

		public function get_where($id)
		{
			$query = $this->mdl_store_orders->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_orders->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_orders->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_orders->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_orders->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_orders->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($limit,$offset,$order_by)
		{
			$query = $this->mdl_store_orders->get_with_limit($limit,$offset,$order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_orders->_custom_query($mysql_query);
			return $query;
		}




	}
?>
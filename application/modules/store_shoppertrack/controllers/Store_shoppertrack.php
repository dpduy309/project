<?php  
	class Store_shoppertrack extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('store_basket');

			$this->load->model('mdl_store_shoppertrack');
			
		}

		public function get_with_double_condition($col1,$value1,$col2,$value2)
		{
			$query = $this->mdl_store_shoppertrack->get_with_double_condition($col1,$value1,$col2,$value2);
			return $query;
		}

		public function _transfer_from_basket($customer_session_id)
		{
			$query = $this->store_basket->get_where_custom('session_id', $customer_session_id);

			foreach ($query->result() as $row) {
				$data['session_id'] = $row->session_id;
				$data['item_title'] = $row->item_title;
				$data['price'] = $row->price;
				$data['tax'] = $row->tax;
				$data['item_id'] = $row->item_id;
				$data['item_size'] = $row->item_size;
				$data['item_qty'] = $row->item_qty;
				$data['item_colour'] = $row->item_colour;
				$data['date_added'] = $row->date_added;
				$data['shopper_id'] = $row->shopper_id;
				$data['ip_address'] = $row->ip_address;

				$this->_insert($data);

			}

			$mysql_query = "delete from store_basket where session_id='$customer_session_id'";
			$query = $this->_custom_query($mysql_query);
		}

		public function get_where($id)
		{
			$query = $this->mdl_store_shoppertrack->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_shoppertrack->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_shoppertrack->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_shoppertrack->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_shoppertrack->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_shoppertrack->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($limit,$offset,$order_by)
		{
			$query = $this->mdl_store_shoppertrack->get_with_limit($limit,$offset,$order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_shoppertrack->_custom_query($mysql_query);
			return $query;
		}




	}
?>
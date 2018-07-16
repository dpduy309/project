<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Store_basket extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('store_items');
			$this->load->module('store_item_sizes');
			$this->load->module('store_item_colours');

			$this->load->model('mdl_store_basket');
			$this->form_validation->CI =& $this;
		}

		public function remove()
		{
			$update_id = $this->uri->segment(3);
			$allowed = $this->_make_sure_remove_allowed($update_id);
			if($allowed == FALSE)
			{
				redirect(base_url('cart'));
			}

			$this->_delete($update_id);
			$refer_url = $_SERVER['HTTP_REFERER'];

			redirect(base_url('cart'));
			
			
		}

		public function _make_sure_remove_allowed($update_id)
		{
			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$session_id = $row->session_id;
				$shopper_id = $row->shopper_id;
			}

			if(!isset($shopper_id))
			{
				return FALSE;
			}

			$customer_session_id = $this->session->session_id;
			$customer_shopper_id = $this->site_security->_get_user_id();

			if(($session_id == $customer_session_id) OR ($shopper_id == $customer_shopper_id))
			{
				return TRUE;
			}else{
				return FALSE;
			}

		}

		public function add_to_basket()
		{
			$submit = $this->input->post('submit',TRUE);

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('item_colour','Item Colour','numeric');
				$this->form_validation->set_rules('item_size','Item Size','numeric');
				$this->form_validation->set_rules('item_qty','Item Quantity','required|numeric');
				$this->form_validation->set_rules('item_id','Item ID','required|numeric');

				if($this->form_validation->run() == TRUE)
				{
					$data = $this->fetch_the_data();
					$this->_insert($data);
					redirect(base_url('cart'));
				}else{

					$refer_url = $_SERVER['HTTP_REFERER'];

					$error_msg = validation_errors("<p style='color:red;'>","</p>");
					$this->session->set_flashdata('item',$error_msg);

					redirect($refer_url);

				}
			}
		}



		public function fetch_the_data()
		{
			$item_id = $this->input->post('item_id',TRUE);
			$item_data = $this->store_items->fetch_data_from_db($item_id);

			$item_price = $item_data['item_price'];
			$item_qty = $this->input->post('item_qty',TRUE);
			$item_size = $this->input->post('item_size',TRUE);
			$item_colour = $this->input->post('item_colour',TRUE);

			$shopper_id = $this->site_security->_get_user_id();

			if(!is_numeric($shopper_id))
			{
				$shopper_id = 0;
			}


			$data['session_id'] = $this->session->session_id;
			$data['item_title'] = $item_data['item_title'];
			$data['price'] = $item_price;
			$data['tax'] = '0';
			$data['item_id'] = $item_id;
			$data['item_qty'] = $item_qty;
			$data['item_colour'] = $this->_get_value('size',$item_size);
			$data['item_size'] = $this->_get_value('colour',$item_colour);
			$data['date_added'] = time();
			$data['shopper_id'] = $shopper_id;
			$data['ip_address'] = $this->input->ip_address();
			return $data;
		}

		public function _get_value($value_type, $update_id)
		{
			//colour or size
				if($value_type=='size')
				{
					$query = $this->store_item_sizes->get_where($update_id);
					foreach ($query->result() as $row) {
						$item_size = $row->size;
					}
					if(!isset($item_size))
					{
						$item_size = '';
					}
					$value = $item_size;
				}else{
					$query = $this->store_item_colours->get_where($update_id);
					foreach ($query->result() as $row) {
						$item_colour = $row->colour;
					}
					if(!isset($item_colour))
					{
						$item_colour = '';
					}
					$value = $item_colour;
				}
				return $value;
		}

		function test()
		{
			$session_id = $this->session->session_id;
			echo $session_id."<br>";

			$shopper_id = $this->site_security->_get_user_id();
			echo "ID: ".$shopper_id;
		}


		public function get_where($id)
		{
			$query = $this->mdl_store_basket->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_basket->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_basket->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_basket->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_basket->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_basket->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_basket->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_basket->_custom_query($mysql_query);
			return $query;
		}


	}
?>
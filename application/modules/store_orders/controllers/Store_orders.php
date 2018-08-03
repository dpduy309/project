<?php  
	class Store_orders extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('site_pagination');
			$this->load->module('paypal');
			$this->load->module('store_shoppertrack');
			$this->load->module('store_basket');
			$this->load->module('store_accounts');
			$this->load->module('store_order_status');
			$this->load->module('timedate');
			$this->load->module('enquiries');
			$this->load->module('cart');

			$this->load->model('mdl_store_orders');
			
		}

		public function _send_auto_notify($update_id)
		{
			//NOTE: notifies customer when an order status has been updated
			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$order_status = $row->order_status;
				$order_ref = $row->order_ref;
				$shopper_id = $row->shopper_id;
			}

			//get order status title
			$status_title = $this->store_order_status->_get_status_title($order_status);

			//build a message for the customer
			$msg = 'Order '.$order_ref.' has just been updated';
			$msg.= 'The new status  for your order is '.$status_title.'.';
			//send the msg
			$data['subject'] = 'Order Status Update';
			$data['message'] = $msg;
			$data['sent_to'] = $shopper_id;
					
			$data['date_created'] = time();
			$data['sent_by'] = 0; //admin
			$data['opened'] = 0;
			$data['code'] = $this->site_security->generate_random_string(6);
					
			$this->enquiries->_insert($data);
		}

		public function _set_to_opened($update_id)
		{
			$data['opened'] = 1;
			$this->_update($update_id, $data);
		}

		public function _get_order_status_title()
		{
			//NOTE: this get called by browse  and it figures out the order status title
			$order_status = $this->uri->segment(3);
			$order_status = str_replace('status', '', $order_status); //cat chu status
			if(!is_numeric($order_status))
			{
				$order_status = 0;
			}

			if($order_status==0)
			{
				$status_title = 'Order Submitted';
			}else
			{
				$status_title = $this->store_order_status->_get_status_title($order_status);
			}

			return $status_title;
		}

		public function submit_order_status()
		{
			$update_id = $this->uri->segment(3);
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$order_status = $this->input->post('order_status', TRUE);

			if($submit == "Cancle")
			{
				//get the current order status for this order
				$query = $this->get_where($update_id);
				foreach ($query->result() as $row) {
					$order_status = $row->order_status;
				}

				$target_url = base_url('store_orders/browse/status'.$order_status);
				redirect($target_url);
			}elseif($submit == "Submit")
			{
				$data['order_status'] = $order_status;
				$this->_update($update_id, $data);

				$this->_send_auto_notify($update_id);

				$flash_msg = "The order status were successfully updated.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('account',$value);

				redirect(base_url('store_orders/view/'.$update_id));
			}
		}

		public function view()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update
			$this->_set_to_opened($update_id);

			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$data['order_ref'] = $row->order_ref;
				$date_created = $row->date_created;
				$data['paypal_id'] = $row->paypal_id;
				$session_id = $row->session_id;
				$data['opened'] = $row->opened;
				$order_status = $row->order_status;
				$data['shopper_id'] = $row->shopper_id;
				$data['mc_gross'] = $row->mc_gross;
			}

			$data['date_created'] = $this->timedate->get_nice_date($date_created,'full');
			if($order_status==0)
			{
				$data['status_title'] = 'Order Submitted';
			}else
			{
				$data['status_title'] = $this->store_order_status->_get_status_title($order_status);
			}

			//lay thong tin gio hang
			$table = 'store_shoppertrack';
			$data['query_cc'] = $this->cart->_fetch_cart_contents($session_id,$data['shopper_id'],$table);


			$data['order_status'] = $order_status;
			$data['options'] = $this->store_order_status->_get_dropdown_options();
			$data['store_accounts_data'] = $this->store_accounts->fetch_data_from_db($data['shopper_id']);
			$data['customer_address'] = $this->store_accounts-> _get_shopper_address($data['shopper_id'],'<br>');
			$data['headline'] = 'Order '.$data['order_ref'];

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('account');
			$data['view_file'] = "view";

			$this->templates->admin($data);

		}

		public function browse()
		{
			$this->site_security->_make_sure_is_admin();

			$use_limit = FALSE;
			$mysql_query = $this->_generate_mysql_query($use_limit);
			$query = $this->_custom_query($mysql_query);
			$total_items = $query->num_rows();

			$total_items = 50;

			//fetch the item for this page
			$use_limit = TRUE;
			$mysql_query = $this->_generate_mysql_query($use_limit);
			$data['query'] = $this->_custom_query($mysql_query);
			$data['num_rows'] = $data['query']->num_rows();

			$pagination_data['template'] = 'admin';
			$pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
			$pagination_data['total_rows'] = $total_items;
			$pagination_data['offset_segment'] = 4;
			$pagination_data['limit'] = $this->get_limit();
			$pagination_data['offset'] = $this->get_offset();



			$data['site_pagination'] = $this->site_pagination->_generate_pagination($pagination_data);
			$data['showing_statement'] = $this->site_pagination->get_showing_statement($pagination_data);


			$data['current_status_title'] = $this->_get_order_status_title();
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "browse";

			$this->templates->admin($data);
		}

		public function _generate_mysql_query($use_limit)
		{
			$order_status = $this->uri->segment(3);
			$order_status = str_replace('status', '', $order_status); //cat chu status
			if(!is_numeric($order_status))
			{
				$order_status = 0;
			}

			if($order_status > 0)
			{
				$mysql_query = "
					SELECT store_orders.id,
					store_orders.order_ref,
					store_orders.date_created,
					store_orders.opened,
					store_orders.mc_gross,
					store_accounts.firstname,
					store_accounts.lastname,
					store_accounts.company,
					store_order_status.status_title
					FROM store_orders INNER JOIN store_accounts ON store_orders.shopper_id = store_accounts.id
					INNER JOIN store_order_status ON store_orders.order_status = store_order_status.id
					WHERE store_orders.order_status = $order_status order by store_orders.date_created desc
					";
			}else{
				$mysql_query = "
					SELECT store_orders.id,
					store_orders.order_ref,
					store_orders.date_created,
					store_orders.opened,
					store_orders.mc_gross,
					store_accounts.firstname,
					store_accounts.lastname,
					store_accounts.company
					FROM store_orders INNER JOIN store_accounts ON store_orders.shopper_id = store_accounts.id
					WHERE store_orders.order_status = $order_status order by store_orders.date_created desc
					";
			}

			if($use_limit == TRUE)
			{
				$limit = $this->get_limit();
				$offset = $this->get_offset();
				$mysql_query.=" limit ".$offset.", ".$limit;
			}
			return $mysql_query;
		}

		public function get_limit()
		{
			$limit = 5;
			return $limit;
		}

		public function get_offset()
		{
			$offset = $this->uri->segment(4);

			if(!isset($offset))
			{
				$offset = 0;
			}

			return $offset;
		}

		public function get_target_pagination_base_url()
		{
			$first_bit = $this->uri->segment(1);
			$second_bit = $this->uri->segment(2);
			$third_bit = $this->uri->segment(3);
			$target_base_url = base_url().$first_bit."/".$second_bit."/".$third_bit;
			return $target_base_url;

		}		

		public function _get_mc_gross($paypal_id)
		{
			$query = $this->paypal->get_where($paypal_id);
			foreach ($query->result() as $row) {
				$posted_information = $row->posted_information;
			}

			if(!isset($posted_information))
			{
				$mc_gross = 0;
			}else{
				$posted_information = unserialize($posted_information);
				$mc_gross = $posted_information['mc_gross'];
			}
			return $mc_gross;
		}

		public function _get_shopper_id($customer_session_id)
		{
			$query = $this->store_basket->get_where_custom('session_id',$customer_session_id);
			foreach ($query->result() as $row) {
				$shopper_id = $row->shopper_id;
			}
			if(!isset($posted_information))
			{
				$shopper_id = 0;
			}
			return $shopper_id;
		}

		public function _auto_generate_order($paypal_id, $customer_session_id)
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
			$data['shopper_id'] = $this->_get_shopper_id($customer_session_id);
			$data['mc_gross'] = $this->_get_mc_gross($paypal_id);

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

		public function get_with_double_condition($col1,$value1,$col2,$value2)
		{
			$query = $this->mdl_store_orders->get_with_double_condition($col1,$value1,$col2,$value2);
			return $query;
		}



	}
?>
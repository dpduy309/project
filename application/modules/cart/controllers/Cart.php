<?php  
	class Cart extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('store_item_colours');
			$this->load->module('store_item_sizes');
			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('store_basket');
			$this->load->module('store_items');
			$this->load->module('shipping');
			$this->load->module('paypal');
			$this->load->module('store_accounts');


		}

		public function _calc_cart_total($cart_data)
		{
			$shopper_id = $cart_data['shopper_id'];
			$customer_session_id = $cart_data['customer_session_id'];
			$table = $cart_data['table'];
			$add_shipping = $cart_data['add_shipping'];

			$query = $this->_fetch_cart_contents($customer_session_id,$shopper_id,$table);

			$grand_total = 0;
			foreach ($query->result() as $row) {
				$sub_total = $row->price*$row->item_qty;
				$grand_total = $grand_total + $sub_total;
			}

			if($add_shipping == TRUE)
			{
				$shipping = $this->shipping->_get_shipping();
			}else{
				$shipping = 0;
			}

			$grand_total = $grand_total + $shipping;
			return $grand_total;
		}

		public function _check_and_get_session_id($checkout_token)
		{
			$session_id	= $this->_get_session_id_from_token($checkout_token);
			if($session_id=='')
			{
				redirect(base_url());
			}

			//check to see if this session ID appears on store_basket table
			$query = $this->store_basket->get_where_custom('session_id',$session_id);
			$num_rows = $query->num_rows();

			if($num_rows<1)
			{
				redirect(base_url());
			}

			return $session_id;
		}

		public function _create_checkout_token($session_id)
		{
			$encrypted_str = $this->site_security->_encrypt_string($session_id);
			//remove ki tu 
			$checkout_token = str_replace('+','-plus-', $encrypted_str);
			$checkout_token = str_replace('/','-fwrd-', $checkout_token);
			$checkout_token = str_replace('=','-eqls-', $checkout_token);
			return $checkout_token;
		}

		public function _get_session_id_from_token($checkout_token)
		{
			$session_id = str_replace('-plus-','+', $checkout_token);
			$session_id = str_replace('-fwrd-','/', $session_id);
			$session_id = str_replace('-eqls-','=', $session_id);

			$session_id = $this->site_security->_decrypt_string($session_id);

			return $session_id;
		}

		public function _generate_guest_account($checkout_token)
		{
			//customer has selected No Thanks
			$customer_session_id = $this->_get_session_id_from_token($checkout_token);

			//create guest account
			$ref = $this->site_security->generate_random_string(4);
			$data['username'] = 'Guest'.$ref;
			$data['firstname'] = 'Guest';
			$data['lastname'] = 'Account';
			$data['date_made'] = time();
			$data['pword'] = $checkout_token; //security
			$this->store_accounts->_insert($data);

			//get the new account ID
			$new_account_id = $this->store_accounts->get_max();
			foreach ($new_account_id->result() as $row) {
				$new_account_id = $row->id;
			}

			$mysql_query = "update store_basket set shopper_id='$new_account_id' ";
			$mysql_query.= "where session_id='$customer_session_id'";
			$query = $this->store_accounts->_custom_query($mysql_query);

		}

		public function submit_choice()
		{
			$submit = $this->input->post('submit', TRUE);
			if($submit == "No Thanks")
			{
				$checkout_token = $this->input->post('checkout_token',TRUE);
				$this->_generate_guest_account($checkout_token);
				redirect(base_url('cart/index/'.$checkout_token));
			}elseif ($submit == "Yes - Let's Do It") {
				redirect(base_url('youraccount/start'));
			}
		}


		public function go_to_checkout()
		{
			$shopper_id = $this->site_security->_get_user_id();
			
			if(is_numeric($shopper_id))
			{
				redirect(base_url('cart')); //phai co account moi cho checkout
			}

			$data['checkout_token'] = $this->uri->segment(3);
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "go_to_checkout";
			$this->templates->public_bootstrap($data);
		}

		public function _attempt_draw_checkout_btn($query)
		{
			$data['query'] = $query;
			$shopper_id = $this->site_security->_get_user_id();
			$third_bit = $this->uri->segment(3);



			if((!is_numeric($shopper_id)) AND ($third_bit==''))
			{
				$this->_draw_checkout_btn_fake($query);
			}else
			{
				$this->_draw_checkout_btn_real($query);
			}
		}

		public function _draw_checkout_btn_fake($query)
		{
			foreach ($query->result() as $row) {
				$session_id = $row->session_id;
			}

			$data['checkout_token'] = $this->_create_checkout_token($session_id);
			$this->load->view('checkout_btn_fake',$data);
		}

		public function _draw_checkout_btn_real($query)
		{
			$this->paypal->_draw_checkout_btn($query);
		}

		public function _draw_cart_contents($query, $user_type)
		{
			//user_type can be admin or public

			if($user_type == 'public')
			{
				$view_file = 'cart_contents_public';
			}else{
				$view_file = 'cart_contents_admin';
			}

			$data['shipping'] = $this->shipping->_get_shipping();
			$data['query'] = $query;
			$this->load->view($view_file,$data);
		}

		public function index()
		{
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "cart";

			$third_bit = $this->uri->segment(3);
			if($third_bit!='')
			{
				//check that the token is cool, then return session ID
				$session_id = $this->_check_and_get_session_id($third_bit);
			}else{
				$session_id = $this->session->session_id;
			}

			$shopper_id = $this->site_security->_get_user_id();
			if(!is_numeric($shopper_id))
			{
				$shopper_id = 0;
			}

			$table = 'store_basket';
			$data['query'] = $this->_fetch_cart_contents($session_id,$shopper_id,$table);
			$data['num_rows'] = $data['query']->num_rows(); //dem so san pham trong gio hang
			$data['showing_statement'] = $this->_get_showing_statement($data['num_rows']);
			$this->templates->public_bootstrap($data);	
		}

		public function _get_showing_statement($num_items)
		{
			if($num_items == 1)
			{
				$showing_statement = "You have one item in your shopping basket.";
			}else{
				$showing_statement = "You have ".$num_items." items in your shopping basket.";
			}
			return $showing_statement;
		}

		public function _fetch_cart_contents($session_id,$shopper_id,$table)
		{
			//fetch the contents of the shopping cart
			$mysql_query = "
				SELECT $table.*,
				store_items.small_pic,
				store_items.item_url
				FROM $table LEFT JOIN store_items ON $table.item_id = store_items.id
				";
			if($shopper_id>0)
			{
				$where_condition = "WHERE $table.shopper_id=$shopper_id";
			}else{
				$where_condition = "WHERE $table.session_id='$session_id'";
			}

			$mysql_query.=$where_condition;
			$query = $this->store_basket->_custom_query($mysql_query);
			return $query;
		}

		public function _draw_add_to_cart($item_id)
		{
			//fetch the data colour options
			$submitted_colour = $this->input->post('submitted_colour',TRUE);
			if($submitted_colour =="")
			{
				$colour_options[''] = "Select ... ";
			}

			$query = $this->store_item_colours->get_where_custom('item_id',$item_id);
			$data['num_colours'] = $query->num_rows();
			foreach ($query->result() as $row) {
				$colour_options[$row->id] = $row->colour;
			}

			//fetch the data size options
			$submitted_size = $this->input->post('submitted_size',TRUE);
			if($submitted_size == "")
			{
				$size_options[''] = "Select ...";
			}

			$query = $this->store_item_sizes->get_where_custom('item_id',$item_id);
			$data['num_sizes'] = $query->num_rows();
			foreach($query->result() as $row)
			{
				$size_options[$row->id] = $row->size;
			}


			$data['submitted_colour'] = $submitted_colour;
			$data['colour_options'] = $colour_options;
			$data['submitted_size'] = $submitted_size;
			$data['size_options'] = $size_options;
			$data['item_id'] = $item_id;
			$this->load->view('add_to_cart',$data);


		}
	}


?>
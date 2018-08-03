<?php 
	class Templates extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('site_security');
			$this->load->module('cart');
			$this->load->module('site_settings');

		}

		public function _draw_top_nav_jqm($customer_id)
		{
			$top_nav_btns = [
				['text' => 'Home', 'icon' => 'home', 'btn_target_url' =>base_url()],
				['text' => 'Login', 'icon' => 'action', 'btn_target_url' =>base_url('youraccount/login')],
				['text' => 'Account', 'icon' => 'user', 'btn_target_url' =>base_url('youraccount/update_page')],
				['text' => 'Contact', 'icon' => 'phone', 'btn_target_url' =>base_url('contactus')],
				['text' => 'Basket', 'icon' => 'shop', 'btn_target_url' =>base_url('cart')],
			];

			if((is_numeric($customer_id)) AND ($customer_id>0))
			{
				//custome is login -> an Login
				unset($top_nav_btns['1']);
			}else{
				unset($top_nav_btns['2']);
			}

			$data['top_nav_btns'] = $top_nav_btns;
			$data['current_url'] = current_url();
			$this->load->view('top_nav_jqm',$data);
		}

		public function _draw_page_top()
		{
			$shopper_id = $this->site_security->_get_user_id();

			$this->_draw_page_top_lhs();
			$this->_draw_page_top_mid($shopper_id);
			$this->_draw_page_top_rhs($shopper_id);
		}

		public function _draw_page_top_lhs()
		{
			$this->load->view('page_top_lhs');
		}

		public function _draw_page_top_rhs($shopper_id)
		{
			$cart_data['shopper_id'] = $shopper_id;
			$cart_data['customer_session_id'] = $this->session->session_id;
			$cart_data['table'] = 'store_basket';
			$cart_data['add_shipping'] = FALSE;
			$cart_total = $this->cart->_calc_cart_total($cart_data);

			if($cart_total<0.01)
			{
				$cart_info = "Your Basket Is Empty";
			}else{
				$cart_total_desc = number_format($cart_total,2);
				$cart_total_desc = str_replace('.00','',$cart_total_desc);
				$cart_info = "Basket Total: ".$cart_total_desc;
			}

			$data['cart_info'] = $cart_info;
			$this->load->view('page_top_rhs',$data);
		}

		public function _draw_page_top_mid($shopper_id)
		{

			if(is_numeric($shopper_id) AND ($shopper_id>0))
			{
				$view_file = 'page_top_mid_in'; //urser is logged in
			}else{
				$view_file = 'page_top_mid_out'; //urser is not logged in
			}

			$this->load->view($view_file);
		}

		public function test()
		{
			$data = "";
			$this->public_jqm($data);
		}

		public function _draw_breadcrumbs($breadcrumbs_data)
		{
			//to work, must contain: template, cur_page_title , breadcrumbs_array
			$this->load->view('breadcrumbs_public_bootstrap',$breadcrumbs_data);
		}

		public function login($data)
		{
			if(!isset($data['view_module']))
			{
				$data['view_module'] = $this->uri->segment(1);	
			}
			$this->load->view('login_page',$data);
		}

		public function public_bootstrap($data)
		{
			if(!isset($data['view_module']))
			{
				$data['view_module'] = $this->uri->segment(1);	
			}

			$data['customer_id'] = $this->site_security->_get_user_id();

			$this->load->view('public_bootstrap',$data);
		}

		public function public_jqm($data)
		{
			if(!isset($data['view_module']))
			{
				$data['view_module'] = $this->uri->segment(1);	
			}

			$data['customer_id'] = $this->site_security->_get_user_id();

			$this->load->view('public_jqm',$data);
		}

		public function admin($data)
		{
			if(!isset($data['view_module']))
			{
				$data['view_module'] = $this->uri->segment(1);	
			}
			$this->load->view('admin',$data);
		}
	}


?>
<?php 
	class Templates extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('site_security');

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
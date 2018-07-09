<?php  
	class Dashboard extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_cookies');
			
		}

		public function home()
		{
			$this->site_security->_make_sure_is_admin();

			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "home";

			$this->templates->admin($data);

		}
		
		



	}
?>
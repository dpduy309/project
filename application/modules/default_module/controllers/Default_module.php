<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Default_module extends MX_Controller
	{	
		//This is the module that load by default
		public function __construct()
		{
			parent::__construct();
			$this->load->module('webpages');
			$this->load->module('templates');

		}

		public function index()
		{
			$first_bit = trim($this->uri->segment(1));

			$query = $this->webpages->get_where_custom('page_url',$first_bit);

			$num_rows = $query->num_rows();

			if($num_rows>0)
			{
				foreach ($query->result() as $row) {
					$data['page_url'] = $row->page_url;
					$data['page_title'] = $row->page_title;
					$data['page_keywords'] = $row->page_keywords;
					$data['page_content'] = $row->page_content;
					$data['page_description'] = $row->page_description;
				}

			}else{
				$data['page_content'] = "<h1>It's a webpage but not as we know it!</h1>";
			} 
			$this->templates->public_bootstrap($data);
				
		}

	}
		
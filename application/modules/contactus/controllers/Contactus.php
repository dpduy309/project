<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Contactus extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('enquiries');
			$this->load->module('blacklist');
		}

		public function submit()
		{

			$submit = $this->input->post('submit',TRUE);
			$refer_url = $_SERVER['HTTP_REFERER'];
			$target_refer_url = base_url('contactus');
			$firstname = trim($this->input->post('firstname',TRUE));//hidden var

			if($firstname!='')
			{
				$this->_blacklist_user();
			}

			if($submit == "Submit" AND ($refer_url == $target_refer_url))
			{
				
				$this->form_validation->set_rules('yourname','Your Name','required|max_length[60]');
				$this->form_validation->set_rules('email','Email','required|valid_email');
				$this->form_validation->set_rules('telnum','Telephone Number','required|max_length[20]');
				$this->form_validation->set_rules('message','Message','required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$posted_data = $this->fetch_data_from_post();

					//insert the message
					$data['code'] = $this->site_security->generate_random_string(6);
					$data['subject'] = 'Contact Form';
					$data['message'] = $this->build_msg($posted_data);
					$data['sent_to'] = 0;
					$data['date_created'] = time();
					$data['opened'] = 0;
					$data['sent_by'] = 0;
					$data['urgent'] = 0;



					$this->enquiries->_insert($data);

					redirect(base_url('contactus/thankyou'));
					
				}else{
					$this->index();
				}
			}
		}

		public function build_msg($posted_data)
		{
			$yourname = ucfirst($posted_data['yourname']);
			$msg .= $yourname.' submitted the following information:<br><br>';
			$msg .= 'Name: '.$yourname."<br>";
			$msg .= 'Email: '.$posted_data['email']."<br>";
			$msg .= 'Telephone Number: '.$posted_data['telnum']."<br>";
			$msg .= 'Message: '.$posted_data['message']."<br>";
			return $msg;
		}

		public function fetch_data_from_post()
		{
			$data['yourname'] = $this->input->post('yourname', TRUE);
			$data['email'] = $this->input->post('email', TRUE);
			$data['telnum'] = $this->input->post('telnum', TRUE);
			$data['message'] = $this->input->post('message', TRUE);
			return $data;
		}

		public function index()
		{
			$data = $this->fetch_data_from_post();
			$data['address'] = $this->site_settings->_get_our_address();
			$data['our_telnum'] = $this->site_settings->_get_our_telnum();
			$data['name'] = $this->site_settings->_get_our_name();
			$data['map_code'] = $this->site_settings->_get_map_code();
			$data['form_location'] = base_url('contactus/submit');
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "contactus";

			$this->templates->public_bootstrap($data);	
		}


		public function thankyou()
		{

	
			$data['view_file'] = "thankyou";

			$this->templates->public_bootstrap($data);	
		}

		public function _blacklist_user()
		{
			$data['ip_address'] = $this->input->ip_address;
			$data['date_created'] = time();
			$this->blacklist->_insert($data);
		}

	
	}
?>
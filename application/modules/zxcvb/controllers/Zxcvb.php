<?php  
	class Zxcvb extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_cookies');
			$this->load->module('store_accounts');

			$this->form_validation->CI =& $this;
		}

		
		public function index()
		{
			$data['username'] = $this->input->post('username', TRUE);
			$this->templates->login($data);		
		}

		public function submit_login()
		{
			$submit = $this->input->post('submit',TRUE);
			if($submit == "Submit")
			{
				$this->form_validation->set_rules('username','Username','required|min_length[3]|callback_username_check'); 
				$this->form_validation->set_rules('pword','Password','required|min_length[3]|max_length[35]');

				if($this->form_validation->run() == TRUE)
				{
					$this->_in_you_go();

				}else{
					echo validation_errors();
				}
			}
		}

		public function _in_you_go()
		{
			
			//set a session variable
			$this->session->set_userdata('is_admin','1');

			redirect(base_url('dashboard/home'));
		}

		public function logout()
		{
			unset($_SESSION['is_admin']);
			redirect(base_url());
		}


		public function username_check($str)
        {
        	$error_msg = "You did not enter a correct username or password";

        	$pword = $this->input->post('pword', TRUE);
        	$result = $this->site_security->_check_admin_login_details($str,$pword);

        	if($result == FALSE)
        	{
        		$this->form_validation->set_message('username_check', $error_msg);
        		return FALSE;
        	}else
        	{
        		return TRUE;
        	}


        }
		



	}
?>
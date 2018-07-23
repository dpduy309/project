<?php  
	class Youraccount extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_cookies');
			$this->load->module('store_accounts');
			$this->load->module('store_basket');

			$this->form_validation->CI =& $this;
		}

	

		public function logout()
		{
			unset($_SESSION['user_id']);
			$this->site_cookies->_destroy_cookie();
			redirect(base_url());
		}

		public function welcome()
		{
			$this->site_security->_make_sure_logged_in();
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "welcome";
			$this->templates->public_bootstrap($data);
		}

		public function test1()
		{
			$your_name = "Duy Bin";
			$this->session->set_userdata('your_name', $your_name);
			echo "The session variable was set.";

			echo "<hr>";
			echo anchor('youraccount/test2', 'Get[display] the session variable.')."<br>";
			echo anchor('youraccount/test3', 'Unset[destroy] the session variable.')."<br>";
		}

		public function test2()
		{
			$your_name = $this->session->userdata('your_name');
			if($your_name!="")
			{
				echo "Hello $your_name!";
			} else
			{
				echo "NO session variable has been set for your_name";
			}
			echo "<hr>";
			echo anchor('youraccount/test1', 'Set the session variable.')."<br>";
			echo anchor('youraccount/test3', 'Unset[destroy] the session variable.')."<br>";
		}
		
		public function test3()
		{
			unset($_SESSION['your_name']);
			echo "The session variable was unset";

			echo "<hr>";
			echo anchor('youraccount/test1', 'Set the session variable.')."<br>";
			echo anchor('youraccount/test2', 'Get[display] the session variable.')."<br>";
		}

		public function login()
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
					//figure out the user id
					$col1 = 'username';
		        	$value1 = $this->input->post('username', TRUE);
		        	$col2 = 'email';
		        	$value2 = $this->input->post('username', TRUE);

		        	$query = $this->store_accounts->get_with_double_condition($col1,$value1,$col2,$value2);
		        	foreach ($query->result() as $row) {
		        		$user_id = $row->id;
		        	}

		        	$remember = $this->input->post('remember', TRUE);
		        	if($remember == "remember-me")
		        	{
		        		$login_type = "longterm";
		        	} else
		        	{
		        		$login_type = "shortterm";
		        	}

		        	$data['last_login'] = time();
		        	$this->store_accounts->_update($user_id,$data);

					//send them to the private page
					$this->_in_you_go($user_id,$login_type);

				}else{
					echo validation_errors();
				}
			}
		}

		public function _in_you_go($user_id, $login_type)
		{
			//NOTE: login type can be long or short
			if($login_type == "longterm")
			{
				//set a cookie
				$this->site_cookies->_set_cookie($user_id);
			}else{
				//set a session variable
				$this->session->set_userdata('user_id', $user_id);

			}

			//attempt to update cart  and divirt to cart
			$this->_attempt_cart_divert($user_id);

			redirect(base_url('youraccount/welcome'));
		}

		public function _attempt_cart_divert($user_id){

			//check session id dung va shopper id = 0 de cap nhat lai shopper id =1
			$customer_session_id = $this->session->session_id;

			$col1 = 'session_id';
			$value1 = $customer_session_id;
			$col2 = 'shopper_id';
			$value2 = 0;
			$query = $this->store_basket->get_with_double_condition($col1,$value1,$col2,$value2);

			$num_rows = $query->num_rows();
			if($num_rows > 0)
			{
				//there are records that need corrected
				$mysql_query = "update store_basket set shopper_id=$user_id where session_id='$customer_session_id'";
				$query = $this->store_basket->_custom_query($mysql_query);
				redirect(base_url('cart'));
			}
		}

		public function submit()
		{
			$submit = $this->input->post('submit',TRUE);
			if($submit == "Submit")
			{
				$this->form_validation->set_rules('username','Username','required|min_length[3]'); 
				//|is_unique[store_accounts.username]
				//is_unique kiem tra username da ton tai
				$this->form_validation->set_rules('email','Email','required|valid_email');
				$this->form_validation->set_rules('pword','Password','required|min_length[3]|max_length[35]');
				$this->form_validation->set_rules('repeat_pword','Repeat Password','matches[pword]|required');

				if($this->form_validation->run() == TRUE)
				{
					$this->_process_create_account();
					redirect(base_url('youraccount/login'));
				}else{
					$this->start();
				}
			}
		}

		public function _process_create_account()
		{
			$data = $this->fetch_data_from_post();
			unset($data['repeat_pword']);

			$pword = $data['pword'];
			$data['pword'] = $this->site_security->_hash_string($pword);
			$this->store_accounts->_insert($data);
		}

		public function start()
		{
			$data = $this->fetch_data_from_post();
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "start";
			$this->templates->public_bootstrap($data);
		}

		public function fetch_data_from_post()
		{
			$data['username'] = $this->input->post('username', TRUE);
			$data['email'] = $this->input->post('email', TRUE);
			$data['pword'] = $this->input->post('pword', TRUE);
			$data['repeat_pword'] = $this->input->post('repeat_pword', TRUE);
			return $data;
		}

		public function username_check($str)
        {
        	$error_msg = "You did not enter a correct username or password";

        	$col1 = 'username';
        	$value1 = $str;
        	$col2 = 'email';
        	$value2 = $str;
        	$query = $this->store_accounts->get_with_double_condition($col1,$value1,$col2,$value2);
        	$num_rows = $query->num_rows();

        	if($num_rows < 1)
        	{
        		$this->form_validation->set_message('username_check', $error_msg);
        		return FALSE;
        	}

        	foreach ($query->result() as $row) {
        		$pword_on_table = $row->pword;
        	}

        	$pword = $this->input->post('pword', TRUE);

        	$result = $this->site_security->_verify_hash($pword, $pword_on_table);
        	
        	if($result == TRUE)
        	{
        		return TRUE;
        	}else
        	{
        		$this->form_validation->set_message('username_check', $error_msg);
        		return FALSE;
        	}

        }
	}
?>
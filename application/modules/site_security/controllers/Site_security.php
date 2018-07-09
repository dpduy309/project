<?php  
	class Site_security extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('site_cookies');
		}

		// function test()
		// {
		// 	$name = 'Bin';
		// 	$hashed_name = $this->_hash_string($name);
		// 	echo 'You are '.$name.'<br>';
		// 	echo $hashed_name;

		// 	echo '<hr>';
		// 	$submitted_name = 'Duy';
		// 	$result = $this->_verify_hash($submitted_name,$hashed_name);

		// 	if($result == TRUE)
		// 	{
		// 		echo 'well done';
		// 	} else
		// 	{
		// 		echo 'fail. You can not hack me!';
		// 	}
		// }

		public function _check_admin_login_details($username,$pword)
		{
			$target_username = "admin";
			$target_pass = "password";

			if(($username == $target_username) && ($pword == $target_pass))
			{
				return TRUE;
			}else{
				return FALSE;
			}
		}

		public function _make_sure_logged_in()
		{
			$user_id = $this->_get_user_id();
			if(!is_numeric($user_id))
			{
				redirect(base_url('youraccount/login'));
			}
		}

		public function _get_user_id()
		{
			//attempt  to get the ID of the user

			//start by checking for a session variable
			$user_id = $this->session->userdata('user_id');

			if(!is_numeric($user_id))
			{
				//check for a valid cookie
				$user_id = $this->site_cookies->_attempt_get_user_id();
			}

			return $user_id;
		}

		public function _hash_string($str)
		{
			$hashed_string = password_hash($str, PASSWORD_BCRYPT, array(

				'cost' => 11  //tang do dai chuoi bam -> tang them do an toan

			));	
			return $hashed_string;
		}

		public function _verify_hash($plain_text_str, $hashed_string)
		{
			$result = password_verify($plain_text_str, $hashed_string); //ham co san cua he thong
			return $result; //TRUE or FALSE
		}

		// public function generate_random_string($length)
		// {
	 //        $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	 //        $randomString = '';

	 //        for($i = 0; $i < $length; $i++){
	 //            $randomString .= $characters[rand(0, strlen($characters) - 1 )];
	 //        }

	 //        return $randomString;
	 //    }﻿

		public function generate_random_string($length)
		{
		    $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
		    $randstring = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randstring .= $characters[rand(0, strlen($characters)-1)];
		    }
		    return $randstring;
		}

		public function _make_sure_is_admin()
		{

			$is_admin = $this->session->userdata('is_admin');
			if($is_admin == 1)
			{
				return TRUE;
			} else
			{
				redirect(base_url('/site_security/not_allowed'));
			}

		}

		public function not_allowed()
		{
			echo "You are not allowed to be here";
		}

	}
?>
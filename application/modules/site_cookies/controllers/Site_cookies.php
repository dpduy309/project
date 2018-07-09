<?php  
	class Site_cookies extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->model('mdl_site_cookies');
			
			$this->form_validation->CI =& $this;
		}

		public function test()
		{
			echo anchor('site_cookies/test_set', 'Set The Cookie');
			echo "<hr>";

			echo anchor('site_cookies/test_destroy', 'Destroy The Cookie');

			$user_id = $this->_attempt_get_user_id();
			if(is_numeric($user_id))
			{
				echo "<h1>You are user $user_id</h1>";
			}

		}

	

		public function test_set()
		{
			$user_id = 55;
			$this->_set_cookie($user_id);
			echo "The cookie has been set";

			echo anchor('site_cookies/test', 'Get The Cookie');
			echo "<hr>";

			echo anchor('site_cookies/test_destroy', 'Destroy The Cookie');
		}

		public function test_destroy()
		{
			$this->_destroy_cookie();
			echo "The cookie has been destroyed";

			echo anchor('site_cookies/test', 'Attempt To Get The Cookie');
			echo "<hr>";
			echo anchor('site_cookies/test_set', 'Set The Cookie');
			
		}

		public function _set_cookie($user_id)
		{

			$nowtime = time();
			$one_day = 86400;
			$two_weeks = $one_day*14;
			$two_weeks_ahead = $nowtime + $two_weeks;

			$data['cookie_code'] = $this->site_security->generate_random_string(128);
			$data['user_id'] = $user_id;
			$data['expiry_date'] = $two_weeks_ahead;

			$this->_insert($data);

			$cookie_name = $this->site_settings->_get_cookie_name();

			setcookie($cookie_name, $data['cookie_code'], $data['expiry_date']);
			$this->_auto_delete_old();
		}

		public function _attempt_get_user_id()
		{
			//kiem tra user id co valid cookies
			$cookie_name = $this->site_settings->_get_cookie_name();
			
			//check for cookie
			if(isset($_COOKIE[$cookie_name]))
			{
				$cookie_code = $_COOKIE[$cookie_name];

				//have the cookie but is it still on the table?
				$query = $this->get_where_custom('cookie_code', $cookie_code);
				$num_rows = $query->num_rows();

				if($num_rows<1)
				{
					$user_id = '';
				}

				foreach ($query->result() as $row) {
					$user_id = $row->user_id;
				}
			}else{
				$user_id = '';
			}
			return $user_id;

		}

		public function _destroy_cookie()
		{
			$cookie_name = $this->site_settings->_get_cookie_name();

			if(isset($_COOKIE[$cookie_name]))
			{
				$cookie_code = $_COOKIE[$cookie_name];
				$mysql_query = "delete from site_cookies where cookie_code=?";
				$this->db->query($mysql_query,array($cookie_code));
			}

			setcookie($cookie_name, '',time() - 3600);

			
		}

		public function _auto_delete_old()
		{
			$nowtime = time();
			$mysql_query = "delete from site_cookies where expiry_date<$nowtime";
			$query = $this->_custom_query($mysql_query);
		}


		public function get_where($id)
		{
			$query = $this->mdl_site_cookies->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_site_cookies->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_site_cookies->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_site_cookies->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_site_cookies->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_site_cookies->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($limit,$offset,$order_by)
		{
			$query = $this->mdl_site_cookies->get_with_limit($limit,$offset,$order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_site_cookies->_custom_query($mysql_query);
			return $query;
		}




	}
?>
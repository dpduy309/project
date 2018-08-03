<?php  
	class Store_order_status extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');


			$this->load->model('mdl_store_order_status');

			$this->form_validation->CI =& $this;
		}

		public function _get_dropdown_options()
		{
			$options[0] = 'Order Submitted';

			$query = $this->get('status_title');
			foreach ($query->result() as $row) {
				$options[$row->id] = $row->status_title;
			}

			return $options;
		}

		public function _get_status_title($update_id)
		{
			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$status_title = $row->status_title;
			}

			if(!isset($status_title))
			{
				$status_title = 'Unknown';
			}

			return $status_title;
		}

		public function _draw_left_nav_links()
		{
			$data['query_sos'] = $this->get('status_title');
			$this->load->view('left_nav_links',$data);

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			} 
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Option';
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "deleteconf";

			$this->templates->admin($data);
		}

			public function delete($update_id){
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$submit =$this->input->post('submit');
			if($submit == 'Cancel')
			{
				redirect(base_url('store_order_status/create/'.$update_id));
			} elseif($submit == 'Yes - Delete')
			{
				$allowed = $this->_make_sure_delete_allowed($update_id);
				if($allowed == FALSE)
				{
					$flash_msg = "You are not allowed to delete this status option.";
					$value = '<div class="alert alert-danger" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);

					redirect(base_url('store_accounts/manage'));
				}

				$this->_delete($update_id);

				$flash_msg = "The status order option was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('store_order_status/manage'));

		}

		public function _make_sure_delete_allowed($update_id)
		{
			//returns TRUE or FALSE
			//do NOT allow delete IF has an order has this status
			$mysql_query = "select * from store_orders where order_status='$update_id'";
			$query = $this->_custom_query($mysql_query);
			$num_rows = $query->num_rows();

			if($num_rows>0)
			{
				return FALSE; //ko xoa duoc vi co item trong gio hang
			}else
			{
				return TRUE;
			}
		}

		public function _get_customer_id_from_token($token)
		{
			$last_six_chars = substr($token,0,6); //last 6 from stored (hashed)  pword
			$date_made = substr($token,6,10);
			$last_login = substr($token,16,10);

			$sql = "SELECT * FROM store_order_status WHERE date_made = ? AND pword LIKE ? AND last_login = ?";
			$query = $this->db->query($sql, array($date_made,'%'.$last_six_chars,$last_login));

			foreach ($query->result() as $row) {
				$customer_id = $row->id;
			}

			if(!isset($customer_id))
			{
				$customer_id = 0;
			}

			return $customer_id;
		}


		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('store_order_status/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('status_title','Status Title','required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();

					if(is_numeric($update_id))
					{
						//update the account details
						$this->_update($update_id,$data);
						$flash_msg = "The status title were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('account',$value);

					} else{

						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new account

						$flash_msg = "The order status option were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('account',$value);

						redirect(base_url('store_order_status/create/'.$update_id));
					}
				}
			}

			if((is_numeric($update_id)) && $submit != "Submit") //neu co id va chua submit -> lay du lieu tu databse
			{
				$data = $this->fetch_data_from_db($update_id);
			} else{
				$data = $this->fetch_data_from_post(); //nguoc lai thi them moi
			}

			if(!is_numeric($update_id)) 
			{
				$data['headline'] = 'Add New Order Status Option';
			} else{
				$data['headline'] = 'Update Order Status Option';
			}

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('account');
			//$data['view_module'] = "store_order_status";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}

	

		public function fetch_data_from_post() //lay data tu form
		{
			$data['status_title'] = $this->input->post('status_title',TRUE);
		
			return $data;
		}

		public function fetch_data_from_db($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}

			$query = $this->get_where($update_id);
			foreach ($query->result() as $row)
			{
			
				$data['status_title'] = $row->status_title;

				if(!isset($data))
				{
					$data = "";
				}

				return $data;
			}
		}


		public function manage()
		{
			$this->site_security->_make_sure_is_admin();

			$data['query'] = $this->get('status_title');
			$data['flash'] = $this->session->flashdata('account');

			//$data['view_module'] = "store_order_status";
			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}

		public function get_where($id)
		{
			if(!is_numeric($id))
			{
				die('Nom-numeric variable!');
			}

			$query = $this->mdl_store_order_status->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_order_status->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_order_status->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_order_status->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_order_status->get($order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_order_status->_custom_query($mysql_query);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_order_status->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_double_condition($col1,$value1,$col2,$value2)
		{
			$query = $this->mdl_store_order_status->get_with_double_condition($col1,$value1,$col2,$value2);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_order_status->get_with_limit($col,$value);
			return $query;
		}			


	}
?>
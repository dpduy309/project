<?php  
	class Store_accounts extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');


			$this->load->model('mdl_store_accounts');

			$this->form_validation->CI =& $this;
		}

		public function _get_shopper_address($update_id,$delimiter)
		{
			$data = $this->fetch_data_from_db($update_id);
			$address = '';

			if($data['address1'] != '')
			{
				$address.=$data['address1'];
				$address.= $delimiter;
			}

			if($data['address2'] != '')
			{
				$address.=$data['address2'];
				$address.= $delimiter;
			}

			if($data['town'] != '')
			{
				$address.=$data['town'];
				$address.= $delimiter;
			}

			if($data['country'] != '')
			{
				$address.=$data['country'];
				$address.= $delimiter;
			}

			if($data['postcode'] != '')
			{
				$address.=$data['postcode'];
				$address.= $delimiter;
			}

			return $address;
		}


		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			} 
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Account';
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
				redirect(base_url('store_accounts/create/'.$update_id));
			} elseif($submit == 'Yes - Delete')
			{
				$allowed = $this->_make_sure_delete_allowed($update_id);
				if($allowed == FALSE)
				{
					$flash_msg = "You are not allowed to delete this account.";
					$value = '<div class="alert alert-danger" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);

					redirect(base_url('store_accounts/manage'));
				}

				$this->_delete($update_id);

				$flash_msg = "The store account entry was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);

				redirect(base_url('store_accounts/manage'));
			}


		}

		public function _make_sure_delete_allowed($update_id)
		{
			//returns TRUE or FALSE
			//do NOT allow delete IF shopper has item in basket (or shoppertrack)
			$mysql_query = "select * from store_basket where shopper_id='$update_id'";
			$query = $this->_custom_query($mysql_query);
			$num_rows = $query->num_rows();


			if($num_rows>0)
			{
				return FALSE; //ko xoa duoc vi co item trong gio hang
			}else
			{
				$mysql_query = "select * from store_shoppertrack where shopper_id='$update_id'";
				$query = $this->_custom_query($mysql_query);
				$num_rows = $query->num_rows();

				if($num_rows>0)
				{
					return FALSE; //ko xoa duoc vi co item trong gio hang
				}
			}

			return TRUE; //duoc xoa
		}

		public function _generate_token($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$date_made = $data['date_made'];
			$last_login = $data['last_login'];
			$pword = $data['pword'];

			$pword_length = strlen($pword);
			$start_point = $pword_length-6;
			$last_six_chars = substr($pword, $start_point, 6);

			if(($pword_length>5) AND ($last_login>0))
			{
				$token = $last_six_chars.$date_made.$last_login;
			}else{
				$token = '';
			}

			return $token;
		}

		public function _get_customer_id_from_token($token)
		{
			$last_six_chars = substr($token,0,6); //last 6 from stored (hashed)  pword
			$date_made = substr($token,6,10);
			$last_login = substr($token,16,10);

			$sql = "SELECT * FROM store_accounts WHERE date_made = ? AND pword LIKE ? AND last_login = ?";
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

		public function _get_customer_name($update_id, $optional_customer_data=NULL)
		{

			if(!isset($optional_customer_data))
			{
				$data = $this->fetch_data_from_db($update_id);
			}else
			{
				$data['firstname'] = $optional_customer_data['firstname'];
				$data['lastname'] = $optional_customer_data['lastname'];
				$data['company'] = $optional_customer_data['company'];
			}

			if($data == "")
			{
				$customer_name = "Unknown";
			}else{
				$firstname = trim(ucfirst($data['firstname']));
				$lastname = trim(ucfirst($data['lastname']));
				$company = trim(ucfirst($data['company']));


				$company_length = strlen($company);
				if ($company_length > 2)
				{
					$customer_name = $company;
				}else
				{
					$customer_name = $firstname." ".$lastname;
				}

			}

			return $customer_name;

		}

		public function update_pword()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if(!is_numeric($update_id))
			{
				redirect(base_url('store_accounts/manage'));
			} elseif($submit == "Cancle")
			{
				redirect(base_url('store_accounts/create/'.$update_id));
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('pword','Password','required|min_length[7]|max_length[35]');
				$this->form_validation->set_rules('repeat_pword','Repeat Password','matches[pword]|required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$pword = $this->input->post('pword');
					$data['pword'] = $this->site_security->_hash_string($pword);

					
						//update the account pword
					$this->_update($update_id,$data);
					$flash_msg = "The account password were successfully updated.";
					$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('account',$value);

					
				}
			}

			$data['headline'] = 'Update Account Password';
			

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('account');
			//$data['view_module'] = "store_accounts";
			$data['view_file'] = "update_pword";

			$this->templates->admin($data);

		}

		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('store_accounts/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('username','Username','required');
				$this->form_validation->set_rules('firstname','First Name','required');
				$this->form_validation->set_rules('lastname','Last Name','required');
				$this->form_validation->set_rules('company','Company','required');

				

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();

					if(is_numeric($update_id))
					{
						//update the account details
						$this->_update($update_id,$data);
						$flash_msg = "The account details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('account',$value);

					} else{
						$data['date_made'] = time();

						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new account

						$flash_msg = "The account details were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('account',$value);

						redirect(base_url('store_accounts/create/'.$update_id));
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
				$data['headline'] = 'Add New Account';
			} else{
				$data['headline'] = 'Update Account';
			}

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('account');
			//$data['view_module'] = "store_accounts";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}

		public function autogen()
		{
			$mysql_query = 'show columns from store_accounts';
			$query = $this->_custom_query($mysql_query);
			/*
			foreach ($query -> result() as $row) 
			{
				$column_name = $row->Field;

				if($column_name!='id')
				{
					echo '$data[\''.$column_name.'\'] = $this->input->post(\''.$column_name.'\',TRUE);<br>';
				}
			}
			
			echo '<hr>';

			foreach ($query -> result() as $row) 
			{
				$column_name = $row->Field;

				if($column_name!='id')
				{
					echo '$data[\''.$column_name.'\'] = $row->'.$column_name.';<br>';
				}
			}
			echo '<hr>';

			foreach ($query -> result() as $row) 
			{
				$column_name = $row->Field;

				if($column_name!='id')
				{	//ucfirst chuyen ki tu dau tien thanh chu in hoa
					$var = 	'<div class="control-group">
							  <label class="control-label" for="typeahead">'.ucfirst($column_name). '</label>
							  <div class="controls">
								<input type="text" class="span6" name="'.$column_name.'" value="<?= $'.$column_name.' ?>" >
							  </div>
							</div>';
				 	echo htmlentities($var);
				 	echo '<br>';
				}
			}
*/

		}

		public function fetch_data_from_post() //lay data tu form
		{
			$data['username'] = $this->input->post('username',TRUE);
			$data['firstname'] = $this->input->post('firstname',TRUE);
			$data['lastname'] = $this->input->post('lastname',TRUE);
			$data['company'] = $this->input->post('company',TRUE);
			$data['address1'] = $this->input->post('address1',TRUE);
			$data['address2'] = $this->input->post('address2',TRUE);
			$data['town'] = $this->input->post('town',TRUE);
			$data['country'] = $this->input->post('country',TRUE);
			$data['postcode'] = $this->input->post('postcode',TRUE);
			$data['telnum'] = $this->input->post('telnum',TRUE);
			$data['email'] = $this->input->post('email',TRUE);


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
				$data['username'] = $row->username;
				$data['firstname'] = $row->firstname;
				$data['lastname'] = $row->lastname;
				$data['company'] = $row->company;
				$data['address1'] = $row->address1;
				$data['address2'] = $row->address2;
				$data['town'] = $row->town;
				$data['country'] = $row->country;
				$data['postcode'] = $row->postcode;
				$data['telnum'] = $row->telnum;
				$data['email'] = $row->email;
				$data['date_made'] = $row->date_made;
				$data['pword'] = $row->pword;
				$data['last_login'] = $row->last_login;


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

			$data['query'] = $this->get('lastname');
			$data['flash'] = $this->session->flashdata('account');

			//$data['view_module'] = "store_accounts";
			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}

		public function get_where($id)
		{
			if(!is_numeric($id))
			{
				die('Nom-numeric variable!');
			}

			$query = $this->mdl_store_accounts->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_accounts->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_accounts->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_accounts->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_accounts->get($order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_accounts->_custom_query($mysql_query);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_accounts->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_double_condition($col1,$value1,$col2,$value2)
		{
			$query = $this->mdl_store_accounts->get_with_double_condition($col1,$value1,$col2,$value2);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_accounts->get_with_limit($col,$value);
			return $query;
		}	

		public function get_max()
		{
			$query = $this->mdl_store_accounts->get_max();
			return $query;
		}				


	}
?>
<?php  
	class Enquiries extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('store_accounts');
			$this->load->model('mdl_enquiries');
			
		}

		public function submit_ranking()
		{
			$this->site_security->_make_sure_is_admin();
			$submit = $this->input->post('submit',TRUE);
			$data['ranking'] = $this->input->post('ranking',TRUE);

			if($submit == 'Cancle')
			{
				redirect(base_url('enquiries/inbox/'));
			}

			$update_id = $this->uri->segment(3);
			$this->_update($update_id,$data);

			$flash_msg = "The enquiry ranking were successfully updated.";
			$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
			$this->session->set_flashdata('item',$value);

			redirect(base_url('enquiries/view/'.$update_id));
		}

		public function fix()
		{
			$query = $this->get('id');
			foreach ($query->result() as $row) {
				$data['code'] = $this->site_security->generate_random_string(6);
				$this->_update($row->id,$data);
			}					
			echo "finish";
		}

		public function _attempt_get_data_from_code($customer_id,$code)
		{
			//make sure customer is allowed to view/respond and fetch data
			$query = $this->get_where_custom('code',$code);
			$num_rows = $query->num_rows();


			foreach ($query->result() as $row) {
				$data['subject'] = $row->subject;
				$data['message'] = $row->message;
				$data['sent_to'] = $row->sent_to;
				$data['date_created'] = $row->date_created;
				$data['opened'] = $row->opened;
				$data['sent_by'] = $row->sent_by;
				$data['urgent'] = $row->urgent;
			}

			//make sure code is good and customer is allowed 
			if(($num_rows<1) OR ($customer_id!=$data['sent_to']))
			{
				redirect(base_url('site_security/not_allowed'));
			}

			return $data;
		}

		public function _draw_customer_inbox($customer_id)
		{

			$folder_type = "inbox";
			$data['customer_id'] = $customer_id;
			$data['query'] = $this->_fetch_customer_enquiries($folder_type, $customer_id);
			$data['folder_type'] = ucfirst($folder_type);

			$data['flash'] = $this->session->flashdata('item');

			$is_mobile = $this->site_settings->is_mobile();
			$view_file = 'customer_inbox';
			if($is_mobile == TRUE)
			{
				$view_file .= '_jqm';
			}

			$this->load->view($view_file,$data);

		}


		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('enquiries/inbox'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('sent_to','Recipient','required');
				$this->form_validation->set_rules('subject','Subject','required|max_length[240]');
				$this->form_validation->set_rules('message','Message','required');


				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					
					$data['date_created'] = time();
					$data['sent_by'] = 0;
					$data['opened'] = 0;
					$data['code'] = $this->site_security->generate_random_string(6);
					
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new item

						$flash_msg = "The message were successfully created.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

						redirect(base_url('enquiries/inbox'));
					
				}
			}

			if((is_numeric($update_id)) && $submit != "Submit") //neu co id va chua submit -> lay du lieu tu databse
			{
				$data = $this->fetch_data_from_db($update_id);
				$data['message'] = "<br><br>
				---------------------------------------------------<br>
				The Original message is shown below:<br><br>".$data['message'];
			} else{
				$data = $this->fetch_data_from_post(); //nguoc lai thi them moi
			}

			if(!is_numeric($update_id)) 
			{
				$data['headline'] = 'Compose New Message';
			}else{
				$data['headline'] = 'Reply To Message';
			}

			$data['options'] = $this->_fetch_customers_as_options();

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}

		public function _fetch_customers_as_options()
		{
			$options[''] = "Select Customer...";
			//for dropdown menu
			$query = $this->store_accounts->get('lastname');
			foreach ($query->result() as $row) {
				$customer_name = $row->firstname." ".$row->lastname;

				$company_length = strlen($row->company);
				if($company_length>2)
				{
					$customer_name .= " from ".$row->company;
				}

				$customer_name = trim($customer_name);
				if($customer_name!="")
				{
					$options[$row->id] = $customer_name;
				}
			}
			return $options;
		}

		public function fetch_data_from_post() //lay data tu form
		{
			$data['subject'] = $this->input->post('subject',TRUE);
			$data['message'] = $this->input->post('message',TRUE);
			$data['sent_to'] = $this->input->post('sent_to',TRUE);

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
				$data['subject'] = $row->subject;
				$data['message'] = $row->message;
				$data['sent_to'] = $row->sent_to;
				$data['date_created'] = $row->date_created;
				$data['opened'] = $row->opened;
				$data['sent_by'] = $row->sent_by;
				$data['urgent'] = $row->urgent;

				if(!isset($data))
				{
					$data = "";
				}

				return $data;
			}
		}

		public function view()
		{	
			$this->site_security->_make_sure_is_admin();
			$update_id = $this->uri->segment(3);

			$this->_set_to_opened($update_id);

			//set options for ranking dropdown
			$options[''] = 'Select ...';
			$options['1'] = 'One Star';
			$options['2'] = 'Two Stars';
			$options['3'] = 'Three Stars';
			$options['4'] = 'Four Stars';
			$options['5'] = 'Five Stars';

			$data['options'] = $options;

			$data['update_id'] = $update_id;
			$data['headline'] = "Enquiry ID ".$update_id;

			$data['query'] = $this->get_where($update_id);

			$data['flash'] = $this->session->flashdata('item');

			$data['view_file'] = "view";

			$this->templates->admin($data);
		}

		public function _set_to_opened($update_id)
		{
			$data['opened'] = 1;
			$this->_update($update_id, $data);
		}

		public function inbox()
		{
			//$this->output->enable_profiler(TRUE);
			$this->site_security->_make_sure_is_admin();

			$folder_type = "inbox";
			$data['query'] = $this->_fetch_enquiries($folder_type);
			$data['folder_type'] = ucfirst($folder_type);


			$data['flash'] = $this->session->flashdata('item');

			$data['view_file'] = "view_enquiries";

			$this->templates->admin($data);
		}

		public function _fetch_enquiries($folder_type)
		{
			$mysql_query = "
				SELECT enquiries.*,
				store_accounts.firstname,
				store_accounts.lastname,
				store_accounts.company
				FROM enquiries LEFT JOIN store_accounts ON enquiries.sent_by = store_accounts.id
				WHERE enquiries.sent_to=0
				order by enquiries.date_created desc
			";
			$query = $this->_custom_query($mysql_query);
			return $query;
		}

		public function _fetch_customer_enquiries($folder_type, $customer_id)
		{
			$mysql_query = "
				SELECT enquiries.*,
				store_accounts.firstname,
				store_accounts.lastname,
				store_accounts.company
				FROM enquiries INNER JOIN store_accounts ON enquiries.sent_to = store_accounts.id
				WHERE enquiries.sent_to=$customer_id
				order by enquiries.date_created desc
			";
			$query = $this->_custom_query($mysql_query);
			return $query;
		}

		public function get_where($id)
		{
			$query = $this->mdl_enquiries->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_enquiries->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_enquiries->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_enquiries->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_enquiries->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_enquiries->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($limit,$offset,$order_by)
		{
			$query = $this->mdl_enquiries->get_with_limit($limit,$offset,$order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_enquiries->_custom_query($mysql_query);
			return $query;
		}

		public function get_with_double_condition($col1,$value1,$col2,$value2)
		{
			$query = $this->mdl_enquiries->get_with_double_condition($col1,$value1,$col2,$value2);
			return $query;
		}


	}
?>
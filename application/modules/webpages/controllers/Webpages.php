<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Webpages extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('store_item_colours');
			$this->load->module('store_item_sizes');


			$this->load->model('mdl_webpages');
			

			$this->form_validation->CI =& $this;
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
				redirect(base_url('webpages/create/'.$update_id));
			} elseif($submit == 'Yes - Delete Page')
			{
				$this->_delete($update_id);

				$flash_msg = "The page was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('webpages/manage'));

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			} elseif($update_id<3) //prevent from delete home and contact us page
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Page';
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "deleteconf";

			$this->templates->admin($data);
		}

		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('webpages/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('page_title','Page Title','required|max_length[240]');
			
				$this->form_validation->set_rules('page_content','Page Content','required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					$data['page_url'] = url_title($data['page_title']);

					if(is_numeric($update_id))
					{	
						if($update_id<3)
						{
							unset($data['page_url']);
						}
						//update the item details
						$this->_update($update_id,$data);
						$flash_msg = "The page details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

					} else{
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new item

						$flash_msg = "The page were successfully created.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

						redirect(base_url('webpages/create/'.$update_id));
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
				$data['headline'] = 'Create New Page';
			} else{
				$data['headline'] = 'Update Page Details';
			}


			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}

		public function fetch_data_from_post() //lay data tu form
		{
			$data['page_url'] = $this->input->post('page_url',TRUE);
			$data['page_title'] = $this->input->post('page_title',TRUE);
			$data['page_keywords'] = $this->input->post('page_keywords',TRUE);
			$data['page_description'] = $this->input->post('page_description',TRUE);
			$data['page_content'] = $this->input->post('page_content',TRUE);

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
				$data['page_url'] = $row->page_url;
				$data['page_title'] = $row->page_title;
				$data['page_keywords'] = $row->page_keywords;
				$data['page_content'] = $row->page_content;
				$data['page_description'] = $row->page_description;

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

			$data['query'] = $this->get('page_url');
			$data['flash'] = $this->session->flashdata('item');

			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}

		public function get_where($id)
		{
			$query = $this->mdl_webpages->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_webpages->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_webpages->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_webpages->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_webpages->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_webpages->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_webpages->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_webpages->_custom_query($mysql_query);
			return $query;
		}

		



	}
?>
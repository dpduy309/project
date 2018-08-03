<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Btm_nav extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('site_pagination');
			$this->load->module('webpages');

		
			$this->load->model('mdl_btm_nav');
			

			$this->form_validation->CI =& $this;
		}

		public function _draw_btm_nav()
		{
			
			$mysql_query = "
			SELECT 
				btm_nav.id,
				btm_nav.page_id,
				btm_nav.priority,
				webpages.page_url,
				webpages.page_title
			FROM
				btm_nav
			INNER JOIN webpages ON btm_nav.page_id = webpages.id
			ORDER BY btm_nav.priority
			";
			$data['query'] = $this->_custom_query($mysql_query);
			$this->load->view('btm_nav',$data);
		}

		public function get_special_pages()
		{
			$special_pages[] = 2; //contact us
			$special_pages[] = 6;//refund policy
			return $special_pages;
		}

		public function delete($update_id)
		{
			$this->site_security->_make_sure_is_admin();

			if(!is_numeric($update_id))
			{
				die();
			}

			$this->_delete($update_id);
			redirect(base_url('btm_nav/manage'));
		}

		public function submit_create()
		{
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$page_id = $this->input->post('page_id',TRUE);

			if($submit=="Cancle")
			{
				redirect(base_url('btm_nav/manage'));
			}elseif ($submit=="Submit") {
				$data['page_id'] = $page_id;
				$data['priority'] = 0;
				$this->_insert($data);
				redirect(base_url('btm_nav/manage'));
			}
		}

		public function _draw_create_modal()
		{
			//modal for creating new record
			$query = $this->get('priority');
			foreach ($query->result() as $row) {
				$selected_options[$row->page_id] = $row->page_id;
			}

			$data['options'] = $this->_get_dropdown_options($selected_options);
			$data['form_location'] = base_url('btm_nav/submit_create');
			$this->load->view('create_modal',$data);
		}

		public function _get_dropdown_options($selected_options)
		{
			//get all the webpages
			$options[''] = 'Please Select ...';

			$query = $this->webpages->get('page_url');
			foreach ($query->result() as $row) {
				if($row->page_url=='')
				{
					$row->page_url = 'Home';
				}

				if(!in_array($row->id,$selected_options))
				{
					$options[$row->id] = $row->page_url;
				}
			}

			if(!isset($options))
			{
				$options = '';
			}

			return $options;
		}


		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			} 
			
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Entire Offer Block';
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "deleteconf";

			$this->templates->admin($data);
		}

		public function _draw_sortable_list()
		{
			$mysql_query = "
			SELECT 
				btm_nav.id,
				btm_nav.page_id,
				btm_nav.priority,
				webpages.page_url,
				webpages.page_title
			FROM
				btm_nav
			INNER JOIN webpages ON btm_nav.page_id = webpages.id
			ORDER BY btm_nav.priority
			";
			$data['query'] = $this->_custom_query($mysql_query);
			$data['special_pages'] = $this->get_special_pages();
			$this->load->view('sortable_list',$data);
		}

		public function _draw_blocks()
		{
			$data['query'] = $this->get('priority');
			$num_rows = $data['query']->num_rows();
			if($num_rows>0)
			{
				$this->load->view('btm_nav', $data);
			} 
		}


		public function view($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();
			
			//fetch the item details
			$data = $this->fetch_data_from_db($update_id);

			//count the item that belong to the hompage offer
			$use_limit = FALSE;
			$mysql_query = $this->_generate_mysql_query($update_id,$use_limit);
			$query = $this->_custom_query($mysql_query);
			$total_items = $query->num_rows();


			//fetch the item for this page
			$use_limit = TRUE;
			$mysql_query = $this->_generate_mysql_query($update_id,$use_limit);

			$pagination_data['template'] = 'public_bootstrap';
			$pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
			$pagination_data['total_rows'] = $total_items;
			$pagination_data['offset_segment'] = 4;
			$pagination_data['limit'] = $this->get_limit();
			$pagination_data['offset'] = $this->get_offset();



			$data['site_pagination'] = $this->site_pagination->_generate_pagination($pagination_data);
			$data['showing_statement'] = $this->site_pagination->get_showing_statement($pagination_data);
			$data['query'] = $this->_custom_query($mysql_query);
			$data['item_segments'] = "fashional/choice/";
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_module'] = "btm_nav";
			$data['view_file'] = "view";

			$this->templates->public_bootstrap($data);	
		}

		public function get_target_pagination_base_url()
		{
			$first_bit = $this->uri->segment(1);
			$second_bit = $this->uri->segment(2);
			$third_bit = $this->uri->segment(3);
			$target_base_url = base_url().$first_bit."/".$second_bit."/".$third_bit;
			return $target_base_url;

		}

		public function _generate_mysql_query($update_id,$use_limit)
		{
			$mysql_query = "Select store_items.item_title, store_items.item_url, store_items.item_price, store_items.small_pic, store_items.was_price from store_cat_assign INNER JOIN store_items ON store_cat_assign.item_id = store_items.id WHERE store_cat_assign.cat_id = $update_id and store_items.status=1";

			if($use_limit == TRUE)
			{
				$limit = $this->get_limit();
				$offset = $this->get_offset();
				$mysql_query.=" limit ".$offset.", ".$limit;
			}
			return $mysql_query;
		}

		public function get_limit()
		{
			$limit = 6;
			return $limit;
		}

		public function get_offset()
		{
			$offset = $this->uri->segment(4);

			if(!isset($offset))
			{
				$offset = 0;
			}

			return $offset;
		}		


	

		public function sort()
		{

			$this->site_security->_make_sure_is_admin();

			$number = $this->input->post('number',TRUE);

			for ($i=1; $i <= $number; $i++) { 
				$update_id = $_POST['order'.$i];
				$data['priority'] = $i;
				$this->_update($update_id,$data);
			}

		}

	

		public function _get_block_title($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$block_title = $data['block_title'];
			return $block_title;
		}


		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('btm_nav/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('block_title','Hompage offer Title','required');
			

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					//$data['cat_url'] = url_title($data['block_title']);


					if(is_numeric($update_id))
					{
						//update the Categorie details
						$this->_update($update_id,$data);
						$flash_msg = "The hompage offer details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

					} else{
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new Categorie

						$flash_msg = "The hompage offer details were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

						redirect(base_url('btm_nav/create/'.$update_id));
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
				$data['headline'] = 'Create New Navigation Link';
			} else{
				$block_title = $this->_get_block_title($update_id);
				$data['headline'] = 'Update '.$block_title;
			}

			//$data['options'] = $this->_get_dropdown_options($update_id);
			//$data['num_dropdown_options'] = count($data['options']);

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('categories');
			//$data['view_module'] = "store_Bottom Navigation";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}
		
		public function fetch_data_from_post() //lay data tu form
		{
			$data['block_title'] = $this->input->post('block_title',TRUE);
		

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
				$data['block_title'] = $row->block_title;

				

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
			
			$data['sort_this'] = TRUE;
			$data['flash'] = $this->session->flashdata('Categorie');

			//$data['view_module'] = "store_Bottom Navigation";
			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}


		public function get_where($id)
		{
			$query = $this->mdl_btm_nav->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_btm_nav->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_btm_nav->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_btm_nav->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_btm_nav->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_btm_nav->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_btm_nav->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_btm_nav->_custom_query($mysql_query);
			return $query;
		}


	}
?>
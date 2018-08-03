<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Sliders extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('site_pagination');
			$this->load->module('slides');

			$this->load->model('mdl_sliders');
			

			$this->form_validation->CI =& $this;
		}

		public function _attempt_draw_slider()
		{
			$current_url = current_url();
			$query_ads = $this->get_where_custom('target_url',$current_url);
			$num_rows_ads = $query_ads->num_rows();
			if($num_rows_ads>0)
			{
				//draw
				foreach ($query_ads->result() as $row) {
					$parent_id = $row->id;
				}

				$data['query_slides'] = $this->slides->get_where_custom('parent_id',$parent_id);
				$this->load->view('slider',$data);
			}

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
				redirect(base_url('sliders/create/'.$update_id));
			} elseif($submit == 'Yes - Delete Slider')
			{
				//delete any slides that the associated with this slider
				$query = $this->slides->get_where_custom('parent_id',$update_id);
				foreach ($query->result() as $row) {
					$this->slides->_process_delete($row->id);
				}

				//delete the page
				$this->_delete($update_id);

				$flash_msg = "The slider was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('sliders/manage'));

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			} 
			
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Entire Slider';
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "deleteconf";

			$this->templates->admin($data);
		}

		public function _draw_blocks()
		{
			$data['query'] = $this->get('target_url');
			$num_rows = $data['query']->num_rows();
			if($num_rows>0)
			{
				$this->load->view('sliders', $data);
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

			//count the item that belong to the slider
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
			$data['view_module'] = "sliders";
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
				$data['target_url'] = $i;
				$this->_update($update_id,$data);
			}

		}

		public function _get_title($update_id)
		{
			$title = $this->_get_slider_title($update_id);
			return $title;
		}

		public function _get_slider_title($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$slider_title = $data['slider_title'];
			return $slider_title;
		}


		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('sliders/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('slider_title','Slider Title','required');
				$this->form_validation->set_rules('target_url','Target URL','required');

				if($this->form_validation->run() == TRUE)
				{
					$data = $this->fetch_data_from_post();

					if(is_numeric($update_id))
					{
						//update the Categorie details
						$this->_update($update_id,$data);
						$flash_msg = "The slider details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

					} else{
						$this->_insert($data);

						$flash_msg = "The slider details were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

						redirect(base_url('sliders/create/'.$update_id));
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
				$data['headline'] = 'Add New Slider';
			} else{
				$slider_title = $this->_get_slider_title($update_id);
				$data['headline'] = 'Update '.$slider_title;
			}


			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('categories');
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}
		
		public function fetch_data_from_post() //lay data tu form
		{
			$data['slider_title'] = $this->input->post('slider_title',TRUE);
			$data['target_url'] = $this->input->post('target_url',TRUE);

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
				$data['slider_title'] = $row->slider_title;
				$data['target_url'] = $row->target_url;

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
			
			$data['query'] = $this->get('slider_title');
			$data['num_rows'] = $data['query']->num_rows();
			$data['sort_this'] = TRUE;
			$data['flash'] = $this->session->flashdata('Categorie');

			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}


		public function get_where($id)
		{
			$query = $this->mdl_sliders->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_sliders->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_sliders->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_sliders->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_sliders->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_sliders->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_sliders->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_sliders->_custom_query($mysql_query);
			return $query;
		}

	}
?>
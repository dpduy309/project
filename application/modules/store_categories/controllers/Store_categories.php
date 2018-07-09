<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Store_categories extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('site_pagination');

		
			$this->load->model('mdl_store_categories');
			

			$this->form_validation->CI =& $this;
		}

		public function _get_full_cat_url($update_id)
		{
			$item_segments = "fashion/choices/";
			$data = $this->fetch_data_from_db($update_id);
			$cat_url = $data['cat_url'];
			$full_cat_url = base_url().$item_segments.$cat_url;
			return $full_cat_url;
		}

		public function test()
		{
			$users['bin'] = 72;
			$users['jenny'] = 56;
			$users['susu'] = 50;
			$users['liem'] = 58;

			echo '<h1> Who is the oldest?</h1>';
			$the_oldest = $this->the_oldest($users);
			echo $the_oldest;
		}

		public function the_oldest($target_array)
		{

			foreach ($target_array as $key => $value) {
				if(!isset($the_highest_value))
				{
					$the_highest_value = $key;
				}elseif($value> $target_array[$the_highest_value])
				{
					$the_highest_value = $key;
				}
			}
			return $the_highest_value;
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

			//count the item that belong to the category
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
			$data['view_module'] = "store_categories";
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

		public function _get_cat_id_from_cat_url($cat_url)
		{
			$query = $this->get_where_custom('cat_url',$cat_url);
			foreach ($query->result() as $row) {
				$cat_id = $row->id;
			}

			if(!isset($cat_id))
			{
				$cat_id = 0;
			}
			return $cat_id;
		}

		public function _draw_top_nav()
		{
			$mysql_query = "select * from store_categories where parent_cat_id=0 order by priority";
			$query = $this->_custom_query($mysql_query);
			foreach ($query->result() as $row) {
				$parent_categories[$row->id] = $row->cat_title; 
			}

			$item_segments = "fashion/choices/";

			$data['target_url_start'] = base_url().$item_segments;

			$data['parent_categories'] = $parent_categories;
			$this->load->view('top_nav',$data);
		}

		public function _get_parent_cat_title($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$parent_cat_id = $data['parent_cat_id'];
			$parent_cat_title = $this->_get_cat_title($parent_cat_id);
			return $parent_cat_title;
		}

		public function _get_all_sub_cats_for_dropdown()
		{
			//this get used on store_cat_assign
			$mysql_query = "select * from store_categories where parent_cat_id!=0 order by parent_cat_id, cat_title";
			$query = $this->_custom_query($mysql_query);
			foreach ($query->result() as $row) {
				$parent_cat_title = $this->_get_cat_title($row->parent_cat_id);	
				$sub_categories[$row->id] = $parent_cat_title." > ".$row->cat_title;
			}

			if(!isset($sub_categories))
			{
				$sub_categories= "";
			}

			return $sub_categories;
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


			// $info = "The following was posted ";

			// foreach ($_POST as $key => $value) {
			// 	$info .= "key of $key with value of $value";
			// }

			// $data['posted_info'] = $info;
			// $update_id = 1;
			// $this->_update($update_id,$data);
		}

		public function _draw_sortable_list($parent_cat_id)
		{
			$mysql_query = "select * from store_categories where parent_cat_id = $parent_cat_id order by priority";
			$data['query'] = $this->_custom_query($mysql_query);
			$this->load->view('sortable_list',$data);
		}

		public function _count_sub_cats($update_id)
		{
			//return the number of sub categories, belonging to this category
			$query = $this->get_where_custom('parent_cat_id',$update_id);
			$num_rows = $query->num_rows();
			return $num_rows;
		}

		public function _get_cat_title($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$cat_title = $data['cat_title'];
			return $cat_title;
		}

		public function _get_dropdown_options($update_id)
		{
			if(!is_numeric($update_id))
			{
				$update_id = 0;
			}

			$options[''] = 'Please Select ...';
			//build an array of all the parent categories
			$mysql_query = "select * from store_categories where parent_cat_id=0 and id!=$update_id";
			$query = $this->_custom_query($mysql_query);

			foreach ($query->result() as $row) {
				$options[$row->id] = $row->cat_title;
			}
			return $options;

		}

		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('store_categories/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('cat_title','Category Title','required');
			

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					$data['cat_url'] = url_title($data['cat_title']);


					if(is_numeric($update_id))
					{
						//update the Categorie details
						$this->_update($update_id,$data);
						$flash_msg = "The category details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

					} else{
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new Categorie

						$flash_msg = "The category details were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('categories',$value);

						redirect(base_url('store_categories/create/'.$update_id));
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
				$data['headline'] = 'Add New Category';
			} else{
				$data['headline'] = 'Update Category';
			}

			$data['options'] = $this->_get_dropdown_options($update_id);
			$data['num_dropdown_options'] = count($data['options']);

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('categories');
			//$data['view_module'] = "store_Categories";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}
		
		public function fetch_data_from_post() //lay data tu form
		{
			$data['cat_title'] = $this->input->post('cat_title',TRUE);
			$data['parent_cat_id'] = $this->input->post('parent_cat_id',TRUE);
		

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
				$data['cat_title'] = $row->cat_title;
				$data['cat_url'] = $row->cat_url;
				$data['parent_cat_id'] = $row->parent_cat_id;

				

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
			
			//an cac cat_title con. Muon xem thi nhap id tuong ung
			$parent_cat_id = $this->uri->segment(3);
			if(!is_numeric($parent_cat_id))
			{
				$parent_cat_id = 0;
			}

			$data['sort_this'] = TRUE;
			$data['parent_cat_id'] = $parent_cat_id;
			$data['query'] = $this->get_where_custom('parent_cat_id',$parent_cat_id);
			$data['flash'] = $this->session->flashdata('Categorie');

			//$data['view_module'] = "store_Categories";
			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}


		public function get_where($id)
		{
			$query = $this->mdl_store_categories->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_categories->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_categories->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_categories->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_categories->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_categories->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_categories->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_categories->_custom_query($mysql_query);
			return $query;
		}

		// public function Categorie_check($str)
  //       {
  //       	$Categorie_url = url_title($str);
  //       	$mysql_query = "SELECT * FROM store_Categories where cat_title ='$str' AND Categorie_url = '$Categorie_url'"; //de bi hack

  //       	$update_id = $this->uri->segment(3);
  //       	if(is_numeric($update_id)){
  //       		//this is an update
  //       		$mysql_query.= " AND id!=$update_id";
  //       	}

  //       	$query = $this->_custom_query($mysql_query);
  //       	$num_rows = $query->num_rows();


  //               if ($num_rows > 0)
  //               {
  //                       $this->form_validation->set_message('Categorie_check', 'The Categorie title that you submitted is not available');
  //                       return FALSE;
  //               }
  //               else
  //               {
  //                       return TRUE;
  //               }
  //       }




	}
?>
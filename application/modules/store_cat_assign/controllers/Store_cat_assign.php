<?php  
	class Store_cat_assign extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('store_categories');

			$this->load->model('mdl_store_cat_assign');
			

			$this->form_validation->CI =& $this;
		}

		public function delete($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			//fetch the item_id
			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$item_id = $row->item_id;
			}
			$this->_delete($update_id);

			$flash_msg = "The option was successfully deleted.";
			$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
			$this->session->set_flashdata('item',$value);

			redirect(base_url('store_cat_assign/update/'.$item_id));
		}

		public function submit($item_id)
		{

			if(!is_numeric($item_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$cat_id = trim($this->input->post('cat_id',TRUE)); //tranh nguoi dung nhap khoang trang bay ba 

			if($submit == "Finished")
			{
				redirect(base_url('store_items/create/'.$item_id));
			} elseif($submit == "Submit"){
				//insert
				if($cat_id!="")
				{
					$data['item_id'] = $item_id;
					$data['cat_id'] = $cat_id;
					$this->_insert($data);

					$cat_title = $this->store_categories->_get_cat_title($cat_id);

					$flash_msg = "The item was successfully assigned to the ".$cat_title." category.";
					$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);
				}
			}
			redirect(base_url('store_cat_assign/update/'.$item_id));
		}

		public function update($item_id)
		{
			if(!is_numeric($item_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			//get an array of all sub categories on the site
			$sub_categories = $this->store_categories->_get_all_sub_cats_for_dropdown();

			//get an array of all assigned categories
			$query = $this->get_where_custom('item_id',$item_id);
			$data['num_rows'] = $query->num_rows();
			$data['query'] = $query;
			foreach($query->result() as $row)
			{
				$cat_title = $this->store_categories->_get_cat_title($row->cat_id);
				$parent_cat_title = $this->store_categories->_get_parent_cat_title($row->cat_id);
				$assigned_categories[$row->cat_id] = $parent_cat_title." > ".$cat_title;
			}

			if(!isset($assigned_categories))
			{
				$assigned_categories = "";
			} else{
				//the item has been assigned to at least one category
				$sub_categories = array_diff($sub_categories,$assigned_categories);	
			}

			$data['options'] = $sub_categories;
			$data['cat_id'] = $this->input->post('cat_id',TRUE);

		

			$data['headline'] = "Category Assign";
			$data['item_id'] = $item_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "update";

			$this->templates->admin($data);
		}

		public function get_where($id)
		{
			$query = $this->mdl_store_cat_assign->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_cat_assign->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_cat_assign->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_cat_assign->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_cat_assign->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_cat_assign->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_cat_assign->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_cat_assign->_custom_query($mysql_query);
			return $query;
		}




	}
?>
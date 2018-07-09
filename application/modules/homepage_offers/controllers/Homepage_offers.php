<?php  
	class Homepage_offers extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');


			$this->load->model('mdl_homepage_offers');

			$this->form_validation->CI =& $this;
		}

		public function _draw_offers($block_id,$theme){
			$query = $this->get_where_custom('block_id', $block_id);
$mysql_query = "SELECT store_items.* FROM homepage_offers INNER JOIN homepage_blocks ON homepage_offers.block_id = homepage_blocks.id INNER JOIN store_items ON homepage_offers.item_id = store_items.id WHERE Homepage_offers.block_id = $block_id";
			
			$query = $this->_custom_query($mysql_query);


			$num_rows = $query->num_rows();
			if($num_rows>0)
			{
				$data['query'] = $query;
				$data['theme'] = $theme;
				$this->load->view('offers', $data);
			}
		}

		public function _delete_for_item($block_id)
		{
			$mysql_query = "DELETE FROM homepage_offers WHERE block_id = $block_id";
			$query = $this->_custom_query($mysql_query);
		}

		public function delete($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			//fetch the block_id
			$query = $this->get_where($update_id);
			foreach ($query->result() as $row) {
				$block_id = $row->block_id;
			}
			$this->_delete($update_id);

			$flash_msg = "The option was successfully deleted.";
			$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
			$this->session->set_flashdata('item',$value);

			redirect(base_url('homepage_offers/update/'.$block_id));
		}

		public function _is_valid_item($item_id)
		{
			//is this a valid item ID
			if(!is_numeric($item_id))
			{
				return FALSE;
			}

			$this->load->module('store_items');
			$query = $this->store_items->get_where($item_id);
			$num_rows = $query->num_rows();
			if($num_rows>0)
			{
				return TRUE;
			} else{
				return FALSE;
			}
		}

		public function submit($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$item_id = trim($this->input->post('item_id',TRUE)); //tranh nguoi dung nhap khoang trang bay ba 

			if($submit == "Finished")
			{
				redirect(base_url('homepage_blocks/manage/'));
			} elseif($submit == "Submit"){

				$is_valid_item = $this->_is_valid_item($item_id);
				if($is_valid_item == FALSE)
				{
					$flash_msg = "The item ID that you submitted was not valid.";
					$value = '<div class="alert alert-danger" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);	
					redirect(base_url('homepage_offers/update/'.$update_id));

				}

				//insert
				if($item_id!="")
				{
					$data['block_id'] = $update_id;
					$data['item_id'] = $item_id;
					$this->_insert($data);

					$flash_msg = "The new offer was successfully updated.";
					$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);
				}
			}
			redirect(base_url('homepage_offers/update/'.$update_id));
		}

		public function update($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			//fetch existing options
			$data['query'] = $this->get_where_custom('block_id', $update_id);
			$data['num_rows'] = $data['query']->num_rows();

			$data['headline'] = "Upload Item Offer";
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "update";

			$this->templates->admin($data);

		}



		public function get_where($id)
		{
			$query = $this->mdl_homepage_offers->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_homepage_offers->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_homepage_offers->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_homepage_offers->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_homepage_offers->get($order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_homepage_offers->_custom_query($mysql_query);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_homepage_offers->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_homepage_offers->get_with_limit($col,$value);
			return $query;
		}		
		public function count_where($col,$value)
		{
			$query = $this->mdl_homepage_offers->count_where($col,$value);
			return $query;
		}	


	}
?>
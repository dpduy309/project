<?php  
	class Store_item_colours extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');


			$this->load->model('mdl_store_item_colours');

			$this->form_validation->CI =& $this;
		}

		public function _delete_for_item($item_id)
		{
			$mysql_query = "DELETE FROM store_item_colours WHERE item_id = $item_id";
			$query = $this->_custom_query($mysql_query);
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

			redirect(base_url('store_item_colours/update/'.$item_id));
		}

		public function submit($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$colour = trim($this->input->post('colour',TRUE)); //tranh nguoi dung nhap khoang trang bay ba 

			if($submit == "Finished")
			{
				redirect(base_url('store_items/create/'.$update_id));
			} elseif($submit == "Submit"){
				//insert
				if($colour!="")
				{
					$data['item_id'] = $update_id;
					$data['colour'] = $colour;
					$this->_insert($data);

					$flash_msg = "The new colour option was successfully updated.";
					$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
					$this->session->set_flashdata('item',$value);
				}
			}
			redirect(base_url('store_item_colours/update/'.$update_id));
		}

		public function update($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			//fetch existing options
			$data['query'] = $this->get_where_custom('item_id', $update_id);
			$data['num_rows'] = $data['query']->num_rows();

			$data['headline'] = "Upload Item Colour";
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "update";

			$this->templates->admin($data);

		}



		public function get_where($id)
		{
			$query = $this->mdl_store_item_colours->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_item_colours->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_item_colours->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_item_colours->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_item_colours->get($order_by);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_item_colours->_custom_query($mysql_query);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_item_colours->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_item_colours->get_with_limit($col,$value);
			return $query;
		}			


	}
?>
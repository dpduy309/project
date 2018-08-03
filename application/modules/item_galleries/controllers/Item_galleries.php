<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Item_galleries extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('store_items');
			$this->load->model('mdl_item_galleries');
			

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Slide';
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
				redirect(base_url('item_galleries/view/'.$update_id));
			} elseif($submit == 'Yes - Delete')
			{
				$parent_id = $this->_get_parent_id($update_id);
				$this->_process_delete($update_id);

				$flash_msg = "The picture was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('item_galleries/update_group/'.$parent_id));

		}

		public function _process_delete($update_id)
		{

			$data = $this->fetch_data_from_db($update_id);
			$picture = $data['picture'];

			$picture_path = './item_galleries_pics/'.$picture;

			//remove the image
			if(file_exists($picture_path))//duong dan ton tai
			{
				unlink($picture_path);	//thi xoa file theo duong dan
			}

			//delete item records form store_items
			$this->_delete($update_id);
		}

		public function _get_parent_title($parent_id)
		{
			$parent_title = $this->store_items->_get_title($parent_id);
			return $parent_title;
		}

		public function _get_entity_name($type)
		{
			if($type == 'singular')
			{
				$entity_name = 'picture';
			}else{
				//plural
				$entity_name = 'pictures';
			}

			return $entity_name;
		}		

		public function get_update_group_headline($parent_id)
		{
			$parent_title = ucfirst($this->_get_parent_title($parent_id));
			$entity_name = ucfirst($this->_get_entity_name('plural'));
			$headline = 'Update '.$entity_name.' For '.$parent_title;
			return $headline;
		}

		public function update_group($parent_id)
		{
			//update/manage records belonging to a parent
			$this->site_security->_make_sure_is_admin();
			
			$data['parent_id'] = $parent_id;
			$data['query'] = $this->get_where_custom('parent_id',$parent_id);
			$data['num_rows'] = $data['query']->num_rows();
			$data['headline'] = 'Manage Item Galleries';
			$data['entity_name'] = $this->_get_entity_name('plural');
			$data['parent_title'] = $this->_get_parent_title($parent_id);
			$data['flash'] = $this->session->flashdata('item');
			$data['sub_headline'] = $this->get_update_group_headline($parent_id);

			$data['view_file'] = "update_group";

			$this->templates->admin($data);
		}


		public function submit_create()
		{
			//form has been submitted, try to create new record
			$this->site_security->_make_sure_is_admin();

			$data['parent_id'] = $this->input->post('parent_id', TRUE);
			$this->_insert($data);

			$max_id = $this->get_max();
			foreach ($max_id->result() as $row) {
				$max_id = $row->id;
			}
			redirect(base_url('item_galleries/view/'.$max_id));
		}	

		public function _get_parent_id($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$parent_id = $data['parent_id'];
			return $parent_id;
		}

		public function fetch_data_from_post() 
		{
			$data['parent_id'] = $this->input->post('parent_id',TRUE);

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
				$data['picture'] = $row->picture;
				$data['parent_id'] = $row->parent_id;

				if(!isset($data))
				{
					$data = "";
				}

				return $data;
			}
		}	


		public function _draw_img_btn($update_id)
		{
			//draw an upload img btn
			$data = $this->fetch_data_from_db($update_id);

			$picture = $data['picture'];
			if($picture == '')
			{
				$data['got_pic'] = FALSE;
				$data['btn_style'] = '';
				$data['btn_info'] = 'No picture has been uploaded';
			}else{
				$data['got_pic'] = TRUE;
				$data['btn_style'] = 'style="clear:both; margin-top:24px;"';
				$data['btn_info'] = 'The picture that is being use is shown below';
				$data['pic_path'] = base_url('item_galleries_pics/'.$picture);
			}

			$this->load->view('img_btn',$data);
		}

		public function upload_image($parent_id)
		{

			if(!is_numeric($parent_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = "Upload Image";
			$data['parent_id'] = $parent_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "upload_image";

			$this->templates->admin($data);

		}

		public function do_upload($parent_id)
		{
			if(!is_numeric($parent_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();
				
			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancel")
			{
				redirect(base_url('item_galleries/update_group/'.$parent_id));				
			}

			

			$config['upload_path']          = './item_galleries_pics/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['max_width']            = 2048;
            $config['max_height']           = 2048;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile'))
            {
                    $data['error'] = array('error' => $this->upload->display_errors('<p style="color: red">','</p>'));

                    $data['headline'] = "Upload Error";
					$data['parent_id'] = $parent_id;
					$data['flash'] = $this->session->flashdata('item');
					//$data['view_module'] = "store_items";
					$data['view_file'] = "upload_image";

					$this->templates->admin($data);
                   
            }
            else
            {
            	//upload was successful
                    $data = array('upload_data' => $this->upload->data());

                    $upload_data = $data['upload_data'];
                    $file_name = $upload_data['file_name'];

                    //insert the database
                    unset($data);
                    $data['parent_id'] = $parent_id;
                    $data['picture'] = $file_name;
                    $this->_insert($data);

					redirect(base_url('item_galleries/update_group/'.$parent_id));				
            		
            }
		}

		public function submit($update_id)
		{
			//update the record that has been submitted via view
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancel")
			{
				$parent_id = $this->_get_parent_id($update_id);
				redirect(base_url('item_galleries/update_group/'.$update_id));				
			}elseif($submit == "Submit"){
				$this->_update($update_id,$data);
				redirect(base_url('item_galleries/view/'.$update_id));				
			}
		}


		public function get_where($id)
		{
			$query = $this->mdl_item_galleries->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_item_galleries->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_item_galleries->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_item_galleries->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_item_galleries->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_item_galleries->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_item_galleries->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_item_galleries->_custom_query($mysql_query);
			return $query;
		}

		public function get_max()
		{
			$query = $this->mdl_item_galleries->get_max();
			return $query;
		}		
	}
?>
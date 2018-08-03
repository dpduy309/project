<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Slides extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('sliders');
			$this->load->model('mdl_slides');
			

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
				redirect(base_url('slides/view/'.$update_id));
			} elseif($submit == 'Yes - Delete')
			{
				$parent_id = $this->_get_parent_id($update_id);
				$this->_process_delete($update_id);

				$flash_msg = "The slide was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('slides/update_group/'.$parent_id));

		}

		public function _process_delete($update_id)
		{

			$data = $this->fetch_data_from_db($update_id);
			$picture = $data['picture'];

			$picture_path = './slider_pics/'.$picture;

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
			$parent_title = $this->sliders->_get_title($parent_id);
			return $parent_title;
		}

		public function _get_entity_name($type)
		{
			if($type == 'singular')
			{
				$entity_name = 'slide';
			}else{
				//plural
				$entity_name = 'slides';
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
			$data['entity_name'] = $this->_get_entity_name('plural');
			$data['parent_title'] = $this->_get_parent_title($parent_id);
			$data['flash'] = $this->session->flashdata('item');
			$data['headline'] = $this->get_update_group_headline($parent_id);

			$data['view_file'] = "update_group";

			$this->templates->admin($data);
		}

		public function _draw_create_modal($parent_id)
		{
			//modal for creating new record
			$data['parent_id'] = $parent_id;
			$data['form_location'] = base_url('slides/submit_create');
			$this->load->view('create_modal',$data);
		}

		public function submit_create()
		{
			//form has been submitted, try to create new record
			$this->site_security->_make_sure_is_admin();

			$data['parent_id'] = $this->input->post('parent_id', TRUE);
			$data['target_url'] = $this->input->post('target_url', TRUE);
			$data['alt_text'] = $this->input->post('alt_text', TRUE);
			$this->_insert($data);

			$max_id = $this->get_max();
			foreach ($max_id->result() as $row) {
				$max_id = $row->id;
			}
			redirect(base_url('slides/view/'.$max_id));
		}	

		public function _get_parent_id($update_id)
		{
			$data = $this->fetch_data_from_db($update_id);
			$parent_id = $data['parent_id'];
			return $parent_id;
		}

		public function fetch_data_from_post() 
		{
			$data['target_url'] = $this->input->post('target_url',TRUE);
			$data['alt_text'] = $this->input->post('alt_text',TRUE);
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
				$data['target_url'] = $row->target_url;
				$data['alt_text'] = $row->alt_text;
				$data['picture'] = $row->picture;
				$data['parent_id'] = $row->parent_id;

				if(!isset($data))
				{
					$data = "";
				}

				return $data;
			}
		}	

		public function view($update_id)
		{
			//view details regarding this record and displayform

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('slides/update_group/'.$update_id));				
			}

			

			if($submit != "Submit")
			{
				$data = $this->fetch_data_from_db($update_id);
			} else{
				$data = $this->fetch_data_from_post();
				$data['picture'] = "";
			}

			$entity_name = ucfirst($this->_get_entity_name('singular'));
			$data['headline'] = 'Update '.$entity_name;
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "view";

			$this->templates->admin($data);

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
				$data['btn_info'] = 'The picture that is being use for this slide is shown below';
				$data['pic_path'] = base_url('slider_pics/'.$picture);
			}

			$this->load->view('img_btn',$data);
		}

		public function upload_image($update_id)
		{

			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = "Upload Image";
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "upload_image";

			$this->templates->admin($data);

		}

		public function do_upload($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();
				
			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancel")
			{
				$parent_id = $this->_get_parent_id($update_id);
				redirect(base_url('slides/update_group/'.$parent_id));				
			}

			

			$config['upload_path']          = './slider_pics/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['max_width']            = 2048;
            $config['max_height']           = 2048;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile'))
            {
                    $data['error'] = array('error' => $this->upload->display_errors('<p style="color: red">','</p>'));

                    $data['headline'] = "Upload Error";
					$data['update_id'] = $update_id;
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

                    //update the database
                    $update_data['picture'] = $file_name;
                    $this->_update($update_id, $update_data);

					redirect(base_url('slides/view/'.$update_id));				
            		
            }
		}

		public function submit($update_id)
		{
			//update the record that has been submitted via view
			$this->site_security->_make_sure_is_admin();

			$submit = $this->input->post('submit',TRUE);
			$target_url = $this->input->post('target_url',TRUE);
			$alt_text = $this->input->post('alt_text',TRUE);

			if($submit == "Cancel")
			{
				$parent_id = $this->_get_parent_id($update_id);
				redirect(base_url('slides/update_group/'.$update_id));				
			}elseif($submit == "Submit"){
				$data['target_url'] = $target_url;
				$data['alt_text'] = $alt_text;
				$this->_update($update_id,$data);
				redirect(base_url('slides/view/'.$update_id));				
			}
		}


		public function get_where($id)
		{
			$query = $this->mdl_slides->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_slides->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_slides->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_slides->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_slides->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_slides->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_slides->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_slides->_custom_query($mysql_query);
			return $query;
		}

		public function get_max()
		{
			$query = $this->mdl_slides->get_max();
			return $query;
		}		
	}
?>
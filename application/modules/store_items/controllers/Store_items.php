<?php  
	class Store_items extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');

			$this->load->module('store_item_colours');
			$this->load->module('store_item_sizes');
			$this->load->module('store_cat_assign');
			$this->load->module('store_categories');


			$this->load->model('mdl_store_items');
			

			$this->form_validation->CI =& $this;
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

			$breadcrumbs_data['template'] = 'public_bootstrap';
			$breadcrumbs_data['current_page_title'] = $data['item_title'];
			$breadcrumbs_data['breadcrumbs_array'] = $this->_generate_breadcrumbs_array($update_id);
			$data['breadcrumbs_data'] = $breadcrumbs_data;

			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_module'] = "store_items";
			$data['view_file'] = "view";

			$this->templates->public_bootstrap($data);	
		}

		public function _generate_breadcrumbs_array($update_id)
		{
			$homepage_url = base_url();
			$breadcrumbs_array[$homepage_url] = 'Home';

			//figure out the sub cat title and URL
			$sub_cat_id = $this->_get_sub_cat_id($update_id);
			$sub_cat_title = $this->store_categories->_get_cat_title($sub_cat_id);
			$sub_cat_url = $this->store_categories->_get_full_cat_url($sub_cat_id);

			$breadcrumbs_array[$sub_cat_url] = $sub_cat_title;
			return $breadcrumbs_array;
		}

		public function _get_sub_cat_id($update_id)
		{

			if(!isset($_SERVER['HTTP_REFERER']))
			{
				$refer_url = '';
			}else{
				$refer_url = $_SERVER['HTTP_REFERER'];
			}
			$ditch_this = base_url()."fashion/choices/";
			$cat_url = str_replace($ditch_this,'',$refer_url);
			$sub_cat_id = $this->store_categories->_get_cat_id_from_cat_url($cat_url);
			if($sub_cat_id>0)
			{
				return $sub_cat_id;
			}else{
				$query = $this->store_cat_assign->get_where_custom('item_id',$update_id);
				foreach ($query->result() as $row) {
				$sub_cat_id = $row->cat_id;
			}

			return $sub_cat_id;
			}

			
		}

		public function _get_item_id_from_item_url($item_url)
		{
			$query = $this->get_where_custom('item_url',$item_url);
			foreach ($query->result() as $row) {
				$item_id = $row->id;
			}

			if(!isset($item_id))
			{
				$item_id = 0;
			}
			return $item_id;
		}

		public function _process_delete($update_id)
		{
			//attempt to delete colours
			$this->store_item_colours->_delete_for_item($update_id);
			//attempt to delete sizes
			$this->store_item_sizes->_delete_for_item($update_id);
			

			$data = $this->fetch_data_from_db($update_id);
			$big_pic = $data['big_pic'];
			$small_pic = $data['small_pic'];

			$big_pic_path = './big_pics/'.$big_pic;
			$small_pic_path = './small_pics/'.$small_pic;


			//remove the image
			if(file_exists($big_pic_path))//duong dan ton tai
			{
				unlink($big_pic_path);	//thi xoa file theo duong dan
			}

			if(file_exists($small_pic_path))
			{
				unlink($small_pic_path);
			}

			//delete item records form store_items
			$this->_delete($update_id);
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
				redirect(base_url('store_items/create/'.$update_id));
			} elseif($submit == 'Yes - Delete Item')
			{
				$this->_process_delete($update_id);

				$flash_msg = "The item was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('store_items/manage'));

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data['headline'] = 'Delete Item';
			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			$data['view_file'] = "deleteconf";

			$this->templates->admin($data);
		}

		public function delete_image($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data = $this->fetch_data_from_db($update_id);
			$big_pic = $data['big_pic'];
			$small_pic = $data['small_pic'];

			$big_pic_path = './big_pics/'.$big_pic;
			$small_pic_path = './small_pics/'.$small_pic;


			//remove the image
			if(file_exists($big_pic_path))//duong dan ton tai
			{
				unlink($big_pic_path);	//thi xoa file theo duong dan
			}

			if(file_exists($small_pic_path))
			{
				unlink($small_pic_path);
			}

			//update database
			unset($data);
			$data['big_pic'] = "";
			$data['small_pic'] = "";
			$this->_update($update_id, $data);

			$flash_msg = "The item image was successfully deleted.";
			$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
			$this->session->set_flashdata('item',$value);

			redirect(base_url('store_items/create/'.$update_id));
		}

		public function _generate_thumbnail($file_name)
		{
			$config['image_library'] = 'gd2';
			$config['source_image'] = './big_pics/'.$file_name;
			// $config['create_thumb'] = TRUE; 
			$config['maintain_ratio'] = TRUE;
			$config['new_image'] = './small_pics/'.$file_name;
			$config['width']         = 200;
			$config['height']       = 200;

			$this->load->library('image_lib', $config);

			$this->image_lib->resize();
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
				redirect(base_url('store_items/create/'.$update_id));				
			}

			

			$config['upload_path']          = './big_pics/';
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
                    $this->_generate_thumbnail($file_name);

                    //update the database
                    $update_data['big_pic'] = $file_name;
                    $update_data['small_pic'] = $file_name;
                    $this->_update($update_id, $update_data);

            		$data['headline'] = "Upload Success";
					$data['update_id'] = $update_id;
					$data['flash'] = $this->session->flashdata('item');
					//$data['view_module'] = "store_items";
					$data['view_file'] = "upload_success";

					$this->templates->admin($data);


                    $this->load->view('upload_success', $data);
            }
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

		public function create()
		{

			$this->site_security->_make_sure_is_admin();

			$update_id = $this->uri->segment(3); //get value -> update

			$submit = $this->input->post('submit',TRUE);

			if($submit == "Cancle")
			{
				redirect(base_url('store_items/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('item_title','Item Title','required|max_length[240]|callback_item_check');
				$this->form_validation->set_rules('item_price','Item Price','required|numeric');
				$this->form_validation->set_rules('was_price','Was price','numeric');
				$this->form_validation->set_rules('status','Status','required|numeric');
				$this->form_validation->set_rules('item_description','Item Description','required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					$data['item_url'] = url_title($data['item_title']);

					if(is_numeric($update_id))
					{
						//update the item details
						$this->_update($update_id,$data);
						$flash_msg = "The item details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

					} else{
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new item

						$flash_msg = "The item details were successfully added.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

						redirect(base_url('store_items/create/'.$update_id));
					}
				}
			}

			if((is_numeric($update_id)) && $submit != "Submit") //neu co id va chua submit -> lay du lieu tu databse
			{
				$data = $this->fetch_data_from_db($update_id);
			} else{
				$data = $this->fetch_data_from_post(); //nguoc lai thi them moi
				$data['big_pic'] = "";
			}

			if(!is_numeric($update_id)) 
			{
				$data['headline'] = 'Add New Item';
			} else{
				$data['headline'] = 'Update Item';
			}


			$data['update_id'] = $update_id;
			$data['flash'] = $this->session->flashdata('item');
			//$data['view_module'] = "store_items";
			$data['view_file'] = "create";

			$this->templates->admin($data);

		}

		public function manage()
		{
			$this->site_security->_make_sure_is_admin();
			$search_data = array();	


			if($this->input->get('item_title'))
			{
				$search_data['item_title'] = $this->input->get('item_title');

				$data['query'] = $this->get_where_like('item_title',$search_data['item_title']);


			} 
			


			// $num_rows = $data['query']->num_rows();

			// if($num_rows > 0)
			// {
			// 	$data['query'] = $this->get('item_title');
			// }

			else{
			$data['query'] = $this->get('item_title');
			$data['flash'] = $this->session->flashdata('item');
			}
			//$data['view_module'] = "store_items";
			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}

		public function fetch_data_from_post() //lay data tu form
		{
			$data['item_title'] = $this->input->post('item_title',TRUE);
			$data['item_price'] = $this->input->post('item_price',TRUE);
			$data['was_price'] = $this->input->post('was_price',TRUE);
			$data['item_description'] = $this->input->post('item_description',TRUE);
			$data['status'] = $this->input->post('status',TRUE);

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
				$data['item_title'] = $row->item_title;
				$data['item_url'] = $row->item_url;
				$data['item_price'] = $row->item_price;
				$data['item_description'] = $row->item_description;
				$data['big_pic'] = $row->big_pic;
				$data['small_pic'] = $row->small_pic;
				$data['was_price'] = $row->was_price;
				$data['status'] = $row->status;

				if(!isset($data))
				{
					$data = "";
				}

				return $data;
			}
		}




		public function get_where($id)
		{
			$query = $this->mdl_store_items->get_where($id);
			return $query;
		}

		public function get_where_like($col,$value)
		{
			$query = $this->mdl_store_items->get_where_like($col,$value);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_store_items->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_store_items->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_store_items->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_store_items->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_store_items->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_store_items->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_store_items->_custom_query($mysql_query);
			return $query;
		}

		public function item_check($str)
        {
        	$item_url = url_title($str);
        	$mysql_query = "SELECT * FROM store_items where item_title ='$str' AND item_url = '$item_url'"; //de bi hack

        	$update_id = $this->uri->segment(3);
        	if(is_numeric($update_id)){
        		//this is an update
        		$mysql_query.= " AND id!=$update_id";
        	}

        	$query = $this->_custom_query($mysql_query);
        	$num_rows = $query->num_rows();


                if ($num_rows > 0)
                {
                        $this->form_validation->set_message('item_check', 'The item title that you submitted is not available');
                        return FALSE;
                }
                else
                {
                        return TRUE;
                }
        }




	}
?>
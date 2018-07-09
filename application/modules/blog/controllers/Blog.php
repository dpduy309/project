<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Blog extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('timedate');

			$this->load->model('mdl_blog');
			$this->load->helper('string');


			$this->form_validation->CI =& $this;
		}

		public function _draw_feed_hp()
		{
			$mysql_query = "select * from blog order by date_published desc limit 0,3";
			$data['query'] = $this->_custom_query($mysql_query);
			$this->load->view('feed_hp',$data);
		}

		public function delete_image($update_id)
		{
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$data = $this->fetch_data_from_db($update_id);
			$picture = $data['picture'];

			$big_pic_path = './blog_pics/'.$picture;
			$small_picture = str_replace('.','_thumb.',$picture);
			$small_pic_path = './blog_pics/'.$small_picture;


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
			$data['picture'] = "";
			$this->_update($update_id, $data);

			$flash_msg = "The image was successfully deleted.";
			$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
			$this->session->set_flashdata('item',$value);

			redirect(base_url('blog/create/'.$update_id));
		}

		public function _generate_thumbnail($file_name,$thumbnail_name)
		{
			$config['image_library'] = 'gd2';
			$config['source_image'] = './blog_pics/'.$file_name;
			$config['maintain_ratio'] = TRUE;
			$config['new_image'] = './blog_pics/'.$thumbnail_name;
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
				redirect(base_url('blog/create/'.$update_id));				
			}

			

			$config['upload_path']          = './blog_pics/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['max_width']            = 2048;
            $config['max_height']           = 2048;
            $config['file_name']			= random_string('alnum',16);

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile'))
            {
                    $data['error'] = array('error' => $this->upload->display_errors('<p style="color: red">','</p>'));

                    $data['headline'] = "Upload Error";
					$data['update_id'] = $update_id;
					$data['flash'] = $this->session->flashdata('item');
					//$data['view_module'] = "blog";
					$data['view_file'] = "upload_image";

					$this->templates->admin($data);
                   
            }
            else
            {
            	//upload was successful
                    $data = array('upload_data' => $this->upload->data());

                    $upload_data = $data['upload_data'];

                    //raw name ... file ext
                    $raw_name = $upload_data['raw_name'];
                    $file_ext = $upload_data['file_ext'];
                    //generate a thumbnail name
                    $thumbnail_name = $raw_name."_thumb".$file_ext;


                    $file_name = $upload_data['file_name'];
                    $this->_generate_thumbnail($file_name, $thumbnail_name);

                    //update the database
                    $update_data['picture'] = $file_name;
                    $this->_update($update_id, $update_data);

            		$data['headline'] = "Upload Success";
					$data['update_id'] = $update_id;
					$data['flash'] = $this->session->flashdata('item');
					//$data['view_module'] = "blog";
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


		public function delete($update_id){
			if(!is_numeric($update_id))
			{
				redirect(base_url('site_security/not_allowed'));
			}
			$this->site_security->_make_sure_is_admin();

			$submit =$this->input->post('submit');
			if($submit == 'Cancel')
			{
				redirect(base_url('blog/create/'.$update_id));
			} elseif($submit == 'Yes - Delete Page')
			{
				$this->_delete($update_id);

				$flash_msg = "The blog was successfully deleted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);
			}

				redirect(base_url('blog/manage'));

		}

		public function deleteconf($update_id)
		{
			if(!is_numeric($update_id))
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
				redirect(base_url('blog/manage'));				
			}

			if($submit == "Submit")
			{
				$this->form_validation->set_rules('page_title','Page Title','required|max_length[240]');

				$this->form_validation->set_rules('date_published','Date Published','required');

				$this->form_validation->set_rules('page_content','Page Content','required');

				if($this->form_validation->run() == TRUE)
				{
					//get the variables
					$data = $this->fetch_data_from_post();
					$data['page_url'] = url_title($data['page_title']);
					//->unix timestamp
					// $data['date_published'] = $this->timedate->make_timestamp_from_datepicker_us($data['date_published']);
					$data['date_published'] = $this->timedate->make_timestamp_from_datepicker_us($data['date_published']);


					if(is_numeric($update_id))
					{	
						// var_dump($data['date_published']);
						//update the item details
						$this->_update($update_id,$data);
						$flash_msg = "The blog details were successfully updated.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

					} else{
						$this->_insert($data);
						//$update_id = $this->get_max(); //get the ID of the new item

						$flash_msg = "The article were successfully created.";
						$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
						$this->session->set_flashdata('item',$value);

						redirect(base_url('blog/create/'.$update_id));
					}
				}
			}

			if((is_numeric($update_id)) && $submit != "Submit") //neu co id va chua submit -> lay du lieu tu databse
			{
				$data = $this->fetch_data_from_db($update_id);
			} else{
				$data = $this->fetch_data_from_post(); //nguoc lai thi them moi
				$data['picture'] = "";
			}

			if(!is_numeric($update_id)) 
			{
				$data['headline'] = 'Create New Blog';
			} else{
				$data['headline'] = 'Update Blog Details';
			}

			if($data['date_published'] > 0)
			{
				//chac chan la unix time -> convert lai thanh date format
				$data['date_published'] = $this->timedate->get_nice_date($data['date_published'], 'datepicker_us');
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
			$data['date_published'] = $this->input->post('date_published',TRUE);
			$data['author'] = $this->input->post('author',TRUE);
			$data['picture'] = $this->input->post('picture',TRUE);




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
				$data['date_published'] = $row->date_published;
				$data['author'] = $row->author;
				$data['picture'] = $row->picture;




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
			
			$data['query'] = $this->get('date_published desc');
			$data['flash'] = $this->session->flashdata('item');

			$data['view_file'] = "manage";

			$this->templates->admin($data);

		}

		public function get_where($id)
		{
			$query = $this->mdl_blog->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_blog->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_blog->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_blog->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_blog->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_blog->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_blog->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_blog->_custom_query($mysql_query);
			return $query;
		}

		



	}
?>
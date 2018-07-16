<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Comments extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');

		
			$this->load->model('mdl_comments');
			

		}

		public function submit()
		{
			$this->site_security->_make_sure_is_admin();
			$data['comment'] = $this->input->post('comment',TRUE);
			$data['comment_type'] = $this->input->post('comment_type',TRUE);
			$data['update_id'] = $this->input->post('update_id',TRUE);
			$data['date_created'] = time();

			if($data['comment']!='')
			{
				$this->_insert($data);
				$flash_msg = "The comment were successfully submitted.";
				$value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
				$this->session->set_flashdata('item',$value);	
			}

			$finish_url = $_SERVER['HTTP_REFERER'];
			redirect($finish_url);


		}

		public function _draw_comments($comment_type,$update_id)
		{
			$mysql_query = "SELECT * FROM comments where comment_type='$comment_type' and update_id='$update_id' order by date_created";
			$data['query'] = $this->_custom_query($mysql_query);

			$num_rows = $data['query']->num_rows();

			if($num_rows>0)
			{
				$this->load->view('comments', $data);
			}

		}


			


		public function get_where($id)
		{
			$query = $this->mdl_comments->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_comments->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_comments->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_comments->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_comments->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_comments->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_comments->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_comments->_custom_query($mysql_query);
			return $query;
		}

		// public function Categorie_check($str)
  //       {
  //       	$Categorie_url = url_title($str);
  //       	$mysql_query = "SELECT * FROM store_Hompage Offers where block_title ='$str' AND Categorie_url = '$Categorie_url'"; //de bi hack

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
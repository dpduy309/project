<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Mdl_store_basket extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
			
		}


		public function get_table()
		{
			$table = "store_basket";
			return $table;
		}

		public function get($order_by)
		{
			$table = $this->get_table();
			$this->db->order_by($order_by);
			$query = $this->db->get($table);
			return $query;
		}

		public function get_with_limit($limit,$offset,$order_by)
		{
			$table = $this->get_table();
			$this->db->limit($limit,$offset);
			$this->db->order_by($order_by);
			$query = $this->db->get($table);
			return $query;
		}

		public function get_where($id)
		{
			$table = $this->get_table();
			$this->db->where('id',$id);
			$query = $this->db->get($table);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$table = $this->get_table();
			$this->db->where($col,$value);
			$query = $this->db->get($table);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->db->query($mysql_query);
			return $query;
		}

		public function _insert($data)
		{
			$table = $this->get_table();
			if($data)
			{
				$this->db->insert($table,$data);
			}
		}

		public function _update($update_id,$data)
		{
			$table = $this->get_table();
			$this->db->where('id',$update_id);
			$this->db->update($table,$data);
		}

		public function _delete($id)
		{
			$table = $this->get_table();
			$this->db->where('id',$id);
			$this->db->delete($table);
		}
	}



?>
<?php 
	if ( !defined('BASEPATH')) exit('No direct script access allowed');
	Class MY_Model extends CI_Model{
		//Ten table
		var $table ='';
		//key chinh cua table
		var $key='id';
		//Order mac dinh (VD Order= arr('id','desc'))
		var $order ='';
		//CAC fiel select mac dinh khi get_list (VD: $select ='id,name')
		var $select ='';
		/**
		Them row moi
		$data: du lieu can them vao
		*/
		function create($data)
		{
			if($this->db->insert($this->table,$data))
			{
				return TRUE;
			}else{
				return FALSE;
			}
		}
		/*
		Them row moi tu id
		id: Khoa chinh cua mang can sua
		data: mang du lieu can chinh sua
		*/
		function update($id,$data)
		{
			if(!$id)
			{
				return FALSE;
			}
				$where = array();
				$where[$this->key] = $id;
				$this->upadte_rule($where,$data);

				return TRUE;
			
		}
		/*
		Cap nhat row tu dieu khien 
		$where : dieu khien 
		$data: mang du lieu cap nhat
		*/
		function upadte_rule($where,$data)
		{
			if(!$where)
			{
				return FALSE;
			}
				$this->db->where($where);
				$this->db->update($this->table,$data);
				return TRUE;
			
		}
		/*
		Xoa row tu data
		*/
		function delete($id)
		{
			if(!$id)
			{
				return FALSE;
			}
			//neu la so
			if(is_numeric($id))
			{
				$where = array($this->key=>$id);
			}else{
				//$id = 1,2,3;
				$where = $this->key. "IN(".$id.")";
			}
			$this->del_rule($where);
			return TRUE;	
		}
		/*
		Xoa row tu dieu khien
		*/
		function del_rule($where)
		{
			if(!$where)
			{
				return FALSE;
			}
			$this->db->where($where);
			$this->db->delete($this->table);

			return TRUE;
		}

		/*
		Thuc hien cau lenh query
		$sql: cau lenh sql;
		*/
		function query($sql)
		{
			$rows = $this->db->query($id);
			return $rows->result();
		}

		/*
		Lay thong tin cua row tu id
		$id; id can lay thong tin
		$field: cot du lieu can lay
		*/
		function get_info($id, $field = '')
		{
			if(!$id)
			{
				return FALSE;
			}
			$where = array();
			$where[$this->key] = $id;
			return $this->get_info_rule($where, $field);
		}
		/*
		* lay thong tin row tu dieu kien
		* $where: Mang dieu kien
		* $field: Cot muon lay du lieu
		*/
		function get_info_rule($where= array(),$field='')
		{
			if(!$field)
			{
				$this->db->select($field);
			}
			$this->db->where($where);
			$query = $this->db->get($this->table);
			if($query->num_rows())
			{
				return $query->row();
			}
			return FALSE;
		}
		/*
		*lay tong so
		*/
		function get_total($input = array())
		{
			$this->get_list_set_input($input);
			$query = $this->db->get($this->table);
			return $query->num_rows();
		}
		/*
		*lay 1 row
		*/
		function get_row($input= array())
		{
			$this->get_list_set_input($input);
			$query = $this->db->get($this->table);
			return $query->row();
		}
		/*
		* lay danh sach
		*input: mang du lieu vao 
		*/
		function get_list($input= array())
		{
			$this->get_list_set_input($input);
			//thuc hien truy van du lieu
			$query = $this->db->get($this->table);
			// echo $this->db->last_query()
			return $query->result();
		}
		/*
		* Gan cac thuoc tinh trong input khi lay danh sach
		*$input: mang du lieu vao
		*/
		protected function get_list_set_input($input= array())
		{
			//Them dieu kien truy van truyen qua bien $input['Where']
			//(VD: $input['where'] array('email'=>'haihorrible@gamil.com'))
			if((isset($input['where']))&& $input['where'])
			{
				$this->db->where($input['where']);
			}
			//tim kiem like
			//$input['like'] = array('name'=>'abc')
			if ((isset($input['like']))&&$input['like']) {
				$this->db->like($input['like'][0],$input['like'][1]);
			}
			if(isset($input['order'][0]) && isset($input['order'][1]))
			{
				$this->db->order_by($input['order'][0],$input['order'][1]);
			}else{
				$order =( $this->order=='') ? array($this->table.'.'.$this->key,'desc'): $this->order;
				$this->db->order_by($order[0],$order[1]);
			}
			if(isset($input['limit'][0]) &&isset($input['limit'][1]))
			{
				$this->db->limit($input['limit'][0],$input['limit'][1]);
			}
		}

		function check_exists($where = array())
		{
			$this->db->where($where);

			$query = $this->db->get($this->table);

			if($query->num_rows()>0)
			{
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}




 ?>
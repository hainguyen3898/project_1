<?php
	Class User extends MY_Controller
	{
		function __construct()
		{
			parent :: __construct();
			$this->load->model('user_model');
		}
		
		function create()
		{
			$data = array();
			$data ['name'] = 'Hải Hững Hờ';
			$data['email'] = 'horribleforwork@gmail.com';
			$data['phone'] = '0349327070';
			$data['address'] = '030898';
			$data['password'] = 'passcailoz1';
			if($this->user_model->create($data))
			{
				echo "Them moi thanh cong";
			}else{
				echo"Them moi khong thanh cong";
			}
		}
		
		function update()
		{
			$id = '19';
			$data['name'] = 'Hải Hững Hờ';
			$data['email'] = 'horribleforwork@gmail.com';
			$data['phone'] = '113';
			$data['address'] = 'Bi';
			if($this->user_model->update($id,$data))
			{
				echo "Cap nhat thanh cong";
			}else{
				echo "Cap nhat khong tahnh cong";
			}
		}
		
		function delete()
		{
			$id = 20;
			if($this->user_model->delete($id))
			{
				echo "Xoa thanh cong";
			}else{
				echo "xoa khong thanh cong";
			}
		}
		
		function get_info()
		{
			$id ='21';
			$info = $this->user_model->get_info($id);
			echo "<pre>";
			print_r ($info);
		}
		function get_list()
		{
			$input = array();
			//$input['where'] = array('name'=>'Hải Hững Hờ');
			$input['order'] = array('name','asc');
			$input['limit'] = array (2,1);
			$list = $this->user_model->get_list($input);
			echo "<pre>";
			print_r ($list);
		}
		
	}
?>
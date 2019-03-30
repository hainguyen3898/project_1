<?php 
	Class Catalog extends MY_Controller
	{
		function __construct()
		{
			parent :: __construct();
			$this->load->model('catalog_model');
		}

		function index()
		{
			$list = $this->catalog_model->get_list();
			$this->data['list'] = $list;

			$message = $this->session->flashdata('message');
			$this->data['message'] = $message;

			$this->data['temp'] = 'admin/catalog/index';
			$this->load->view('admin/main',$this->data);
		}

		function add()
		{
			//load ra thu vien validaion du lieu 
			$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên','required');

				if($this->form_validation->run())
				{
					$name = $this->input->post('name');
					$parent_id = $this->input->post('parent_id');
					$sort_order = $this->input->post('sort_order');
					
					$data = array(
						'name' => $name,
						'parent_id' => $parent_id,
						'sort_order' => intval($sort_order)	
					);
					if($this->catalog_model->create($data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('catalog'));
				}
			}

			//lay ra danh sach danh muc cha
			$input = array();
			$input['where'] = array('parent_id' => '0');
			$list = $this->catalog_model->get_list($input);
			$this->data['list'] = $list;

			$this->data['temp'] = 'admin/catalog/add';
			$this->load->view('admin/main',$this->data);
		}

		function edit()
		{
			//load ra thu vien validaion du lieu 
			$this->load->library('form_validation');
			$this->load->helper('form');

			$id = $this->uri->rsegment(3);
			$info = $this->catalog_model->get_info($id);
			if(!$info)
			{
				$this->session->set_flashdata('message','không tồn tại danh mục này');
				redirect(admin_url('catalog'));
			}
			$this->data['info'] = $info;

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên','required');

				if($this->form_validation->run())
				{
					$name = $this->input->post('name');
					$parent_id = $this->input->post('parent_id');
					$sort_order = $this->input->post('sort_order');
					
					$data = array(
						'name' => $name,
						'parent_id' => $parent_id,
						'sort_order' => intval($sort_order)	
					);
					if($this->catalog_model->update($id,$data))
					{
						$this->session->set_flashdata('message','Cập nhật dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Cập nhật dữ liệu không thành công');
					}

					redirect(admin_url('catalog'));
				}
			}

			//lay ra danh sach danh muc cha
			$input = array();
			$input['where'] = array('parent_id' => '0');
			$list = $this->catalog_model->get_list($input);
			$this->data['list'] = $list;

			$this->data['temp'] = 'admin/catalog/edit';
			$this->load->view('admin/main',$this->data);
		}

		function delete()
		{
			$id = $this->uri->rsegment('3');
			$this->_del($id);
			$this->session->set_flashdata('message','Xoá dữ liệu thành công');
			redirect(admin_url('catalog'));
		}

		function delete_all()
		{
			$ids = $this->input->post('ids');
			foreach($ids as $id)
			{
				$this->_del($id, false);
			}

		}

		private function _del($id, $redirect = TRUE)
		{

			$id = intval($id);
			//lay ra thong tin admin
			$info = $this->catalog_model->get_info($id);

			if(!$info)
			{
				$this->session->set_flashdata('message','Danh mục không tồn tại');
				if($redirect)
				{
					redirect(admin_url('catalog'));
				}else{
					return false;
				}
			}
			//kiem tra neu danh muc co san pham khong thi moi thuc hien xoa
			$this->load->model('product_model');
			$product = $this->product_model->get_info_rule(array('catalog_id'=> $id,'id'));
			if($product)
			{
				$this->session->set_flashdata('message','Danh mục '.$info->name.' có chứa sản phẩm, cần xoá sản phẩm trước khi xoá sản phẩm');
				if($redirect)
				{
					redirect(admin_url('catalog'));
				}else{
					return false;
				}
				
			}
			//thuc hien xoa admin
			$this->catalog_model->delete($id);
		}
	}

 ?>
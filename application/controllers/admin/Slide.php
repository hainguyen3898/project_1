<?php 
	Class Slide extends MY_Controller
	{
		function __construct()
		{
			parent :: __construct();
			$this->load->model('slide_model');
		}

		//hien thi danh sach Slide
		function index()
		{
			//lay ra tong so luong tat ca cac élide
			$total_rows = $this->slide_model->get_total();
			$this->data['total_rows'] = $total_rows;
			
			$input = array();
			
			//lay danh sach bai viet
			$list = $this->slide_model->get_list($input);
			$this->data['list'] = $list;
			// lay danh sach danh muc san pham
			
 
			$message = $this->session->flashdata('message');
			$this->data['message'] = $message;

			$this->data['temp'] = 'admin/slide/index';
			$this->load->view('admin/main',$this->data);
		}

		//them bai viet moi
		function add()
		{
			
			
			//load view
			$this->data['temp'] = 'admin/slide/add';
			$this->load->view('admin/main',$this->data);

			$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên slide','required');


				if($this->form_validation->run())
				{
					
					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/slide';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';

					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					// //upload cac anh kem theo
					// $image_list = array();
					// $upload_list = $this->upload_library->upload_file($upload_path,'image_list');
					// $image_list = json_encode($slide->image_list);

					
					$data = array(
						'name' => $this->input->post('name'),
						'image_link' => $image_link,
						'link' => $this->input->post('link'),
						'info' => $this->input->post('info'),
						'sort_order' => $this->input->post('sort_order'),
					);
					if($this->slide_model->create($data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('slide'));
				}
			}
		}

		function edit()
		{
			$id = $this->uri->rsegment('3');
			$slide = $this->slide_model->get_info($id);
			if(!$slide)
			{
				$this->session->set_flashdata('message','Không tồn tại bài viết này');
				redirect(admin_url('slide'));
			}
			$this->data['slide'] = $slide;
			
			//load view
			$this->data['temp'] = 'admin/slide/edit';
			$this->load->view('admin/main',$this->data);

				$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên slide','required');

				if($this->form_validation->run())
				{

					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/slide';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';

					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					$data = array(
						'name' => $this->input->post('name'),
						'link' => $this->input->post('link'),
						'info' => $this->input->post('info'),
						'sort_order' => $this->input->post('sort_order'),
					);
					if($image_link !='')
					{
						$data['image_link'] = $image_link;
					}
					if($this->slide_model->update($slide->id,$data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('slide'));
				}
			}
		}

		function del()
		{
			$id = $this->uri->rsegment('3');
			$this->_del($id);
			$this->session->set_flashdata('message','Xoá thành công bài viết');
			redirect(admin_url('slide'));
		}

		//xoa nhieu san pham
		function delete_all()
		{
			$ids = $this->input->post('ids');
			foreach($ids as $id)
			{
				$this->_del($id);
			}
			
		}

		private function _del($id)
		{
			$slide = $this->slide_model->get_info($id);
			if(!$slide)
			{
				$this->session->set_flashdata('message','Không có bài viết');
				redirect(admin_url('slide'));
			}

			$this->slide_model->delete($id);
			//xoa cac anh cua san pham
			$image_link = './upload.slide/'.$slide->image_link;
			if(file_exists('./upload/slide/'.$slide->image_link))
			{
				unlink($image_link);
			}
			//Xoa cac anh kem theo cua san pham
			
		}
	}


 ?>
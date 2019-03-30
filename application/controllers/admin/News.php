<?php 
	Class News extends MY_Controller
	{
		function __construct()
		{
			parent :: __construct();
			$this->load->model('news_model');
		}

		//hien thi danh sach san pham
		function index()
		{
			//lay ra tong so luong tat ca cac bai viet
			$total_rows = $this->news_model->get_total();
			$this->data['total_rows'] = $total_rows;

			//load ra thu vien phan trang
			$this->load->library('pagination');
			$config = array();
			$config['total_rows'] = $total_rows;//Tổng tất cả các sản phẩm trên web
			$config['base_url'] = admin_url('news/index');//link hiển thị ra dánh sách sản phẩm
			$config['per_page'] = '5'; //so luong sp hien thi tren 1 trang
			$config['uri_segment'] = '4';//phan doan hien thi ra so trang tren url
			$config['next_link'] = 'Trang kế';
			$config['prev_link'] = 'Trang sau';
			//khoi tao cau hinh phan trang
			$this->pagination->initialize($config);
			$segment = $this->uri->segment('4');
			$segment = intval($segment);
			
			$input = array();
			$input['limit'] = array(
				$config['per_page'],
				$segment

			);

			//kiem tra co thuc hien duoc du lieu hay khong
			$id = $this->input->get('id');
			$input['where'] = array();
			$id = intval($id);
			if($id > 0)
			{
				$input['where']['id'] = $id;

			}

			$title = $this->input->get('title');
			if($title)
			{
				$input['like'] = array('title',$title);
			}
			
			//lay danh sach bai viet
			$list = $this->news_model->get_list($input);
			$this->data['list'] = $list;
			// lay danh sach danh muc san pham
			
 
			$message = $this->session->flashdata('message');
			$this->data['message'] = $message;

			$this->data['temp'] = 'admin/news/index';
			$this->load->view('admin/main',$this->data);
		}

		//them bai viet moi
		function add()
		{
			
			
			//load view
			$this->data['temp'] = 'admin/news/add';
			$this->load->view('admin/main',$this->data);

			$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('title','Tiêu đề','required');
				$this->form_validation->set_rules('content','Nội dung','required');


				if($this->form_validation->run())
				{
					
					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/news';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';

					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					// //upload cac anh kem theo
					// $image_list = array();
					// $upload_list = $this->upload_library->upload_file($upload_path,'image_list');
					// $image_list = json_encode($news->image_list);

					
					$data = array(
						'title' => $this->input->post('title'),
						'image_link' => $image_link,
						'meta_desc' => $this->input->post('meta_desc'),
						'meta_key' => $this->input->post('meta_key'),
						'content' => $this->input->post('content'),
						'created' => now(),
					);
					if($this->news_model->create($data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('news'));
				}
			}
		}

		function edit()
		{
			$id = $this->uri->rsegment('3');
			$news = $this->news_model->get_info($id);
			if(!$news)
			{
				$this->session->set_flashdata('message','Không tồn tại bài viết này');
				redirect(admin_url('news'));
			}
			$this->data['news'] = $news;
			
			//load view
			$this->data['temp'] = 'admin/news/edit';
			$this->load->view('admin/main',$this->data);

				$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('title','Tiêu đề','required');
				$this->form_validation->set_rules('content','Nội dung','required');

				if($this->form_validation->run())
				{

					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/news';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';

					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					$data = array(
						'title' => $this->input->post('title'),
						'meta_desc' => $this->input->post('meta_desc'),
						'meta_key' => $this->input->post('meta_key'),
						'content' => $this->input->post('content'),
						'created' => now(),
					);
					if($image_link !='')
					{
						$data['image_link'] = $image_link;
					}
					if($this->news_model->update($news->id,$data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('news'));
				}
			}
		}

		function del()
		{
			$id = $this->uri->rsegment('3');
			$this->_del($id);
			$this->session->set_flashdata('message','Xoá thành công bài viết');
			redirect(admin_url('news'));
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
			$news = $this->news_model->get_info($id);
			if(!$news)
			{
				$this->session->set_flashdata('message','Không có bài viết');
				redirect(admin_url('news'));
			}

			$this->news_model->delete($id);
			//xoa cac anh cua san pham
			$image_link = './upload.news/'.$news->image_link;
			if(file_exists('./upload/news/'.$news->image_link))
			{
				unlink($image_link);
			}
			//Xoa cac anh kem theo cua san pham
			
		}
	}


 ?>
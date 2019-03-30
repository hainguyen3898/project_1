<?php 
	Class Product extends MY_Controller
	{
		function __construct()
		{
			parent :: __construct();
			$this->load->model('product_model');
		}

		//hien thi danh sach san pham
		function index()
		{
			//lay ra tong so luong tat ca cac san pham
			$total_rows = $this->product_model->get_total();
			$this->data['total_rows'] = $total_rows;

			//load ra thu vien phan trang
			$this->load->library('pagination');
			$config = array();
			$config['total_rows'] = $total_rows;//Tổng tất cả các sản phẩm trên web
			$config['base_url'] = admin_url('product/index');//link hiển thị ra dánh sách sản phẩm
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

			$name = $this->input->get('name');
			if($name)
			{
				$input['like'] = array('name',$name);
			}
			$catalog_id = $this->input->get('catalog');
			$catalog_id = intval($catalog_id);
			if($catalog_id > 0)
			{
				$input['where']['catalog_id'] = $catalog_id; 
			}
			//lay danh sach san pham
			$list = $this->product_model->get_list($input);
			$this->data['list'] = $list;
			// lay danh sach danh muc san pham
			$this->load->model('catalog_model');
			$input = array();
			$input['where'] = array('parent_id' => '0' );
			$catalogs = $this->catalog_model->get_list();
			foreach($catalogs as $row)
			{
				$input['where'] = array('parent_id'=>$row->id);
				$subs = $this->catalog_model->get_list($input);
				$row->subs = $subs;
			}
			$this->data['catalogs'] = $catalogs;
 
			$message = $this->session->flashdata('message');
			$this->data['message'] = $message;

			$this->data['temp'] = 'admin/product/index';
			$this->load->view('admin/main',$this->data);
		}

		function add()
		{
			$this->load->model('catalog_model');
			$input = array();
			$input['where'] = array('parent_id'=>0);
			$catalogs = $this->catalog_model->get_list();
			foreach($catalogs as $row)
			{
				$input['where'] = array('parent_id'=>$row->id);
				$subs = $this->catalog_model->get_list($input);
				$row->subs = $subs;
			}
			$this->data['catalogs'] = $catalogs;
			
			//load view
			$this->data['temp'] = 'admin/product/add';
			$this->load->view('admin/main',$this->data);

			$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên','required');
				$this->form_validation->set_rules('catalog','Thể loại','required');
				$this->form_validation->set_rules('price','Gia','required');


				if($this->form_validation->run())
				{
					$name = $this->input->post('name');
					$catalog_id = $this->input->post('catalog');
					$price = $this->input->post('price');
					$price = str_replace(',','',$price);
					$discount = $this->input->post('discount');
					$discount = str_replace(',','',$discount);

					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/product';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';

					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					// //upload cac anh kem theo
					// $image_list = array();
					// $upload_list = $this->upload_library->upload_file($upload_path,'image_list');
					// $image_list = json_encode($product->image_list);

					
					$data = array(
						'name' => $name,
						'catalog_id' => $catalog_id,
						'price' => $price,
						'image_link' => $image_link,
						'discount' => $discount,
						'warranty' => $this->input->post('warranty'),
						'gifts' => $this->input->post('gifts'),
						'site_title' => $this->input->post('site_title'),
						'meta_desc' => $this->input->post('meta_desc'),
						'meta_key' => $this->input->post('meta_key'),
						'content' => $this->input->post('content'),
						'created' => now(),
					);
					if($this->product_model->create($data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('product'));
				}
			}
		}

		function edit()
		{
			$id = $this->uri->rsegment('3');
			$product = $this->product_model->get_info($id);
			if(!$product)
			{
				$this->session->set_flashdata('message','Không tồn tại sản phẩm này');
				redirect(admin_url('product'));
			}
			$this->data['product'] = $product;
			$this->load->model('catalog_model');
			$input = array();
			$input['where'] = array('parent_id'=>0);
			$catalogs = $this->catalog_model->get_list();
			foreach($catalogs as $row)
			{
				$input['where'] = array('parent_id'=>$row->id);
				$subs = $this->catalog_model->get_list($input);
				$row->subs = $subs;
			}
			$this->data['catalogs'] = $catalogs;
			
			//load view
			$this->data['temp'] = 'admin/product/edit';
			$this->load->view('admin/main',$this->data);

				$this->load->library('form_validation');
			$this->load->helper('form');

			if($this->input->post())
			{
				$this->form_validation->set_rules('name','Tên','required');
				$this->form_validation->set_rules('catalog','Thể loại','required');
				$this->form_validation->set_rules('price','Gia','required');


				if($this->form_validation->run())
				{
					$name = $this->input->post('name');
					$catalog_id = $this->input->post('catalog');
					$price = $this->input->post('price');
					$price = str_replace(',','',$price);

					//lay ten file anh duoc upload len
					$this->load->library('Upload_library');
					$upload_path = './upload/product';
					$upload_data = $this->upload_library->upload($upload_path,'image');
					$image_link = '';
					$discount = $this->input->post('discount');
					$discount = str_replace(',','',$discount);


					if(isset($upload_data['file_name']))
					{
						$image_link = $upload_data['file_name'];
					}

					//upload cac anh kem theo
			

					
					$data = array(
						'name' => $name,
						'catalog_id' => $catalog_id,
						'price' => $price,
						'discount' => $discount,
						'warranty' => $this->input->post('warranty'),
						'gifts' => $this->input->post('gifts'),
						'site_title' => $this->input->post('site_title'),
						'meta_desc' => $this->input->post('meta_desc'),
						'meta_key' => $this->input->post('meta_key'),
						'content' => $this->input->post('content')
					);
					if($image_link !='')
					{
						$data['image_link'] = $image_link;
					}
					if($this->product_model->update($product->id,$data))
					{
						$this->session->set_flashdata('message','Thêm mới dữ liệu thành công');
					}else{
						$this->session->set_flashdata('message','Thêm mới dữ liệu không thành công');
					}

					redirect(admin_url('product'));
				}
			}
		}

		function del()
		{
			$id = $this->uri->rsegment('3');
			$this->_del($id);
			$this->session->set_flashdata('message','Xoá thành công sản phẩm');
			redirect(admin_url('product'));
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
			$product = $this->product_model->get_info($id);
			if(!$product)
			{
				$this->session->set_flashdata('message','Không có sản phẩm');
				redirect(admin_url('product'));
			}

			$this->product_model->delete($id);
			//xoa cac anh cua san pham
			$image_link = './upload.product/'.$product->image_link;
			if(file_exists('./upload/product/'.$product->image_link))
			{
				unlink($image_link);
			}
			//Xoa cac anh kem theo cua san pham
			$image_list = json_encode($product->image_list);
			if(is_array($image_list))
			{
				foreach($imaage as $img)
				{
					$image_list = './upload.product/'.$img;
					if(file_exists($product->image_list))
					{
						unlink($image_link);
					}
				}
			}
		}
	}


 ?>
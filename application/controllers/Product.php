<?php 
	Class Product extends MY_Controller
	{
		function __construct()
		{
			parent:: __construct();
			//load model
			$this->load->model('product_model');


		}
		// hien thi danh sach san pham theo danh muc
		function catalog()
		{
			$this->load->model('catalog_model');
			//lay ID cua the loai
			$id = intval($this->uri->rsegment('3'));
			//lay ra thong tin cua the loai
			$catalog = $this->catalog_model->get_info($id);
			if(!$catalog)
			{
				redirect();
			}
			//lay ra danh sach san pham danh muc do
			//Phan trang
			$total_rows = $this->product_model->get_total();
			$this->data['total_rows'] = $total_rows;
			//load ra thu vien phan trang
			$this->load->library('pagination');
			$config = array();
			$config['total_rows'] = $total_rows;//tong tat ca cac san pham tren website
			$config['base_url']   = base_url('product/catalog'.$id);//link hien thi ra danh sach san pham
			$config['per_page']   = 15;//so luong danh sach san pham hien thi trong 1 trang
			$config['uri_segment']= 4;//phan doan hien thi tren trang url
			$config['next_link']  = 'Trang kế';
			$config['prev_link']  = 'Trang sau';
			//khoi tao cac cau hinh phan trang
			$this->pagination->initialize($config);

			$segment = $this->uri->segment('4');
			$segment = intval($segment);

			$input   = array();
			$input['limit'] = array($config['per_page'],$segment);
			$input['where'] = array('catalog_id'=>$id); 

			//lay danh sach san pham
			$list = $this->product_model->get_list($input);
			$this->data['list'] = $list;

			//hien thi ra view
			$this->data['temp'] = 'site/product/catalog';
		}
	}


 ?>
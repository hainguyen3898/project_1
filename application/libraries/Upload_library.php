<?php 
	Class Upload_library
	{
		var $CI = '';

		function __construct()
		{
			$this->CI = & get_instance();
		}

		function upload($upload_path = '', $file_name = '')
		{
			$config = $this->config($upload_path);
			$this->CI->load->library('upload',$config);
			if($this->CI->upload->do_upload($file_name))
			{
				$data = $this->CI->upload->data();
			}else{
				$data = $this->CI->upload->display_errors();
			}
			return $data;

		}

		function upload_file($upload_path = '', $file_name = '')
		{
			$config = $this->config($upload_path);

			  $file  = $_FILES['image_list'];
		        $count = count($file['name']);//lấy tổng số file được upload
		        $image_list = array();
		        for($i=0; $i<=$count-1; $i++) {
		              
		              $_FILES['userfile']['name']     = $file['name'][$i];  //khai báo tên của file thứ i
		              $_FILES['userfile']['type']     = $file['type'][$i]; //khai báo kiểu của file thứ i
		              $_FILES['userfile']['tmp_name'] = $file['tmp_name'][$i]; //khai báo đường dẫn tạm của file thứ i
		              $_FILES['userfile']['error']    = $file['error'][$i]; //khai báo lỗi của file thứ i
		              $_FILES['userfile']['size']     = $file['size'][$i]; //khai báo kích cỡ của file thứ i
		              //load thư viện upload và cấu hình
		              $this->CI->load->library('upload', $config);
		              //thực hiện upload từng file
		              if($this->CI->upload->do_upload())
		              {
		                  //nếu upload thành công thì lưu toàn bộ dữ liệu
		                  $data = $this->CI->upload->data();
		                  // pre($data);
		                  //in cấu trúc dữ liệu của các f
		                  $image_list[] = $data['file_name'];
		              }     
         		}
         		return $image_list;

		}

		function config($upload_path = '')
		{
				$config = array();

				$config['upload_path'] = $upload_path;

				$config['allowed_types'] = 'jpg|png|gif';

				$config['max_size'] = '1200';

				$config['max_width'] = '1028';

				$config['max_height'] = '1028';

				return $config;
		}
	}

 ?>
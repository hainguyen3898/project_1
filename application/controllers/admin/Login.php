<?php 
	Class login extends MY_controller
	{
		function index()
		{
			$this->load->library('form_validation');
			$this->load->helper('form');
			
			if($this->input->post())
			{
				$this->form_validation->set_rules('login','login','callback_check_login');
				if($this->form_validation->run())
				{
					$this->session->set_userdata('login',true);
					redirect(admin_url('home'));
				}
			}

			$this->load->view('admin/login/index');
		}

		function check_login()
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$password = md5($password);

			$this->load->model('admin_model');
			$where = array(
				'username' => $username,
				'password' => $password
			);
			if($this->admin_model->check_exists($where))
			{
				return TRUE;
			}
				$this->form_validation->set_message(__FUNCTION__,'Sai tên đăng nhập hoặc mật khẩu');
				return FALSE;

				redirect(admin_url('login'));

		}
	}

 ?>
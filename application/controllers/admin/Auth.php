<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_admin');
		$this->lang->load('admin/auth', $this->site_language);
	}

	public function index()
	{
		if(!$this->input->get('token') && $this->token)
		{
			$data['warning'] = $this->lang->line('error_token');
		}
		
		$this->_check_login('login');
		
		//Language
		$data['input_email_text']		= $this->lang->line('input_email_text');
		$data['input_password_text']	= $this->lang->line('input_password_text');	
		$data['string_forgot_password']	= $this->lang->line('string_forgot_password');
		$data['btn_login']				= $this->lang->line('btn_login');

		//data
		$data['post'] = array();
			
		//hrefs
		$data['href_reset']	= base_url()."admin/auth/reset";
		$data['href_login']	= base_url()."admin/auth";
		
		$this->load->library('form_validation');
		
		if($this->input->method() == 'post')
		{
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('s_email', 'E-mail', 'trim|required');
			$this->form_validation->set_rules('s_password', 'Password', 'trim|required|callback__validate_login');
			
			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			if($this->form_validation->run($this) == TRUE)
			{
				//we are logged in redirect to the dashboard.
				$this->_go_url('admin/dashboard');
			}
		}

		$data['content_view']	= 'admin/admin_login_v';
		$this->template->load_admin_login_template($data);
	}
	
	public function logout()
	{
		$this->_destroy_session();
		redirect(base_url());
	}
	
	public function reset()
	{
		$data['btn_reset']				= $this->lang->line('btn_reset');
		$data['input_email_text']		= $this->lang->line('input_email_text');
		$data['string_reset_password']	= $this->lang->line('string_reset_password');

		if($this->input->method() == 'post')
		{
			//empty array
			$data['post'] = array();
			
			//return all post fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			$validate = $this->_validate_reset($this->input->post());
			if(count($validate['errors']) == 0)
			{
				$check = $this->M_admin->valid_email($this->input->post('r_email'));
				if($check)
				{
					$this->_reset_member($check, "reset_account");
				}
				
				$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_reset_success'));
				redirect(base_url().'admin/auth/reset');
			}
			else
			{	
				//error on field(s)
				$data['errors'] = $validate['errors'];	
			}
		}

		$data['content_view']	= 'admin/admin_reset_v';
		
		//render to screen
		$this->template->load_admin_login_template($data);
	}
	
	public function reset_confirm($reset_token=false)
	{
		$check = $this->M_admin->check_reset_token($reset_token);
		
		if(!$check)
		{
			redirect(base_url().'admin/auth/reset');
		}
		
		$data['string_new_password'] 				= $this->lang->line('string_new_password');
		$data['input_new_password_text'] 			= $this->lang->line('input_new_password_text');
		$data['input_new_password_confirm_text'] 	= $this->lang->line('input_new_password_confirm_text');
		
		$data['btn_change']	= $this->lang->line('btn_change');
		
		if($this->input->method() == 'post')
		{
			$validate = $this->_validate_new($this->input->post());
			if(count($validate['errors']) == 0)
			{
				$update = $this->M_admin->update_password($this->input->post('password'), $check);

				if($update)
				{
					$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_password_success'));
				}
				
				redirect(base_url().'admin/auth');
				
			}
			else
			{	
				//error on field(s)
				$data['errors'] = $validate['errors'];	
			}
		}

		$data['content_view']	= 'admin/admin_reset_confirm_v';
		
		//render to screen
		$this->template->load_admin_login_template($data);
	}
	
	public function _validate_login($password)
	{
		$data = $this->M_admin->get_member_data_by_email($this->input->post('s_email'));
		if($data)
		{
			if (password_verify($password, $data->password))
			{
				//create session here
				$login_data = array(
					'member_id'  => $data->member_id,
					'firstname'  => $data->firstname,
					'lastname'   => $data->lastname,
					'last_seen'  => $data->last_seen,
					'group'  	 => $data->member_group
				);
				$this->session->set_userdata('member', $login_data);
				
				//update last seen
				$update = $this->M_admin->update_member_last_seen($data->member_id);

				//set session token
				$this->_set_token();
				
				//set permissions
				$this->_set_permissions($data->permissions);
				
				return TRUE;
			}
		}

		$this->form_validation->set_message('_validate_login', $this->lang->line('error_login'));
		return FALSE;
	}
	
	public function _validate_reset($post)
	{
		$errors = array();
		
		if(!isset($post['r_email']))
		{
			$errors[] = $this->lang->line('error_email');
		}
		else
		{
			if(strlen($post['r_email']) > 255)
			{
				$errors[] = $this->lang->line('error_email');
			}
			elseif(!filter_var($post['r_email'], FILTER_VALIDATE_EMAIL))
			{
				$errors[] = $this->lang->line('error_email');
			}
		}
		
		$return['errors'] = $errors;
		return $return;
	}
	
	public function _validate_new($post)
	{
		$errors = array();
		
		if(!isset($post['password']) || !isset($post['password2']))
		{
			$errors[] = $this->lang->line('error_password');
		} 
		else
		{
			if($post['password'] != $post['password2'])
			{
				$errors[] = $this->lang->line('error_password');
			}
		}
		
		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
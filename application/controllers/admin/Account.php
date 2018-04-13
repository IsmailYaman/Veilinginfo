<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_account');

		$this->lang->load('admin/account', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index()
	{
		//language
		$data['site_title']  						= $this->M_config->get('site_title').' :: admin :: account';
		$data['page_header_text'] 					= $this->lang->line('page_header_text');
		$data['table_header_text'] 					= $this->lang->line('table_header_text');
		
		$data['entry_firstname_text'] 				= $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']  				= $this->lang->line('entry_lastname_text');
		$data['entry_email_text'] 					= $this->lang->line('entry_email_text');
		$data['entry_new_password_text']			= $this->lang->line('entry_new_password_text');
		$data['entry_new_password_confirm_text']	= $this->lang->line('entry_new_password_confirm_text');
		$data['entry_current_password_text']		= $this->lang->line('entry_current_password_text');

		$data['btn_submit_text']					= $this->lang->line('btn_submit_text');
		$data['input_password_text']				= $this->lang->line('input_password_text');
		$data['input_current_password_text']		= $this->lang->line('input_current_password_text');

		//hrefs
		$data['action']	= base_url().'admin/account?token=' . $this->token;

		//get data
		$account_data = $this->M_admin_account->get_account_details($this->member_data['member_id']);
		if($account_data)
		{
			//return all fields (Anti-XSS)
			foreach($account_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//submission detected
			if($this->input->method() == 'post')
			{
				//empty array
				$data['post'] = array();
				
				//return all post fields (Anti-XSS)
				foreach($this->input->post() as $n=>$v)
				{
					$data['post'][$n] = clean_output($v);
				}

				//run extended validation
				$post = $this->input->post();
				$post['member_id'] = $this->member_data['member_id'];
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//update
					$update_account = $this->M_admin_account->update_account($this->input->post(), $this->member_data['member_id']);
					if($update_account)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/account');
						
					} else {	
						//error on insert
						$data['errors'][] = $this->lang->line('text_no_insert');	
					}
					
				} else {	
					//error on field(s)
					$data['errors'] = $validate['errors'];	
				}
			}
		
		}
		
		$data['content_view'] = 'admin/admin_account_form_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function _validate($post)
	{	
		$errors = array();
			
		if(!isset($post['firstname']))
		{
			$errors[] = $this->lang->line('error_firstname');
		} else {
			if(strlen($post['firstname']) > 255)
			{
				$errors[] = $this->lang->line('error_firstname_2');
			}
		}
	
		if(!isset($post['lastname']))
		{
			$errors[] = $this->lang->line('error_lastname');
		} else {
			if(strlen($post['lastname']) > 255)
			{
				$errors[] = $this->lang->line('error_lastname_2');
			}
		}
		
		if(!isset($post['email']))
		{
			$errors[] = $this->lang->line('error_email');
		} else {
			if(strlen($post['email']) > 255)
			{
				$errors[] = $this->lang->line('error_email_2');
			}
			elseif(!filter_var($post['email'], FILTER_VALIDATE_EMAIL))
			{
				$errors[] = $this->lang->line('error_email_3');
			}
			else
			{
				$account_check = $this->M_admin_account->check_members($post['email'], $post['member_id']);
				if($account_check)
				{
					$errors[] = $this->lang->line('error_email_4');
				}
			}
		}
		
		if(isset($post['password']))
		{
			if(!isset($post['password2']) || $post['password'] != $post['password2'])
			{
				$errors[] = $this->lang->line('error_password_mismatch');
			}
		}
		
		$account = $this->M_admin_account->get_account_details($post['member_id']);
		if(!isset($post['current_password']) || !password_verify($post['current_password'], $account->password))
		{
			$errors[] = $this->lang->line('error_password_incorrect');
		}
			
		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pageform extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//load language
		$this->load->model("M_pages");
		$this->load->model("M_messages");
		
		$this->lang->load('front/form', $this->site_language);
		
		if(Is_Subdomain())
		{
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().url_alias('pageform'));
		}
		
	}
	 
	public function index()
	{

		$data['page_title'] = $this->lang->line('page_title_pageform');
		
		$data['entry_firstname_text']	 = $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']	 = $this->lang->line('entry_lastname_text');
		$data['entry_email_text']		 = $this->lang->line('entry_email_text');
		$data['entry_message_text']		 = $this->lang->line('entry_message_text');
		$data['entry_page_request_text'] = sprintf($this->lang->line('entry_page_request_text'), $this->config->item('http_host'));
		$data['entry_captcha_text']		 = $this->lang->line('entry_captcha_text');
		
		$data['input_page_request_text'] = $this->lang->line('input_page_request_text');
		$data['input_message_text'] 	 = $this->lang->line('input_message_text');
		
		$data['btn_request_text']		 = $this->lang->line('btn_request_text');
		
		$data['msg_pageform_info']		 = $this->lang->line('msg_pageform_info');
		
		if($this->input->method() == 'post')
		{
			
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			$validate = $this->_validate($this->input->post());
			
			if(count($validate['errors']) == 0)
			{
				$add_page = $this->M_pages->add_page($this->input->post());
				if($add_page)
				{
					//add message
					$message_data = array(
						"type" 		=> 1,
						"data_id"	=> $add_page,
						"firstname" => $this->input->post('firstname'),
						"lastname" 	=> $this->input->post('lastname'),
						"email" 	=> $this->input->post('email'),
						"message" 	=> $this->input->post('message')
					);

					$this->M_messages->add_message($message_data);
					
					//set message and redirect
					$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
					redirect('/'.url_alias('pageform'));
					
				} else {
					
					//error on insert
					$data['errors'][] = $this->lang->line('text_no_insert');
					
				}
				
			} else {
				
				//error on field(s)
				$data['errors'] = $validate['errors'];
				
			}
		}
		
		$data['content_view'] = 'pageform_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
	
	public function _validate($post)
	{	
		$errors = array();

		if(!isset($post['page']) || empty($post['page']))
		{
			$errors[] = $this->lang->line('error_page_empty');
			
		} else {
			
			if(!ctype_alnum($post['page']))
			{
				$errors[] = $this->lang->line('error_page_format');
				
			} else {

				$check_page = $this->M_pages->check_url($post['page']);
				
				if($check_page)
				{
					$errors[] = $this->lang->line('error_page_exist');
				}
				
			}
		}
		
		if(!isset($post['firstname']) || empty($post['firstname']))
		{
			$errors[] = $this->lang->line('error_firstname');
		}
		else
		{
			if(strlen($post['firstname']) > 255)
			{
				$errors[] = $this->lang->line('error_firstname');
			}
		}
		
		if(!isset($post['lastname']) || empty($post['lastname']))
		{
			$errors[] = $this->lang->line('error_lastname');
		}
		else
		{
			if(strlen($post['lastname']) > 255)
			{
				$errors[] = $this->lang->line('error_lastname');
			}
		}
		
		if(!isset($post['email']) || empty($post['email']))
		{
			$errors[] = $this->lang->line('error_email');
		}

		if(isset($post['email']) && !empty($post['email']))
		{
			if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL))
			{
				$errors[] = $this->lang->line('error_email');
			}
		}
		
		if(isset($post['message']))
		{
			if(strlen($post['message']) > 1000)
			{
				$errors[] = $this->lang->line('error_message');
			}
		}
		
		$check = captch_verify($post['g-recaptcha-response']);
		if(!$check->success)
		{
			$errors[] = $this->lang->line('error_captcha');
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
	
}


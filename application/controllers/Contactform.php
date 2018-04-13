<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contactform extends MY_Controller {
	
	private $page_name;
	private $page_id;

	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_pages");
		$this->load->model("M_members");
		
		$this->lang->load('front/form', $this->site_language);
		
		$this->page_name = $this->config->item('http_host');
		
		if(Is_Subdomain())
		{
			$getDomain = CurrentPage();
			$this->page_name = $getDomain['subdomain'];
			$this->page_id 	 = $getDomain['current'];
		}
	}
	 
	public function index()
	{
		$data['page_title']				 = $this->lang->line('page_title_contactform');
		$data['entry_firstname_text']	 = $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']	 = $this->lang->line('entry_lastname_text');
		$data['entry_email_text']		 = $this->lang->line('entry_email_text');
		$data['entry_message_text']		 = $this->lang->line('entry_message_text');
		$data['entry_captcha_text']		 = $this->lang->line('entry_captcha_text');
		$data['input_message_text'] 	 = $this->lang->line('input_message_text');
		$data['btn_send_text']			 = $this->lang->line('btn_send_text');
		$data['msg_contactform_info']	 = sprintf($this->lang->line('msg_contactform_info'), ucfirst($this->page_name));

		if($this->input->method() == 'post')
		{

			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			$validate = $this->_validate($this->input->post());
			
			if(count($validate['errors']) == 0)
			{
				
				//get page owner details
				$owner_id = $this->M_pages->get_page_owner($this->page_id);
				if($owner_id == 0)
				{
					$to 	   = $this->M_config->get('email_from_address');
					$firstname = $this->M_config->get('email_from_name');
					$lastname  = "";
				}
				else
				{
					$get_member_info = $this->M_members->get_member_info($owner_id);
					//$to 			 = $get_member_info->email;
					$to 			 = "ben@renaissance.nl";
					$firstname 		 = $get_member_info->firstname;
					$lastname  		 = $get_member_info->lastname;
				}

				//send email to page owner
				$params = array(
					"firstname_owner" 	=> $firstname,
					"lastname_owner" 	=> $lastname,
					"page_name" 		=> ucfirst($this->page_name),
					"firstname" 		=> $this->input->post('firstname'),
					"lastname" 			=> $this->input->post('lastname'),
					"email" 			=> $this->input->post('email'),
					"message" 			=> $this->input->post('message'),
					"site_name" 		=> $this->M_config->get('site_title'),
				);
				
				$this->_send_email($to, sprintf($this->lang->line('email_subject_contact'), $this->input->post('firstname').' '.$this->input->post('lastname')), "contact_form_mail", $params);
				
				//set message and redirect
				$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success_mail'));
				
				if(Is_Subdomain())
				{
					redirect(format_page_url($this->page_name).'/contact-opnemen');
				}
				else
				{
					redirect('/contact-opnemen');
				}
	
			} else {
				
				//error on field(s)
				$data['errors'] = $validate['errors'];
				
			}
		}

		$data['content_view'] = 'contactform_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
	
	public function _validate($post)
	{	
		$errors = array();

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
		
		if(!isset($post['message']) || empty($post['message']))
		{
			$errors[] = $this->lang->line('error_message');
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

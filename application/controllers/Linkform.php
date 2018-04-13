<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Linkform extends MY_Controller {
	
	private $page_id;
	private $page_name;

	public function __construct()
	{
		parent::__construct();
		
		//load language
		$this->lang->load('front/form', $this->site_language);
		
		$this->load->model("M_pages");
		$this->load->model("M_links");
		$this->load->model("M_messages");
		$this->load->model("M_categories");
		
		if(Is_Subdomain())
		{
			$getDomain 		 = CurrentPage();
			$this->page_id 	 = $getDomain['current'];
			$this->page_name = $getDomain['subdomain'];
		}
		
	}
	 
	public function index()
	{
		//language
		$data['entry_firstname_text']	= $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']	= $this->lang->line('entry_lastname_text');
		$data['entry_email_text']		= $this->lang->line('entry_email_text');
		$data['entry_link_text']		= $this->lang->line('entry_link_text');
		$data['entry_backlink_text']	= $this->lang->line('entry_backlink_text');
		$data['entry_page_text']		= $this->lang->line('entry_page_text');
		$data['entry_category_text']	= $this->lang->line('entry_category_text');
		$data['entry_message_text']		= $this->lang->line('entry_message_text');
		$data['entry_anchor_text']		= $this->lang->line('entry_anchor_text');
		$data['entry_captcha_text']		= $this->lang->line('entry_captcha_text');
		
		$data['input_link_text']		= $this->lang->line('input_link_text');
		$data['input_backlink_text']	= $this->lang->line('input_backlink_text');
		$data['input_message_text']		= $this->lang->line('input_message_text');
		
		$data['btn_request_text']		= $this->lang->line('btn_request_text');
		$data['input_category_text']	= $this->lang->line('input_category_text');
		$data['input_page_text']		= $this->lang->line('input_page_text');
		
		$data['page_title'] 			= $this->lang->line('page_title_linkform');
		$data['msg_linkform_info'] 		= $this->lang->line('msg_linkform_info');

		//data
		$data['pages'] 	 = $this->M_pages->get_all_pages(true);
		$data['page_id'] = $this->page_id;
		
		$data['errors'] = array();
		
		//submission detected
		if($this->input->method() == 'post')
		{
			
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			$validate = $this->_validate($this->input->post());
			
			if(count($validate['errors']) == 0)
			{
				//add link
				$add_link = $this->M_links->add_link($this->input->post());
				if($add_link)
				{
					//add message
					$message_data = array(
						"type" 		=> 2,
						"data_id"	=> $add_link,
						"firstname" => $this->input->post('firstname'),
						"lastname" 	=> $this->input->post('lastname'),
						"email" 	=> $this->input->post('email'),
						"message" 	=> $this->input->post('message')
					);

					$this->M_messages->add_message($message_data);
					
					//set message and redirect
					$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
					
					if(Is_Subdomain())
					{
						redirect(format_page_url($this->page_name).'/'.url_alias('linkform'));
					}
					else
					{
						redirect('/'.url_alias('linkform'));
					}

				} else {
					
					//error on insert
					$data['errors'][] = $this->lang->line('text_no_insert');
					
				}
				
			} else {
				
				//error on field(s)
				$data['errors'] = $validate['errors'];
				
			}
		}
		
		
		$data['content_view']	= 'linkform_v';

		
		//render to screen
		$this->template->load_default_template($data);
	}

	public function _validate($post)
	{	
		$errors = array();
		$allow_url_protocol = array('http','https');

		if(!ctype_digit($post['page_id']))
		{
			$errors[] = $this->lang->line('error_page');
			
		} else {
			
			if(!ctype_digit($post['category_id']))
			{
				$errors[] = $this->lang->line('error_category');
				
			} else {

				$check_category = $this->M_categories->validate_category($post['page_id'], $post['category_id']);
				
				if(!$check_category)
				{
					$errors[] = $this->lang->line('error_category');
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
		
		if(!isset($post['anchor']) || empty($post['anchor']))
		{
			$errors[] = $this->lang->line('error_anchor_isset');
		} 
		else 
		{
			if(strlen($post['anchor']) > 255)
			{
				$errors[] = $this->lang->line('error_anchor');
			}
		}
		
		if(!isset($post['url']) || empty($post['url']))
		{
			$errors[] = $this->lang->line('error_url');
		}
		
		if(!isset($post['backlink']) || empty($post['backlink']))
		{
			$errors[] = $this->lang->line('error_backlink');
		}

		if(isset($post['url']) && !empty($post['url']))
		{
			if(!(bool)filter_var($post['url'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) || !in_array(parse_url($post['url'], PHP_URL_SCHEME), $allow_url_protocol))
			{
				$errors[] = $this->lang->line('error_url');
			}
		}
		
		if(isset($post['backlink']) && !empty($post['backlink']))
		{
			if(!(bool)filter_var($post['backlink'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) || !in_array(parse_url($post['backlink'], PHP_URL_SCHEME), $allow_url_protocol))
			{
				$errors[] = $this->lang->line('error_backlink');
			}
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

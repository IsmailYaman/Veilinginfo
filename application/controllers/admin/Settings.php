<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_settings');
		$this->load->model('admin/M_admin_languages');

		$this->lang->load('admin/settings', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index()
	{
		//language
		$data['site_title']  					= $this->M_config->get('site_title').' :: admin :: settings';
		$data['page_header_text'] 				= $this->lang->line('page_header_text');
		$data['table_header_text'] 				= $this->lang->line('table_header_text');
		$data['entry_site_title_text'] 			= $this->lang->line('entry_site_title_text');
		$data['entry_email_from_address_text'] 	= $this->lang->line('entry_email_from_address_text');
		$data['entry_email_from_name_text'] 	= $this->lang->line('entry_email_from_name_text');
		$data['entry_site_language_text'] 		= $this->lang->line('entry_site_language_text');
		$data['btn_submit_text']				= $this->lang->line('btn_submit_text');
		$data['input_language_text']			= $this->lang->line('input_language_text');

		//data
		$data['languages']	= $this->M_admin_languages->get_all_languages(); 
	
		//hrefs
		$data['action']	= base_url().'admin/settings?token=' . $this->token;

		//get language data
		$settings_data = $this->M_admin_settings->get_settings();
		if($settings_data)
		{
			//return all fields (Anti-XSS)
			foreach($settings_data as $setting)
			{
				$data['post'][$setting->name] = clean_output($setting->value);
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
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//update
					$update_settings = $this->M_admin_settings->update_settings($this->input->post());
					if($update_settings)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/settings');
						
					} else {	
						//error on insert
						$data['errors'][] = $this->lang->line('text_no_insert');	
					}
					
				} else {	
					//error on field(s)
					$data['errors'] = $validate['errors'];	
				}
			}
		
		} else {
			$data['errors'] = array("No valid language");
		}
		
		$data['data_type'] 		= 'edit';
		$data['content_view'] 	= 'admin/admin_settings_form_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function _validate($post)
	{	
		$errors = array();

		if(!$this->_has_permission('write'))
		{
			$errors[] = $this->lang->line('error_write_permission');
		} 
		else {
			
			if(!isset($post['site_title']))
			{
				$errors[] = $this->lang->line('error_site_title');
			} else {
				if(strlen($post['site_title']) > 255)
				{
					$errors[] = $this->lang->line('error_site_title_2');
				}
			}
		
			if(!isset($post['email_from_name']))
			{
				$errors[] = $this->lang->line('error_email_from_name');
			} else {
				if(strlen($post['email_from_name']) > 255)
				{
					$errors[] = $this->lang->line('error_email_from_name_2');
				}
			}
			
			if(!isset($post['email_from_address']))
			{
				$errors[] = $this->lang->line('error_email_from_address');
			} else {
				if(strlen($post['email_from_address']) > 255)
				{
					$errors[] = $this->lang->line('error_email_from_address_2');
				}
				elseif(!filter_var($post['email_from_address'], FILTER_VALIDATE_EMAIL))
				{
					$errors[] = $this->lang->line('error_email_from_address_3');
				}
			}
			
			if(!isset($post['site_language']))
			{
				$errors[] = $this->lang->line('error_site_language');
			} else {
				if(!ctype_digit($post['site_language']))
				{
					$errors[] = $this->lang->line('error_site_language_2');
				}
				else
				{
					$check_lang = $this->M_admin_languages->check_language_exist($post['site_language']);
					if(!$check_lang)
					{
						$errors[] = $this->lang->line('error_site_language_2');
					}
				}	
			}
		}
		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
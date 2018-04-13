<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Languages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_languages');

		$this->lang->load('admin/languages', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index($page=0)
	{ 
		$search = '';
		$limit_list = 10;

		//get requested search
		if($this->input->get('search') && strlen($this->input->get('search')) > 0)
		{
			$search = $this->input->get('search');
		}
		
		//get requested limit
		if($this->input->get('limit') && ctype_digit($this->input->get('limit')) && $this->input->get('limit') < 101 && $this->input->get('limit') > 0)
		{
			$limit_list = $this->input->get('limit');
		}
		
		//search filter
		$filter = array(
			"name" 			=> ($this->input->get('filter_name') ? $this->input->get('filter_name') : ''),
			"machine_name"	=> ($this->input->get('filter_machine_name') ? $this->input->get('filter_machine_name') : ''),
			"search"		=> $search
		);

		//get count (with filter)
		$total_languages = $this->M_admin_languages->get_language_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_languages ? (($total_languages-$limit_list) >= 0 ? ($total_languages-$limit_list) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all languages (with filter and limit)
		$languages = $this->M_admin_languages->get_languages($filter, $limit);

		$data['languages'] = array();
		foreach($languages as $language)
		{
			$data['languages'][] = array(
				"language_id"	=> $language->language_id,
				"name"			=> $language->name,
				"machine_name"	=> $language->machine_name,
				"href_edit"		=> base_url().'admin/languages/edit/' . $language->language_id . '?token=' . $this->token
			);
		}
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: languages';
		$data['site_name']					= $this->M_config->get('site_title');
		$data['page_header_text']			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $filter['search'] ? sprintf($this->lang->line('table_header_search_text'), $filter['search']) : $this->lang->line('table_header_all_text');
		$data['input_search_text'] 			= $this->lang->line('input_search_text');
		$data['btn_search_text'] 			= $this->lang->line('btn_search_text');
		$data['btn_add_text'] 				= $this->lang->line('btn_add_text');
		$data['btn_edit_text'] 				= $this->lang->line('btn_edit_text');
		$data['btn_link_view_text'] 		= $this->lang->line('btn_link_view_text');
		$data['btn_filter_text'] 			= $this->lang->line('btn_filter_text');
		$data['btn_delete_selected_text'] 	= $this->lang->line('btn_delete_selected_text');
		$data['column_language_text']		= $this->lang->line('column_language_text');
		$data['column_machine_name_text']	= $this->lang->line('column_machine_name_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');

		//data		
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/languages';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_add']	= base_url().'admin/languages/add' . '?token=' . $this->token;
		$data['href_remove']	= base_url().'admin/languages/delete' . '?token=' . $this->token;
		$data['href_filter']	= base_url().'admin/languages' . '?token=' . $this->token;

		//Pagination
		$this->load->library('pagination');
		$config['base_url']		= base_url().'admin/languages/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_languages;
		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_languages_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add()
	{
		//language vars
		$data['site_title']  		= $this->M_config->get('site_title').' :: admin :: languages :: add new';
		$data['page_header_text'] 	= $this->lang->line('page_header_text');
		$data['table_header_text'] 	= $this->lang->line('table_header_new_text');
		$data['site_name'] 			= $this->M_config->get('site_title');
		$data['entry_language_text']			= $this->lang->line('entry_language_text');
		$data['entry_machine_name_text']	= $this->lang->line('entry_machine_name_text');
		$data['entry_aliases_text']	= $this->lang->line('entry_aliases_text');
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		//data
		$data['token']		  = $this->token;
		
		$aliases = $this->config->item('page_aliases');
		
		foreach($aliases as $alias)
		{
			$data['aliases'][] = array("field" => sprintf($this->lang->line('input_alias_for_text'), $alias), "name" => $alias);
		}

		//hrefs
		$data['action']	= base_url().'admin/languages/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('name', 'Language', 'trim|required');
			$this->form_validation->set_rules('machine_name', 'Machnine Name', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['language_id'] = array();
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//add language
					$add_language = $this->M_admin_languages->add_language($this->input->post());
					if($add_language)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/languages');
						
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
		
		$data['data_type'] = 'add';
		$data['content_view'] = 'admin/admin_language_form_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function delete()
	{
		if($this->_has_permission('delete'))
		{
			$removed=0;
			if($this->input->method() == 'post')
			{
				//validate id
				$language_ids = $this->input->post('language_id');
				if($language_ids && is_array($language_ids))
				{
					foreach($language_ids as $language_id)
					{
						//max number the int field can hold.
						if(ctype_digit($language_id) && $language_id > 1 && $language_id < 2147483648)
						{
							//delete module
							$removed = $this->M_admin_languages->delete_language($language_id);
						}
					}
				}
			}
		} else {
			//set message
			$this->session->set_flashdata('flash_message_error', $this->lang->line('error_delete_permission'));
		}
		
		if($removed)
		{
			//set message
			$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
		}
		
		//redirect
		$this->_go_url('admin/languages');	
	}
	
	public function edit($language_id=null)
	{
		//language vars
		$data['site_title']  		= $this->M_config->get('site_title').' :: admin :: languages :: edit';
		$data['page_header_text'] 	= $this->lang->line('page_header_text');
		$data['table_header_text'] 	= $this->lang->line('table_header_new_text');
		$data['site_name'] 			= $this->M_config->get('site_title');
		
		$data['entry_language_text']			= $this->lang->line('entry_language_text');
		$data['entry_machine_name_text']	= $this->lang->line('entry_machine_name_text');
		$data['entry_aliases_text']	= $this->lang->line('entry_aliases_text');
		
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$aliases = $this->config->item('page_aliases');
		
		foreach($aliases as $alias)
		{
			$data['aliases'][] = array("field" => sprintf($this->lang->line('input_alias_for_text'), $alias), "name" => $alias);
		}

		//data vars
		$data['token']		= $this->token;
		$data['language_id']	= $language_id;
	
		//hrefs
		$data['action']	= base_url().'admin/languages/edit/'.(int)$language_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get language data
		$language_data = $this->M_admin_languages->get_language((int)$language_id);
		if($language_data)
		{
			//return all fields (Anti-XSS)
			foreach($language_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			$alias_data = $this->M_admin_languages->get_aliases((int)$language_id);
			foreach($alias_data as $data_alias)
			{
				$data['post']['alias'][$data_alias->query] = clean_output($data_alias->keyword);
			}

			//submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('name', 'Language', 'trim|required');
				$this->form_validation->set_rules('machine_name', 'Machnine Name', 'trim|required');
				
				//empty array
				$data['post'] = array();
				
				//return all post fields (Anti-XSS)
				foreach($this->input->post() as $n=>$v)
				{
					$data['post'][$n] = clean_output($v);
				}

				if($this->form_validation->run())
				{
					//run extended validation
					$post = $this->input->post();
					$post['language_id'] = $language_id;
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit
						$edit_language = $this->M_admin_languages->edit_language($this->input->post(), $language_id);
						if($edit_language)
						{
							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/languages');
							
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
		
		} else {
			$data['errors'] = array("No valid language");
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_language_form_v';
		
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

			if(strlen($post['name']) > 255)
			{
				$errors[] = $this->lang->line('error_name');
			}
			
			if(strlen($post['machine_name']) > 255)
			{
				$errors[] = $this->lang->line('error_machine_name');
			} 
			elseif(!ctype_alnum($post['machine_name']))
			{
				$errors[] = $this->lang->line('error_machine_special');
				
			}
			elseif(!is_dir('application/language/'.$post['machine_name']))
			{
				$errors[] = $this->lang->line('error_language');
			} 
			else
			{
				$lang_check = $this->M_admin_languages->check_language($post['machine_name'], $post['language_id']);
				
				if($lang_check)
				{
					$errors[] = $this->lang->line('error_machine_exist');
				}
			}
		}

		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
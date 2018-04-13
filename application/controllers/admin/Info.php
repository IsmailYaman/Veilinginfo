<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_info');

		$this->lang->load('admin/info', $this->site_language);
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
			"title" 		=> ($this->input->get('filter_title') ? $this->input->get('filter_title') : ''),
			"text" 			=> ($this->input->get('filter_text') ? $this->input->get('filter_text') : ''),
			"description"	=> ($this->input->get('filter_description') ? $this->input->get('filter_description') : ''),
			"menu_title"	=> ($this->input->get('filter_menu') ? $this->input->get('filter_menu') : ''),
			"search"		=> $search
		);

		//get info count (with filter)
		$total_info = $this->M_admin_info->get_info_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_info ? (($total_info-$limit_list) >= 0 ? ($total_info-$limit_list) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all (with filter and limit)
		$info_pages = $this->M_admin_info->get_info_pages($filter, $limit);
		
		$this->load->helper('text');
		
		$data['info_pages'] = array();
		foreach($info_pages as $info_page)
		{
			$data['info_pages'][] = array(
				"info_id"		=> $info_page->info_id,
				"title"			=> $info_page->title,
				"text"			=> word_limiter($info_page->text, 15, '...'),
				"description"	=> word_limiter($info_page->description, 15, '...'),
				"slug"			=> $info_page->slug,
				"menu_title"	=> $info_page->menu_title,
				"active"		=> $info_page->active,
				"href_edit"		=> base_url().'admin/info/edit/' . $info_page->info_id . '?token=' . $this->token
			);
		}
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: categories';
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
		
		$data['column_description_text'] 	= $this->lang->line('column_description_text');
		$data['column_text'] 		 		= $this->lang->line('column_text');	
		$data['column_menu_title_text'] 	= $this->lang->line('column_menu_title_text');	
		$data['column_title_text'] 		 	= $this->lang->line('column_title_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		
		$data['string_homepage_text'] 	 	= $this->lang->line('string_homepage_text');
		$data['string_admin_text'] 	 		= $this->lang->line('string_admin_text');
		
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');
		$data['confirm_delete_text'] 		= $this->lang->line('confirm_delete_text');
		
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/info';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_page_edit']		= base_url().'admin/info/edit/';
		$data['href_add']			= base_url().'admin/info/add' . '?token=' . $this->token;
		$data['href_remove']		= base_url().'admin/info/delete' . '?token=' . $this->token;
		$data['href_filter']		= base_url().'admin/info' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/info/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_info;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_info_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add()
	{
		//language
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: page :: add';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_edit_text');
		$data['site_name'] 				= $this->M_config->get('site_title');
		
		$data['entry_title_text'] 		= $this->lang->line('entry_title_text');
		$data['entry_text'] 			= $this->lang->line('entry_text');
		$data['entry_description_text'] = $this->lang->line('entry_description_text');
		$data['entry_slug_text'] 		= $this->lang->line('entry_slug_text');
		$data['entry_menu_text'] 		= $this->lang->line('entry_menu_text');
		$data['entry_menutype_text'] 	= $this->lang->line('entry_menutype_text');
		$data['entry_status_text'] 		= $this->lang->line('entry_status_text');

		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$data['string_active'] 		 		= $this->lang->line('string_active');
		$data['string_inactive'] 		 	= $this->lang->line('string_inactive');
		
		$this->load->model('admin/M_admin_members');
		
		$data['menus'] = $this->M_admin_info->get_menus();

		//data
		$data['token'] = $this->token;
			
		//hrefs
		$data['action']	= base_url().'admin/info/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('title', 'Title', 'trim|required');
			$this->form_validation->set_rules('text', 'Text', 'trim|required');
			$this->form_validation->set_rules('slug', 'Slug', 'trim|required');
			$this->form_validation->set_rules('menutype', 'Menu', 'trim|required');
			$this->form_validation->set_rules('menu_title', 'Menu Title', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('active', 'Status', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['info_id'] = array();
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//add
					$add_info = $this->M_admin_info->add_info($this->input->post());
					if($add_info)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/info');
						
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
		$data['content_view'] = 'admin/admin_info_form_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function delete()
	{
		if($this->_has_permission('delete')){
			$removed=0;
			if($this->input->method() == 'post')
			{
				//validate id
				$info_ids = $this->input->post('info_id');
				if($info_ids && is_array($info_ids))
				{
					foreach($info_ids as $info_id)
					{
						//max number the int field can hold.
						if(ctype_digit($info_id) && $info_id > 1 && $info_id < 2147483648)
						{
							//delete info
							$this->M_admin_info->delete_info($info_id);
							$removed=1;
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
		$this->_go_url('admin/info');	
	}
	
	public function edit($info_id=null)
	{
		
		//language
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: page :: add';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_edit_text');
		$data['site_name'] 				= $this->M_config->get('site_title');
		
		$data['entry_title_text'] 		= $this->lang->line('entry_title_text');
		$data['entry_text'] 			= $this->lang->line('entry_text');
		$data['entry_description_text'] = $this->lang->line('entry_description_text');
		$data['entry_slug_text'] 		= $this->lang->line('entry_slug_text');
		$data['entry_menu_text'] 		= $this->lang->line('entry_menu_text');
		$data['entry_menutype_text'] 	= $this->lang->line('entry_menutype_text');
		$data['entry_status_text'] 		= $this->lang->line('entry_status_text');

		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$data['string_active'] 		 	= $this->lang->line('string_active');
		$data['string_inactive'] 		= $this->lang->line('string_inactive');
		
		$data['menus'] = $this->M_admin_info->get_menus();
		
		//data
		$data['token']		= $this->token;
		$data['info_id']	= $info_id;
	
		//hrefs
		$data['action']	= base_url().'admin/info/edit/'.(int)$info_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get data
		$info_data = $this->M_admin_info->get_info((int)$info_id);
		if($info_data)
		{
			$data['post'] = array(
				"title"			=> clean_output($info_data['title']),
				"text"			=> $info_data['text'],
				"description"	=> clean_output($info_data['description']),
				"slug"			=> clean_output($info_data['slug']),
				"menutype"		=> $info_data['menutype'],
				"menu_title"	=> clean_output($info_data['menu_title']),
				"active"		=> $info_data['active'],
			);
				
			//page submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('title', 'Title', 'trim|required');
				$this->form_validation->set_rules('text', 'Text', 'trim|required');
				$this->form_validation->set_rules('slug', 'Slug', 'trim|required');
				$this->form_validation->set_rules('menutype', 'Menu', 'trim|required');
				$this->form_validation->set_rules('menu_title', 'Menu Title', 'trim|required');
				$this->form_validation->set_rules('description', 'Description', 'trim|required');
				$this->form_validation->set_rules('active', 'Status', 'trim|required');
				
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
					$post['info_id'] = $info_id;
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit info
						$edit_info = $this->M_admin_info->edit_info($this->input->post(), $info_id);
						if($edit_info)
						{

							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/info');
							
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
			$data['errors'] = array("Not a valid info page");
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_info_form_v';
		
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

			if(strlen($post['title']) > 255)
			{
				$errors[] = $this->lang->line('error_title');
			}
			
			if(strlen($post['slug']) > 255)
			{
				$errors[] = $this->lang->line('error_slug');
			}
			else
			{
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬\s]/', $post['slug']))
				{
					$errors[] = $this->lang->line('error_slug_1');
				}
			}
			
			if(strlen($post['menu_title']) > 255)
			{
				$errors[] = $this->lang->line('error_menu_title');
			}
			
			if(!ctype_digit($post['active']) || $post['active'] > 1 || $post['active'] < 0)
			{
				$errors[] = $this->lang->line('error_status');
			}
			
			if(!isset($post['menutype']))
			{
				$errors[] = $this->lang->line('error_menu');
			}
			elseif(!ctype_digit($post['menutype']))
			{
				$errors[] = $this->lang->line('error_menu');
			}
			else
			{
				$check_menu = $this->M_admin_info->check_menu($post['menutype']);
				
				if(!$check_menu)
				{
					$errors[] = $this->lang->line('error_menu');
				}
			}
		
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
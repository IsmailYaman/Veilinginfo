<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_pages');

		$this->lang->load('admin/pages', $this->site_language);
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
			"name" 			=> ($this->input->get('filter_page') ? $this->input->get('filter_page') : ''),
			"description"	=> ($this->input->get('filter_description') ? $this->input->get('filter_description') : ''),
			"url"			=> ($this->input->get('filter_url') ? $this->input->get('filter_url') : ''),
			"search"		=> $search,
			"pages"			=> $this->_member_pages()
		);

		//only superadmins can search by member
		if($this->_is_superadmin())
		{
			$filter['member_id'] = $this->input->get('filter_member');
		}
		
		//get pages count (with filter)
		$total_pages = $this->M_admin_pages->get_pages_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_pages ? (($total_pages-$limit_list) >= 0 ? ($total_pages-$limit_list) : 0) : $this->uri->segment(3)) : 0;

		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all pages (with filter and limit)
		$pages = $this->M_admin_pages->get_pages($filter, $limit);
		
		$this->load->helper('text');
		
		$data['pages'] = array();
		foreach($pages as $page)
		{
			$data['pages'][] = array(
				"page_id"		=> $page->page_id,
				"url"			=> $page->url,
				"description"	=> word_limiter($page->description, 15, '...'),
				"name"			=> $page->name,
				"member_id"		=> $page->member_id,
				"member"		=> $page->member,
				"href_edit"		=> base_url().'admin/pages/edit/' . $page->page_id . '?token=' . $this->token,
				"href_category"	=> base_url().'admin/categories?token=' . $this->token . '&filter_page=' . $page->page_id
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
		$data['column_page_text'] 		 	= $this->lang->line('column_page_text');	
		$data['column_url_text'] 		 	= $this->lang->line('column_url_text');	
		$data['column_member_text'] 		= $this->lang->line('column_member_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		
		$data['string_homepage_text'] 	 	= $this->lang->line('string_homepage_text');
		$data['string_admin_text'] 	 		= $this->lang->line('string_admin_text');
		
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');
		$data['confirm_delete_text'] 		= $this->lang->line('confirm_delete_text');
		
		$data['msg_empty_description'] 		= $this->lang->line('msg_empty_description');

		$this->load->model('admin/M_admin_members');
		
		//data
		$data['members'] = $this->M_admin_members->get_all_members();
		
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/pages';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_add']			= base_url().'admin/pages/add' . '?token=' . $this->token;
		$data['href_remove']		= base_url().'admin/pages/delete' . '?token=' . $this->token;
		$data['href_filter']		= base_url().'admin/pages' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/pages/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_pages;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_pages_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add()
	{
		if(!$this->_is_superadmin() && !$this->_can_add_page())
		{
			$this->_go_url('admin/pages');
		}
	
		//language 
		$data['site_title']  				= $this->M_config->get('site_title').' :: admin :: page :: add';
		$data['page_header_text'] 			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $this->lang->line('table_header_edit_text');
		$data['site_name'] 					= $this->M_config->get('site_title');
		
		$data['entry_page_text'] 			= $this->lang->line('entry_page_text');
		$data['entry_url_text'] 			= sprintf($this->lang->line('entry_url_text'), $this->config->item('http_host'));
		$data['entry_member_text'] 			= $this->lang->line('entry_member_text');
		$data['entry_description_text']		= $this->lang->line('entry_description_text');
		
		$data['string_admin_text'] 			= $this->lang->line('string_admin_text');
		
		$data['input_member_text'] 			= $this->lang->line('input_member_text');
		
		$data['btn_submit_text']			= $this->lang->line('btn_submit_text');
		
		$this->load->model('admin/M_admin_members');
		
		//data
		$data['members'] = $this->M_admin_members->get_all_members();
		
		$data['token'] 		  = $this->token;
		$data['column_count'] = $this->config->item('column_count');
			
		//hrefs
		$data['action']	= base_url().'admin/pages/add/' . '?token=' . $this->token;
		
		//get all pages
		$this->load->model('admin/M_admin_pages');
		$data['pages'] = $this->M_admin_pages->get_all_pages($this->_member_pages());

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//page submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('page_name', 'Page Name', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('url', 'URL', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['page_id'] = array();
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//add page
					$add_page = $this->M_admin_pages->add_page($this->input->post(), $this->_is_superadmin());
					if($add_page)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/pages');
						
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
		
		$data['data_type'] 		= 'add';
		$data['content_view'] 	= 'admin/admin_page_form_v';
		
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
				$page_ids = $this->input->post('page_id');
				if($page_ids && is_array($page_ids))
				{
					foreach($page_ids as $page_id)
					{
						//max number the int field can hold.
						if(ctype_digit($page_id) && $page_id > 1 && $page_id < 2147483648)
						{
							//delete page
							$this->M_admin_pages->delete_page($page_id, $this->_member_pages());
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
		$this->_go_url('admin/pages');	
	}
	
	public function edit($page_id=null)
	{
		
		//language vars
		$data['site_title']  					= $this->M_config->get('site_title').' :: admin :: page :: edit';
		$data['page_header_text'] 				= $this->lang->line('page_header_text');
		$data['table_header_text'] 				= $this->lang->line('table_header_edit_text');
		$data['site_name'] 						= $this->M_config->get('site_title');
		
		$data['entry_page_text'] 				= $this->lang->line('entry_page_text');
		$data['entry_url_text'] 				= sprintf($this->lang->line('entry_url_text'), $this->config->item('http_host'));
		$data['entry_member_text'] 				= $this->lang->line('entry_member_text');
		$data['entry_description_text'] 		= $this->lang->line('entry_description_text');
		
		$data['string_admin_text'] 				= $this->lang->line('string_admin_text');
		
		$data['input_member_text'] 				= $this->lang->line('input_member_text');
		
		$data['btn_submit_text']				= $this->lang->line('btn_submit_text');
		
		$this->load->model('admin/M_admin_members');
		
		//data vars
		$data['members']	= $this->M_admin_members->get_all_members();
		$data['token']		= $this->token;
		$data['page_id']	= $page_id;
	
		//hrefs
		$data['action']	= base_url().'admin/pages/edit/'.(int)$page_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get page data
		$page_data = $this->M_admin_pages->get_page((int)$page_id, $this->_member_pages());
		if($page_data)
		{
			//return all fields (Anti-XSS)
			foreach($page_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//page submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('page_name', 'Page Name', 'trim|required');
				$this->form_validation->set_rules('description', 'Description', 'trim|required');
				
				if($this->_is_superadmin())
				{
					if(isset($page_id) && $page_id > 1 || (!isset($page_id))){
						$this->form_validation->set_rules('url', 'URL', 'trim|required');
					}
				}

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
					$post['page_id'] = $page_id;
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit page
						$edit_page = $this->M_admin_pages->edit_page($this->input->post(), $page_id, $this->_is_superadmin());
						if($edit_page)
						{

							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/pages');
							
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
			$data['errors'] = array("No valid page");
		}
		
		$data['data_type']		 = 'edit';
		$data['content_view'] 	 = 'admin/admin_page_form_v';
		
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

			if(strlen($post['page_name']) > 255)
			{
				$errors[] = $this->lang->line('error_page');
			}
			
			if(strlen($post['description']) > 500)
			{
				$errors[] = $this->lang->line('error_description');
			}
			
			//superadmin extra member_id check
			if($this->_is_superadmin())
			{
				if(isset($post['url']))
				{
					if(strlen($post['url']) > 25)
					{
						$errors[] = $this->lang->line('error_url');
						
					} elseif(!ctype_alnum($post['url']) || strpos($post['url'], " ") !== false) {
						
						$errors[] = $this->lang->line('error_url_special');
						
					} else {
						
						$this->load->model('admin/M_admin_pages');
						
						$url = $this->M_admin_pages->check_url($post['url'], $post['page_id']);
						if($url)
						{
							$errors[] = $this->lang->line('error_url_taken');
						}
					}
				}
				
				if(!isset($post['member_id']) || !ctype_digit($post['member_id']) || $post['member_id'] < 0)
				{
					$errors[] = $this->lang->line('error_member');
				} else {
					
					$this->load->model('admin/M_admin_members');
					
					$member = $this->M_admin_members->check_member($post['member_id']);
					if(!$member && $member != 0)
					{
						$errors[] = $this->lang->line('error_member');
					}
				}
			}
		
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
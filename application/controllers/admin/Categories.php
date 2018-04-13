<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		//check if member is logged in
		$this->_check_login();
		
		//load category model here because we use it alot
		$this->load->model('admin/M_admin_categories');
		
		//load language files
		$this->lang->load('admin/categories', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);

	}

	public function index($page=0)
	{
		//default empty search
		$search = '';
		
		//get requested search
		if($this->input->get('search') && strlen($this->input->get('search')) > 0)
		{
			//set new search
			$search = $this->input->get('search');
		}
		
		//default limit
		$data['limit'] = 10;
		
		//get requested limit
		if($this->input->get('limit') && ctype_digit($this->input->get('limit')) && $this->input->get('limit') < 101 && $this->input->get('limit') > 0)
		{
			//set new limit
			$data['limit'] = $this->input->get('limit');
		}
		
		//search filter
		$filter = array(
			"name" 		=> ($this->input->get('filter_category') ? $this->input->get('filter_category') : ''),
			"page_id"	=> $this->input->get('filter_page'),
			"search"	=> $search,
			"pages"		=> $this->_member_pages()
		);
		
		//get categories count (with filter)
		$total_categories = $this->M_admin_categories->get_categories_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_categories ? (($total_categories-$data['limit']) >= 0 ? ($total_categories-$data['limit']) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $data['limit']
		);

		//get all categories (with filter and limit)
		$categories = $this->M_admin_categories->get_categories($filter, $limit);
		
		$data['categories'] = array();
		
		foreach($categories as $category)
		{
			$data['categories'][] = array(
				"category_id" 	=> $category->category_id,
				"name" 			=> $category->name,
				"global" 		=> $category->global,
				"column_row" 	=> $category->column_row,
				"page_url" 		=> $category->page_url,
				"page" 			=> $category->page,
				"href_edit"		=> base_url().'admin/categories/edit/' . $category->category_id . '?token=' . $this->token,
				"href_links"	=> base_url().'admin/links?token=' . $this->token . '&filter_category=' . $category->category_id
			);
		}
		
		//get all pages
		$this->load->model('admin/M_admin_pages');
		
		//data
		$data['pages'] = $this->M_admin_pages->get_all_pages($this->_member_pages());

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
		
		$data['column_category_text'] 		= $this->lang->line('column_category_text');
		$data['column_page_text'] 			= $this->lang->line('column_page_text');	
		$data['column_action_text'] 		= $this->lang->line('column_action_text');
		$data['text_no_results'] 			= $this->lang->line('text_no_results');
		$data['string_homepage_text'] 		= $this->lang->line('string_homepage_text');
		
		$data['confirm_delete_text'] 		= $this->lang->line('confirm_delete_text');

		//data
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/categories';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		
		//hrefs
		$data['href_add']			= base_url().'admin/categories/add' . '?token=' . $this->token;
		$data['href_remove']		= base_url().'admin/categories/delete' . '?token=' . $this->token;
		$data['href_filter']		= base_url().'admin/categories' . '?token=' . $this->token;
		$data['href_links_view']	= base_url().'admin/links' . '?token=' . $this->token . '&filter_category=';
		//
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/categories/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_categories;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_categories_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add(){
		
		//language
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: categories';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_new_text');
		$data['site_name'] 	 			= $this->M_config->get('site_title');
		
		$data['btn_submit_text'] 		= $this->lang->line('btn_submit_text');
		
		$data['entry_page_text'] 		= $this->lang->line('entry_page_text');
		$data['entry_category_text'] 	= $this->lang->line('entry_category_text');
		$data['entry_column_text'] 		= $this->lang->line('entry_column_text');
		$data['entry_sort_order_text'] 	= $this->lang->line('entry_sort_order_text');

		$data['input_page_text'] 		= $this->lang->line('input_page_text');
		$data['input_category_text'] 	= $this->lang->line('input_category_text');
		
		$data['string_homepage_text'] 	= $this->lang->line('string_homepage_text');
		
		$this->load->model('admin/M_admin_pages');
		
		//data
		$data['pages']		  = $this->M_admin_pages->get_all_pages($this->_member_pages());
		$data['token'] 		  = $this->token;
		$data['column_count'] = $this->config->item('column_count');
			
		//hrefs
		$data['action']	= base_url().'admin/categories/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//category submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('page_id', 'Page', 'trim|required');
			$this->form_validation->set_rules('category_name', 'Category', 'trim|required');
			$this->form_validation->set_rules('column_row', 'Column', 'trim|required');
			$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$validate = $this->_validate($this->input->post());
				
				if(count($validate['errors']) == 0)
				{
					//add category
					$add_category = $this->M_admin_categories->add_category($this->input->post());
					if($add_category)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/categories');
						
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
		$data['content_view'] 	= 'admin/admin_category_form_v';
		
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
				$category_ids = $this->input->post('category_id');
				if($category_ids && is_array($category_ids))
				{
					foreach($category_ids as $category_id)
					{
						//max number the int field can hold.
						if(ctype_digit($category_id) && $category_id > 0 && $category_id < 2147483648)
						{
							//delete category
							$this->M_admin_categories->delete_category($category_id, $this->_member_pages());
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
		$this->_go_url('admin/categories');	
	}
	
	public function edit($category_id=null)
	{
		
		//language vars
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: category :: edit';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_edit_text');
		$data['site_name'] 				= $this->M_config->get('site_title');
		
		$data['entry_page_text'] 		= $this->lang->line('entry_page_text');
		$data['entry_category_text'] 	= $this->lang->line('entry_category_text');
		$data['entry_column_text'] 		= $this->lang->line('entry_column_text');
		$data['entry_sort_order_text'] 	= $this->lang->line('entry_sort_order_text');
		
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		$data['input_page_text'] 		= $this->lang->line('input_page_text');
		$data['input_category_text'] 	= $this->lang->line('input_category_text');
		$data['string_homepage_text'] 	= $this->lang->line('string_homepage_text');
		
		$this->load->model('admin/M_admin_pages');
		
		//data vars
		$data['pages'] 		  = $this->M_admin_pages->get_all_pages($this->_member_pages());
		$data['token']		  = $this->token;
		$data['column_count'] = $this->config->item('column_count');
			
		//hrefs
		$data['action']	= base_url().'admin/categories/edit/'.(int)$category_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get category data
		$category_data = $this->M_admin_categories->get_category((int)$category_id, $this->_member_pages());
		if($category_data)
		{
			//return all fields (Anti-XSS)
			foreach($category_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//category submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('page_id', 'Page', 'trim|required');
				$this->form_validation->set_rules('category_name', 'Category', 'trim|required');
				$this->form_validation->set_rules('column_row', 'Column', 'trim|required');
				$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required');

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
					$validate = $this->_validate($this->input->post());
					
					if(count($validate['errors']) == 0)
					{
						//edit category
						$edit_category = $this->M_admin_categories->edit_category($this->input->post(), $category_id);
						if($edit_category)
						{

							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/categories');
							
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
			$data['errors'] = array("No valid category");
		}
		
		$data['data_type'] 	  = 'edit';
		$data['content_view'] = 'admin/admin_category_form_v';
		
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

			$this->load->model('admin/M_admin_pages');
			
			if(!ctype_digit($post['page_id']))
			{
				$errors[] = $this->lang->line('error_page');
				
			} else {
				
				$page = $this->M_admin_pages->verify_page($post['page_id'], $this->_member_pages());
				if(!$page)
				{
					$errors[] = $this->lang->line('error_page');
				}
				
			}
			
			if(strlen($post['category_name']) > 255)
			{
				$errors[] = $this->lang->line('error_category');
			}

			if (!ctype_digit($post['column_row']) || $post['column_row'] < 1 || $post['column_row'] > $this->config->item('column_count'))
			{
				$errors[] = sprintf($this->lang->line('error_column_row'), $this->config->item('column_count'));
			}
			
			if ($post['sort_order'] < 0)
			{
				$errors[] = $this->lang->line('error_sort_order_1');
			}
			
			$max_sort_order = $this->M_admin_pages->get_sort_count($post['page_id'], $post['column_row'])+1;
			if ($post['sort_order'] > $max_sort_order)
			{
				$errors[] = sprintf($this->lang->line('error_sort_order_2'), $max_sort_order);
			}
		
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
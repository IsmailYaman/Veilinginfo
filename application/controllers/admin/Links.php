<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Links extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		//check if member is logged in
		$this->_check_login();
		
		//load link model here because we use it alot
		$this->load->model('admin/M_admin_links');
		
		//load language files
		$this->lang->load('admin/links', $this->site_language);
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
			"anchor" 		=> ($this->input->get('filter_anchor') ? $this->input->get('filter_anchor') : ''),
			"url" 			=> ($this->input->get('filter_url') ? $this->input->get('filter_url') : ''),
			"backlink" 		=> ($this->input->get('filter_backlink') ? $this->input->get('filter_backlink') : ''),
			"category_id" 	=> $this->input->get('filter_category'),
			"page_id" 		=> $this->input->get('filter_page'),
			"search" 		=> $search,
			"pages"			=> $this->_member_pages()
		);
		
		//get link count (with filter)
		$total_links = $this->M_admin_links->get_links_total($filter);

		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_links ? (($total_links-$data['limit']) >= 0 ? ($total_links-$data['limit']) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $data['limit']
		);

		//get all links (with filter and limit)
		$links = $this->M_admin_links->get_links($filter, $limit);
		
		$data['links'] = array();
		
		foreach($links as $link)
		{
			$data['links'][] = array(
				"link_id" 	 	=> $link->link_id,
				"anchor"	 	=> $link->anchor,
				"url" 		 	=> $link->url,
				"backlink"	 	=> $link->backlink,
				"expire_date" 	=> $link->expire_date,
				"category" 		=> $link->category,
				"page" 			=> $link->page,
				"page_url" 		=> $link->page_url,
				"href_edit" 	=> base_url().'admin/links/edit/' . $link->link_id . '?token=' . $this->token
			);
		}
		
		//get all categories
		$this->load->model('admin/M_admin_categories');
		$data['categories'] = $this->M_admin_categories->get_all_categories($this->_member_pages());
		
		//get all pages
		$this->load->model('admin/M_admin_pages');
		$data['pages'] = $this->M_admin_pages->get_all_pages($this->_member_pages());
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: links';
		$data['site_name']					= $this->M_config->get('site_title');
		$data['page_header_text']			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $filter['search'] ? sprintf($this->lang->line('table_header_search_text'), $filter['search']) : $this->lang->line('table_header_all_text');
		$data['input_search_text'] 			= $this->lang->line('input_search_text');
		
		$data['btn_search_text'] 			= $this->lang->line('btn_search_text');
		$data['btn_add_text'] 				= $this->lang->line('btn_add_text');
		$data['btn_edit_text'] 				= $this->lang->line('btn_edit_text');
		$data['btn_filter_text'] 			= $this->lang->line('btn_filter_text');
		$data['btn_delete_selected_text'] 	= $this->lang->line('btn_delete_selected_text');
		
		$data['column_anchor_text'] 		= $this->lang->line('column_anchor_text');
		$data['column_url_text'] 			= $this->lang->line('column_url_text');
		$data['column_backlink_text'] 		= $this->lang->line('column_backlink_text');
		$data['column_category_text'] 		= $this->lang->line('column_category_text');
		$data['column_page_text'] 			= $this->lang->line('column_page_text');
		$data['column_expire_text']			= $this->lang->line('column_expire_text');
		$data['column_action_text'] 		= $this->lang->line('column_action_text');
		$data['string_homepage_text'] 		= $this->lang->line('string_homepage_text');
		$data['text_no_results'] 			= $this->lang->line('text_no_results');
		$data['text_no_backlink'] 			= $this->lang->line('text_no_backlink');

		//data
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/links';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		
		//hrefs
		$data['href_add']		= base_url().'admin/links/add' . '?token=' . $this->token;
		$data['href_remove']	= base_url().'admin/links/delete' . '?token=' . $this->token;
		$data['href_filter']	= base_url().'admin/links' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/links/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_links;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_links_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add(){
		
		//language
		$data['site_title']          = $this->M_config->get('site_title').' :: admin :: links';
		$data['page_header_text']    = $this->lang->line('page_header_text');
		$data['table_header_text'] 	 = $this->lang->line('table_header_new_text');
		$data['site_name'] 			 = $this->M_config->get('site_title');
		
		$data['btn_submit_text'] 		= $this->lang->line('btn_submit_text');
		
		$data['entry_page_text'] 		= $this->lang->line('entry_page_text');
		$data['entry_category_text'] 	= $this->lang->line('entry_category_text');
		$data['entry_description_text']	= $this->lang->line('entry_description_text');
		$data['entry_anchor_text'] 		= $this->lang->line('entry_anchor_text');
		$data['entry_url_text'] 		= $this->lang->line('entry_url_text');
		$data['entry_backlink_text'] 	= $this->lang->line('entry_backlink_text');
		$data['entry_email_text'] 		= $this->lang->line('entry_email_text');
		$data['entry_no_follow_text'] 	= $this->lang->line('entry_no_follow_text');
		$data['entry_sort_order_text'] 	= $this->lang->line('entry_sort_order_text');
		$data['entry_expire_date_text'] = $this->lang->line('entry_expire_date_text');
		
		$data['input_custom_date_text'] = $this->lang->line('input_custom_date_text');
		$data['input_email_text'] 		= $this->lang->line('input_email_text');
		$data['input_backlink_text'] 	= $this->lang->line('input_backlink_text');
		$data['input_url_text'] 		= $this->lang->line('input_url_text');
		$data['input_page_text'] 		= $this->lang->line('input_page_text');
		$data['input_category_text'] 	= $this->lang->line('input_category_text');
		
		$data['string_homepage_text'] 	= $this->lang->line('string_homepage_text');
		
		//data
		$data['token'] = $this->token;
		$data['expire_list'] = array(
			"never" 	=> $this->lang->line('date_expire_never'), 
			"1day" 		=> $this->lang->line('date_expire_one_day'), 
			"1week" 	=> $this->lang->line('date_expire_one_week'), 
			"1month" 	=> $this->lang->line('date_expire_one_month'), 
			"1year" 	=> $this->lang->line('date_expire_one_year'), 
			"5years" 	=> $this->lang->line('date_expire_five_years'), 
			"10years" 	=> $this->lang->line('date_expire_ten_years'), 
			"custom" 	=> $this->lang->line('date_expire_custom'));
			
		//hrefs
		$data['action']	= base_url().'admin/links/add/' . '?token=' . $this->token;
		
		//get all pages
		$this->load->model('admin/M_admin_pages');
		$data['pages'] = $this->M_admin_pages->get_all_pages($this->_member_pages());

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//link submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('page_id', 'Page', 'trim|required');
			$this->form_validation->set_rules('category_id', 'Category', 'trim|required');
			$this->form_validation->set_rules('anchor', 'Anchor', 'trim|required');
			$this->form_validation->set_rules('url', 'url', 'trim|required');
			$this->form_validation->set_rules('email', 'email', 'trim');
			$this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
			$this->form_validation->set_rules('expire_date', 'expire_date', 'trim|required');

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
					//add link
					$add_link = $this->M_admin_links->add_link($this->input->post());
					if($add_link)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/links');
						
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
		$data['content_view'] = 'admin/admin_links_form_v';
		
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
				$link_ids = $this->input->post('link_id');
				if($link_ids && is_array($link_ids))
				{
					foreach($link_ids as $link_id)
					{
						//max number the int field can hold.
						if(ctype_digit($link_id) && $link_id > 0 && $link_id < 2147483648)
						{
							//delete link
							$removed = $this->M_admin_links->delete_link($link_id, $this->_member_pages());
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
		$this->_go_url('admin/links');	
	}
	
	public function edit($link_id=null)
	{
		
		//language vars
		$data['site_title']  		= $this->M_config->get('site_title').' :: admin :: links :: edit';
		$data['page_header_text'] 	= $this->lang->line('page_header_text');
		$data['table_header_text'] 	= $this->lang->line('table_header_edit_text');
		$data['site_name'] 			= $this->M_config->get('site_title');
		
		$data['entry_description_text']	= $this->lang->line('entry_description_text');
		$data['entry_page_text']		= $this->lang->line('entry_page_text');
		$data['entry_category_text'] 	= $this->lang->line('entry_category_text');
		$data['entry_anchor_text'] 		= $this->lang->line('entry_anchor_text');
		$data['entry_url_text'] 		= $this->lang->line('entry_url_text');
		$data['entry_backlink_text'] 	= $this->lang->line('entry_backlink_text');
		$data['entry_email_text'] 		= $this->lang->line('entry_email_text');
		$data['entry_no_follow_text'] 	= $this->lang->line('entry_no_follow_text');
		$data['entry_sort_order_text'] 	= $this->lang->line('entry_sort_order_text');
		$data['entry_expire_date_text']	= $this->lang->line('entry_expire_date_text');
		
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		$data['input_custom_date_text'] = $this->lang->line('input_custom_date_text');
		$data['input_email_text'] 		= $this->lang->line('input_email_text');
		$data['input_backlink_text'] 	= $this->lang->line('input_backlink_text');
		$data['input_url_text'] 		= $this->lang->line('input_url_text');
		$data['input_page_text'] 		= $this->lang->line('input_page_text');
		$data['input_category_text'] 	= $this->lang->line('input_category_text');
		$data['string_homepage_text'] 	= $this->lang->line('string_homepage_text');
		
		//data vars
		$data['token'] = $this->token;
		$data['expire_list'] = array(
			"never" 	=> $this->lang->line('date_expire_never'), 
			"1day" 		=> $this->lang->line('date_expire_one_day'), 
			"1week" 	=> $this->lang->line('date_expire_one_week'), 
			"1month" 	=> $this->lang->line('date_expire_one_month'), 
			"1year" 	=> $this->lang->line('date_expire_one_year'), 
			"5years" 	=> $this->lang->line('date_expire_five_years'), 
			"10years" 	=> $this->lang->line('date_expire_ten_years'), 
			"custom" 	=> $this->lang->line('date_expire_custom'));
			
		//hrefs
		$data['action']	= base_url().'admin/links/edit/'.(int)$link_id . '?token=' . $this->token;
		
		//get all pages
		$this->load->model('admin/M_admin_pages');
		$data['pages'] = $this->M_admin_pages->get_all_pages($this->_member_pages());
		
		//load form validation
		$this->load->library('form_validation');
		
		//get link data
		$link_data = $this->M_admin_links->get_link((int)$link_id, $this->_member_pages());
		if($link_data)
		{
			//return all fields (Anti-XSS)
			foreach($link_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//link submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('page_id', 'Page', 'trim|required');
				$this->form_validation->set_rules('category_id', 'Category', 'trim|required');
				$this->form_validation->set_rules('description', 'Description', 'trim|required');
				$this->form_validation->set_rules('anchor', 'Anchor', 'trim|required');
				$this->form_validation->set_rules('url', 'url', 'trim|required');
				$this->form_validation->set_rules('email', 'email', 'trim');
				$this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
				$this->form_validation->set_rules('expire_date', 'expire_date', 'trim|required');

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
						//edit link
						$edit_link = $this->M_admin_links->edit_link($this->input->post(), $link_id);
						if($edit_link)
						{

							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/links');
							
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
			$data['errors'] = array("No valid link");
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_links_form_v';
		
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
		
			$allow_url_protocol = array('http','https');
			
			$this->load->model('admin/M_admin_categories');

			if(!ctype_digit($post['page_id']))
			{
				$errors[] = $this->lang->line('error_page');
				
			} else {
				
				if(!ctype_digit($post['category_id']))
				{
					$errors[] = $this->lang->line('error_category');
					
				} else {

					$check_category = $this->M_admin_categories->validate_category($post['page_id'], $post['category_id'], $this->_member_pages());
					
					if(!$check_category)
					{
						$errors[] = $this->lang->line('error_category');
					}
					
				}
			}
			
			if(!isset($post['description']))
			{
				$errors[] = $this->lang->line('error_description');
			}
			elseif(strlen($post['description']) > 255)
			{
				$errors[] = $this->lang->line('error_description');
			}
			
			if(strlen($post['anchor']) > 255)
			{
				$errors[] = $this->lang->line('error_anchor');
			}

			if(!(bool)filter_var($post['url'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) || !in_array(parse_url($post['url'], PHP_URL_SCHEME), $allow_url_protocol))
			{
				$errors[] = $this->lang->line('error_url');
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
			
				if (isset($post['no_follow']) && (!ctype_digit($post['no_follow']) ||$post['no_follow'] < 0 || $post['no_follow'] > 1))
				{
					$errors[] = $this->lang->line('error_no_follow');
				}
			
			if ($post['sort_order'] < 0)
			{
				$errors[] = $this->lang->line('error_sort_order_1');
			}
			
			$max_sort_order = $this->M_admin_categories->get_sort_count($post['category_id']);
			if ($post['sort_order'] > $max_sort_order)
			{
				$errors[] = sprintf($this->lang->line('error_sort_order_2'), $max_sort_order);
			}

			if ($post['expire_date'] == "custom")
			{
				if (!isset($post['custom_expire_date']) || empty($post['custom_expire_date']))
				{
					$errors[] = $this->lang->line('error_custom_date_1');
				}
				
				if (strtotime($post['custom_expire_date']) < time())
				{
					$errors[] = $this->lang->line('error_custom_date_2');
				}

			}
		
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Module extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_module');

		$this->lang->load('admin/module', $this->site_language);
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

		//get module count (with filter)
		$total_modules = $this->M_admin_module->get_module_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_modules ? (($total_modules-$limit_list) >= 0 ? ($total_modules-$limit_list) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all modules (with filter and limit)
		$modules = $this->M_admin_module->get_modules($filter, $limit);
		
		$this->load->helper('text');
		
		$data['modules'] = array();
		foreach($modules as $module)
		{
			$data['modules'][] = array(
				"module_id"		=> $module->module_id,
				"name"			=> $module->name,
				"machine_name"	=> $module->machine_name,
				"href_edit"		=> base_url().'admin/module/edit/' . $module->module_id . '?token=' . $this->token
			);
		}
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: modules';
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
		$data['column_name_text']			= $this->lang->line('column_name_text');
		$data['column_machine_name_text']	= $this->lang->line('column_machine_name_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');

		//data		
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/module';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_add']		= base_url().'admin/module/add' . '?token=' . $this->token;
		$data['href_remove']	= base_url().'admin/module/delete' . '?token=' . $this->token;
		$data['href_filter']	= base_url().'admin/module' . '?token=' . $this->token;

		//Pagination
		$this->load->library('pagination');
		$config['base_url']		= base_url().'admin/module/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_modules;
		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_modules_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add()
	{
		//language
		$data['site_title']  				= $this->M_config->get('site_title').' :: admin :: module :: install';
		$data['page_header_text'] 			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $this->lang->line('table_header_new_text');
		$data['site_name'] 					= $this->M_config->get('site_title');
		$data['entry_name_text']			= $this->lang->line('entry_name_text');
		$data['entry_machine_name_text']	= $this->lang->line('entry_machine_name_text');
		$data['entry_column_text']			= $this->lang->line('entry_column_text');
		$data['entry_sort_order_text']		= $this->lang->line('entry_sort_order_text');
		$data['btn_submit_text']			= $this->lang->line('btn_submit_text');
		
		//data
		$data['token']		  = $this->token;
		$data['column_count'] = $this->config->item('column_count');
			
		//hrefs
		$data['action']	= base_url().'admin/module/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('name', 'Module Name', 'trim|required');
			$this->form_validation->set_rules('machine_name', 'Module Name', 'trim|required');
			$this->form_validation->set_rules('column_row', 'Column', 'trim|required');
			$this->form_validation->set_rules('sort_order', 'Sort', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['module_id'] = array();
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//add module
					$add_module = $this->M_admin_module->add_module($this->input->post());
					if($add_module)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/module');
						
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
		$data['content_view'] = 'admin/admin_module_form_v';
		
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
				$module_ids = $this->input->post('module_id');
				if($module_ids && is_array($module_ids))
				{
					foreach($module_ids as $module_id)
					{
						//max number the int field can hold.
						if(ctype_digit($module_id) && $module_id > 0 && $module_id < 2147483648)
						{
							//delete module
							$removed = $this->M_admin_module->delete_module($module_id);
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
		$this->_go_url('admin/module');	
	}
	
	public function edit($module_id=null)
	{
		//language
		$data['site_title']  				= $this->M_config->get('site_title').' :: admin :: module :: install';
		$data['page_header_text'] 			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $this->lang->line('table_header_new_text');
		$data['site_name'] 					= $this->M_config->get('site_title');
		
		$data['entry_name_text']			= $this->lang->line('entry_name_text');
		$data['entry_machine_name_text']	= $this->lang->line('entry_machine_name_text');
		$data['entry_column_text']			= $this->lang->line('entry_column_text');
		$data['entry_sort_order_text']		= $this->lang->line('entry_sort_order_text');
		
		$data['btn_submit_text']			= $this->lang->line('btn_submit_text');

		//data
		$data['token']		= $this->token;
		$data['module_id']	= $module_id;
		
		$data['column_count'] = $this->config->item('column_count');
	
		//hrefs
		$data['action']	= base_url().'admin/module/edit/'.(int)$module_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get module data
		$module_data = $this->M_admin_module->get_module((int)$module_id);
		if($module_data)
		{
			//return all fields (Anti-XSS)
			foreach($module_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('name', 'Module Name', 'trim|required');
				$this->form_validation->set_rules('machine_name', 'Module Name', 'trim|required');
				$this->form_validation->set_rules('column_row', 'Column', 'trim|required');
				$this->form_validation->set_rules('sort_order', 'Sort', 'trim|required');
				
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
					$post['module_id'] = $module_id;
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit module
						$edit_module = $this->M_admin_module->edit_module($this->input->post(), $module_id);
						if($edit_module)
						{
							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/module');
							
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
			$data['errors'] = array("No valid module");
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_module_form_v';
		
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
			elseif(!is_dir('application/modules/'.$post['machine_name']) || in_array($post['machine_name'],$this->config->item('core_modules')) )
			{
				$errors[] = $this->lang->line('error_module');
			} 
			else
			{
				$module_check = $this->M_admin_module->check_machine_name($post['machine_name'], $post['module_id']);
				
				if($module_check)
				{
					$errors[] = $this->lang->line('error_machine_exist');
				}
			}

			if (!ctype_digit($post['column_row']) || $post['column_row'] < 1 || $post['column_row'] > $this->config->item('column_count'))
			{
				$errors[] = sprintf($this->lang->line('error_column_row'), $this->config->item('column_count'));
			}
			
			if ($post['sort_order'] < 0)
			{
				$errors[] = $this->lang->line('error_sort_order');
			}
		}

		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
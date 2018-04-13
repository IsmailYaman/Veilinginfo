<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_group extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_members');

		$this->lang->load('admin/members', $this->site_language);
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
			"name"		=> ($this->input->get('filter_name') ? $this->input->get('filter_name') : ''),
			"search"		=> $search
		);
		
		//only superadmins can search by member
		if($this->_is_superadmin())
		{
			$filter['member_id'] = $this->input->get('filter_member');
		}
		
		//get group count (with filter)
		$total_groups = $this->M_admin_members->get_groups_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(4)) ? ($this->uri->segment(4) > $total_groups ? (($total_groups-$limit_list) >= 0 ? ($total_groups-$limit_list) : 0) : $this->uri->segment(4)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all groups (with filter and limit)
		$member_groups = $this->M_admin_members->get_member_groups($filter, $limit);

		$data['groups'] = array();
		foreach($member_groups as $member_group)
		{
			$data['groups'][] = array(
				"member_group_id"	=> $member_group->member_group_id,
				"name"				=> $member_group->name,
				"href_edit"			=> base_url().'admin/member/group/edit/'. $member_group->member_group_id . '?token=' . $this->token
			);
		}
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: member groups';
		$data['site_name']					= $this->M_config->get('site_title');
		$data['page_header_text']			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $filter['search'] ? sprintf($this->lang->line('table_header_group_search_text'), $filter['search']) : $this->lang->line('table_header_all_group_text');
		$data['input_search_text'] 			= $this->lang->line('input_search_text');
		
		$data['btn_search_text'] 			= $this->lang->line('btn_search_text');
		$data['btn_add_group_text'] 		= $this->lang->line('btn_add_group_text');
		$data['btn_edit_text'] 				= $this->lang->line('btn_edit_text');
		$data['btn_filter_text'] 			= $this->lang->line('btn_filter_text');
		$data['btn_delete_selected_text'] 	= $this->lang->line('btn_delete_selected_text');
		
		$data['column_group_text'] 			= $this->lang->line('column_group_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');
		$data['confirm_delete_text'] 		= $this->lang->line('confirm_delete_text');

		$this->load->model('admin/M_admin_members');
		
		//data	
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/member/group';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_add']			= base_url().'admin/member/group/add' . '?token=' . $this->token;
		$data['href_remove']		= base_url().'admin/member/group/delete' . '?token=' . $this->token;
		$data['href_filter']		= base_url().'admin/member/group' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/member/group';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_groups;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_member_groups_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function add()
	{
		//language
		$data['site_title']  				= $this->M_config->get('site_title').' :: admin :: member group :: add';
		$data['page_header_text'] 			= $this->lang->line('page_header_text');
		$data['table_header_text']		 	= $this->lang->line('table_header_new_group_text');
		$data['site_name']					= $this->M_config->get('site_title');
		
		$data['entry_group_name_text']		= $this->lang->line('entry_group_name_text');
		$data['entry_group_moderator_text']	= $this->lang->line('entry_group_moderator_text');
		
		$data['string_yes']					= $this->lang->line('string_yes');
		$data['string_no']					= $this->lang->line('string_no');

		$data['btn_submit_text']			= $this->lang->line('btn_submit_text');
		
		//data
		$data['token'] 			 = $this->token;
		$data['permission_list'] = $this->config->item('permission_functions');
			
		//hrefs
		$data['action']	= base_url().'admin/member/group/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//group submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('moderator', 'Moderator', 'trim|required');

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['member_id'] = array();
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					
					$permission['read'] = array();
					$permission['write'] = array();
					$permission['delete'] = array();
					
					if($this->input->post('read')){
						$permission['read'] = $this->input->post('read');
						$permission['read'][] = 'dashboard';
					}
					
					if($this->input->post('write')){
						$permission['write'] = $this->input->post('write');
						$permission['write'][] = 'dashboard';
					}
					
					if($this->input->post('delete')){
						$permission['delete'] = $this->input->post('delete');
						$permission['delete'][] = 'dashboard';
					}

					$post_data = array(
						"name" 			=> $this->input->post('name'),
						"moderator"		=> $this->input->post('moderator'),
						"permissions" 	=> serialize($permission)
					);

					//add group
					$add_group = $this->M_admin_members->add_group($post_data);
					if($add_group)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/member/group');
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
		$data['content_view'] = 'admin/admin_member_group_form_v';
		
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
				$group_ids = $this->input->post('member_group_id');
				if($group_ids && is_array($group_ids))
				{
					foreach($group_ids as $group_id)
					{
						//max number the int field can hold.
						if(ctype_digit($group_id) && $group_id > 0 && $group_id < 2147483648)
						{
							//delete member
							$check = $this->M_admin_members->check_group($group_id);
							if($check)
							{
								$removed = $this->M_admin_members->delete_group($group_id);
							} else {
								$this->session->set_flashdata('flash_message_error', sprintf($this->lang->line('error_group_members'),$group_id));
							}
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
		$this->_go_url('admin/member/group');	
	}
	
	public function edit($group_id=null)
	{
		
		//language
		$data['site_title']  				= $this->M_config->get('site_title').' :: admin :: member group :: add';
		$data['page_header_text'] 			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $this->lang->line('table_header_new_group_text');
		$data['site_name'] 					= $this->M_config->get('site_title');
		
		$data['entry_group_name_text']		= $this->lang->line('entry_group_name_text');
		$data['entry_group_moderator_text']	= $this->lang->line('entry_group_moderator_text');
		
		$data['string_yes']					= $this->lang->line('string_yes');
		$data['string_no']					= $this->lang->line('string_no');
		
		$data['btn_submit_text']			= $this->lang->line('btn_submit_text');
		
		//data
		$data['token'] 			 = $this->token;
		$data['permission_list'] = $this->config->item('permission_functions');
			
		//hrefs
		$data['action']	= base_url().'admin/member/group/edit/'.(int)$group_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get group data
		$group_data = $this->M_admin_members->get_group((int)$group_id);
		if($group_data)
		{
			//return all fields (Anti-XSS)
			foreach($group_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v, TRUE);
			}

			//submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('name', 'Name', 'trim|required');
				$this->form_validation->set_rules('moderator', 'Moderator', 'trim|required');

				//empty array
				$data['post'] = array();
				
				//return all post fields (Anti-XSS)
				$permission_post = array();
				foreach($this->input->post() as $n=>$v)
				{
					if($n == 'read' || $n == 'write' || $n == 'delete'){
						$data['post']['permissions'][$n] = clean_output($v);
					} else {
						$data['post'][$n] = clean_output($v);
					}
					
				}

				if($this->form_validation->run())
				{
					//run extended validation
					$validate = $this->_validate($this->input->post());
					
					if(count($validate['errors']) == 0)
					{
						$permission['read'] = array();
						$permission['write'] = array();
						$permission['delete'] = array();
						
						if($this->input->post('read')){
							$permission['read'] = array_unique($this->input->post('read'));
							$permission['read'][] = 'dashboard';
						}
						
						if($this->input->post('write')){
							$permission['write'] = array_unique($this->input->post('write'));
							$permission['write'][] = 'dashboard';
						}
						
						if($this->input->post('delete')){
							$permission['delete'] = array_unique($this->input->post('delete'));
							$permission['delete'][] = 'dashboard';
						}

						$post_data = array(
							"name" 			=> $this->input->post('name'),
							"moderator"		=> $this->input->post('moderator'),
							"permissions" 	=> serialize($permission)
						);
						
						//edit group
						$edit_group = $this->M_admin_members->edit_group($post_data, $group_id);
						if($edit_group)
						{
							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/member/group');
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
		
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_member_group_form_v';
		
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
				$errors[] = $this->lang->line('error_firstname');
			}
			
			if($post['moderator'] > 1 || $post['moderator'] < 0)
			{
				$errors[] = $this->lang->line('error_moderator');
			}

			if(isset($post['read'])){
				$permission = '';
				foreach($post['read'] as $permission)
				{
					if(!in_array($permission, $this->config->item('permission_functions')))
					{
						$errors[] = $this->lang->line('error_permission_function');
						break;
					}
				}
			}
			
			if(isset($post['write'])){
				$permission = '';
				foreach($post['write'] as $permission)
				{
					if(!in_array($permission, $this->config->item('permission_functions')))
					{
						$errors[] = $this->lang->line('error_permission_function');
						break;
					}
				}
			}
			
			if(isset($post['delete'])){
				$permission = '';
				foreach($post['delete'] as $permission)
				{
					if(!in_array($permission, $this->config->item('permission_functions')))
					{
						$errors[] = $this->lang->line('error_permission_function');
						break;
					}
				}
			}
			
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
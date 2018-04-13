<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_list extends MY_Controller {

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
			"firstname"		=> ($this->input->get('filter_firstname') ? $this->input->get('filter_firstname') : ''),
			"lastname"		=> ($this->input->get('filter_lastname') ? $this->input->get('filter_lastname') : ''),
			"email"			=> ($this->input->get('filter_email') ? $this->input->get('filter_email') : ''),
			"search"		=> $search
		);

		//get member count (with filter)
		$total_members = $this->M_admin_members->get_members_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(4)) ? ($this->uri->segment(4) > $total_members ? (($total_members-$limit_list) >= 0 ? ($total_members-$limit_list) : 0) : $this->uri->segment(4)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all members (with filter and limit)
		$members = $this->M_admin_members->get_members($filter, $limit);

		$data['members'] = array();
		foreach($members as $member)
		{
			$data['members'][] = array(
				"member_id"	=> $member->member_id,
				"firstname"	=> $member->firstname,
				"lastname"	=> $member->lastname,
				"email"		=> $member->email,
				"last_seen"	=> date($this->lang->line('date_last_access_format'), $member->last_seen),
				"href_edit"	=> base_url().'admin/member/list/edit/'. $member->member_id . '?token=' .$this->token,
				"href_category"	=> base_url().'admin/pages?token=' .$this->token.'&filter_member=' . $member->member_id
			);
		}
		
		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: members';
		$data['site_name']					= $this->M_config->get('site_title');
		$data['page_header_text']			= $this->lang->line('page_header_text');
		$data['table_header_text'] 			= $filter['search'] ? sprintf($this->lang->line('table_header_search_text'), $filter['search']) : $this->lang->line('table_header_all_text');
		$data['input_search_text'] 			= $this->lang->line('input_search_text');
		
		$data['btn_search_text'] 			= $this->lang->line('btn_search_text');
		$data['btn_add_text'] 				= $this->lang->line('btn_add_text');
		$data['btn_edit_text'] 				= $this->lang->line('btn_edit_text');
		$data['btn_page_view_text'] 		= $this->lang->line('btn_page_view_text');
		$data['btn_filter_text'] 			= $this->lang->line('btn_filter_text');
		$data['btn_delete_selected_text'] 	= $this->lang->line('btn_delete_selected_text');
		
		$data['column_firstname_text'] 		= $this->lang->line('column_firstname_text');
		$data['column_lastname_text'] 		= $this->lang->line('column_lastname_text');	
		$data['column_email_text'] 		 	= $this->lang->line('column_email_text');		
		$data['column_last_active_text']	= $this->lang->line('column_last_active_text');		
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		
		$data['string_homepage_text'] 	 	= $this->lang->line('string_homepage_text');
		$data['string_admin_text'] 	 		= $this->lang->line('string_admin_text');
		
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');
		$data['confirm_delete_text'] 		= $this->lang->line('confirm_delete_text');

		$this->load->model('admin/M_admin_members');
		
		//data	
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['action_filter']	= base_url().'admin/member/list';
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		
		//hrefs
		$data['href_add']		= base_url().'admin/member/list/add' . '?token=' . $this->token;
		$data['href_remove']	= base_url().'admin/member/list/delete' . '?token=' . $this->token;
		$data['href_filter']	= base_url().'admin/member/list' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/member/list';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_members;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_members_list_v';
		
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
		
		$data['entry_firstname_text']	= $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']	= $this->lang->line('entry_lastname_text');
		$data['entry_email_text']		= $this->lang->line('entry_email_text');
		$data['entry_group_text']		= $this->lang->line('entry_group_text');

		$data['input_group_text'] 		= $this->lang->line('input_group_text');
		
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		//data
		$data['member_groups'] = $this->M_admin_members->get_all_member_groups();
		
		//hrefs
		$data['action']	= base_url().'admin/member/list/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//member submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('firstname', 'Firstname', 'trim|required');
			$this->form_validation->set_rules('lastname', 'Lastname', 'trim|required');
			$this->form_validation->set_rules('email', 'E-mail', 'trim|required');
			$this->form_validation->set_rules('member_group_id', 'Group', 'trim|required');

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
					//add member
					$add_member = $this->M_admin_members->add_member($this->input->post());
					if($add_member)
					{
						$this->_reset_member($add_member);
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/member/list');
						
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
		
		$data['content_view'] = 'admin/admin_member_form_v';
		
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
				$member_ids = $this->input->post('member_id');
				if($member_ids && is_array($member_ids))
				{
					foreach($member_ids as $member_id)
					{
						//max number the int field can hold.
						if(ctype_digit($member_id) && $member_id > 0 && $member_id < 2147483648)
						{
							//delete member
							$this->M_admin_members->delete_member($member_id);
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
		$this->_go_url('admin/member/list');	
	}
	
	public function edit($member_id=null)
	{
		//language
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: member :: edit';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_edit_text');
		$data['site_name'] 				= $this->M_config->get('site_title');
		
		$data['entry_firstname_text']	= $this->lang->line('entry_firstname_text');
		$data['entry_lastname_text']	= $this->lang->line('entry_lastname_text');
		$data['entry_email_text']		= $this->lang->line('entry_email_text');
		$data['entry_group_text']		= $this->lang->line('entry_group_text');
		
		$data['input_group_text'] 		= $this->lang->line('input_group_text');
		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$this->load->model('admin/M_admin_members');
		
		//data
		$data['member_groups'] = $this->M_admin_members->get_all_member_groups();
			
		//hrefs
		$data['action']	= base_url().'admin/member/list/edit/'.(int)$member_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get member data
		$member_data = $this->M_admin_members->get_member((int)$member_id);
		if($member_data)
		{
			//return all fields (Anti-XSS)
			foreach($member_data as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}

			//member submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('firstname', 'Firstname', 'trim|required');
				$this->form_validation->set_rules('lastname', 'Lastname', 'trim|required');
				$this->form_validation->set_rules('email', 'E-mail', 'trim|required');
				$this->form_validation->set_rules('member_group_id', 'Group', 'trim|required');

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
					$post['member_id'] = $member_id;
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit member
						$edit_member = $this->M_admin_members->edit_member($this->input->post(), $member_id);
						if($edit_member)
						{
							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/member/list');
							
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
		
		$data['content_view'] = 'admin/admin_member_form_v';
		
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

			if(strlen($post['firstname']) > 255)
			{
				$errors[] = $this->lang->line('error_firstname');
			}
			
			if(strlen($post['lastname']) > 255)
			{
				$errors[] = $this->lang->line('error_lastname');
			}
			
			if(strlen($post['email']) > 255)
			{
				$errors[] = $this->lang->line('error_email');
				
			} elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
				
				$errors[] = $this->lang->line('error_email2');
				
			} else {

				$email = $this->M_admin_members->check_email($post['email'], $post['member_id']);
				if($email)
				{
					$errors[] = $this->lang->line('error_email3');
				}
			}
		}

		$return['errors'] = $errors;
		return $return;
	}
}
	
?>
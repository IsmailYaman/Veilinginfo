<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_messages');

		$this->lang->load('admin/messages', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index($page=0, $status=1, $call=FALSE)
	{
		if(!$call)
		{
			$this->_go_url('admin/messages/open');
		}
		
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
			"type"		=> ($this->input->get('filter_type') ? $this->input->get('filter_type') : ''),
			"name"		=> ($this->input->get('filter_name') ? $this->input->get('filter_name') : ''),
			"email"		=> ($this->input->get('filter_email') ? $this->input->get('filter_email') : ''),
			"search"	=> $search
		);

		//get message count (with filter)
		$total_messages = $this->M_admin_messages->get_messages_total($filter, $status, $this->_member_pages(false, true), $this->_is_superadmin());
	
		//get offset for pagination
		$offset = ($this->uri->segment(4)) ? ($this->uri->segment(4) > $total_messages ? (($total_messages-$limit_list) >= 0 ? ($total_messages-$limit_list) : 0) : $this->uri->segment(4)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all messages (with filter and limit)
		$messages = $this->M_admin_messages->get_messages($filter, $limit, $status, $this->_member_pages(false, true), $this->_is_superadmin());
		
		$this->load->helper('text');
		
		$data['messages'] = array();
		if($messages)
		{
			foreach($messages as $message)
			{
				$data['messages'][] = array(
					"data_id"	=> $message['data_id'],
					"message_id"	=> $message['message_id'],
					"firstname"		=> $message['firstname'],
					"lastname"		=> $message['lastname'],
					"email"			=> $message['email'],
					"message"		=> word_limiter($message['message'], 15, '...'),
					"message_full"	=> $message['message'],
					"type"			=> $message['type'],
					"data_link"		=> $message['data_link'],
					"data_name"		=> $message['data_name'],
					"href_approve"	=> base_url().'admin/messages/approve/' . $message['message_id'] . '?token=' . $this->token
				);
			}
		}

		//language
		$data['site_title']					= $this->M_config->get('site_title').' :: admin :: messages';
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
		$data['btn_approve_text']			= $this->lang->line('btn_approve_text');
		$data['btn_read_more_text'] 		= $this->lang->line('btn_read_more_text');
		$data['btn_view_text'] 				= $this->lang->line('btn_view_text');
		
		$data['column_name_text']			= $this->lang->line('column_name_text');
		$data['column_email_text']			= $this->lang->line('column_email_text');	
		$data['column_message_text']		= $this->lang->line('column_message_text');	
		$data['column_type_text']			= $this->lang->line('column_type_text');	
		$data['column_data_name_text']		= $this->lang->line('column_data_name_text');	
		$data['column_data_url_text']		= $this->lang->line('column_data_url_text');	
		$data['column_action_text'] 	 	= $this->lang->line('column_action_text');
		
		$data['text_page']					= $this->lang->line('text_page');
		$data['text_link']					= $this->lang->line('text_link');
		$data['text_contact']				= $this->lang->line('text_contact');
		
		$data['text_no_results'] 		 	= $this->lang->line('text_no_results');
		$data['string_no_message'] 		 	= $this->lang->line('string_no_message');
		$data['string_anchor'] 		 		= $this->lang->line('string_anchor');
		$data['string_link'] 		 		= $this->lang->line('string_link');
		$data['string_backlink'] 		 	= $this->lang->line('string_backlink');
		$data['string_place_on'] 		 	= $this->lang->line('string_place_on');
		$data['string_page'] 		 		= $this->lang->line('string_page');
		$data['string_page_name'] 		 	= $this->lang->line('string_page_name');
		$data['string_page_url'] 		 	= $this->lang->line('string_page_url');
		$data['string_category'] 		 	= $this->lang->line('string_category');

		//data		
		$data['limit_list']		= array(10, 15, 25, 50, 100);									
		$data['token']			= $this->token;
		$data['filter']  		= $filter;
		$data['limit'] 			= $limit_list;
		$data['status'] 		= $status;
		
		//hrefs
		$data['href_remove']	= base_url().'admin/messages/delete' . '?token=' . $this->token;

		//Pagination
		$this->load->library('pagination');
		
		if($status)
		{
			$data['action_filter']	= base_url().'admin/messages/archive';
			$config['base_url']		= base_url().'admin/messages/archive';
			$data['href_filter']	= base_url().'admin/messages/archive' . '?token=' . $this->token;
		}
		else
		{		
			$config['base_url']		= base_url().'admin/messages/open';
			$data['action_filter']	= base_url().'admin/messages/open';
			$data['href_filter']	= base_url().'admin/messages/open' . '?token=' . $this->token;
		}
		
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_messages;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_messages_list_v';
		
		//render to screen
		$this->template->load_admin_template($data);
	}
	
	public function open($page=0)
	{
		$this->index($page, 0, TRUE);
	}
	
	public function archive($page=0)
	{
		$this->index($page, 1, TRUE);
	}
	
	public function delete()
	{
		if($this->_has_permission('delete')){
			$removed=0;
			if($this->input->method() == 'post')
			{
				//validate id
				$message_ids = $this->input->post('message_id');
				if($message_ids && is_array($message_ids))
				{
					foreach($message_ids as $message_id)
					{
						//max number the int field can hold.
						if(ctype_digit($message_id) && $message_id > 1 && $message_id < 2147483648)
						{
							//get message info
							$message = $this->M_admin_messages->get_message($message_id, $this->_member_pages(false, true), $this->_is_superadmin());
							
							if($message)
							{
								if($message['status'] == 0)
								{
									//delete link or page
									if($message['type'] == 1)
									{
										//delete page
										$this->load->model('admin/M_admin_pages');
										$this->M_admin_pages->delete_page($message['data_id'], 'all');
									}
									
									if($message['type'] == 2)
									{
										//delete link
										$this->load->model('admin/M_admin_links');
										$this->M_admin_links->delete_link($message['data_id'], 'all');
									}
								}
								
								//delete message
								$removed = $this->M_admin_messages->delete_message($message_id);
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
		$this->_go_url('admin/messages/open');	
	}
	
	public function approve($message_id=0)
	{
		
		if(!$this->_has_permission('write'))
		{
			$this->session->set_flashdata('flash_message_error', $this->lang->line('error_write_permission'));
		} 
		else {
			
			$message = $this->M_admin_messages->get_message($message_id, $this->_member_pages(false, true), $this->_is_superadmin());

			if($message)
			{
				//check what data has been approved
				if($message['type'] == 1) //page
				{
					$this->load->model('admin/M_admin_members');
					$check = $this->M_admin_members->check_email($message['email']);
					if($check)
					{
						//member exists
						$member = $this->M_admin_members->get_member_by_email($message['email']);
						$member_id = $member['member_id'];
					} 
					else 
					{
						$message['member_group_id'] = $this->config->item('default_member_group'); //default group
						$member_id = $this->M_admin_members->add_member($message);
						
						$this->_reset_member($member_id);
					}
				
					$this->load->model('admin/M_admin_pages');
					$page = $this->M_admin_pages->get_page($message['data_id'], $this->_member_pages(false, true));

					$params = array(
						"firstname" => $message['firstname'],
						"lastname"	=> $message['lastname'],
						"page_name" => $page['page_name'],
						"page_link" => format_page_url($page['url']),
						"site_name" => $this->M_config->get('site_title')
					);
					
					//send page approved mail
					$this->_send_email($message['email'], sprintf($this->lang->line('mail_subject_page_approve'), $this->M_config->get('site_title')), "page_approve_mail", $params);
				
					//assign page to member
					$p_data = array(
						"url" 			=> $page['url'],
						"description" 	=> "",
						"page_name" 	=> $page['page_name'],
						"member_id" 	=> $member_id,
						"status" 		=> 1
					);
					$this->M_admin_pages->edit_page($p_data, $message['data_id'], TRUE);
					
					//update message
					$this->M_admin_messages->approve_message($message_id);

					$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
				}
				
				//No member account created for link request.
				if($message['type'] == 2) //link
				{
					$this->load->model('admin/M_admin_links');
					$link = $this->M_admin_links->get_link($message['data_id'], $this->_member_pages(false, true));

					$params = array(
						"firstname" => $message['firstname'],
						"lastname"	=> $message['lastname'],
						"anchor" 	=> $link['anchor'],
						"page_link" => format_page_url($link['page_url']),
						"site_name" => $this->M_config->get('site_title')
					);
					
					//send page approved mail
					$this->_send_email($message['email'], sprintf($this->lang->line('mail_subject_link_approve'), $link['page_name']), "link_approve_mail", $params);
				
					//update link email
					$l_data = array(
						"category_id" 	=> $link['category_id'],
						"anchor" 		=> $link['anchor'],
						"url" 			=> $link['url'],
						"backlink" 		=> $link['backlink'],
						"no_follow" 	=> $link['no_follow'],
						"email" 		=> $message['email'],
						"sort_order" 	=> $link['sort_order'],
						"status" 		=> 1,
						"premium" 		=> 0,
						"expire_date" 	=> "1year"
					);
					
					$this->M_admin_links->edit_link($l_data, $message['data_id'], TRUE);
					
					//update message
					$this->M_admin_messages->approve_message($message_id);
					
					$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
				}

			}
		}
		
		//redirect
		$this->_go_url('admin/messages/open');		
	}
	
}
	
?>
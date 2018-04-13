<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Spotlight extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_spotlight');

		$this->lang->load('admin/spotlight', $this->site_language);
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
			"body" 			=> ($this->input->get('filter_body') ? $this->input->get('filter_body') : ''),
			"link"			=> ($this->input->get('filter_link') ? $this->input->get('filter_link') : ''),
			"search"		=> $search
		);

		//get spotlight count (with filter)
		$total_spotlight = $this->M_admin_spotlight->get_spotlight_total($filter);
	
		//get offset for pagination
		$offset = ($this->uri->segment(3)) ? ($this->uri->segment(3) > $total_spotlight ? (($total_spotlight-$limit_list) >= 0 ? ($total_spotlight-$limit_list) : 0) : $this->uri->segment(3)) : 0;
		
		//set limit array for model
		$limit = array(
			"start"  => $offset,
			"max"	 => $limit_list
		);

		//get all (with filter and limit)
		$spotlight_cards = $this->M_admin_spotlight->get_spotlight_cards($filter, $limit);
		
		$this->load->helper('text');
		
		$data['spotlight_cards'] = array();
		foreach($spotlight_cards as $spotlight_card)
		{
			$data['spotlight_cards'][] = array(
				"spotlight_id"		=> $spotlight_card->spotlight_id,
				"title"			=> $spotlight_card->title,
				"body"			=> word_limiter($spotlight_card->body, 15, '...'),
				"media"			=> $spotlight_card->media,
				"link"			=> $spotlight_card->link,
				"nofollow"		=> $spotlight_card->nofollow,
				"href_edit"		=> base_url().'admin/spotlight/edit/' . $spotlight_card->spotlight_id . '?token=' . $this->token
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
		
		$data['column_link_text'] 			= $this->lang->line('column_link_text');
		$data['column_body_text'] 		 	= $this->lang->line('column_body_text');	
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
		$data['href_page_edit']		= base_url().'admin/spotlight/edit/';
		$data['href_add']			= base_url().'admin/spotlight/add' . '?token=' . $this->token;
		$data['href_remove']		= base_url().'admin/spotlight/delete' . '?token=' . $this->token;
		$data['href_filter']		= base_url().'admin/spotlight' . '?token=' . $this->token;
		
		//Pagination
		$this->load->library('pagination');

		$config['base_url']		= base_url().'admin/spotlight/';
		$config['per_page']		= $data['limit'];
		$config['total_rows']	= $total_spotlight;

		$this->pagination->initialize($config);
		
		$data['pagination']		=  $this->pagination->create_links();
		$data['content_view']	= 'admin/admin_spotlight_list_v';
		
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
		$data['entry_body_text'] 		= $this->lang->line('entry_body_text');
		$data['entry_link_text'] 		= $this->lang->line('entry_link_text');
		$data['entry_media_text'] 		= $this->lang->line('entry_media_text');

		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$data['string_active'] 		 		= $this->lang->line('string_active');
		$data['string_inactive'] 		 	= $this->lang->line('string_inactive');

		//data
		$data['token'] = $this->token;
			
		//hrefs
		$data['action']	= base_url().'admin/spotlight/add/' . '?token=' . $this->token;

		//load form validation
		$this->load->library('form_validation');
		
		$data['errors'] = array();
		
		//submission detected
		if($this->input->method() == 'post')
		{
			//set rules
			$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
			$this->form_validation->set_rules('title', 'Title', 'trim|required');
			$this->form_validation->set_rules('body', 'Body', 'trim|required');
			$this->form_validation->set_rules('link', 'Link', 'trim|required');
			//$this->form_validation->set_rules('media_image', 'Media', 'trim|required');
			
			$data['media_upload'] = false;
			
			$this->load->library('upload');
			
			$file_name = md5(time());
			
			$config = array(
				'file_name' => $file_name,
				'upload_path' => "./uploads/",
				'allowed_types' => "gif|jpg|png|jpeg|pdf",
				'overwrite' => TRUE,
				'max_size' => "2048000" // Can be set to particular file size , here it is 2 MB(2048 Kb)
			);
			
			$this->load->library('upload');
			$this->upload->initialize($config);
			
			if($this->upload->do_upload('media_image'))
			{
				$upload_data = $this->upload->data();
				if(file_exists($upload_data['full_path']))
				{
					$data['media_upload'] = base_url().'uploads/'.$upload_data['file_name'];
				}
			}

			//return all fields (Anti-XSS)
			foreach($this->input->post() as $n=>$v)
			{
				$data['post'][$n] = clean_output($v);
			}
			
			if($this->form_validation->run())
			{
				//run extended validation
				$post = $this->input->post();
				$post['spotlight_id'] = array();
				$post['media_upload'] = $data['media_upload'];
				$validate = $this->_validate($post);
				
				if(count($validate['errors']) == 0)
				{
					//add
					$add_spotlight = $this->M_admin_spotlight->add_spotlight($this->input->post(), $data['media_upload']);
					if($add_spotlight)
					{
						//set message and redirect
						$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
						$this->_go_url('admin/spotlight');
						
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
		$data['content_view'] = 'admin/admin_spotlight_form_v';
		
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
				$spotlight_ids = $this->input->post('spotlight_id');
				if($spotlight_ids && is_array($spotlight_ids))
				{
					foreach($spotlight_ids as $spotlight_id)
					{
						//max number the int field can hold.
						if(ctype_digit($spotlight_id) && $spotlight_id > 1 && $spotlight_id < 2147483648)
						{
							//delete info
							$this->M_admin_spotlight->delete_spotlight($spotlight_id);
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
		$this->_go_url('admin/spotlight');	
	}
	
	public function edit($spotlight_id=null)
	{
		
		//language
		$data['site_title']  			= $this->M_config->get('site_title').' :: admin :: spotlight :: edit';
		$data['page_header_text'] 		= $this->lang->line('page_header_text');
		$data['table_header_text'] 		= $this->lang->line('table_header_edit_text');
		$data['site_name'] 				= $this->M_config->get('site_title');
		
		$data['entry_title_text'] 		= $this->lang->line('entry_title_text');
		$data['entry_body_text'] 		= $this->lang->line('entry_body_text');
		$data['entry_link_text'] 		= $this->lang->line('entry_link_text');
		$data['entry_media_text'] 		= $this->lang->line('entry_media_text');

		$data['btn_submit_text']		= $this->lang->line('btn_submit_text');
		
		$data['string_active'] 		 	= $this->lang->line('string_active');
		$data['string_inactive'] 		= $this->lang->line('string_inactive');
		
		//data
		$data['token']			= $this->token;
		$data['spotlight_id']	= $spotlight_id;
	
		//hrefs
		$data['action']	= base_url().'admin/spotlight/edit/'.(int)$spotlight_id . '?token=' . $this->token;
		
		//load form validation
		$this->load->library('form_validation');
		
		//get data
		$spotlight_data = $this->M_admin_spotlight->get_spotlight((int)$spotlight_id);
		if($spotlight_data)
		{
			$data['post'] = array(
				"title"			=> clean_output($spotlight_data['title']),
				"body"			=> $spotlight_data['body'],
				"link"			=> clean_output($spotlight_data['link']),
				"media"			=> clean_output($spotlight_data['media'])
			);
				
			//page submission detected
			if($this->input->method() == 'post')
			{
				//set rules
				$this->form_validation->set_error_delimiters('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ', '</div>');
				$this->form_validation->set_rules('title', 'Title', 'trim|required');
				$this->form_validation->set_rules('body', 'Body', 'trim|required');
				$this->form_validation->set_rules('link', 'Link', 'trim|required');
				
				$data['media_upload'] = false;
				
				$this->load->library('upload');
				
				$file_name = md5(time());
				
				$config = array(
					'file_name' => $file_name,
					'upload_path' => "./uploads/",
					'allowed_types' => "gif|jpg|png|jpeg",
					'overwrite' => TRUE,
					'max_size' => "6144000" // Can be set to particular file size , here it is 2 MB(2048 Kb)
				);
				
				$this->load->library('upload');
				$this->upload->initialize($config);
				
				if($this->upload->do_upload('media_image'))
				{
					$upload_data = $this->upload->data();
					if(file_exists($upload_data['full_path']))
					{
						$data['media_upload'] = base_url().'uploads/'.$upload_data['file_name'];
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
					$post['spotlight_id'] = $spotlight_id;
					$post['media_upload'] = $data['media_upload'];
					$validate = $this->_validate($post);
					
					if(count($validate['errors']) == 0)
					{
						//edit spotlight
						$edit_spotlight = $this->M_admin_spotlight->edit_spotlight($this->input->post(), $spotlight_id, $post['media_upload']);
						if($edit_spotlight)
						{

							//set message and redirect
							$this->session->set_flashdata('flash_message_success', $this->lang->line('msg_success'));
							$this->_go_url('admin/spotlight');
							
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
			$data['errors'] = array("Not a valid spotlight card");
		}
		
		$data['data_type'] = 'edit';
		$data['content_view'] = 'admin/admin_spotlight_form_v';
		
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
			
			if(strlen($post['body']) > 1000)
			{
				$errors[] = $this->lang->line('error_body');
			}
			
			if(is_array($post['spotlight_id']))
			{
				if(!isset($post['media_upload']) || empty($post['media_upload']))
				{
					$errors[] = $this->lang->line('error_upload');
				}
			}
			else
			{
				if(!isset($post['media_upload']) && !isset($post['media_image']))
				{
					$errors[] = $this->lang->line('error_upload');
				}
			}
		}

		$return['errors'] = $errors;
		
		return $return;
		
	}
	
}
	
?>
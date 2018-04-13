<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->_check_login();

		$this->load->model('admin/M_admin_dashboard');

		$this->lang->load('admin/dashboard', $this->site_language);
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index()
	{
		//Language
		$data['site_title'] 		= $this->M_config->get('site_title').' :: admin :: dashboard';
		$data['welcome_text']		= sprintf($this->lang->line('msg_welcome'), $this->member_data['firstname']);
		$data['page_header_text']	= $this->lang->line('page_header_text');
		
		$data['total_new_messages'] = sprintf($this->lang->line('message_text'), $this->M_admin_dashboard->get_messages_total(0, $this->_member_pages(false, true), $this->_is_superadmin()));
		$data['total_categories'] 	= sprintf($this->lang->line('category_text'), $this->M_admin_dashboard->get_categories_total($this->_member_pages()));
		$data['total_links'] 		= sprintf($this->lang->line('link_text'), $this->M_admin_dashboard->get_links_total($this->_member_pages()));
		$data['total_pages'] 		= sprintf($this->lang->line('pages_text'), $this->M_admin_dashboard->get_pages_total($this->_member_pages()));
		
		//get data from the last 7 days (pages, links, categories, messages)
		$data['content_view'] = 'admin/admin_dashboard_v';
		$this->template->load_admin_template($data);
	}
	
}
	
?>
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_check_login();
	}

	public function index()
	{
		die('no access');
	}
	
	public function get_categories()
	{
		$test = 0;
		if($this->input->method() == 'post' || $test > 0)
		{
			if(ctype_digit($this->input->post('uid')) && $this->input->post('uid') > -1 || $test > 0){
				
				$this->load->model('admin/M_admin_categories');
				
				$categories = $this->M_admin_categories->get_page_categories($this->input->post('uid'), $this->_member_pages());
				
				header('Content-Type: application/json');
				
				if($categories){
					echo json_encode($categories);
				} else {
					echo json_encode(array("" => "No Categories found for this page"));
				}
			}
		}
	}
	
	public function max_link_sort()
	{
		$max = 0;
		if($this->input->method() == 'post')
		{
			if(ctype_digit($this->input->post('cid')) && $this->input->post('cid') > 0)
			{
				$this->load->model('admin/M_admin_categories');
				$max = $this->M_admin_categories->get_sort_count($this->input->post('cid'));
			}
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("max" => $max));
	}
	
	public function max_category_sort()
	{
		$max = 0;
		if($this->input->method() == 'post')
		{
			if(ctype_digit($this->input->post('cid')) && ctype_digit($this->input->post('col')))
			{
				$this->load->model('admin/M_admin_pages');
				$max = $this->M_admin_pages->get_sort_count($this->input->post('cid'), $this->input->post('col'));
			}
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("max" => $max));
	}
	
	public function get_link_details()
	{
		if($this->input->method() == 'post')
		{
			if(ctype_digit($this->input->post('uid')) && $this->input->post('uid') > -1)
			{
				$this->load->model('admin/M_admin_links');
				$result = $this->M_admin_links->link_detail($this->input->post('uid'), $this->_member_pages());
				
				if($result)
				{
					echo json_encode($result);
				}
			}
		}
	}
	
	public function get_page_details()
	{
		if($this->input->method() == 'post')
		{
			if(ctype_digit($this->input->post('uid')) && $this->input->post('uid') > -1)
			{
				$this->load->model('admin/M_admin_pages');
				$result = $this->M_admin_pages->get_page($this->input->post('uid'), $this->_member_pages());
				
				if($result)
				{
					echo json_encode($result);
				}
			}
		}
	}
}
	
?>
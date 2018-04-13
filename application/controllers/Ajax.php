<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
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
				
				$this->load->model('M_categories');
				
				$categories = $this->M_categories->get_page_categories($this->input->post('uid'));
				
				header('Content-Type: application/json');
				
				if($categories){
					echo json_encode($categories);
				} else {
					echo json_encode(array("" => "No Categories found for this page"));
				}
				
			}
			
		}

	}
}
	
?>
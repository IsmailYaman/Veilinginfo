<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//load language
		$this->load->model("M_info");
		
		if(Is_Subdomain())
		{
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'dochterpaginas');
		}
	}
	 
	public function index($page_slug = false)
	{
		$info = '';
		if($page_slug)
		{
			$info = $this->M_info->get_info($page_slug);
		}
		
		//data
		$data['description']	= $info->description;
		$data['info'] 		  	= $info;
		$data['content_view'] 	= 'info_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
}

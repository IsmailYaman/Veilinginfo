<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//load language
		$this->load->model("M_pages");
		
		if(Is_Subdomain())
		{
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'dochterpaginas');
		}
	}
	 
	public function index()
	{
	
		$pages = $this->M_pages->get_all_pages();
		
		$cpc = round(count($pages)/4);
		
		$c  = 0;
		$cr = 1;
		$column_data = array();

		foreach($pages as $page)
		{
			$char = strtolower(substr($page->url, 0, 1));
			$title = strtoupper($char);
			if(is_numeric($char))
			{
				$title = "123";
			}
			
			if($c > ($cpc * $cr) && empty($column_data[$cr][$title]) && $cr != 4) {
				$cr = $cr + 1;
			}

			$column_data[$cr][$title][] = array('url' => ucfirst($page->url), 'link' => format_page_url($page->url));
			
			$c++;
		}

		$data['block']		  = $column_data;
		$data['content_view'] = 'pages_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
}

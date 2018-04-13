<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//load language
		$this->load->model("M_search");
	}
	 
	public function index()
	{
		//get search request
		$categories = array();
		if($this->input->get('query') && strlen($this->input->get('query')) < 101 && strlen($this->input->get('query')) > 3)
		{
			$categories = $this->M_search->get_all_links($this->input->get('query'));
		}

		$cpc = round(count($categories)/3);
		
		$c  = 0;
		$cr = 1;
		$column_data = array();

		foreach($categories as $category)
		{
			//print_r($category);
			
			$title = ucfirst($category[0]['category']);

			if($c > ($cpc * $cr) && empty($column_data[$cr][$title]) && $cr != 3) {
				$cr = $cr + 1;
			}

			foreach($category as $links)
			{
				//print_r($links);
				$column_data[$cr][$title][] = array('url' => $links['url'], 'anchor' => $links['anchor'], 'description' => $links['description']);
			}
			$c++;
		}

		$data['block']		  = $column_data;
		$data['content_view'] = 'search_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
}

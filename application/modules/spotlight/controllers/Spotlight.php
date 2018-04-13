<?php

class Spotlight extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Spotlight/M_spotlight');
	}
	
	public function display_data()
	{
		$data['spotlight_content'] = $this->build_link_list();
		return $this->load->view('spotlight/spotlight_v', $data, true);
	}

	public function build_link_list()
	{
		$links = $this->M_spotlight->get_spotlights_cards();
		
		$link_content = '';
		foreach($links as $link){
			$data['title']	= $link->title;
			$data['body']	= $link->body;
			$data['media']  = $link->media;
			$data['link']   = $link->link;
			$link_content  .= $this->load->view('spotlight/spotlight_list_v', $data, true);
		}
		
		return $link_content;
	}
}
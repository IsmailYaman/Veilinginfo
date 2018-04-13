<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//load language
		$this->lang->load('front/front', $this->site_language);
	}
	 
	public function index()
	{
		$page = CurrentPage();
		
		$data['error_message'] = null;
		if(!$page['exist']){
			header('HTTP/1.0 404 Not Found');
			$data['error_message'] = $this->lang->line('msg_not_found');			
		}

		$this->load->module('widgets');
		$this->load->module('links');
		$this->load->module('spotlight');
		
		$widgets = $this->widgets->get_all_widgets();
		$links 	 = $this->links->display_data($page['current']);

		$columns_total = $this->config->item('column_count');

		$column_row_merge	= array();
		$column_row_widgets = array();
		$column_row_links	= array();
		
		for ($x = 1; $x <= $columns_total; $x++)
		{
			$column_row_merge['column_'.$x] = '';
			
			$total_items_in_current_column = 0;
			
			$total_widget 	= 0;
			$total_links 	= 0;
			if(isset($widgets['column_'.$x])){
				$total_widget = max(array_keys($widgets['column_'.$x]));
			}
			
			if(isset($links['column_'.$x])){
				$total_links = max(array_keys($links['column_'.$x]));
			}
			
			$total_items_in_current_column = max($total_widget, $total_links);
			
			for ($d = 0; $d <= $total_items_in_current_column; $d++) {
				if(isset($widgets['column_'.$x][$d])){
					$column_row_merge['column_'.$x] .= $widgets['column_'.$x][$d];
				}
				if(isset($links['column_'.$x][$d])){
					$column_row_merge['column_'.$x] .= $links['column_'.$x][$d];
				}
			}
		}
		
		$this->load->model('M_pages');
		$page_info = $this->M_pages->get_page_info($page['current']);
		
		if($page_info)
		{
			$data['page_title'] 	= $page_info->name;
			$data['description'] 	= $page_info->description;
		}
		
		//Languages
		$data['lang_txt_header_1'] = $this->lang->line('header_1');
		$data['lang_txt_header_2'] = $this->lang->line('header_2');

		$data['spotlight']		= $this->spotlight->display_data();
		$data['block']			= $column_row_merge;
		$data['content_view']	= 'homepage_v';
		
		//render to screen
		$this->template->load_default_template($data);
	}
}

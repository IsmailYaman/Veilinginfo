<?php

class Links extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Links/M_links');
	}
	
	public function display_data($subpage = 0){
		
		$categories 	  = $this->M_links->get_categories($subpage);
		$category_columns = array();
		$sort_list 		  = array();
		foreach($categories as $category)
		{
			$data['category_name']    = $category->name;
			$data['category_content'] = $this->build_link_list($category->category_id);
			$sort_order = $category->sort_order;
			
			//we have entry with the same sort order!
			if(isset($sort_list[$category->column_row][$sort_order]))
			{
				$sort_order = $sort_order+1;
			}
			
			$category_columns['column_'.$category->column_row][$sort_order] = $this->load->view('links/links_v', $data, true);
			$sort_list[$category->column_row][$sort_order] = true;
		}
		
		return $category_columns;
	}

	public function build_link_list($category_id)
	{
		$links = $this->M_links->get_links($category_id);
		
		$link_content = '';
		foreach($links as $link){
			$data['nofollow'] = '';
			if($link->no_follow)
			{
				$data['nofollow'] = "rel='nofollow'";
			}
			$data['url'] 		 = $link->url;
			$data['anchor'] 	 = $link->anchor;
			$data['description'] = $link->description;
			$link_content  .= $this->load->view('links/link_list_v', $data, true);
		}
		
		return $link_content;
	}
}
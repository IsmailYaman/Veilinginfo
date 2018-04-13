<?php

class M_links extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_categories($page_id = 1)
	{
		$query = $this->db->query("SELECT category_id, name, sort_order, column_row FROM start_categories WHERE (page_id = ? OR global = 1) AND (SELECT count(link_id) FROM start_links WHERE category_id = start_categories.category_id AND expire_date > ?) > 0 ORDER by sort_order", array($page_id, time()));
		return $query->result();
	}
	
	public function get_links($category_id){
		$query = $this->db->query("SELECT link_id, no_follow, url, anchor, description FROM start_links WHERE category_id = ? AND expire_date > ? AND status = 1 ORDER BY sort_order", array($category_id, time()));
		return $query->result();
	}
}

?>
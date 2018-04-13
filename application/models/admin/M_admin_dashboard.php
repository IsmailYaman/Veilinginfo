<?php

class M_admin_dashboard extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_messages_total($status, $pages, $is_admin)
	{
		$sql   = "SELECT count(a.message_id) as total FROM start_messages a";
		$sql  .= " LEFT JOIN start_links b ON b.link_id = a.data_id LEFT JOIN start_categories c ON c.category_id = b.category_id";
		$sql  .= " WHERE a.status = ? AND c.page_id IN ({$pages}) AND a.type = 2";

		$binds = array();
		
		$binds[] = $status;

		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, $binds);
		$result = $query->row();
		
		if($is_admin)
		{
			$sql  = "SELECT count(a.message_id) as total FROM start_messages a";
			$sql  .= " WHERE a.status = ? AND a.type = 1";
			
			$binds = array();
			
			$binds[] = $status;
			
			$sql .= " LIMIT 1";
			
			$query 	 = $this->db->query($sql, $binds);
			$result2 = $query->row();
			
			return ($result->total + $result2->total);
			
		}
		
		return $result->total;
	}
	
	public function get_categories_total($pages)
	{
		$sql = "SELECT count(category_id) as total FROM start_categories a LEFT JOIN start_pages b ON a.page_id = b.page_id WHERE b.status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND a.page_id IN ({$pages})";
		}	
		
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql);
		$result = $query->row();
		return $result->total;
	}
	
	public function get_links_total($pages)
	{
		$sql = "SELECT count(link_id) as total FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE c.status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND b.page_id IN ({$pages})";
		}	
		
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql);
		$result = $query->row();
		return $result->total;
	}
	
	public function get_pages_total($pages)
	{
		$sql = "SELECT count(page_id) as total FROM start_pages a LEFT JOIN start_members b ON a.member_id = b.member_id WHERE status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND a.page_id IN ({$pages})";
		}
		
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql);
		$result = $query->row();
		return $result->total;
	}
}

?>
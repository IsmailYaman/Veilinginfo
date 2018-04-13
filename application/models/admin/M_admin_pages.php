<?php

class M_admin_pages extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}
	
	public function get_pages($filter, $limit)
	{
		$sql = "SELECT a.page_id, a.url, a.description, a.name, a.member_id, CONCAT(b.firstname,' ',b.lastname) as member FROM start_pages a LEFT JOIN start_members b ON a.member_id = b.member_id WHERE status = 1";
		
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	

				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( a.name LIKE ? ";
						$sql .= "OR a.description LIKE ? )";
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					}
					elseif($filter_name == "pages")
					{	
						if($filter_value !== "all")
						{
							$sql .= " AND ( a.page_id IN ({$filter_value}) )";	
						}

					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " AND  a.".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " AND  a.".$filter_name." = ? ";
							$binds[] = $filter_value;
						}
					}	
				}
			}
			
		}
		
		if(isset($limit))
		{
			$sql .= " LIMIT ".$limit['start'].",".$limit['max'];
		}
		
		$query = $this->db->query($sql, $binds);
		return $query->result();
	}
	
	public function get_pages_total($filter)
	{
		$sql = "SELECT count(page_id) as total FROM start_pages a LEFT JOIN start_members b ON a.member_id = b.member_id WHERE status = 1";

		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( a.name LIKE ? )";
						$binds[] = '%'.$filter_value.'%';
					}
					elseif($filter_name == "pages")
					{	
						if($filter_value !== "all")
						{
							$sql .= " AND ( a.page_id IN ({$filter_value}) )";	
						}

					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " AND  a.".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " AND  a.".$filter_name." = ? ";
							$binds[] = $filter_value;
						}
					}	
				}
			}
			
		}	
		
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, $binds);
		$result = $query->row();
		return $result->total;
	}
	
	public function get_all_pages($pages)
	{
		$sql = "SELECT page_id, name, description FROM start_pages WHERE status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}
		
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function get_sort_count($page_id, $col)
	{
		$query_max  = $this->db->query("SELECT max(sort_order) as total FROM start_categories WHERE page_id = ? AND column_row = ? LIMIT 1", array($page_id, $col) );
		$result_max = $query_max->row();
		
		$query_count  = $this->db->query("SELECT count(category_id) as total FROM start_categories WHERE page_id = ? AND column_row = ? LIMIT 1", array($page_id, $col) );
		$result_count = $query_count->row();
		
		$total = max($result_count->total, $result_max->total);

		return ($total);
	}
	
	public function verify_page($page_id, $pages)
	{
		$sql = "SELECT page_id FROM start_pages WHERE page_id = ? AND status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}
		$sql .= " LIMIT 1";
		
		$query = $this->db->query($sql, array($page_id) );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function edit_page($data, $page_id, $is_superadmin)
	{
		$update = array(
			"description" 	=> $data['description'],
			"name" 			=> $data['page_name']
		);
		
		if($is_superadmin)
		{
			$update['member_id'] = $data['member_id'];
			$update['url'] 		 = $data['url'];
		}
		
		if(isset($data['status']))
		{
			$update['status'] = $data['status'];
		}
		
		$query = $this->db->update('pages', $update, array("page_id" => (int)$page_id) );
		
		return $query;
	}
	
	public function add_page($data, $is_superadmin)
	{
		$insert = array(
			"url" 			=> $data['url'],
			"description" 	=> $data['description'],
			"name" 			=> $data['page_name'],
			"status" 		=> 1
		);
		
		if($is_superadmin)
		{
			$insert['member_id'] = $data['member_id'];
		}
		
		$query = $this->db->insert('pages', $insert);
		
		return $query;
	}
	
	public function get_page($page_id, $pages)
	{
		$sql = "SELECT a.url, a.description, a.name, a.member_id FROM start_pages a WHERE a.page_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, array($page_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"url" 			=> $result->url,
				"description" 	=> $result->description,
				"page_name" 	=> $result->name,
				"member_id"		=> $result->member_id
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function check_url($url, $page_id = array())
	{
		$sql = "SELECT page_id FROM start_pages WHERE url = ?";
		$binds[] = $url;
		
		if(!is_array($page_id))
		{
			$sql .= " AND page_id != ?";
			$binds[] = $page_id;
		}
		
		$sql .= " LIMIT 1";
		
		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function delete_page($page_id, $pages)
	{
		$sql_c = "SELECT category_id FROM start_categories WHERE page_id = ?";
		
		if($pages !== "all")
		{
			$sql_c .= " AND page_id IN ({$pages})";
		}
		
		$query = $this->db->query( $sql_c, array($page_id) );
		
		if($query->num_rows() > 0)
		{
			$results = $query->result();
			foreach($results as $result)
			{
				$this->db->delete('categories', array('category_id' => (int)$result->category_id));
				$this->db->delete('links', array('category_id' => (int)$result->category_id));
			}
		}
		
		$sql_p = "DELETE FROM start_pages WHERE page_id = ?";
		
		if($pages !== "all")
		{
			$sql_p .= " AND page_id IN ({$pages})";
		}
		
		$this->db->query( $sql_p, array($page_id) );

	}
}

?>
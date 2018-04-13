<?php

class M_admin_categories extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_categories($filter, $limit)
	{
		$sql = "SELECT a.category_id, a.name, a.global, a.column_row, b.url as page_url, b.name as page FROM start_categories a LEFT JOIN start_pages b ON a.page_id = b.page_id WHERE b.status = 1";

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
		
		if(isset($limit))
		{
			$sql .= " LIMIT ".$limit['start'].",".$limit['max'];
		}
		
		$query = $this->db->query($sql, $binds);
		return $query->result();
	}
	
	public function get_categories_total($filter)
	{
		$sql = "SELECT count(category_id) as total FROM start_categories a LEFT JOIN start_pages b ON a.page_id = b.page_id WHERE b.status = 1";
		
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
	
	public function get_page_categories($page_id=0, $pages)
	{
		$sql = "SELECT a.category_id, a.name as category FROM start_categories a LEFT JOIN start_pages b ON a.page_id = b.page_id WHERE a.page_id = ? AND b.status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND a.page_id IN ({$pages})";
		}	
		
		$query = $this->db->query($sql, array($page_id) );
		
		if($query->num_rows() > 0){
			
			$return = array();
			foreach($query->result() as $result ){
				$return[$result->category_id] = $result->category;
			}
			
			return $return;
		}
		
		return false;
	}
	
	public function validate_category($page_id=0, $category_id=0, $pages)
	{
		$sql = "SELECT category_id FROM start_categories WHERE page_id = ? AND category_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}	
		$sql .= " LIMIT 1";
		
		$query = $this->db->query($sql, array($page_id, $category_id) );
		
		if($query->num_rows() == 1){
			return true;
		}
		return false;
	}
	
	public function get_sort_count($category_id)
	{
		$query_max  = $this->db->query("SELECT max(sort_order) as total FROM start_links WHERE category_id = ? LIMIT 1", array($category_id) );
		$result_max = $query_max->row();
		
		$query_count  = $this->db->query("SELECT count(link_id) as total FROM start_links WHERE category_id = ? LIMIT 1", array($category_id) );
		$result_count = $query_count->row();
		
		$total = max($result_count->total, $result_max->total);
		
		return ($total+1);
	}
	
	public function get_all_categories($pages)
	{
		$sql = "SELECT category_id, name FROM start_categories WHERE 1=1";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}	
		
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function add_category($data)
	{
		$insert = array(
			"page_id" 		=> $data['page_id'],
			"name" 			=> $data['category_name'],
			"column_row" 	=> $data['column_row'],
			"sort_order" 	=> $data['sort_order']
		);
		
		$query = $this->db->insert('categories', $insert);
		
		return $query;
	}
	
	public function edit_category($data, $category_id)
	{
		$update = array(
			"page_id" 		=> $data['page_id'],
			"name" 			=> $data['category_name'],
			"column_row" 	=> $data['column_row'],
			"sort_order" 	=> $data['sort_order']
		);
		
		$query = $this->db->update('categories', $update, array("category_id" => (int)$category_id) );
		
		return $query;
	}
	
	public function get_category($category_id, $pages)
	{
		$sql = "SELECT a.name, a.column_row, a.sort_order, a.page_id FROM start_categories a WHERE a.category_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND a.page_id IN ({$pages})";
		}
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, array($category_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"page_id" => $result->page_id,
				"category_name" => $result->name,
				"column_row" => $result->column_row,
				"sort_order" => $result->sort_order
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function delete_category($category_id, $pages)
	{
		$sql = "SELECT category_id FROM start_categories WHERE category_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND page_id IN ({$pages})";
		}
		
		$sql .= " LIMIT 1";

		$result = $this->db->query($sql, array($category_id));

		if($result->num_rows() > 0)
		{
			$this->db->delete('categories', array('category_id' => (int)$category_id));
			$this->db->delete('links', array('category_id' => (int)$category_id));
		}

	}
}

?>
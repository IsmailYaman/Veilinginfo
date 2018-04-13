<?php

class M_admin_links extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_links($filter, $limit)
	{
		$sql = "SELECT a.link_id, a.anchor, a.url, a.backlink, a.expire_date, b.name as category, c.name as page, c.url as page_url FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE a.status = 1";
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( a.anchor LIKE ? ";
						$sql .= " OR a.url LIKE ? ";
						$sql .= " OR a.backlink LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					}
					elseif($filter_name == "pages")
					{	
						if($filter_value !== "all")
						{
							$sql .= " AND ( c.page_id IN ({$filter_value}) )";	
						}
					} elseif($filter_name == "page_id") {
						$sql .= " AND  b.".$filter_name." = ? ";
						$binds[] = $filter_value;
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
	
	public function get_links_total($filter)
	{
		$sql = "SELECT count(link_id) as total FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE c.status = 1";
		
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || is_numeric($filter_value))
				{

					if($filter_name == "search")
					{
						$sql .= " AND ( a.anchor LIKE ? ";
						$sql .= " OR a.url LIKE ? ";
						$sql .= " OR a.backlink LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					}
					elseif($filter_name == "pages")
					{	
						if($filter_value !== "all")
						{
							$sql .= " AND ( c.page_id IN ({$filter_value}) )";	
						}
					} elseif($filter_name == "page_id") {
						$sql .= " AND  b.".$filter_name." = ? ";
						$binds[] = $filter_value;
					} else {
						$sql .= " AND  a.".$filter_name." = ? ";
						$binds[] = $filter_value;
					}	
				}
			}
			
		}	
		
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, $binds);
		$result = $query->row();
		return $result->total;
	}
	
	public function add_link($data)
	{
		$expires = array(
			"never" 	=> time()+630720000, 
			"1day" 		=> time()+86400, 
			"1week" 	=> time()+604800, 
			"1month" 	=> time()+2592000, 
			"1year" 	=> time()+31536000, 
			"5years" 	=> time()+157680000, 
			"10years" 	=> time()+315360000, 
			"custom" 	=> strtotime($data['custom_expire_date']), 
		);
		
		if(!isset($data['no_follow'])){
			$data['no_follow'] = 0;
		}
		
		$insert = array(
			"category_id" 	=> $data['category_id'],
			"anchor" 		=> $data['anchor'],
			"description" 	=> $data['description'],
			"url" 			=> $data['url'],
			"backlink" 		=> $data['backlink'],
			"no_follow" 	=> $data['no_follow'],
			"email" 		=> $data['email'],
			"sort_order" 	=> $data['sort_order'],
			"premium" 		=> 0,
			"creation_date" => time(),
			"expire_date" 	=> $expires[$data['expire_date']],
			"status" 		=> 1
		);
		
		$query = $this->db->insert('links', $insert);
		
		return $query;
	}
	
	public function get_link($link_id, $pages)
	{
		$sql = "SELECT a.link_id, a.anchor, a.description, a.url, a.backlink, a.email, a.no_follow, a.sort_order, a.expire_date, a.category_id, c.page_id, c.url as page_url, c.name as page_name FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE a.link_id = ? AND c.status = 1";
		
		if($pages !== "all")
		{
			$sql .= " AND c.page_id IN ({$pages})";
		}
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, array($link_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"page_id" => $result->page_id,
				"page_url" => $result->page_url,
				"page_name" => $result->page_name,
				"category_id" => $result->category_id,
				"anchor" => $result->anchor,
				"description" => $result->description,
				"url" => $result->url,
				"backlink" => $result->backlink,
				"email" => $result->email,
				"no_follow" => $result->no_follow,
				"sort_order" => $result->sort_order,
				"expire_date" => "custom",
				"custom_expire_date" => date('m/d/Y',$result->expire_date),
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function link_detail($link_id, $pages)
	{
		$sql = "SELECT a.link_id, a.anchor, a.url, a.backlink, a.email, a.no_follow, a.sort_order, a.expire_date, a.category_id, c.page_id, b.name as cat_name, c.name as page_name FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE a.link_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND c.page_id IN ({$pages})";
		}
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, array($link_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"page_id" => $result->page_id,
				"category_id" => $result->category_id,
				"anchor" => $result->anchor,
				"url" => $result->url,
				"cat_name" => $result->cat_name,
				"page_name" => $result->page_name,
				"backlink" => $result->backlink,
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function edit_link($data, $link_id)
	{
		$expires = array(
			"never" 	=> time()+630720000, 
			"1day" 		=> time()+86400, 
			"1week" 	=> time()+604800, 
			"1month" 	=> time()+2592000, 
			"1year" 	=> time()+31536000, 
			"5years" 	=> time()+157680000, 
			"10years" 	=> time()+315360000, 
			"custom" 	=> strtotime($data['custom_expire_date']), 
		);
		
		$update = array(
			"category_id" 	=> $data['category_id'],
			"anchor" 		=> $data['anchor'],
			"description" 	=> $data['description'],
			"url" 			=> $data['url'],
			"backlink" 		=> $data['backlink'],
			"no_follow" 	=> $data['no_follow'],
			"email" 		=> $data['email'],
			"sort_order" 	=> $data['sort_order'],
			"premium" 		=> 0,
			"expire_date" 	=> $expires[$data['expire_date']]
		);
		
		if(isset($data['status']))
		{
			$update['status'] = 1;
		}
		
		$query = $this->db->update('links', $update, array("link_id" => (int)$link_id) );
		
		return $query;
	}
	
	public function delete_link($link_id, $pages)
	{
		$sql = "SELECT a.link_id FROM start_links a LEFT JOIN start_categories b ON a.category_id = b.category_id LEFT JOIN start_pages c ON c.page_id = b.page_id WHERE a.link_id = ?";
		
		if($pages !== "all")
		{
			$sql .= " AND c.page_id IN ({$pages})";
		}
		
		$sql .= " LIMIT 1";
		
		$result = $this->db->query($sql, array($link_id));
		
		if($result->num_rows() > 0)
		{
			$del = $this->db->delete('links', array('link_id' => (int)$link_id));
			return (bool)$del;
		}
		
		return false;
	}
}

?>
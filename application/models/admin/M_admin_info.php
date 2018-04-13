<?php

class M_admin_info extends CI_Model {

	public function __construct()
	{
			// Call the CI_Model constructor
			parent::__construct();
			//$this->output->enable_profiler(TRUE);
	}
	
	public function get_info_pages($filter, $limit)
	{
		$sql = "SELECT * FROM start_info WHERE 1=1";
		
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	

				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( title LIKE ? ";
						$sql .= "OR text LIKE ? ";
						$sql .= "OR description LIKE ? ";
						$sql .= "OR menu_title LIKE ? )";
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " AND  ".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " AND  ".$filter_name." = ? ";
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
	
	public function get_info_total($filter)
	{
		$sql = "SELECT count(info_id) as total FROM start_info WHERE 1=1";

		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( title LIKE ? ";
						$sql .= " OR text LIKE ? ";
						$sql .= " OR description LIKE ? ";
						$sql .= " OR menu_title LIKE ? )";
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " AND  ".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " AND  ".$filter_name." = ? ";
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

	public function edit_info($data, $info_id)
	{
		$update = array(
			"title" 		=> $data['title'],
			"text" 			=> $data['text'],
			"description" 	=> $data['description'],
			"slug" 			=> $data['slug'],
			"menutype" 		=> $data['menutype'],
			"menu_title" 	=> $data['menu_title'],
			"active" 		=> $data['active']
		);

		$query = $this->db->update('info', $update, array("info_id" => (int)$info_id) );
		
		return $query;
	}
	
	public function add_info($data)
	{
		$insert = array(
			"title" 		=> $data['title'],
			"text" 			=> $data['text'],
			"description" 	=> $data['description'],
			"menutype" 		=> $data['menutype'],
			"slug" 			=> $data['slug'],
			"menu_title" 	=> $data['menu_title'],
			"active" 		=> 1
		);

		$query = $this->db->insert('info', $insert);
		
		return $query;
	}
	
	public function get_info($info_id)
	{
		$sql = "SELECT title, text, description, slug, menutype, menu_title, active FROM start_info WHERE info_id = ? LIMIT 1";
		
		$query 	= $this->db->query($sql, array($info_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"title" 		=> $result->title,
				"text" 			=> $result->text,
				"description" 	=> $result->description,
				"slug" 			=> $result->slug,
				"menutype" 		=> $result->menutype,
				"menu_title" 	=> $result->menu_title,
				"active"		=> $result->active
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function get_menus()
	{
		$sql = "SELECT menu_id, menu_title FROM start_menus";

		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function check_menu($menu_id)
	{
		$sql = "SELECT menu_id FROM start_menus WHERE menu_id = ? LIMIT 1";

		$query = $this->db->query($sql, array($menu_id) );
		
		if($query->num_rows() == 1){
			return true;
		}
		return false;
	}

	public function delete_info($info_id)
	{		
		$query = $this->db->query( "DELETE FROM start_info WHERE info_id = ? LIMIT 1", array($info_id) );
		return $query;
	}
}

?>
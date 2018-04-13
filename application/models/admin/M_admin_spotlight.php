<?php

class M_admin_spotlight extends CI_Model {

	public function __construct()
	{
			// Call the CI_Model constructor
			parent::__construct();
			//$this->output->enable_profiler(TRUE);
	}
	
	public function get_spotlight_cards($filter, $limit)
	{
		$sql = "SELECT * FROM start_spotlight WHERE 1=1";
		
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
						$sql .= "OR body LIKE ? ";
						$sql .= "OR link LIKE ? )";
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
	
	public function get_spotlight_total($filter)
	{
		$sql = "SELECT count(spotlight_id) as total FROM start_spotlight WHERE 1=1";

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
						$sql .= " OR body LIKE ? ";
						$sql .= " OR link LIKE ? )";
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

	public function edit_spotlight($data, $spotlight_id, $upload)
	{
		$update = array(
			"title" 		=> $data['title'],
			"body" 			=> $data['body'],
			"link" 			=> $data['link'],
			"media" 		=> $data['media']
		);
		
		if(isset($upload) && !empty($upload))
		{
			$update['media'] = $upload;
		}
		else
		{
			$update['media'] = $data['media'];
		}

		$query = $this->db->update('spotlight', $update, array("spotlight_id" => (int)$spotlight_id) );
		
		return $query;
	}
	
	public function add_spotlight($data, $upload)
	{
		$insert = array(
			"title" 		=> $data['title'],
			"body" 			=> $data['body'],
			"link" 			=> $data['link'],
			"media" 		=> $upload
		);

		$query = $this->db->insert('spotlight', $insert);
		
		return $query;
	}
	
	public function get_spotlight($spotlight_id)
	{
		$sql = "SELECT title, body, media, link FROM start_spotlight WHERE spotlight_id = ? LIMIT 1";
		
		$query 	= $this->db->query($sql, array($spotlight_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"title" 		=> $result->title,
				"body" 			=> $result->body,
				"media" 		=> $result->media,
				"link" 			=> $result->link
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

	public function delete_spotlight($spotlight_id)
	{		
		$query = $this->db->query( "DELETE FROM start_spotlight WHERE spotlight_id = ? LIMIT 1", array($spotlight_id) );
		return $query;
	}
}

?>
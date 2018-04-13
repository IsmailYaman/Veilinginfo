<?php

class M_admin_languages extends CI_Model {

	public function __construct()
	{
			// Call the CI_Model constructor
			parent::__construct();
			//$this->output->enable_profiler(TRUE);
	}
	
	public function get_all_languages()
	{
		$sql = "SELECT language_id, name, machine_name FROM start_languages";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_languages($filter, $limit)
	{
		$sql = "SELECT language_id, name, machine_name FROM start_languages WHERE 1=1 ";
		
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	

				if(!empty($filter_value) || ctype_digit($filter_value))
				{
					$sql .= " AND ";
					
					if($filter_name == "search")
					{
						$sql .= "( name LIKE ?";
						$sql .= " OR machine_name LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " ".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " ".$filter_name." = ? ";
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

	public function get_language_total($filter)
	{
		$sql = "SELECT count(language_id) as total FROM start_languages WHERE 1=1 ";
		
		$binds = array();
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || ctype_digit($filter_value))
				{
					$sql .= " AND ";
					
					if($filter_name == "search")
					{
						$sql .= "( name LIKE ?";
						$sql .= " OR machine_name LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " ".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " ".$filter_name." = ? ";
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
	
	public function get_aliases($language_id)
	{
		$sql = "SELECT query, keyword FROM start_url_alias WHERE language_id = ?";
		$query = $this->db->query($sql, array($language_id));
		return $query->result();
	}
	
	public function add_language($data)
	{
		$insert = array(
			"name"				=> $data['name'],
			"machine_name"		=> $data['machine_name']
		);

		$query = $this->db->insert('languages', $insert);
		if($query)
		{
			$language_id = $this->db->insert_id();
			foreach($data['alias'] as $key=>$val)
			{
				$insert = array(
					"query"			=> $key,
					"keyword"		=> $val,
					"language_id"	=> $language_id
				);
				
				$query = $this->db->insert('url_alias', $insert);
			}
		}
		return $query;
	}
	
	public function get_language($language_id)
	{
		$sql = "SELECT name, machine_name FROM start_languages WHERE language_id = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($language_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"name"			=> $result->name,
				"machine_name"	=> $result->machine_name
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function edit_language($data, $language_id)
	{
		$update = array(
			"name"			=> $data['name'],
			"machine_name"	=> $data['machine_name']
		);
		
		$query = $this->db->update('languages', $update, array("language_id" => (int)$language_id) );
		
		if($query)
		{
			$remove = $this->db->query( "DELETE FROM start_url_alias WHERE language_id = ?", array($language_id) );
			if($remove)
			{
				foreach($data['alias'] as $key=>$val)
				{
					$insert = array(
						"query"			=> $key,
						"keyword"		=> $val,
						"language_id"	=> $language_id
					);
					
					$query = $this->db->insert('url_alias', $insert);
				}
			}
		}
		
		return $query;
	}
	
	public function check_language($machine_name, $language_id = array())
	{
		$sql = "SELECT language_id FROM start_languages WHERE machine_name = ?";
		$binds[] = $machine_name;
		
		if(!is_array($language_id))
		{
			$sql .= " AND language_id != ?";
			$binds[] = $language_id;
		}
		
		$sql .= " LIMIT 1";

		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function check_language_exist($language_id)
	{
		$sql = "SELECT language_id FROM start_languages WHERE language_id = ?";
		$binds[] = $language_id;
		$sql .= " LIMIT 1";

		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function delete_language($language_id)
	{
		$del = $this->db->delete('languages', array('language_id' => (int)$language_id));
		if($del)
		{
			$del = $this->db->delete('url_alias', array('language_id' => (int)$language_id));
		}
		return $del;
	}
}

?>
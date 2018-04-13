<?php

class M_admin_settings extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_settings()
	{
		$sql = "SELECT name, value FROM start_settings";
		
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function update_settings($data)
	{
		foreach($data as $k=>$v)
		{
			$query = $this->db->update('settings', array("value" => $v), array("name" => $k) );
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
<?php

class M_info extends CI_Model {

	public function __construct()
	{
			// Call the CI_Model constructor
			parent::__construct();
	}

	public function get_all_infos($all=false, $menutype)
	{
		$query = $this->db->query("SELECT slug, menu_title FROM start_info WHERE active = 1 AND menutype = ? ORDER BY menu_title", $menutype);
		return $query->result();
	}
	
	public function get_info($slug=false)
	{ 
		if(!$slug)
		{
			return false;
		}
		
		$sql 	 = "SELECT title, text, description FROM start_info WHERE slug = ? LIMIT 1";
		$binds[] = $slug;
		
		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		return false;
	}
}
?>
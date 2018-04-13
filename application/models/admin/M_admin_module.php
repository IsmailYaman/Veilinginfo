<?php

class M_admin_module extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_modules($filter, $limit)
	{
		$sql = "SELECT module_id, name, machine_name, column_row, sort_order FROM start_modules WHERE 1=1 ";
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

	public function get_module_total($filter)
	{
		$sql = "SELECT count(module_id) as total FROM start_modules WHERE 1=1 ";
		
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
	
	public function add_module($data)
	{
		$insert = array(
			"name"				=> $data['name'],
			"machine_name"		=> $data['machine_name'],
			"column_row"		=> $data['column_row'],
			"sort_order"		=> $data['sort_order'],
			"active"			=> 1
		);

		return $this->db->insert('modules', $insert);
	}
	
	public function get_module($module_id)
	{
		$sql = "SELECT name, machine_name, column_row, sort_order FROM start_modules WHERE module_id = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($module_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"name"			=> $result->name,
				"machine_name"	=> $result->machine_name,
				"column_row"	=> $result->column_row,
				"sort_order"	=> $result->sort_order
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function edit_module($data, $module_id)
	{
		$update = array(
			"name"			=> $data['name'],
			"machine_name"	=> $data['machine_name'],
			"column_row"	=> $data['column_row'],
			"sort_order"	=> $data['sort_order']
		);
		
		$query = $this->db->update('modules', $update, array("module_id" => (int)$module_id) );
		
		return $query;
	}
	
	public function check_machine_name($machine_name, $module_id = array())
	{
		$this->output->enable_profiler(TRUE);
		
		$sql = "SELECT module_id FROM start_modules WHERE machine_name = ?";
		$binds[] = $machine_name;
		
		if(!is_array($module_id))
		{
			$sql .= " AND module_id != ?";
			$binds[] = $module_id;
		}
		
		$sql .= " LIMIT 1";

		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function delete_module($module_id)
	{
		return $this->db->delete('modules', array('module_id' => (int)$module_id));
	}
		
}

?>
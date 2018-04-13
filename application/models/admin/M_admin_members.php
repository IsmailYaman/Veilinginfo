<?php

class M_admin_members extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_all_members(){
		$sql = "SELECT member_id, CONCAT(firstname,' ',lastname) as name FROM start_members";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function get_all_member_groups()
	{
		$sql = "SELECT member_group_id, name FROM start_member_groups";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function check_member($member_id)
	{
		$query = $this->db->query("SELECT member_id FROM start_members WHERE member_id = ? LIMIT 1", array($member_id) );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function get_members($filter, $limit)
	{
		$sql = "SELECT a.member_id, a.firstname, a.lastname, a.email, a.last_seen, b.name as member_group FROM start_members a LEFT JOIN start_member_groups b ON a.member_group_id = b.member_group_id WHERE 1=1 ";
		
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
						$sql .= "( a.firstname LIKE ?";
						$sql .= " OR a.lastname LIKE ?";
						$sql .= " OR a.email LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " a.".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " a.".$filter_name." = ? ";
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

	public function get_members_total($filter)
	{
		$sql = "SELECT count(member_id) as total FROM start_members a LEFT JOIN start_member_groups b ON a.member_group_id = b.member_group_id WHERE 1=1 ";
		
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
						$sql .= "( a.firstname LIKE ?";
						$sql .= " OR a.lastname LIKE ?";
						$sql .= " OR a.email LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
						
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " a.".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " a.".$filter_name." = ? ";
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
	
	public function add_member($data)
	{
		$insert = array(
			"firstname"			=> $data['firstname'],
			"lastname"			=> $data['lastname'],
			"email" 			=> $data['email'],
			"member_group_id"	=> $data['member_group_id']
		);

		$query = $this->db->insert('members', $insert);
		
		if($query)
		{
			return $this->db->insert_id();
		}
		
		return false;
	}
	
	public function get_member($member_id)
	{
		$sql = "SELECT a.firstname, a.lastname, a.email, b.member_group_id FROM start_members a LEFT JOIN start_member_groups b ON a.member_group_id = b.member_group_id WHERE a.member_id = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($member_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"firstname"			=> $result->firstname,
				"lastname"			=> $result->lastname,
				"email"				=> $result->email,
				"member_group_id"	=> $result->member_group_id,
				"signup_date"		=> time(),
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function get_member_by_email($email)
	{
		$sql = "SELECT a.member_id, a.firstname, a.lastname, a.email, b.member_group_id FROM start_members a LEFT JOIN start_member_groups b ON a.member_group_id = b.member_group_id WHERE a.email = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($email));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"member_id"			=> $result->member_id,
				"firstname"			=> $result->firstname,
				"lastname"			=> $result->lastname,
				"email"				=> $result->email,
				"member_group_id"	=> $result->member_group_id,
				"signup_date"		=> time(),
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function get_member_pages($member_id)
	{
		$sql = "SELECT page_id FROM start_pages WHERE member_id = ?";
		
		if($this->is_member_moderator($member_id))
		{
			//$sql .= " OR member_id = 0";
		}
		
		$query 	= $this->db->query($sql, array($member_id));
		
		if($query->num_rows() > 0)
		{
			$return		= array();
			$results 	= $query->result();
			foreach($results as $result)
			{
				$return[] = $result->page_id;
			}
			
			return $return;
		}
		return array();
	}
	
	public function get_member_categories($member_id)
	{
		$pages = $this->get_member_pages($member_id);
		
		if($pages)
		{	
			$pages = implode(',', $pages);
	
			$sql = "SELECT category_id FROM start_categories WHERE page_id IN ({$pages}) ";
			$query 	= $this->db->query($sql, array($member_id));
			
			if($query->num_rows() > 0)
			{
				$return		= array();
				$results 	= $query->result();
				foreach($results as $result)
				{
					$return[] = $result->category_id;
				}
				
				return $return;
			}
		}

		return false;
	}
	
	public function is_member_moderator($member_id)
	{
		$sql = "SELECT a.moderator FROM start_member_groups a LEFT JOIN start_members b ON a.member_group_id = b.member_group_id WHERE b.member_id = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($member_id));
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			if($row->moderator == 1)
			{
				return true;
			}
		}
		return false;
	}
	
	public function edit_member($data, $member_id)
	{
		$update = array(
			"firstname"			=> $data['firstname'],
			"lastname"			=> $data['lastname'],
			"email"				=> $data['email'],
			"member_group_id"	=> $data['member_group_id']
		);
		
		$query = $this->db->update('members', $update, array("member_id" => (int)$member_id) );
		
		return $query;
	}
	
	public function check_email($email, $member_id = array())
	{
		$sql = "SELECT member_id FROM start_members WHERE email = ?";
		$binds[] = $email;
		
		if(!is_array($member_id))
		{
			$sql .= " AND member_id != ?";
			$binds[] = $member_id;
		}
		
		$sql .= " LIMIT 1";

		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function insert_reset_token($member_id, $member_email, $ip, $token)
	{
		$this->db->delete('member_reset', array('member_id' => (int)$member_id));
		
		$insert = array(
			"member_id"		=> $member_id,
			"email"			=> $member_email,
			"ip"			=> $ip,
			"token"			=> $token,
			"timestamp"		=> time(),
		);

		$query = $this->db->insert('member_reset', $insert);
		
		return $query;
	}
	
	public function delete_member($member_id)
	{
		//delete member
		$this->db->delete('members', array('member_id' => (int)$member_id));
		
		//delete reset tokens
		$this->db->delete('member_reset', array('member_id' => (int)$member_id));
		
		//transfer all member owned pages to admin
		$update = array(
			"member_id"	=> 0,
		);
		
		$query = $this->db->update('pages', $update, array("member_id" => (int)$member_id) );
	}
	
	
	/* Member Groups */
	
	public function get_member_groups($filter, $limit)
	{
		$sql = "SELECT member_group_id, name, moderator FROM start_member_groups WHERE 1=1 ";
		
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
						$sql .= "( name LIKE ? )";
						$binds[] = '%'.$filter_value.'%';
						
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= $filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= $filter_name." = ? ";
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
	
	public function get_groups_total($filter)
	{
		$sql = "SELECT count(member_group_id) as total FROM start_member_groups WHERE 1=1 ";
		
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
						$sql .= "( name LIKE ? )";
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
	
	public function add_group($data)
	{
		$insert = array(
			"name"			=> $data['name'],
			"moderator"		=> $data['moderator'],
			"permissions"	=> $data['permissions'],
		);

		$query = $this->db->insert('member_groups', $insert);

		return $query;
	}
	
	public function get_group($group_id)
	{
		$sql = "SELECT name, permissions, moderator FROM start_member_groups WHERE member_group_id = ? LIMIT 1";
		$query 	= $this->db->query($sql, array($group_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){
		
			$return = array(
				"name"			=> $result->name,
				"moderator"		=> $result->moderator,
				"permissions"	=>	unserialize($result->permissions)
			);
			
			return $return;
		
		}
		return false;
	}
	
	public function edit_group($data, $member_group_id)
	{
		$update = array(
			"name"			=> $data['name'],
			"moderator"		=> $data['moderator'],
			"permissions"	=> $data['permissions']
		);
		
		$query = $this->db->update('member_groups', $update, array("member_group_id" => (int)$member_group_id) );
		
		return $query;
	}
	
	public function check_group($group_id)
	{
		$sql = "SELECT member_id FROM start_members WHERE member_group_id = ?";
		$query 	= $this->db->query($sql, array($group_id));

		if($query->num_rows() == 0){
			return true;
		}
		
		return false;
	}
	
	public function delete_group($group_id)
	{
		//delete group
		return $this->db->delete('member_groups', array('member_group_id' => (int)$group_id));
	}
}

?>
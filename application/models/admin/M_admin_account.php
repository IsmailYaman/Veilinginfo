<?php

class M_admin_account extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_account_details($member_id)
	{
		$sql = "SELECT password, firstname, lastname, email FROM start_members WHERE member_id = ?";
		
		$query = $this->db->query($sql, array($member_id));
		return $query->row();
	}

	
	public function check_members($email, $member_id)
	{
		$sql = "SELECT member_id FROM start_members WHERE email = ? AND member_id != ? LIMIT 1";
		$binds[] = $email;
		$binds[] = $member_id;

		$query = $this->db->query( $sql, $binds );
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function update_account($data, $member_id)
	{
		$update = array(
			"firstname"	=> $data['firstname'],
			"lastname"	=> $data['lastname'],
			"email"		=> $data['email']
		);
		
		if(!empty($data['password']))
		{
			$update['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
		
		$query = $this->db->update('members', $update, array("member_id" => (int)$member_id) );
		
		return $query;
	}
}

?>
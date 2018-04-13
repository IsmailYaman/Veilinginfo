<?php

class M_admin extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
        }

        public function get_member_data_by_email($email)
        {
			$query = $this->db->query("SELECT a.member_id, a.firstname, a.lastname, a.password, a.last_seen, b.member_group_id as member_group, b.permissions FROM start_members a LEFT JOIN start_member_groups b ON b.member_group_id = a.member_group_id WHERE a.email = ? LIMIT 1", array($email));

			return $query->row();
        }
		
		public function valid_email($email)
		{
			$sql = "SELECT member_id FROM start_members WHERE email = ? LIMIT 1";
			$binds[] = $email;

			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				$result = $query->row();
				return $result->member_id;
			}
			return false;
		}
		
        public function update_member_last_seen($member_id)
        {
			$data = array(
					'last_seen' => time()
			);

			$this->db->where('member_id', $member_id);
			$query = $this->db->update('members', $data);
			
			return $query;
        }
		
		public function check_reset_token($reset_token)
		{
			$sql = "SELECT member_id FROM start_member_reset WHERE token = ? LIMIT 1";
			$binds[] = $reset_token;
			
			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				$result = $query->row();
				return $result->member_id;
			}
			return false;
			
		}
		
		public function update_password($password, $member_id)
		{
			$this->db->delete('member_reset', array('member_id' => (int)$member_id));
			
			$data = array(
				'password' => password_hash($password, PASSWORD_DEFAULT)
			);

			$this->db->where('member_id', $member_id);
			$query = $this->db->update('members', $data);
			
			return $query;
		}

}

?>
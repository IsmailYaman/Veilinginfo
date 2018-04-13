<?php

class M_members extends CI_Model {

        public function __construct()
        {
                parent::__construct();
        }
		
		public function get_member_info($member_id)
		{ 
			$sql = "SELECT firstname, lastname, email FROM start_members WHERE member_id = ? LIMIT 1";
			$binds[] = $member_id;
			
			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				return $query->row();
			}
			return false;
		}
}
?>
<?php

class M_pages extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
        }

        public function get_all_pages($all=false)
        {
			$sql = " AND url != '' ";
			if($all)
			{
				$sql = "";
			}
			$query = $this->db->query("SELECT page_id, name, description, url FROM start_pages WHERE status = 1 {$sql} ORDER BY url ASC");
			return $query->result();
        }
		
		public function check_url($url)
		{ 
			$sql = "SELECT page_id FROM start_pages WHERE url = ? LIMIT 1";
			$binds[] = $url;
			
			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				return true;
			}
			return false;
		}
		
		public function add_page($data)
		{
	
			$insert = array(
				"url" 			=> $data['page'],
				"description" 	=> '',
				"name" 			=> ucfirst($data['page'])
			);

			$query = $this->db->insert('pages', $insert);
			
			if($query)
			{
				return $this->db->insert_id();
			}
			
			return false;
		}
		
		public function get_page_owner($page_id=false)
		{ 
			if(!$page_id)
			{
				return 0;
			}
			
			$sql = "SELECT member_id FROM start_pages WHERE page_id = ? LIMIT 1";
			$binds[] = $page_id;
			
			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				return $query->row()->member_id;
			}
			return 0;
		}
		
		public function get_page_info($page_id=false)
		{
			if(!$page_id)
			{
				return false;
			}
			
			$sql 	 = "SELECT description, name FROM start_pages WHERE page_id = ? LIMIT 1";
			$binds[] = $page_id;
			
			$query = $this->db->query( $sql, $binds );
			if($query->num_rows() == 1)
			{
				return $query->row();
			}
			return false;
		}

}
?>
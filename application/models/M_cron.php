<?php

class M_cron extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
        }

        public function get_new_messages($type=2, $notified=0)
        {
			$query = $this->db->query("SELECT d.page_id, d.member_id, a.message_id FROM start_messages a LEFT JOIN start_links b ON a.data_id = b.link_id LEFT JOIN start_categories c ON c.category_id = b.category_id LEFT JOIN start_pages d ON d.page_id = c.page_id WHERE a.status = 0 AND a.notified = ? AND a.type = ?", array($notified, $type));
			return $query->result();
        }
		
        public function get_new_messages_member($type=2, $notified=0, $member_id)
        {
			$query = $this->db->query("SELECT count(d.page_id) as total FROM start_messages a LEFT JOIN start_links b ON a.data_id = b.link_id LEFT JOIN start_categories c ON c.category_id = b.category_id LEFT JOIN start_pages d ON d.page_id = c.page_id WHERE a.status = 0 AND a.notified = ? AND a.type = ? AND d.member_id = ?", array($notified, $type, $member_id));
			if($query->num_rows() == 1)
			{
				$result = $query->row();
				return $result->total;
			}
			return false;
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

}
?>
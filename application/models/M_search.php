<?php

class M_search extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
        }

        public function get_all_links($search)
        {
			$bind_search = '%'.$search.'%';
			
			$query = $this->db->query("SELECT a.link_id, a.anchor, a.description, a.url, b.name as category, b.category_id FROM start_links as a LEFT JOIN start_categories as b ON a.category_id = b.category_id WHERE a.anchor LIKE ? OR a.description LIKE ? OR a.url LIKE ?", array($bind_search, $bind_search, $bind_search));
			
			$results_search = $query->result();
			
			$categories = array();
			foreach($results_search as $search)
			{
				$categories[$search->category_id][] = array(
					"anchor" 		=> $search->anchor,
					"description" 	=> $search->description,
					"url" 			=> $search->url,
					"category" 		=> $search->category,
				);
			}

			return $categories;
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
<?php

class M_categories extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
				//$this->output->enable_profiler(TRUE);
        }
	
		public function get_page_categories($page_id=0){
			$sql = "SELECT a.category_id, a.name as category FROM start_categories a LEFT JOIN start_pages b ON a.page_id = b.page_id WHERE a.page_id = ? AND b.status = 1";

			$query = $this->db->query($sql, array($page_id) );
			
			if($query->num_rows() > 0){
				
				$return = array();
				foreach($query->result() as $result ){
					$return[$result->category_id] = $result->category;
				}
				
				return $return;
			}
			
			return false;
		}		
		
		public function validate_category($page_id=0, $category_id=0){
			$sql = "SELECT category_id FROM start_categories WHERE page_id = ? AND category_id = ? LIMIT 1";

			$query = $this->db->query($sql, array($page_id, $category_id) );
			
			if($query->num_rows() == 1){
				return true;
			}
			return false;
		}

}

?>
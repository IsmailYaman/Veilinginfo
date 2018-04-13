<?php

class M_config extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
        }

        public function get($name)
        {
			$query = $this->db->query("SELECT value FROM start_settings WHERE name = ? LIMIT 1", array($name));
			if($query->num_rows() == 1)
			{
				return $query->row()->value;
			}
			return false;
        }
		
        public function current_language()
        {
			$query = $this->db->query("SELECT a.machine_name FROM start_languages a LEFT JOIN start_settings b ON b.value = a.language_id WHERE b.name = 'site_language' LIMIT 1");
			if($query->num_rows() == 1)
			{
				return $query->row()->machine_name;
			}
			return false;
        }
		
		

}

?>
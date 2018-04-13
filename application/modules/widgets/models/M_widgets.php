<?php

class M_widgets extends CI_Model {

        public function __construct()
        {
			// Call the CI_Model constructor
			parent::__construct();
        }

        public function get_active_modules()
        {
			$query = $this->db->query('SELECT name, machine_name, sort_order, column_row FROM start_modules WHERE active = 1 ORDER BY sort_order');
			return $query->result();
        }
}

?>
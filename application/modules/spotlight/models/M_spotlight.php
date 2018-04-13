<?php

class M_spotlight extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_spotlights_cards(){
		$query = $this->db->query("SELECT title, body, media, link FROM start_spotlight ORDER BY spotlight_id");
		return $query->result();
	}
}

?>
<?php

class M_messages extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
				//$this->output->enable_profiler(TRUE);
        }

		public function add_message($data)
		{
			$insert = array(
				"type" 		=> $data['type'],
				"data_id" 	=> $data['data_id'],
				"firstname" => $data['firstname'],
				"lastname" 	=> $data['lastname'],
				"email" 	=> $data['email'],
				"message" 	=> $data['message'],
				"status" 	=> 0,
				"timestamp" => time(),
			);
			
			$query = $this->db->insert('messages', $insert);
			
			return $query;
		}


}

?>
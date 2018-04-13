<?php

class M_links extends CI_Model {

        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
				//$this->output->enable_profiler(TRUE);
        }

		public function add_link($data)
		{
			
			$expires = array(
				"never" 	=> time()+3153600000, 
				"1day" 		=> time()+86400, 
				"1week" 	=> time()+604800, 
				"1month" 	=> time()+2592000, 
				"1year" 	=> time()+31536000, 
				"5years" 	=> time()+157680000, 
				"10years" 	=> time()+315360000, 
				"custom" 	=> strtotime($data['custom_expire_date']), 
			);
			
			if(!isset($data['no_follow'])){
				$data['no_follow'] = 0;
			}
			
			$insert = array(
				"category_id" 	=> $data['category_id'],
				"anchor" 		=> $data['anchor'],
				"url" 			=> $data['url'],
				"backlink" 		=> $data['backlink'],
				"no_follow" 	=> 0,
				"email" 		=> '',
				"sort_order" 	=> 0,
				"premium" 		=> 0,
				"creation_date" => time(),
				"expire_date" 	=> 3153600000
			);
			
			$query = $this->db->insert('links', $insert);
			
			if($query)
			{
				return $this->db->insert_id();
			}
			
			return false;
		}


}

?>
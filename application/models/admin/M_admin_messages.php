<?php

class M_admin_messages extends CI_Model {

	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function get_messages($filter, $limit, $status, $pages, $is_admin)
	{
		$sql   = "SELECT a.message_id, a.firstname, a.lastname, a.email, a.message, a.type, a.data_id FROM start_messages a";
		$sql  .= " LEFT JOIN start_links b ON b.link_id = a.data_id LEFT JOIN start_categories c ON c.category_id = b.category_id";
		$sql  .= " WHERE a.status = ? AND c.page_id IN ({$pages}) AND a.type = 2";

		$binds = array();
		
		$binds[] = $status;
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	

				if(!empty($filter_value) || is_numeric($filter_value))
				{
					if($filter_name == "search")
					{
						$sql .= " AND ( concat_ws(' ',a.firstname,a.lastname) LIKE ? ";
						$sql .= " OR a.email LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					}elseif($filter_name == "name"){
						$sql .= " AND concat_ws(' ',a.firstname,a.lastname) LIKE ?";
						$binds[] = '%'.$filter_value.'%';
					} else {
						if(!ctype_digit($filter_value)){
							$sql .= " AND  a.".$filter_name." LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							$sql .= " AND  a.".$filter_name." = ? ";
							$binds[] = $filter_value;
						}
					}	
				}
			}

		}
		
		if($is_admin)
		{
			$sql .= " UNION ";
			
			$sql  .= " SELECT a.message_id, a.firstname, a.lastname, a.email, a.message, a.type, a.data_id FROM start_messages a";
			$sql  .= " WHERE a.status = ? AND a.type = 1";
			
			$binds[] = $status;
			
			//run filter again
			if(isset($filter))
			{
				foreach($filter as $filter_name=>$filter_value)
				{	

					if(!empty($filter_value) || is_numeric($filter_value))
					{
						if($filter_name == "search")
						{
							$sql .= " AND ( concat_ws(' ',firstname,lastname) LIKE ? ";
							$sql .= " OR a.email LIKE ? )";
							
							$binds[] = '%'.$filter_value.'%';
							$binds[] = '%'.$filter_value.'%';
						}elseif($filter_name == "name"){
							$sql .= " AND concat_ws(' ',firstname,lastname) LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							if(!ctype_digit($filter_value)){
								$sql .= " AND  ".$filter_name." LIKE ?";
								$binds[] = '%'.$filter_value.'%';
							} else {
								$sql .= " AND  ".$filter_name." = ? ";
								$binds[] = $filter_value;
							}
						}	
					}
				}

			}
			
		}
		
		if(isset($limit))
		{
			$sql .= " LIMIT ".$limit['start'].",".$limit['max'];
		}
		
		$query = $this->db->query($sql, $binds);
		
		$return = array();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $result)
			{
				if($result->type == 1) //page
				{
					$sql_type = "SELECT url as data_link, name as data_name FROM start_pages WHERE page_id = ?";
				}		

				if($result->type == 2) //link
				{
					$sql_type = "SELECT url as data_link, anchor as data_name FROM start_links WHERE link_id = ?";
				}			

				if($result->type < 3)
				{
					$query_type	= $this->db->query($sql_type, array($result->data_id));
					$result_type = $query_type->row();
				}

				$return[] = array(
					'data_id' => $result->data_id,
					'message_id' => $result->message_id,
					'firstname' => $result->firstname,
					'lastname' => $result->lastname,
					'email' => $result->email,
					'message' => $result->message,
					'type' => $result->type,
					'data_link' => isset($result_type->data_link) ? $result_type->data_link : '',
					'data_name' => isset($result_type->data_name) ? $result_type->data_name : '',
				);
			}
		}

		return $return;
	}
	
	public function get_messages_total($filter, $status, $pages, $is_admin)
	{
		$sql   = "SELECT count(a.message_id) as total FROM start_messages a";
		$sql  .= " LEFT JOIN start_links b ON b.link_id = a.data_id LEFT JOIN start_categories c ON c.category_id = b.category_id";
		$sql  .= " WHERE a.status = ? AND c.page_id IN ({$pages}) AND a.type = 2";

		$binds = array();
		
		$binds[] = $status;
		
		if(isset($filter))
		{
			foreach($filter as $filter_name=>$filter_value)
			{	
				if(!empty($filter_value) || is_numeric($filter_value))
				{

					if($filter_name == "search")
					{
						$sql .= " AND ( concat_ws(' ',a.firstname,a.lastname) LIKE ? ";
						$sql .= " OR a.email LIKE ? )";
						
						$binds[] = '%'.$filter_value.'%';
						$binds[] = '%'.$filter_value.'%';
					}elseif($filter_name == "name"){
						$sql .= " AND concat_ws(' ',a.firstname,a.lastname) LIKE ?";
						$binds[] = '%'.$filter_value.'%';
					} else {
						$sql .= " AND a.".$filter_name." = ? ";
						$binds[] = $filter_value;
					}	
				}
			}
			
		}

		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, $binds);
		$result = $query->row();
		
		if($is_admin)
		{
			$sql  = "SELECT count(a.message_id) as total FROM start_messages a";
			$sql  .= " WHERE a.status = ? AND a.type = 1";
			
			$binds = array();
			
			$binds[] = $status;
			
			//run filter again
			if(isset($filter))
			{
				foreach($filter as $filter_name=>$filter_value)
				{	

					if(!empty($filter_value) || is_numeric($filter_value))
					{
						if($filter_name == "search")
						{
							$sql .= " AND ( concat_ws(' ',firstname,lastname) LIKE ? ";
							$sql .= " OR email LIKE ? )";
							
							$binds[] = '%'.$filter_value.'%';
							$binds[] = '%'.$filter_value.'%';
						}elseif($filter_name == "name"){
							$sql .= " AND concat_ws(' ',firstname,lastname) LIKE ?";
							$binds[] = '%'.$filter_value.'%';
						} else {
							if(!ctype_digit($filter_value)){
								$sql .= " AND  ".$filter_name." LIKE ?";
								$binds[] = '%'.$filter_value.'%';
							} else {
								$sql .= " AND  ".$filter_name." = ? ";
								$binds[] = $filter_value;
							}
						}	
					}
				}

			}
			
			$sql .= " LIMIT 1";
			
			$query 	 = $this->db->query($sql, $binds);
			$result2 = $query->row();
			
			return ($result->total + $result2->total);
			
		}
		
		return $result->total;
	}
	
	public function get_message($message_id, $pages, $is_admin)
	{
		$sql  = "SELECT a.firstname, a.lastname, a.email, a.type, a.data_id, a.status FROM start_messages a";
		$sql .= " LEFT JOIN start_links b ON b.link_id = a.data_id LEFT JOIN start_categories c ON c.category_id = b.category_id";
		$sql .= " WHERE a.message_id = ? AND c.page_id IN ({$pages}) AND a.type = 2";
		$sql .= " LIMIT 1";
		
		$query 	= $this->db->query($sql, array($message_id));
		$result = $query->row();
		
		if($query->num_rows() > 0){

			$return = array(
				"firstname"	=> $result->firstname,
				"lastname"	=> $result->lastname,
				"email"		=> $result->email,
				"type"		=> $result->type,
				"data_id"	=> $result->data_id,
				"status"	=> $result->status
			);
			
			return $return;
		
		} else {

			if($is_admin)
			{
				$sql  = "SELECT a.firstname, a.lastname, a.email, a.type, a.data_id, a.status FROM start_messages a";
				$sql .= " WHERE a.message_id = ? AND a.type = 1";
				$sql .= " LIMIT 1";
				
				$query 	= $this->db->query($sql, array($message_id));
				$result = $query->row();
				
				if($query->num_rows() > 0){
					
					$return = array(
						"firstname"	=> $result->firstname,
						"lastname"	=> $result->lastname,
						"email"		=> $result->email,
						"type"		=> $result->type,
						"data_id"	=> $result->data_id,
						"status"	=> $result->status
					);
					
					return $return;
					
				}
				
			}
			
		}
		
		return false;
	}
	
	public function approve_message($message_id)
	{
		$update = array(
			"status"	=> 1
		);
		
		$query = $this->db->update('messages', $update, array("message_id" => (int)$message_id) );
		
		return $query;
	}
	
	public function delete_message($message_id)
	{
		$del = $this->db->delete('messages', array('message_id' => (int)$message_id));
		return (bool)$del;
	}
}

?>
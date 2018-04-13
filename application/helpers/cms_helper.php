<?php

	function CurrentPage()
	{
		$CI 		  = &get_instance();
		$current_page = 1;
		$current 	  = '';
		$page_exist   = true;
		list($subdomain, $rest) = explode('.', $_SERVER['SERVER_NAME'], 2);
		if(isset($subdomain) && $subdomain != 'www' && $CI->config->item('http_host') != $_SERVER['SERVER_NAME']){

			$query = $CI->db->query("SELECT page_id FROM start_pages WHERE url = ? AND status = 1", array($subdomain));
	
			if($query->num_rows() > 0){
				$row 		  = $query->row();
				$current_page = $row->page_id;
				$current 	  = $subdomain;
			} else {
				$page_exist   = false;
			}
		
		}

		$return['exist'] 	= $page_exist;
		$return['current']  = $current_page;
		$return['subdomain']  = $current;
		
		return $return;
	}
	
	function Is_Subdomain()
	{
		$CI	= &get_instance();
		list($subdomain, $rest) = explode('.', $_SERVER['SERVER_NAME'], 2);
		if(isset($subdomain) && $subdomain != 'www' && $CI->config->item('http_host') != $_SERVER['SERVER_NAME']){
			return true;
		}
		return false;
	}
	
	function format_page_url($page=false)
	{
		$CI	= &get_instance();
		$url  = $CI->config->item('ssl_on') ? 'https://' : 'http://';
		if($page)
		{
			$url .= $page.'.';
		}
		$url .= $CI->config->item('http_host');
		
		return $url;
	}
	
	function url_alias($query)
	{
		$CI	= &get_instance();
		
		$query = $CI->db->query("SELECT a.keyword FROM start_url_alias a LEFT JOIN start_settings b ON a.language_id = b.value WHERE a.query = ? AND b.name = 'site_language' ", array($query));
		if($query->num_rows() > 0){
			return $query->row()->keyword;
		}
		return false;
	}
	
	function clean_output($str, $multi_array = FALSE)
	{
		if(is_array($str)){
			$str_a = array();
			foreach($str as $k=>$o)
			{
				if(is_array($o)){
					
					foreach($o as $t=>$e)
					{
						$str_a[$k][$t] = htmlentities($e);
					}
					
				} else {
					$str_a[$k] = htmlentities($o);
				}
				
			}
			return $str_a;
		}
		return htmlentities($str);
	}
	
	function clean_input($str)
	{
		return strip_tags($str);
	}
	
	function captch_verify($response)
	{
		$CI	= &get_instance();
		$secret = $CI->config->item('recaptcha_secret');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("response" => $response, "secret" => $secret)) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$server_output = curl_exec($ch);
		if(!$server_output)
		{
			return false;
		}
		curl_close ($ch);
		return json_decode($server_output);
	}

?>
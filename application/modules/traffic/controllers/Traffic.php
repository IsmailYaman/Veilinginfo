<?php
class Traffic extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function display_data($data = NULL)
	{
		$traffic_info 	= $this->_get_traffic();
		$data['traffic_info'] 	= array();
		$c=0;
		$max=5;		if($traffic_info['roadEntries'])		{
			foreach($traffic_info['roadEntries'] as $info)
			{
				if($c < $max)
				{
					$data['traffic_info'][] = $info;
				}
				$c++;
			}		}
		return $this->load->view('traffic/traffic_v', $data, true);
	}	
	public function _get_traffic()
	{
		$dataSource = sprintf("https://www.anwb.nl/feeds/gethf");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $dataSource);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($curl); 
		curl_close($curl); 
		$data = json_decode($result, true);
		if(!$data){
			return false;
		}
		return $data;
	}
}
<?php

class Weather extends MY_Controller
{
	private $AppID;
	private $weatherLocation;
	
	public function __construct()
	{
		parent::__construct();
		$this->AppID 			= "dd0510f0b9a2bd9a445ddd1523f9154f";
		$this->weatherLocation  = "utrecht,netherlands";
	}
	
	public function display_data($data = NULL)
	{
		$weatherData['current'] 	= $this->_get_weather("weather");
		$weatherData['forecast'] 	= $this->_get_weather("forecast/daily");

		$data['current_temp']		= isset($weatherData['current']['main']['temp']) ? round($weatherData['current']['main']['temp'], 1) : 0;
		$data['current_wind'] 		= isset($weatherData['current']['wind']['speed']) ? round($weatherData['current']['wind']['speed'], 0) : 0;
		
		$data['forecast_temp_min'] 	= isset($weatherData['forecast']['list'][0]['temp']['min']) ? round($weatherData['forecast']['list'][0]['temp']['min'], 0) : 0;
		$data['forecast_temp_max'] 	= isset($weatherData['forecast']['list'][0]['temp']['max']) ? round($weatherData['forecast']['list'][0]['temp']['max'], 0) : 0;

		return $this->load->view('weather/weather_v', $data, true);
	}

	public function _get_weather($type)
	{
		$dataSource = sprintf("http://api.openweathermap.org/data/2.5/%s?q=%s&units=metric&mode=json&APPID=%s", $type, $this->weatherLocation, $this->AppID);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $dataSource);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($curl); 
		curl_close($curl); 
		$data = json_decode($result, true);
		if($data['cod'] == 401){
			return false;
		}
		return $data;
	}
}
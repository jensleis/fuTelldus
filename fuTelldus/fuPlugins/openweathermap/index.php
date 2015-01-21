<?php
namespace openweathermap\openweathermap;
/*
 Needs:

*/
	function activateHook() {
		// return an array with the description, and all fields needed for determining to correct sensor state
		return getConfigArray();
	}
	
	function disableHook() {
		// nothing todo for this plugin
	}
	
	function updateHook() {
		return getConfigArray();
	}
	
	function getConfigArray() {
		return $configs = array(
				array('key' => 'date',
						'type' => 'return',
						'description' => 'Date'),
				array('key' => 'day_min',
						'type' => 'return',
						'description' => 'Daily min temperature'),
				array('key' => 'day_max',
						'type' => 'return',
						'description' => 'Daily max temperature'),
				array('key' => 'windchill_temp',
						'type' => 'return',
						'description' => 'Windchill temperature'),				
				array('key' => 'weather_description',
						'type' => 'return',
						'description' => 'Weather description'),				
				array('key' => 'weather_icon',
						'type' => 'return',
						'description' => 'Weather icon'),
				array('key' => 'wind_speed',
						'type' => 'return',
						'description' => 'Wind speed'),
				array('key' => 'wind_direction',
						'type' => 'return',
						'description' => 'Wind direction'),
				array('key' => 'language',
						'type' => 'text',
						'description' => 'Language code'),				
				array('key' => 'latitude',
						'type' => 'text',
						'description' => 'Latitude'),				
				array('key' => 'longitude',
						'type' => 'text',
						'description' => 'Longitude'),
				array('key' => 'forecast_period',
						'type' => 'callBackMethodReturnList;listKnownPeriods',
						'description' => 'ForecastPeriod')
		);
	}
	
	function listKnownPeriods($params) {
		$returnVal = array();

		array_push($returnVal, array('id' => 1, 'name' => "Today"));
		array_push($returnVal, array('id' => 2, 'name' => "Tomorrow"));
		array_push($returnVal, array('id' => 3, 'name' => "Day after tomorrow"));
		array_push($returnVal, array('id' => 4, 'name' => "Today + 3"));
		array_push($returnVal, array('id' => 5, 'name' => "Today + 4"));
		array_push($returnVal, array('id' => 6, 'name' => "Today + 5"));
		array_push($returnVal, array('id' => 7, 'name' => "Today + 6"));
	
		return $returnVal;
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$temperature = $lastLogValues['temperature'];
		$humidity = $lastLogValues['humidity'];

		$widget = "";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
					if (isset($temperature) && strlen($temperature) > 0) {
						$widget.= "<img src='". $myPath."/thermometer.png' alt='icon' />";
						$widget.= "".$temperature."&deg;&nbsp;";
					}
					if (isset($humidity) && strlen($humidity) > 0) {
						$widget.= "<img src='". $myPath."/water.png' alt='icon' />";
						$widget.= "".$humidity."%&nbsp;";
					}
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$timeUpdatedByInsertedLog = time();
				}
				
				$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."'>".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
			$widget.= "</div>";	
		
		return $widget;
	}
		
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$resultJson = sendGETCommandToOpenWeatherMap($parameter);

		$result = json_decode($resultJson);
		$forecastIndex = $parameter['forecast_period']['value'] - 1;
		$resultForecast = ($result->list[$forecastIndex]);

		$returnValArr = array();

		$date = $resultForecast->dt;
		if (isset($date)) {
			$returnValArr['date']=date_stringDate($date, $parameter['language']['value']);
		}
		
		$day_min = $resultForecast->temp->min-273.15;
		if (isset($day_min)) {
			$returnValArr['day_min']=number_format(round($day_min, 1), 1, '.', '');
		}
		
		$day_max  = $resultForecast->temp->max-273.15;
		if (isset($day_max)) {
			$returnValArr['day_max']=number_format(round($day_max, 1), 1, '.', '');
		}
		
		$weather_description = $resultForecast->weather[0]->description;
		if (isset($weather_description)) {
			$returnValArr['weather_description']=$weather_description;
		}
		
		$weather_icon = $resultForecast->weather[0]->icon;
		if (isset($weather_icon)) {
			$returnValArr['weather_icon']=convertWeatherIcon($weather_icon);
		} /*<i class="wi wi-day-lightning"></i>*/
		
		$wind_speed = $resultForecast->speed;
		if (isset($wind_speed)) {
			$returnValArr['wind_speed']=round($wind_speed*3.6, 0); //km/h
		}
		
		$wind_direction = $resultForecast->deg;
		if (isset($date)) {
			$returnValArr['wind_direction']=$wind_direction;
		}

		$windchill_temp = $resultForecast->temp->day-273.15;
		if (isset($windchill_temp)) {
			//W = 13,12 + 0,6215 x T – 11,37 x V0,16 + 0,3965 x T x V0,16
			$windchill_temp_calced1 = 0.6215*$windchill_temp; // 0,6215 x T
			$windchill_temp_calced2 = 11.37*pow($wind_speed, 0.16); //11,37 x V0,16
			$windchill_temp_calced3 = 0.3965*$windchill_temp*pow($wind_speed, 0.16); //0,3965 x T x V0,16
			$windchill_temp_calced = 13.12 + $windchill_temp_calced1 - $windchill_temp_calced2 + $windchill_temp_calced3;

			$returnValArr['windchill_temp']=number_format(round($windchill_temp_calced, 1), 1, '.', '');
		}
		
		return $returnValArr;
	}
	
	function convertWeatherIcon($orgIcon) {
		
		$icons = array (
				'01d' => 'wi-day-sunny',  //clear sky
				'02d' => 'wi-day-sunny-overcast',  //few clouds
				'03d' => 'wi-day-cloudy',  //scattered clouds
				'04d' => 'wi-cloudy',  //broken clouds
				'09d' => 'wi-day-showers',  //shower rain
				'10d' => 'wi-day-rain',  //rain
				'11d' => 'wi-day-thunderstorm',  //thunderstorm
				'13d' => 'wi-day-snow',  //snow
				'50d' => 'wi-fog',  //mmist
				
				'01n' => 'wi-night-clear',
				'02n' => 'wi-night-cloudy',
				'03n' => 'wi-night-cloudy',
				'04n' => 'wi-cloudy',
				'09n' => 'wi-night-showers',
				'10n' => 'wi-night-rain',
				'11n' => 'wi-night-thunderstorm',
				'13n' => 'wi-night-snow',
				'50n' => 'wi-fog',		
		);
		
		$icon = $icons[$orgIcon];
		
		return "<i class='wi ".$icon."'></i>";
	}
	
	function sendGETCommandToOpenWeatherMap($parameter) {
		$lat = $parameter['latitude']['value'];
		$long = $parameter['longitude']['value'];
		$lang = $parameter['language']['value'];
		$period = $parameter['forecast_period']['value'];
		$url = "http://api.openweathermap.org/data/2.5/forecast/daily?lat=".$lat."&lon=".$long."&cnt=".$period."&lang=".$lang;
	
		// create and send the HTTPPost Request
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$result=curl_exec($ch);
	
		return $result;
	}
	
	// return true if the data should grouped according to the 
	// time range selected in the chart to get a better performance
	function groupChartData() {
		return true;
	}

?>

<!--<script src="../../lib/packages/jquery/jquery-1.9.1.min.js"></script>
<script src="../../lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="../../lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script> 
<script src="../../lib/jscripts/jquery.bootstrap.confirm.popover.js"></script>
<script src="../../lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
<link href="../../lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">
<script src="../../lib/packages/timeago_jquery/jquery.timeago.js"></script>
<script src="../../lib/packages/jquery_csv/jquery_csv-0_71.min.js"></script>-->

<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	
	// get parameter
	$data = clean($_GET['data']);
	
	$tempColor = "#6698FF";
	$humiColor = "#99C68E";
	
	/* Generate the chart
	-------------------------------------------------------*/
	$text = "";
	$text = generateSceneChart($id, $data);
	
	echo $text;
	echo "<div id='container' style='height: 500px; min-width: 500px'></div>";

	function generateSceneChart($id, $data) {
		$scenes = json_decode($data);

		
		//$axisArray = getVirtualSensorChartAxis($id);
		$yaxis = "";
		$series = "";
		$callback = "";
		$axisInfo = array();
		$position = 0;
		foreach ($scenes as $scene) {
			$sensorID = $scene->id;
			$sensorType = $scene->type;
			$sensorName = $scene->name;
			$yAxisPosition = "yAxis: ".$position.",";
			
			if ($sensorType == 'virtual') {	
				$axisArray = getVirtualSensorChartAxis($sensorID);
				foreach ($axisArray as $axisConfig) {
					$yAxisPosition = "yAxis: ".$position.",";
					$description = $axisConfig[1];
					$suffix = $axisConfig[2];
					
					$opposide = "";
					if (($position % 2) == 1) { 
						$opposide = "opposite: true,";
					} 
				  
					$yaxis .= "{
							labels: {
								format: '{value}$suffix',
							},
							
							$opposide
						}, ";
					$seriesPlugin = getVirtualSensorSeriesDisplay($sensorID);
					$series .= "{
							name: '$description',
							data : data[$position],
							dataGrouping: {
								enabled: false
							},
							type: 'spline',
							tooltip: {
								valueSuffix: '$suffix'
							},
							".$yAxisPosition." 
							".$seriesPlugin." 
						}, ";
					$callback .= "
						chart.series[$position].setData(data[$position]);";
					
					$position++;
				}
			}
			
			if ($sensorType == 'device') {	
				$yaxis .= "{
						labels: {
							formatter: function() {
								if (this.value==1){
									return 'on';
								} else if (this.value==0){
									return 'off';
								}
							},
							align: 'right',
							x:-10,
							y:5,
						},
					}, ";
				$series .= "{
						name: '$sensorName',
						data : data[$position],
						dataGrouping: {
							enabled: false
						},
						$yAxisPosition
						type: 'area',
						tooltip: {
							formatter: function(){
								if (data.label==1){
									return 'on';
								} else {
									return 'off';
								}
							},
						},		
					}, ";
				$callback .= "
					chart.series[$position].setData(data[$position]);";
					
				$position++;
			}
			
			if ($sensorType == 'sensor') {	
				// sensor has two values, temp and humidity
				$sensorAxis = array("Temperature" => "\u00B0C", "Humidity"=>" %");
				foreach ($sensorAxis as $sensorSubName => $suffix) {
					$yAxisPosition = "yAxis: ".$position.",";
					
					$opposide = "";
					if (($position % 2) == 0) { 
						$opposide = "opposite: true,";
					} 
				  
					$yaxis .= "{
							labels: {
								format: '{value}$suffix',
							},
							
							$opposide
						}, ";
					$series .= "{
							name: '$sensorSubName $sensorName',
							data : data[$position],
							dataGrouping: {
								enabled: false
							},
							type: 'spline',
							tooltip: {
								valueSuffix: '$suffix'
							},
							$yAxisPosition
						}, ";
					$callback .= "
						chart.series[$position].setData(data[$position]);";
					
					$position++;
				}
			}
		}
		
		$axisInfo['yaxis'] = $yaxis;
		$axisInfo['series'] = $series;
		$axisInfo['callback'] = $callback;
		return getSceneJavaScript($data, $axisInfo);
	}
	
	function getSceneJavaScript($data, $axisInfo) {
		$script = "";
		$script .= "
				<script type='text/javascript'>
				$(function() {
					Highcharts.setOptions({
						global: {
							useUTC: false
						}
					});
					// See source code from the JSONP handler at https://github.com/highslide-software/highcharts.com/blob/master/samples/data/from-sql.php
					var startTime=1;
					
					//$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?type=scene&data={$data}&start=1371757044000&callback=?', function(data) {
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?data={$data}&start='+startTime+'&callback=?', function(data) {
						
								
						// create the chart
						$('#container').highcharts('StockChart', {
							chart : {
								
								zoomType: 'x'
							},
							
							navigator : {
								adaptToUpdatedData: false,
								series : {
									data : data[0]
								}
							},
							
							legend: {
								enabled: true,
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -10,
								y: 100,
								borderWidth: 0,
								itemStyle: {
									width: 90
								},
							},

							scrollbar: {
								liveRedraw: false
							},
							
							rangeSelector : {
								buttons: [{
									type: 'hour',
									count: 1,
									text: '1h'
								}, {
									type: 'day',
									count: 1,
									text: '1d'
								}, {
									type: 'week',
									count: 1,
									text: '1w'
								},{
									type: 'month',
									count: 1,
									text: '1m'
								}, {
									type: 'year',
									count: 1,
									text: '1y'
								}, {
									type: 'all',
									text: 'All'
								}],
								inputEnabled: false, // it supports only days
								selected : 5 // all
							},
							
							series: [";
		
		// add series from array;
		$script .= $axisInfo['series'];
								
		$script .="					],
							yAxis: [";
							
		// add yaxis from array
		$script .= $axisInfo['yaxis'];
								
		$script .= "					],
							
							xAxis : {
								type: 'datetime',
								ordinal: false,
								events : {
									afterSetExtremes : afterSetExtremes
								},
								minRange: 3600 // one hour
							},
						});
					});
				});

				/**
				 * Load new data depending on the selected min and max
				 */
				function afterSetExtremes(e) {

					var url,
						currentExtremes = this.getExtremes(),
						range = e.max - e.min;
					var chart = $('#container').highcharts();
					chart.showLoading('Loading data from server...');
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?data={$data}&start='+ Math.round(e.min) +
							'&end='+ Math.round(e.max) +'&callback=?', function(data) {";
		
		// add callback from array
		$script .= $axisInfo['callback'];
					
		$script .= "		chart.hideLoading();
					});
				}
				</script> ";
		return $script;
	}
	

?>

<?php 

$device_id = clean($_GET['device']);

$query = " SELECT * FROM ".$db_prefix."displays
			WHERE display_id='{$device_id}'
			";

$result = $mysqli->query($query);
$deviceResult = $result->fetch_assoc();

?>

<script type="text/javascript">
var timerID;
var xhr;

window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
	$("#wait").hide();
	
	$("#sensorName").html("Error");
	$("#sensorHTML").html("<div style='text-align:center'>An error occured. The request will be sent again in 30 seconds.<br /><br />"+errorMsg+"</div>");
	
	timerID = setTimeout('refreshCurrentPage();', 30000);
    return false;
}

$(function() {
	loadNextPage();

	calculateSensorHTMLHeight();
	
	$("#refreshAfter").change( function() {
		var refreshAfter = $("#refreshAfter").val()*1000;
// 		var showForLeft = $("#showForLeft").val()*1000;

		var showForLeft = $("#showForLeft").val() * 1000;
		showForLeft = showForLeft - refreshAfter;
		$("#showForLeft").val(showForLeft/1000);
		
		if (refreshAfter<0) {
			timerID = setTimeout('loadNextPage();', showForLeft);
		} else {
		
			if ((showForLeft!=null && showForLeft>0)) {
				if (showForLeft<refreshAfter) {
					timerID = setTimeout('refreshCurrentPage();', showForLeft);
				} else {
					timerID = setTimeout('refreshCurrentPage();', refreshAfter);
				}
			} else {
				timerID = setTimeout('loadNextPage();', refreshAfter);
			}
		}
		
	});


	$("#nextPage").click(function () {
		clearDisplayForAction();
		loadNextPage();
	});

	$("#prevPage").click(function () {
		clearDisplayForAction();
		loadPrevPage();
	});
});

function clearDisplayForAction() {
	clearTimeout(timerID);
	abortRunningCall();
	
	$("#sensorName").hide();
	$("#sensorHTML").hide();

	calculateSensorHTMLHeight();
}

function abortRunningCall() {
	$("#wait").hide();
	if (xhr!=null) {
		try {
			xhr.abort();
		} catch (error) {
		}
	}
}

function calculateSensorHTMLHeight() {
	var contentHeight = $("#sensorContent").outerHeight();
	var nameHeight = $("#sensorNameRow").outerHeight();
	var buttonRowHeight = $("#buttonRow").outerHeight();
	var newHtmlHeight = contentHeight - nameHeight - buttonRowHeight;
	
	$("#sensorHTMLRow").height(newHtmlHeight);

// 	$("#nextPage").css("left", ($("#prevPage").width()));
	
}

function refreshCurrentPage() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();


	xhr = $.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=sensor&pageaction=current&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		$("#sensorName").html(jsonResult.description);
		$("#sensorHTML").html(jsonResult.html);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		calculateSensorHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function loadNextPage() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=sensor&pageaction=next&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		
		$("#sensorName").html(jsonResult.description);
		$("#sensorHTML").html(jsonResult.html);
		
		$("#currentPageID").val(jsonResult.page_id);
		$("#showForLeft").val(jsonResult.showFor);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		$("#sensorName").show();
		$("#sensorHTML").show();
		
		
		calculateSensorHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function loadPrevPage() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=sensor&pageaction=prev&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		
		$("#sensorName").html(jsonResult.description);
		$("#sensorHTML").html(jsonResult.html);
		
		$("#currentPageID").val(jsonResult.page_id);
		$("#showForLeft").val(jsonResult.showFor);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		$("#sensorName").show();
		$("#sensorHTML").show();
		
		
		calculateSensorHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}
</script>

<input type="hidden" id="refreshAfter" value="" />
<input type="hidden" id="showForLeft" value="0" />
<input type="hidden" id="currentPageID" value="" />

<div id="sensorContent" style="height:100%;">
	<div class="row" id="sensorNameRow">
		<div class="col-xs-12 col-md-12">
			<h4 id="sensorName"></h4>
		</div>
	</div>
	<div class="row" style="" id="sensorHTMLRow">
			<div class="col-xs-12 col-md-12" style="height:100%;text-align:left;margin:0px;padding:0px;">
				<div class="container-fluid" id="sensorHTML" style="height:100%;max-height:100%">
				</div>
			</div>
	</div>
	<div class="row" id="buttonRow" style="padding-bottom:10px">
		<i id='prevPage' class='icon-chevron-left' style='margin-right:15px;cursor: pointer;padding-left:30px;padding-right:30px'></i>
		<i id='nextPage' class='icon-chevron-right' style='margin-right:15px;cursor: pointer;padding-left:30px;padding-right:30px'></i>
	</div>
</div>
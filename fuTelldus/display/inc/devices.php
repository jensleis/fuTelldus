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

window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
	$("#wait").hide();
	
	$("#deviceName").html("Error");
	$("#deviceHTML").html("<div style='text-align:center'>An error occured. The request will be sent again in 30 seconds.<br /><br />"+errorMsg+"</div>");
	
	timerID = setTimeout('refreshCurrentPage();', 30000);
    return false;
}

$(function() {
	getNextDevice();
	
	$("#refreshAfter").change( function() {
		var refreshAfter = $("#refreshAfter").val()*1000;
		if (refreshAfter>0) {
			timerID = setTimeout('refreshCurrentPage();', refreshAfter);
		} 
		
	});

	$("#nextPage").click(function () {
		getNextDevice();
	});

	$("#prevPage").click(function () {
		getPrevDevice();
	});
});

function refreshCurrentPage() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();


	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=current&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		$("#deviceName").html(jsonResult.description);
		$("#deviceHTML").html(jsonResult.html);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();
		
		calculateDeviceHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}


function getNextDevice() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=next&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		
		$("#deviceName").html(jsonResult.description);
		$("#deviceHTML").html(jsonResult.html);
		
		$("#currentPageID").val(jsonResult.page_id);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();
		
		calculateDeviceHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function getPrevDevice() {
	clearTimeout(timerID);
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=prev&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		
		$("#deviceName").html(jsonResult.description);
		$("#deviceHTML").html(jsonResult.html);
		
		$("#currentPageID").val(jsonResult.page_id);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		calculateDeviceHTMLHeight();
		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function calculateDeviceHTMLHeight() {
	var contentHeight = $("#sensorContent").outerHeight();
	var nameHeight = $("#deviceNameRow").outerHeight();
	var newHtmlHeight = contentHeight - nameHeight;

	$("#deviceHTMLRow").height(newHtmlHeight);

	$("#nextPage").css("left", ($("#prevPage").width()));
	
}

</script>

<input type="hidden" id="refreshAfter" value="" />
<input type="hidden" id="currentPageID" value="" />

<div id="sensorContent" style="height:100%;">
	<div class="row" id="deviceNameRow">
		<div class="col-xs-12 col-md-12">
			<h4 id="deviceName"></h4>
		</div>
	</div>
	<div class="row" style="" id="deviceHTMLRow">
<!--  		<div class="overlay" id="prevPage" style="width:50%">
            </div>-->
<!--             <div class="overlay" id="nextPage" style="width:50%"> -->
<!--             </div> -->
			<div class="col-xs-12 col-md-12" style="height:100%;text-align:left;margin:0px;padding:0px;">
				<div class="container-fluid" id="deviceHTML" style="height:100%;max-height:100%">
				</div>
			</div>
	</div>
</div>
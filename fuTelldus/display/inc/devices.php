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
	$("#waitSwitch").hide();
	$("#errorSwitch").show();
	
	
	$("#deviceHTML").show();
	$("#deviceName").html("Error");
	$("#deviceHTML").html("<div style='text-align:center'>An error occured. The request will be sent again in 30 seconds.<br /><br />"+errorMsg+"</div>");
	
	timerID = setTimeout('refreshCurrentPage();', 30000);
    return false;
}

$(function() {
	$("#deviceHTML").hide();
	$("#waitSwitch").hide();
	$("#errorSwitch").hide();

	calculateDeviceHTMLHeight();
	
	getNextDevice();
	
	$("#refreshAfter").change( function() {
		var refreshAfter = $("#refreshAfter").val()*1000;
		if (refreshAfter>0) {
			timerID = setTimeout('refreshCurrentPage();', refreshAfter);
		} 
		
	});

	$("#state").change( function() {
		var state = $("#state").val();
		  if (state==0) {
		  	$("#switchButton").attr("src","../../images/red_button.png");
		  } else if (state==1) {
		  	$("#switchButton").attr("src","../../images/green_button.png");
		  }
	});

	$("#switchBox").click(function() {
		clearTimeout(timerID);
		abortRunningCall();
		
		$("#waitSwitch").show();
		$("#errorSwitch").hide();

		calculateDeviceHTMLHeight();
		
		var deviceID = $("#device_id").val();
		var state = $("#state").val();
		var toState = "off";

		if (state==0) {
			$("#switchButton").attr("src","../../images/green_button.png");
			toState="on";
	  	} else if (state==1) {
		  	$("#switchButton").attr("src","../../images/red_button.png");
		  	toState="off";
		}

		$.ajax({
			  url: "../api/register/switchDevice.php?state=" + toState + "&deviceID=" + deviceID,
			  async:false
			}).done(function(data) {
				if (data.trim()=="error") {
					$("#errorSwitch").show();
				}
				$("#waitSwitch").hide();
				calculateDeviceHTMLHeight();
				refreshCurrentPage();
			});
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
	abortRunningCall();
	
	$("#wait").show();
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();


	xhr = $.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=current&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		$("#state").val(jsonResult.html).change();
		$("#device_id").val(jsonResult.type_id);
		$("#deviceName").html(jsonResult.description);
		$("#deviceDescription").html(jsonResult.description);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		$("#deviceHTML").show();	
		$("#deviceName").show();

		calculateDeviceHTMLHeight();
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
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


function clearDisplayForAction() {
	clearTimeout(timerID);
	abortRunningCall();
	
	$("#deviceName").hide();
	$("#deviceHTML").hide();
	$("#waitSwitch").hide();
	$("#errorSwitch").hide();

	calculateDeviceHTMLHeight();
}

function getNextDevice() {
	clearDisplayForAction();
	
	$("#wait").show();
	
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=next&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		$("#currentPageID").val(jsonResult.page_id);
		
		$("#deviceName").html(jsonResult.description);
		$("#state").val(jsonResult.html).change();
		$("#device_id").val(jsonResult.type_id);
		$("#deviceDescription").html(jsonResult.description);

		$("#refreshAfter").val(jsonResult.refreshAfter).change();
		
		$("#deviceHTML").show();
		$("#deviceName").show();

		calculateDeviceHTMLHeight();
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function getPrevDevice() {
	clearDisplayForAction();
	
	$("#wait").show();
	
	var device_id=$("#current_device_id").val();
	var page_id=$("#currentPageID").val();
	
	$.ajax( "../api/register/getPage.php?device_id="+device_id+"&type=device&pageaction=prev&currentpageid="+page_id)
	 .done(function(returnVal) {
		var jsonResult = jQuery.parseJSON(returnVal);
		$("#currentPageID").val(jsonResult.page_id);
		
		$("#deviceName").html(jsonResult.description);
		$("#state").val(jsonResult.html).change();
		$("#device_id").val(jsonResult.type_id);
		$("#deviceDescription").html(jsonResult.description);

		
		$("#refreshAfter").val(jsonResult.refreshAfter).change();

		$("#deviceHTML").show();
		$("#deviceName").show();

		calculateDeviceHTMLHeight();		
		$("#wait").hide();
	 })
	 .error(function(request, status, error) {
		 throw error;
	 });
}

function calculateDeviceHTMLHeight() {
	var contentHeight = $("#deviceContent").outerHeight();
	var nameHeight = $("#deviceNameRow").outerHeight();
	var buttonRowHeight = $("#buttonRow").outerHeight();
	var resultRowHeight = $("#resultRow").outerHeight();
	
	var newHtmlHeight = contentHeight - nameHeight - buttonRowHeight - resultRowHeight;

	$("#deviceHTMLRow").height(newHtmlHeight);

	$("#nextPage").css("left", ($("#prevPage").width()));
	
}

</script>

<input type="hidden" id="refreshAfter" value="" />
<input type="hidden" id="currentPageID" value="" />

<div id="deviceContent" style="height:100%;">
	<div class="row" id="deviceNameRow">
		<div class="col-xs-12 col-md-12">
			<h4 id="deviceName"></h4>
		</div>
	</div>
	<div class="row" style="" id="deviceHTMLRow">
			<div class="col-xs-12 col-md-12" id="deviceHTML" style="">
				<div class="displayDeviceBox" style="" id="switchBox">
					<div class="container-fluid">
						<div class="col-xs-7 col-md-7">
							<span id="deviceDescription"></span>
						</div>
						<div class="col-xs-5 col-md-5">
							<img id="switchButton" src="../../images/gray_button.png" style="height:50px;width:50px" />
						</div>
					</div>
				</div>
			</div>
	</div>
	<div class="row" id="resultRow">
		<span id="waitSwitch" style="">
			<img src='../../images/ajax-loader2.gif' style="padding:5px;"/>
		</span>
		<span id="errorSwitch" style="">
			<img src='../../images/error.png' style="padding:5px;"/>
		</span>
		<input type="hidden" id="state" value="" />
		<input type="hidden" id="device_id" value="" />
	</div>
	<div class="row" id="buttonRow" style="padding-bottom:10px">
		<i id='prevPage' class='icon-chevron-left' style='margin-right:15px;cursor: pointer;padding-left:30px;padding-right:30px'></i>
		<i id='nextPage' class='icon-chevron-right' style='margin-right:15px;cursor: pointer;padding-left:30px;padding-right:30px'></i>
	</div>				
</div>
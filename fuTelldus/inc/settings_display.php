<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	
	if (isset($_GET['id'])) {
		/* Get userdata
		 --------------------------------------------------------------------------- */
		$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$getID."'");
		$selectedUser = $result->fetch_array();
	}


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>".$lang['Userdata updated']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-danger autohide'>".$lang['Old password is wrong']."</div>";
		elseif ($_GET['msg'] == 03) echo "<div class='alert alert-danger autohide'>".$lang['New password does not match']."</div>";
		elseif ($_GET['msg'] == 04) echo "<div class='alert alert-info autohide'>".$lang['Test message sent']."</div>";
	}
?>

<script	src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/codemirror.min.js"></script>
<script	src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/xml/xml.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/codemirror.min.css" rel="stylesheet">


<script type="text/javascript">
$(function() {
	var codeEditor = CodeMirror.fromTextArea(document.getElementById("inputHTML"), {
		  height: "150px",
		  mode: "text/html",
		  lineNumbers: true,
	      styleActiveLine: true,
		  matchBrackets: true
		});
	
	$("#table").hide();
	$("#edit").hide();
	
	$("#wait").show();

	// load data initially
	var user_id = $("#user_id").val();
	$.ajax( "display/api/register/getExistingDevices.php?user_id="+user_id)
	.done(function(returnVal) {
		 var jsonResult = jQuery.parseJSON(returnVal);
	
		 for (var i = 0; i < jsonResult.length; i++) {
		    var device = jsonResult[i];
		    var displayName = device.name;
		    var displayID = device.display_id;
		    var userID = device.user_id;
	
		    var deviceLink = "<tr id='"+displayID+"'><td>"+displayName+"</td><td style='text-align:right'>";
		    deviceLink = deviceLink + "<a href='#' class='btn btn-warning btn-xs editBtn' id='editBtn-"+displayID+"' title='edit display' data-user='"+userID+"' data-display='"+displayID+"' data-name='"+displayName+"'><i class='glyphicon glyphicon-white glyphicon glyphicon-pencil'></i></a>&nbsp;";
		    deviceLink = deviceLink + "<a href='#' class='btn btn-danger btn-xs deleteBtn' title='delete display' data-user='"+userID+"' data-display='"+displayID+"' data-name='"+displayName+"'><i class='glyphicon glyphicon-white glyphicon glyphicon-trash'></i></a>&nbsp;";
		    deviceLink = deviceLink + "</td></tr>";
		    $("#displayTable").append(deviceLink);
		}

		$("#table").show();
		$("#wait").hide();
	});

	//event for create page
	$("#createPageBtn").click(function() {
		$("#current_page_id").val("");
		$("#type").val("");
		$("#inputDescription").val("");
		$("#inputShowFor").val("");
		$("#inputRefreshAfter").val("");
		$("#selectDevice").val("");
// 	 	$("#inputHTML").val("");
	 	codeEditor.setValue("");
	 	codeEditor.refresh();
		$("#legend").html("new Page");
		$("#selectCurrent").bootstrapToggle('off');

		
		$("#editPage").show();
		$("#selectDeviceOptions").hide();
		$("#editPageTable").hide();
	});

	// event for save page
	$("#savePageBtn").click(function() {
		$("#wait").show();
		$("#editPage").hide();

		var display_id=$("#current_display_id").val();
		var page_id=$("#current_page_id").val();
		var type=$("#type").val();
		var description=$("#inputDescription").val();
		var showFor=$("#inputShowFor").val();
		var refreshAfter=$("#inputRefreshAfter").val();
		var device=$("#selectDevice").val();
		if (type=='sensor') {
			var html = codeEditor.getValue();
		} else if (type=='device') {
			var html = "{{state}}";
		}
		
		var reqCurrent = $("#selectCurrent").prop('checked');
		if (reqCurrent) {
			reqCurrent=1;
		} else {
			reqCurrent=0;
		}

		$.post( "display/api/register/savePage.php",
				{ display_id:display_id, page_id:page_id, type:type, description:description, showFor:showFor, refreshAfter:refreshAfter, device:device, html:html, reqCurrent:reqCurrent },
			  function(data) {
					$("#editPageTable").show();
					$("#editBtn-"+$("#current_display_id").val()).click();
			  }
		);
	});

	// event for change type combo
	$("#type").change(function() {
		$("#wait").show();
		$("#selectDeviceOptions").hide();
		
		
		var selected = $( this ).val();
		if (selected == 'device') {
			$("#selectDeviceLabel").html("Device");
		} else if (selected == 'sensor') {
			$("#selectDeviceLabel").html("Sensor");
		}

		if (selected == 'device' || selected == 'sensor') {
			var user_id = $("#user_id").val();
			
			$.ajax( {
					url:"display/api/register/getVirtualObjectsToUser.php?user_id="+user_id+"&type="+selected,
					async:false,
					success:function(returnVal) {
						$("#selectDevice").html("");
						$("#selectDevice").append("<option value='' selected='selected'></option>");
						var jsonResult = jQuery.parseJSON(returnVal);
					
						for (var i = 0; i < jsonResult.length; i++) {
							var device = jsonResult[i];
							$("#selectDevice").append("<option value='"+device.id+"'>"+device.description+"</option>");
						}
			
						$("#wait").hide();
						$("#selectDeviceOptions").show();
					}
			});
		} else {
			$("#wait").hide();
		}
	
		// clear inputs
// 		$("#inputHTML").val("");
		codeEditor.setValue("");
		codeEditor.refresh();

		if (selected=='device') {
			$("#showForRow").hide();
		} else if (selected=='sensor') {
			$("#showForRow").show();
		}
		
		
		$("#placeholders").html("");
		$("#selectDevice").val("");
		$("#requestCurrentRow").hide();
		$("#deviceHTMLOptions").hide();
	});


	
	// event for change type combo
	$("#selectDevice").change(function() {
		$("#wait").show();
		$("#deviceHTMLOptions").hide();

		var id = $(this).val();
		var type = $("#type").val();
		if (type == 'device') {
			$("#selectDeviceLabel").html("Device");
			$("#requestCurrentRow").hide();
			$("#deviceHTMLOptions").hide();
		} else if (type == 'sensor') {
			$("#selectDeviceLabel").html("Sensor");
			$("#requestCurrentRow").show();
			$("#deviceHTMLOptions").show();
		}

		if (id!='') {
			$.ajax( {
				url:"display/api/register/getVirtualObjectReturnVals.php?id="+id+"&type="+type,
				async:false,
				success:function(returnVal) {
					$("#placeholders").html("");
		
					var jsonResult = jQuery.parseJSON(returnVal);
					var placeholder = "";
					$("#controlArea").html("");
					$.each(jsonResult, function(key, value){
						var description = value.description;
						placeholder += key+" : " + description +"<br />";

						// for preview page
						var controlAreaInput = "<div class='row'  style='margin-top:15px'>"+
							"<label class='control-label col-md-3' for='placeholder_"+key+"'>"+key+"</label>"+
							"<div class='input-group col-md-5'>"+
							"	<input type='text' class='form-control placeholderInput' data-key='"+key+"' id='placeholder_"+key+"'/>"+
							"</div>"+
							"<div class='input-group col-md-4'></div>"+
							"</div>";
					
						$("#controlArea").append(controlAreaInput);			    
					});

					$("#placeholders").html(placeholder);

					// append apply-button to preview
					$("#controlArea").append("<br /><a id='applyValuesBtn' class='btn btn-success' style='margin-right:15px'>Apply</a>");
					
					$("#wait").hide();
				}
			});
		} else {
			$("#wait").hide();
		}
		
		// clear inputs
// 		$("#inputHTML").val("");
		codeEditor.setValue("");
		codeEditor.refresh();
	});

	// event for edit button
	$( document ).on( 'click', '.editBtn', function () {
		$("#table").hide();
		$("#wait").show();
		
		var user_id=$(this).data("user");
		var display_id=$(this).data("display");
		var display_name=$(this).data("name");

		$("#current_display_id").val(display_id);

		$.ajax( "display/api/register/getPagesToDevice.php?device="+display_id)
		.done(function(returnVal) {
			$("#wait").hide();
			
			$("#pagesTable").html("");
			 var jsonResult = jQuery.parseJSON(returnVal);
		
			 for (var i = 0; i < jsonResult.length; i++) {
			    var page = jsonResult[i];
			    var pageDescription = page.description;
			    var pageType = page.type;
			    var page_id = page.page_id;
			    var page_content = JSON.stringify(page);

			    var pageLink = "";
	 		    pageLink += "<tr id='page-"+page_id+"'><td>"+pageDescription+"</td><td>"+pageType+"</td><td style='text-align:right'>";
			    pageLink += "<a href='#' class='btn btn-warning btn-xs editPageBtn' title='edit page' data-page='"+page_id+"' data-pagecont='"+page_content+"'><i class='glyphicon glyphicon-white glyphicon glyphicon-pencil'></i></a>&nbsp;";
			    pageLink += "<a href='#' class='btn btn-danger btn-xs deletePageBtn' title='delete page'  data-page='"+page_id+"'><i class='glyphicon glyphicon-white glyphicon glyphicon-trash'></i></a>&nbsp;";
				pageLink += "</td></tr>";
			    
			    $("#pagesTable").append(pageLink);
			}

			$("#edit").show();
			$("#editPage").hide();
		});

		$("#title").html("Edit display "+ display_name);		
	});


	//event for edit page button
	$( document ).on( 'click', '.editPageBtn', function () {
		$("#editPageTable").hide();
		
		var pageContent=$(this).data("pagecont");

		$("#current_display_id").val(pageContent.display_id);
		$("#current_page_id").val(pageContent.page_id);
		$("#type").val(pageContent.type).change();
		$("#inputDescription").val(pageContent.description);
		$("#inputShowFor").val(pageContent.showFor);
		$("#inputRefreshAfter").val(pageContent.refreshAfter);

		if (pageContent.reqCurrent == 1) {
			$("#selectCurrent").bootstrapToggle('on');
		} else {
			$("#selectCurrent").bootstrapToggle('off');
		}

		$("#selectDevice").val(pageContent.type_id).change();
//	  	$("#inputHTML").val(Base64.decode(pageContent.html));
// 	 	codeEditor.setValue(Base64.decode(pageContent.html));
	 	codeEditor.setValue(pageContent.html);
	 	setTimeout(function() {
	 		codeEditor.refresh();
	 	},1);
	 	

		$("#legend").html("Page " + pageContent.description);
		
		$("#editPage").show();
		
		$("#editPageTable").hide();
	});

	//event for delete button
	$( document ).on( 'click', '.deletePageBtn', function () {
		$("#wait").show();
		$("#pagesTable").hide();
		
		var page_id=$(this).data("page");

		$.ajax( "display/api/register/removePage.php?page_id="+page_id)
			.done(function(returnVal) {
			$("#page-"+page_id).remove();

			$("#pagesTable").show();	
			$("#wait").hide();
		});
	});

	//event for delete button
	$( document ).on( 'click', '.deleteBtn', function () {
		$("#wait").show();
		
		var display_id=$(this).data("display");

		$.ajax( "display/api/register/removeDevice.php?device_id="+display_id)
			.done(function(returnVal) {
			$("#"+display_id).remove();
			
			$("#wait").hide();
		});
	});


	$(document).on("click", "#previewBtn", function () {
		$("#header-scene-text").text("Preview");
		$("#modal-body-scene").css("min-height", $(window).height() - 250); // 520
		$("#deviceHTML").html(codeEditor.getValue());
		$("#deviceName").html($("#inputDescription").val());
		
		$('#sohwDialog').modal('show');
	});

	$( document ).on( 'click', '#applyValuesBtn', function () {
		$(".placeholderInput").each(function() {
			var key = $(this).data("key");
			var value = $(this).val();
			if (value.length>0) {
				var newText = $("#deviceHTML").html().replace("{{"+key+"}}", value)
				$("#deviceHTML").html(newText);
			}
		});
	});
	
});





</script>

<h4 id="title">Diplays</h4>

<input type="hidden" id="user_id" value="<?php echo $getID ?>"/>
<input type="hidden" id="current_display_id"/>
<input type="hidden" id="current_page_id"/>

<div id="wait">
	<img src='images/ajax-loader2.gif'/>
</div>

<div id="table">
	<div class='row'>
		<div class='col-md-12'>
		<table class='table table-striped table-hover'>
			<thead>
				<tr>
					<th width='85%'>Name</th>
					<th width='15%'></th>
				</tr>
			</thead>
			
			<tbody id="displayTable">
			</tbody>
		</table>
		</div>
	</div>
</div>	

<div id="edit">
	<div id="editPageTable">
		<fieldset>
			<legend>Pages</legend>
			<div class='row'>
				<div class='col-md-12'>
				<table class='table table-striped table-hover'>
					<thead>
						<tr>
							<th width='60%'>Description</th>
							<th width='30%'>Type</th>
							<th width='10%'></th>
						</tr>
					</thead>
					
					<tbody id="pagesTable">
					</tbody>
					
				</table>
				</div>
			</div>
			<div class='row'>
				<div id="" style='float:right;margin-top:15px'>
					<a class='btn btn-success' id="createPageBtn">Create page</a>
				</div>
			</div>			
		</fieldset>
	</div>
	
	<div id="editPage">
		<fieldset>
			<legend id="legend">Page 1</legend>
			<div class="container-fluid">
				<form class='form-horizontal' action='#' method='POST'>
					<div class="row"  style="margin-top:15px">
						<label class="control-label col-md-3" for="type">Type</label>
						<div class="input-group col-md-5">
							<select name='type' id="type" class='form-control'>
								<option value='' selected='selected'></option>
								<option value='sensor'>Sensor</option>
								<option value='device'>Device</option>
							</select>
						</div>
						<div class="input-group col-md-4"></div>
					</div>		
				
					<div class="row"  style="margin-top:15px">
						<label class="control-label col-md-3" for="inputDescription">Description</label>
						<div class="input-group col-md-5">
							<input type="text" class="form-control" name='inputDescription' id="inputDescription" placeholder="Description" />
						</div>
						<div class="input-group col-md-4"></div>
					</div>
					
					<div class="row"  style="margin-top:15px" id="showForRow">
						<label class="control-label col-md-3" for="inputShowFor">Show for (in seconds)</label>
						<div class="input-group col-md-5">
							<input type="text" class="form-control" name='inputShowFor' id="inputShowFor" placeholder="Show for" />
						</div>
						<div class="input-group col-md-4"></div>
					</div>
					
					<div class="row"  style="margin-top:15px" id="refreshAfterRow">
						<label class="control-label col-md-3" for="inputRefreshAfter">Refresh after (in seconds)</label>
						<div class="input-group col-md-5">
							<input type="text" class="form-control" name='inputRefreshAfter' id="inputRefreshAfter" placeholder="Refresh after" />
						</div>
						<div class="input-group col-md-4"></div>
					</div>
					
					<div id="selectDeviceOptions">
						<div class="row"  style="margin-top:30px">
							<label class="control-label col-md-3" id="selectDeviceLabel" for="selectDevice">Sensor</label>
							<div class="input-group col-md-5">
								<select name='selectDevice' id="selectDevice" class='form-control'>
									<option value='' selected="selected"></option>
								</select>
							</div>
							<div class="input-group col-md-4"></div>
						</div>	

						<div class="row"  style="margin-top:30px" id="requestCurrentRow">
							<label class="control-label col-md-3" id="selectDeviceLabel" for="selectCurrent">Request current values</label>
							<div class="input-group col-md-5">
								<input type="checkbox" checked data-toggle="toggle" id="selectCurrent" data-on="Current values" data-off="Last values">
							</div>
							<div class="input-group col-md-4"></div>
						</div>	
						
	
						<div id="deviceHTMLOptions">
							<div class="row"  style="margin-top:30px">
								<label class="control-label col-md-3" for="intputHTML">HTML</label>
								<div class="input-group col-md-9">
									<div class="row">
										<div class="col-md-7">
											<textarea class='form-control' rows="5" id="inputHTML"></textarea>
										</div>
										<div class="col-md-5">
											Use the following placeholders to add values (e.g. {{temperature}} ) <br /><br />
											<div id="placeholders">
											</div>					
										</div>
									</div>
								</div>
							</div>
							
							
						</div>
						<div class="row" style="margin-top:30px; float:right">
								<a id="previewBtn" href="#sohwDialog" class='btn btn-success' style="margin-right:15px">Preview</a>
								<a id="savePageBtn" class='btn btn-primary' style=>Save Page</a>	
							</div>
					</div>
				</form>
			</div>
		</fieldset>
	</div>
</div>

<div class="modal modal-wide fade" id="sohwDialog">
		<div class="modal-dialog">
			<div class="modal-content" style="">
				<div class="modal-header" id="modal-header-scene">
					<a class="close" data-dismiss="modal">&times;</a>
					<div class="header" id="header-scene">
						<h3 id="header-scene-text"></h3>
					</div>
				</div>
				<div class="modal-body" id="modal-body-scene" style="text-align:left">
				
				
					<div class="container-fluid">
				
						<div class="col-md-6" id="previewArea" style="height:240px;width:320px;border: solid 1px">
							<div id="sensorContent" style="height:100%;">
							<div class="row" id="deviceNameRow">
								<div class="col-xs-12 col-md-12" style="text-align:center">
									<h4 id="deviceName"></h4>
								</div>
							</div>
							<div class="row" style="" id="deviceHTMLRow">
									<div class="col-xs-12 col-md-12" style="height:100%;text-align:left;margin:0px;padding:0px;">
										<div class="container-fluid" id="deviceHTML" style="height:100%;max-height:100%">
										</div>
									</div>
							</div>
							</div>
						</div>
						
						<div class="col-md-6" id="controlArea">
							
							
							
						</div>
					</div>
				
				</div>
				<div class="modal-footer" id="modal-footer-scene">
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['Close'] ?></a>
				</div>
			</div>
		</div>
	</div>	
<?php
	require_once "webhooksdk/WhkEventManager.php";
	require_once 'setting.php';
	
	// session_start();
	// if(!isset($_SESSION['token']) || $_SESSION['token'] == '')
	// {
	// 	header("Location: " . $_HOMEPAGE_FILE);
	// 	die;
	// }
	
	// rcpt_to //
	$rcpt_to = '';
	if( isset($_POST['rcpt_to']) ){
		$rcpt_to = $_POST['rcpt_to'];
		// echo $rcpt_to . '<br>';
	}

	// friendly_from //
	$friendly_from = '';
	if( isset($_POST['friendly_from']) ){
		$friendly_from = $_POST['friendly_from'];
		// echo $friendly_from . '<br>';
	}

	// subject //
	$subject = '';
	if( isset($_POST['subject']) ){
		$subject = $_POST['subject'];
		// echo $subject. '<br>';
	}

	// campaign_id //
	$campaign_id = '';
	if( isset($_POST['campaign_id']) ){
		$campaign_id = $_POST['campaign_id'];
		// echo $campaign_id . '<br>';
	}

	// type //
	$type = '';
	if( isset($_POST['type']) ){
		$type = $_POST['type'];
		// echo $type . '<br>';
	}

	// datetime //
	$datetime = '';
	if( isset($_POST['datetime']) ){
		$datetime = $_POST['datetime'];
		// echo $datetime . '<br>';
	}

	/**
	 * parse webhookd_indata to check which event type, and save to database
	 */
	Log::INFO('mailtracer.php');
	$eventMgr = new WhkEventManager();
	$detail = $eventMgr->GetMailEvent($rcpt_to, $friendly_from, $subject, $campaign_id, $type, $datetime);

	// echo '<br>';
	// var_dump($detail);
	usort($detail, 'sortByTimestamp');
?>

<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=Edge">
			<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			<title>SparkWebhook</title>
			<!-- Favicon-->			
			
			<!--REQUIRED PLUGIN CSS-->
			<link href="<?php echo ($base_url.'/cs/font-awesome.min.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/bootstrap.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/sweetalert.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/alertify.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/bootstrap-select.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/bootstrap-material-datetimepicker.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/daterangepicker.css');?>" rel="stylesheet">


			<!--THIS PAGE LEVEL CSS-->
			<link href="<?php echo ($base_url.'/cs/dataTables.bootstrap.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/responsive.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/scroller.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/fixedHeader.bootstrap.min.css');?>" rel="stylesheet">

			<!--THIS PAGE LEVEL CSS-->
			<link href="<?php echo ($base_url.'/cs/alertify.css');?>" rel="stylesheet">

			<link href="<?php echo ($base_url.'/cs/scroller.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ($base_url.'/cs/fixedHeader.bootstrap.min.css');?>" rel="stylesheet">

			<!--REQUIRED THEME CSS -->
			<link href="<?php echo ($base_url.'/cs/dropify.min.css');?>" rel="stylesheet">

			<!-- Animation Css -->
			<link href="<?php echo ($base_url.'/cs/animate.css');?> rel="stylesheet" />

			<!-- Custom Css -->
			<link href="<?php echo ($base_url.'/cs/style.css');?>" rel="stylesheet">

			<!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
			<link href="<?php echo ($base_url.'/cs/all-themes.css');?>" rel="stylesheet" />

			<!--  Sparkpost customized from css/themes instead of get all themes -->
			<link href="<?php echo ($base_url.'/cs/sparkpost.css');?>" rel="stylesheet" />

			<!-- Google Fonts -->
			<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
			<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

			<script type="text/javascript">
				var baseURL = "https://audiostreams.envisionapps.net/";
			</script>
			
			<!-- Jquery Core Js -->
			<script src="<?php echo ($base_url.'/js/jquery.min.js');?>"></script>
			<!-- Bootstrap Core Js -->
			<script src="<?php echo ($base_url.'/js/bootstrap.js');?>"></script>
			<!-- Select Plugin Js -->
			<script src="<?php echo ($base_url.'/js/bootstrap-select.js');?>"></script>
			<!-- Waves Effect Plugin Js -->
			<script src="<?php echo ($base_url.'/js/jquery.slimscroll.js');?>"></script>	
			
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
		</head>
		
		<body class="light layout-fixed theme-blue">
		<section class="content">
			<!-- Page content-->
			<div class="container-fluid">				
				<!-- Exportable Table -->
				<div class="row">					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">					
						<form id="event_form" action="<?php echo $base_url.$base_file_uri;?>" method="post">
							<div class="card">
								<div class="body">
									<div class="header" style="margin:0px; padding:0px"> 	
										<div class="row" style='height:50px; background-color:#0075bd; font-size:24px; text-align:center;color: #fff;font-weight: bold;padding-top: 8px'>
											Email Events
										</div>
										<div class="row" style='margin-bottom: 30px'>
											<div class="col-sm-8">
												<div class="input-group search-box" style=" margin-bottom: 0px;margin-top: 10px;">	
													<div class="input-group-append" style="display: table-cell; text-align: left; border-left-style: solid; border-width: 5px;border-color: #b9b8b8;">																											
														<label style='margin:15px'> What email addres would you like to trace? </label>		
														<input type="text" id='rcpt_to' name='rcpt_to' placeholder="example@outlook.com" style='border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8;width: 30%;height: 30px;padding-left: 5px;' value='<?php echo $rcpt_to;?>'>										
														<button type="submit" id="btn_datetime" class="btn btn-light" style="padding-top: 6px;padding-bottom: 6px;margin-bottom: 10px;margin-top: 10px;margin-right: 10px; float:right">Search All Email Activity</button>
													</div>								
												</div>
											</div>
											<div class='col-sm-4' style="margin-top: 50px; text-align:right; font-weight:bold">
												<a href="#" id ="export" role='button'>Export into a CSV File</a>
											</div>										
										</div>
										<div class="row" style='height:30px; background-color:#0075bd; font-size:18px; text-align:center;color: #fff;font-weight: bold; padding-top:2px'>
											Filters										
										</div>
										<div class="row clearfix" style='margin-bottom: 50px'>	
											<div style="width:20%; height:35px; float:left;border-style: solid;border-width: 1px;border-color: #b9b8b8;padding: 5px;">
												<input type="text" id='friendly_from' name='friendly_from' placeholder="Filter From Address" value="<?php echo $friendly_from;?>" style='border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8; width:100%;height: 25px;padding-left: 5px;' >										
											</div>		
											
											<div style="width:20%; height:35px; float:left;border-style: solid;border-width: 1px;border-color: #b9b8b8;padding: 5px;">
												<input type="text" id='subject' name='subject' placeholder="Filter By Subject" value="<?php echo $subject;?>" style='border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8; width:100%;height: 25px;padding-left: 5px;' >										
											</div>		

											<div style="width:20%; height:35px; float:left;border-style: solid;border-width: 1px;border-color: #b9b8b8;padding: 5px;">
												<input type="text" id='campaign_id' name='campaign_id' placeholder="Filter By Campaign" value="<?php echo $campaign_id;?>" style='border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8; width:100%;height: 25px;padding-left: 5px;' >										
											</div>		

											<div style="width:20%; height:35px; float:left;border-style: solid;border-width: 1px;border-color: #b9b8b8;padding: 5px;">
												<input type="text" id='type' name='type' placeholder="Filter By Type" value="<?php echo $type;?>" style='border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8; width:100%;height: 25px;padding-left: 5px;' >
											</div>		

											<div style="width:20%; height:35px; float:left;border-style: solid;border-width: 1px;border-color: #b9b8b8;padding: 5px;">
												<input class="form-control" style="border-style: solid;border-width: 1px;box-shadow: -2px -2px 2px #ccc;border-color: #b9b8b8; width:100%;height: 25px;padding-left: 5px;" id="datetime" name="datetime" placeholder="YYYY-MM-DD" value="<?php echo $datetime;?>" type="text"/>															
											</div>											
										</div>

									</div>								

									<div id="dvData" style="overflow-x:auto;">
										<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable detail-bordered">
											<tbody>											
												<!-- Job Details -->
												<tr class = "header">
													<td > No </td>
													<td > From </td>
													<td > Subject </td>
													<td > Campaign_id </td>
													<td > Mail_Server_Event	</td>												
													<td > Time Stamp </td>
													<td > Bounce Details(if any) </td>
													<td > Est.Loc When Clicked </td>
													<td > IP Addres </td>
													<td > Meta Data </td>
													<td > Sub AccountID </td>						
												</tr>

												<?php													
													for($idx = 0; $idx < count($detail); $idx++) 
													{
														$class = $idx % 2 == 0? "odd":"even";
														$row = $detail[$idx]; 
												?>											
													<tr class="<?php echo $class; ?>">
														<td > <?php echo $idx + 1; ?> </td>
														<td > <?php if(isset($row->friendly_from)) echo $row->friendly_from; ?> </td>
														<td > <?php if(isset($row->subject)) echo $row->subject; ?> </td>
														<td > <?php if(isset($row->campaign_id)) echo $row->campaign_id; ?> </td>
														<td > <?php if(isset($row->type)) echo $row->type; ?> </td>
														<td > <?php if(isset($row->timestamp)) echo convertToNewYork(date('m/d/Y H:i:s', $row->timestamp)); ?> </td>
														<td > <?php if(isset($row->reason)) echo $row->reason; ?> </td>
														<td > <?php if(isset($row->geo_ip)) echo $row->geo_ip; ?> </td>
														<td > <?php if(isset($row->ip_address)) echo $row->ip_address; ?> </td>
														<td > <?php if(isset($row->rcpt_meta)) echo $row->rcpt_meta	; ?> </td>
														<td > <?php if(isset($row->subaccount_id)) echo $row->subaccount_id; ?> </td>

													</tr>
												<?php
													}
												?>

											</tbody>
										</table>
									</div>													
								</div>
							</div>					
						</form>
					</div>
				</div>
			</section>


			<!-- Custom Js -->
			<script src="<?php echo ($base_url.'/js/admin.js');?>"></script>
			<!-- Demo Js -->
			<script src="<?php echo ($base_url.'/js/demo.js');?>"></script>

			<!--THIS PAGE LEVEL JS-->
			<script src="<?php echo ($base_url.'/js/jquery.dataTables.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.bootstrap.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.buttons.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/buttons.flash.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/jszip.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/pdfmake.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/vfs_fonts.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/buttons.html5.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/buttons.print.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.keyTable.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.responsive.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/responsive.bootstrap.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.scroller.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dataTables.fixedHeader.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/jquery.slimscroll.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/jquery-datatable.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/bootstrap-notify.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/sweetalert.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/bootstrap-notify.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/bootstrap-select.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/dropify.min.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/moment.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/bootstrap-material-datetimepicker.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/daterangepicker.js');?>"></script>
			<script src="<?php echo ($base_url.'/js/jsmediatags.min.js');?>"></script>
			<!-- LAYOUT JS -->
			<script src="<?php echo ($base_url.'/js/ajax.js');?>"></script>
			<script>			
				function exportTableToCSV($table, filename) 
				{					
                	var $headers = $table.find('tr:has(th)')
                    $rows = $table.find('tr:has(td)')
                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                    tmpColDelim = String.fromCharCode(11) // vertical tab character
                    tmpRowDelim = String.fromCharCode(0) // null character
                    
					// actual delimiter characters for CSV format
                    colDelim = '","'
                    rowDelim = '"\r\n"';
                    
					// Grab text from table into CSV formatted string
                    var csv = '"';
                    csv += formatRows($headers.map(grabRow));
                    csv += rowDelim;
                    csv += formatRows($rows.map(grabRow)) + '"';
                    
					// Data URI
					var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
					$(this).attr({
						'download': filename,
						'href': csvData
						//,'target' : '_blank' //if you want it to open in a new window
					});
					//------------------------------------------------------------
					// Helper Functions 
					//------------------------------------------------------------
					// Format the output so it has the appropriate delimiters
					function formatRows(rows){
						return rows.get().join(tmpRowDelim)
							.split(tmpRowDelim).join(rowDelim)
							.split(tmpColDelim).join(colDelim);
					}
					// Grab and format a row from the table
					function grabRow(i,row){						
						var $row = $(row);
						//for some reason $cols = $row.find('td') || $row.find('th') won't work...
						var $cols = $row.find('td'); 
						if(!$cols.length) $cols = $row.find('th');  
						return $cols.map(grabCol)
									.get().join(tmpColDelim);
					}
					// Grab and format a column from the table 
					function grabCol(j,col){
						var $col = $(col),
							$text = $col.text();
						return $text.replace('"', '""'); // escape double quotes
					}
				}

				function exportCSV_click() 
				{
					var outputFile = window.prompt("What do you want to name your output file (Note: This won't have any effect on Safari)") || 'export';
                	outputFile = outputFile.replace('.csv','') + '.csv';
					exportTableToCSV($('#categories-table'), outputFile);
				}

				// This must be a hyperlink
				$("#export").click(function (event) {
					// var outputFile = 'export'
					var outputFile = window.prompt("What do you want to name your output file (Note: This won't have any effect on Safari)") || 'export';
					outputFile = outputFile.replace('.csv','') + '.csv'
					
					// CSV
					exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
					
					// IF CSV, don't do event.preventDefault() or return false
					// We actually need this to be a typical hyperlink
				});

				$(document).ready(function() {					
					var date_input=$('input[name="datetime"]'); //our date input has the name "date"
					var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
					date_input.datepicker({
							format: 'yyyy-mm-dd',
							container: container,
							todayHighlight: true,
							autoclose: true,
					})
				});
			
				$(function() {
					if(document.getElementById('reportrange')==undefined)return;
					var date = document.getElementById('reportrange').value;
					var res = date.split(" - ");
					var start = 0;
					var end = 0;
					if(res[0] != undefined && res[1] != undefined){
					start = res[0];
					end = res[1];
					}else{
					start = (moment().subtract(0, 'days')).format('YYYY-MM-DD');
					end = (moment()).format('YYYY-MM-DD');
					}

					function cb(start, end) {
						$('#reportrange span').html(start + ' - ' + end);
					}

					$('#reportrange').daterangepicker({
						startDate: start,
						endDate: end,
						locale: {
						format: 'YYYY-MM-DD'
						},
						ranges: {
						'Today': [moment(), moment()],
						'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'Last 7 Days': [moment().subtract(6, 'days'), moment()],
						'Last 30 Days': [moment().subtract(29, 'days'), moment()],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
						}
					}, cb);
					cb(start, end);
				});
			</script>
			<script src="<?php echo ($base_url.'/js/common.js');?>"></script>
		</body>
</html>
<?php
	require_once "webhooksdk/WhkEventManager.php";
	require_once 'setting.php';
	
	// session_start();
	// if(!isset($_SESSION['token']) || $_SESSION['token'] == '')
	// {
	// 	header("Location: " . $_HOMEPAGE_FILE);
	// 	die;
	// }
	
	$option = 'open';
	$days = '7';
	$campaignid = '';

	if( isset($_POST['filter_type']) ){
		$option = $_POST['filter_type'];
		// echo $option;
	}

	if( isset($_POST['filter_days']) ) {		
		$days = $_POST['filter_days'];
		// echo $days;
	}

	if( isset($_POST['filter_campaignid']) ) {		
		$campaignid = $_POST['filter_campaignid'];
		// echo $days;
	}

	/**
	 * parse webhookd_indata to check which event type, and save to database
	 */
	Log::INFO('recipientTracer.php');
	if( !isset($_POST['filter_type']) && !isset($_POST['filter_days']) && !isset($_POST['filter_campaignid']) )
	{
		$detail = array();
	}
	else 
	{
		$eventMgr = new WhkEventManager();
		$detail = $eventMgr->GetRecipients($option, $days, $campaignid);	
	}	
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
		</head>

		<body class="light layout-fixed theme-blue">
		<section class="content">
			<!-- Page content-->
			<div class="container-fluid">
				<!-- Exportable Table -->
				<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<form id="recipient_form" action="<?php echo $base_url.$base_file_uri;?>" method="post">
							<div class="card">
								<div class="body">
									<div class="header" style="text-align:right; margin:0px; padding:0px"> 	
										<div class="row">					
											<div class="col-sm-10">
												<div class="input-group search-box" style=" margin-bottom: 0px;margin-top: 10px;">	
													<div class="input-group-append" style="display: table-cell; text-align: left; border-left-style: solid; border-width: 5px;border-color: #b9b8b8;">																											
														<label style='margin:15px'> By Type </label>
														<select class="browser-default custom-select" id='optionSelect' style="margin:15px">
															<option selected><?php echo $option;?></option>
															<option value="1">open</option>
															<option value="2">click</option>
															<option value="3">no_open</option>
															<option value="4">no_click</option>																												
														</select>
														<span>&nbsp;&nbsp;</span>

														<label style='margin:15px'> CampaingId </label>
														<input style="border-bottom: 1px solid #ddd !important;" type='text' id='campaignidTxt' name='campaignidTxt' value ='<?php echo $campaignid; ?>' size='60' placeholder='write campaignid correctly' />
														<span>&nbsp;&nbsp;</span>

														<label style='margin:15px'> By Last Days </label>
														<select class="browser-default custom-select" id='daysSelect' style="margin:15px">
															<option selected><?php echo $days;?></option>
															<option value="1">7</option>
															<option value="2">15</option>
															<option value="3">30</option>	
															<option value="3">45</option>	
															<option value="3">60</option>
															<option value="3">90</option>
														</select>
														days ago
														<button type="submit" id="btn_cmpgn_date" class="btn btn-light" style="padding-top: 6px;padding-bottom: 6px;margin-bottom: 10px;margin-top: 10px;margin-right: 10px; float:right">view</button>
													</div>
													<input type='hidden' id='filter_type' name='filter_type'>
													<input type='hidden' id='filter_days' name='filter_days'>
													<input type='hidden' id='filter_campaignid' name='filter_campaignid'>
												</div>
											</div>
											<div class='col-sm-2' style="margin-top: 50px; text-align:right; font-weight:bold">
												<a href="#" id ="export" role='button'>Export into a CSV File</a>
											</div>										
										</div>
									</div>								

									<div id="dvData" style="overflow-x:auto;">
										<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable detail-bordered">
											<tbody>											
												<!-- Job Details -->
												<tr class = "header">
													<td > no </td>
													<td > type </td>
													<td > recipient </td>
													<td > campaign 	</td>
													<td > ipaddress </td>												
													<td > count_of_<?php echo $option ?></td>
												</tr>

												<?php												
													for($idx = 0; $idx < count($detail); $idx++) 
													{
														$class = $idx % 2 == 0? "odd":"even";
														$row = $detail[$idx]; 
												?>											
													<tr class="<?php echo $class; ?>">
														<td > <?php echo $idx + 1; ?> </td>
														<td > <?php if(isset($row->type)) echo $row->type; ?> </td>
														<td > <?php if(isset($row->rcpt_to)) echo $row->rcpt_to; ?> </td>
														<td > <?php if(isset($row->campaign_id)) echo $row->campaign_id; ?> </td>
														<td > <?php if(isset($row->ip_address)) echo $row->ip_address; ?> </td>
														<td > <?php if(isset($row->count_of_campaign)) echo $row->count_of_campaign; ?> </td>
													</tr>
												<?php
													}
												?>
											</tbody>
										</table>
									</div>				
									<!-- <div style="margin-top: 50px; text-align:right">
										<button type="button" onclick="exportCSV_click();" class="btn btn-primary"> export to csv</button>
									</div> -->								
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
					$("#recipient_form").submit(function(event){																
						var option = $('#optionSelect :selected').text();
						var days = $('#daysSelect :selected').text();
						var campaignid = $('#campaignidTxt').val();						

						$('#filter_type').val(option);
						$('#filter_days').val(days);
						$('#filter_campaignid').val(campaignid);
					});
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

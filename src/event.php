<?php
	require_once "webhooksdk/WhkEventManager.php";
	require_once 'setting.php';

	// session_start();
	// if(!isset($_SESSION['token']) || $_SESSION['token'] == '')
	// {
	// 	header("Location: " . $_HOMEPAGE_FILE);
	// 	die;
	// }

	$campaign_id = $_GET['campaign_id'];
	$request_type =  $_GET['request_type'];
	$target_link_url = "";
	if(isset($_GET['target_link_url']))
		$target_link_url = $_GET['target_link_url'];
	  
	/**
	 * parse webhookd_indata to check which event type, and save to database
	 */
	Log::INFO('event.php');
	$eventMgr = new WhkEventManager();
	$detail = $eventMgr->GetDetail($campaign_id, '', $request_type, $target_link_url);	
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

			<!-- Google Fonts -->
			<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
			<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

			<!--  Sparkpost customized from css/themes instead of get all themes -->
			<link href="<?php echo ($base_url.'/cs/sparkpost.css');?>" rel="stylesheet" />

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
						<div class="card">
							<div class="body">
								<div id="dvData" style="overflow-x:auto;">
									<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable detail-bordered">
										<tbody>											
											<!-- Job Details -->
											<tr class = "header">
												<td > no </td>
												<td > datetime </td>
												<td > ipaddress </td>
												<td > event_id </td>
												<td > campaign_id </td>
												<td > type </td>
												<td > target_link_url </td>	
												<td > recipient_domain </td>	
												<td > rcpt_to </td>
												<td > friendly_from </td>
											</tr>

											<?php
												for($idx = 0; $idx < count($detail); $idx++) 
												{
													$class = $idx % 2 == 0? "odd":"even";
													$row = $detail[$idx]; 
											?>											
												<tr class="<?php echo $class; ?>">
													<td > <?php echo $idx; ?> </td>

													<td > <?php echo convertToNewYork(date('m/d/Y H:i:s', $row->timestamp)); ?> </td>

													<td > <?php if(isset($row->ip_address)) echo $row->ip_address; ?> </td>

													<td > <?php if(isset($row->event_id)) echo $row->event_id; ?> </td>

													<td > <?php if(isset($row->campaign_id)) echo $row->campaign_id; ?> </td>

													<td > <?php if(isset($row->type)) echo $row->type; ?> </td>

													<td > <?php if(isset($row->target_link_url)) echo $row->target_link_url; ?> </td>

													<td > <?php if(isset($row->recipient_domain)) echo $row->recipient_domain; ?> </td>

													<td > <?php if(isset($row->rcpt_to)) echo $row->rcpt_to; ?> </td>

													<td > <?php if(isset($row->friendly_from)) echo $row->friendly_from; ?> </td>
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

								<div class='button' style="margin-top: 50px; text-align:right; font-weight:bold">
									<a href="#" id ="export" role='button'>Export into a CSV File</a>
								</div>
							</div>
						</div>
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
				// var tableToExcel = (function() 
				// {
				// 	var uri = 'data:application/vnd.ms-excel;base64,',
				// 	template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
				// 	base64 = function(s) {
				// 		return window.btoa(unescape(encodeURIComponent(s)))
				// 	},
				// 	format = function(s, c) {
				// 		return s.replace(/{(\w+)}/g, function(m, p) {
				// 			return c[p];
				// 		})
				// 	}
				// 	return function(table, name)
				// 	{
				// 		if (!table.nodeType)
				// 			table = document.getElementById(table)
				// 		var ctx = {
				// 			worksheet: name || 'Worksheet',
				// 			table: table.innerHTML
				// 		}

				// 		var HeaderName = 'Download-ExcelFile';
				// 		var ua = window.navigator.userAgent;
				// 		var msieEdge = ua.indexOf("Edge");
				// 		var msie = ua.indexOf("MSIE ");

				// 		if (msieEdge > 0 || msie > 0)
				// 		{
				// 			if (window.navigator.msSaveBlob) 
				// 			{
				// 				var dataContent = new Blob([(format(template, ctx))], {
				// 					type: "application/csv;charset=utf-8;"
				// 				});

				// 				var fileName = "excel.csv";
				// 				navigator.msSaveBlob(dataContent, fileName);
				// 			}
				// 			return;
				// 		}
				// 		window.open('data:application/vnd.ms-excel,' + (format(template, ctx)));
				// 	}
				// })();
				
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

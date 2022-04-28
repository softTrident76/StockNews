
<?php

	// groupe by campagin_id, and orderby timestamp, and select in filter limit
	require_once "webhooksdk/WhkEventManager.php";
	require_once 'webhooksdk/log.php';

	// $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
	// $log = Log::Init($logHandler, 15);

	// $eventMgr = new WhkEventManager();
	// $eventMgr->ConfigureTestDatabase();	
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
			<link href="<?php echo ('/cs/font-awesome.min.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/bootstrap.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/sweetalert.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/alertify.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/bootstrap-select.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/bootstrap-material-datetimepicker.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/daterangepicker.css');?>" rel="stylesheet">

			<!--THIS PAGE LEVEL CSS-->
			<link href="<?php echo ('/cs/dataTables.bootstrap.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/responsive.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/scroller.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/fixedHeader.bootstrap.min.css');?>" rel="stylesheet">

			<!--THIS PAGE LEVEL CSS-->
			<link href="<?php echo ('/cs/alertify.css');?>" rel="stylesheet">

			<link href="<?php echo ('/cs/scroller.bootstrap.min.css');?>" rel="stylesheet">
			<link href="<?php echo ('/cs/fixedHeader.bootstrap.min.css');?>" rel="stylesheet">

			<!--REQUIRED THEME CSS -->
			<link href="<?php echo ('/cs/dropify.min.css');?>" rel="stylesheet">

			<!-- Animation Css -->
			<link href="<?php echo ('/cs/animate.css');?> rel="stylesheet" />

			<!-- Custom Css -->
			<link href="<?php echo ('/cs/style.css');?>" rel="stylesheet">

			<!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
			<link href="<?php echo ('/cs/all-themes.css');?>" rel="stylesheet" />

			<!-- Google Fonts -->
			<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
			<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

			<script type="text/javascript">
				var baseURL = "https://audiostreams.envisionapps.net/";
			</script>
			

			<!-- Jquery Core Js -->
			<script src="<?php echo ('/js/jquery.min.js');?>"></script>
			<!-- Bootstrap Core Js -->
			<script src="<?php echo ('/js/bootstrap.js');?>"></script>
			<!-- Select Plugin Js -->
			<script src="<?php echo ('/js/bootstrap-select.js');?>"></script>
			<!-- Waves Effect Plugin Js -->
			<script src="<?php echo ('/js/jquery.slimscroll.js');?>"></script>
				
		</head>

		<body class="light layout-fixed theme-blue">
		
		<style>
			table tbody {
				font-size:20px;
			}
			table td.icon {
				width:5%; 
				padding:17px !important;
			}

			table td.content {
				width: 60%;
			}

			table td.sentBtn {
				width: 15%;
			}

			table td.percent {
				width: 10%;
			}

			table.list-border tbody td {
				border-bottom-width: 0;
				border-left: none;
    			border-right: none;
			}
		</style>

		<section class="content">
			<!-- Page content-->
			<div class="container-fluid">	
				<!-- Exportable Table -->
				<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="body">
								<div style="overflow-x:auto;">
									<div>
										Configure Your Test Database, Loading Data From webhood_testdata.ini
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Custom Js -->
			<script src="<?php echo ('/js/admin.js');?>"></script>
			<!-- Demo Js -->
			<script src="<?php echo ('/js/demo.js');?>"></script>

			<!--THIS PAGE LEVEL JS-->
			<script src="<?php echo ('/js/jquery.dataTables.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.bootstrap.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.buttons.min.js');?>"></script>
			<script src="<?php echo ('/js/buttons.flash.min.js');?>"></script>
			<script src="<?php echo ('/js/jszip.min.js');?>"></script>
			<script src="<?php echo ('/js/pdfmake.min.js');?>"></script>
			<script src="<?php echo ('/js/vfs_fonts.js');?>"></script>
			<script src="<?php echo ('/js/buttons.html5.min.js');?>"></script>
			<script src="<?php echo ('/js/buttons.print.min.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.keyTable.min.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.responsive.min.js');?>"></script>
			<script src="<?php echo ('/js/responsive.bootstrap.min.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.scroller.min.js');?>"></script>
			<script src="<?php echo ('/js/dataTables.fixedHeader.min.js');?>"></script>
			<script src="<?php echo ('/js/jquery.slimscroll.js');?>"></script>
			<script src="<?php echo ('/js/jquery-datatable.js');?>"></script>
			<script src="<?php echo ('/js/bootstrap-notify.js');?>"></script>
			<script src="<?php echo ('/js/sweetalert.min.js');?>"></script>
			<script src="<?php echo ('/js/bootstrap-notify.js');?>"></script>
			<script src="<?php echo ('/js/bootstrap-select.js');?>"></script>
			<script src="<?php echo ('/js/dropify.min.js');?>"></script>
			<script src="<?php echo ('/js/moment.js');?>"></script>
			<script src="<?php echo ('/js/bootstrap-material-datetimepicker.js');?>"></script>
			<script src="<?php echo ('/js/daterangepicker.js');?>"></script>
			<script src="<?php echo ('/js/jsmediatags.min.js');?>"></script>
			<!-- LAYOUT JS -->
			<script src="<?php echo ('/js/ajax.js');?>"></script>
			<script>
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
			<script src="<?php echo ('/js/common.js');?>"></script>
		</body>
</html>

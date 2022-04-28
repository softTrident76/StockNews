<?php

session_start([
    'cookie_lifetime' => 604800,
]);
    
    $role = $_SESSION['sess_userrole'];
    if(!isset($_SESSION['sess_username']) || $role !== 'admin'){
      header('Location: index.php?m=2');
    }
    $username = $_SESSION['sess_username'];

// groupe by campagin_id, and orderby timestamp, and select in filter limit
require_once "webhooksdk/WhkEventManager.php";
require_once 'webhooksdk/lib/WhkPagination.php';
require_once 'setting.php';

// date_default_timezone_set('America/New_York');
Log::INFO('index.php');

// get last top on campaign table, which added at lastest since current.
$eventMgr = new WhkEventManager();
$result = $eventMgr->GetCampaignList(0, 0);

if(count($result) == 0)
	$from = '2019-09-13T00:00';
else
	$from = $result[0]['injection_time'];

// $from = '2019-09-13T00:00';	
// $localzone = new DateTimeZone("Asia/Seoul");
$time = new DateTime($from);
// $time->setTimezone($localzone);
$time->add(new DateInterval('PT' . 1 . 'M'));
$from = date_format($time, 'Y-m-d\TH:i'); 

try {
	$response = sparkpost('GET', 
		'metrics/campaigns',
		[
			'from'=> $from		
			// 'timezone' => 'America/New_York'	
		]
	);

	// if no new campaign exist
	if( !isset($response['results']) || count($response['results']['campaigns']) == 0 )
		throw new Exception('no result');

	$elements = $response['results']['campaigns'];
	$new_cmpg_list = array();
	foreach( $elements as $each)
	{
		$query = array('campaign_id' => $each);
		if( count($eventMgr->GetCampaignList(-1, -1, $query)) <= 0 )
			array_push($new_cmpg_list, $each);
	}

	// get campaign_list from ~ to now //
	$new_my_spark_list = array();
	foreach($new_cmpg_list as $campaign)
	{
		$response = sparkpost('GET', 'events/message', [
				'from'=> $from,
				'campaigns' => $campaign,
				// 'timezone' => 'America/New_York'	
		]);
	
		$list = $response['results'];
		
		if(count($list) == 0)
			continue;

		$new_campaign = new stdClass();
		$new_campaign->campaign_id = $campaign;
		$new_campaign->subject = $list[0]['subject'];
		$new_campaign->friendly_from = $list[0]['friendly_from'];
		$new_campaign->ip_address = $list[0]['ip_address'];
		$new_campaign->injection_time = $list[0]['injection_time'];
		$new_campaign->delv_method = $list[0]['delv_method'];
		$new_campaign->timestamp = $list[0]['timestamp'];
		if(isset($list[0]['rcpt_meta']['ListUsed']))
			$new_campaign->list_used = $list[0]['rcpt_meta']['ListUsed'];	
		else
			$new_campaign->list_used = '';		
		$new_campaign->ip_pool = $list[0]['ip_pool'];

		$response = sparkpost('GET', 'metrics/deliverability/campaign', [
			'from' => $from,
			'campaigns' => $campaign,
			// 'timezone' => 'America/New_York',
			'metrics' => ['count_injected', 'count_bounce', 'count_rejected', 'count_delivered', 'count_delivered_first', 'count_delivered_subsequent', 'total_delivery_time_first', 
							'total_delivery_time_subsequent', 'total_msg_volume', 'count_policy_rejection', 'count_generation_rejection', 'count_generation_failed', 'count_inband_bounce', 
								'count_outofband_bounce', 'count_soft_bounce', 'count_hard_bounce', 'count_block_bounce', 'count_admin_bounce', 'count_undetermined_bounce', 'count_delayed', 
									'count_delayed_first', 'count_rendered', 'count_unique_rendered', 'count_unique_confirmed_opened', 'count_clicked', 'count_unique_clicked', 'count_targeted', 
										'count_sent', 'count_accepted', 'count_spam_complaint'
						],
		]);		
				

		$list = $response['results'];
		if(count($list) == 0)
			continue;
		$list = $response['results'][0];

		if( $list['count_injected'] != $list['count_sent'] )
		{
			echo '<script> Campaign ' . $campaign . ' Is Not Yet Processed by SparkPost. Wait some minutes. Try Again </script>';
			continue;
		}

		foreach($list as $key => $value)
			$new_campaign->$key = $value;

		array_push($new_my_spark_list, $new_campaign);
	}
	
	// var_dump($new_my_spark_list);
	$eventMgr->SaveCampaignToDatabase($new_my_spark_list);
			
} catch (\Exception $e) {
	// echo $e->getCode()."\n";
	// echo $e->getMessage()."\n";		
}
	
// fetch campaign list from database based on pagination
$pagination = new WhkPagination();
$count_totals = (int)$eventMgr->GetCampaignCounts();
$pagination->initialize($count_totals);

// echo '<br>';
//echo $pagination->start_paginatin_no.', '. $pagination->end_paginatin_no.', '. $count_totals;
//echo '<br>';

$start_campaign_id = ($pagination->current_page_idx - 1) * $pagination->const_count_rows_per_page;
$end_campaign_id = ($pagination->current_page_idx) * $pagination->const_count_rows_per_page - 1;
if( $end_campaign_id > $pagination->count_totals )
	$end_campaign_id = $pagination->count_totals;

//echo '<br>'. $start_campaign_id . ", " . $end_campaign_id. '<br>';
//return;

$result = $eventMgr->GetCampaignList($start_campaign_id, $end_campaign_id);
// var_dump($result);
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
						<?php include ('header.inc'); ?>
						
							<div style="overflow-x:auto;">
								<form id="campaign_form" action="<?php echo $base_url.$base_file_uri;?>" method="post">
									<div class="header" style="text-align:right; margin-right:10px"> 
										<?php $pagination->render(); ?>
									</div>
									<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable list-border">
										<tbody>
											<?php
												$newresult = $result;
												foreach($result as $row)
												{
													$campaign_id = $row['campaign_id'];
													$detail = $eventMgr->GetDetail($campaign_id, "index");
											?>
												<tr>
													<td class="text-center icon">
														<i style="margin-bottom:5px;" class="material-icons" data-id="0">email</i>
													</td>

													<td class="content">
														<div class="row" style="margin-bottom: 5px" >	
															<a href="<?php echo $base_url?>detail.php?campaign_id=<?php echo $row['campaign_id']; ?>" style=" font-size: 28px; float: none; margin-bottom: 5px; color: #3c9033 !important;">
																<?php echo $row['campaign_id']; ?>
															</a>
														</div>

														<div class="row" style="margin-bottom: 5px">														
															Subject Line: <span><b><?php  echo $row['subject'];?></b></span>
														</div>

														<div class="row" style="margin-bottom: 5px">
															<span>Sent on <b>
																<?php 
																		// $time = new DateTime($row['injection_time']);
																		// $injectionTm = date_format($time, 'Y-m-d\TH:i');  
																		echo convertToNewYork($row['injection_time']);
																?></b> to 
																<b>
																<?php 
																	echo $row['count_sent']
																?>
																</b> recipients
															</span>		
														</div>

														<div class="row" style="margin-bottom: 5px">
															<span>From <strong><?php  echo $row['friendly_from'];?></strong></span>
														</div>
													</td>

													<td class="sentBtn">
														<button type="button" class="btn btn-lg btn-round btn-success" style="border-radius: 15px; float: none;">
															Sent
														</button>
													</td>

													<td class="percent">
														<div class="row" >	
															<span> <b><?php echo $detail->gross_opens?></b> </span>
														</div>

														<div class="row" >														
															<span>Opens</span>
														</div>
													</td>

													<td class="percent">
														<div class="row" >	
															<span><b><?php echo $detail->gross_clicks?></b> </span>
														</div>

														<div class="row" >														
															<span>Clicks</span>
														</div>
													</td>
												</tr>	
											<?php
												}
											?>
										</tbody>
									</table>
									<input type="hidden" id="currentpage_idx" name="currentpage_idx" value="">
									<input type="hidden" id="currentblock_idx" name="currentblock_idx" value="">
								</form>
							</div>
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

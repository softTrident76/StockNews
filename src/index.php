
<?php
// groupe by campagin_id, and orderby timestamp, and select in filter limit
require_once "webhooksdk/WhkEventManager.php";
require_once 'webhooksdk/lib/WhkPagination.php';
require_once 'setting.php';

// date_default_timezone_set('America/New_York');
Log::INFO('index.php');

// get last top on campaign table, which added at lastest since current.
$eventMgr = new WhkEventManager();
$pagination = new WhkPagination();

$cmpgn_date = '';
$cmpgn_id = '';

// ######################################################### //
// ################ 	Testing Part		################ //
// ######################################################### //
// require_once(__DIR__ .'/define.php');
session_start();

if(!isset($_SESSION['token']) || $_SESSION['token'] == '')
{
	Infusionsoft_OAuth2::$clientId = $_INFUSIONSOFT_CLIENT_ID;
	Infusionsoft_OAuth2::$clientSecret = $_INFUSIONSOFT_CLIENT_SECRET;
	
	$cleanRedirectUri = explode('?', $_SERVER['REQUEST_URI'], 2);
	Infusionsoft_OAuth2::$redirectUri = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$cleanRedirectUri[0].$_HOMEPAGE_FILE;
	
	// if( isset($_GET['code'] ) )
	// {
		// $token = Infusionsoft_OAuth2::getToken($_GET['code']);
		// if(isset($token['access_token']))
		// {
			// echo $token['access_token'];
			// return;		
		// }
	// }
	
	$appDomain = Infusionsoft_OAuth2::processAuthenticationResponseIfPresent();
	$app = Infusionsoft_AppPool::getApp($appDomain);
	
	if(!$app->hasTokens()){
		echo '<a href="' . Infusionsoft_OAuth2::getAuthorizationUrl() . '">Click here to authorize the infusionsoft</a>';
		die();
	}
	
	$_SESSION['token'] = $app->getAccessToken();
	header("Location: " . Infusionsoft_OAuth2::$redirectUri);		
}

$results = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('FirstName' => '%'), 2);



/*
 ########### rest api test for recipient #################
*/
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// $param = array('groups' => '273');	
// $json_string = json_encode($param);

// // $ch = curl_init('http://13.231.159.175:9001/api/getrecipient');
// $ch = curl_init('http://n.stocknews.com:9001/api/getrecipient');
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);	
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 	'Content-Type: application/json',
// 	'Content-Length: ' . strlen($json_string))
// );

// $result = curl_exec($ch);
// if($errno = curl_errno($ch)) {
//     $error_message = curl_strerror($errno);
//     echo "cURL error ({$errno}):\n {$error_message}";
// }

// // Close the handle
// curl_close($ch);

// $ret = json_decode($result, true);
// echo '<pre>';
// var_dump($ret);
// echo '</pre>';
// return ;

/**
 * ############ sparkpost ###########
 */
// $response = sparkpost('Get',
// 		'transmissions'		
// );
// var_dump($response);
// return;

// $from = '2019-09-30T00:00';
// $response = sparkpost('GET', 
// 			'metrics/campaigns',
// 			[
// 				'from'=> $from
// 				// 'timezone' => 'America/New_York'	
// 			]
// );

// var_dump($response);
// return;
// ######################################################### //

// merge scheduled campaign //
// get the all scheduled campaign by GET/api/v1/transmissions //
$response = sparkpost('Get',
	'transmissions'
);

$scheduled_list = $response['results'];
$scheduled_list = array_filter($scheduled_list, function($element) {
	if( !isset($element['state']) || $element['state'] == NULL)
		return false;
	return  $element['state'] === 'submitted';
});

try {
	// in search mode //
	if( isset($_POST['cmpgn_date']) || isset($_POST['cmpgn_id']))
	{
		// fetch campaign list from database based on pagination
		$cmpgn_date = $_POST['cmpgn_date'];
		$cmpgn_id = $_POST['cmpgn_id'];		

		$query = array();
		$from = '';
		$to = '';
		if($cmpgn_date != '')
		{	
			// convert to utc //
			$utc = convertToUTC($cmpgn_date);
			$utcdate = substr($utc, 0, strpos($utc, 'T'));

			$from = $cmpgn_date.'T00:00:00.000Z';
			$to = $cmpgn_date.'T23:59:59.000Z';			
		}

		if($cmpgn_id != '') {
			$query = array('campaign_id' => $cmpgn_id);	
		}

		$count_totals = (int)$eventMgr->GetCampaignCounts($query, $from, $to);	
		$pagination->initialize($count_totals);

		// setting my pagination //				
		$start_campaign_id = ($pagination->current_page_idx - 1) * $pagination->const_count_rows_per_page;
		$end_campaign_id = ($pagination->current_page_idx) * $pagination->const_count_rows_per_page - 1;
		if( $end_campaign_id > $pagination->count_totals )
			$end_campaign_id = $pagination->count_totals;

		// fetch data for pagination //
		$result = $eventMgr->GetCampaignList($start_campaign_id, $end_campaign_id, $query, $from, $to);

		// fetch filtering data for scheduled //
		if( $scheduled_list != NULL && count($scheduled_list) > 0 && $cmpgn_id != "") {
			$scheduled_list = array_filter($scheduled_list, function($element) use ($cmpgn_id) {
				return strpos($element['campaign_id'], $cmpgn_id); 
			});
			$scheduled_list = array_filter($scheduled_list, function($element) use ($cmpgn_date) {
				$td = $cmpgn_date . 'T23:59:59';
				$fd = $cmpgn_date . 'T00:00:00';
				if( $element['start_time'] >= $fd && $element['start_time'] <= $td )
					return true;
				else
					return false;
			});
		}
		
	}	// in search mode //

	// in normal mode or in delete mode //
	else 
	{
		// in delete mode //
		if( isset($_GET['method']) && isset($_GET['transmission_id']))
		{
			$method = $_GET['method'];
			$transmission_id = $_GET['transmission_id'];

			if($method == 'delete')
			{
				// cancel schedule transmission //	
				$response = sparkpost('DELETE',
					'transmissions/' . $transmission_id
				);

				// redirect //			
				$uri_parts = explode('?', $base_url.$base_file_uri, 2);				
				header("Location: " . $uri_parts[0]);
				return;
			}
		}

	// ####################################################################################region under calling sparkpost
		// // in standard mode //
		// // get top last campaign //
		// $result = $eventMgr->GetCampaignList(0, 0);
		
		// if(count($result) == 0)
		// 	$from = '2019-09-13T00:00';
		// else
		// 	$from = $result[0]['injection_time'];

		// // $from = '2019-09-13T00:00';
		// // $localzone = new DateTimeZone("Asia/Seoul");

		// $localzone = new DateTimeZone("UTC");
		// $time = new DateTime($from);
		// $time->setTimezone($localzone);
		// $time->sub(new DateInterval('PT' . 24 . 'H'));
		// $from = date_format($time, 'Y-m-d\TH:i'); 

		// $response = sparkpost('GET', 
		// 	'metrics/campaigns',
		// 	[
		// 		'from'=> $from
		// 		// 'timezone' => 'America/New_York'	
		// 	]
		// );

		// // if no new campaign exist
		// if( !isset($response['results']) || count($response['results']['campaigns']) == 0 )
		// 	throw new Exception('no result');

		// $elements = $response['results']['campaigns'];
		// $new_cmpg_list = array();
		// foreach( $elements as $each)
		// {
		// 	$query = array('campaign_id' => $each);
		// 	if( count($eventMgr->GetCampaignList(-1, -1, $query)) <= 0 )
		// 		array_push($new_cmpg_list, $each);
		// }	

		// // fill the database for each campaign on campaign_list
		// // subject, friendly_from, ip_address, injection_time by https://developers.sparkpost.com/api/events/#events-get-search-for-message-events, events/message: campaigns => each campaign
		// // 	by https://developers.sparkpost.com/api/metrics/#metrics-get-metrics-by-campaign, metrics/deliverability/campaign
		// 	//	count_injected, count_bounce, count_rejected, count_delivered, count_delivered_first, count_delivered_subsequent, 
		// 	//	total_delivery_time_first, total_delivery_time_subsequent, total_msg_volume, 
		// 	//	count_policy_rejection, count_generation_rejection, count_generation_failed, count_inband_bounce, count_outofband_bounce, count_soft_bounce, count_hard_bounce, count_block_bounce, 
		// 	//	count_admin_bounce, count_undetermined_bounce, count_delayed, count_delayed_first, count_rendered, count_unique_rendered, count_unique_confirmed_opened, 
		// 	//	count_clicked, count_unique_clicked, count_targeted, count_sent, count_accepted, count_spam_complaint	

		// // get campaign_list from ~ to now //
		// // var_dump($new_cmpg_list);
		// // return;
		// // $new_cmpg_list = array('1048106860-1048109899');

		// $new_my_spark_list = array();
		// foreach($new_cmpg_list as $campaign)
		// {
		// 	$response = sparkpost('GET', 'events/message', [
		// 			'from'=> $from,
		// 			'campaigns' => $campaign				
		// 	]);	
			
		// 	$list = $response['results'];			
		// 	if(count($list) == 0)
		// 		continue;

		// 	$new_campaign = new stdClass();
		// 	$new_campaign->campaign_id = $campaign;
		// 	$new_campaign->subject = $list[0]['subject'];
		// 	$new_campaign->friendly_from = $list[0]['friendly_from'];			
		// 	$new_campaign->injection_time = $list[0]['injection_time'];
		// 	$new_campaign->ip_pool = $list[0]['ip_pool'];			
		// 	$new_campaign->timestamp = $list[0]['timestamp'];

		// 	if(isset($list[0]['rcpt_meta']['ListUsed']))
		// 		$new_campaign->list_used = $list[0]['rcpt_meta']['ListUsed'];	
		// 	else
		// 		$new_campaign->list_used = '';
				
		// 	if(isset($list[0]['delv_method']))
		// 		$new_campaign->delv_method = $list[0]['delv_method'];	
		// 	else
		// 		$new_campaign->delv_method = '';
			
		// 	if(isset($list[0]['ip_address']))
		// 		$new_campaign->ip_address = $list[0]['ip_address'];	
		// 	else
		// 		$new_campaign->ip_address = '';
			

		// 	$response = sparkpost('GET', 'metrics/deliverability/campaign', [
		// 		'from' => $from,
		// 		'campaigns' => $campaign,			
		// 		'metrics' => ['count_injected', 'count_bounce', 'count_rejected', 'count_delivered', 'count_delivered_first', 'count_delivered_subsequent', 'total_delivery_time_first', 
		// 						'total_delivery_time_subsequent', 'total_msg_volume', 'count_policy_rejection', 'count_generation_rejection', 'count_generation_failed', 'count_inband_bounce', 
		// 							'count_outofband_bounce', 'count_soft_bounce', 'count_hard_bounce', 'count_block_bounce', 'count_admin_bounce', 'count_undetermined_bounce', 'count_delayed', 
		// 								'count_delayed_first', 'count_rendered', 'count_unique_rendered', 'count_unique_confirmed_opened', 'count_clicked', 'count_unique_clicked', 'count_targeted', 
		// 									'count_sent', 'count_accepted', 'count_spam_complaint'
		// 					],
		// 	]);
					
		// 	$list = $response['results'];
		// 	if(count($list) == 0)
		// 		continue;

		// 	// var_dump($list);
		// 	// return;

		// 	$list = $response['results'][0];

		// 	if( $list['count_injected'] != $list['count_targeted'] )
		// 	{
		// 		echo '<script> Campaign ' . $campaign . ' Is Not Yet Processed by SparkPost. Wait some minutes. Try Again </script>';
		// 		continue;
		// 	}

		// 	foreach($list as $key => $value)
		// 		$new_campaign->$key = $value;

		// 	array_push($new_my_spark_list, $new_campaign);
		// }
		
		// // store new campaign to database //
		// if( count($new_my_spark_list) > 0)
		// 	$eventMgr->SaveCampaignToDatabase($new_my_spark_list);

	// ####################################################################################endregion

		// fetch campaign list from database based on pagination //
		$count_totals = (int)$eventMgr->GetCampaignCounts();
		$pagination->initialize($count_totals);

		// setting my pagination //
		$start_campaign_id = ($pagination->current_page_idx - 1) * $pagination->const_count_rows_per_page;
		$end_campaign_id = ($pagination->current_page_idx) * $pagination->const_count_rows_per_page - 1;
		if( $end_campaign_id > $pagination->count_totals )
			$end_campaign_id = $pagination->count_totals;

		// fetch data for pagination //
		$result = $eventMgr->GetCampaignList($start_campaign_id, $end_campaign_id);		

	}	// in normal mode or in delete mode //
} // try block //
catch (Exception $e) { }

// setting state, if sent or scheduled
for($i = 0; $i < count($result); $i++) 
{
	$row = $result[$i];	
	$row['state'] = 'Sent';
	$result[$i] = $row;
}

// get from_date and to_date for schedule campaign //
$from_date = '';
$to_date = '';

if(count($result) == 0 || $result == NULL )
{
	$from_date = '0000-00-00T00:00:00';
	$to_date = '9999-10-16T09:00:00';
}
else 
{
	// first element's injection_time on page //
	$first = $result[0];
	$to_date = $first['injection_time'];

	// all for first page, when you get first page, to_date = now //
	if( $pagination->current_page_idx == 1)
		$to_date = '9999-10-16T09:00:00';

	// last element's injection_time on page //
	$last = $result[count($result) - 1];
	$from_date = $last['injection_time'];
}

$scheduled_for_page = array();
foreach($scheduled_list as $item)
{
	$val = $item;
	$val['injection_time'] = $item['start_time'];
	$val['state'] = 'Scheduled';

	if($val['injection_time'] < $to_date && $val['injection_time'] >= $from_date)
		array_push($scheduled_for_page, $val);
}

// merge two array, and sort //
if( count($scheduled_for_page) > 0 && $scheduled_for_page != NULL)
{
	$result = array_merge($result, $scheduled_for_page);
	usort($result, 'sortByInjectTime');
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
		
		<!-- REQUIRED PLUGIN CSS -->
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>

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

	    <!--  jQuery -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<!-- Isolated Version of Bootstrap, not needed if your site already uses Bootstrap -->
		<link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />
		<!-- Bootstrap Date-Picker Plugin -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

		<script>
			$(document).ready(function(){
				var date_input=$('input[name="cmpgn_date"]'); //our date input has the name "date"
				var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
				date_input.datepicker({
					format: 'yyyy-mm-dd',
					container: container,
					todayHighlight: true,
					autoclose: true,
				})
			})
		</script>

	</head>

	<style>		
	</style>

	<body class="light layout-fixed theme-blue">
	<section class="content">
		<!-- Page content-->
		<div class="container-fluid">	
			<!-- Exportable Table -->
			<div class="row clearfix">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="card">	
						<div class="body">
							<div style="overflow-x:auto;">
								<form id="campaign_form" action="<?php echo $base_url.$base_file_uri;?>" method="post">								
									<div class="header" style="text-align:right; margin-right:10px"> 	
										<div class="row">					
											<div class="col-sm-8">
												<div class="input-group search-box" style=" margin-bottom: 0px;margin-top: 10px;">	
													<div class="input-group-append" style="display: table-cell; text-align: left; border-left-style: solid; border-right-style: solid; border-width: 5px;border-color: #b9b8b8;text-align: right;">																											
														<input class="form-control" style="padding-top: 15px; padding-left:10px; font-size: 18px; width: 200px;" id="cmpgn_date" name="cmpgn_date" placeholder="Select YYYY-MM-DD" value='<?php echo $cmpgn_date?>' type="text"/>
														<button type="submit" id="btn_cmpgn_date" class="btn btn-light" style="padding-top: 6px;padding-bottom: 6px;margin-bottom: 10px;margin-top: 10px;margin-right: 10px;">From date</button>
													</div>

													<input type="text" class="form-control" id="cmpgn_id" name="cmpgn_id" style="font-size:14px; float:none; font-size:20px;margin-left: 10px;" value='<?php echo $cmpgn_id?>' placeholder="Type campaign name...">
													<div class="input-group-append" style="display: table-cell;">														
														<button type="submit" id="btn_cmpgn_id" class="btn btn-light" style="padding-top: 6px;padding-bottom: 6px;margin-bottom: 10px;margin-top: 10px;margin-right: 10px;">By name</button>
													</div>																									
												</div>
											</div>
											<div class="col-sm-4">
												<?php $pagination->render(); ?>
											</div>
										</div> 										
									</div>
									<div class="clearfix">
										<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable list-border">
											<tbody>
												<?php																													
													foreach($result as $row)
													{
														$campaign_id = $row['campaign_id'];
														$state = $row['state'];
														
														if($state == 'Sent')
															$detail = $eventMgr->GetDetail($campaign_id, "index");
														else
															$detail = NULL;
												?>
													<tr>
														<td class="text-center icon">
															<i style="margin-bottom:5px;" class="material-icons" data-id="0">email</i>
														</td>

														<td class="content">
															<div class="row" style="margin-bottom: 5px" >	
																	<a href="<?php echo $base_url?>detail.php?campaign_id=<?php echo $row['campaign_id']; ?>&state=<?php echo $row['state']; ?>" style=" font-size: 28px; float: none; margin-bottom: 5px">
																		<?php echo $row['campaign_id']; ?>
																	</a>
															</div>
															<?php 
																if( $row['state'] == 'Sent')
															{
															?>
																<div class="row" style="margin-bottom: 5px">														
																	<span><b><?php  echo $row['subject'];?></b></span>
																</div>

																<div class="row" style="margin-bottom: 5px">
																	<span> <?php echo $state;?> <b>
																		<?php					
																			echo convertToNewYork($row['injection_time']);
																		?></b> to 
																		<b>
																		<?php 
																			echo $row['count_sent']
																		?>
																		</b>
																	</span>		
																</div>
																<div class="row" style="margin-bottom: 5px">
																	<span>recipients by <?php  echo $row['friendly_from'];?> </span>
																</div>
															<?php
															}
															else if( $row['state'] == 'Scheduled')
															{
															?>
																<div class="row" style="margin-bottom: 5px">														
																	<span>Description: <b><?php  echo $row['description'];?></b></span>
																</div>

																<div class="row" style="margin-bottom: 5px">
																	<span> Start Time:  <b>
																		<?php					
																				echo convertToNewYork($row['start_time']);
																		?></b> State 
																		<b>
																		<?php 
																			echo $row['state']
																		?>
																		</b>
																	</span>		
																</div>
																<div class="row" style="margin-bottom: 5px">
																	<span>Name <?php  echo $row['name'];?> </span>
																</div>
															<?php
															}
															?>
														</td>

														<td class="sentBtn">
															<?php 
																if($state == 'Sent')
																{															
															?>
																<a href="#" style="pointer-events: none; cursor: default;" class="btn btn-success btn-rounded"><?php echo $state; ?></a>
															<?php
																} 
																if($state == 'Scheduled')
																{
															?>
																<a href="javascript:cancelMailing('<?php echo $row['id']; ?>');" class="btn btn-info btn-rounded"> 
																	<span class = 'schedule'> Scheduled </span>
																	<span class = 'cancel_emailing'> Cancel Emailing </span>
																</a>
															<?php
																}
															?>
														</td>

														<td class="percent">
															<div class="row" >	
																<span> <b><?php if( $detail != NULL ) echo $detail->unique_opens; else echo '0'; ?></b> </span>
															</div>

															<div class="row" >														
																<span>Opens</span>
															</div>
														</td>

														<td class="percent">
															<div class="row" >	
																<span>
																<b>
																	<?php 
																		if( $detail != NULL ) echo $detail->unique_clicks; else echo '0'; 																	
																	?>
																</b> 
																</span>
															</div>

															<div class="row" >														
																<span>Clicks</span>
															</div>
														</td>

														<td class="percent">
															<div class="row" >	
																<span><b><?php if( $detail != NULL ) echo $detail->count_delivered; else echo '0'; ?></b> </span>
															</div>

															<div class="row" >														
																<span>Size</span>
															</div>
														</td>
													</tr>	
												<?php
													}
												?>
											</tbody>
										</table>
									</div>
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

<script>
	function cancelMailing(transmission_id)
	{
		swal({
				title: 'Are you sure ?',
				text:  'Do you really want to cancel this scheduled mailing?',
				type:  'warning',
				confirmButtonColor: "#DD6B55",
				showCancelButton: true,
				confirmButtonText: 'Sure'
			}, function() {
				document.location.href = '<?php echo $base_url.$base_file_uri; ?>' + '?method=delete&transmission_id=' + transmission_id;
		});
	}	
</script>


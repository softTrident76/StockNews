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
	$state = $_GET['state'];

	/**
	 * parse webhookd_indata to check which event type, and save to database
	 */
	Log::INFO('detail.php');

	if($state == 'Sent')
	{
		$eventMgr = new WhkEventManager();	
		$detail = $eventMgr->GetDetail($campaign_id);
	}
	else if($state = 'Scheduled')
	{
		// get the all scheduled campaign by GET/api/v1/transmissions //
		$response = sparkpost('Get',
			'transmissions',
			[
				'campaign_id' => $campaign_id
			]
		);
		if( isset($response['results']) && $response['results'] != NULL && count($response['results']) > 0)
			$detail = $response['results'][0];
	}

	$response = sparkpost('GET', 'templates/'.$campaign_id);
	if( isset($response['results']) && count($response['results']['content']) > 0 )
	{
		$html = $response['results']['content']['html'];
		$path = 	'templates/' . $campaign_id . '.html';
		$iframeurl = $base_url.$path;
		$handle = fopen($path, 'w');
		fwrite($handle, $html, strlen($html));
		fclose($handle);
	}	

	// echo $iframeurl;
	// echo $html;
	// return;

	// $response = sparkpost('GET', 'templates', 
	// 			[
	// 			'draft' => 'false'
	// 			]);
	// echo "<pre>";
	// print_r ($response);
	// echo "</pre>";
	// echo '<br>';
	
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

			table.detail-bordered tbody td {
				border-bottom-width: 0;
				border: 1px solid #d5dfe6 !important;
			}

			table.detail-bordered tbody tr.header {
				background-color: lightskyblue;
			}
			table.detail-bordered tbody tr.header td {
				font-weight: bold;
			}
			
			table.detail-bordered tbody td.cols_2 {
				width:50%;
			}

			table.detail-bordered tbody td.cols_2_2 {
				width:25%;
			}

			table.detail-bordered tbody tr.odd {
				background-color:white;
			}

			table.detail-bordered tbody tr.even {
				background-color: #e3f0f9;
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
									<table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable detail-bordered">
										<tbody>				
											<?php 
											if( $state == 'Sent')
											{
											?>
												<!-- Job Details -->
												<tr class = "header">
													<td colspan="3" > <?php echo $detail->campaign_id . 'as of'. convertToNewYork(date('Y-m-d H:i'))  ?></td>
												</tr>
												<tr class="odd">
													<td class="cols_2"> Campaign Code </td>
													<td colspan="2" class="cols_2">  <?php echo $detail->campaign_id;?> </td>
												</tr>								

												<tr class="odd">
													<td class="cols_2"> Date/Time Submitted  </td>
													<td colspan="2" class="cols_2"> <?php echo convertToNewYork($detail->injection_time);?> </td>										
												</tr>

												<tr class="even">
													<td class="cols_2"> Launched  </td>
													<td colspan="2" class="cols_2"> <?php echo convertToNewYork($detail->timestamp);?> </td>										
												</tr>

												<tr class="odd"> 
													<td class="cols_2"> Subject Line </td>
													<td colspan="2" class="cols_2"> <?php echo $detail->subject;?>  </td>										
												</tr>											
												
												<tr class="even"> 
													<td class="cols_2"> From </td>
													<td colspan="2" class="cols_2">  <?php echo $detail->friendly_from;?> </td>										
												</tr>																		

												<tr class="odd">
													<td class="cols_2"> Format </td>
													<td colspan="2" class="cols_2"> <?php echo $detail->delv_method;?>  </td>										
												</tr>

												<tr class="even">
													<td class="cols_2"> List Used </td>
													<td colspan="2" class="cols_2"> <?php echo $detail->list_used;?>  </td>										
												</tr>

												<tr class="odd">
													<td class="cols_2"> IP Pool Used </td>
													<td colspan="2" class="cols_2"> <?php echo $detail->ip_pool;?>  </td>										
												</tr>

												<tr class="even">
													<td class="cols_2"> View Html Sent </td>
													<td colspan="2" class="cols_2"> 
														<a href = 'javascript: onViewHtmlSent();' > click me </a>
													</td>										
												</tr>

												<!-- Delivery Details -->
												<tr class = "header">
													<td colspan="3" > Delivery Details </td>
												</tr>

												<tr class="odd">
													<td class="cols_2"> Total Pieces in Mailing </td>
													<td class="cols_2_2">
													<?php echo $detail->count_injected; ?></a> 
													</td>
													<td class="cols_2_2"> 
														<?php echo number_format($detail->count_targeted * 100.0 / $detail->count_injected, 2); ?> %
													</td>
												</tr>

												<tr class="even">
													<td class="cols_2"> &nbsp;&nbsp; - Soft Bounce </td>
													<td class="cols_2_2"> 
														<?php echo $detail->count_soft_bounce; ?> 
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($detail->count_soft_bounce * 100.0 / $detail->count_injected, 2); ?> %
													</td>									
												</tr>

												<tr class="odd">
													<td class="cols_2"> &nbsp;&nbsp; - Hard Bounce </td>
													<td class="cols_2_2">
														<?php echo $detail->count_hard_bounce; ?>
													</td>
													<td class="cols_2_2"> 
														<?php echo number_format($detail->count_hard_bounce * 100.0 / $detail->count_injected, 2); ?> %
													</td>									
												</tr>

												<tr class="even">
													<td class="cols_2"> &nbsp;&nbsp; - Spam Blocks or Content Rejection </td>
													<td class="cols_2_2">
														<?php echo $detail->count_spam_complaint + $detail->count_rejected; ?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format( ($detail->count_spam_complaint + $detail->count_rejected) * 100.0 / $detail->count_injected, 2); ?> %
													</td>		
												</tr>

												<tr class="odd">
													<td class="cols_2"> &nbsp;&nbsp; - Bounce Due to IP Block </td>
													<td class="cols_2_2">
														<?php echo $detail->count_block_bounce ?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($detail->count_block_bounce * 100.0 / $detail->count_injected, 2); ?> %
													</td>		
												</tr>

												<tr class="even">
													<td class="cols_2">Net Deliveries, Approximate</td>
													<td class="cols_2_2">
														<?php 
															$deliver = $detail->count_delivered;
															echo $deliver;
														?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($deliver * 100.0 / $detail->count_injected, 2) ?> %
													</td>									
												</tr>

												<tr class="odd">
													<td class="cols_2">Messages Delivered on 1st Attempt</td>
													<td class="cols_2_2">
														<?php 
															$deliver_first = $detail->count_delivered_first;
															echo $deliver_first;
														?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($deliver_first * 100.0 / $detail->count_injected, 2) ?> %
													</td>									
												</tr>

												<tr class="even">
													<td class="cols_2">Messages delivered that required more than 1 attempt</td>
													<td class="cols_2_2">
														<?php 
															$deliver_subsequent = $detail->count_delivered_subsequent;
															echo $deliver_subsequent;
														?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($deliver_subsequent * 100.0 / $detail->count_injected, 2) ?> %
													</td>									
												</tr>

												<tr class="odd">
													<td class="cols_2">Delayed Emails</td>
													<td class="cols_2_2">
														<?php 
															$delay = $detail->count_delayed;
															echo $delay;
														?>
													</td>
													<td class="cols_2_2"> 
														<?php echo number_format($delay * 100.0 / $detail->count_injected, 2) ?> %
													</td>									
												</tr>

												<!-- Open Rate Details -->
												<tr class = "header">
													<td colspan="3" > OPEN RATE DETAILS </td>
												</tr>
												<tr class="odd">
													<td class="cols_2">Gross Opens</td>												
													<td class="cols_2_2"> <a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id ?>&request_type=gross_opens">
														<?php echo $detail->gross_opens;?></a>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($detail->gross_opens * 100.0 / $detail->count_delivered, 2); ?> %
													</td>
												</tr>
												<tr class="even">
													<td class="cols_2">Unique Opens</td>
													<td class="cols_2_2"> <a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id ?>&request_type=unique_opens"> 
														<?php echo $detail->unique_opens;?>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($detail->unique_opens * 100.0 / $detail->count_delivered , 2); ?>%
													</td>									
												</tr>

												<!-- Unsubscribe Details -->
												<tr class = "header">
													<td colspan="3" > UNSUBSCRIBE DETAILS</td>
												</tr>
												<tr class="odd">
													<td class="cols_2">Link Unsubscribe</td>												
													<td class="cols_2_2"> <a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id ?>&request_type=link_unsubscribe">
														<?php echo $detail->link_unsubscribe;?></a>
													</td>		
													<td class="cols_2_2"> 
														<?php echo number_format($detail->link_unsubscribe * 100.0 / $detail->count_delivered, 2); ?> %
													</td>
												</tr>
												<tr class="even">
													<td class="cols_2">List Unsubscribe</td>
													<td class="cols_2_2"> <a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id ?>&request_type=list_unsubscribe"> 
														<?php echo $detail->list_unsubscribe;?>
													</td>
													<td class="cols_2_2"> 
														<?php echo number_format($detail->list_unsubscribe * 100.0 / $detail->count_delivered , 2); ?>%
													</td>									
												</tr>

												<!-- Click Through Details -->
												<tr class = "header">
													<td colspan="3" > CLICK THROUGH DETAILS CODE </td>
												</tr>

												<?php
													$links = $detail->link;			
													$total_link_click = 0; $total_link_unquie_click = 0;																				
													for($idx = 0; $idx < count($links); $idx++) 
													{
														$link = $links[$idx];
														$url = $link->target_link_url;
														$gross_link_click = $link->gross_link_click;
														$unique_link_click = $link->unique_link_click;	
														$class = $idx % 2 == 0 ? "odd":"even";		
														
														$total_link_click += $gross_link_click;
														$total_link_unquie_click += $unique_link_click;
												?>
													<tr class="<?php echo $class; ?>">
														<td class="cols_2"><?php echo $url; ?> </td>
														<td class="cols_2_2"> 
															<a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id; ?>&request_type=click_gross_through&target_link_url=<?php echo $url;?>">
																<?php echo $gross_link_click;?> clicks
															</a>
														</td>
														<td class="cols_2_2"> 
															<a href="<?php echo $base_url;?>event.php/?campaign_id=<?php echo $campaign_id; ?>&request_type=click_unique_through&target_link_url=<?php echo $url;?>">
																<?php echo $unique_link_click;?> unique clicks
															</a>
														</td>
													</tr>
												<?php
													}
												?>

												<!-- Total Click  -->
												<tr class = "header">
													<td style="border: none !important" > Total Clicks </td>
													<td class="cols_2_2" style="border: none !important">  <?php echo $total_link_click; ?> clicks </td>
													<td class="cols_2_2" style="border: none !important">  <?php echo $total_link_unquie_click; ?> Total unique clicks, <?php echo $detail->unique_clicks;?> unique clickers  </td>
												</tr>
											<?php
											}											
											else if( $state == 'Scheduled' && isset($detail) && $detail != NULL)
											{											
											?>
												<!-- Job Details -->
												<tr class = "header">
													<td colspan="3" > <?php echo $detail['campaign_id'] . 'as of'. convertToNewYork(date('Y-m-d H:i')) ;?></td>
												</tr>
												<tr class="odd">
													<td class="cols_2"> Campaign Code </td>
													<td colspan="2" class="cols_2">  <?php echo $detail['campaign_id'];?> </td>
												</tr>								

												<tr class="even">
													<td class="cols_2"> Date/Time Scheduled  </td>
													<td colspan="2" class="cols_2"> <?php echo convertToNewYork($detail['start_time']);?> </td>										
												</tr>
												
												<tr class="odd"> 
													<td class="cols_2"> Description </td>
													<td colspan="2" class="cols_2">  <?php echo $detail['description'];?> </td>										
												</tr>																		

												<tr class="even">
													<td class="cols_2"> Name </td>
													<td colspan="2" class="cols_2"> <?php echo $detail['name'];?>  </td>										
												</tr>												

												<tr class="odd">
													<td class="cols_2"> View Html Sent </td>
													<td colspan="2" class="cols_2"> 
														<a href = 'javascript: onViewHtmlSent();' > click me </a>
													</td>										
												</tr>
											<?php
											}
											?>							
											
										</tbody>
									</table>
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
				function onViewHtmlSent()
				{
					$("#htmlTemplate").modal();
				}

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

			<!-- Modal -->
			<div class="modal fade" id="htmlTemplate" tabindex="-1" role="dialog" aria-labelledby="htmlTemplate" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLongTitle"> <?php echo $detail->job_detail;?> </h5>													
						</div>
					<div class="modal-body">
						<div class="row" style="border: 2px;border-style: solid;border-color: #d1d4d7;">
							<div class="col-sm-4" style="background-color: #686868;padding: 5px;color: #fff;font-weight: bold;">
								Campaign Code
							</div>
							<div class="col-sm-8" style="padding: 5px;">
								<?php echo $detail->campaign_id;?> 
							</div>
						</div>

						<div class="row" style="border: 2px;border-style: solid;border-color: #d1d4d7;">
							<div class="col-sm-4" style="background-color: #686868;padding: 5px;color: #fff;font-weight: bold;">
								Subject
							</div>
							<div class="col-sm-8" style="padding: 5px;">
								<?php echo $detail->subject;?> 
							</div>
						</div>

						<div class="row" style="border: 2px;border-style: solid;border-color: #d1d4d7;">
							<div class="col-sm-4" style="background-color: #686868;padding: 5px;color: #fff;font-weight: bold;">
								Date Sent
							</div>
							<div class="col-sm-8" style="padding: 5px;">
								<?php echo convertToNewYork($detail->timestamp);?>
							</div>
						</div>
						<div class="row" style="border: 2px;border-style: solid;border-color: #d1d4d7;">
							<!-- <iframe class="embed-responsive-item" style="height:500px;width:100%;" src="https://www.youtube.com/embed/v64KOxKVLVg" allowfullscreen> -->
							<iframe id="myframe" class="embed-responsive-item" style="height:500px;width:100%;" src="<?php echo $iframeurl;?>" allowfullscreen> 
								<html>
								<head> </head>								
								<body>
									 hello
								</body>
								</html>
								
							</iframe>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>					
					</div>
					</div>
				</div>
			</div>
		</body>
</html>

<script>
	$(document).ready(function() {					

	});
</script>
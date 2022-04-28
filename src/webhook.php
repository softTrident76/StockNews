<?php
	require 'define.php';

	$rawData = file_get_contents("php://input");
	if( !isset($rawData) || $rawData == null )
	{
		echo 'no request';
		return;
	}

	// $rawData = '[{"msys":{"message_event":{"campaign_id":"1049597422-1049597436","click_tracking":true,"customer_id":"258443","delv_method":"esmtp","event_id":"751776816092991583","friendly_from":"adam@adammesh.com","initial_pixel":true,"injection_time":"2019-10-24T20:10:07.000Z","ip_address":"67.195.228.111","ip_pool":"default","message_id":"00291f05b25d906732b8","msg_from":"msprvs1=18200OiPXXx7T=bounces-258443-2@bounce.adammesh.com","msg_size":"33410","num_retries":"1","open_tracking":true,"outbound_tls":"1","queue_time":"14462944","rcpt_meta":{"ongage-list-id":"79156","ongage-connection-id":"38567","ongage-account-id":"10778"},"rcpt_tags":[],"rcpt_to":"rjeffc3@yahoo.com","recv_method":"rest","routing_domain":"yahoo.com","sending_ip":"192.174.93.30","subaccount_id":"2","subject":"[Exclusive] Are you ready for this level of trading?","template_id":"template_751774563281630866","template_version":"0","timestamp":"1571962269","transmission_id":"751774563281630866","type":"delivery","raw_rcpt_to":"rjeffc3@yahoo.com"}}},{"msys":{"message_event":{"campaign_id":"1049597422-1049597436","click_tracking":true,"customer_id":"258443","delv_method":"esmtp","event_id":"751776816092991735","friendly_from":"adam@adammesh.com","initial_pixel":true,"injection_time":"2019-10-24T20:10:06.000Z","ip_address":"67.195.228.94","ip_pool":"default","message_id":"00291e05b25d9067d1b7","msg_from":"msprvs1=18200OiPXXx7T=bounces-258443-2@bounce.adammesh.com","msg_size":"33625","num_retries":"1","open_tracking":true,"outbound_tls":"1","queue_time":"14463442","rcpt_meta":{"ongage-connection-id":"38567","ongage-list-id":"79156","ongage-account-id":"10778"},"rcpt_tags":[],"rcpt_to":"atalie777@yahoo.com","recv_method":"rest","routing_domain":"yahoo.com","sending_ip":"192.174.93.30","subaccount_id":"2","subject":"[Exclusive] Are you ready for this level of trading?","template_id":"template_751774563281630866","template_version":"0","timestamp":"1571962270","transmission_id":"751774563281630866","type":"delivery","raw_rcpt_to":"atalie777@yahoo.com"}}}]';
	// $rawData = '[{"msys":{"unsubscribe_event":{"campaign_id":"stocknews-newsletter-1106201916-59-06","click_tracking":true,"customer_id":"258443","event_id":"13196467555173118","friendly_from":"contact@stocknews.com","injection_time":"2019-11-07T14:22:24.000Z","initial_pixel":true,"ip_pool":"stocknews","message_id":"0021a028c45d41a85328","msg_from":"msprvs1=18214jZ86vzXT=bounces-258443-1@bounce.stocknews.com","msg_size":"19715","open_tracking":true,"rcpt_meta":{"ListUsed":"StockNews.com Newsletter List (Infusion Tag ID # 273)"},"rcpt_tags":[],"rcpt_to":"boby_varghese1@yahoo.com","recv_method":"rest","routing_domain":"dunmar.com","sending_ip":"192.174.93.30","subaccount_id":"1","subject":"8 Elite Income Stocks with 8%+ Dividend Yield","template_id":"stocknews-newsletter-1106201916-59-06","template_version":"1","timestamp":"1573154107","transmission_id":"607671033147392727","type":"list_unsubscribe","raw_rcpt_to":"boby_varghese1@yahoo.com"}}}]';

	$json_string = json_encode(array('data' => $rawData));	
	// $ch = curl_init('http://localhost:9001/api/gethook');        
	$ch = curl_init($_API_WEBHOOK_HANDLER);                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($json_string))                                                                       
	);
	$result = curl_exec($ch);
	echo $result;
?>
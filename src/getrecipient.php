<?php

	require_once "webhooksdk/WhkEventManager.php";
	require_once 'webhooksdk/lib/WhkPagination.php';
	require_once 'setting.php';
	// require_once 'infusionsoft-php-sdk/Infusionsoft/infusionsoft.php';
	
	header("Content-Type:application/json");

	$appName = $_INFUSIONSOFT_APPNAME;
	$apiKey = $_INFUSIONSOFT_APIKEY;

	if( !isset($_GET['token']) ) {
		Log::INFO('infusionsoft recipient error: token' );
		return;
	}

	$token = $_GET['token'];
	if($token == '' ) {
		Log::INFO('infusionsoft recipient error: empty param '. $token);

		$response['error'] = 'infusionsoft recipient error: empty param '. $token;
		$json_response = json_encode($response);
		echo $json_response;

		return;
	}

	// $people = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => '273'), $limit = 1000, $page = 0);
	// var_dump($people);
	// Change tagname //	

	$sTags = $_GET['tags'];
	if($sTags == '' ) {
		Log::INFO('infusionsoft recipient error: empty tags '. $sTags);

		$response['error'] = 'infusionsoft recipient error: tags param '. $sTags;
		$json_response = json_encode($response);
		echo $json_response;
		return;
	}

	Log::INFO('infusionsoft recipient token = '. $token . ', tags = '.$sTags);
	$tags = json_decode($sTags);
	$eventMgr = new WhkEventManager();
	$result = $eventMgr->UpdateRecipients($tags);

	// $response['sucess'] = 'infusionsoft recipient count: '. count($result);
	$json_response = json_encode($result);
	
	echo $json_response;
?>
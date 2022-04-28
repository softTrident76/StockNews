<?php

	require_once "webhooksdk/WhkEventManager.php";
	require_once 'webhooksdk/lib/WhkPagination.php';
	require_once 'setting.php';		

	$path = __DIR__ .'/infusionsoft-sdk/Infusionsoft/';
	require_once($path.'infusionsoft.php');
	require_once($path.'config.php');

	header("Content-Type:application/json");

	$appName = $_INFUSIONSOFT_APPNAME;
	$apiKey = $_INFUSIONSOFT_APIKEY;

	if( !isset($_GET['type']) ) {
		Log::INFO('infusionsoft error: type not get' );
		return;
	}		
	
	if( !isset($_GET['email']) ) {
		Log::INFO('infusionsoft error: email not get' );
		return;
	}
	
	if( !isset($_GET['old']) ) {
		Log::INFO('infusionsoft error: old not get' );
		return;
	}

	if( !isset($_GET['new']) ) {
		Log::INFO('infusionsoft error: new not get' );
		return;
	}
	
	if( !isset($_GET['contract']) ) {
		Log::INFO('infusionsoft error: contract not get' );
		return;
	}

	$type = $_GET['type'];
	$email = $_GET['email'];
	$old = $_GET['old'];
	$new = $_GET['new'];
	$contactID = $_GET['contract'];

	if($type == '' || $email == '' || $old == '' || $new == '' || $contactID == '') {
		Log::INFO('infusionsoft error: empty param '. $type . ', ' . $email . ', ' . $old . ', ' . $new . ', ' . $contactID );

		$response['error'] = 'infusionsoft error: empty param '. $type . ', ' . $email . ', ' . $old . ', ' . $new . ', ' . $contactID;
		$json_response = json_encode($response);
		echo $json_response;
		return;
	}

	//Initiate the Infusionsoft_App with API credentials
	$app = new Infusionsoft_App($appName, $apiKey);

	//Add the Infusionsoft App to the AppPool class
	Infusionsoft_AppPool::addApp($app);

	$res_remove = Infusionsoft_ContactService::removeFromGroup($contactID, $old);
	$response['res_remove'] = $res_remove;
	
	// $res_add = Infusionsoft_ContactService::addToGroup(2631, 273);
	$res_add = Infusionsoft_ContactService::addToGroup($contactID, $new);
	$response['res_add'] = $res_add;

	$json_response = json_encode($response);
	echo $json_response;
		
?>
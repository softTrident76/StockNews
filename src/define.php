<?php
	$path = __DIR__ .'/infusionsoft-sdk/Infusionsoft/';
	require_once($path.'infusionsoft.php');
	require_once($path.'config.php');
	
	$_API_WEBHOOK_HANDLER = 'http://localhost:9001/api/gethook';
	$_API_GET_RECIPIENT = 'http://localhost:9001/api/getrecipient';
	$_API_SAVE_TRANSMITTION = 'http://localhost:9001/api/savetransmission';

	$_SPARKPOST_APIKEY = 'Authorization: 7075e8dfa7c778de5808f11c1bafce9b941ad5f9';
	$_INFUSIONSOFT_APPNAME = 'sk687.infusionsoft.com';
	$_INFUSIONSOFT_APIKEY = '0f0598ec5f4b0b1adc2b0b7f06159377';

	$_DB_CONNECTOR = (object) array (
		'username' => "root",
		'password' => "admin",
		'hostname' => "localhost",
		'dabasename' => "sparkpostwebhook"
	);	

	$_INFUSIONSOFT_CLIENT_ID = '4bh68ag3gtrwtq6gjccrvtvm';
	$_INFUSIONSOFT_CLIENT_SECRET = 'UXMyD8ac5E';
	
	$_HOMEPAGE_FILE = 'index.php';	
	$_REDIRECT_URI = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
?>
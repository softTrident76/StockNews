<?php 
    require_once 'webhooksdk/log.php';
    require_once 'sparkpost.php';
    require_once 'define.php';
    // require 'vendor/autoload.php';
    // use SparkPost\SparkPost;
    // use GuzzleHttp\Client;
    // use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);

    $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
    $base_file_uri = 'index.php';
    $path = explode("?", $base_url);
    if( isset(pathinfo($path[0])['extension']) &&  pathinfo($path[0])['extension'] == 'php' )
    {   
        $base_file_uri = basename($base_url);     
        $base_url = pathinfo($path[0])['dirname'].'/';    
    }        
    else
    {
        $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
    }
      
    // $httpClient = new GuzzleAdapter(new Client());
    // $sparky = new SparkPost($httpClient, ['key' => 'be39c9aff0c827ea4b92396cfe25b8472abfa75b']);    
    Log::INFO($base_url);
    Log::INFO($base_file_uri);

    function convertToNewYork($datetime)
    {
        $localzone = new DateTimeZone("America/New_York");
        $time = new DateTime($datetime);
        $time->setTimezone($localzone);        
        $from = date_format($time, 'Y-m-d\TH:i'); 
        return $from;
    }

    function convertToUTC($datetime)
    {
        $localzone = new DateTimeZone("UTC");
        $time = new DateTime($datetime);
        $time->setTimezone($localzone);        
        $from = date_format($time, 'Y-m-d\TH:i'); 
        return $from;
    }

    function sortByTimestamp($a, $b)
    {
		if($a->timestamp == $b->timestamp)
			return 0;
		return ($a->timestamp < $b->timestamp) ? 1 : -1;
    }
    
    function sortByInjectTime($a, $b)
    {
        if($a['injection_time'] == $b['injection_time'])
			return 0;
		return ($a['injection_time']  < $b['injection_time'])  ? 1 : -1;
    }

  
?>
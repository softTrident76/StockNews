<?php 
    // require_once 'sparkpost.php';
    // require_once 'webhooksdk/log.php';

    // define('AUTH_KEY', 'be39c9aff0c827ea4b92396cfe25b8472abfa75b');
    // define('AUTH_KEY', '7075e8dfa7c778de5808f11c1bafce9b941ad5f9');

    // $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');    
    // $log = Log::Init($logHandler, 15);
    
    $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
    $path = explode("?", $base_url);
    if( isset(pathinfo($path[0])['extension']) )
        $base_url = pathinfo($path[0])['dirname'].'/';
    else
        $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
   

    function sparkpost($method, $uri, $payload = [])
    {
        $headers = [ 'Authorization: 7075e8dfa7c778de5808f11c1bafce9b941ad5f9' ];
        $defaultHeaders = [ 'Content-Type: application/json' ];
    
        $curl = curl_init();
        $method = strtoupper($method);       

        $finalHeaders = array_merge($defaultHeaders, $headers);        
        $url = 'https://api.sparkpost.com/api/v1/'.$uri;
        
        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        }
        else {
            $request = '?';
            foreach($payload as $key => $value) 
            {
                if( is_array($value) )
                {
                    $sublist = '';
                    foreach($value as $subvalue) 
                        $sublist .= ($subvalue . ',');
                    $sublist = rtrim($sublist, ',');
                    $request .= ($key . '=' . $sublist . '&');
                }
                else
                {
                    $request .= ($key.'='.$value.'&'); 
                }                    
            }
            
            $request = rtrim($request, '&');
            $url .= $request;
        }
       

        set_time_limit(0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $finalHeaders);             

        // echo $url;
        // echo '<br>';
    
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    ####################     GET    ###################    
    // $sending_domains_results = sparkpost('GET', 
    //         'events/message', 
    //         [
    //             'from' => '2019-09-13T00:00', 
    //             'campaigns' => 'TRIAL-SN-NEWSLETTER-20190918'			
    //         ], 
    //         $headers
    // );
    
    // echo "Sending domain results <br>"; 
    // echo json_encode(json_decode($sending_domains_results, false), JSON_PRETTY_PRINT);
    // var_dump(json_decode($sending_domains_results, false));
    // var_dump($sending_domains_results);
    // echo '<br>';
    // echo "finish results";
    // echo '<br>';
    // var_dump ($sending_domains_results['results']);
   
    ###################     TRANSMISSION    ###################
    // $recipients = [
    // 	["address" => ["email" => "pablo@adammesh.com"]],
    // 	["address" => ["email" => "pablo2@adammesh.com"]],
    // 	["address" => ["email" => "pablo3@adammesh.com"]]
    // 	];

    // $payload = [
    // 	'options' => [
    // 								'sandbox' => false,
    // 								/*'start_time' => '2019-09-14T12:30:00-04:00' get_option('send_schedule'),*/
    // 								'start_time' => '2019-09-20T17:56:21-00:00',
    // 								'open_tracking' => true,
    // 								'click_tracking' => true
    // 				],
    // 	'campaign_id' => 'hello'.'-'.date("Ymd"),
    // 	'content'     => [
    // 						'from'    =>  [
    // 										'name' => "StockNews.com",
    // 										'email'=> "test@adammesh.com"
    // 									],
    // 						'subject' => 'gliq_newsletter_headline',
    // 						'html'    => '<h1>whats up <a href=\"https://adammesh.com\">click here!</a><br><br><a href=\"https://members.adammesh.com\">2nd click here!</a></h1>',
    // 						'reply_to' => 'test@adammesh.com'
    // 					],
    // 	'recipients' => $recipients
    // ];
    // $email_results = sparkpost('POST', 'transmissions', $payload);
?>
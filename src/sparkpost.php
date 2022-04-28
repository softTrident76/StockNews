<?php     
    // require_once 'sparkpost.php';
    // require_once 'webhooksdk/log.php';

    $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
    $path = explode("?", $base_url);
    if( isset(pathinfo($path[0])['extension']) )
        $base_url = pathinfo($path[0])['dirname'].'/';
    else
        $base_url = ("http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
   

    function sparkpost($method, $uri, $payload = [])
    {
        require 'define.php';
        $headers = [$_SPARKPOST_APIKEY];
        // $headers = [ 'Authorization: bf697c82561b6dc7184b3ce56e500200aac89d04' ];
        $defaultHeaders = [ 'Content-Type: application/json' ];
    
        $curl = curl_init();
        $method = strtoupper($method);       

        $finalHeaders = array_merge($defaultHeaders, $headers);        
        $url = 'https://api.sparkpost.com/api/v1/'.$uri;
        
        if ($method === 'POST') {
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

    function byteArray2Hex($byteArray) {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
      }
    
    
    function getToken($info, $time)
    {
        $stime = "" . $time;
    
        if(strlen($info) < strlen($stime) )
        {
            str_pad($info, strlen($stime) - strlen($info), "0", STR_PAD_LEFT);
        }
    
        $aInfo = array_values(unpack('C*', $info));
        $aTime = array_values(unpack('C*', $stime));
    
        $k = 0;
        $len = count($aTime);
        for($idx = 0; $idx < count($aInfo); $idx++)
        {
            $aInfo[$idx] = $aInfo[$idx] ^ $aTime[$k];   
     
            $k++;
            if($k == $len)
                $k = 0;
        }
    
        return byteArray2Hex($aInfo);
    }

    function apiGetRecipient($groups, $infusion_id, $infusion_secret)
	{
        require 'define.php';

        $current = time();
        
        // $current = '1577986294';
        // $param = array('groups' => '273');
        $param = array('groups' => $groups, 'id' => getToken($infusion_id, $current), 'secret' => getToken($infusion_secret, $current) );
		$json_string = json_encode($param);
	
		$ch = curl_init($_API_GET_RECIPIENT);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json_string))
		);
		$result = curl_exec($ch);
		$ret = json_decode($result, true);
		return $ret;
	};

	function apiSaveTransmission($campaign_id, $recipients)
	{
        require 'define.php';
		// $trans_param = array('stocknews-saturday-newsletter-1025201918-00-55' => array(
		//     array('email' => 'neil@yolopub.com', 'firstname' => 'Reader', 'lastname' => '-'),
		//     array('email' => 'wasifchipa84@gmail.com.com', 'firstname' => 'Read', 'lastname' => '-')
		// ));
		
		// $trans_param = array('stocknews-saturday-newsletter-1025201918-00-55' => $ret);
		$trans_param = array($campaign_id => $recipients);    
		$json_string = json_encode($trans_param);
		$ch = curl_init($_API_SAVE_TRANSMITTION);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json_string))
		);
        $result = curl_exec($ch);
        $ret = json_decode($result, true);
		return $ret;
    }

    function fn_sendCampaign($groupid, $newsletter_name, $fromaddress, $list_used, $ip_pool, $timeschedule)
    {
        require 'define.php';
        try 
        {
            $people = apiGetRecipient($groupid, $_INFUSIONSOFT_CLIENT_ID, $_INFUSIONSOFT_CLIENT_SECRET);
            if(count($people) <= 0 )
                throw new Exception("cant' get any recipients. maybe you have to wait some minutes, and then try again.");
            
            foreach ($people as $contact) {
                $recipients[] = array( 'address'=>array('email'=>$contact['email']) );
            }

            $recipientsJSON = json_encode($recipients, JSON_FORCE_OBJECT);
            $campaign_id = strtolower($newsletter_name.'-'.date("dmYH-i-s"));
            
            // ##################     Create a Template    ################### //  
            $payload = [
                        "id" => $campaign_id,
                        "name" => $campaign_id,
                        "published"=> true,
                        "description" => "Template for a " . $campaign_id,
                        "shared_with_subaccounts" => false,
                        "options" => [
                            "open_tracking" => true,
                            "click_tracking" => true
                        ],

                        "content" => [
                            "from" => [
                            "email" => $fromaddress,
                            "name" => $newsletter_list_name
                            ],      

                            'subject' => stripslashes(fn_remove_smart_quotes(get_option('gliq_newsletter_headline'))),
                            'html'    => fn_remove_smart_quotes(file_get_contents(plugin_dir_url(__FILE__).$newsletter_name.'.html')),
                            'reply_to' => $fromaddress
                        ]
            ];
            $response = sparkpost('POST', 'templates', $payload);

            if( !isset($response['results']['id']) )
            {
                echo "<pre>";
                print_r ($response);
                echo "</pre>";
                echo '<br>';
                throw new Exception("cant' create a template");
            }

            // ##################     send the campaign   ################### //  
            $payload = [
                    'options' => [
                                    'sandbox' => false,
                                    /*'start_time' => '2019-09-14T12:30:00-04:00' get_option('send_schedule'),*/
                                    'start_time' => $timeschedule,
                                    'open_tracking' => true,
                                    'click_tracking' => true,
                                    'ip_pool' => $ip_pool
                                ],
                                    
                    'metadata' => [
                                    'ListUsed' => $list_used
                                ],
                                    
                    'campaign_id' => $campaign_id,
                    'content'     => [
                                        'template_id' => $campaign_id       
                                    ],
                    'recipients' => $recipients
            ];

            $response = sparkpost('POST', 'transmissions', $payload);       
            if( isset($response['errors']) ) 
            {
                echo "<pre>";
                print_r ($response);
                echo "</pre>";
                echo '<br>';
                throw new Exception("cant' transmit the campaign");
            }
            else 
            {                       
                $response = apiSaveTransmission($campaign_id, $people);  // seito added
                echo "<pre>";
                print_r ($response);
                echo "</pre>";
                echo '<br>';
            }
            return count($people);
        } catch (Exception $exception) {
            echo $exception;
            echo '<br>';
            return -1;
        }
    }

    function fn_remove_smart_quotes($content)
    {

        $content= str_replace(
            array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
            array("'", "'", '"', '"', '-', '--', '...'),
            $content
        );

        $content= str_replace(
            array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
            array("'", "'", '"', '"', '-', '--', '...'), 
            $content
        );
        return $content;
    }


    ####################     API MANUAL    #######################
    // header("Content-Type:application/json");
    // require_once 'sparkpost.php';

    // $recipient = apiGetRecipient('273');
    // if( $recipient == NULL ) 
    // {
    //     var_dump($recipient);
    //     echo 'no response from server. please ask to supportor.';
    //     return;
    // }
    // else if(count($recipient) <= 0 )
    // {
    //     var_dump($recipient);
    //     echo 'recipient not ready. please waiting 5 ~ 10 mins ';
    //     return;
    // }   
    
    // $ret = apiSaveTransmission('stocknews-saturday-newsletter-1025201918-00-55', $recipient);        
    // if($ret == NULL) 
    // {      
    //     echo 'no response from server. please ask to supportor.';
    //     return;
    // }
    // if( $ret['result'] != 'success')    
    //     echo $ret['result'];    
    
    ####################     GET    #######################    
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
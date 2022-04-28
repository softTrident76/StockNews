<?php
/* 
  Newsletter Wrapper Admin Page
  (For newsletters that are manually typed, then pasted into a box, then spits out html file for sending through email)
*/

require_once 'sparkpost.php';

//require 'vendor/autoload.php';

//use SparkPost\SparkPost;
//use GuzzleHttp\Client;
//use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

session_start();
	$newsletter_name = 'stocknews-newsletter';	
	$_SESSION['newsletter_name'] = $newsletter_name;

// $sparky->setOptions(['async' => false]);
// var_dump($sparky);
// return;

// Load Infusionsoft
//require '../infusionsoft-sdk/Infusionsoft/infusionsoft.php';

// // Load config file (copy config.sample.php to config.php and put your clientid (key) and secret in.
// require 'config.php';

$path = plugin_dir_path( __FILE__ ).'../infusionsoft-sdk/Infusionsoft/';
require_once($path.'infusionsoft.php');
require_once($path.'config.php');

// $people = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => '102'),$limit = 1000, $page = 0);
// var_dump($people);
// return;
	

function get_Recursive(&$collector, $recursive, $page_depth = 0) 
{
  if ($recursive != null && count($recursive) <= 1000 && count($recursive) > 0) 
  {     
    $people_temp = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => '102'),$limit = 1000, $page = $page_depth);
    if($people_temp == null || count($recursive) == 0)
      return;
    $collector = array_merge($collector, $people_temp);

    $page_depth++;
    get_Recursive($collector, $people_temp, $page_depth);    
  }
}

function stocknews_admin_newsletter_wrapper_page() {

	session_start();
	$newsletter_name = 'stocknews-newsletter';	
	$_SESSION['newsletter_name'] = $newsletter_name;

	// var_dump($_SESSION['newsletter_name']);
	// echo '<br>';
	
  // ############################################################# //
  // #####################    TEST PARTS      #################### //
  // ############################################################# //

  // ##################     Create a Template    ################### //  
  // $payload = [
  //             "id" => "summer_sale2",
  //             "name" => "Summer Sale!",
  //             "published"=> true,
  //             "description" => "Template for a Summer Sale!",
  //             "shared_with_subaccounts" => false,
  //             "options" => [
  //               "open_tracking" => true,
  //               "click_tracking" => true
  //             ],

  //             "content" => [
  //               "from" => [
  //                 "email" => "contact@stocknews.com",
  //                 "name" => "Example Company Marketing"
  //               ],
  //               "subject" => "Summer deals for",
  //               "reply_to" => "Summer deals <summer_deals@company.example>",
  //               // "text" => "Check out these deals!",
  //               "html" => "<b>Check out these deals!</b>"
  //               // "headers" => [
  //               //     "X-Example-Header" => "Summer2014"
  //               // ]
  //             ]
  // ];

  // $response = sparkpost('POST', 'templates', $payload);

  // echo "<pre>";
  // print_r ($response['results']['id']);
  // echo "</pre>";
  // echo '<br>';

  // ##################     update a Template    ################### //
  // $response = sparkpost('PUT', 'templates/summer_sale', 
  //             [
  //              'published' => true
  //             ]);
  // echo "<pre>";
  // print_r ($response);
  // echo "</pre>";
  // echo '<br>';

  // $response = sparkpost('DELETE', 'templates/stocknews-newsletter-0210201916-21-11');
  // echo "<pre>";
  // print_r ($response);
  // echo "</pre>";
  // echo '<br>';
  // return;

  // $response = sparkpost('GET', 'templates', 
  //             [
  //              'draft' => 'false'
  //             ]);
  // echo "<pre>";
  // print_r ($response);
  // echo "</pre>";
  // echo '<br>';
  // return;
// #########################   END   ########################## //

	
  if ( !current_user_can( 'publish_posts' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
 
  echo '<div class="wrap">';
  echo '<h2>Daily Newsletter Sender</h2>';
    if ($_GET["saved"] == 1) {


	// var_dump($_SESSION['newsletter_name']);
	// echo '<br>';
	
    echo '<hr><p>Review the email below, and if all looks good, click the confirmation button at the very bottom of this page to send or schedule the newsletter.</p><hr>';
    
    echo $_POST['sendschedule'];

    echo '<iframe style="background:#fff" width="700" height="1000" src="'.plugin_dir_url(__FILE__).$newsletter_name.'.html"></iframe>';

    echo '<hr>';

    sn_alert_warning('<strong>IMPORTANT:</strong> If you see any problems with the newsletter above, simply <strong>close this browser tab</strong>, go back to your original Daily Newsletter Sender tab, and edit again from there.');

    echo '<hr>';

    $confirm_code = base64_encode(time());
    update_option("newsletter_confirm_code",$confirm_code);

    echo '<p><a href="'.admin_url('admin.php?page=sn_admin_newsletter_wrapper_generator&confirmed=1&confirm_code='.$confirm_code).'" class="button-primary" onclick="return confirm(\'Are you 100% certain? This will automatically send the newsletter to our lists, so everything must be perfect with it!\')">All looks good, let\'s send the newsletter now!</a></p>';

	//do_action( 'admin_post_newsletter_wrapper');
	sn_admin_newsletter_wrapper();
    /*

    // spit out links to download the HTML files
    echo '<div style="padding:20px; background: #ffffff; border:1px solid #000">';
    echo '<p>Right click each of these files, Save As, and save to your Desktop, overwriting previous files if necessary.</p>';
    echo '<ul>';
    echo '<li><strong><a href="'.plugin_dir_url(__FILE__).'PAID-SN-NEWSLETTER.html">PAID-SN-NEWSLETTER.html</a></strong></li>';
    echo '<li><strong><a href="'.plugin_dir_url(__FILE__).'TRIAL-SN-NEWSLETTER.html">TRIAL-SN-NEWSLETTER.html</a></strong></li>';
    echo '<li><strong><a href="'.plugin_dir_url(__FILE__).'EXPIRED-SN-NEWSLETTER.html">EXPIRED-SN-NEWSLETTER.html</a></strong></li>';
    echo '<li><strong><a href="'.plugin_dir_url(__FILE__).'FREE-SN-NEWSLETTER.html">FREE-SN-NEWSLETTER.html</a></strong></li>';
    echo '</ul><hr>';
    echo '<p><strong>Important!</strong> If this tool didn\'t open a second "Add Snippet" tab, you need to <a href="http://support.lesley.edu/support/solutions/articles/4000009686-allowing-pop-ups-for-specific-sites" target="_blank">allow popups</a> from https://stocknews.com/ on your browser.</p>';
    echo '</div><br><br>';

    */

    //echo '<script>window.open("'.admin_url().'admin.php?page=stocknews_admin_add_snippet&populate_default_content","_blank");</script>';
  } elseif ($_GET["confirmed"] == 1) {

	var_dump($SESSION['newsletter_name']);
	echo '<br>';
	
	echo $newsletter_name;
	
    // first ensure we got here properly by checking the timestamp (should be within 10 seconds of the timestamp given)
    if ($_GET["confirm_code"] == get_option("newsletter_confirm_code")) {

      $timeschedule = get_option('send_schedule') . ':00-00:00';

      // set our code with a dummy value so it can't accidentially be used again
      update_option("newsletter_confirm_code",base64_encode(time()));

      echo "<hr>";

      echo "<p>If You see any error messages below, please contact the administrator.</p>";

      echo "<hr>";

      // do four loops to update our four newsletter templates
      // if ($i == 0) {
      //   $newsletter_name = 'PAID-SN-NEWSLETTER';
      // } elseif ($i == 1) {
      //   $newsletter_name = 'EXPIRED-SN-NEWSLETTER';
      // } elseif ($i == 2) {
      //   $newsletter_name = 'FREE-SN-NEWSLETTER';
      // } elseif ($i == 3) {
      //   $newsletter_name = 'TRIAL-SN-NEWSLETTER';
      // }

      //$newsletter_name = 'TRIAL-SN-NEWSLETTER';
	  	  
      $people = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => '102'),$limit = 1000, $page = 0);
      get_Recursive($people, $people, 1);

      foreach ($people as $contact) {
          $recipients[] = array( 'address'=>array('email'=>$contact->Email) );
      }
      
      $recipientsJSON = json_encode($recipients, JSON_FORCE_OBJECT);

      // Newsletter Details
      $newsletter_list_name = "StockNews.com";
      $fromaddress = "contact@stocknews.com";
      $list_used = "StockNews.com Newsletter List";
      $ip_pool = "stocknews";
      $campaign_id = $newsletter_name.'-'.date("dmYH-i-s");
      
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

                    'subject' => stripslashes(sn_remove_smart_quotes(get_option('gliq_newsletter_headline'))),
                    'html'    => sn_remove_smart_quotes(file_get_contents(plugin_dir_url(__FILE__).$newsletter_name.'.html')),
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
          return;
      }    

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
                                  // 'from'    =>  [
                                  //                 'name' => $newsletter_list_name,
                                  //                 'email'=> $fromaddress
                                  //               ],
                                  // 'subject' => stripslashes(sn_remove_smart_quotes(get_option('gliq_newsletter_headline'))),
                                  // 'html'    => sn_remove_smart_quotes(file_get_contents(plugin_dir_url(__FILE__).$newsletter_name.'.html')),
                                  // 'reply_to' => $fromaddress
                              ],
              'recipients' => $recipients
      ];

      $response = sparkpost('POST', 'transmissions', $payload);
      echo "<pre>";
      print_r ($response);
      echo "</pre>";
      echo '<br>';

      try 
      {
          if( isset($response['errors']) )
            throw new Exception('no result');
          else 
            echo "Successful Transmission";
      } catch (Exception $exception) {
            echo $exception;
      }      

    } 
    else 
    {
      die("You've reached this page in error.");
    } // end if

  } else {
	
    echo "<hr><p><em>This tool allows you to paste, edit, check over, and finally to send the daily email newsletter.</em></p><hr><br>";
    echo '<form action="'.admin_url().'admin-post.php" method="post" id="snippet-form" target="_blank">';

  /*  echo "<strong>Main Target Symbol <small>(ex: SPDR Dow Jones Industrial Average ETF(NYSEARCA:DIA))</small></strong><br>";
    echo "<input style=\"width:100%\" type=\"text\" name=\"source_symbol\"><br><br>";*/

    echo '<strong>Newsletter Title</strong> (You\'ll also use this as your email subject)<br>';
    echo '<input type="text" name="newsletter_title" required style="width:100%"><br><br>';

    //echo '<strong>Newsletter Intro</strong><br>
    //<em>Optional</em>';
    //wp_editor($default_content, 'newsletter_body');
    echo '<input type="hidden" name="action" value="newsletter_wrapper">';

    echo '<br><strong>Article 1 Headline</strong><br>';
    echo '<input type="text" name="headline1" style="width:100%"><br><br>';
    echo '<strong>Article 1 Link/URL</strong><br>';
    echo '<input type="text" name="articleurl1" style="width:100%"><br><br>';
    echo '<strong>Article 1 Image Link/URL</strong><br>';
    echo '<input type="text" name="imageurl1" style="width:100%"><br><br>';
    echo '<strong>Article 1 Excerpt</strong><br>';
    wp_editor($default_content, 'article1body');

    
    
     echo '<br><strong>Article 2 Headline</strong><br>';
    echo '<input type="text" name="headline2" style="width:100%"><br><br>';
    echo '<strong>Article 2 Link/URL</strong><br>';
    echo '<input type="text" name="articleurl2" style="width:100%"><br><br>';
    echo '<strong>Article 2 Image Link/URL</strong><br>';
    echo '<input type="text" name="imageurl2" style="width:100%"><br><br>';
    echo '<strong>Article 2 Excerpt</strong><br>';
    wp_editor($default_content, 'article2body');

      
    
     echo '<br><strong>Article 3 Headline</strong><br>';
    echo '<input type="text" name="headline3" style="width:100%"><br><br>';
    echo '<strong>Article 3 Link/URL</strong><br>';
    echo '<input type="text" name="articleurl3" style="width:100%"><br><br>';
    echo '<strong>Article 3 Image Link/URL</strong><br>';
    echo '<input type="text" name="imageurl3" style="width:100%"><br><br>';
    echo '<strong>Article 3 Excerpt</strong><br>';
    wp_editor($default_content, 'article3body');
    echo '<br><br>';
    
    /* removed this bit of javascript from below block. the add media button was not working with this
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	    */
    echo '<script src="https://cdn.jsdelivr.net/momentjs/2.14.1/moment.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

<h4>Schedule Email (leave blank to send "NOW")</h4>
                  <div class="input-group date" id="datetimepicker1">
                     <input type="text" class="form-control" name="sendschedule"/>
                     <span class="input-group-addon">
                     <span class="glyphicon glyphicon-calendar"></span>
                     </span>
                  </div>
              
<script>
  $(function () {
    $("#datetimepicker1").datetimepicker({format: "YYYY-MM-DDTHH:mm"});
 });
 

</script>';
          
    echo '<input type="submit" value="Generate!" style="font-size:24px">';
    echo '</form>';

}

  echo '</div>';
}

function sn_admin_newsletter_wrapper() {

	
	
  if (!empty($_POST)) {

    // define our title and newsletter body
    $headline1 = $_POST['headline1'];
	$imageurl1 = $_POST['imageurl1'];
	$articleurl1 = $_POST['articleurl1'];
	$articlebody1 = $_POST['article1body'];
	
	$headline2 = $_POST['headline2'];
	$imageurl2 = $_POST['imageurl2'];
	$articleurl2 = $_POST['articleurl2'];
	$articlebody2 = $_POST['article2body'];
	
	$headline3 = $_POST['headline3'];
	$imageurl3 = $_POST['imageurl3'];
	$articleurl3 = $_POST['articleurl3'];
	$articlebody3 = $_POST['article3body'];
	
	
	if (!empty($_POST['sendschedule'])) {
		
$dt = strtotime($_POST['sendschedule']); 

$dt = date("M j, Y", $dt);

$sendschedule = $_POST['sendschedule'];
} else {
	$dt = date("M j, Y");
	
	$sendschedule = date('Y-m-d\TG:i');
}
	
	

    $newsletter_title = $_POST["newsletter_title"];
    
    /* $signature = '<pre style="font-family: Helvetica, arial, sans-serif; font-size:18px;">
<img src="https://stocknews.com/wp-content/uploads/2019/03/reity.jpg" style="width:90px !important;">
<span style="font-family: Helvetica, arial, sans-serif; font-size:18px;">Steve Reitmeister
<em>…but my friends call me Reity (pronounced “Righty”)</em>
CEO, StockNews</span></pre>
<hr>'; */

//<h3 style="font-family: Helvetica Neue, Arial, Helvetica, sans-serif;color: black; font-size:20px;">Top Articles on StocksNews.com</h3>
$newsletter_articles = '<!--Article 1-->
                <tr>
                    <!-- dir=ltr is where the magic happens. This can be changed to dir=rtl to swap the alignment on wide while maintaining stack order on narrow. -->
                    <td dir="ltr" height="100%" valign="top" width="100%" style="font-size:0; padding: 10px; background-color: #ffffff;">
                        <!--[if mso]>
                        <table role="presentation" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
                        <tr>
                        <td valign="top" width="220" style="width: 220px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 220px; min-width:160px; vertical-align:top; width:100%;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="padding: 0 10px 10px 10px;">
                                        <a href="'.$articleurl1.'"><img src="'.$imageurl1.'" width="200" height="" border="0" alt="Read: '.stripslashes($headline1).'" class="center-on-narrow" style="width: 100%; max-width: 200px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 16px; line-height: 15px; color: #555555;"></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        <td valign="top" width="440" style="width: 440px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 440px; min-width:280px; vertical-align:top;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="font-family: sans-serif; font-size: 16px; line-height: 20px; color: #555555; padding: 10px 10px 0; text-align: left;" class="center-on-narrow">
                                        <h2 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 20px; line-height: 22px; color: #333333; font-weight: bold;"><a href="'.$articleurl1.'" style="color:black; text-decoration: none;">'.stripslashes($headline1).'</a></h2>
                                        <p style="margin: 0 0 10px 0;">'.stripslashes($articlebody1).' <a href="'.$articleurl1.'">Full Story</a></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        <hr style="border: 0;border-top: 1px solid #ccc;margin: 20px 0;">
                    </td>
                </tr>
                
                <!--Article 2-->
                <tr>
                    <!-- dir=ltr is where the magic happens. This can be changed to dir=rtl to swap the alignment on wide while maintaining stack order on narrow. -->
                    <td dir="ltr" height="100%" valign="top" width="100%" style="font-size:0; padding: 10px; background-color: #ffffff;">
                        <!--[if mso]>
                        <table role="presentation" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
                        <tr>
                        <td valign="top" width="220" style="width: 220px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 220px; min-width:160px; vertical-align:top; width:100%;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="padding: 0 10px 10px 10px;">
                                        <a href="'.$articleurl2.'"><img src="'.$imageurl2.'" width="200" height="" border="0" alt="Read: '.stripslashes($headline2).'" class="center-on-narrow" style="width: 100%; max-width: 200px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 16px; line-height: 15px; color: #555555;"></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        <td valign="top" width="440" style="width: 440px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 440px; min-width:280px; vertical-align:top;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="font-family: sans-serif; font-size: 16px; line-height: 20px; color: #555555; padding: 10px 10px 0; text-align: left;" class="center-on-narrow">
                                        <h2 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 20px; line-height: 22px; color: #333333; font-weight: bold;"><a href="'.$articleurl2.'" style="color:black; text-decoration: none;">'.stripslashes($headline2).'</a></h2>
                                        <p style="margin: 0 0 10px 0;">'.stripslashes($articlebody2).' <a href="'.$articleurl2.'">Full Story</a></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        <hr style="border: 0;border-top: 1px solid #ccc;margin: 20px 0;">
                    </td>
                </tr>
                
                <!--Article 3-->
				<tr>
                    <!-- dir=ltr is where the magic happens. This can be changed to dir=rtl to swap the alignment on wide while maintaining stack order on narrow. -->
                    <td dir="ltr" height="100%" valign="top" width="100%" style="font-size:0; padding: 10px; background-color: #ffffff;">
                        <!--[if mso]>
                        <table role="presentation" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
                        <tr>
                        <td valign="top" width="220" style="width: 220px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 220px; min-width:160px; vertical-align:top; width:100%;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="padding: 0 10px 10px 10px;">
                                        <a href="'.$articleurl3.'"><img src="'.$imageurl3.'" width="200" height="" border="0" alt="Read: '.stripslashes($headline3).'" class="center-on-narrow" style="width: 100%; max-width: 200px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 16px; line-height: 15px; color: #555555;"></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        <td valign="top" width="440" style="width: 440px;">
                        <![endif]-->
                        <div style="display:inline-block; margin: 0 -1px; max-width: 440px; min-width:280px; vertical-align:top;" class="stack-column">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td dir="ltr" style="font-family: sans-serif; font-size: 16px; line-height: 20px; color: #555555; padding: 10px 10px 0; text-align: left;" class="center-on-narrow">
                                        <h2 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 20px; line-height: 22px; color: #333333; font-weight: bold;"><a href="'.$articleurl3.'" style="color:black; text-decoration: none;">'.stripslashes($headline3).'</a></h2>
                                        <p style="margin: 0 0 10px 0;">'.stripslashes($articlebody3).' <a href="'.$articleurl3.'">Full Story</a></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        <hr style="border: 0;border-top: 1px solid #ccc;margin: 20px 0;">
                    </td>
                </tr>
                
	        </table>'; 
    
    //<hr style="border: 0;border-top: 1px solid #ccc;margin: 20px 0;">

if (!empty($_POST["newsletter_body"])) {

    $newsletter_body = "<p>".sn_google_doc_newsletter(stripslashes($_POST["newsletter_body"]))."</p><hr style=\"border: 0;border-top: 1px solid #ccc;margin: 20px 0;\">".$newsletter_articles;
    } else {
    $newsletter_body = $newsletter_articles;
};
    
    
    $newsletter_preview = substr(strip_tags($newsletter_body), 0, 100)."...";

    // store our newsletter title in the database so we can grab it later
    update_option("gliq_newsletter_headline",$newsletter_title);
    
    update_option("send_schedule",$sendschedule);

    // hack up the newsletter body to find the first paragraph (after which we'll insert upsells later)
    $temp_body = str_replace('<br>','<br />',$newsletter_body);
    $temp_arr = explode('<br />', $temp_body);
    $first_paragraph = $temp_arr[0];

    // define our body for our news snippet
    $newsletter_body_for_snippet = str_replace($first_paragraph,$first_paragraph.'<!--more--><br /><br />',$newsletter_body.$newsletter_articles);

    // extract security name and symbol from source_symbol
    $sym = trim(strrchr($_POST["source_symbol"],"("));
    $name = str_replace($sym, '', $_POST["source_symbol"]);
    $fullsym = str_replace("AMERICAN","",str_replace("ARCA","",sn_between_chars("(",")",$sym)));
    $arr = explode(":", $fullsym);
    $sym = $arr[1];

    // populate default values for when we create our snippet
    update_option("populate_default_content",$newsletter_body_for_snippet);
    update_option("populate_default_company",$name);
    update_option("populate_default_symbol",$sym);
    update_option("populate_default_fullsym",$fullsym);
    update_option("populate_default_event","Commentary");
    update_option("populate_default_top_story",1);
    update_option("populate_default_premium","");
    update_option("populate_default_headline",$newsletter_title);
    update_option("populate_default_featured_image","");
    update_option("populate_default_notes","");

    // header portion of newsletter
    $newsletter_head =
    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <title>'.$newsletter_title.'</title>
    
        <style>

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        img {
            -ms-interpolation-mode:bicubic;
        }

        a {
            text-decoration: none;
        }

        a[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links a,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        .im {
            color: inherit !important;
        }

        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        img.g-img + div {
            display: none !important;
        }

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
                min-width: 414px !important;
            }
        }

    </style>

    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

	    .button-td,
	    .button-a {
	        transition: all 100ms ease-in;
	    }
	    .button-td-primary:hover,
	    .button-a-primary:hover {
	        background: #555555 !important;
	        border-color: #555555 !important;
	    }

	    /* Media Queries */
	    @media screen and (max-width: 480px) {

	        .stack-column,
	        .stack-column-center {
	            display: block !important;
	            width: 100% !important;
	            max-width: 100% !important;
	            direction: ltr !important;
	        }

	        .stack-column-center {
	            text-align: center !important;
	        }

	        .center-on-narrow {
	            text-align: center !important;
	            display: block !important;
	            margin-left: auto !important;
	            margin-right: auto !important;
	            float: none !important;
	        }
	        table.center-on-narrow {
	            display: inline-block !important;
	        }

	        .email-container p {
	            font-size: 17px !important;
	        }
	    }

    </style>
    <!-- Progressive Enhancements : END -->
</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #fff;">
	<center style="width: 100%; background-color: #fff;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffe;">
    <tr>
    <td>
    <![endif]-->

        <!-- Visually Hidden Preheader Text : BEGIN -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">'.stripslashes($articlebody1).'</div>
        <!-- Visually Hidden Preheader Text : END -->

        <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
        <!-- Preview Text Spacing Hack : BEGIN -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>
        <!-- Preview Text Spacing Hack : END -->

        <!--
            Set the email width. Defined in two places:
            1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 680px.
            2. MSO tags for Desktop Windows Outlook enforce a 680px width.
        -->
        <div style="max-width: 680px; margin: 0 auto;" class="email-container">
            <!--[if mso]>
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="680">
            <tr>
            <td>
            <![endif]-->

	        <!-- Email Body : BEGIN -->
	        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
		        <!-- Email Header : BEGIN -->
	            <tr>
	                <td style="padding: 5px 0; text-align: center; background-color: #262626;">
		                <a href="https://stocknews.com/"><img src="https://stocknews.com/img/stocknews.com-logo-ic.png" height="30" border="0" style="border: 0;outline: none;text-decoration: none;margin:15px 10px;" alt="StockNews.com" align="left" /></a>
		                <h2 style="margin: 18px 10px 0 0; font-family: sans-serif; font-size: 18px; line-height: 22px; color: #fff; font-weight: bold;" align="right">'.$dt.'</h2>
	                </td>
	            </tr>
		        <!-- Email Header : END -->';
            
            
            
$newsletter_head_title = '<div><h3 class="article-body" style="font-family:Helvetica, Arial, sans-serif;font-size:26px;">'.stripslashes($newsletter_title).'</h3><hr>';
            
            //<p class="welcome" style="color:#999;font-family:Helvetica, arial, sans-serif;margin-top:0;line-height:1.4em;font-size:.9em;border-bottom:1px solid #ccc;padding-bottom:20px;margin-bottom:20px;"></p>
            


  /*  $newsletter_footer = '
    <br><br>

<hr style="border:0;border-top:1px solid #ccc;margin:20px 0;">
<div class="wrap-links" style="text-align:center;width:100%;clear:both;">
<ul style="margin:1em auto;padding:0;list-style-type:none;width:100%;display:inline-block;">
<li style="margin:1em 0;border-radius:8px;padding:.66em 1em;font-size:.9em;background:#448BD4;text-align:center;">
<a href="http://stocknews.com/watchlist/" style="color:#FFFFFF;text-decoration:none;font-weight:bold;">Manage Your Watchlist <span style="display: inline-block;     min-width: 10px;     padding: 3px 7px;     font-size: 12px;     font-weight: 700;     line-height: 1;     color: #fff;     text-align: center;     white-space: nowrap;     vertical-align: baseline;     background-color: #777;     border-radius: 10px;">NEW</span></a>
</li>
<li style="margin:1em 0;border-radius:8px;padding:.66em 1em;font-size:.9em;background:#448BD4;text-align:center;">
<a href="http://stocknews.com/powr-upgrades-downgrades/" style="color:#FFFFFF;text-decoration:none;font-weight:bold;">Upgrades/Downgrades <span style="display: inline-block;     min-width: 10px;     padding: 3px 7px;     font-size: 12px;     font-weight: 700;     line-height: 1;     color: #fff;     text-align: center;     white-space: nowrap;     vertical-align: baseline;     background-color: #777;     border-radius: 10px;">NEW</span></a>
</li>
<li style="margin:1em 0;border-radius:8px;padding:.66em 1em;font-size:.9em;background:#448BD4;text-align:center;">
<a href="http://stocknews.com/" style="color:#FFFFFF;text-decoration:none;font-weight:bold;">Visit StockNews.com</a>
</li>
<li style="margin:1em 0;border-radius:8px;padding:.66em 1em;font-size:.9em;background:#448BD4;text-align:center;">
<a href="mailto:contact@stocknews.com" style="color:#FFFFFF;text-decoration:none;font-weight:bold;">Contact StockNews.com</a>
</li>
<li style="margin:1em 0;border-radius:8px;padding:.66em 1em;font-size:.9em;background:#448BD4;text-align:center;">
<a href="http://stocknews.com/news/" style="color:#FFFFFF;text-decoration:none;font-weight:bold;">Breaking News Feed</a>
</li>
</ul>
</div>
<div class="footer" style="background:#E4E4E4; padding:50px 10px 50px 10px; text-align:center; font-size:13px; color:#555">
  This email was sent to #email#<br>
  You are receiving this email because you opted in at our website at StockNews.com.<br><br>
  <a href="http://www.gliq.com/cgi-bin/unsub_dedicated?stocknews&#email#&#ccode#&rt_1">Unsubscribe from this list</a><br><br>
  StockNews.com<br>146 W 29th Street, Suite 8E<br>New York, NY 10001
</div>
<!--
<div class="footer" style="background:#E4E4E4;position:relative;overflow:hidden;padding:10px 0 60px 0;clear:both;">
<div class="social-icons" style="text-align:center;">
</div>
</div>
-->
</div>
</div>
</body>
</html>
    '; */
    
    $newsletter_footer = '<!-- Email Footer : BEGIN -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 680px;">
	            <tr>
		            <td style="padding: 10px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center;">
			            <p style="font-family: Helvetica Neue, Arial, Helvetica, sans-serif;color: black; font-size:17px;">Enjoy more articles, tools and POWR stock ratings on <a href="https://stocknews.com/">StockNews now!</a></p>
		            </td>
	            </tr>
                <tr>
                    <td style="padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; background-color: #E4E4E4;">
                        
		                This email was sent to {{email}}<br>
  You are receiving this email because you opted in at our website at StockNews.com.
  <br><br>
  StockNews.com<br>146 W 29th Street, Suite 8E<br>New York, NY 10001<br><br>
  <a data-msys-unsubscribe="1"
   href="http://stocknews.com/sorry-to-see-you-go/"
   title="Unsubscribe from this list ">Unsubscribe from this list</a>                                          </td>
                </tr>
            </table>
            <!-- Email Footer : END -->

            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>

    <!--[if mso | IE]>
    </td>
    </tr>
    </table>
    <![endif]-->
    </center>
</body>
</html>';

    //echo "<pre>".htmlentities($newsletter_head.$newsletter_body.$newsletter_footer)."</pre>";
    //die("");

    // loop through four times to generate our four versions of this newsletter, one for each of our segments: paid, trial, free, and expired
    for ($x=0;$x<4;$x++) {

      //echo "<pre>".htmlentities($newsletter_head.$newsletter_body.$newsletter_footer)."</pre>";

      if ($x == 0) {
        $this_html = $newsletter_body;
        $sn_prefix = 'PAID';
      } elseif ($x == 1) {
        // trial member upsell
       /* $add_html = '
        <div style="background:#feeaa4; padding:20px">
          <strong>Your StockNews.com Account Will Expire Soon!</strong><br><br>
          Upgrade to a paid account today for <strong>only $99 per year</strong> to retain access to our daily newsletters, POWR Ratings, Best Stocks List, and much more!<br><br>
          <a href="https://stocknews.com/members/signup" class="button">Upgrade To Premium Now!</a>
        </div> 
        '; */
        $add_html = '';
        $this_html = str_replace($first_paragraph,$first_paragraph.''.$add_html,$newsletter_body);
        $sn_prefix = 'TRIAL';
      } elseif ($x == 2) {
        // expired member upsell
       $add_html ='';
       /* $add_html = '
        <div style="background:#feeaa4; padding:20px">
          <strong>Renew Your StockNews.com Premium Account!</strong><br><br>
          Unfortunately your StockNews.com Premium account has expired. Upgrade now to re-enable access to our exclusive daily newsletters, POWR Ratings, Best Stocks List, and much more!<br><br>
          <a href="https://stocknews.com/members/signup" class="button">Upgrade To Premium Now!</a>
        </div>
        '; */
        $this_html = str_replace($first_paragraph,$first_paragraph.''.$add_html,$newsletter_body);
        $sn_prefix = 'EXPIRED';
      } elseif ($x == 3) {
        // free member upsell
        $add_html = '';
       /* $add_html = '
        <div style="background:#feeaa4; padding:20px">
          <strong>Try StockNews.com Premium!</strong><br><br>
          Get access to our exclusive daily newsletters, POWR Ratings, Best Stocks List, and much more!  We\'ve applied POWR Ratings&trade; to several thousand stocks and ETFs, and the results speak for themselves: 95% of our A-rated stocks are up from the time we recommended them, with an average return of +16.45%!<br><br>
          <a href="https://stocknews.com/members/signup" class="button">Join Now!</a>
        </div>
        '; */
        $this_html = str_replace($first_paragraph,$first_paragraph.''.$add_html,$newsletter_body);
        $sn_prefix = 'FREE';
      } // end if
         
		$newsletter_name = $_SESSION['newsletter_name'];
	  
      if (!empty($_POST['newsletter_body'])) {
    file_put_contents(plugin_dir_path(__FILE__). $newsletter_name. '.html', $newsletter_head.$newsletter_head_title.str_replace("’","'",$this_html).$newsletter_footer);
}else
{file_put_contents(plugin_dir_path(__FILE__). $newsletter_name. '.html', $newsletter_head.str_replace("’","'",$this_html).$newsletter_footer);}
      

      // save our HTML file to disk
            

    } // end for

    // redirect to page to download files
    wp_redirect('/wp-admin/admin.php?page=sn_admin_newsletter_wrapper_generator&saved=1');
    exit;

  } // end if
} // end function
add_action( 'admin_post_newsletter_wrapper', 'sn_admin_newsletter_wrapper' );

function sn_linkify_ticker($sym){ 
  $sym = strtoupper($sym);
  return '(<strong><a href="https://stocknews.com/stock/'.$sym.'/">'.$sym.'</a></strong>)';
}

function sn_get_ticker_links($text) { //text of post passed in  
  preg_match_all("/\[\[([\w-]+)\]\]/", $text, $matches);
  foreach ($matches[1] as $val) {
      $text = preg_replace ("/\[\[$val\]\]/",sn_linkify_ticker($val),$text);  
  }
  return $text;
}

function sn_google_doc_newsletter($text) {
  return nl2br(force_balance_tags(sn_get_ticker_links(str_replace("&nbsp;\r\n\r\n","",str_replace('</span>','',str_replace('<span style="font-weight: 400;">','',$text))))));
}

// GLIQ function to upload a piece of content (usually an HTML email template)
function sn_gliq_upload_content($arr=array()) {
    // array cannot be empty
    if (!empty($arr)) {
        // ensure our requried array elements are specified
        if (($arr["doc_type"] <> "") && ($arr["doc_name"] <> "") && ($arr["doc_content"] <> "")) {
            // url for GLIQ SOAP API WSDL
            $wsdl = 'https://www.globalintellisystems.com/api/gliqconnect.php?wsdl';

            // set our SOAP options
            $options = array(
                    'uri'=>'https://www.globalintellisystems.com/api/gliqconnect.php',
                    'style'=>SOAP_RPC,
                    'use'=>SOAP_ENCODED,
                    'soap_version'=>SOAP_1_1,
                    'cache_wsdl'=>WSDL_CACHE_NONE,
                    'connection_timeout'=>15,
                    'trace'=>true,
                    'encoding'=>'UTF-8',
                    'exceptions'=>true
                );

            // attempt to connect and execute some calls
            $soap = new SoapClient($wsdl, $options);
     
            // $html = "<html><head></head><body>This is a test document.</body></html>";

            // upload the piece of content (in our case, an html file)
            $foo = $soap->__soapCall(
                "UploadContent",array(
                    "UserAuth"=>array("account"=>"stocknews","username"=>"stocknews","password"=>"i8Q4Aj3M3OE33y"),
                    "ContentDoc"=>array(
                        'contentname' => $arr["doc_name"],
                        'content' => array(
                            array(
                                'DocType'=> $arr["doc_type"],
                                'DocData' => base64_encode(base64_encode($arr["doc_content"]))
                            )
                        ),
                        'overwrite' => 1,
                        'maxlinelength' => "",
                        'footer' => "",
                        'template' => ""
                        )
                    )
                );

            return $foo;
        }
    }

}


// GLIQ function to track links on uploaded content
function gliq_track_links($arr=array()) {
               // url for GLIQ SOAP API WSDL
            $wsdl = 'https://www.globalintellisystems.com/api/gliqconnect.php?wsdl';

            // set our SOAP options
            $options = array(
                    'uri'=>'https://www.globalintellisystems.com/api/gliqconnect.php',
                    'style'=>SOAP_RPC,
                    'use'=>SOAP_ENCODED,
                    'soap_version'=>SOAP_1_1,
                    'cache_wsdl'=>WSDL_CACHE_NONE,
                    'connection_timeout'=>15,
                    'trace'=>true,
                    'encoding'=>'UTF-8',
                    'exceptions'=>true
                );

            // attempt to connect and execute some calls
            $soap = new SoapClient($wsdl, $options);
     

            // track links
            $foo = $soap->__soapCall(
                "TrackClicks",array(
                    "UserAuth"=>array("account"=>"stocknews","username"=>"stocknews","password"=>"i8Q4Aj3M3OE33y"),
                    "TrackingDoc"=>array(
                        'contentname' => $arr["contentname"],
                        'mode' => 0,
                        'trackingnumber' => "",
                        'trackinglinks' => array()
                        )
                    ));

            return $foo;
}

function sn_gliq_send_mailing($arr=array()) {
    // array cannot be empty
    if (!empty($arr)) {
        // ensure our requried array elements are specified
        if (($arr["subject"] <> "") && ($arr["content_name"] <> "") && ($arr["timetolaunch"] <> "") && ($arr["list_group"] <> "") && ($arr["key_code"] <> "")) {
            // url for GLIQ SOAP API WSDL
            $wsdl = 'https://www.globalintellisystems.com/api/gliqconnect.php?wsdl';

            // set our SOAP options
            $options = array(
                    'uri'=>'https://www.globalintellisystems.com/api/gliqconnect.php',
                    'style'=>SOAP_RPC,
                    'use'=>SOAP_ENCODED,
                    'soap_version'=>SOAP_1_1,
                    'cache_wsdl'=>WSDL_CACHE_NONE,
                    'connection_timeout'=>15,
                    'trace'=>true,
                    'encoding'=>'UTF-8',
                    'exceptions'=>true
                );

            // attempt to connect and execute some calls
            $soap = new SoapClient($wsdl, $options);

            // actually send the mailing using our parameters
            $foo = $soap->__soapCall(
                "StartAMailing",array(
                    "UserAuth"=>array("account"=>"stocknews","username"=>"stocknews","password"=>"i8Q4Aj3M3OE33y"),
                    "MailingDoc"=>array(
                        'contentname' => $arr["content_name"],
                        'footername' => '',
                        'timetolaunch' => $arr["timetolaunch"],
                        'openrate' => 1,
                        'listgroup' => $arr["list_group"], // note this must be an array
                        'suppressiongroup' => '',
                        'suppressionlist' => '01182018171542',
                        'requireconfirm' => 0,
                        'mailingheader' => array(
                            'replyto' => 'contact@stocknews.com',
                            'totext' => '',
                            'toaddress' => '',
                            'fromtext' => 'StockNews.com',
                            'fromaddress' => 'contact@stocknews.com',
                            'bounceaddress' => 'stocknews@return1.gliq.com',
                            'subject' => stripslashes($arr["subject"]),
                            'adminaddress' => 'contact@stocknews.com',
                            'keycode' => $arr["key_code"],
                            'keycode2' => '',
                            'acctcode' => '',
                            'acctcode2' => '',
                            'acctcode3' => '',
                            'jobcode' => '',
                            'bouncefield' => '',
                            'modhtml' => '',
                            'priority' => '',
                            'conditional' => '',
                            'stripcr' => '',
                            'allowdupes' => 'off',
                            'dedicated' => '',
                            'bouncetrack' => ''
                        ),
                        'cleanheader' => 0,
                        'cleancontent' => 0
                    )
                )
            );

            return $foo;

        } // end if all array items set correctly
    } // end if array not empty
} // end function

function sn_remove_smart_quotes($content) {

  $content= str_replace(
  array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
  array("'", "'", '"', '"', '-', '--', '...'), $content);

  $content= str_replace(
  array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
  array("'", "'", '"', '"', '-', '--', '...'), $content);

  return $content;

}

<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("../../../wp-load.php");

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

/***************************************************************************************************/

$ticker = ($_GET["q"] <> "")?($_GET["q"]):('MSFT');
$social = array();

echo "<h1>StockTwits/Twitter Blended Filtered Search Results</h1>";

?>
<form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
  <input type="text" name="q" placeholder="Stock Symbol"><input type="submit">
</form>
<?php
$total_messages = 0;

// get stocktwits
$twits = json_decode(file_get_contents("https://api.stocktwits.com/api/2/streams/symbol/$ticker.json"));
$messages = $twits->messages;
$total_messages += count($twits->messages);
foreach ($messages as $message) {
  if (!$message->reshare_message) {
    $cleaned = block_crappy_tweets($message->body);
    if ((((substr_count($cleaned, '$') < 3) && (substr_count($cleaned, 'https://') < 2)) || ($message->likes > 2)) && (substr_count($cleaned, '#') < 3) && (strlen(str_replace('$'.$query,'',$cleaned)) > 20) && (!contains_banned_words($cleaned)) && (stripos($message->user->user_name, "penny") === false) && (!is_message_too_similar($message->body,$social))) {
      $i++;
      $social[] = array(
        "username" => $message->user->username,
        "real_name" => $message->user->name,
        "avatar" => $message->user->avatar_url,
        "message" => $message->body,
        "timestamp" => strtotime($message->created_at),
        "service" => "StockTwits"
      );
    }
  }
}

// get tweets
require "../../../wp-content/themes/strappress-child/includes/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
// consumer key, consumer secret, access token, token secret
$connection = new TwitterOAuth('pdGSlCFnnGQikH3S04FoXSecu', 'h0Qk8K2zzGF57SzRopJ83ABLaLwYsjbiZ1DHQKlVtiXkmIutt4', '9626012-JMoAE2LxQKDToy5XY3LBwvSsCiHhIKz9cZK8zzcIho', 'InhAWHBk3h1vDKG61mbLjylCBaSImruyvEKeIuVgkZwCq');
$content = $connection->get("search/tweets", array('q' => '$'.$ticker, 'result_type' => 'mixed', 'count' => 100));
$statuses = $content->statuses;
$total_messages += count($content->statuses);
foreach ($statuses as $status) {
  $cleaned = block_crappy_tweets($status->text);
  if ((((substr_count($cleaned, '$') < 3) && (substr_count($cleaned, 'https://') < 2)) || ($status->favorite_count > 2)) && (substr_count($cleaned, '#') < 3) && (strlen(str_replace('$'.$query,'',$cleaned)) > 20) && (!contains_banned_words($cleaned)) && (stripos($status->user->screen_name, "penny") === false) && (!is_message_too_similar($status->text,$social))) {
    $i++;
    $social[] = array(
      "username" => $status->user->screen_name,
      "real_name" => $status->user->name,
      "avatar" => $status->user->profile_image_url,
      "message" => $status->text,
      "timestamp" => strtotime($status->created_at),
      "service" => "Twitter"
    );
  }
}

// sort the array, then echo it
usort($social,"sort_messages_by_time");
for ($a=0;!empty($social[$a]);$a++) {
  echo "@".$social[$a]["username"]." ".$social[$a]["message"]."<br>";
  echo time_elapsed_string($social[$a]["timestamp"])." on ".$social[$a]["service"];
  echo "<hr>";
}
echo "<pre>";
print_r($social);
echo "</pre>";

function is_message_too_similar($text,$array) {
  $text = strip_urls($text);
  foreach ($array as $arr) {
    $diff = levenshtein($text,strip_urls($arr["message"]));
    //echo "1: ".strip_urls($arr["message"])."<br>"."2: ".strip_urls($text)."<br>Similarity: ".similar_text(strip_urls($arr["message"]), strip_urls($text))."<br><br>";
    if ($diff < 10) {
      return true;
    }
  }
}

function strip_urls($text) {
  return preg_replace('/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $text);
}

function contains_banned_words($status) {
  $arr = array(
    "penny stock",
    "pennystock",
    "RT : ",
    "record gains",
    "stock alert",
    "subscribe ",
    "Statement of Changes in Beneficial Ownership",
    "fuck",
    "shit",
    "idiot",
    "100%",
    "100+%",
    "!!",
    "ReTw ",
    "retweet"
  );
  foreach ($arr as $a) {
    if (stripos($status, $a) !== false) {
      return true;
    }
  }
  return false;
}

function block_crappy_tweets($status) {
  $matches = array();
  preg_match_all(
      "/@([a-z0-9_]+)/i",
      $status,
      $matches);
  foreach ($matches as $match) {
    $status = str_replace($match, "", $status);
  }
  return $status;
}

function sort_tweets_by_time($a, $b) {
  return strtotime($b->created_at) - strtotime($a->created_at);
}

function sort_messages_by_time($a, $b) {
  return $b["timestamp"] - $a["timestamp"];
}

function time_elapsed_string($ptime) {
  $etime = time() - $ptime;
  if ($etime < 1) {
    return '0 seconds';
  }

  $a = array( 365 * 24 * 60 * 60  =>  'year',
               30 * 24 * 60 * 60  =>  'month',
                    24 * 60 * 60  =>  'day',
                         60 * 60  =>  'hour',
                              60  =>  'minute',
                               1  =>  'second'
              );
  $a_plural = array( 'year'   => 'years',
                     'month'  => 'months',
                     'day'    => 'days',
                     'hour'   => 'hours',
                     'minute' => 'minutes',
                     'second' => 'seconds'
              );

  foreach ($a as $secs => $str) {
    $d = $etime / $secs;
    if ($d >= 1)
    {
      $r = round($d);
      return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
    }
  }
}

echo "<p>".$total_messages." statuses found. Showing only ".count($messages)." (".round((count($messages)/$total_messages *100),2)."%).";

/*

stdClass Object
(
    [statuses] => Array
        (
            [0] => stdClass Object
                (
                    [metadata] => stdClass Object
                        (
                            [result_type] => popular
                            [iso_language_code] => en
                        )

                    [created_at] => Wed Nov 04 16:31:01 +0000 2015
                    [id] => 661943743826759680
                    [id_str] => 661943743826759680
                    [text] => .@McDonalds is reinventing fast food with a new line of gourmet burgers. 👍 or  👎? https://t.co/yP90zvtDOp $MCD https://t.co/ElwEsdn0ZC
                    [source] => Sprinklr
                    [truncated] => 
                    [in_reply_to_status_id] => 
                    [in_reply_to_status_id_str] => 
                    [in_reply_to_user_id] => 
                    [in_reply_to_user_id_str] => 
                    [in_reply_to_screen_name] => 
                    [user] => stdClass Object
                        (
                            [id] => 16184358
                            [id_str] => 16184358
                            [name] => CNNMoney
                            [screen_name] => CNNMoney
                            [location] => 
                            [description] => The money news that matters most to you. Meet our team: http://t.co/krbacc9dFn
                            [url] => http://t.co/6ltU37Tymf
                            [entities] => stdClass Object
                                (
                                    [url] => stdClass Object
                                        (
                                            [urls] => Array
                                                (
                                                    [0] => stdClass Object
                                                        (
                                                            [url] => http://t.co/6ltU37Tymf
                                                            [expanded_url] => http://CNNMoney.com
                                                            [display_url] => CNNMoney.com
                                                            [indices] => Array
                                                                (
                                                                    [0] => 0
                                                                    [1] => 22
                                                                )

                                                        )

                                                )

                                        )

                                    [description] => stdClass Object
                                        (
                                            [urls] => Array
                                                (
                                                    [0] => stdClass Object
                                                        (
                                                            [url] => http://t.co/krbacc9dFn
                                                            [expanded_url] => http://cnnmon.ie/team
                                                            [display_url] => cnnmon.ie/team
                                                            [indices] => Array
                                                                (
                                                                    [0] => 56
                                                                    [1] => 78
                                                                )

                                                        )

                                                )

                                        )

                                )

                            [protected] => 
                            [followers_count] => 1141528
                            [friends_count] => 907
                            [listed_count] => 17674
                            [created_at] => Mon Sep 08 13:44:47 +0000 2008
                            [favourites_count] => 232
                            [utc_offset] => -18000
                            [time_zone] => Eastern Time (US & Canada)
                            [geo_enabled] => 
                            [verified] => 1
                            [statuses_count] => 84509
                            [lang] => en
                            [contributors_enabled] => 
                            [is_translator] => 
                            [is_translation_enabled] => 
                            [profile_background_color] => CCCCCC
                            [profile_background_image_url] => http://pbs.twimg.com/profile_background_images/415352398/cnnmoney-bg-tile.jpg
                            [profile_background_image_url_https] => https://pbs.twimg.com/profile_background_images/415352398/cnnmoney-bg-tile.jpg
                            [profile_background_tile] => 
                            [profile_image_url] => http://pbs.twimg.com/profile_images/562388464209895424/klTcBowc_normal.png
                            [profile_image_url_https] => https://pbs.twimg.com/profile_images/562388464209895424/klTcBowc_normal.png
                            [profile_banner_url] => https://pbs.twimg.com/profile_banners/16184358/1422918817
                            [profile_link_color] => 004571
                            [profile_sidebar_border_color] => FFFFFF
                            [profile_sidebar_fill_color] => F0F0F0
                            [profile_text_color] => 000000
                            [profile_use_background_image] => 
                            [has_extended_profile] => 
                            [default_profile] => 
                            [default_profile_image] => 
                            [following] => 
                            [follow_request_sent] => 
                            [notifications] => 
                        )

                    [geo] => 
                    [coordinates] => 
                    [place] => 
                    [contributors] => 
                    [is_quote_status] => 
                    [retweet_count] => 24
                    [favorite_count] => 22
                    [entities] => stdClass Object
                        (
                            [hashtags] => Array
                                (
                                )

                            [symbols] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [text] => MCD
                                            [indices] => Array
                                                (
                                                    [0] => 106
                                                    [1] => 110
                                                )

                                        )

                                )

                            [user_mentions] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [screen_name] => McDonalds
                                            [name] => McDonald's
                                            [id] => 71026122
                                            [id_str] => 71026122
                                            [indices] => Array
                                                (
                                                    [0] => 1
                                                    [1] => 11
                                                )

                                        )

                                )

                            [urls] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [url] => https://t.co/yP90zvtDOp
                                            [expanded_url] => http://cnnmon.ie/1ksEe6h
                                            [display_url] => cnnmon.ie/1ksEe6h
                                            [indices] => Array
                                                (
                                                    [0] => 82
                                                    [1] => 105
                                                )

                                        )

                                )

                            [media] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [id] => 661943743721938944
                                            [id_str] => 661943743721938944
                                            [indices] => Array
                                                (
                                                    [0] => 111
                                                    [1] => 134
                                                )

                                            [media_url] => http://pbs.twimg.com/media/CS-yU-nWoAAqAf3.jpg
                                            [media_url_https] => https://pbs.twimg.com/media/CS-yU-nWoAAqAf3.jpg
                                            [url] => https://t.co/ElwEsdn0ZC
                                            [display_url] => pic.twitter.com/ElwEsdn0ZC
                                            [expanded_url] => http://twitter.com/CNNMoney/status/661943743826759680/photo/1
                                            [type] => photo
                                            [sizes] => stdClass Object
                                                (
                                                    [large] => stdClass Object
                                                        (
                                                            [w] => 1024
                                                            [h] => 512
                                                            [resize] => fit
                                                        )

                                                    [thumb] => stdClass Object
                                                        (
                                                            [w] => 150
                                                            [h] => 150
                                                            [resize] => crop
                                                        )

                                                    [medium] => stdClass Object
                                                        (
                                                            [w] => 600
                                                            [h] => 300
                                                            [resize] => fit
                                                        )

                                                    [small] => stdClass Object
                                                        (
                                                            [w] => 340
                                                            [h] => 170
                                                            [resize] => fit
                                                        )

                                                )

                                        )

                                )

                        )

                    [favorited] => 
                    [retweeted] => 
                    [possibly_sensitive] => 
                    [lang] => en
                )

            [1] => stdClass Object
                (
                    [metadata] => stdClass Object
                        (
                            [result_type] => popular
                            [iso_language_code] => en
                        )

                    [created_at] => Sun Nov 01 05:00:10 +0000 2015
                    [id] => 660682720515870720
                    [id_str] => 660682720515870720
                    [text] => McDonald's is testing out a new kind of fry: https://t.co/ShkE0ab92x  $MCD https://t.co/zgfGJR6pV9
                    [source] => Hootsuite
                    [truncated] => 
                    [in_reply_to_status_id] => 
                    [in_reply_to_status_id_str] => 
                    [in_reply_to_user_id] => 
                    [in_reply_to_user_id_str] => 
                    [in_reply_to_screen_name] => 
                    [user] => stdClass Object
                        (
                            [id] => 20402945
                            [id_str] => 20402945
                            [name] => CNBC
                            [screen_name] => CNBC
                            [location] => 
                            [description] => First in Business Worldwide
                            [url] => http://t.co/YKho1OnIvS
                            [entities] => stdClass Object
                                (
                                    [url] => stdClass Object
                                        (
                                            [urls] => Array
                                                (
                                                    [0] => stdClass Object
                                                        (
                                                            [url] => http://t.co/YKho1OnIvS
                                                            [expanded_url] => http://cnbc.com
                                                            [display_url] => cnbc.com
                                                            [indices] => Array
                                                                (
                                                                    [0] => 0
                                                                    [1] => 22
                                                                )

                                                        )

                                                )

                                        )

                                    [description] => stdClass Object
                                        (
                                            [urls] => Array
                                                (
                                                )

                                        )

                                )

                            [protected] => 
                            [followers_count] => 2165821
                            [friends_count] => 911
                            [listed_count] => 20834
                            [created_at] => Mon Feb 09 00:03:41 +0000 2009
                            [favourites_count] => 2374
                            [utc_offset] => -18000
                            [time_zone] => Eastern Time (US & Canada)
                            [geo_enabled] => 1
                            [verified] => 1
                            [statuses_count] => 78602
                            [lang] => en
                            [contributors_enabled] => 
                            [is_translator] => 
                            [is_translation_enabled] => 
                            [profile_background_color] => 0D181F
                            [profile_background_image_url] => http://pbs.twimg.com/profile_background_images/583652722282377216/qZPzBhcz.png
                            [profile_background_image_url_https] => https://pbs.twimg.com/profile_background_images/583652722282377216/qZPzBhcz.png
                            [profile_background_tile] => 
                            [profile_image_url] => http://pbs.twimg.com/profile_images/659562683931324416/g82W3EqJ_normal.jpg
                            [profile_image_url_https] => https://pbs.twimg.com/profile_images/659562683931324416/g82W3EqJ_normal.jpg
                            [profile_banner_url] => https://pbs.twimg.com/profile_banners/20402945/1446089720
                            [profile_link_color] => 2D648A
                            [profile_sidebar_border_color] => FFFFFF
                            [profile_sidebar_fill_color] => EAEBEA
                            [profile_text_color] => 333333
                            [profile_use_background_image] => 1
                            [has_extended_profile] => 
                            [default_profile] => 
                            [default_profile_image] => 
                            [following] => 1
                            [follow_request_sent] => 
                            [notifications] => 
                        )

                    [geo] => 
                    [coordinates] => 
                    [place] => 
                    [contributors] => 
                    [is_quote_status] => 
                    [retweet_count] => 23
                    [favorite_count] => 27
                    [entities] => stdClass Object
                        (
                            [hashtags] => Array
                                (
                                )

                            [symbols] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [text] => MCD
                                            [indices] => Array
                                                (
                                                    [0] => 70
                                                    [1] => 74
                                                )

                                        )

                                )

                            [user_mentions] => Array
                                (
                                )

                            [urls] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [url] => https://t.co/ShkE0ab92x
                                            [expanded_url] => http://cnb.cx/1Wa3cTS
                                            [display_url] => cnb.cx/1Wa3cTS
                                            [indices] => Array
                                                (
                                                    [0] => 45
                                                    [1] => 68
                                                )

                                        )

                                )

                            [media] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [id] => 658863307969855488
                                            [id_str] => 658863307969855488
                                            [indices] => Array
                                                (
                                                    [0] => 75
                                                    [1] => 98
                                                )

                                            [media_url] => http://pbs.twimg.com/media/CSTAsBMVAAAFxiH.jpg
                                            [media_url_https] => https://pbs.twimg.com/media/CSTAsBMVAAAFxiH.jpg
                                            [url] => https://t.co/zgfGJR6pV9
                                            [display_url] => pic.twitter.com/zgfGJR6pV9
                                            [expanded_url] => http://twitter.com/CNBC/status/658863308313903104/photo/1
                                            [type] => photo
                                            [sizes] => stdClass Object
                                                (
                                                    [medium] => stdClass Object
                                                        (
                                                            [w] => 600
                                                            [h] => 314
                                                            [resize] => fit
                                                        )

                                                    [thumb] => stdClass Object
                                                        (
                                                            [w] => 150
                                                            [h] => 150
                                                            [resize] => crop
                                                        )

                                                    [small] => stdClass Object
                                                        (
                                                            [w] => 340
                                                            [h] => 177
                                                            [resize] => fit
                                                        )

                                                    [large] => stdClass Object
                                                        (
                                                            [w] => 600
                                                            [h] => 314
                                                            [resize] => fit
                                                        )

                                                )

                                            [source_status_id] => 658863308313903104
                                            [source_status_id_str] => 658863308313903104
                                            [source_user_id] => 20402945
                                            [source_user_id_str] => 20402945
                                        )

                                )

                        )

                    [favorited] => 
                    [retweeted] => 
                    [possibly_sensitive] => 
                    [lang] => en
                )

        )

    [search_metadata] => stdClass Object
        (
            [completed_in] => 0.026
            [max_id] => 0
            [max_id_str] => 0
            [query] => %24MCD
            [count] => 15
            [since_id] => 0
            [since_id_str] => 0
        )

)

require_once("../../../wp-content/themes/strappress-child/includes/twitter-api-php/TwitterAPIExchange.php");

$settings = array(
    'oauth_access_token' => "9626012-JMoAE2LxQKDToy5XY3LBwvSsCiHhIKz9cZK8zzcIho",
    'oauth_access_token_secret' => "InhAWHBk3h1vDKG61mbLjylCBaSImruyvEKeIuVgkZwCq",
    'consumer_key' => "pdGSlCFnnGQikH3S04FoXSecu",
    'consumer_secret' => "h0Qk8K2zzGF57SzRopJ83ABLaLwYsjbiZ1DHQKlVtiXkmIutt4"
);

//$url = 'https://api.twitter.com/1.1/search/tweets.json';
$url = 'https://api.twitter.com/1.1/friends/list.json';
$requestMethod = 'GET';

$getfield = '?screen_name=paulrubillo&count=25';

$twitter = new TwitterAPIExchange($settings);
$response =  $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
$results = json_decode($response);

$arr = array();
foreach ($results->users as $user) {
  $arr[] = $user->screen_name;
}

$join = "from:".implode("+OR+from:", $arr);

// ---------

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$requestMethod = 'GET';

$getfield = '?q=$SPY+'.$join;

$twitter = new TwitterAPIExchange($settings);
$response =  $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
$results = json_decode($response);

echo "<pre>";
print_r($results);
echo "</pre>";

*/


/***************************************************************************************************/

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo '<p>Page generated in '.$total_time.' seconds.</p>';

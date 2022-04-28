<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("../../../wp-load.php");

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

/***************************************************************************************************/

// initialize db
global $wpdb;

// no time limit
set_time_limit(0);

echo '<table border=1><thead><tr><th>Symbol</th><th>Start Date</th><th>Start Price</th><th>Price Now</th><th>% Return</th></tr></thead><tbody>';
$q = "select symbol,last_price from sn_stocks where powr_number >= 4 order by symbol asc";
$r = $wpdb->get_results($q);
$count = 0;
$total = 0;
$best = 0;
$worst = 0;
$winners = 0;
$losers = 0;
$unchanged = 0;
foreach ($r as $row) {
    $q2 = "select h.day_close, h.date from sn_historical h where symbol='".$row->symbol."' and h.date = (select max(date) from sn_historical where symbol='".$row->symbol."' and powr_number < 4)";
    // where h.symbol='".$row->symbol."' and h.powr_number < 4
    $foo = $wpdb->get_row($q2);
    $ppc = sn_pretty_pct_change($row->last_price,$foo->day_close);
    //$ppc = price_pct_change($row->last_price,$foo->day_close);
    $pc = str_replace('%','',strip_tags($ppc));
    if (($pc <> 'N/A') && ($foo->day_close > 0) && ($row->last_price > 0)) {
        $this_days = sn_days_between_dates($foo->date,'2016-07-07');
        $days += $this_days;
        $total += $pc;
        $count++;
        if ($pc > $best) {
            $best = $pc;
        }
        if ($pc < $worst) {
            $worst = $pc;
        }
        echo 
        '<tr><td>'.$row->symbol.'</td>
         <td>'.$foo->date.'</td>
         <td>'.$foo->day_close.'</td>
         <td>'.$row->last_price.'</td>
         <td>'.$ppc.'</td></tr>';
         if ($pc > 0) {
            $winners++;
         }
         if ($pc < 0) {
            $losers++;
         }
         if ($pc == 0) {
            $unchanged++;
         }
    }
}
echo '</tbody></table>';

$avg = sn_nice_price($total/$count);
$avg_days = round($days/$count);

echo '<p></p>';

echo '<table border=1><thead></thead><tbody>';

echo "<tr><td><strong>Total A-rated stocks</strong></td><td>$count</td></tr>"; //$count
echo "<tr><td><strong>Avg. % return</strong></td><td>$avg%</td></tr>";
echo "<tr><td><strong>Avg. # days since upgraded to A</strong></td><td>$avg_days</td></tr>";
echo "<tr><td><strong>Best return</strong></td><td>$best%</td></tr>";
echo "<tr><td><strong>Worst return</strong></td><td>$worst%</td></tr>";
echo "<tr><td><strong>Num winners</strong></td><td>$winners (".round(($winners/$count) *100)."%)</td></tr>";
echo "<tr><td><strong>Num losers</strong></td><td>$losers (".round(($losers/$count) *100)."%)</td></tr>";

echo '</tbody></table>';

/*
$foo = generate_news_importance();
echo '<pre>';
print_r($foo);
*/

/*
// update prices/cats
$prices = cron_update_prices();
$cats = cron_update_categories();
echo "<pre>";
print_r($prices);
print_r($cats);
echo "</pre>";
*/

/*
$arr = array('UBIC',
'QADB',
'FNLC',
'CATC',
'QNBC',
'BWFG',
'REN',
'ESALY',
'MESO',
'KMDA',
'DHIL',
'SRSC',
'LVNTA',
'DHXM',
'ITCB',
'CRHM',
'MDM',
'JMU',
'LITB',
'CRI',
'OXM',
'KCLI',
'OSGB',
'FORTY',
'BMCH',
'MFIN');
foreach($arr as $a) {
    echo $a." ".sn_generate_history($a)."<br>";
}
*/

/*
$r = $wpdb->get_results("select symbol from sn_stocks where category_id is not null order by symbol asc limit 10");
foreach ($r as $row) {
    echo $row->symbol.": ".sn_generate_history($row->symbol,'2016-06-06')." days updated.<br>";
}
*/

/*
$query = "select * from sn_stocks where profile like 'The investment objective of the%seeks to provide%'";
$results = $wpdb->get_results($query);
foreach ($results as $row) {
    $fixed = str_replace("seeks to provide","is to provide",$row->profile);
    $q = "update sn_stocks set profile='$fixed' where id='".$row->id."' limit 1";
    $wpdb->query($q);
    //echo "$q<br><br>";
}
*/
/*
$cats_etfs = $wpdb->get_results("select id from sn_categories where type=1 order by id asc");
foreach ($cats_etfs as $key=>$cat) {
    // grab all symbols for this category
    $stocks_result = $wpdb->get_results("select symbol from sn_stocks where category_id='".$cat->id."' order by symbol asc");

    // convert to array
    $stocks = array();
    foreach ($stocks_result as $sr) {
      $stocks[] = $sr->symbol;
    }

    // implode symbols
    $symbols = "'".implode("','",$stocks)."'";

    // add symbols to our cats object
    $cats_etfs[$key]->symbols = $symbols;
}
echo "<pre>";
print_r($cats_etfs);
*/
/*
$result = $wpdb->get_results("select id,((trade_number + bh_number + category_number + peer_number)/4) as powr from sn_historical order by id asc limit 100");
foreach ($result as $row) {
  $foo = sn_generate_powr_history($row->id."|".$row->powr);
  echo "$foo - ok?<br>";
}
*/
/*
$cats = $wpdb->get_results("select id from sn_categories order by id asc");
//print_r($cats);
$result = $wpdb->get_results("select distinct date from sn_historical order by date asc limit 1");
foreach ($result as $row) {
    echo $row->date."<br>";
    //$foo = sn_generate_cats_history($row->id,$cats);
    //echo "$foo cats updated.";
}
*/

/*
$prices = cron_update_prices();
$cats = cron_update_categories();
echo "<pre>";
print_r($prices);
print_r($cats);
echo "</pre>";
*/

/*

// define symbol
$sym = $_GET["symbol"];

// build array of dates we have for this symbol
$q = "select date from sn_historical where symbol='$sym'";
$res = $wpdb->get_results($q);
foreach ($res as $row) {
    $arr[] = $row->date;
}

// find missing dates
$current_date = strtotime("2015-03-20");
$end_date = strtotime("-1 day");
$not_found = array();
while ($current_date < $end_date) {
    if (sn_is_market_day(date("Y-m-d",$current_date))) {
        if ((array_search(date("Y-m-d",$current_date), $arr)) === FALSE) {
            $not_found[] = date("Y-m-d",$current_date);
        }
    }
    $current_date = strtotime("+1 day",$current_date);
}

// check for missing dates
if (!empty($not_found)) {
    echo "<p>Uh oh! Dates not found for $sym:</p>";
    foreach($not_found as $nf) {
        echo $nf."<br>";
    }
    // ensure barchart has these dates
    $history = get_historical_data($sym,date("Y-m-d",strtotime("-2 years",strtotime($start_date))),0,'',0,$end_date,0);
    $history = json_decode(json_encode($history->results),TRUE);
    echo '<p>'.count($history)." days of history found.</p>";
    echo '<table><thead><tr><th>Date</th><th>Price</th><th>Vol</th><th>Hi</th></tr></thead><tbody>';
    foreach ($history as $his) {
        echo '<tr><td>'.$his["tradingDay"].'</td><td>'.$his["close"].'</td><td>'.$his["volume"].'</td><td>'.$his["high"].'</td></tr>';
        //print_r($his);
    }
    echo '</tbody></table>';
} else {
    echo "<p>All good! No missing dates found for $sym.</p>";
}

*/

/*
$prices = cron_update_prices();
$cats = cron_update_categories();
*/

/*
$foo = "select id, name from sn_categories";
$res = $wpdb->get_results($foo);
echo "<table>";
foreach ($res as $row) {
    $i = 0;
    $a = $wpdb->get_var("select count(*) from sn_stocks where category_id='".$row->id."'");
    $goo = "select count(*) as cnt from sn_snippets where symbol in (select symbol from sn_stocks where category_id='".$row->id."')";
    $gor = $wpdb->get_results($goo);
    foreach ($gor as $ror) {
        $i += $ror->cnt;
        $a++;
    }
    echo "<tr><td>".$row->name."</td><td>".$i."</td><td>".$a."</td></tr>";
}
echo "</table>";
*/

//$foo = get_all_current_ratings();
//$ytd = sn_ytd_data(sn_all_symbols());

//echo "<pre>";
//print_r($foo);
//print_r($ytd);
//print_r($prices);
//print_r($cats);
//echo "</pre>";

/*

// what symbol are we looking up?
if (isset($_GET["symbol"])) {
    $get_symbol = sanitize_text_field($_GET["symbol"]);
}

// determine rolling period length (usually 5 or 10 days)
if (isset($_GET["rolling_days"])) {
    $rolling_days = intval($_GET["rolling_days"]);
} else {
    $rolling_days = 5;
}

// search form (for debugging only)
echo '<form action="" method="get">';
echo '<input name="symbol" placeholder="Symbol" value="'.$symbol.'"> 
        <input name="rolling_days" placeholder="# Rolling Days" value="'.$rolling_days.'"> 
        <input type="submit" value="Get Trade Grade History">';
echo '</form>';

echo "<pre>";

*/

/*

SPY, KSS, DPZ, AMZN, TWTR, RH

@TODO

- GRADING SYSTEM
- A +35 and up (extreme bullish performance + sentiment)
- B +16 to +34 (bullish)
- C -34 to +15 (neutral)
- D -21 to -14 (bearish)
- F -15 or worse (extreme bearish performance + sentiment)

- how to handle new stocks without year of history?
-- minimum 200 trading days
-- otherwise N/A
-- so if historical query, then if i = < 200 -> N/A
--- how to handle N/As in db?

- determine if weekly or rolling 5-day?
-- pretty sure rolling 5-day YES KEEP IT
-- this means we calculate on the fly based on 1 db query (select last 5 scoring days)
--- OR we just cache in db sn_stocks table when cron_job is run, save current_st_score and current_lt_score
---- this makes more sense, save resources

- determine if SMA or EMA?
-- SMA seems ok, but ask paulie - KEEP IT

- adjust for splits/dividends
-- LOOK INTO IF BC OFFERS ADJUSTED CLOSING PRICES
-- maybe barchart has this built in?
-- NEW ADMIN SCREEN: ADJUST TRADER SCORE
--- INPUT SYMBOL + REASON FOR RESTARTING GRADE + POSSIBLY WIPING OUT HISTORICAL TRADER GRADES
--- STOCK WILL SHOW N/A FOR TRADER'S GRADE UNTIL THEY GET 5 DAYS OF TRADING UNDER THEIR BELT AFTER THIS DATE

- wrap in function

- db table to house

- cron job
-- RUN AT 6:30PM EASTERN EACH DAY
-- run each day, grab closing prices + snippets, recording scores
--- probably best to schedule late at night (10pm? ask paul when the latest he adds snippets is), when you know no more snippets are coming
-- probably just integrate into existing cron

- optimization
-- fastest way to execute queries
-- table indexes?
- integrate front-end on all pages

*/

/*
// define symbols
if ($get_symbol) {
    $symbols = array($symbol);
}
*/



// $prices = cron_update_prices();
// $cats = cron_update_categories();

/*
global $wpdb;
$prices = cron_update_prices();
$cats = cron_update_categories();

echo "<pre>";
print_r($prices);
print_r($cats);
echo "</pre>";
*/

/*


//echo '<meta http-equiv="refresh" content="60" >';

global $wpdb;

// how many snippets need updating?
$need_updates = $wpdb->get_var("select count(*) from sn_snippets where price_when_added is null and date < '2016-01-16' and symbol in (select symbol from sn_stocks)");
echo "<p>$need_updates snippets need updating.</p>";

if ($need_updates > 0) {

    // and how many unique symbols?
    $unique_symbols = $wpdb->get_var("select count(distinct symbol) from sn_snippets where price_when_added is null and date < '2016-01-16' and symbol in (select symbol from sn_stocks)");
    echo "<p>That means $unique_symbols queries to barchart.</p>";

    // limit our number of queries to bc
    $query_limit = 100;
    echo "<p>This page will query barchart $query_limit times, <strong>".round($unique_symbols/$query_limit)." more refreshes needed</strong>.</p>";

    // find oldest snippet, so we know what start date to tell bc to grab historical data from
    $start_date = prev_trading_day(strtotime($wpdb->get_var("select min(date) from sn_snippets limit 1")));

    // grab our group of symbols that we will get historical data for
    $outer = $wpdb->get_results("select distinct symbol from sn_snippets where price_when_added is null and date < '2016-01-16' and symbol in (select symbol from sn_stocks) limit $query_limit");
    echo "<pre>";
    foreach ($outer as $out) {
        $data = array();
        // query barchart for historical pricing data
        $bc = get_historical_data(str_replace("-",".",$out->symbol),$start_date);
        if ($bc->status->code == 200) {
            // re-map bc data as an array with dates as keys
            foreach ($bc->results as $key=>$res) {
                $data[$res->tradingDay] = $res;
                if ($key > 0) {
                    $data[$res->tradingDay]->prev_close = $bc->results[($key-1)]->close;
                }
            }
            // select all snippets for this symbol that need updating
            $inner = $wpdb->get_results("select id,symbol,date,time from sn_snippets where symbol='".$out->symbol."' and price_when_added is null");
            //echo "<p><strong>Attempting to update ".count($inner)." snippets for ".$out->symbol.".</strong></p>";
            $i = 0;
            foreach ($inner as $in) {
                if ((sn_is_market_day($in->date)) && (date('Gi',strtotime($in->date." ".$in->time)) < 1630)) {
                    // this snippet was posted on a market day, prior to the market close, so we use data from that date
                    $data_date = $in->date;
                } else {
                    // this snippet was posted on a non-market day OR after the market close, so we use data from the next trading day
                    $data_date = next_trading_day(strtotime($in->date));
                }
                if ($data[$data_date]) {
                    $query = "update sn_snippets set 
                        price_when_added = '".$data[$data_date]->prev_close."', 
                        day_open = '".$data[$data_date]->open."', 
                        day_close = '".$data[$data_date]->close."', 
                        day_high = '".$data[$data_date]->high."',
                        day_low = '".$data[$data_date]->low."', 
                        day_volume = '".$data[$data_date]->volume."', 
                        prev_close = '".$data[$data_date]->prev_close."' 
                        where id='".$in->id."' limit 1
                    ";
                    //echo "<p>$query</p>";
                    $foo = $wpdb->query($query);
                    $i++;
                } else {
                    echo "Error retrieving closing price for ".$out->symbol." on $data_date.<br>";
                }
            }
            //echo "<h2>$i ".$out->symbol." snippets updated successfully.</h2>";
        } else {
            echo "<h1>Error retrieving historical data for ".$out->symbol."</h1>";
        }
    }
    echo "</pre><hr>";

}

/*

use Abraham\TwitterOAuth\TwitterOAuth;
require "../../themes/strappress-child/includes/twitteroauth/autoload.php";
// consumer key, consumer secret, access token, token secret
$connection = new TwitterOAuth('pdGSlCFnnGQikH3S04FoXSecu', 'h0Qk8K2zzGF57SzRopJ83ABLaLwYsjbiZ1DHQKlVtiXkmIutt4', '9626012-JMoAE2LxQKDToy5XY3LBwvSsCiHhIKz9cZK8zzcIho', 'InhAWHBk3h1vDKG61mbLjylCBaSImruyvEKeIuVgkZwCq');

$start_date = $_GET["start"];
$end_date = $_GET["end"];

foreach ($results as $row) {

    // get tweets
    $ticker = $row->symbol;
    $content = $connection->get("search/tweets", array('q' => '$'.$ticker, 'result_type' => 'recent', 'count' => 100, 'since' => $start_date, 'until' => $end_date));
    echo "<p>$ticker tweets found: ".count($content->statuses)."</p>";
    if (count($content->statuses) == 0) {
        echo "<pre>";
        print_r($content);
        echo "</pre>";
    }

}

*/

/*
echo "<html><body><div style='word-wrap: break-word;'><pre>";

echo '<div style="width:400px"><div id="home-chart-container"></div></div>';

include("../../themes/strappress-child/includes/home-charts-js.php");
*/

/*
global $wpdb;
$result = $wpdb->get_results("SELECT id, slug, COUNT(*) c FROM sn_snippets GROUP BY slug HAVING c > 1");
foreach ($result as $row) {
    
    $i=0;

    // select all rows with this same slug
    $same = $wpdb->get_results("select id,date from sn_snippets where slug='".$row->slug."'");

    // loop through set 
    foreach ($same as $same_rows) {
        $i++;
        if ($i > 1) {
            // update slug with -i
            $query = "update sn_snippets set slug='".$row->slug."-".$i."' where id='".$same_rows->id."' limit 1";
            echo $query."<br>";
            //$foo = $wpdb->query($query);
        }
    }

}
*/

/*
$info = cron_update_prices();
print_r($info);
$info = cron_update_categories();
print_r($info);
*/

echo "</pre><div></body></html>";

/*
$query = ($_GET["q"] <> "")?($_GET["q"]):('MSFT');

echo "<h1>Twitter/ST API Test</h1>";

?>
<form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
  <input type="text" name="q" placeholder="Stock Symbol"><input type="submit">
</form>
<?php

require "../../../wp-content/themes/strappress-child/includes/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

// consumer key, consumer secret, access token, token secret
$connection = new TwitterOAuth('pdGSlCFnnGQikH3S04FoXSecu', 'h0Qk8K2zzGF57SzRopJ83ABLaLwYsjbiZ1DHQKlVtiXkmIutt4', '9626012-JMoAE2LxQKDToy5XY3LBwvSsCiHhIKz9cZK8zzcIho', 'InhAWHBk3h1vDKG61mbLjylCBaSImruyvEKeIuVgkZwCq');
$content = $connection->get("search/tweets", array('q' => '$'.$query, 'result_type' => 'mixed', 'count' => 100));

$statuses = $content->statuses;

usort($statuses,"sort_tweets_by_time");
$i = 0;

foreach ($statuses as $status) {
  $cleaned = block_crappy_tweets($status->text);
  if ((((substr_count($cleaned, '$') < 3) && (substr_count($cleaned, 'https://') < 2)) || ($status->favorite_count > 2)) && (substr_count($cleaned, '#') < 3) && (strlen(str_replace('$'.$query,'',$cleaned)) > 20) && (!contains_banned_words($cleaned)) && (stripos($status->user->screen_name, "penny") === false)) {
    $i++;
    echo "@".$status->user->screen_name." ".$status->text."<br>";
    echo time_elapsed_string(strtotime($status->created_at));
    echo "<hr>";
  }
}

function contains_banned_words($status) {
  $arr = array(
     "penny stock",
     "RT : ",
     "record gains",
     "stock alert",
     "subscribe ",
     "Statement of Changes in Beneficial Ownership"
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

function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1)
    {
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

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

echo "<p>".count($statuses)." statuses found. Showing only $i (".round(($i/count($statuses) *100),2)."%).";



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

<?php
/* 
  Stock data functions
*/
function closing_data($sym,$date,$time="00:00:00") {
  // Returns array of closing stock data for specific stock on a specific date

  if (substr($time, 0, 2) < 16) {
    // Snippet event occurred prior to 4pm
    $prev_trading_day = prev_trading_day(strtotime($date));
  } else {
    $prev_trading_day = $date;
    $date = str_replace("-","",next_trading_day(strtotime($date)));
  }

  $url = "http://ondemand.websol.barchart.com/getHistory.json?apikey=45b6f4c4f75dd07b95be1ce310b9dc98&symbol=".$sym."&type=daily&startDate=".$prev_trading_day."000000&endDate=".$date."000000";
  $data = file_get_contents($url,"r");
  //$data = str_replace( 'BarchartAPIcallback(', '', $data ); // remove the js callback
  //$data = substr( $data, 0, strlen( $data ) - 2 ); //strip out last paren and semicolon
  $data = json_decode($data); //convert to php object

  $arr = array(
      "symbol" => $data->results[1]->symbol,
      "open" => $data->results[1]->open,
      "volume" => $data->results[1]->volume,
      "day_low" => $data->results[1]->low,
      "day_high" => $data->results[1]->high,
      "close" => $data->results[1]->close,
      "prev_close" => $data->results[0]->close,
      "avg_vol" => get_avg_vol($sym),
      "date" => $data->results[1]->tradingDay
    );

  return $arr;

}
function get_avg_vol($sym) {
  $url = "http://ondemand.websol.barchart.com/getQuote.jsonp?apikey=45b6f4c4f75dd07b95be1ce310b9dc98&symbols=$sym&fields=avgVolume&mode=I";
  $data = file_get_contents($url,"r");
  $data = str_replace( 'BarchartAPIcallback(', '', $data ); // remove the js callback
  $data = substr( $data, 0, strlen( $data ) - 2 ); //strip out last paren and semicolon
  $data = json_decode($data); //convert to php object
  
  return $data->results[0]->avgVolume;
}
function get_all_current_data($syms) {
  $url = "http://ondemand.websol.barchart.com/getQuote.jsonp?apikey=45b6f4c4f75dd07b95be1ce310b9dc98&symbols=$syms&fields=previousClose,volume,previousVolume,fiftyTwoWkHigh,fiftyTwoWkHighDate,fiftyTwoWkLow,fiftyTwoWkLowDate,avgVolume,dividendRateAnnual,dividendYieldAnnual,exDividendDate,twelveMnthPct,twelveMnthPctDate,averageWeeklyVolume,averageMonthlyVolume,averageMonthlyVolume,averageQuarterlyVolume,sharesOutstanding";
    set_error_handler(
      create_function(
          '$severity, $message, $file, $line',
          'throw new ErrorException($message, $severity, $severity, $file, $line);'
      )
  );

  try {
      $data = file_get_contents($url,"r");
  }
  catch (Exception $e) {
      echo $e->getMessage();
  }
  restore_error_handler();
 
  $data = str_replace( 'BarchartAPIcallback(', '', $data ); // remove the js callback
  $data = substr( $data, 0, strlen( $data ) - 2 ); //strip out last paren and semicolon
  $data = json_decode($data); //convert to php object

  return $data;
}
function get_all_current_ratings($offset=false) {
  // returns array of all symbols with curret trade/b&h ratings and MAs like array([AAPL]=>array([trade-grade]=>A,[bh_grade]=>A))
  global $wpdb;
  $q1 = "select max(date) from sn_historical";
  $current_date = $wpdb->get_var($q1);
  if ($offset == "1week") {
    $current_date = prev_trading_day(prev_trading_day(prev_trading_day(prev_trading_day(prev_trading_day(strtotime($current_date),true),true),true),true));
  } elseif ($offset == "ytd") {
    $current_date = prev_trading_day(strtotime(date("Y")."-01-01"));
  }
  $q2 = "select * from sn_historical where date = '$current_date' order by symbol asc";
  $result = $wpdb->get_results($q2);
  $arr = array();
  foreach ($result as $row) {
    $arr[$row->symbol] = array(
      "trade_score" => $row->rolling_trade_score,
      "trade_grade" => sn_trade_grade($row->rolling_trade_score),
      "bh_grade" => $row->buy_and_hold_score,
      "ma_10_day" => $row->moving_avg_10_day,
      "ma_20_day" => $row->moving_avg_20_day,
      "ma_50_day" => $row->moving_avg_50_day,
      "ma_100_day" => $row->moving_avg_100_day,
      "ma_200_day" => $row->moving_avg_200_day,
      "trade_number" => $row->trade_number,
      "bh_number" => $row->bh_number,
      "category_number" => $row->category_number,
      "peer_number" => $row->peer_number,
      "powr_number" => $row->powr_number,
      "blended_number" => $row->blended_number
    );
  }
  return $arr;
}
function get_all_current_ytd() {
  // returns array of all symbols with current ytd prices
  global $wpdb;
  $q = "select symbol,last_price,ytd_price from sn_stocks where last_price <> 0 and last_price is not null and ytd_price <> 0 and ytd_price is not null";
  $result = $wpdb->get_results($q);
  $arr = array();
  foreach ($result as $row) {
    $arr[$row->symbol] = array(
      "ytd_price" => $row->ytd_price
    );
  }
  return $arr;
}

/* 
  On-page Alerts
*/
function sn_alert_success($message="Success.") {
  echo '<div id="message" class="updated notice notice-success is-dismissible below-h2">
            <p>'.$message.'</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
          </div>';
}
function sn_alert_error($message="Error.") {
  echo '<div id="message" class="notice notice-error is-dismissible below-h2">
            <p>'.$message.'</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
          </div>';
}
function sn_alert_warning($message="Warning.") {
  echo '<div id="message" class="notice notice-warning is-dismissible below-h2">
            <p>'.$message.'</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
          </div>';
}

/*
  Misc. functions
*/
function sn_is_future($date,$time) {
  // If date is today, and time > 16:00, return true
  if ((date('Y-m-d') == $date) && (substr($time, 0, 2) >= 16)) {
    return true;
  } else {
    return false;
  }
}
function sn_show_category_list($selected_id="") {
  // Displays an html SELECT list of stock/etf categories
  global $wpdb;
  $stocks=0;
  $etfs=0;
  $result = $wpdb->get_results("SELECT * FROM sn_categories ORDER BY type ASC, name ASC");
  echo '<select name="category_id">';
  echo '<option disabled="disabled" selected="selected">Select a Category</option>';
  foreach ($result as $row) {
    if ($row->type == 0) { 
      $stocks++;
    } else { 
      $etfs++;
    }
    if ($stocks == 1) {
      echo '<optgroup label="Stock Categories">';
    } elseif ($etfs == 1) {
      echo '</optgroup>';
      echo '<optgroup label="ETF Categories">';
    }
    echo '<option value="'.$row->id.'"';
    echo ($row->id == $selected_id)?(' selected="selected"'):('');
    echo '>'.$row->name.'</option>';
  }
  echo '</optgroup>';
  echo '</select>';
}
function sn_does_stock_exist($symbol) {
  global $wpdb;
  $result = $wpdb->get_var("SELECT COUNT(*) FROM sn_stocks WHERE symbol='$symbol'");
  return ($result == 0)?(false):(true);
}
function sn_does_firm_exist($firm) {
  global $wpdb;
  $result = $wpdb->get_var("SELECT COUNT(*) FROM sn_firms WHERE name='$firm'");
  return ($result == 0)?(false):(true);
}

/*
  Extract earnings data from SI
*/
function sn_get_earnings($sym,$date) {
  # Grab prior and following days too, just in case
  $date2 = date("n/j/y",(strtotime($date) - 86400)); # Prior day
  $date3 = date("n/j/y",(strtotime($date) + 86400)); # Following day

  # Use the Curl extension to query SI and get back a page of results
  $url = "http://www.streetinsider.com/ec_earnings.php?q=$sym";
  $html = sn_disguise_curl($url);

  # Create a DOM parser object
  $dom = new DOMDocument();

  # Parse the HTML...
  # The @ before the method call suppresses any bad html warnings
  @$dom->loadHTML($html);

  $arr = array();

  # Iterate over all the <table> tags
  foreach($dom->getElementsByTagName('table') as $table) {
    /*
    # Show the <a href>
    echo $link->getAttribute('href');
    echo "<br />";
    */
    $correct = 0;
    foreach($table->getElementsByTagName('tr') as $tr) {
      $tds = $tr->getElementsByTagName('td'); // get the columns in this row
      foreach($tds as $td) {
        if ((trim($td->nodeValue) == $date) || (trim($td->nodeValue) == $date2) || (trim($td->nodeValue) == $date3)) {
          # This is our date. Note we check the prior and following date in case it's off by a day.
          $correct = 1;
          $i = 1;
          $arr[1] = trim($td->nodeValue);
        } elseif ($correct == 1) {
          $i++;
          $arr[$i] = trim($td->nodeValue);
          /*
            $i values
            1 = date
            2 = conf call (not used)
            3 = quarter
            4 = reported EPS
            5 = consensus EPS
            6 = surprise amount
            7 = reported rev
            8 = consensus rev
          */
        }
      }
      $correct = 0;
    }
  }
  return $arr;
}
function sn_disguise_curl($url) 
{ 
  $curl = curl_init(); 

  // Setup headers - I used the same headers from Firefox version 2.0.0.6 
  // below was split up because php.net said the line was too long. :/ 
  //$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,"; 
  //$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
  //$header[] = "Cache-Control: max-age=0"; 
  //$header[] = "Connection: keep-alive"; 
  //$header[] = "Keep-Alive: 300"; 
  //$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
  //$header[] = "Accept-Language: en-us,en;q=0.5"; 
  //$header[] = "Pragma: "; // browsers keep this blank. 

  $User_Agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.37';
  $request_headers = array();
  $request_headers[] = 'User-Agent: '. $User_Agent;
  $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

  //$url = 'http://dynupdate.no-ip.com/ip.php';
  $proxy = '167.114.47.231:3128';
  //$proxyauth = 'user:password';

  $ch = curl_init();
  
  //curl_setopt($curl, CURLOPT_PROXY, $proxy);
  //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_HEADER, 1);

  curl_setopt($curl, CURLOPT_URL, $url); 
  curl_setopt($curl, CURLOPT_USERAGENT, $User_Agent); 
  curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers); 
  curl_setopt($curl, CURLOPT_REFERER, ''); 
  curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate'); 
  curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($curl, CURLOPT_TIMEOUT, 10);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

  $html = curl_exec($curl); // execute the curl command 
  curl_close($curl); // close the connection 

  return $html; // and finally, return $html 
} 

function sn_between_chars($start,$end,$string) {
  if ((strpos($string, $start) !== false) && (strpos($string, $end) !== false) && (strpos($string, $start) <= strpos($string, $end, strpos($string, $start)))) {
    $startsAt = strpos($string, $start) + strlen($start);
    $endsAt = strpos($string, $end, $startsAt);
    $new_string = substr($string, $startsAt, $endsAt - $startsAt);
    return $new_string;
  } else {
    return false;
  }
}

function sn_get_firms() {
  // returns array of firm ids and firm names
  $arr = array();
  global $wpdb;
  $query = "select * from sn_firms order by id asc";
  $result = $wpdb->get_results($query);
  foreach ($result as $row) {
    $arr[$row->id] = $row->name;
  }
  return $arr;
}

function sn_get_events() {
  // returns array of event ids and event names
  $arr = array();
  global $wpdb;
  $query = "select * from sn_events order by id asc";
  $result = $wpdb->get_results($query);
  foreach ($result as $row) {
    $arr[$row->id] = $arr[$row->event];
  }
  return $arr;
}

function sn_intval($num,$dollar=1) {
  // if $num has decimals that equal zero, returns intval of $num. else returns $num.
  $num = str_replace(",","",str_replace('$','',$num));
  if (strpos($num, ".00") !== FALSE) {
    $num = intval($num);
  }
  return ($dollar == 1)?('$'.$num):($num);
}

function sn_contains($needle,$haystack) {
  if (strpos($haystack, $needle) !== false) {
    return true;
  } else {
    return false;
  }
}

function sn_num_snippets($sym) {
  global $wpdb;
  return $wpdb->get_var("select count(*) from sn_snippets where symbol='$sym'");
}

function sn_update_snippet_prices($date=false) {
  global $wpdb;
  $i = 0;

  // determine previous trading day
  $prev_day = prev_trading_day();

  if ($date) {
    $date_prior = prev_trading_day(strtotime($date));
    $date_part = "(date = '$date' OR (date > '$date_prior' AND date < '$date') OR (date = '$date_prior' AND time >= '16:00'))";
  } else {
    $date_part = "date >= '2017-07-28' and date < '$prev_day'";
  }

  if ((!$date) || (sn_is_market_day($date))) {

    // how many snippets need updating?
    $need_updates = $wpdb->get_var("select count(*) from sn_snippets where day_close is null and $date_part and symbol in (select symbol from sn_stocks)");

    if ($need_updates > 0) {

        // and how many unique symbols?
        $unique_symbols = $wpdb->get_var("select count(distinct symbol) from sn_snippets where day_close is null and $date_part and symbol in (select symbol from sn_stocks)");
        if (!$date) {
          echo "<p>$need_updates snippets need updating. That means $unique_symbols queries to barchart.</p>";
        }

        // limit our number of queries to bc (usually 100)
        $query_limit = 100;
        if (!$date) {
          echo "<p>This page will query barchart up to $query_limit times, so <strong>".round($unique_symbols/$query_limit)." more refreshes needed</strong>. <a href='javascript:window.location.reload();'>Refresh now &raquo;</a></p>";
        }

        // find oldest snippet, so we know what start date to tell bc to grab historical data from
        $start_date = prev_trading_day(strtotime($wpdb->get_var("select min(date) from sn_snippets limit 1")));

        // grab our group of symbols that we will get historical data for
        $qh = "select distinct symbol from sn_snippets where day_close is null and $date_part and symbol in (select symbol from sn_stocks) limit $query_limit";
        $outer = $wpdb->get_results($qh);
        
        // set out counter for the number of updates
        $i = 0;
        
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
                $inner = $wpdb->get_results("select id,symbol,date,time from sn_snippets where symbol='".$out->symbol."' and date >= '2017-02-28' and (day_close = 0 or day_close is null)");
                //echo "<p><strong>Attempting to update ".count($inner)." snippets for ".$out->symbol.".</strong></p>";
                foreach ($inner as $in) {
                    if ((sn_is_market_day($in->date)) && (date('Gi',strtotime($in->date." ".$in->time)) < 1600)) {
                        // this snippet was posted on a market day, prior to the market close, so we use data from that date
                        $data_date = $in->date;
                        //echo '<p>Day of: '.$in->date.' @ '.$in->time.', so use '.$data_date.' closing price.</p>';
                    } else {
                        // this snippet was posted on a non-market day OR after the market close, so we use data from the next trading day
                        $data_date = next_trading_day(strtotime($in->date));
                        //echo '<p>Day after: '.$in->date.' @ '.$in->time.', so use '.$data_date.' closing price.</p>';
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
                      if (!$date) {
                        sn_alert_error("Error retrieving closing price for ".$out->symbol." on ".date("D",strtotime($data_date))." $data_date.");
                      }
                    }
                }
                //echo "<h2>$i ".$out->symbol." snippets updated successfully.</h2>";
            } else {
                echo sn_alert_error("Error retrieving historical data for ".$out->symbol);
            }
        }

    }

  }

  return $i;
}

function sn_update_histories() {
  global $wpdb;

  // number of symbols we'll try to update right now
  $num_syms = 10;

  // rolling days for trade grade (we're going with 10, but could be subject to change)
  $rolling_days = 10;

  // ttm_date = 200 trading days ago
  $ttm_date = '2015-05-28';

  // define ultimate end date
  $end_date = prev_trading_day(time());

  // determine total number of symbols left to update
  $unique_symbols = $wpdb->get_var("select count(*) from 
        sn_stocks where 
          category_id is not null and 
          ttm_date != '0000-00-00' and 
          ttm_date < '".$ttm_date."' and 
          symbol not in (select distinct symbol from sn_historical)");

  // let the user know what we're doing
  echo "<p>This page will update $num_syms symbols' Trade/B&H histories at a time, so <strong>".round($unique_symbols/$num_syms)." more refreshes needed</strong>.</p>";

  // determine list of symbols to try
  $q = "select symbol from 
        sn_stocks where 
          category_id is not null and 
          ttm_date != '0000-00-00' and 
          ttm_date < '".$ttm_date."' and 
          symbol not in (select distinct symbol from sn_historical) 
            order by symbol asc 
              limit $num_syms";
  $symbols = $wpdb->get_results($q);

  if (!empty($symbols)) {

      // if ultimate end date is a stock market holiday, we'll use the prior trading day instead
      if (!sn_is_market_day($end_date)) {
          $end_date = prev_trading_day($end_date);
      }

      // start date is ($rolling_days -1) trading days prior to end date (used for determining how far back we need historical data to)
      $start_date = prev_trading_day(strtotime($end_date),true);
      for ($r=1;$r < ($rolling_days -2);$r++) {
          $start_date = prev_trading_day($start_date,true);
      }
      $start_date = prev_trading_day($start_date);

      // "real" start date is the actual starting date (1 year and ($rolling_days -1) days prior to our end date)
      $real_start_date = date("Y-m-d",strtotime("-1 year",strtotime($end_date)));
      if (!sn_is_market_day($real_start_date)) {
          $real_start_date = prev_trading_day($real_start_date);
      }
      $fake_start_date = $real_start_date;
      $real_start_date = prev_trading_day(strtotime($real_start_date),true);
      for ($r=1;$r < ($rolling_days -3);$r++) {
          $real_start_date = prev_trading_day($real_start_date,true);
      }
      $real_start_date = prev_trading_day($real_start_date);

      // loop through symbols
      foreach ($symbols as $sym) {

          $sym = $sym->symbol;

          // debug
          echo "<p><strong>$sym</strong></p>";

          // reset sql query
          $query = "";

          // reset update counter
          $updated_days = 0;

          // get price history
          $history = get_historical_data($sym,date("Y-m-d",strtotime("-2 years",strtotime($start_date))),0,'',0,$end_date,1);
          
          // convert to array (so easier to search/manipulate)
          $history = json_decode(json_encode($history->results),TRUE);

          // we need a minimum of 200 days price history
          if (count($history) > 199) {

            // find the index of the real start date
            $real_start_index = find_where($history,array("tradingDay" => $real_start_date),true);

            // find the index of the fake start date
            $fake_start_index = find_where($history,array("tradingDay" => $fake_start_date),true);

            // grab analyst moves, but only need to do it once
            $moves = sn_snippets_by_symbol($sym,0,0,array(10,33,44,45,47,48,25,26),array($fake_start_date,$end_date));

            // loop through past year price history, generating daily scores and $rolling_days averages along the way
            for ($h=($real_start_index + ($rolling_days -1));$h < count($history);$h++) {

                // find array index (key) of start and end dates
                // $start_index = find_where($history,array("tradingDay" => $this_start_date),true);
                // $end_index = find_where($history,array("tradingDay" => $this_end_date),true);

                // define the starting and ending index for this $rolling_days-day period
                $start_index = $h - ($rolling_days -1);
                $end_index = $h;

                // start score from zero
                $week_score = 0;

                // loop through each $rolling_days day period, incrementing score along the way
                for ($x=0;$x<$rolling_days;$x++) {

                    // empty array to start with
                    $day_score = array();

                    // $x will be our offset
                    $this_start_index = $start_index + $x;

                    // basic closing info
                    $this_date = $history[$this_start_index]["tradingDay"];
                    $this_close = $history[$this_start_index]["close"];
                    $prev_close = $history[($this_start_index -1)]["close"];
                    $price_chg = $this_close - $prev_close;
                    $pct_chg = price_pct_change($this_close,$prev_close);
                    $day_high = $history[$this_start_index]["high"];
                    $day_low = $history[$this_start_index]["low"];

                    // determine current 52-week high and low based on this_date
                    $one_year_ago_date = date("Y-m-d",strtotime("-1 year",strtotime($this_date)));
                    if (!sn_is_market_day($one_year_ago_date)) {
                        $one_year_ago_date = prev_trading_day(strtotime($one_year_ago_date));
                    }
                    $one_year_ago_index = find_where($history,array("tradingDay" => $one_year_ago_date),true);
                    if (!is_int($one_year_ago_index)) {
                        // die("Something went wrong. date: $one_year_ago_date / index: $one_year_ago_index / earliest: ".$history[0]["tradingDay"]);
                        // we don't have a year of price history, so we'll just start from the earliest date we have
                        $one_year_ago_index = 0;
                    }
                    for ($i=$one_year_ago_index;$i<$this_start_index;$i++) {
                        if ($i == $one_year_ago_index) {
                            $fifty_high = $history[$i]["high"];
                            $fifty_low = $history[$i]["low"];
                        } else {
                            if ($history[$i]["high"] > $fifty_high) {
                                $fifty_high = $history[$i]["high"];
                            } elseif ($history[$i]["low"] < $fifty_low) {
                                $fifty_low = $history[$i]["low"];
                            }
                        }
                    }

                    // did we close positive?
                    if ($price_chg > 0) {
                        $day_score["price"] = 1;
                    } elseif ($price_chg < 0) {
                        $day_score["price"] = -1;
                    } else {
                        $day_score["price"] = 0;
                    }

                    // bonus points for large price moves
                    $abs_pct_chg = str_replace("-","",$pct_chg);
                    if ($abs_pct_chg >= 6) {
                        $bonus = 10;
                    } elseif ($abs_pct_chg >= 5) {
                        $bonus = 5;
                    } elseif ($abs_pct_chg >= 4) {
                        $bonus = 4;
                    } elseif ($abs_pct_chg >= 3) {
                        $bonus = 3;
                    } elseif ($abs_pct_chg >= 2) {
                        $bonus = 2;
                    } elseif ($abs_pct_chg >= 1) {
                        $bonus = 1;
                    } else {
                        $bonus = 0;
                    }
                    if ($bonus > 0) {
                        // make bonus negative if necessary        
                        $bonus = $bonus * $day_score["price"];
                        // actually add bonus
                        $day_score["price_bonus"] = $bonus;
                    }

                    // did we hit a new 52-week high or low?
                    if ($day_high > $fifty_high) {
                        $day_score["new_fifty_high"] = 1;
                        $fifty_high = $day_high;
                    }
                    if ($day_low < $fifty_low) {
                        $day_score["new_fifty_low"] = -1;
                        $fifty_low = $day_low;
                    }

                    // bonus points if we close within certain % of 52-week high (also negative bonuses if we don't)
                    $diff = $fifty_high - $this_close;
                    if (($diff / $fifty_high) < 0.05) {
                        $bonus = 6;
                    } elseif (($diff / $fifty_high) < 0.1) {
                        $bonus = 5;
                    } elseif (($diff / $fifty_high) < 0.2) {
                        $bonus = 2;
                    } elseif (($diff / $fifty_high) < 0.30) {
                        $bonus = -2;
                    } elseif (($diff / $fifty_high) < 0.40) {
                        $bonus = -5;
                    } else {
                        $bonus = -6;
                    }
                    $day_score["bonus_52_week"] = $bonus;

                    // check if we closed above/below moving averages: 10, 20, 50, 100, 200 day
                    for ($i=0;$i<5;$i++) {
                        if ($i == 0) {
                            $ma = 10;
                        } elseif ($i == 1) {
                            $ma = 20;
                        } elseif ($i == 2) {
                            $ma = 50;
                        } elseif ($i == 3) {
                            $ma = 100;
                        } elseif ($i == 4) {
                            $ma = 200;
                        }

                        // calculate SMA
                        $ma_start_index = $this_start_index - $ma;
                        $price_total = 0;
                        for ($a=1;$a<($ma +1);$a++) {
                           $price_total += $history[($this_start_index - $a)]["close"];
                        }
                        $this_sma = $price_total / $ma;
                        $sma[$ma] = $this_sma;

                        // are we above this sma?
                        if ($this_close > $this_sma) {
                            $day_score["above_{$ma}_day_sma"] = 1;
                        }
                        if ($this_close < $this_sma) {
                            $day_score["below_{$ma}_day_sma"] = -1;
                        }
                    }

                    // loop through analyst moves, checking for moves on our current date
                    $move_count = 0;
                    foreach ($moves as $row) {
                        if ($row->date == $this_date) {
                            $move_count++;
                            if (($row->event_id == 10) || ($row->event_id == 45) || ($row->event_id == 48)) {
                                // new bearish rating -> -2 from day_score
                                $day_score["downgrade_{$move_count}"] = -2;
                            } elseif ($row->event_id == 25) {
                                // pt lowered -> -1 from day_score
                                $day_score["pt_cut_{$move_count}"] = -1;
                            } elseif (($row->event_id == 33) || ($row->event_id == 44) || ($row->event_id == 47)) {
                                // new bullish rating -> +2 to day_score
                                $day_score["upgrade_{$move_count}"] = 2;
                            } elseif ($row->event_id == 26) {
                                // pt raised -> +1 to day_score
                                $day_score["pt_raised_{$move_count}"] = 1;
                            }
                        }
                    }

                    // loop through our day_score array and tally up the totals
                    $total_day_score = 0;
                    foreach ($day_score as $ds) {
                        $total_day_score = $total_day_score + $ds;
                    }

                    // debug
                    //print_r($day_score);

                    // debug
                    //echo "<p>$this_date score: $total_day_score</p>";

                    // add day's score to week's score
                    $week_score = $week_score + $total_day_score;

                } // end for

                // debug - echo total score
                // echo "<p><strong>$sym Trade Grade on $this_date: ".sn_trade_grade($week_score)." ($week_score)</strong></p><hr>";

                // construct query
                $query = "INSERT INTO sn_historical 
                            (symbol, 
                            `date`, 
                            day_open, 
                            day_high, 
                            day_low, 
                            day_close, 
                            day_volume, 
                            prev_close, 
                            day_trade_score, 
                            rolling_trade_score, 
                            buy_and_hold_score, 
                            fifty_two_week_high, 
                            fifty_two_week_low, 
                            moving_avg_10_day, 
                            moving_avg_20_day, 
                            moving_avg_50_day, 
                            moving_avg_100_day, 
                            moving_avg_200_day) 
                            VALUES 
                            ('$sym', 
                            '".$history[$h]["tradingDay"]."', 
                            '".$history[$h]["open"]."', 
                            '".$history[$h]["high"]."', 
                            '".$history[$h]["low"]."', 
                            '".$history[$h]["close"]."', 
                            '".$history[$h]["volume"]."', 
                            '".$history[($h -1)]["close"]."', 
                            '$total_day_score',
                            '$week_score',
                            '".sn_performance_grade($history[$h]["close"],$fifty_high)."',
                            '$fifty_high',
                            '$fifty_low',
                            '".$sma[10]."',
                            '".$sma[20]."',
                            '".$sma[50]."',
                            '".$sma[100]."',
                            '".$sma[200]."')
                            ";
                        //echo "<p>$query</p>";
                
                //$foo = $wpdb->query($query);
                
                $updated_days++;
                //die("foo: $foo");

            } // end for (historical prices)

            echo "<ul><li>Updated $updated_days days of trading history.</li></ul>";

          } else {

            echo "<ul><li>Skipped - not enough trading history</li></ul>";

          } // end if (minimum 200 days of pricing history)

      } // end foreach (symbols)

  } // end if
}

function sn_extract_city_from_earnings($sym) {

  // returns an array of city, image, and company type, if found

  if ($sym) {

    global $wpdb;

    $q = "select post_content,image_url from sn_snippets where event_id='11' and symbol='$sym' and date < '".date('Y-m-d')."' and post_content is not null and post_content <> '' order by date desc limit 1";
    $prev_earnings = $wpdb->get_row($q);

  }

  if ($prev_earnings <> "") {

    // explode our string and get the city name
    $str = explode('<!--more-->', $prev_earnings->post_content);
    $acity = explode('-based ', trim($str[1]));
    $city = trim(str_replace("The ","",$acity[0]));

    if (($city == "") || (strlen($city) > 40)) {
      $city = '[city]';
      $company = '[company]';
    } else {
      // get the short company description
      $acompany = explode('reported ',$acity[1]);
      $company = trim($acompany[0]);

      if ($company == "") {
        $company = '[company]';
      }
    }

    // assign image
    if ($prev_earnings->image_url <> "") {
      $image = $prev_earnings->image_url;
    } else {
      $image = '';
    }

  } else {

    $city = '[city]';
    $company = '[company]';
    $image = '';

  }

  return array("city"=>$city,"company"=>$company,"image"=>$image);
}

function sn_format_earnings_text($text,$sym=false) {

  global $wpdb;

  $text = sn_remove_earnings_month($text);
  $text = str_replace(" mln "," million ",str_replace(" bln "," billion ",$text));
  $return = '';
  $split = explode("\n",trim($text,"\n"));

  foreach($split as $i=>$s) {
    if ($i==0) {
      $foo = explode(". ",$s);
      //echo $foo[0]; // first sentence, which contains eps/revenue numbers
      $eps = explode(';',$foo[0]);

      $earnings_info = sn_extract_city_from_earnings($sym);
      if ($earnings_info["image"] <> "") {
        update_option("populate_default_featured_image",$earnings_info["image"]);
      }

      $return .= 'The '.$earnings_info["city"].'-based '.$earnings_info["company"].' reported'.str_replace('Capital IQ Consensus','Wall Street consensus estimate',str_replace('excluding non-recurring items,','',str_replace(' per share,',', which was',str_replace(' earnings of ','earnings per share (EPS) of ',str_replace('('.date("M",strtotime(date("Y-m-d")." -31 days")).')','',str_replace('('.date("M").')','',str_replace('Reports','',$eps[0]))))))).".\r\n\r\n";
      $return .= ucfirst(str_replace(' Capital IQ Consensus','',str_replace(' vs the ',', compared with analysts\' view for ',str_replace(' bln ',' billion ',str_replace(' mln ',' million ',str_replace('year/year','from last year',trim($eps[1]))))))).".\r\n\r\n";
      //echo $s."<p>";
    } else {
      $return .= $s."\r\n";
    }
  }
  $return .= "The company commented via press release:\r\n\r\n<blockquote></blockquote>\r\n\r\n";

  $return = str_replace("  "," ",$return);

  return $return;
}







function sn_remove_earnings_month($text) {
  $text = str_replace("(Dec)", "",str_replace("(Nov)", "",str_replace("(Oct)", "",str_replace("(Sep)", "",str_replace("(Aug)", "",str_replace("(Jul)", "",str_replace("(Jun)", "",str_replace("(May)", "",str_replace("(Apr)", "",str_replace("(Mar)", "",str_replace("(Feb)", "",str_replace("(Jan)", "", $text))))))))))));
  return $text;
}





function snipAds1($content, $ad=null, $element="</p>", $altElement='</p>', $afterElement=1, $minElements=5) {
    // Ser your block to be inserted here
    if (!$ad) $ad ='<!-- no ad -->';
 
    // split content
    $parts = explode($element, $content);
 
    // count element ocurrencies 
    $count = count($parts);
 
    // check if the minimum required elements are found
    if(($count-1)<$minElements) {
        $parts = explode($altElement, $content); // check for the alternative tag
        $count = count($parts);
        if(($count-1)<$minElements) return $content; // you can give up here and just return the original content
        $element = $altElement; // continue by using alternative tag instead of the primary one
    }
    
    $output='';
    for($i=1; $i<$count; $i++) {
        // this is the core part that puts all the content together
        //$output .= $parts[$i-1] . $element .  (($i==$afterElement) ? $ad : ''); // this win insert after
        $output .=  ($i==1 ? $parts[0]:'') . (($i==$afterElement) ? $ad : '')  . $element . $parts[$i]; //this win insert before
    }
    return $output;
}

function snipAds2($content, $ad=null, $element="</p>", $altElement='</p>', $afterElement=1, $minElements=5) {
    // Ser your block to be inserted here
    if (!$ad) $ad ='<!-- no ad -->';
 
    // split content
    $parts = explode($element, $content);
 
    // count element ocurrencies 
    $count = count($parts);
 
    // check if the minimum required elements are found
    if(($count-1)<$minElements) {
        $parts = explode($altElement, $content); // check for the alternative tag
        $count = count($parts);
        if(($count-1)<$minElements) return $content; // you can give up here and just return the original content
        $element = $altElement; // continue by using alternative tag instead of the primary one
    }
    
    $output='';
    for($i=1; $i<$count; $i++) {
        // this is the core part that puts all the content together
        //$output .= $parts[$i-1] . $element .  (($i==$afterElement) ? $ad : ''); // this win insert after
        $output .=  ($i==1 ? $parts[0]:'') . (($i==$afterElement) ? $ad : '')  . $element . $parts[$i]; //this win insert before
    }
    return $output;
}








function sn_default_editor_content( $content ) {
  if (isset($_GET["populate_default_content"])) {
    $content = stripslashes(get_option("populate_default_content"));
    return $content;
  }
}
add_filter( 'default_content', 'sn_default_editor_content' );

if (isset($_GET["populate_default_content"])) {
  add_filter('admin_footer_text', 'sn_enqueue_custom_script');
}

function sn_get_symbol_meta_tags($sym) {
  global $wpdb;
  $q = "select * from sn_symbol_meta where symbol='$sym' limit 1";
  $row = $wpdb->get_row($q);
  if (!empty($row)) {
    return $row;
  } else {
    return false;
  }
}

// auto-adds tags to article drafts based on the input
function sn_enqueue_custom_script($text) {

  $company = stripslashes(get_option('populate_default_company'));
  $symbol = get_option('populate_default_symbol');
  $full_symbol = get_option('populate_default_fullsym');
  $symbol_cat_id = get_cat_ID( $full_symbol );
  $symbol_meta = sn_get_symbol_meta_tags($symbol);
  $author = get_option('populate_default_author');
  $source = get_option('populate_default_source');

  $script = "<script>jQuery( document ).ready(function() {
  jQuery('input[name=aiosp_title]').val('".$company."');
  jQuery('textarea[name=aiosp_description]').val('".$company."');";
  if ($full_symbol == "INDEXDJX:.DJI") {
    $script .= "jQuery('input[name=aiosp_keywords]').val('".$full_symbol."');";
    $script .= "jQuery('input#new-tag-post_tag').val('".$full_symbol.",Dow Jones Industrial Average');";
  } else {
    $script .= "jQuery('input[name=aiosp_keywords]').val('".$full_symbol.",".$symbol.",".$company."');";
    $script .= "jQuery('input#new-tag-post_tag').val('".$full_symbol.",".$symbol.",".str_replace(",","",$company)."');";
  }

  // populates tags based on source/author
  if ($author <> "") {
    $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".str_replace(",","",$author)."');";
  }
  if ($source <> "") {
    $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".str_replace(",","",$source)."');";
  }

  // populates ticker-specific tags based on sn_symbol_meta table
  if ($symbol_meta) {
    $script .= "\n\n".'jQuery(\'#snippet_select option:contains("'.$symbol_meta->category.'")\').attr("selected", "selected");';
    if ($symbol_meta->meta1 <> "") {
      $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".$symbol_meta->meta1."');";
    }
    if ($symbol_meta->meta2 <> "") {
      $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".$symbol_meta->meta2."');";
    }
    if ($symbol_meta->meta3 <> "") {
      $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".$symbol_meta->meta3."');";
    }
    if ($symbol_meta->meta4 <> "") {
      $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".$symbol_meta->meta4."');";
    }
    if ($symbol_meta->meta5 <> "") {
      $script .= "jQuery('input#new-tag-post_tag').val(jQuery('input#new-tag-post_tag').val()+',".$symbol_meta->meta5."');";
    }
  }

  // more tags
  $script .=
  "setTimeout(function(){ jQuery('input.tagadd')[0].click() }, 100);
  setTimeout(function(){ jQuery('input#in-category-".$symbol_cat_id."')[0].click() }, 100);";

  // DJIA specific category selection
  if ($full_symbol == "INDEXDJX:.DJI") {
    $symbol_cat_id_2 = get_cat_ID( "NYSE:DIA" );
    $symbol_cat_id_3 = get_cat_ID( "NYSE:SPY" );
    $script .= "setTimeout(function(){ jQuery('input#in-category-".$symbol_cat_id_2."')[0].click() }, 100);";
    $script .= "setTimeout(function(){ jQuery('input#in-category-".$symbol_cat_id_3."')[0].click() }, 100);";
  }

  $script .=
"});</script>";

  echo $script;

}





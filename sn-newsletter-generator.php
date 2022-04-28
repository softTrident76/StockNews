<?php
/* 
  Top 10 Stories Newsletter Generator Admin Page
*/

function stocknews_admin_newsletter_generator_page() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  echo '<div class="wrap">';
  echo '<h2>"Top 10" Stories Newsletter Generator</h2>';

  echo '<p>Send this email Monday through Friday at 1pm Eastern.</p>';

  echo '<p>Right click each of these files, Save As, and save to your Desktop, overwriting previous files if necessary.</p>';

  echo '<hr>';

  echo '<ul>';

  $news = generate_news_importance(date("Y-m-d"));
  $news = array_slice($news,0,10);

  /*
  echo "<pre>";
  print_r($news);
  echo "</pre>";
  */

  $newsletter_head =
    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraph.org/schema/">
  <head>
    <meta property="og:title" content="StockNews.com Daily Newsletter">
    <meta property="fb:page_id" content="43929265776">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockNews.com</title>
    
  <style type="text/css">
    body{
      font-family:"Georgia","Times New Roman",serif;
      font-size:18px;
      text-align:center;
      margin:0;
      line-height:1.66;
    }
    img,a img{
      border:0;
      outline:none;
      text-decoration:none;
    }
    a,a:visited{
      color:#448BD4;
      text-decoration:none;
    }
    figure{
      margin:10px 0;
    }
    figcaption,div.figcaption{
      margin:1em 0;
      padding:0;
    }
    h1,h2,h3,h4,h5{
      font-family:Helvetica,arial,sans-serif;
      line-height:1.5;
      margin:.5em 0;
    }
    h4{
      font-size:1em;
    }
    h3{
      font-size:1.5em;
      margin:5px 0 0;
      color:black;
    }
    hr{
      border:0;
      border-top:1px solid #ccc;
      margin:20px 0;
    }
    ul,ol{
      margin:.5em 0 0 1.5em;
      padding:0;
    }
    li{
      margin:.5em 0;
    }
    blockquote{
      background:#eee;
      padding:20px;
      border-radius:10px;
      margin:40px 0;
    }
    small{
      font-family:Helvetica,arial,sans-serif;
    }
    .wrapper{
      margin:0 auto;
      max-width:640px;
      text-align:left;
      width:95%;
    }
    .hook{
      font-size:1px;
      line-height:0;
      margin:0;
      color:transparent;
      display:block;
      height:0;
      width:0;
    }
    .box-ad,.presented-by{
      text-align:center;
    }
    p.box-ad-label,.article-body .presented-by-label{
      font-size:9px;
      color:#999;
      font-family:"Helvetica","Arial",sans-serif;
      text-transform:uppercase;
      display:block;
      margin:10px 0;
      -webkit-text-size-adjust:none;
    }
    .date{
      font-weight:bold;
      font-family:"Helvetica","Arial",sans-serif;
      margin:1em 0 0;
    }
    .header{
      background:#eee;
      overflow:hidden;
      position:relative;
    }
    .logo{
      background:black;
      position:relative;
      overflow:hidden;
      margin:0;
      float:left;
    }
    .logo img{
      margin-top:10px;
    }
    .share-block{
      float:right;
      padding:20px 5px 0 0;
      margin:0;
    }
    .top-social-icons img{
      margin-right:5px;
    }
    .share-title{
      display:block;
      font-family:Helvetica,arial,sans-serif;
      color:#999;
      text-align:center;
      text-transform:uppercase;
      font-size:9px;
      width:120px;
      margin-bottom:10px;
      -webkit-text-size-adjust:none;
    }
    .welcome{
      color:#999;
      font-family:Helvetica,arial,sans-serif;
      margin-top:0;
      line-height:1.4em;
      font-size:.9em;
      border-bottom:1px solid #ccc;
      padding-bottom:20px;
      margin-bottom:20px;
    }
    .article-body{
      font-family:"Georgia","Times New Roman",serif;
      font-size:18px;
    }
    .article-body p{
      font-size:18px;
      line-height:1.66;
    }
    .article-body img{
      display:block;
      width:100%;
      margin:15px 0 10px;
    }
    .article-body img.box-ad-img{
      display:inline-block;
      width:auto;
      margin:0;
    }
    .rubric{
      color:white;
      background:black;
      display:inline-block;
      padding:8px 5px 5px;
      margin:20px 0 0;
      line-height:1;
      font-size:.75em;
      letter-spacing:1px;
      font-weight:normal;
      text-transform:uppercase;
    }
    .uppercase{
      text-transform:uppercase;
    }
    .most-popular{
      font-size:18px;
    }
    .most-popular h4{
      font-weight:normal;
      margin:0;
    }
    .most-popular ol{
      padding-bottom:20px;
    }
    .article-body small{
      font-family:"Helvetica","Arial",sans-serif;
      font-size:12px;
    }
    .sponsor-label{
      background:#FFC317;
      font-size:11px;
      padding:7px 8px 4px;
      text-transform:uppercase;
      letter-spacing:2px;
      font-weight:bold;
      color:black;
      display:inline-block;
      line-height:1;
      border-radius:5px;
    }
    a.sponsor,a.sponsor:visited{
      color:black;
    }
    .sponsor-title{
      font-family:"Palatino","Times New Roman",times,serif;
      margin:0;
      color:black;
      line-height:1.25em;
    }
    .sponsor-dek{
      font-family:"Helvetica","Arial",sans-serif;
      color:#656565;
      margin:0;
      line-height:1.5;
    }
    .sponsor-content a{
      color:inherit;
      display:block;
    }
    .editorial-promo{
      vertical-align:top;
      margin:40px 0;
      position:relative;
      overflow:hidden;
    }
    .editorial-promo-title{
      background:black;
      color:white;
      padding:4px 10px 2px;
    }
    .editorial-promo-title a{
      color:white;
    }
    .editorial-promo-image{
      display:inline-block;
      vertical-align:top;
      margin-right:10px;
      margin-bottom:10px;
    }
    .editorial-promo-dek{
      display:inline-block;
      max-width:160px;
      vertical-align:top;
      margin:0;
      font-family:"Helvetica","Arial",sans-serif;
      line-height:1.2;
      color:#666;
    }
    .wrap-links{
      text-align:center;
      width:100%;
      clear:both;
    }
    .wrap-links ul{
      list-style-type:none;
      margin:1em auto;
      padding:0;
      max-width:430px;
      display:inline-block;
    }
    .wrap-links li{
      border-radius:8px;
      padding:.66em 1em;
      margin:1em 0;
      font-size:.9em;
      background:#448BD4;
      text-align:center;
      font-family:"Helvetica",Arial,sans-serif;
    }
    .wrap-links li a{
      color:white;
      text-decoration:none;
      font-weight:bold;
    }
    .news-rank{
      font-size:16px;
      font-weight:bold;
      padding:5px 0;
      text-align:center;
      color:#fff;
      width:40px;
      float:left;
    }
    .news-symbol{
      font-weight:bold;
      text-align:center;
      float:left;
      width:70px;
      padding:5px 10px;
    }
    .news-headline{
      background:#f0f0f0;
      padding:5px 10px;
      float:left;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      max-width:800px;
    }
  @media only screen and (max-width: 1199px){
    .news-headline{
      max-width:600px;
    }

} @media only screen and (max-width: 992px){
    .news-headline{
      max-width:400px;
    }

} @media only screen and (max-width: 768px){
    .news-headline{
      float:none;
      clear:both;
      white-space:normal;
      text-overflow:normal;
      overflow:visible;
      max-width:none;
      padding-left:55px;
      padding-bottom:20px;
    }

} @media only screen and (max-width: 768px){
    .no-mobile-pull{
      float:none !important;
    }

} @media only screen and (max-width: 768px){
    .margin-top-mobile{
      margin-top:25px !important;
    }

}   .news-headline-home{
      max-width:600px;
    }
  @media only screen and (max-width: 1199px) and (min-width: 991px){
    .news-headline-home{
      max-width:500px;
    }

}   .news-box{
      background:#f0f0f0;
      margin-bottom:1px;
    }
    .news-ticker{
      padding:5px 10px;
    }
    .news-mover{
      padding-top:5px;
    }
    .clearfix{
      clear:both;
    }
    .rank-1{
      background:#67c067;
    }
    .rank-2{
      background:#7bc97e;
    }
    .rank-3{
      background:#80c383;
    }
    .rank-4{
      background:#89c08b;
    }
    .rank-5{
      background:#92ba94;
    }
    .rank-6{
      background:#97b798;
    }
    .rank-7{
      background:#9bb39d;
    }
    .rank-8{
      background:#a0b1a1;
    }
    .rank-9{
      background:#a9abab;
    }
    .rank-10{
      background:#9b9c9b;
    }
    .rank-11{
      background:#767878;
    }
    .news-headline-home{
      max-width:450px;
    }
    .button {
  /* text styles */
  font-size: 21px;
  line-height: 28px;
  color: #fff !important;
  text-decoration: none;
  text-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
  
  /* box styles */
  display: inline-block;
  height: 29px;
  padding: 0 20px;
  border: 1px solid;
  border-color: #2d86b6 #24659e #255796;
  border-radius: 21px;
  box-shadow: 0 1px 1px rgba(255, 255, 255, 0.2) inset, 0 1px 1px rgba(1, 4, 8, 0.2);
  
  /* gradients */
  background-color: #52b6ec;
  *zoom: 1;
  filter: progid:DXImageTransform.Microsoft.gradient(gradientType=0, startColorstr="#FF52B6EC", endColorstr="#FF2E75CE");
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #52b6ec), color-stop(100%, #2e75ce));
  background-image: -webkit-linear-gradient(top, #52b6ec 0%, #2e75ce 100%);
  background-image: -moz-linear-gradient(top, #52b6ec 0%, #2e75ce 100%);
  background-image: -o-linear-gradient(top, #52b6ec 0%, #2e75ce 100%);
  background-image: linear-gradient(top, #52b6ec 0%, #2e75ce 100%);
    
}
.button:hover {
  text-decoration: none;
  border-color: #377cae #175a9c #0c4893;
  box-shadow: 0 1px 1px rgba(255, 255, 255, 0.2) inset, 0 1px 1px rgba(5, 64, 140, 0.2);
  
  background-color: #4fa6e4;
  *zoom: 1;
  filter: progid:DXImageTransform.Microsoft.gradient(gradientType=0, startColorstr="#FF4FA6E4", endColorstr="#FF1462C4");
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #4fa6e4), color-stop(100%, #1462c4));
  background-image: -webkit-linear-gradient(top, #4fa6e4 0%, #1462c4 100%);
  background-image: -moz-linear-gradient(top, #4fa6e4 0%, #1462c4 100%);
  background-image: -o-linear-gradient(top, #4fa6e4 0%, #1462c4 100%);
  background-image: linear-gradient(top, #4fa6e4 0%, #1462c4 100%);
}
.button:active {
  border-color: #094b84 #094b84 #0f4585;
  box-shadow: 0 1px 1px rgba(241, 243, 247, 0.2), 0 0 20px rgba(0, 0, 0, 0.5) inset;
  
  background-color: #1c7ec9;
  *zoom: 1;
  filter: progid:DXImageTransform.Microsoft.gradient(gradientType=0, startColorstr="#FF1C7EC9", endColorstr="#FF2395D9");
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #1c7ec9), color-stop(100%, #2395d9));
  background-image: -webkit-linear-gradient(top, #1c7ec9 0%, #2395d9 100%);
  background-image: -moz-linear-gradient(top, #1c7ec9 0%, #2395d9 100%);
  background-image: -o-linear-gradient(top, #1c7ec9 0%, #2395d9 100%);
  background-image: linear-gradient(top, #1c7ec9 0%, #2395d9 100%);
}
</style></head>
  <body style="font-family: " georgia="" new="" roman="" serif="" center="">
    <span class="hook" style="font-size: 1px;line-height: 0px;margin: 0;color: transparent;display: block;height: 0px;width: 0px;">Today\'s top market stories, as determined by our proprietary ranking algorithm, are...</span>
    <div class="wrapper" style="margin:0 auto;max-width:640px;text-align:left;width:95%;">
      <div class="header" style="background:#262626;overflow:hidden;position:relative;">
        <h1 class="logo" style="font-family:Helvetica, arial, sans-serif;line-height:1.5;margin:0;background:#262626;position:relative;overflow:hidden;float:left;">
          <a href="http://stocknews.com/"><img src="http://www.gliq.com/clients/stocknews/stocknews.com-logo-ic.png" width="373" style="border: 0;outline: none;text-decoration: none;margin-top: 10px; margin-bottom:15px; margin-left:10px" alt="StockNews.com"></a>
        </h1>
        <div class="share-block" style="float:right;padding:10px 5px 0 0;margin:0;">
          <div class="social-icons top-social-icons"></div>
        </div>
      </div>
            
            <p class="date" style="font-weight:bold;font-family:Helvetica, Arial, sans-serif;margin:1em 0 0 0;">
              '.date("F j, Y").'
            </p>
            <p class="welcome" style="color:#999;font-family:Helvetica, arial, sans-serif;margin-top:0;line-height:1.4em;font-size:.9em;border-bottom:1px solid #ccc;padding-bottom:20px;margin-bottom:20px;">
            </p>
            <div class="article-body" style="font-family:Helvetica, Arial, sans-serif;font-size:18px;">
              <h3>Today\'s Most Important Stock News</h3>
              Our proprietary algorithm ranks each day\'s stock news based on event importance, price moves, ticker and category popularity, and much more.
              <hr style="border:0;border-top:1px solid #ccc;margin:20px 0;">';

$newsletter_footer = '

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
  <a href="http://www.gliq.com/cgi-bin/unsub_dedicated?stocknews&#email#&#ccode#&sl_01182018171542&rt_1">Unsubscribe from this list</a><br><br>
  StockNews.com LLC<br>639 Margarita Ave<br>Coronado, CA 92118
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
    ';

  // loop through four times to generate our four versions of this newsletter, one for each of our segments: paid, trial, free, and expired
  for ($x=0;$x<4;$x++) {

    $html = '';

    for ($i=1;$i<11;$i++) {
      $headline = ($news[($i -1)]->headline <> "")?($news[($i -1)]->headline):(ucwords($news[($i -1)]->notes));
      $html .= '<div class="news-box" id="news-box-'.$i.'">
              <div class="news-rank rank-'.$i.'">'.$i.'</div>
              <div class="news-symbol">'.$news[($i -1)]->symbol.'</div>
              <div class="news-headline news-headline-home"><a href="https://stocknews.com'.$news[($i -1)]->article_url.'">'.$headline.'</a></div>
              <div class="clearfix"></div>
            </div>';
      if ($i == 5) {
        if ($x == 0) {
          $sn_prefix = 'PAID';
        } elseif ($x == 1) {
          // trial member upsell
          $html .= '
          <div style="background:#feeaa4; padding:20px">
            <strong>Your StockNews.com Account Will Expire Soon!</strong><br><br>
            Upgrade to a paid account today for <strong>only $99 per year</strong> to retain access to our daily newsletters, real-time news feed, POWR Ratings, Best Stocks List, and much more!<br><br>
            <a href="https://stocknews.com/members/signup" class="button">Upgrade To Premium Now!</a>
          </div>
          ';
          $sn_prefix = 'TRIAL';
        } elseif ($x == 2) {
          // expired member upsell
          $html .= '
          <div style="background:#feeaa4; padding:20px">
            <strong>Renew Your StockNews.com Premium Account!</strong><br><br>
            Unfortunately your StockNews.com Premium account has expired. Upgrade now to re-enable access to our exclusive daily newsletters, real-time news feed, POWR Ratings, Best Stocks List, and much more!<br><br>
            <a href="https://stocknews.com/members/signup" class="button">Upgrade To Premium Now!</a>
          </div>
          ';
          $sn_prefix = 'EXPIRED';
        } elseif ($x == 3) {
          // free member upsell
          $html .= '
          <div style="background:#feeaa4; padding:20px">
            <strong>Try StockNews.com Premium!</strong><br><br>
            Get access to our exclusive daily newsletters, real-time news feed, POWR Ratings, Best Stocks List, and much more when you take a FREE two-week trial of StockNews.com Premium -- no credit card required!<br><br>
            <a href="https://stocknews.com/members/signup" class="button">Try Premium Now!</a>
          </div>
          ';
          $sn_prefix = 'FREE';
        }
      }
    } // end for

    // save our HTML file to disk
    file_put_contents(plugin_dir_path(__FILE__).$sn_prefix.'-TOP10-NEWSLETTER.html', $newsletter_head.$html.$newsletter_footer);

    // spit out a link to download the HTML file
    echo '<li><strong><a href="'.plugin_dir_url(__FILE__).$sn_prefix.'-TOP10-NEWSLETTER.html">'.$sn_prefix.'-TOP10-NEWSLETTER.html</a></strong></li>';

  } // end for

  echo '</ul>';

  echo '<hr>';

  echo '<p><em>Full instructions on sending these newsletters can be found here.</em></p>';

  echo '</div>';
}
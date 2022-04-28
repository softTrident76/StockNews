<?php
/*
Plugin Name: StockNews.com Admin Section
Plugin URI: http://stocknews.com/
Description: Allows admin to add/edit ticker-centric news snippets and many other site controls
Version: 0.1 alpha
Author: StockNews.com
Author URI: http://stocknews.com/
License: None
Text Domain: StockNews.com
*/

/* Temp fix for AWS time zone bullshit */
date_default_timezone_set("America/New_York");

/* Each admin page gets its own include */
include("sn-menu-header.php");
include("sn-admin-homepage.php");
include("sn-manual-newsletter-generator.php");
//include("sn-csv-generator.php");
//include("sn-powr-csv-generator.php");

/* And all the required functions */
include("sn-functions.php");

/* Temporary: page load stats in footer (@todo: move to its own include) */
add_action('admin_footer', 'sn_admin_footer_function');
function sn_admin_footer_function() {
  echo '<div class="wrap text-center">';
  $snq = get_num_queries();
  echo '<p>'. $snq . ' queries in ' . timer_stop(0) . ' seconds.</p>';
  echo '</div>';
}

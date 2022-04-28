<?php
/* 
  Admin Main Page
*/
function stocknews_admin_plugin_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }  
  echo '<div class="wrap">';
  echo '<h2>StockNews.com Admin</h2>';
  echo '<ul>';
  echo '<li><a href="'.admin_url().'admin.php?page=stocknews_admin_add_snippet">Add a News Snippet</a></li>';
  echo '<li><a href="'.admin_url().'admin.php?page=stocknews_admin_view_snippets">View/Edit News Snippets</a></li>';
  echo '</ul>';
  echo '</div>';
  echo '<hr>';
  echo '<strong>Running StockNews Crons:</strong><pre>';
  $crons = _get_cron_array();
  foreach ($crons as $cron) {
    $cron_name = key($cron);
    if (substr($cron_name,0,3) == "sn_") {
      print_r($cron);
    }
  }
  echo "</pre>";
}
add_action( 'admin_menu', 'stocknews_plugin_menu' );
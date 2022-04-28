<?php
/* 
  Adds menu to left rail and includes js/css we need
*/
function stocknews_plugin_menu() {
  //add_options_page( 'My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
  add_menu_page( 'StockNews.com Admin', 'StockNews.com Admin', 'manage_options', 'stocknews_admin_main', 'stocknews_admin_plugin_options', '', '1.1' );
   add_submenu_page( 'stocknews_admin_main', 'Daily Newsletter Sender', 'Daily Newsletter Sender', 'publish_posts', 'sn_admin_newsletter_wrapper_generator', 'stocknews_admin_newsletter_wrapper_page');
   wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
  wp_enqueue_style('jquery-tablesorter-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.24.5/css/theme.bootstrap_2.min.css');
  wp_enqueue_script('jquery-form-validation', 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js');
  wp_enqueue_script('jquery-tablesorter', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.24.5/js/jquery.tablesorter.min.js');
  wp_enqueue_script( 'stocknews_admin_js', plugin_dir_url( __FILE__ ) . 'stocknews_admin.js' );
  wp_enqueue_style('stocknews_admin_css', plugin_dir_url( __FILE__ ) . 'stocknews_admin.css');
}
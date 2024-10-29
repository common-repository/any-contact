<?php

function AnyContact_Uninstall() {
  
  #if(! defined('WP_UNINSTALL_PLUGIN')) { return; }
  
  global $wpdb;
  $posts = $wpdb->delete($wpdb->prefix.'posts',array('post_type' => 'anycontact'));
  
  delete_option('anycontact_version');
}

?>
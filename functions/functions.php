<?php

function AnyContact_Version() {
  
  $version = '0.1.1';
  
  if(get_option('anycontact_version') != $version) {
    update_option('anycontact_version',$version);
  }
}

function AnyContact_RedirectToWebsite() {
  ?>
  <script type="text/javascript">
    window.location.href = 'http://any-contact.com';
  </script>
  <noscript>
    <a href="http://any-contact.com" target="_blank">Zur Plugin Website</a>
  </noscript>
  <?
}

function AnyContact_AdminLink($args = array()) {
  if(!is_array($args) || sizeof($args) == 0) return admin_url('admin.php');
  foreach($args AS $k => $v) {$get_args .= $k.'='.$v.'&';}
  $get_args = substr($get_args,0,strlen($get_args)-1);
  return admin_url('admin.php?'.$get_args);
}

function AnyContact_Ajax() {
  if(!wp_verify_nonce($_POST['wp_nonce'],$_POST['action'])) {
    die(json_encode(array('error' => array('msg' => 'Ihre Anfrage wurde aus Sicherheitsgründen abgewiesen!'))));
  }
  
  if(!array_key_exists('args',$_POST) || !is_array($_POST['args'])) {
    die(json_encode(array('error' => array('msg' => 'Ihre Anfrage konnte nicht bearbeitet werden, da die benötigten Daten falsch übermittelt wurden.'))));
  }
  
  array_walk_recursive($_POST['args']['field'],'htmlentities_fix');
  array_walk_recursive($_POST['args']['text'],'htmlentities_fix');
  array_walk_recursive($_POST['args']['setting'],'htmlentities_fix');
  
  $post = array('ID'            => $_POST['args']['id'],
                'post_title'    => $_POST['args']['title'],
                'post_content'  => json_encode(array_merge(array(),
                                                           array('field'    => $_POST['args']['field']),
                                                           array('text'     => $_POST['args']['text']),
                                                           array('setting'  => $_POST['args']['setting'])
                                                           )
                                              ),
                'post_type'     => 'anycontact',
                'post_status'   => 'publish'
               );
  $wp_insert_post = wp_insert_post($post,true);
  if(is_wp_error($wp_insert_post)) {
    die(json_encode(array('error' => array('msg' => $wp_insert_post->get_error_message()))));
  } else {
    
    if(!array_key_exists('id',$_POST['args'])) {
      $output = array('msg'       => __('Ihr Formular wurde erfolgreich angelegt.','anycontact'),
                      'location'  => AnyContact_AdminLink(array('page'    => 'anycontact',
                                                                'action'  => 'edit',
                                                                'id'      => $wp_insert_post,
                                                                'new'     => 1
                                                                )
                                                          )
                      );
    } else {
      $output = array('msg' => __('Ihr Formular wurde erfolgreich bearbeitet.','anycontact'));
    }
    die(json_encode($output));
  }
}

function htmlentities_fix(&$value) {
  $value = htmlentities($value, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8', true);
}
function stripslashes_fix(&$value) {
  $value = stripslashes($value);
}

?>
<?php

function AnyContact_Setup() {
  
  register_post_type('anycontact');
  add_shortcode('anycontact','AnyContact_ShortCode');
  add_action('wp_ajax_pac_form','AnyContact_Ajax');
  add_action('wp_head','AnyContact_FrontendStyles');
  add_action('plugins_loaded','AnyContact_Translation');
  
  if(is_admin()) {
    
    if(strpos($_GET['page'],'anycontact') !== false) {
      
      add_action('admin_head','AnyContact_BackendStyles',999);
      add_action('admin_head','AnyContact_BackendScripts',999);
      add_action('admin_head','AnyContact_GetAjaxURL',999);
      
    }
    
    add_action('admin_menu','AnyContact_Navigation');
  }
}

function AnyContact_Navigation() {
  add_menu_page('Any Contact','Any Contact','manage_options','anycontact','AnyContact_HTML','dashicons-email', 1500);
  add_submenu_page('anycontact','anycontact-overview',__('Übersicht','anycontact'),'manage_options','anycontact','AnyContact_HTML');
  add_submenu_page('anycontact','anycontact-overview',__('Neues Formular','anycontact'),'manage_options','anycontact-new','AnyContact_HTML_NewForm');
  add_submenu_page('anycontact','anycontact-external-link',__('Plugin Website','anycontact'),'manage_options','anycontact-external-link','AnyContact_RedirectToWebsite');
}

function AnyContact_FrontendStyles() {
  wp_enqueue_style('ac-frontend-style',ANYCONTACT_HTTP.'css/anycontact-frontend.css');
}
function AnyContact_BackendStyles() {
  wp_enqueue_style('anycontact-backend-style',ANYCONTACT_HTTP.'css/anycontact-backend.css');
}
function AnyContact_BackendScripts() {
  #wp_enqueue_script('iris');
  wp_enqueue_script('ac-backend-script',ANYCONTACT_HTTP.'js/backend.js',array('jquery','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable'));
}

function AnyContact_GetAjaxURL() {
  ?>
  <script type="text/javascript">var WP_ADMIN_AJAX_URL = '<?= admin_url('admin-ajax.php');?>';</script>
  <?
}

function AnyContact_Translation() {
  load_plugin_textdomain('anycontact', false, dirname(plugin_basename(__FILE__)) . '/language/' );
}


?>
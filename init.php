<?php

/*
Plugin Name: Any Contact
Plugin URI: http://any-contact.com
Description: Free Kontakt Form.
Version: 0.1.1
Author: Patrick Helbing
Author URI: http://patrick-helbing.de
License: GPLv3
Text Domain: anycontact
*/


/*

  Copyright (C) 2014  Patrick Helbing (email: mail at patrick-helbing.de)

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if(!$_SESSION) session_start();
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) die('You are not allowed to call this page directly.<p>You could try starting <a href="http://'.$_SERVER['SERVER_NAME'].'">here</a>.');


/* Konfiguration
 ---------------------------------------------------------------------- */
define('ANYCONTACT_HTTP', plugin_dir_url(__FILE__)); // abschließender slash
define('ANYCONTACT_PHP', plugin_dir_path(__FILE__));

/* Plugin
 ---------------------------------------------------------------------- */
require_once ANYCONTACT_PHP.'functions/uninstall.php';
require_once ANYCONTACT_PHP.'functions/setup.php';
require_once ANYCONTACT_PHP.'functions/shortcode.php';
require_once ANYCONTACT_PHP.'functions/functions.php';

if($pagenow == 'admin.php') {
  require_once ANYCONTACT_PHP.'functions/html.php';
}


/* Install & Uninstall
 ---------------------------------------------------------------------- */
register_deactivation_hook(__FILE__,'AnyContact_Uninstall');

/* Setup
 ---------------------------------------------------------------------- */
add_action('init','AnyContact_Setup');

/* Versionierung
 ---------------------------------------------------------------------- */
add_action('init','AnyContact_Version');

?>
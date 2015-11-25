<?php
/******************************
 * Plugin Name: WP Advanced Importer
 * Description: A plugin that helps to import the data's from a XML file.
 * Version: 2.1.1
 * Author: smackcoders.com
 * Plugin URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 * Author URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 *
 * WP Advanced Importer is a Tool for importing XML for the Wordpress
 * plugin developed by Smackcoders. Copyright (C) 2014 Smackcoders.
 *
 * WP Advanced Importer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Advanced
 * Importer, WP Advanced Importer DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * WP Advanced Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * WP Advanced Importer copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2015. All rights reserved".
 ********************************************************************************/

if ( ! defined( 'ABSPATH' ))  
        exit; // Exit if accessed directly

$get_debug_mode = get_option('wpxmlfreesettings');
$debug_mode = isset($get_debug_mode['debug_mode']) ? $get_debug_mode['debug_mode'] : '';
if($debug_mode != 'enable_debug'){
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

@ob_start();
add_action('init', 'AdvimpStartSession', 1);
add_action('wp_logout', 'AdvimpEndSession');
add_action('wp_login', 'AdvimpEndSession');
/**
 * To Start Session
 */
function AdvimpStartSession() {
	if (!session_id()) {
		session_start();
	}
}
/**
 * To Destroy session
 */
function AdvimpEndSession() {
	session_destroy();
}
if ( empty( $GLOBALS['wp_rewrite'] ) )
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();

define('WP_CONST_ADVANCED_XML_IMP_URL', 'http://www.smackcoders.com');
define('WP_CONST_ADVANCED_XML_IMP_NAME', 'WP Advanced Importer');
define('WP_CONST_ADVANCED_XML_IMP_SLUG', 'wp-advanced-importer');
define('WP_CONST_ADVANCED_XML_IMP_SETTINGS', 'WP Advanced Importer');
define('WP_CONST_ADVANCED_XML_IMP_VERSION', '2.1.1');
define('WP_CONST_ADVANCED_XML_IMP_DIR', WP_PLUGIN_URL . '/' . WP_CONST_ADVANCED_XML_IMP_SLUG . '/');
define('WP_CONST_ADVANCED_XML_IMP_DIRECTORY', plugin_dir_path(__FILE__));
define('WP_XMLIMP_PLUGIN_BASE', WP_CONST_ADVANCED_XML_IMP_DIRECTORY);

if (!class_exists('SkinnyControllerWPAdvImp')) {
	require_once('lib/skinnymvc/controller/SkinnyController.php');
}

add_action('plugins_loaded','load_advimp_lang_files');

function load_advimp_lang_files(){
$xml_importer_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
load_plugin_textdomain( 'wp-advanced-importer', false, $xml_importer_dir);
}

require_once('plugins/class.inlineimages.php');
require_once('includes/WPAdvImporter_includes_helper.php');
if(!class_exists('ConvertXML2Array')){
require_once('includes/XML2Array.php');
}
# Activation & Deactivation 
register_activation_hook(__FILE__, array('WPAdvImporter_includes_helper', 'activate'));
register_deactivation_hook(__FILE__, array('WPAdvImporter_includes_helper', 'deactivate'));

function action_xml_imp_admin_menu() {
	if(!function_exists('wp_get_current_user')) {
		include(ABSPATH . "wp-includes/pluggable.php");
	}
	if(is_multisite()) {
		if ( current_user_can( 'administrator' ) ) { 
			add_menu_page(WP_CONST_ADVANCED_XML_IMP_SETTINGS, WP_CONST_ADVANCED_XML_IMP_NAME, 'manage_options', __FILE__, array('WPAdvImporter_includes_helper', 'output_fd_page'), WP_CONST_ADVANCED_XML_IMP_DIR . "images/icon.png");
		} else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WPAdvImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			if(isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
				add_menu_page(WP_CONST_ADVANCED_XML_IMP_SETTINGS, WP_CONST_ADVANCED_XML_IMP_NAME, '2', __FILE__, array('WPAdvImporter_includes_helper', 'output_fd_page'), WP_CONST_ADVANCED_XML_IMP_DIR . "images/icon.png");
			}
		}
	}
	else {
		if ( current_user_can( 'administrator' ) ) {
                        add_menu_page(WP_CONST_ADVANCED_XML_IMP_SETTINGS, WP_CONST_ADVANCED_XML_IMP_NAME, 'manage_options', __FILE__, array('WPAdvImporter_includes_helper', 'output_fd_page'), WP_CONST_ADVANCED_XML_IMP_DIR . "images/icon.png");
                }
		else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WPAdvImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			$enable_author_import = isset($settings['enable_plugin_access_for_author']) ? $settings['enable_plugin_access_for_author'] : '';
			if($enable_author_import == 'enable_plugin_access_for_author') {
				add_menu_page(WP_CONST_ADVANCED_XML_IMP_SETTINGS, WP_CONST_ADVANCED_XML_IMP_NAME, '2', __FILE__, array('WPAdvImporter_includes_helper', 'output_fd_page'), WP_CONST_ADVANCED_XML_IMP_DIR . "images/icon.png");
			}
		} 
	}
}
add_action("admin_menu" , "action_xml_imp_admin_menu"); 

function action_xml_imp_admin_init() {
	if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'wp-advanced-importer/index.php' || $_REQUEST['page'] == 'page')) {
		
		// Code for use the global wordpress functons in javascript

		wp_register_script('advanced-importer-js', plugins_url('js/advanced-importer.js', __FILE__));
		wp_enqueue_script('advanced-importer-js');
		wp_register_script('jquery-widget', plugins_url('js/jquery.ui.widget.js', __FILE__));
		wp_enqueue_script('jquery-widget');
		wp_register_script('jquery-fileupload', plugins_url('js/jquery.fileupload.js', __FILE__));
		wp_enqueue_script('jquery-fileupload');
		wp_enqueue_style('style', plugins_url('css/style.css', __FILE__));
		wp_enqueue_style('jquery-fileupload', plugins_url('css/jquery.fileupload.css', __FILE__));
		wp_enqueue_style('bootstrap-css', plugins_url('css/bootstrap.css', __FILE__));
		wp_enqueue_style('advanced-importer-css', plugins_url('css/main.css', __FILE__));
		wp_enqueue_style('morris-css', plugins_url('css/morris.css', __FILE__));
		// For chart js
		wp_enqueue_script('dropdown', plugins_url('js/dropdown.js', __FILE__));
		wp_enqueue_script('raphael-min-js', plugins_url('js/raphael-min.js', __FILE__));
		wp_enqueue_script('morris-min-js', plugins_url('js/morris.min.js', __FILE__));
		wp_enqueue_script('data', plugins_url('js/dashchart.js', __FILE__));

	}
}

add_action('admin_init', 'action_xml_imp_admin_init');

// Move Pages above Media
function smackxml_change_menu_order( $menu_order ) {
   return array(
       'index.php',
       'edit.php',
       'edit.php?post_type=page',
       'upload.php',
       'wp-advanced-importer/index.php',
   );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'smackxml_change_menu_order' );

function chartone() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_chartone', 'chartone');

function Advimpuploadfilehandle() {
	require_once("lib/jquery-plugins/uploader.php");
	die();
}
add_action('wp_ajax_Advimpuploadfilehandle','Advimpuploadfilehandle');

function charttwo() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_charttwo', 'charttwo');

function chartthree() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_chartthree', 'chartthree');


function Advimp_roundchart() {
	global $wpdb;
	ob_flush();
	$AdvimpObj = new WPAdvImporter_includes_helper(); 
	$content = "<form name='xmlpiechart'> <div id ='Advimp_pieStats' style='height:250px;'>";
	$AdvimpObj->piechart();
	$content .= "</div></form>"; 
	echo $content;
}

function Advimp_linetwoStats() {
	global $wpdb;
	ob_flush();
	$AdvimpObj = new WPAdvImporter_includes_helper(); 
	$content = "<form name='xmlpiechart'> <div id ='Advimp_lineStats' style='height:250px'>";
	$AdvimpObj->getStatsWithDate();
	$content .= "</div></form>"; 
	echo $content;
}


function wpxmlimporter_add_dashboard_widgets() {
	wp_enqueue_style('morris-Advimpcss', plugins_url('css/morris.css', __FILE__));
	wp_enqueue_script('Advimpdashchart', plugins_url('js/dashchart-widget.js', __FILE__));
	wp_enqueue_script('Advimpraphael-js', plugins_url('js/raphael-min.js', __FILE__));
	wp_enqueue_script('Advimpmorris-js', plugins_url('js/morris.min.js', __FILE__));
	wp_add_dashboard_widget('wpxmlimporter_dashboard_piehart', 'Advanced-XML-Importer-Statistics', 'Advimp_roundchart',$screen = get_current_screen() , 'advanced' ,'high' );
	wp_add_dashboard_widget('wpxmlimporter_dashboard_linechart', 'Advanced-XML-Importer-Activity', 'Advimp_linetwoStats',$screen = get_current_screen(),'advanced','high');
}

add_action('wp_dashboard_setup', 'wpxmlimporter_add_dashboard_widgets');

/**
 * To Process the Import
 */
function xmlimportByRequest() {
	require_once("templates/import.php");
	die;
}
add_action('wp_ajax_xmlimportByRequest', 'xmlimportByRequest');

/**
 *To translate the alert strings
 */
function trans_xmlalert_str(){
if(isset($_POST['alertmsg']))
echo __($_POST['alertmsg'],'wp-advanced-importer');
die();

}
add_action('wp_ajax_trans_xmlalert_str' , 'trans_xmlalert_str');

/**
 *
 */
function addwpcustomfd(){
	require_once("templates/Addcustomfields.php");
	die;
}

add_action('wp_ajax_addwpcustomfd','addwpcustomfd');


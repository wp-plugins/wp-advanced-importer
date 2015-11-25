<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2014 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
$impObj = new WPAdvImporter_includes_helper();
$nonceKey = $impObj->create_nonce_key();
if(! wp_verify_nonce($nonceKey, 'smack_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');
$impCheckobj = CallWPAdvImporterObj::checkSecurity();
if($impCheckobj != 'true')
die($impCheckobj);

$post = $page = $custompost = $users = $eshop = $settings = $support = $dashboard = '';
$active_plugins = get_option('active_plugins');
if(in_array('eshop/eshop.php', $active_plugins)){
	$eshop = true;
}
if(in_array('custom-post-type-ui/custom-post-type-ui.php', $active_plugins)){
        $custompost = true;
}
$impCEM = CallWPAdvImporterObj::getInstance();
$get_settings = array();
$get_settings = $impCEM->getSettings();
$mod = isset($_REQUEST['__module']) ? $_REQUEST['__module'] : '';
$module = $manager = '';
if( is_array($get_settings) && !empty($get_settings) ) {
        foreach ($get_settings as $key) {
                $$key = true;
        }
}
if (isset($_POST['post_csv']) && $_POST['post_csv'] == 'Import') {
	$dashboard = 'activate';
} else {
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
               
		$$action = 'activate';
	} else {
		if (isset($mod) && !empty($mod)) {
                       $module_array =array('post','page','custompost','users','custompost','customtaxonomy','customerreviews','comments','eshop','wpcommerce','woocommerce','marketpress','filemanager','schedulemapping','mappingtemplate' ,'dashboard');
                  foreach($module_array as $val) {
                       if($val = $mod) { 
			   $$mod = 'activate';
                             if( $mod!= 'filemanager' &&  $mod != 'schedulemapping' &&  $mod != 'mappingtemplate' && $mod != 'support' && $mod != 'export' && $mod != 'settings' && $mod != 'dashboard') {
                                $module = 'activate';
                                $manager = 'deactivate';
                                $dashboard = 'deactivate';
                                }
                             else if($mod != 'support' && $mod != 'export' && $mod != 'settings' && $mod != 'dashboard') {
                                $manager = 'activate';
                                $module = 'deactivate';
                                $dashboard = 'deactivate';
                                }
                             else if($mod == 'dashboard') {
                                $manager = 'deactivate';
                                $module = 'deactivate';
                                }
                        }                 
                  }
	        } else {
		      if (!isset($_REQUEST['action'])) {
				$dashboard = 'deactivate';
			}
		}
	}
}
$tab_inc = 1;

$menuHTML = "<nav class='navbar navbar-default' role='navigation'>
   <div>
      <ul class='nav navbar-nav'>
         <li  class = '{$dashboard}' ><a href='admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=dashboard'  >".__('Dashboard','wp-advanced-importer')."</a></li>
         <li class='dropdown {$module} '>
            <a href='#'  data-toggle='dropdown'>
               ". __('Imports','wp-advanced-importer')."
               <b class='caret'></b>
            </a>
            <ul class='dropdown-menu'>
               <li class= '{$post}'><a href= 'admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=post&step=uploadfile'>".__('Post','wp-advanced-importer')."</a></li>
               <li class = '{$page}'><a href='admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=page&step=uploadfile'>". __('Page','wp-advanced-importer')."</a></li>";
if($custompost){
$menuHTML .= "<li class = '{$custompost}'><a href= 'admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=custompost&step=uploadfile'>". __('Custom Post','wp-advanced-importer')."</a></li>";
} 
$menuHTML .= "<li class = '{$users}'><a href='admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=users&step=uploadfile'>". __('Users','wp-advanced-importer')."</a></li>";
if($eshop) {
$menuHTML .= "<li class = '{$eshop}'><a href='admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=eshop&step=uploadfile'>". __('Eshop','wp-advanced-importer')."</a></li>";
}
$menuHTML .= "</ul></li>";
           $menuHTML .= "<li class=  '{$settings}'><a href='admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=settings'  />". __('Settings','wp-advanced-importer')."</a></li>";
	$menuHTML .= "<li class=  '{$support}'><a href='https://smackcoders.freshdesk.com' target=_blank  />". __('Support','wp-advanced-importer')."</a></li>;
	<li ><a href='https://www.wpultimatecsvimporter.com/' target='_blank'>". __('Go Pro Now','wp-simple-csv-importer')."</a></li>
         <li ><a href='http://demo.smackcoders.com/demowpthree/wp-admin/admin.php?page=wp-simple-csv-importer-pro/index.php&__module=dashboard' target='_blank'>" . __('Try Live Demo Now','wp-simple-csv-importer')."</a></li>
      </ul>";
    $plugin_version = get_option('ULTIMATE_CSV_IMP_VERSION');
$menuHTML .= "</div>";
$menuHTML .= "<div class='msg' id = 'showMsg' style = 'display:none;'></div>";
$menuHTML .= "<input type='hidden' id='current_url' name='current_url' value='" . get_admin_url() . "admin.php?page=" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/index.php&__module=" . $_REQUEST['__module'] . "&step=uploadfile'/>";
$menuHTML .= "<input type='hidden' name='checkmodule' id='checkmodule' value='" . $_REQUEST['__module'] . "' />";

$menuHTML .=  "
</nav>";

echo $menuHTML;


<?php
/*********************************************************************************
* Plugin Name: WP Advanced Importer
* Description: A plugin that helps to import the data's from a XML file.
* Version: 3.0
* Author: smackcoders.com
* Author URI: https://www.smackcoders.com
*
* Copyright (C) Smackcoders 2014 - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* You can contact Smackcoders at email address info@smackcoders.com.
*********************************************************************************/
$impObj = new WPAdvImporter_includes_helper();
$nonceKey = $impObj->create_nonce_key();
if(! wp_verify_nonce($nonceKey, 'smack_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');
?>

<style> #ui-datepicker-div { display:none } </style>
<div id = 'notification_wp_csv'> </div>
<?php
 	$impCEM = CallWPAdvImporterObj::getInstance();
     	$impCEM->renderMenu();
	if(isset($_REQUEST['action'])){
		$impCEM->requestedAction($_REQUEST['action'],isset($_REQUEST['step']));
	}
	else if(isset($_REQUEST['__module']))
	{
#		print_r($skinny_content);
		if (isset($_REQUEST['__module'])) {
                        if ( current_user_can( 'administrator' ) ) { //uthor' ) && current_user_can( 'editor' ) ) {
                                print_r($skinny_content);
                        } else {
                                if($_REQUEST['__module'] == 'users' || $_REQUEST['__module'] == 'settings') {
                                        die('<p id="warning-msg" class="alert alert-warning" style="margin-top:50px;">You are not having the permission to access this page. Please, Contact your administrator.</p>');
                                } else {
                                        print_r($skinny_content);
                                }
                        }
                }
	}
	else
	{
		echo "<div align='center' style='width:100%;'> <p class='warnings' style='width:50%;text-align:center;color:red;'>".__('This feature is only available in PRO!.','wp-advanced-importer')."</p></div>";
	}

?>

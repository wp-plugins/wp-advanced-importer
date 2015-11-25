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
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
$noncevar = isset($_POST['postdata']['wpnonce']) ? $_POST['postdata']['wpnonce'] : '';
if(!wp_verify_nonce($noncevar, 'smack_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');

$impCheckobj = CallWPAdvImporterObj::checkSecurity();
if($impCheckobj != 'true')
die($impCheckobj);
require_once(WP_CONST_ADVANCED_XML_IMP_DIRECTORY . 'lib/skinnymvc/core/base/SkinnyBaseActions.php');
require_once(WP_CONST_ADVANCED_XML_IMP_DIRECTORY . 'lib/skinnymvc/core/SkinnyActions.php');
$skinnyObj = new CallWPAdvImporterObj();
$curr_action = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'];
$importedAs = Null;
$inserted_post_count = 0;
$noofrecords = '';
if ($curr_action != 'post' && $curr_action != 'page' && $curr_action != 'custompost') {
	require_once(WP_XMLIMP_PLUGIN_BASE . '/modules/' . $curr_action . '/actions/actions.php');
}
if ($curr_action == 'post' || $curr_action == 'page' || $curr_action == 'custompost') {
	$importObj = new WPAdvImporter_includes_helper();
	if ($curr_action == 'post') {
		$importedAs = 'Post';
	}
	if ($curr_action == 'page') {
		$importedAs = 'Page';
	}
	if ($curr_action == 'custompost') {
		$importedAs = 'Custom Post';
	}
	$importObj->MultiImages = $_POST['postdata']['importinlineimage'];
} elseif ($curr_action == 'eshop') {
	$importObj = new EshopActions();
        $importedAs = 'Eshop';
	$importObj->MultiImages = $_POST['postdata']['importinlineimage'];
} elseif ($curr_action == 'wpcommerce') {
	$importObj = new WpcommerceActions();
} elseif ($curr_action == 'woocommerce') {
	$importObj = new WooCommerceActions();
} elseif ($curr_action == 'users') {
	$importObj = new UsersActions();
	if ($curr_action == 'users') {
		$importedAs = 'Users';
	}
} elseif ($curr_action == 'categories') {
	$importObj = new CategoriesActions();
} elseif ($curr_action == 'customtaxonomy') {
	$importObj = new CustomTaxonomyActions();
} elseif ($curr_action == 'comments') {
	$importObj = new CommentsActions();
	if ($curr_action == 'comments') {
		$importedAs = 'Comments';
	}
}


$limit = $_POST['postdata']['limit'];
$totRecords = $_POST['postdata']['totRecords'];
$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importlimit'] = $_POST['postdata']['importlimit'];
$count = $_POST['postdata']['importlimit'];
$requested_limit = $_POST['postdata']['importlimit'];
$tmpCnt = $_POST['postdata']['tmpcount'];
if ($count < $totRecords) {
	$count = $tmpCnt + $count;
	if ($count > $totRecords) {
		$count = $totRecords;
	}
} else {
	$count = $totRecords;
}
$resultArr = array();
$res2 = array();
$res1 = array();
$get_mapped_array = array();
$mapping_value = '';
$filename = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['uploadedFile'];
$xml_object = new ConvertXML2Array();
        $uploadDir = wp_upload_dir();
        $uploadxml_file = $uploadDir['basedir'] . '/' . 'ultimate_importer' . '/' . $filename;

        $xml_file = fopen($uploadxml_file,'r');
        $xml_read = fread($xml_file , filesize($uploadxml_file));
        fclose($xml_file);

        $xml_arr = $xml_object->createArray($xml_read);
        $xml_data = array();
        $skinnyObj->xml_file_data($xml_arr,$xml_data);
        $reqarr = $skinnyObj->xml_reqarr($xml_data);
        $resultArr = $skinnyObj->xml_importdata($xml_data);
if ($_POST['postdata']['dupTitle']) {
	$importObj->titleDupCheck = $_POST['postdata']['dupTitle'];
}
if ($_POST['postdata']['dupContent']) {
	$importObj->conDupCheck = $_POST['postdata']['dupContent'];
}
$xml_rec_count = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['h2'];
$available_groups = $skinnyObj->get_availgroups($curr_action);

//mapped and unmapped count
foreach ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] as $seskey => $sesval ) {
        foreach($available_groups as $groupKey => $groupVal) {
                $current_mapped = explode($groupVal.'mapping', $seskey);
			if(is_array($current_mapped) && count($current_mapped) == 2) {
				$get_mapped_array['mapping'.$current_mapped[1]] = $sesval;
				if($sesval == '-- Select --' ) {
					$res1[$seskey] = $sesval;
				}
				else {
					if ($sesval != '')
					$res2[] = $sesval;
				}
			} 
	}
}
	$mapped = count($res2);
	$unmapped = count($res1);

	for ($i = $limit; $i < $count; $i++) {
		if ($limit == 0) {
			echo "<div style='margin-left:10px;'>"; echo __(' Total no of records -','wp-advanced-importer'); echo $totRecords; echo ".</div><br>";
			echo "<div style='margin-left:10px;'>"; echo __(' Total no of mapped fields for single record -','wp-advanced-importer'); echo  $mapped;  echo".</div><br>";
			echo "<div style='margin-left:10px;'>"; echo __(' Total no of unmapped fields for a record -','wp-advanced-importer'); echo $unmapped; echo ".</div><br>";
		}
		$colCount = count($resultArr[$i]);
		$_SESSION['SMACK_SKIPPED_RECORDS'] = $i;
		$to_be_import_rec = $resultArr[$i];
		$importObj->detailedLog = array();
		$extracted_image_location = null;
		$importinlineimageoption = null;
		if(isset($_POST['postdata']['inline_image_location'])) {
			$importinlineimageoption = 'imagewithextension';
			$extracted_image_location = $_POST['postdata']['inline_image_location'];
		}
		if($_POST['postdata']['inlineimagehandling'] != 'imagewithextension') {
			$importinlineimageoption = 'imagewithurl';
			$extracted_image_location = $_POST['postdata']['inline_image_location'];
			$sample_inlineimage_url = $_POST['postdata']['inlineimagehandling'];
		}
		$importObj->processDataInWP($to_be_import_rec,$_SESSION['SMACK_MAPPING_SETTINGS_VALUES'], $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'], $i, $extracted_image_location, $importinlineimageoption, $sample_inlineimage_url);
	$logarr = array('post_id','assigned_author','category','tags','postdate','image','poststatus');
	if ($curr_action == 'post' || $curr_action == 'page' || $curr_action == 'custompost' || $curr_action == 'eshop') {
		foreach($importObj->detailedLog as $logKey => $logVal) {
		     if(array_key_exists($logarr[0],$logVal)) {
			foreach($logarr as $logarrkey){
				if(!array_key_exists($logarrkey,$logVal)){
					$logVal[$logarrkey] = '';
				}
			}
			echo "<p style='margin-left:10px;'> " . $logVal['post_id'] . " , " . $logVal['assigned_author'] . " , " . $logVal['category'] . " , " . $logVal['tags'] . " , " . $logVal['postdate'] . " , " . $logVal['image'] . " , " . $logVal['poststatus'];
			if($curr_action == 'eshop') {
				echo " , " . $logVal['SKU'] . ", " . $logVal['verify_here'] . "</p>";
			} else {
				echo " , " . $logVal['verify_here'] . "</p>";
			}  } 
		        else {
                                   echo "<p style='margin-left:10px;'>" . $logVal['verify_here'] . "</p>";
                        }
		}
	}
	else if ($curr_action == 'comments' || $curr_action == 'users') {
		foreach($importObj->detailedLog as $logVal) {
			for ($l = 0; $l < count($logVal); $l++) {
				echo "<p style='margin-left:10px;'> " . $logVal[$l] . "</p>";
			}
		}
	}

	$limit++;
        unset($to_be_import_rec);
}

if ($limit >= $totRecords) {
	$advancemedia = $_POST['postdata']['advance_media'];
	$dir = $skinnyObj->getUploadDirectory();
	$get_inline_imageDir = explode('/', $extracted_image_location);
	$explodedCount = count($get_inline_imageDir);
	$inline_image_dirname = $get_inline_imageDir[$explodedCount - 1];
	$uploadDir = $skinnyObj->getUploadDirectory('inlineimages');
	$inline_images_dir = $uploadDir . '/smack_inline_images/' . $inline_image_dirname;
	if($advancemedia == 'true'){
		$skinnyObj->deletefileafterprocesscomplete($inline_images_dir);
	}
	$skinnyObj->deletefileafterprocesscomplete($dir);
}
if ($importObj->insPostCount != 0 || $importObj->dupPostCount != 0 || $importObj->updatedPostCount != 0) {
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = 0;
	}
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = 0;
	}
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = 0;
	}
	if (!isset($importObj->capturedId)) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	}
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] + $importObj->insPostCount;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] + $importObj->dupPostCount;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] + $importObj->updatedPostCount;
	if (isset($importObj->capturedId)) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = $importObj->capturedId;
	}
}
//if ($totRecords <= ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] + $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] + $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'])) {
$inserted_post_count = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'];
if ($inserted_post_count != 0) {
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	}
		$importObj->addStatusLog($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'], $importedAs);
		$importObj->addPieChartEntry($importedAs, $inserted_post_count);
		$inserted_post_count = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId']);
}
if ($limit == $totRecords) {
	echo "<br><div style='margin-left:10px; color:green;'>";
	 echo __('Import successfully completed!.','wp-advanced-importer');
	echo "</div>";
}
foreach ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] as $key => $value) {
	for ($j = 0; $j < $xml_rec_count; $j++) {
		if ($key == 'mapping' . $j) {
			$mapArr[$j] = $value;
		}
	}
}

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
	$filename = isset($_POST['filename']) ? $_POST['filename'] : '';
	$count = isset($_POST['corecount']) ? $_POST['corecount'] : '';
        $impobj = new WPAdvImporter_includes_helper();
	$xml_object = new ConvertXML2Array();
	$uploadDir = wp_upload_dir();
	$uploadxml_file = $uploadDir['basedir'] . '/' . 'ultimate_importer' . '/' . $filename;
	
	$xml_file = fopen($uploadxml_file,'r');
        $xml_read = fread($xml_file , filesize($uploadxml_file));
        fclose($xml_file);
	
        $xml_arr = $xml_object->createArray($xml_read);
        $xml_data = array();
        $impobj->xml_file_data($xml_arr,$xml_data);
        $reqarr = $impobj->xml_reqarr($xml_data);
        $getrecords = $impobj->xml_importdata($xml_data);
	$key = '';

        $returndata = "<tr><td class='left_align' style='width:53.5%; padding-left:150px;'><input type='text' name='addcorefieldname$count' id = 'addcorefieldname$count'/></td>";
	$returndata .= "<td class='left_align'> <select name='addcoremapping$count' id='addcoremapping$count' class='uiButton'>";
		$returndata .= $impobj->xml_mappingbox($getrecords,$key,$count);
	$returndata .= "</select></td>";
	$returndata .= "<td></td><td></td></tr>";
        print_r($returndata);die;
?>

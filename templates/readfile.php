<?php
if(!defined('ABSPATH'))
{
        die('Exit if accessed directly');
}

require_once('../includes/WPAdvImporter_includes_helper.php');
#require_once('../../../../wp-load.php');
//$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
//require_once( $parse_uri[0] . 'wp-load.php' );
$impObj = CallWPAdvImporterObj::getInstance(); //print_r($impObj);//die;
$filename=$_POST['file_name'];
$result = $impObj->xml_file_data($filename, 'xml');
print_r(json_encode($result[$_REQUEST['record_no']]));
?>

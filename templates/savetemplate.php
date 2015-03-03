<?php
require_once('../config/settings.php');
require_once('../lib/skinnymvc/controller/SkinnyController.php');

//require_once('../../../../wp-load.php');
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
if($_REQUEST['stepstatus']){ 
	foreach($_REQUEST as $reqKey => $reqVal){
		if($reqKey == 'h2'){
			$getArr = explode(',',$reqVal);
			foreach($getArr as $v){
				$mapArr[] = $v;
			}
			$_SESSION['SMACK_IMP_OPTIONS']['mapArr'] = $mapArr;
		}elseif($reqKey != 'h2'){
			$_SESSION['SMACK_IMP_OPTIONS'][$reqKey] = $reqVal;
		}
	}
	print_r(json_encode($_REQUEST));
}
die;
?>

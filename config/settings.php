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

class SkinnySettings { public static $CONFIG = array(

"project name"    => WP_CONST_ADVANCED_XML_IMP_NAME,
"debug"           => false,
"preload model"   => true,  //true = all model classes will be loaded with each request;
                            //false = model classes will be loaded only if explicitly required (use require_once)

"unauthenticated default module" => "default", //set this to where you want unauthenticated users redirected.
"unauthenticated default action" => "index",
);}
    

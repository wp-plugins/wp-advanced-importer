<?php
/*****************************************************************************
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

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class WPAdvImporter_includes_helper {

	public function __construct()
	{
		$this->getKeyVals();
	}

	// @var string CSV upload directory name
	public $uploadDir = 'ultimate_importer';

	// @var boolean post title check
	public $titleDupCheck = false;

	// @var boolean content title check
	public $conDupCheck = false;

	// @var boolean for post flag
	public $postFlag = true;

	// @var int duplicate post count
	public $dupPostCount = 0;

	// @var int inserted post count
	public $insPostCount = 0;

	// @var int no post author count
	public $noPostAuthCount = 0;

	// @var int updated post count
	public $updatedPostCount=0;

	// @var array wp field keys
	public $keys = array();

	// @var Multi images
	public $MultiImages = false;

	// @var array for default columns
	public $defCols = array(
			'post_title' => null,
			'post_content' => null,
			'post_excerpt' => null,
			'post_date' => null,
			'post_name' => null,
			'post_tag' => null,
			'post_category' => null,
			'post_author' => null,
			'featured_image' => null,
			'post_parent' => 0,
			'post_status' => 0,
			'menu_order'  => 0,
			'post_format' => 0,
			'wp_page_template' => null,
			);

	// @var array CSV headers
	public $headers = array();
	
	public $xmlheaders  = array();

        public $xmlarray = array();

	public $capturedId=0;

	public $detailedLog = array();

	/* getImportDataConfiguration */
	public function getImportDataConfiguration(){
		if($_REQUEST['__module'] == 'custompost')
			$importDataConfig = "<div class='importstatus'id='importallwithps_div' style= 'padding-top:40px;'>";
		else
		$importDataConfig = "<div class='importstatus'id='importallwithps_div'>";
                $importDataConfig .="<table><tr><td>
			<label id='importalign'>". __('Import with post status','wp-advanced-importer') ."</label><span class='mandatory'> *</span></td><td>
			<div style='float:left;margin-right:10px;'>
			<select name='importallwithps' id='importallwithps' style ='width:150px;'onChange='selectpoststatus();' >
			<option value='0'>". __('Status as in XML','wp-advanced-importer')."</option>
			<option value='1'>".__('Publish','wp-advanced-importer')."</option>
			<option value='2'>".__('Sticky','wp-advanced-importer')."</option>
			<option value='3'>".__('Private','wp-advanced-importer')."</option>
			<option value='6'>".__('Protected','wp-advanced-importer')."</option>
			<option value='4'>".__('Draft','wp-advanced-importer')."</option>
			<option value='5'>".__('Pending','wp-advanced-importer')."</option>
			</select></div>
			<div style='float:right;'>
			<a href='#' class='tooltip'>
			<img src='".WP_CONST_ADVANCED_XML_IMP_DIR."images/help.png' />
			<span class='tooltipPostStatus'>
			<img class='callout' src='".WP_CONST_ADVANCED_XML_IMP_DIR."images/callout.gif' />
			". __('Select the status for the post  imported, if not defined within your xml .E.g.publish','wp-advanced-importer')."
			<img src='". WP_CONST_ADVANCED_XML_IMP_DIR."images/help.png' style='margin-top: 6px;float:right;' />
			</span></a> </div>
			</td></tr><tr><td>
			<div id='globalpassword_label' class='globalpassword' style='display:none;padding-left:5px'><label>". __('Password','wp-advanced-importer')."</label><span class='mandatory'> *</span></div></td><td>
			<div id='globalpassword_text' class='globalpassword' style='display:none;'><input type = 'text' id='globalpassword_txt' name='globalpassword_txt' style ='width:150px;' placeholder=".__('Password for all post','wp-advanced-importer')."></div></td></tr></table>
			</div>";
		return $importDataConfig;
	}

	/**
	 * Get upload directory
	 */
	public function getUploadDirectory($check = 'plugin_uploads')
	{
		$upload_dir = wp_upload_dir();
		if($check == 'plugin_uploads'){
			return $upload_dir ['basedir'] . "/" . $this->uploadDir;
		}else{
			return $upload_dir ['basedir'];
		}
	}

	/**
	 *	generate help tooltip
	 *	@param string $content ** content to show on tooltip **
	 *	@return string $html ** generated HTML **
	 **/
	public function generatehelp($content, $mapping_style = NULL)
	{
		$html = '<div style = "'.$mapping_style.'"> <a href="#" class="tooltip">
			<img src="'.WP_CONST_ADVANCED_XML_IMP_DIR.'images/help.png" />
			<span class="tooltipPostStatus">
			<img class="callout" src="'.WP_CONST_ADVANCED_XML_IMP_DIR.'images/callout.gif" />
			'.$content.'
			<img src="'.WP_CONST_ADVANCED_XML_IMP_DIR.'images/help.png" style="margin-top: 6px;float:right;" />
			</span> </a> </div>';
		return $html;
	}

	public static function output_fd_page()
	{
		$get_pluginData = get_plugin_data(plugin_dir_path(__FILE__) . '../index.php');
		$plugin_version = get_option('ULTIMATE_CSV_IMPORTER_UPGRADE_FREE_VERSION');
		if(!$plugin_version) {
			$plugin_version = get_option('ULTIMATE_CSV_IMP_FREE_VERSION');
		}
		if ($get_pluginData['Version'] == '3.6' && $plugin_version == '') {
			if (file_exists(WP_CONST_ADVANCED_XML_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php')) {
				require_once(WP_CONST_ADVANCED_XML_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php');
				die();  
			}
		}
		else if (!isset($_REQUEST['__module']))
		{
			if (!isset($_REQUEST['__module'])) {
				wp_redirect(get_admin_url() . 'admin.php?page=' . WP_CONST_ADVANCED_XML_IMP_SLUG . '/index.php&__module=dashboard');

			}
		}
		require_once(WP_CONST_ADVANCED_XML_IMP_DIRECTORY.'config/settings.php');
		require_once(WP_CONST_ADVANCED_XML_IMP_DIRECTORY.'lib/skinnymvc/controller/SkinnyController.php');

		$c = new SkinnyControllerWPAdvImp;
		$c->main();
	}

	public function getSettings(){
		return get_option('wpxmlfreesettings');
	}

	public function renderMenu()
	{
		include(plugin_dir_path(__FILE__) . '../templates/menu.php');
	}

	public function requestedAction($action,$step){
		$actions = array('dashboard','settings','help','users','comments','eshop','wpcommerce','woocommerce','categories','customtaxonomy','export', 'mappingtemplate');
		if(!in_array($action,$actions)){
			include(plugin_dir_path(__FILE__) . '../templates/view.php');
		}else{
			include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/actions/actions.php');
			include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/templates/view.php');
		}
	}

	/**
	 * Move CSV to the upload directory
	 */
	public function move_file()
	{
		if ($_FILES ["xml_import"] ["error"] == 0) {
			$tmp_name = $_FILES ["xml_import"] ["tmp_name"];
			$this->xmlFileName = $_FILES ["xml_import"] ["name"];
			move_uploaded_file($tmp_name, $this->getUploadDirectory() . "/$this->xmlFileName");
		}
	}

	/**
	 * Check upload dirctory permission
	 */
	function checkUploadDirPermission()
	{
		$this->getUploadDirectory();
		$upload_dir = wp_upload_dir();
		if (!is_dir($upload_dir ['basedir'])) {
			print " <div style='font-size:16px;margin-left:20px;margin-top:25px;'>" . $this->t("UPLOAD_PERMISSION_ERROR") . "
				</div><br/>
				<div style='margin-left:20px;'>
				<form class='add:the-list: validate' method='post' action=''>
				<input type='submit' class='button-primary' name='Import Again' value='" . $this->t("IMPORT_AGAIN") . "'/>
				</form>
				</div>";
			$this->freeze();
		} else {
			if (!is_dir($this->getUploadDirectory())) {
				wp_mkdir_p($this->getUploadDirectory());
			}
		}
	}


	/**
	 * Get field colum keys
	 */
	function getKeyVals()
	{
		$cust_fields='';
		$acf_field=array();
		$wpxmlfreesettings = array();
		global $wpdb;
		$active_plugins = get_option('active_plugins');
		$limit = ( int )apply_filters('postmeta_form_limit', 150);
		$this->keys = $wpdb->get_col("SELECT meta_key FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%'
				ORDER BY meta_key
				LIMIT $limit");

		foreach ($this->keys as $val) {
			$this->defCols ["CF: " . $val] = $val;
		}
				if(in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)){
					$seo_custoFields =array('SEO: keywords','SEO: description','SEO: title','SEO: noindex','SEO: nofollow','SEO: titleatr','SEO: menulabel','SEO: disable','SEO: disable_analytics','SEO: noodp','SEO: noydir');
					foreach($seo_custoFields as $val)
						$this->defCols[$val]=$val;
				}
	}

	/**
	 * Function converts CSV data to formatted array.
	 * @param $file CSV input filename
	 * @param $delim delimiter for the CSV
	 * @return array formatted CSV output as array
	 */

	function xml_file_data($arr, &$out, $prefix='') {
                    $filterarr = array();
                    $prefix = $prefix ? $prefix . '-->' : '';
                    foreach($arr as $k => $value) {
                        $key =  $prefix . $k;
                        if(is_array($value)) {
                            $this->xml_file_data($value, $out, $key);
                        }
                        else {
                            $this->xmlarray[$key] = $value;
                            $out[$key] = $value;
                        }
                    }
        }

	function xml_reqarr($arr){
                $reqarr = array();
                $newarr = array();
                $arrindex = array();
                $count = 0;
                $flag1 = false;
                $flag2 = false;
                foreach($arr as $k => $val){
                        $flag1 = false;
                        $i = 0;
                        $newarr = explode("-->",$k);
                        foreach($newarr as $key => $v){
                                if(is_numeric($v)){
                                        $flag1 = true;
                                        $arrindex[$i] = $v;
                                        $i++;
                                }
                        }
                        if($flag1){
                                $flag2 = false;
                                foreach($arrindex as $value){
                                        if($value != 0){
                                                $flag2 = true;
                                                break;
                                        }
                                }
                                if($flag2){
                                        continue;
                                }
                                else {
                                        $reqarr[$k] = $val;
                                }
                        }
                        else{
                                $reqarr[$k] = $val;
                        }

                }
		 // Replace the array index
                foreach($reqarr as $key => $val){
                        $parent_arr = strpos($key,'-->');
                        if($parent_arr === false){
                                continue;
                        }
                        else {
                                $key = str_replace('-->0','',$key);
                                $filtered_arr[$key] = $val;
                        }
                }
                return $filtered_arr;
        }

        function is_nestedkey($xmlkey){
                $nestedkey = false;
                $arrkey = explode("-->",$xmlkey);
                foreach($arrkey as $newkey => $newvalue){
                        if(is_numeric($newvalue)){
                                $nestedkey = true;
                                break;
                        }
                }
                return $nestedkey;
        }
	
	function xml_importdata($arr){
                $flag1 = false;
                $i = 0;
                $j = 0;
                $import_arr = array();
                $import_arrdata = array();
                $import_strindex = array();
                $result_arr = array();
                $replace_result_arr = array();
                foreach($arr as $k => $val){
                        $flag1 = false;
                        $newarr = explode("-->",$k);
                        foreach($newarr as $key => $v){
                                if(is_numeric($v)){
                                        $flag1  = true;
                                        $i = $v;
                                        break;
                                }
                        }
                        if($flag1){
                                $import_arrdata[$i][$k] = $val;
                                $import_arr[$i]= $import_arrdata;
                        }
                        else{
                                $import_strindex[$k] = $val;
                        }
                }
                if(!empty($import_arrdata)){
                        foreach($import_arrdata as $key => $value){
                                $result_arr[$j] = array_merge($import_strindex,$value);
                                $j++;
                        }
                }else{
                        foreach($import_strindex as $key => $value){
                            $result_arr[0][$key]  = $value;
                        }
                }

		foreach($result_arr as $key => $value){
                        foreach($value as $hkey => $hvalue){
                                $count = 0 ;
                                $newarr = explode("-->",$hkey);
                                foreach($newarr as $newkey => $newval){
                                        if(is_numeric($newval)){
                                                $count++;
                                                if($count > 1){
                                                        break;
                                                }
                                                        unset($newarr[$newkey]);
                                        }
                                }
                                        $hkey = implode("-->",$newarr);
                                $this->xmlheaders[] = $hkey;
                                $replace_result_arr[$key][$hkey] = $hvalue;
                        }

                }
                return $replace_result_arr;
        }

        function xml_mappingbox($getrecords,$def_headers,$Field_count){
		$xml_selectbox = '';
		$xml_selectbox .= "<option class = 'addrow_option' id='select'> -- Select --</option>";
                $xml_selectbox .= "<optgroup class = 'addrow_optgrp' style='font-size:14px;line-height:15px;' label = 'Parent Nodes'></optgroup>";
                $parent_arr = array();
                $nested_arr = array();
                $newarr = array();
                foreach($getrecords as $xkey => $xvalue){
                        foreach($xvalue as $xmlkey => $xmlheader){
                                $nestedkey = $this->is_nestedkey($xmlkey);
                                if($nestedkey){
                                        if(!array_key_exists($xmlkey,$nested_arr))
                                                $nested_arr[$xmlkey] = $xmlheader;
                                }
				else {
                                        if(!array_key_exists($xmlkey,$nested_arr))
                                                $parent_arr[$xmlkey] = $xmlheader;
                                }
                        }
                }
                foreach($parent_arr as $xmlkey => $xmlheader){
                        $newarr = explode("-->",$xmlkey);
                        $auto_header = strtolower(trim($newarr[count($newarr)-1]));
                        if($auto_header == strtolower($def_headers)){
                                $xml_selectbox .= "<option class = 'addrow_option' value = '$xmlkey' selected>$xmlkey</option>";?>
                        <?php }else{
                                $xml_selectbox .= "<option class = 'addrow_option' value = '$xmlkey'>$xmlkey</option>";
                        }
                }
                $xml_selectbox .= "<optgroup class = 'addrow_optgrp' style='font-size:14px;line-height:15px;' label = 'Nested Nodes'></optgroup>";
                if(!empty($nested_arr)){
                        foreach($nested_arr as $xmlkey => $xmlheader){
                                $newarr = explode("-->",$xmlkey);
                                $auto_header = strtolower(trim($newarr[count($newarr)-1]));
                                if($auto_header == strtolower($def_headers)){
                                        $xml_selectbox .= "<option class = 'addrow_option' value = '$xmlkey' selected>$xmlkey</option>";?>
                 		<?php }else{
                                        $xml_selectbox .= "<option class = 'addrow_option' value = '$xmlkey'>$xmlkey</option>";
                                }
                        }
                }
                return $xml_selectbox;
        }
 
	function get_availgroups($module) {
			$groups = array();
			if ($module == 'post' || $module == 'page' || $module == 'custompost' || $module == 'eshop') {
				$groups = array('','core','addcore','seo');
			}
			 if ($module == 'users') {
                                $groups = array('');
                        }
		return $groups;
	}

	/**
	 * Manage duplicates
	 *
	 * @param string type = (title|content), string content
	 * @return boolean
	 */
	function duplicateChecks($type = 'title', $text, $gettype, $currentLimit, $postTitle)
	{
		global $wpdb;
		if ($type == 'content') {
			$htmlDecode = html_entity_decode($text);
			$strippedText = strip_tags($htmlDecode);
			$contentLength = strlen($strippedText);
			$allPosts_count = $wpdb->get_results("SELECT COUNT(ID) as count FROM $wpdb->posts WHERE post_type = \"{$gettype}\" and post_status IN('publish','future','draft','pending','private')");
			$allPosts_count = $allPosts_count[0]->count;
			$allPosts = $wpdb->get_results("SELECT ID,post_title,post_date,post_content FROM $wpdb->posts WHERE post_type = \"{$gettype}\" and post_status IN('publish','future','draft','pending','private')");
			foreach ($allPosts as $allPost) {
				$htmlDecodePCont = html_entity_decode($allPost->post_content);
				$strippedTextPCont = strip_tags($htmlDecodePCont);
				similar_text($strippedTextPCont, $strippedText, $p);
				if ($p == 100) {
					$this->dupPostCount++;
					$this->detailedLog[$currentLimit]['verify_here'] = "Post-content Already Exists. It can't be imported.";
					return false;
				}
			}
			return true;
		} else if ($type == 'title') {
			$post_exist = $wpdb->get_results("select ID from " . $wpdb->posts . " where post_title = \"{$text}\" and post_type = \"{$gettype}\" and post_status in('publish','future','draft','pending','private')");
			 if (!(count($post_exist) == 0 && ($text != null || $text != ''))) {
                                $this->dupPostCount++;
                                $this->detailedLog[$currentLimit]['verify_here'] = "Post-title Already Exists. It can't be imported.";
                                return false;
                        }
                return true;
                } else if ($type == 'title && content') {
                        $post_exist = $wpdb->get_results("select ID from " . $wpdb->posts . " where post_title = \"{$postTitle}\" and post_content = \"{$text}\"  and post_status IN('publish','future','draft','pending','private')");
                        if (!(count($post_exist) == 0 && ($text != null || $text != ''))) {
                                $this->dupPostCount++;
                                $this->detailedLog[$currentLimit]['verify_here'] = "Post-title and post-content Already Exists. It can't be imported.";
                                return false;
                         }
                        return true;
                }
        }


	/**
	 * function to fetch the featured image from remote URL
	 * @param $f_img
	 * @param $fimg_path
	 * @param $fimg_name
	 * @param $post_slug_value
	 * @param $currentLimit
	 * @param string $logObj
	 */
	public static function get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit = null, $logObj = ""){
		$f_img = str_replace( " ","%20", $f_img );
		if($fimg_path!="" && $fimg_path){
			$fimg_path = $fimg_path . "/" . $fimg_name;
		}
		$ch = curl_init ($f_img);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$rawdata = curl_exec($ch);
		if(strpos($rawdata, 'Not Found') != 0) {
			$rawdata = false;
		}
		if ($rawdata == false) {
			if ($logObj == '') {
				$this->detailedLog[$currentLimit]['image'] = "<b>" .__('Image','wp-advanced-importer')." -</b> " .__('host not resolved','wp-advanced-importer');
			} else {
				$logObj->detailedLog[$currentLimit]['image'] = "<b>" . __('Image','wp-advanced-importer')." -</b>" .__('host not resolved','wp-advanced-importer');
			}
		} else {
			if (file_exists($fimg_path)) {
				unlink($fimg_path);
			}
			$fp = fopen($fimg_path, 'x');
			fwrite($fp, $rawdata);
			fclose($fp);
			$logObj->detailedLog[$currentLimit]['image'] = "<b>". __('Image','wp-advanced-importer')." -</b>" . $fimg_name;
		}
		curl_close($ch);
		return $fimg_name;
	}

	/**
	 * function to map the xml file and process it
	 *
	 * @return boolean
	 */
	function processDataInWP($data_rows,$ret_array,$session_arr,$currentLimit,$extractedimagelocation,$importinlineimageoption,$sample_inlineimage_url = null) {
		global $wpdb;
		$post_id = '';
		$new_post = array();
		$smack_taxo = array();
		$custom_array = array();
		$seo_custom_array= array();		
		$imported_feature_img = array();
		$headr_count = $ret_array['h2'];
		  for ($i = 0; $i <= $ret_array['basic_count']; $i++) {
			if(array_key_exists('corefieldname' . $i,$ret_array)){
				if($ret_array['coremapping' . $i] != '-- Select --' && $ret_array['coremapping'.$i] != ''){
					$mappedindex = str_replace('CF: ','',$ret_array['corefieldname'.$i]);
					if(array_key_exists($ret_array['coremapping'.$i],$data_rows)){
					$new_post[$mappedindex] = $data_rows[$ret_array['coremapping'.$i]];
					//$custom_array[$ret_array['coremapping'.$i]] = $data_rows[$mappedindex];
					}
				}
			}
			else if (array_key_exists('seofieldname' .$i,$ret_array)){
				if($ret_array['seomapping' . $i] != '-- Select --' && $ret_array['seomapping'.$i] != ''){
					$mappedindex = str_replace('SEO: ','',$ret_array['seofieldname'.$i]);
					if(array_key_exists($ret_array['seomapping'.$i],$data_rows)){
						//$new_post[$mappedindex] = $data_rows[$ret_array['seomapping'.$i]];
						$seo_custom_array[$mappedindex] = $data_rows[$ret_array['seomapping'.$i]];
					}
				}
			} 	
			else if (array_key_exists('addcorefieldname' . $i,$ret_array)){
				if($ret_array['addcoremapping' . $i] != '-- Select --' && $ret_array['addcoremapping'.$i] != ''){
					if(array_key_exists($ret_array['addcoremapping'.$i],$data_rows)){
						$custom_array[$ret_array['addcorefieldname'.$i]] = $data_rows[$ret_array['addcoremapping'.$i]];
					}
				}
					
			}
			else if (array_key_exists('mapping' . $i, $ret_array)) { 
				if($ret_array ['mapping' . $i] != '-- Select --'){
					if(array_key_exists($ret_array['mapping'.$i],$data_rows)){
						$new_post[$ret_array['fieldname'.$i]] = $data_rows[$ret_array['mapping' . $i]];
					}
				}
			}
		}
		for ($inc = 0; $inc < count($data_rows); $inc++) {
			foreach ($this->keys as $k => $v) {
				if (array_key_exists($v, $new_post)) {
					$custom_array [$v] = $new_post [$v];
				}
			}
		}
		if(is_array( $new_post )){
			foreach ($new_post as $ckey => $cval) {
				$this->postFlag = true;
				$taxo = get_taxonomies();
				foreach ($taxo as $taxokey => $taxovalue) {
					if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format') {
						if ($taxokey == $ckey) {
							$smack_taxo [$ckey] = $new_post [$ckey];
						}
					}
				}

				$taxo_check = 0;
				if (!isset($smack_taxo[$ckey])) {
					$smack_taxo [$ckey] = null;
					$taxo_check = 1;
				}
				if ($ckey != 'post_category' && $ckey != 'post_tag' && $ckey != 'featured_image' && $ckey != $smack_taxo [$ckey] && $ckey != 'wp_page_template') {	
					if ($taxo_check == 1) {
						unset($smack_taxo[$ckey]);
						$taxo_check = 0;
					}
					if (array_key_exists($ckey, $custom_array)) {
						$darray [$ckey] = $new_post [$ckey];
					} else {
						if (array_key_exists($ckey, $smack_taxo)) {
							$data_array[$ckey] = null;
						} else {
							$data_array[$ckey] = $new_post [$ckey];
						}
					}
				} else {
					switch ($ckey) {
						case 'post_tag' :
							$tags [$ckey] = $new_post [$ckey];
							break;
						case 'post_category' :
							$categories [$ckey] = $new_post [$ckey];
							break;
						case 'wp_page_template' :
							$custom_array['_wp_page_template'] = $new_post [$ckey];
							break;
						case 'featured_image' :
							require_once(ABSPATH . "wp-includes/pluggable.php");
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$dir = wp_upload_dir();
							$get_media_settings = get_option('uploads_use_yearmonth_folders');
							if($get_media_settings == 1){
								$dirname = date('Y') . '/' . date('m');
								$full_path = $dir ['basedir'] . '/' . $dirname;
								$baseurl = $dir ['baseurl'] . '/' . $dirname;
							}else{
								$full_path = $dir ['basedir'];
								$baseurl = $dir ['baseurl'];
							}

							$f_img = $new_post [$ckey];
							$fimg_path = $full_path;

							$fimg_name = @basename($f_img);
							$featured_image = $fimg_name;
							$fimg_name = strtolower(str_replace(' ','-',$fimg_name));
							$fimg_name =  preg_replace('/[^a-zA-Z0-9._\s]/', '', $fimg_name);
							$fimg_name = urlencode($fimg_name);

							$parseURL = parse_url($f_img);
							$path_parts = pathinfo($f_img);
							if(!isset($path_parts['extension']))
								$fimg_name = $fimg_name . '.jpg';
							//else
							//	$fimg_name = $fimg_name.'.'.$path_parts['extension'];
								
							$f_img_slug = '';
							$f_img_slug = strtolower(str_replace('','-',$f_img_slug));
							$f_img_slug =  preg_replace('/[^a-zA-Z0-9._\s]/', '',$f_img_slug);

							$post_slug_value = strtolower($f_img_slug);
							if(array_key_exists('extension',$path_parts)){
							//$fimg_name = wp_unique_filename($fimg_path, $fimg_name, $path_parts['extension']);
							}
							$this->get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit, $this);
							$filepath = $fimg_path ."/" . $fimg_name;

							if(@getimagesize($filepath)){
								$img = wp_get_image_editor($filepath);
								$file ['guid'] = $baseurl."/".$fimg_name;
								$file ['post_title'] = $fimg_name;
								$file ['post_content'] = '';
								$file ['post_status'] = 'attachment';
							}
							else	{
								$file = false;
							}
							break;
					}
				}
			}
		}

		if($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] != 'custompost'){
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'];
		}else{
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['custompostlist'];
		}
		if ($this->titleDupCheck == 'true')
			$this->postFlag = $this->duplicateChecks('title', $data_array ['post_title'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);

		if ($this->conDupCheck == 'true' && $this->postFlag){
			if(isset($data_array['post_content']) && $data_array['post_content'] != '')
			$this->postFlag = $this->duplicateChecks('content', $data_array ['post_content'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);
		}

		if ($this->titleDupCheck == 'true' && $this->conDupCheck == 'true') 
                         $this->postFlag = $this->duplicateChecks('title && content', $data_array ['post_content'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);
                


		if ($this->postFlag) {
			unset ($sticky);
			if (empty($data_array['post_status']))
				$data_array['post_status'] = null;

			if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'] != 0)
				$data_array['post_status'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'];

			switch ($data_array ['post_status']) {
				case 1 :
					$data_array['post_status'] = 'publish';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
					break;
				case 2 :
					if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] == 'post')
                                        {
                                        $data_array['post_status'] = 'publish';
                                        $sticky = true;
                                        $this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('sticky','wp-advanced-importer');
                                        }
                                        else
                                        {
                                        $data_array['post_status'] = 'publish';
                                        $this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
                                        }
                                        break;
				case 3 :
					$data_array ['post_status'] = 'private';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('private','wp-advanced-importer');
					break;
				case 4 :
					$data_array ['post_status'] = 'draft';
					$data_array['post_date_gmt'] = $data_array['post_date'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('draft','wp-advanced-importer');
					break;
				case 5 :
					$data_array ['post_status'] = 'pending';
					$data_array['post_date_gmt'] = $data_array['post_date'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('pending','wp-advanced-importer');
					break;
				default :
					$poststatus_pwd = $data_array['post_status'];
					$poststatus = $data_array['post_status'] = strtolower($data_array['post_status']);
					if ($data_array['post_status'] == 'pending') {
                                                $data_array['post_status'] = 'pending';
						$data_array['post_date_gmt'] = $data_array['post_date'];
                                                $this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('pending','wp-advanced-importer');
                                        }
                                        if ($data_array['post_status'] == 'draft') {
                                                $data_array['post_status'] = 'draft';
						$data_array['post_date_gmt'] = $data_array['post_date'];
                                                $this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('draft','wp-advanced-importer');
                                        }
                                        if ($data_array['post_status'] == 'publish') {
                                                $data_array['post_status'] = 'publish';
                                                $this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
                                        }
                                        if ($data_array['post_status'] == 'private') {
                                                $data_array['post_status'] = 'private';
                                                $this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('private','wp-advanced-importer');
                                        }

					if ($data_array['post_status'] != 'publish' && $data_array['post_status'] != 'private' && $data_array['post_status'] != 'draft' && $data_array['post_status'] != 'pending' && $data_array['post_status'] != 'sticky') {
						$stripPSF = strpos($data_array['post_status'], '{');
						if ($stripPSF === 0) {
							$poststatus = substr($poststatus_pwd, 1);
							$stripPSL = substr($poststatus, -1);
							if ($stripPSL == '}') {
								$postpwd = substr($poststatus_pwd, 1, -1);
								$data_array['post_status'] = 'publish';
								$data_array ['post_password'] = $postpwd;
								if (strlen($postpwd) !=0)
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('protected with password','wp-advanced-importer');
								else
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
							} else {
								$data_array['post_status'] = 'publish';
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
							}
						} else {
							$data_array['post_status'] = 'publish';
							$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
						}
					}
					if ($data_array['post_status'] == 'sticky') {
						if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] == 'post') {
                                                	$data_array['post_status'] = 'publish';
                                                	$sticky = true;
                                                	$this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('sticky','wp-advanced-importer');
                                            	}
                                            	else {     	
                                                	$data_array['post_status'] = 'publish';
                                                	$this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('publish','wp-advanced-importer');
                                            	}

					}
			}
			// Post Format Options

			if(isset($data_array ['post_format'])) {
				$post_format = 0;
				switch ($data_array ['post_format']) {
					case 1 :
						$post_format = 'post-format-aside';
						break;
					case 2 :
						$post_format = 'post-format-image';
						break;
					case 3 :
						$post_format = 'post-format-video';
						break;
					case 4 :
						$post_format = 'post-format-audio';
						break;
					case 5 :
						$post_format = 'post-format-quote';
						break;
					case 6 :
						$post_format = 'post-format-link';
						break;
					case 7 :
						$post_format = 'post-format-gallery';
						break;
					default :
						if($data_array['post_format']=='post-format-aside'){
					  		$post_format='post-format-aside';
					  		break;   
						 }	
					 	if($data_array['post_format']=='post-format-image'){
					  		$post_format='post-format-image'; 
					  		break;
					 	}
					 	if($data_array['post_format']=='post-format-video'){
					 		$post_format='post-format-video'; 
					 		break;
					 	}
					 	if($data_array['post_format']=='post-format-audio'){
					 		$post_format='post-format-audio'; 
					 		break;
					 	}
					 	if($data_array['post_format']=='post-format-quote'){
					 		$post_format='post-format-quote'; 
					 		break;
					 	}
					 	if($data_array['post_format']=='post-format-link'){
					 		$post_format='post-format-link'; 
					 		break;
					 	}
					 	if($data_array['post_format']=='post-format-gallery'){
					 		$post_format='post-format-gallery'; 
					 		break;
					 	}
						$post_format = 0;

				}
			}


			// Author name/id update
			if(isset($data_array ['post_author'])){
				$authorLen = strlen($data_array ['post_author']);
				$postuserid = $data_array ['post_author'];
				$checkpostuserid = intval($data_array ['post_author']);
				$postAuthorLen = strlen($checkpostuserid);
				$postauthor = array();

				if ($authorLen == $postAuthorLen) {
					$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = \"{$postuserid}\"");
					if(empty($postauthor) || !$postauthor[0]->ID) { // If user name are numeric Ex: 1300001
						$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where user_login = \"{$postuserid}\"");
					}
				} else {
					$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where user_login = \"{$postuserid}\"");
				}

				if (empty($postauthor) || !$postauthor[0]->ID) {
					$data_array ['post_author'] = 1;
					$admindet = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = 1");
					$this->detailedLog[$currentLimit]['assigned_author'] = "<b>" .__('Author - not found (assigned to','wp-advanced-importer')." </b>" . $admindet[0]->user_login . ")";
					$this->noPostAuthCount++;
				} else {
					$data_array ['post_author'] = $postauthor [0]->ID;
					$this->detailedLog[$currentLimit]['assigned_author'] = "<b>".__('Author','wp-advanced-importer')." - </b>" . $postauthor[0]->user_login;
				}
			}
			else{
				$data_array ['post_author'] = 1;
				$admindet = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = 1");
				$this->detailedLog[$currentLimit]['assigned_author'] = "<b>".__('Author - not found (assigned to','wp-advanced-importer')." </b>" . $admindet[0]->user_login . ")";
				$this->noPostAuthCount++;
			}

			// Date format post
			if(!isset($data_array['post_date']))
				$data_array['post_date'] = date('Y-m-d');
			if ($data_array ['post_date'] == null){
				$data_array ['post_date'] = date('Y-m-d');
				$this->detailedLog[$currentLimit]['postdate'] = "<b>".__('Date','wp-advanced-importer')." - </b>" . $data_array ['post_date'];
			}else{
				$data_array ['post_date'] = date('Y-m-d H:i:s', strtotime($data_array ['post_date']));
				$this->detailedLog[$currentLimit]['postdate'] = "<b>".__('Date','wp-advanced-importer')." - </b>" . $data_array ['post_date'];
			}
			if(isset($data_array ['post_slug'])){
				$data_array ['post_name'] = $data_array ['post_slug'];
			}

			//add global password
			if($data_array){
				if($ret_array['importallwithps'] == 6){
					$data_array['post_status'] = 'publish';
					$data_array['post_password'] = $ret_array['globalpassword_txt'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>".__('Status','wp-advanced-importer')." - </b>".__('protected with password','wp-advanced-importer') ;
				}
			}
			if ($data_array) {
				if($this->MultiImages == 'true') { // Inline image import feature by fredrick marks
					$inlineImagesObj = new WPxmlImporter_inlineImages();
					$postid = wp_insert_post($data_array);
					$post_id = $inlineImagesObj->importwithInlineImages($postid, $currentLimit, $data_array, $this, $importinlineimageoption, $extractedimagelocation, $sample_inlineimage_url);
				//	$inline_shortcode = $inlineImagesObj->capture_all_shortcodes($data_array['post_content']);
				} else {
					$post_id = wp_insert_post($data_array);
					$this->detailedLog[$currentLimit]['post_id'] = "<b>".__('Created Post_ID','wp-advanced-importer')." - </b>" . $post_id . " - success";
				}
			}
			unset($postauthor);
			if ($post_id) {
				$uploaded_file_name=$session_arr['uploadedFile'];
				$real_file_name = $session_arr['uploaded_xml_name'];
				$action = $data_array['post_type'];
				$get_imported_feature_image = array();
				$get_imported_feature_image = get_option('IMPORTED_FEATURE_IMAGES');
				if(is_array($get_imported_feature_image)){
					$imported_feature_img = array_merge($get_imported_feature_image, $imported_feature_img);
				}
				else{
					$imported_feature_img = $imported_feature_img;
				}
				update_option('IMPORTED_FEATURE_IMAGES', $imported_feature_img);
				$created_records[$action][] = $post_id;
				if($action == 'post'){
					$imported_as = 'Post';
				}
				if($action == 'page'){
					$imported_as = 'Page';
				}
				if($action != 'post' && $action != 'page'){
					$imported_as = 'Custom Post';
				}
				$keyword = $action;
				$this->insPostCount++;
				if (isset($sticky) && $sticky)
					stick_post($post_id);

				if (!empty ($custom_array)) {
					foreach ($custom_array as $custom_key => $custom_value) {
						update_post_meta($post_id, $custom_key, $custom_value);
					}
				}


				// Import post formats added by fredrick marks
				if(isset($post_format)) {
					wp_set_object_terms($post_id, $post_format, 'post_format');

				}          
				//Import SEO Values     
				if(!empty($seo_custom_array)){
					$this->importSEOfields($seo_custom_array,$post_id);
				}

				// Create custom taxonomy to post
				if (!empty ($smack_taxo)) {
					foreach ($smack_taxo as $taxo_key => $taxo_value) {
						if (!empty($taxo_value)) {
							$split_line = explode('|', $taxo_value);
							wp_set_object_terms($post_id, $split_line, $taxo_key);
						}
					}
				}

				// Create/Add tags to post
				if (!empty ($tags)) {
					$this->detailedLog[$currentLimit]['tags'] = "";
					foreach ($tags as $tag_key => $tag_value) {
						$this->detailedLog[$currentLimit]['tags'] .= $tag_value . "|";
						wp_set_post_tags($post_id, $tag_value);
					}
					$this->detailedLog[$currentLimit]['tags'] = "<b>".__('Tags','wp-advanced-importer')." - </b>" .substr($this->detailedLog[$currentLimit]['tags'], 0, -1);
				}

				// Create/Add category to post
				if (!empty ($categories)) {
					$this->detailedLog[$currentLimit]['category'] = "";
					$assigned_categories = array();
					$split_cate = explode('|', $categories ['post_category']);
					foreach ($split_cate as $key => $val) {
						if (is_numeric($val)) {
							$split_cate[$key] = 'uncategorized';
							$assigned_categories['uncategorized'] = 'uncategorized';
						}
						$assigned_categories[$val] = $val;
					}
					foreach($assigned_categories as $cateKey => $cateVal) {
						$this->detailedLog[$currentLimit]['category'] .= $cateKey . "|";
					}
					$this->detailedLog[$currentLimit]['category'] = "<b>".__('Category','wp-advanced-importer')." - </b>" .substr($this->detailedLog[$currentLimit]['category'], 0, -1);
					wp_set_object_terms($post_id, $split_cate, 'category');
				}
				// Add featured image
				if (!empty ($file)) {
					$wp_upload_dir = wp_upload_dir();
					$attachment = array(
							'guid' => $file ['guid'],
							'post_mime_type' => 'image/jpeg',
							'post_title' => preg_replace('/[^a-zA-Z0-9._\s]/', '', @basename($file ['guid'])),
							'post_content' => '',
							'post_status' => 'inherit'
							);
					if($get_media_settings == 1){
						$generate_attachment = $dirname . '/' .  $fimg_name;
					}else{
						$generate_attachment = $fimg_name;
					}
					$uploadedImage = $wp_upload_dir['path'] . '/' . $fimg_name;
					$existing_attachment = array();
                                        $query = $wpdb->get_results("select post_title from $wpdb->posts where post_type = 'attachment' and post_mime_type = 'image/jpeg'");
                                        foreach($query as $key){
                                       $existing_attachment[] = $key->post_title;
                                         }
                                        if(!in_array($fimg_name ,$existing_attachment)){
                                        $attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
                                        $attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
                                        wp_update_attachment_metadata($attach_id, $attach_data);
                                         }else{
                                        $query2 = $wpdb->get_results("select ID from $wpdb->posts where post_title = '$fimg_name' and post_type = 'attachment'");
                                        foreach($query2 as $key2){
                                               $attach_id = $key2->ID;
                                                }
                                        }
					set_post_thumbnail($post_id, $attach_id);
				}
			}
			else{
				$skippedRecords[] = $_SESSION['SMACK_SKIPPED_RECORDS'];
			}
		
		$this->detailedLog[$currentLimit]['verify_here'] = "<b>Verify Here -</b> <a href='" . get_permalink( $post_id ) . "' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $data_array['post_title'] ) ) . "' rel='permalink' target='_blank'>" . __( 'Web View','wp-advanced-importer' ) . "</a> | <a href='" . get_edit_post_link( $post_id, true ) . "' title='" . esc_attr( __( 'Edit this item','wp-advanced-importer' ) ) . "' target='_blank'>" . __( 'Admin View','wp-advanced-importer' ) . "</a>";
		}
		unset($data_array);
	}

	// Create Data base for Statistic chart
	public static function activate() {
		if (!defined('PDO::ATTR_DRIVER_NAME')) {
			echo __("Make sure you have enable the PDO extensions in your environment before activate the plugin!",'wp-advanced-importer');
			die;
		}
		global $wpdb;
		$sql1="CREATE TABLE `smackxml_pie_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) DEFAULT NULL,
			`value` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`)
				) ENGINE=InnoDB;";

		$sql2="CREATE TABLE `smackxml_line_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`month` varchar(60) DEFAULT NULL,
			`year` varchar(60) DEFAULT NULL,
			`imported_type` varchar(60) DEFAULT NULL, 
			`imported_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`inserted` int(11) DEFAULT NULL,

			PRIMARY KEY (`id`)
				) ENGINE=InnoDB;";
		$wpdb->query($sql1);
		$wpdb->query($sql2);  
		$importedTypes = array('Post','Page','Custom Post','Comments','Users','Eshop');
		foreach($importedTypes as $importedType){
			$querycheck = $wpdb->get_results("select *from smackxml_pie_log where type = \"{$importedType}\"");
			if (count($querycheck) == 0){
				$sql4 = "insert into smackxml_pie_log (type,value) values(\"$importedType\",0)";
				$wpdb->query($sql4);
			}
		}
		$saveSettings = array('savesettings' => 'Save', 'post' => 'post', 'page' => 'page', 'drop_table' => 'off', 'debug_mode' => 'disable_debug', 'export_delimiter' => ';',);
		update_option('wpxmlfreesettings', $saveSettings);
	}

	//Drop Database While Deactivate plugin
	public function deactivate() {
		global $wpdb;
		$sql1 = "DROP TABLE smackxml_pie_log;";
		$wpdb->query($sql1);

		$sql2 = "DROP TABLE smackxml_line_log;";
		$wpdb->query($sql2);

		update_option('wpxmlfreesettings','');
	}
	public function addPieChartEntry($imported_as, $count) {
		//add total counts
		global $wpdb;
		$getTypeID = $wpdb->get_results("select * from smackxml_pie_log where type = '$imported_as'");
		if(count($getTypeID) == 0)
			$wpdb->insert('smackxml_pie_log',array('type'=>$imported_as,'value'=>$count));       
		else
			$wpdb->update('smackxml_pie_log', array('value' =>$getTypeID[0]->value+$count), array('id'=>$getTypeID[0]->id));
	}
	function addStatusLog($inserted,$imported_as){
		global $wpdb;
		$today = date('Y-m-d h:i:s');
		$mon = date("M",strtotime($today));
		$year = date("Y",strtotime($today));
		$wpdb->insert('smackxml_line_log', array('month'=>$mon,'year'=>$year,'imported_type'=>$imported_as,'imported_on'=>date('Y-m-d h:i:s'), 'inserted'=>$inserted ));
	}

	/**
	 * Function for importing the all in seo data 
	 * Feature added by Fredrick on version3.5.4
	 */
	function importSEOfields($array,$postId)
	{
		$seo_opt = get_option('active_plugins');
		if(in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $seo_opt)){
			if(isset($array['keywords'])) {    $custom_array['_aioseop_keywords'] = $array['keywords']; } 
			if(isset($array['description'])) { $custom_array['_aioseop_description'] = $array['description']; }
			if(isset($array['title'])) {       $custom_array['_aioseop_title'] = $array['title']; }
			if(isset($array['noindex'])) {     $custom_array['_aioseop_noindex'] = $array['noindex']; }
			if(isset($array['nofollow'])) {    $custom_array['_aioseop_nofollow'] = $array['nofollow']; }
			if(isset($array['titleatr'])) {    $custom_array['_aioseop_titleatr'] = $array['titleatr']; }
			if(isset($array['menulabel'])) {   $custom_array['_aioseop_menulabel'] = $array['menulabel']; }
			if(isset($array['disable'])) {     $custom_array['_aioseop_disable'] = $array['disable']; }
			if(isset($array['disable_analytics'])) { $custom_array['_aioseop_disable_analytics'] = $array['disable_analytics']; }
			if(isset($array['noodp'])) { $custom_array['_aioseop_noodp'] = $array['noodp']; }
			if(isset($array['noydir'])) { $custom_array['_aioseop_noydir'] = $array['noydir']; }
		}
		if (! empty ( $custom_array )) {
			foreach ( $custom_array as $custom_key => $custom_value ) {
				update_post_meta ( $postId, $custom_key, $custom_value );
			}
		}

	}//importSEOfields ends

	/**
	 * Delete uploaded file after import process
	 */
	function deletefileafterprocesscomplete($uploadDir) {
		$files = array_diff(scandir($uploadDir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$uploadDir/$file")) ? rmdir("$uploadDir/$file") : unlink("$uploadDir/$file"); 
		} 
	}

	// Function convert string to hash_key
	public function convert_string2hash_key($value) {
		$file_name = hash_hmac('md5', "$value", 'secret');
		return $file_name;
	}

	// Function to show common notice for PRO Feature
	public function common_notice_for_pro_feature() {
		return "<p align='center'> <label style='color:red;'> ".__('This feature is only available in Pro!','wp-advanced-importer')." </label> <a href='http://www.smackcoders.com/wp-advanced-importer-pro.html' target='_blank'>". __('Go Pro Now','wp-advanced-importer')."</a> </p>";
	}

	// Function for common footer
	public function common_footer_for_other_plugin_promotions(){
		$content = '<div class="accordion-inner">
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">Social All in One Bot</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/google-seo-author-snippet-plugin/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">Google SEO Author Snippet</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">WP Advanced Importer</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">WP Sugar</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">WP Zoho crm Sync</a></label>

			<label class="plugintags"><a href="http://www.smackcoders.com/wp-advanced-importer-pro.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">WP Ultimate CSV Importer Pro</a></label>
			<label class="plugintags"><a href="http://www.smackcoders.com/wordpress-sugar-integration-automated-multi-web-forms-generator-pro.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">WordPress Sugar Pro</a></label>
			<div style="position:relative;float:right;"><a href="http://www.smackcoders.com/"><img width=80 src="http://www.smackcoders.com/skin/frontend/default/megashop/images/logo.png?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" /></a></div>
			</div>';
		echo $content;
	}

	// Function for social sharing
	public function importer_social_profile_share() {
		$urlCurrentPage = "http://www.smackcoders.com/wp-advanced-importer.html";
		$fbimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/images/facebook.png";
		$googleimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/images/googleplus.png";
		$linkedimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/images/linkedin.png";
		$twitimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ADVANCED_XML_IMP_SLUG . "/images/twitter.png";
		$strPageTitle = 'WP Ultimate CSV Importer';
		$linked_in_username = 'smackcoders';

		//Facebook
		$htmlShareButtons = '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_facebook_share" href="http://www.facebook.com/sharer.php?u=' . $urlCurrentPage  . '" target="_blank">';
		$htmlShareButtons .= '<img title="Facebook" class="wpcsv" src="' . $fbimgsrc . '" alt="Facebook" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Google Plus
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_google_share" href="https://plus.google.com/share?url=' . $urlCurrentPage  . '" target="_blank" >';
		$htmlShareButtons .= '<img title="Google+" class="wpcsv" src="' . $googleimgsrc . '" alt="Google+" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Linked in
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_linkedin_share" class="wpcsv_share_link" href="http://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($urlCurrentPage)  . '&title='.urlencode($strPageTitle).'&source='.$linked_in_username.'" target="_blank" >';
		$htmlShareButtons .= '<img title="LinkedIn" class="wpcsv" src="' . $linkedimgsrc . '" alt="LinkedIn" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Twitter
		$username = "smackcoders";
		// format the URL into friendly code
		$twitterShareText = urlencode(html_entity_decode($strPageTitle . ' ', ENT_COMPAT, 'UTF-8'));
		// twitter share link
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_twitter_share" href="http://twitter.com/share?url=' . $urlCurrentPage .'&via='.$username.'&related='.$username.'&text=' . $twitterShareText . '" target="_blank">';
		$htmlShareButtons .= '<img title="Twitter" class="wpcsv" src="' . $twitimgsrc . '" alt="Twitter" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';
		echo $htmlShareButtons;
	}

	public function common_footer() {
		$get_pluginData = get_plugin_data(plugin_dir_path( __FILE__ ).'../index.php');
		$footer = '';
		$footer .= '<div style="padding:10px;">';
		$footer .= '<label class="plugintags"></label>';
		$footer .= '<label class="plugintags"><a href="http://www.wpultimatecsvimporter.com" target="_blank">'.__("Home",'wp-advanced-importer').'</a></label><label class="plugintags"><a href="http://wiki.smackcoders.com/WP_Ultimate_CSV_Importer?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">'. __("Wiki",'wp-advanced-importer').'</a></label>
			<label class="plugintags"><a href="http://www.wpultimatecsvimporter.com" target="_blank">' .__('Tutorials','wp-advanced-importer').'</a></label>                  	<label class="plugintags"><a href="http://wiki.smackcoders.com/WP_Ultimate_CSV_Importer_Videos?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">'. __("Videos",'wp-advanced-importer').'</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/wordpress-ultimate-csv-importer-csv-sample-files-and-updates.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">'. __("Sample Files",'wp-advanced-importer').'</a></label>';
		$footer .= '</div>';
		$footer .= '<div style="padding:10px;margin-bottom:20px;">';
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'settings')
			$footer .= "<div style='float:right;margin-top:-49px;'><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ADVANCED_XML_IMP_SLUG."/index.php&__module=settings'>". $this->reduceStringLength(__('Click here to Enable any disabled module','wp-advanced-importer'),'Click here to Enable any disabled module')."</a></div>";
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'settings') {
			$footer .= "<div style='float:right;margin-top:-48px;'><span style='margin-right:20px;'><a class='label label-info' href='http://wordpress.org/plugins/wp-advanced-importer/developers/'>".$this->reduceStringLength(__('Get Old Versions','wp-advanced-importer'),'Get Old Versions')."</a></span><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ADVANCED_XML_IMP_SLUG."/index.php&__module=support'>".$this->reduceStringLength(__('Click here to Get some useful links','wp-advanced-importer'),'Click here to Get some useful links')."</a></div>";	
			$footer .= "<div style='float:right;margin-right:15px;'> </span> ".__('Current Version','wp-advanced-importer').":".$get_pluginData['Version']." </div>";
		}
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'support' && $_REQUEST['__module'] != 'settings') {
			$footer .= "<div style='float:right;margin-right:225px;margin-top:-48px;'><span style='margin-right:20px;'> <a class='label label-info' href='http://wordpress.org/plugins/wp-advanced-importer/developers/'>".$this->reduceStringLength(__('Get Old Versions','wp-advanced-importer'),'Get Old Versions')."</a></span><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ADVANCED_XML_IMP_SLUG."/index.php&__module=support'>". $this->reduceStringLength(__('Click here to Get some useful links','wp-advanced-importer'),'Click here to Get some useful links')."</a></div>";	
			$footer .= "<div style='float:right;margin-right:15px;'> ".'Current Version'.": ".$get_pluginData['Version']." </div>";
		}
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'support'){
			$footer .= "<div style='float:right;margin-right:15px;'><span style='margin-right:20px;'>".__('Current Version','wp-advanced-importer').": ".$get_pluginData['Version']." </span><span style='margin-right:10px;'><a class='label label-info' href='http://wordpress.org/plugins/wp-advanced-importer/developers/'>".$this->reduceStringLength(__('Get Old Versions','wp-advanced-importer'),'Get Old Versions')."</a></span></div>";
		}
		$footer .= '</div>';
		$footer .= '<div style="float:right;margin-right:15px;margin-top:-10px;"> <label>Plugin By <a href="http://www.smackcoders.com"> Smackcoders</a></label> </div>';
		//echo $footer;
	}

	function smack_xml_import_method() {

		$smack_xml_import_method = '<div class="importfileoption">

			<div align="center" style="text-align:left;margin-top:-33px;">
			<div id="boxmethod1" class="method1">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="uploadfilefromcomputer" onclick="choose_import_method(this.id);" checked/></span> <span class="header-text" id="importopt">' . __('From Computer','wp-advanced-importer') . '</span> </label> <br>
			<!-- The fileinput-button span is used to style the file input field as button -->
			<div id="method1" style="display:block;height:40px;">
			<span class="btn btn-success fileinput-button">
			<span>' . __('Browse','wp-advanced-importer') . '</span>
			<input id="fileupload" type="file" name="files[]" multiple>
			<a href="#" id="zip_process" style = "display:none">  Click Here To Process Zip </a>
			</span>';
		// The global progress bar 
		$smack_xml_import_method .= '<span style="padding-top:10px;">
			<div id="progress" class="progress">
			<div class="progress-bar progress-bar-success"></div>
			<div align="center" id="helpnotify" style="width:100%;"><p class="msgborder" style="color:green;">' . __('You can also drag and drop files here','wp-advanced-importer') . '</div>
			</div>
			</span>
			</div>
			</div>
			<div  style = "opacity: 0.3;background-color: ghostwhite;">
			<div id="boxmethod2" class="method2">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="dwnldftpfile"  /></span> <span class="header-text" id="importopt">' . __('From FTP','wp-advanced-importer') . '</span> </label> <img src="' . WP_CONTENT_URL . '/plugins/' . WP_CONST_ADVANCED_XML_IMP_SLUG . '/images/pro_icon.gif" title="PRO Feature" /> <br>
			</div>
			<div id="boxmethod3" class="method3">
			<label> <span class="radio-icon"><input type="radio" name="importmethod" id="dwnldextrfile"  /></span> <span class="header-text" id="importopt">' . __('From URL','wp-advanced-importer') . '</span></label> <img src="' . WP_CONTENT_URL . '/plugins/' . WP_CONST_ADVANCED_XML_IMP_SLUG . '/images/pro_icon.gif" title="PRO Feature" /> <br>
			</div>
			<div id="boxmethod4" class="method4">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="useuploadedfile"  /></span> <span class="header-text" id="importopt">' . __('From Already Uploaded','wp-advanced-importer') . '</span></label> <img src="' . WP_CONTENT_URL . '/plugins/' . WP_CONST_ADVANCED_XML_IMP_SLUG . '/images/pro_icon.gif" title="PRO Feature" /> <br>
			</div>
			</div>

			</div>
			</div>';
			$curr_module = $_REQUEST['__module'];
			if($curr_module == 'post' || $curr_module == 'page' || $curr_module == 'custompost' || $curr_module == 'eshop') {
			$smack_xml_import_method .= '<div class="media_handling" style ="padding-left:2px;">
			<span class="advancemediahandling"> <label id="importalign"> <input type="checkbox" name="advance_media_handling" id="advance_media_handling"   onclick = "filezipopen();" /> '.__("Advance Media Handling",'wp-advanced-importer').' </label> </span>
			<span id = "filezipup" style ="display:none;">
			<span class="advancemediahandling" style="padding-left:30px;"> <input type="file" name="inlineimages" id="inlineimages" onchange ="checkextension(this.value);" /> </span>
			</span>
			</div>';
			}

		return $smack_xml_import_method;
	}
	function helpnotes()
	{
		$smackhelpnotes = '<span style="position:absolute;margin-top:6px;margin-left:15px;">
			<a href="" class="tooltip">
			<img src="'. WP_CONST_ADVANCED_XML_IMP_DIR .'images/help.png" />
			<span class="tooltipPostStatus">
			<img class="callout" src="'. WP_CONST_ADVANCED_XML_IMP_DIR . 'images/callout.gif" />
			Default value is 1. You can give any value based on your environment configuration.
			<img src="'. WP_CONST_ADVANCED_XML_IMP_DIR .'images/help.png" style="margin-top: 6px;float:right;" />
			</span>
			</a>
			</span>';
		return $smackhelpnotes;
	}

	function inlinehelpnotes()
        {
                $inlinehelpnotes = '<span style="position:absolute;margin-top:6px;margin-left:15px;">
                        <a href="" class="tooltip">
                        <img src="'. WP_CONST_ADVANCED_XML_IMP_DIR .'images/help.png" />
                        <span class="tooltipPostStatus">
                        <img class="callout" src="'. WP_CONST_ADVANCED_XML_IMP_DIR . 'images/callout.gif" />
			Check it for importing the inline images. 
                        </span>
                        </a>
                        </span>';
                return $inlinehelpnotes;
        }



	function create_nonce_key(){
		return wp_create_nonce('smack_nonce');
	}
        function reduceStringLength($convert_str,$checktext){
                        if ($checktext == 'Enable' || $checktext == 'Disable' || $checktext == 'Mapping'){
                                if( strlen($convert_str) > 7)
                            $convert_str = substr($convert_str, 0, 5) . '..';
                        }
                        else if ($checktext == 'caticonEnable' || $checktext == 'caticonDisable') {
                                if( strlen($convert_str) > 7)
                            $convert_str = substr($convert_str,0,4) . '..';
                        }
                        else if ($checktext == 'Enabled' || $checktext == 'Disabled'){
                                if(  strlen($convert_str) > 8)
                            $convert_str = substr($convert_str,0,3) . '..';
                        }
                        else if ($checktext == 'Check All' || $checktext == 'Uncheck All'){
                                if(strlen($convert_str) > 12)
                            $convert_str = substr($convert_str,0,10) . '..';
                        }
                        else if($checktext == 'Yes' || $checktext == 'No' ){
                                if( strlen($convert_str) > 4)
                            $convert_str = substr($convert_str,0,2) . '..';
                        }
                        else if ($checktext == 'Next' && strlen($convert_str) > 5)
                            $convert_str = substr($convert_str,0,4) . '..';
			else if ($checktext == 'Get Old Versions' && strlen($convert_str) > 17)
				$convert_str = substr($convert_str,0,14) . '..';
			else if ($checktext == 'Click here to Get some useful links' && strlen($convert_str) > 37)
				$convert_str = substr($convert_str,0,33) . '..';
			else if ($checktext == 'Click here to Enable any disabled module' && strlen($convert_str) > 42)
				$convert_str = substr($convert_str,0,40) . '..';
			else if ($checktext == 'Add Custom Field' && strlen($convert_str) > 17)
                                $convert_str = substr($convert_str,0,17) . '..';
                        return $convert_str;
        }  

                public function getStatsWithDate() {
                        global $wpdb;
                        $returnArray = array();
                        $plot =array();
                        $get_imptype = array('Post','Page','Comments','Custom Post','Users','Eshop');
                        $mon_year = array(11 => 'Nov',10 =>'Oct',9 =>'Sep',8 =>'Aug',7 => 'Jul', 6 => 'Jun', 5 => 'May' ,4 => 'Apr', 3 => 'Mar', 2 => 'Feb', 1 => 'Jan',12 => 'Dec');
                        $today = date("Y-m-d H:i:s");
                        for($i = 0; $i <= 11; $i++) {
                                $month[] = date("M", strtotime( $today." -$i months"));
                                $year[]  = date("Y", strtotime( $today." -$i months"));
                        }
                        foreach($month as $mkey) {
                               foreach($year as $ykey) {
                                $mon_num = array_search($mkey,$mon_year);
                                $postCount = $pageCount = $customCount = $userCount = $shopCount = 0;
                                $j = 0;
                                $plot = $wpdb->get_results("select inserted,imported_type from smackxml_line_log where imported_type in ('Post','Page','Comments','Custom Post','Users','Eshop') and  month = '{$mkey}' and year = '{$ykey}'");
                                foreach($plot as $pkey) {
                                        switch ($pkey->imported_type) {
                                                case 'Post':
                                                        $postCount += $pkey->inserted;
                                                        break;
                                                case 'Page':
                                                        $pageCount += $pkey->inserted;
                                                        break;
                                                case 'Custom Post':
                                                        $customCount += $pkey->inserted;
                                                        break;
                                                case 'Users':
                                                        $userCount  += $pkey->inserted;
                                                        break;
                                                case 'Eshop':
                                                        $shopCount  += $pkey->inserted;
                                                        break;
                                                default:
                                                        break;
                                        }
                                        $returnArray[$j] = array('year' => ''.$ykey.'-'.$mon_num.'','post' => (int)$postCount,'page' => (int)$pageCount,'custompost' => (int)$customCount, 'users' => (int)$userCount, 'eshop' => (int)$shopCount);
                                $j++;
                                }
                               }
                        }
                        if(empty($returnArray)){
                                $returnArray[$j] = array('year' => ''.$ykey.'-'.$mon_num.'','post' => 0,'page' => 0,'custompost' => 0, 'users' => 0, 'eshop' => 0);

                        }
                        $reqarr = array();
                       $reqarr[0] = $returnArray[count($returnArray) - 1];
                       return json_encode($reqarr);
                }
                public function piechart()
                {
                        ob_clean();
                        global $wpdb;
                        $blog_id = 1;
                $returnArray = array();
                    $imptype = array('Post','Page','Comments','Custom Post','Users','Eshop');
                        $i = 0;
                   foreach($imptype as $imp) {
                         $OverviewDetails = $wpdb->get_results("select *  from smackxml_pie_log where type = '{$imp}'  and value != 0");
                        foreach($OverviewDetails as  $overview){
                               //$returnArray[$i][0] = $overview->type;
                                //$returnArray[$i][1] = (int)$overview->value;
                                $returnArray[$i] = array(
                                                'label'   => ''.$overview->type.'',
                                                'value'   => ''.(int)$overview->value.'',

                                );
                                $i++;
                        }

            }
                                if(empty($returnArray ) ){
                               $returnArray['label']  = 'No Imports Yet' ;
                                      }
                        return json_encode($returnArray);
                }
}	

class CallWPAdvImporterObj extends WPAdvImporter_includes_helper
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) )  //or if( is_null(self::$_instance) ) or if( self::$_instance == null )
			self::$_instance = new WPAdvImporter_includes_helper();
		return self::$_instance;
	}
	public static function checkSecurity(){
                $msg = 'You are not allowed to do this operation! Please contact your admin';
                if(!function_exists('session_status')){
                        if(session_id() == '')
                                return $msg;
			else
				return 'true';
                }
                else if(session_status() != PHP_SESSION_ACTIVE)
                        return $msg;
                else if(!defined('ABSPATH'))
                        return $msg;
                else if (php_sapi_name() == "cli") 
                        return $msg;
                else
                        return 'true';
        }
}



<?php
/*********************************************************************************
 * WP Advanced Importer is a Tool for importing XML for the Wordpress
 * plugin developed by Smackcoder. Copyright (C) 2013 Smackcoders.
 *
 * WP Advanced Importer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3 as
 * published by the Free Software Foundation with the addition of the following
 * permission added to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE
 * COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Advanced Importer,
 * WP Advanced Importer DISCLAIMS THE WARRANTY OF NON INFRINGEMENT OF THIRD
 * PARTY RIGHTS.
 *
 * WP Advanced Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the WordPress ultimate
 * XML Importer copyright notice. If the display of the logo is not reasonably feasible
 * for technical reasons, the Appropriate Legal Notices must display the words
 * "Copyright Smackcoders. 2013. All rights reserved".
 ********************************************************************************/
require_once("WXR_importer.php");
require_once ("SmackWpXMLHandler.php");
class SmackXMLImpCE extends SmackWpXMLHandler
{

    // @var string XML upload directory name
    public $uploadDir = 'xml_importer';

    // @var boolean post title check
    public $titleDupCheck = false;

    // @var boolean content title check
    public $conDupCheck = false;

    // @var string delimiter
    public $delim = ",";

    // @var array delilimters supported by XML importer
    public $delim_avail = array(
        ',',
        ';'
    );

    // @var array wp field keys
    public $keys = array();

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
	'post_type'	=> 'post',
        'featured_image' => null,
        'post_parent' => 0,
        'post_status' => 0
    );

    // @var array XML headers
    public $headers = array();

    // @var boolean for post flag
    public $postFlag = true;

    // @var int duplicate post count
    public $dupPostCount = 0;

    // @var int inserted post count
    public $insPostCount = 0;

    // @var int no post author count
    public $noPostAuthCount = 0;

    // @var string XML file name
    public $xmlFileName;

    // @var string XML file extension
    public $xmlFileExtn = '';

    /**
     */
    function __construct()
    {
        $this->getKeyVals();
    }

    /**
     * Manage duplicates
     *
     * @param string type = (title|content), string content
     * @return boolean
     */
    function duplicateChecks($type = 'title', $text, $gettype)
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
                    return false;
                }
            }
            return true;
        } else if ($type == 'title') {
            $post_exist = $wpdb->get_results("select ID from " . $wpdb->posts . " where post_title = \"{$text}\" and post_type = \"{$gettype}\" and post_status in('publish','future','draft','pending','private')");
            if (count($post_exist) == 0 && ($text != null || $text != ''))
                return true;
        }
        $this->dupPostCount++;
        return false;
    }

    /**
     * Get upload directory
     */
    function getUploadDirectory()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir ['basedir'] . "/" . $this->uploadDir;
    }
    /**
     * Move XML to the upload directory
     */
    function move_file()
    {
        if ($_FILES ["xml_import"] ["error"] == 0) {
            $tmp_name = $_FILES ["xml_import"] ["tmp_name"];
            $this->xmlFileName = $_FILES ["xml_import"] ["name"];
            move_uploaded_file($tmp_name, $this->getUploadDirectory() . "/$this->xmlFileName");
        }
    }

    /**
     * Remove XML file
     */
    function fileDelete($filepath, $filename)
    {
        if (file_exists($filepath . $filename) && $filename != "" && $filename != "n/a") {
            unlink($filepath . $filename);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get field colum keys
     */
    function getKeyVals()
    {
        global $wpdb;
        $limit = ( int )apply_filters('postmeta_form_limit', 30);
        $this->keys = $wpdb->get_col("SELECT meta_key FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%'
				ORDER BY meta_key
				LIMIT $limit");

        foreach ($this->keys as $val) {
            $this->defCols ["CF: " . $val] = $val;
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
     * Function converts XML data to formatted array.
     *
     * @param $file XML
     *            input filename
     * @param $delim delimiter
     *            for the XML
     *@param $fileExtension
     *            for Extension of file (xml)
     * @return array formatted output as array
     */
    function import_file_data($file, $delim, $fileExtension, $module='post')
    {
	    $this->checkUploadDirPermission();
	    ini_set("auto_detect_line_endings", true);

	    $data_rows = array();

	# Check whether file is present in the given file location
	    $fileexists = file_exists($file);
	    if ($fileexists) {
		    if($fileExtension=='xml')
		    {
			    $mycls = new Knol_WXR_Parser();
			    //call parse() function from WXR_importer.php file that returns xml contents as array
			    $all_arr=$mycls->parse($file); 
			    if(is_array($all_arr))
			    {
				    foreach($all_arr as $key => $value)
				    {
					    //Under the posts key all Posts are available in all WXR file. 
					    if($key=='posts')
					    {
						    $i=0;
						    foreach($value as  $post_value)
						    {
							if(array_key_exists('post_type',$post_value) && $post_value['post_type'] == $module){
							    $my_post[$i]=$post_value;
							    $i++;
							}
						    }
					    }
				    }
				    $all_domain=array();
				    $my_title=array();
				    for($j=0;$j<$i;$j++)
				    {       
					    $n=0;
					    // Check here the post_type
						$post_types=get_post_types(); 
					    foreach($my_post[$j] as $my_key => $my_val)
					    {
						    //Key of my_posts are post headers
						    array_push($my_title,$my_key);
						    //Value of my_posts are post contents
						    if(is_array($my_val)&& $my_key!='postmeta' && $my_key!='comments')
							    $my_content[$j][$n]="";
						    elseif($my_key!='postmeta' && $my_key!='comments')
							    $my_content[$j][$n]=$my_val;
						    //In WXR file, post_categories and post_tags will be in terms key
						    if($my_key=='terms')
						    {
							    $x=0;$z=0;
							    foreach($my_val as $my_category)
							    {	$y=0;
								    foreach($my_category as $categ_key => $categ_val)
								    {      
									    //domain refered as post_categories or post_tags
									    if($categ_key=='domain')
									    {	
										    //Some headers are in domain
										    if($categ_val)
											    $all_domain[$z]=$categ_val;
										    else
											    $all_domain[$z]=null;
										    $z++;
									    }
									    if(isset($categ_key))
										    $terms_key[$j][$x][$y]=$categ_key;
									    else
										    $terms_key[$j][$x][$y]=null;
									    if(isset($categ_val))
										    $terms_val[$j][$x][$y]=$categ_val;
									    else
										    $terms_val[$j][$x][$y]=null;

									    $y++;
								    }
								    $x++;
							    }

						    }
						    if($my_key=='postmeta')
						    {
							$pm = 0;
							foreach($my_val as $pmArr){
								$postmetaArr[$j]['postmeta'][$pmArr['key']] = $pmArr['value'];
							} $pm++;
						    } 
						if($my_key!='postmeta' && $my_key!='comments')
						    $n++;
					    }
					$my_content[$j][$n]="";
				    }
				    //Remove duplicates from my_title
				    $my_title=array_unique($my_title); 
				    //Remove duplicates from all_domain
				    $all_domain=array_unique($all_domain);
				    foreach($all_domain as $key => $value)
				    {
					    array_push($my_title,$value);
				    }
				    $l=0;
				    foreach($my_title as $value)
				    {	
					    //Make a serial index	
					    $my_titles[$l]=$value;$l++;
				    } 
				    // collect post_categories and post_tags values
                                    for($l=0;$l<count($all_domain);$l++)
                                    {
                                            for($m=0;$m<$i;$m++)
                                            {
                                                    for($n=0;$n<count($terms_key[$m]);$n++)
                                                    {                             
                                                            for($j=0;$j<count($terms_val[$m]);$j++)
                                                            { 
								if(isset($all_domain[$l]) && isset($terms_val[$m][$n][$j]) && $all_domain[$l]==$terms_val[$m][$n][$j])
                                                                    {
                                                                            for($k=0;$k<count($terms_val[$m][$n]);$k++)
                                                                            {
                                                                                    if($terms_key[$m][$n][$k]=='name'){
											if(!empty($domain_name[$l][$m])){
											    if($all_domain[$l] == 'post_tag'){
	                                                                                            $domain_name[$l][$m].=$terms_val[$m][$n][$k].', ';												  }else{
												    $domain_name[$l][$m].=$terms_val[$m][$n][$k].'|';
											    }
											}
											else{
											    if($all_domain[$l] == 'post_tag'){
												    $domain_name[$l][$m]=$terms_val[$m][$n][$k].', ';
											    }else{
												    $domain_name[$l][$m]=$terms_val[$m][$n][$k].'|';
											    }
											}
										    }
                                                                            }
                                                                    }
							    }
                                                    }
					    }
				    } 
				    //Insert post_categories and post_tags values
				    for($l=0;$l<count($all_domain);$l++)
				    {
					    for($m=0;$m<count($my_content);$m++)
						    array_push($my_content[$m],$domain_name[$l][$m]);
				    }
				    for($l=0;$l<count($postmetaArr);$l++)
                                    {
					    for($m=0;$m<count($my_content);$m++){
						if($l == $m)
                	                            array_push($my_content[$m],$postmetaArr[$l]);
					    }
				    }

				    $data_rows=$my_content;
				    $this->headers=$my_titles;
				    //    ini_set("auto_detect_line_endings", false);

			    }
			    else
			    {
				    $warning = '<div style="font-size: 16px; margin-left: 20px;">'. $this->t("UPLOAD_ANOTHER_FILE") .'</div>
                                <br/>
                                <div style="margin-left: 20px;">
                                <form class="add:the-list: validate" method="post" action="">
                                <input type="submit" class="button-primary" name="Import Again"
                                value="Import Again"/>
                                </form>
                                </div>
                                <div style="margin-left: 20px; margin-top: 30px;">
                                <b>'. $this->t("NOTE") .' :-</b>

                                <p>1.'. $this->t("NOTE_CONTENT_1") .'</p>

                                <p>2.'. $this->t("NOTE_CONTENT_2") .'</p>
                                </div>';
				print($warning);die;
			    }
		    }
		    } else {
#	require_once "class.renderxml.php";
#	$impRen = new RenderXMLCE;
#	echo $impRen->showMessage('error', "File Not Exists in this location $file");
		    }
	//print '<pre>';print_r($data_rows);die;
       return $data_rows;
    }
      /**
     * function to map the xml file and process it
     *
     * @return boolean
     */
    function processDataInWP()
    {
	    global $wpdb;

	    $smack_taxo = array();
	    $custom_array = array();
	    if(!empty($_POST ['filename'])){
		$file = $_POST ['filename'];
	    }else{
		$file = $_POST['uploadedFile'];
	    }
	    $get_xml_option = get_option('wp_advanced_importer'); 
	    $fileexists = file_exists($file);
	    if ($fileexists) 
	    {
		    $path_parts= pathinfo($file);
		    $fileExtension =$path_parts['extension'];
		    $get_allpost_types=get_post_types();
		    $eliminate = array('post','page','attachment','revision','nav_menu_item');
		    foreach($get_allpost_types as $ptkey => $ptval){
			if(!in_array($ptkey, $eliminate))
				$customposts[] = $ptval;
		    }
        	    if(isset($get_xml_option['wp_advance_importer']['common']['importas'])){
			if(in_array('Post', $get_xml_option['wp_advance_importer']['common']['importas'])){
				$module = 'post';
				$returnArr = $this->import_file_data($file, $this->delim, $fileExtension, $module);
				if($returnArr)
					$data_rows[] = $returnArr;
			}
			if(in_array('Page', $get_xml_option['wp_advance_importer']['common']['importas'])){
				$module = 'page';
				$returnArr = $this->import_file_data($file, $this->delim, $fileExtension, $module);
                                if($returnArr)
                                        $data_rows[] = $returnArr;
			}
			if(in_array('CustomPost', $get_xml_option['wp_advance_importer']['common']['importas'])){
				$module = 'custompost';
				for($mod=0;$mod<count($customposts);$mod++){
					$returnArr = $this->import_file_data($file, $this->delim, $fileExtension, $customposts[$mod]);
        	                        if($returnArr)
	                                        $data_rows[] = $returnArr;
				}
			}
	            }
	    }
	    else
		    die("File Not Exists");
 
        foreach ($_POST as $postkey => $postvalue) {
            if ($postvalue != '-- Select --') {
                $ret_array [$postkey] = $postvalue;
            }
        }
	for($dc=0;$dc<count($data_rows);$dc++){
        foreach ($data_rows[$dc] as $key => $value) {
            for ($i = 0; $i < count($value); $i++) {
                if (array_key_exists('mapping' . $i, $ret_array)) {
                    if ($ret_array ['mapping' . $i] != 'add_custom' . $i) {
                        $strip_CF = strpos($ret_array['mapping' . $i], 'CF: ');
                        if ($strip_CF === 0) {
                            $custom_key = substr($ret_array['mapping' . $i], 4);
                            $custom_array[$custom_key] = $value[$i];
                        } else {
                            $new_post[$ret_array['mapping' . $i]] = $value[$i];
                        }
                    } else {
                        $new_post [$ret_array ['textbox' . $i]] = $value [$i];
                        $custom_array [$ret_array ['textbox' . $i]] = $value [$i];
                    }
                }
		elseif(in_array('postmeta',$value)){
			$new_post['postmeta'] = $value[$i]['postmeta'];
		}
            }

            for ($inc = 0; $inc < count($value); $inc++) {
                foreach ($this->keys as $k => $v) {
                    if (array_key_exists($v, $new_post)) {
                        $custom_array [$v] = $new_post [$v];
                    }
                }
            }
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
                if ($ckey != 'post_category' && $ckey != 'post_tag' && $ckey != 'featured_image' && $ckey != $smack_taxo [$ckey] && $ckey != 'postmeta') {
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
                        case 'featured_image' :
                            /*
                             * TODO: Cleanup required
                             */
                            $split_filename = explode('/', htmlentities($new_post [$ckey]));
                            $arr_filename = count($split_filename);
                            $plain_filename = $split_filename [$arr_filename - 1];
                            $new_post [$ckey] = str_replace(' ', '%20', $new_post [$ckey]);
                            $file_url = $filetype [$ckey] = $new_post [$ckey];
                            $file_type = explode('.', $filetype [$ckey]);
                            $count = count($file_type);
                            $type = $file_type [$count - 1];

                            if ($type == 'png') {
                                $file ['post_mime_type'] = 'image/png';
                            } else if ($type == 'jpg' || $type == 'jpeg') {
                                $file ['post_mime_type'] = 'image/jpeg';
                            } else if ($type == 'gif') {
                                $file ['post_mime_type'] = 'image/gif';
                            }
                            $img_name = explode('/', $file_url);
                            $imgurl_split = count($img_name);
                            $img_name = explode('.', $img_name [$imgurl_split - 1]);
                            if (count($img_name) > 2) {
                                for ($r = 0; $r < (count($img_name) - 1); $r++) {
                                    if ($r == 0)
                                        $img_title = $img_name[$r];
                                    else
                                        $img_title .= '.' . $img_name[$r];
                                }
                            } else {
                                $img_title = $img_name = $img_name [0];
                            }
                            $attachmentName = urldecode($img_title) . '.' . $type;
                            $dir = wp_upload_dir();
                            $dirname = date('Y') . '/' . date('m');
                            $full_path = $dir ['basedir'] . '/' . $dirname;
                            $baseurl = $dir ['baseurl'] . '/' . $dirname;
                            $filename = explode('/', $file_url);
                            $file_split = count($filename);
                            $filepath = $full_path . '/' . urldecode($plain_filename);
                            $fileurl = $baseurl . '/' . $filename [$file_split - 1];
                            if (is_dir($full_path)) {
                                $smack_fileCopy = @copy($file_url, $filepath);
                            } else {
                                wp_mkdir_p($full_path);
                                $smack_fileCopy = @copy($file_url, $filepath);
                            }
                            $img = wp_get_image_editor($filepath);
                            if (!is_wp_error($img)) {

                                $sizes_array = array(
                                    // #1 - resizes to 1024x768 pixel, square-cropped image
                                    array('width' => 1024, 'height' => 768, 'crop' => true),
                                    // #2 - resizes to 100px max width/height, non-cropped image
                                    array('width' => 100, 'height' => 100, 'crop' => false),
                                    // #3 - resizes to 100 pixel max height, non-cropped image
                                    array('width' => 300, 'height' => 100, 'crop' => false),
                                    // #3 - resizes to 624x468 pixel max width, non-cropped image
                                    array('width' => 624, 'height' => 468, 'crop' => false)
                                );
                                $resize = $img->multi_resize($sizes_array);
                            }
                            if ($smack_fileCopy) {
                                $file ['guid'] = $fileurl;
                                $file ['post_title'] = $img_title;
                                $file ['post_content'] = '';
                                $file ['post_status'] = 'attachment';
                            } else {
                                $file = false;
                            }
                            break;
			case 'postmeta' :
			    if(is_array($cval)){
				foreach($cval as $pmKey => $pmVal){
					$custom_array[$pmKey] = $pmVal;
				}
			    }
			    break;
                    }
                }
            }

            if ($this->titleDupCheck)
                $this->postFlag = $this->duplicateChecks('title', $data_array ['post_title'], $data_array ['post_type']);

            if ($this->conDupCheck && $this->postFlag)
                $this->postFlag = $this->duplicateChecks('content', $data_array ['post_content'], $data_array ['post_type']);


            if ($this->postFlag) {
                unset ($sticky);
                if (empty($data_array['post_status']))
                    $data_array['post_status'] = null;

                if ($_POST['importallwithps'] != 0)
                    $data_array['post_status'] = $_POST['importallwithps'];

                switch ($data_array ['post_status']) {
                    case 1 :
                        $data_array['post_status'] = 'publish';
                        break;
                    case 2 :
                        $data_array['post_status'] = 'publish';
                        $sticky = true;
                        break;
                    case 3 :
                        $data_array['post_status'] = 'publish';
                        $data_array ['post_password'] = $_POST ['postsPassword'];
                        break;
                    case 4 :
                        $data_array ['post_status'] = 'private';
                        break;
                    case 5 :
                        $data_array ['post_status'] = 'draft';
                        break;
                    case 6 :
                        $data_array ['post_status'] = 'pending';
                        break;
                    default :
                        $poststatus = $data_array['post_status'] = strtolower($data_array['post_status']);
                        if ($data_array['post_status'] != 'publish' && $data_array['post_status'] != 'private' && $data_array['post_status'] != 'draft' && $data_array['post_status'] != 'pending' && $data_array['post_status'] != 'sticky') {
                            $stripPSF = strpos($data_array['post_status'], '{');
                            if ($stripPSF === 0) {
                                $poststatus = substr($data_array['post_status'], 1);
                                $stripPSL = substr($poststatus, -1);
                                if ($stripPSL == '}') {
                                    $postpwd = substr($poststatus, 0, -1);
                                    $data_array['post_status'] = 'publish';
                                    $data_array ['post_password'] = $postpwd;
                                } else {
                                    $data_array['post_status'] = 'publish';
                                    $data_array ['post_password'] = $poststatus;
                                }
                            } else {
                                $data_array['post_status'] = 'publish';
                            }
                        }
                        if ($data_array['post_status'] == 'sticky') {
                            $data_array['post_status'] = 'publish';
                            $sticky = true;
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
				$postauthor = $wpdb->get_results("select ID from $wpdb->users where ID = \"{$postuserid}\"");
			} else {
				$postauthor = $wpdb->get_results("select ID from $wpdb->users where user_login = \"{$postuserid}\"");
			}

			if (empty($postauthor) || !$postauthor[0]->ID) {
				$data_array ['post_author'] = 1;
				$this->noPostAuthCount++;
			} else {
				$data_array ['post_author'] = $postauthor [0]->ID;
			}
		}
		else{
			$data_array ['post_author'] = 1;
			$this->noPostAuthCount++;
		}
                // Date format post
                if (!isset($data_array ['post_date'])){
                    $data_array ['post_date'] = date('Y-m-d H:i:s');
		}else{
	                $data_array ['post_date'] = date('Y-m-d H:i:s', strtotime($data_array ['post_date']));
		}
		if(isset($data_array ['post_slug'])){
			$data_array ['post_name'] = $data_array ['post_slug'];
		}

                if ($data_array)
                    $post_id = wp_insert_post($data_array);

                unset($data_array);
                unset($postauthor);
                if ($post_id) {
                    $this->insPostCount++;
                    if (isset($sticky) && $sticky)
                        stick_post($post_id);

                    if (!empty ($custom_array)) {
                        foreach ($custom_array as $custom_key => $custom_value) {
                            add_post_meta($post_id, $custom_key, $custom_value);
                        }
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
                        foreach ($tags as $tag_key => $tag_value) {
                            wp_set_post_tags($post_id, $tag_value);
                        }
                    }

                    // Create/Add category to post
                    if (!empty ($categories)) {
                        $split_cate = explode('|', $categories ['post_category']);
                        foreach ($split_cate as $key => $val) {
                            if (is_numeric($val))
                                $split_cate[$key] = 'uncategorized';
                        }
                        wp_set_object_terms($post_id, $split_cate, 'category');
                    }
                    // Add featured image
                    if (!empty ($file) && !empty($attachmentName)) {
                        $wp_filetype = wp_check_filetype(basename($attachmentName), null);
                        $wp_upload_dir = wp_upload_dir();
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($attachmentName),
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($attachmentName)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $generate_attachment = $dirname . '/' . $attachmentName;
                        $uploadedImage = $wp_upload_dir['path'] . '/' . $attachmentName;
                        $attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        set_post_thumbnail($post_id, $attach_id);
                    }
                }
            }
        }
	}

        if (file_exists($this->getUploadDirectory() . '/' . $_POST ['filename'])) {
            $filePath = $this->getUploadDirectory() . '/';
            $this->fileDelete($filePath, $_POST ['filename']);
        }
    }
}
?>

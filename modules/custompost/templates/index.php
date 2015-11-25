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
$impCE = new WPAdvImporter_includes_helper();
$xml_object = new ConvertXML2Array();
$nonce_Key = $impCE->create_nonce_key();
?>
	<div style="width:100%;">
	<div id="accordion">
	<table class="table-importer">
	<tr>
	<td>
	<h3><?php echo __('XML Import Options','wp-advanced-importer'); ?></h3>
	<div id='sec-one' <?php if($_REQUEST['step']!= 'uploadfile') {?> style='display:none;' <?php } ?>>
	<?php if(is_dir($impCE->getUploadDirectory('default'))){ 
                if (!is_writable($impCE->getUploadDirectory('default'))) {
                        if (!chmod($impCE->getUploadDirectory('default'), 0777)) { ?>
                                <input type='hidden' id='is_uploadfound' name='is_uploadfound' value='notfound' /> <?php
                        }
                } else { ?>
                        <input type='hidden' id='is_uploadfound' name='is_uploadfound' value='found' />
                <?php }?>
	<?php } else { ?>
		<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='notfound' />
	<?php } ?>
         <div class="warning" id="warning" name="warning" style="display:none;margin: 4% 0 4% 22%;"></div>
	<form action='<?php echo admin_url().'admin.php?page='.WP_CONST_ADVANCED_XML_IMP_SLUG.'/index.php&__module='.$_REQUEST['__module'].'&step=mapping_settings'?>' id='browsefile' enctype="multipart/form-data" method='post' name='browsefile'>
	<div class="importfile" align='center'>
	<div id='filenamedisplay'></div>
	<div class="container">
          <?php echo $impCE->smack_xml_import_method(); ?>
	<input type ='hidden' id="pluginurl"value="<?php echo WP_CONTENT_URL;?>">
	<input type='hidden' id='dirpathval' name='dirpathval' value='<?php echo ABSPATH; ?>' />
	<?php $uploadDir = wp_upload_dir(); ?>
	<input type="hidden" id="uploaddir" value="<?php if(isset($uploadDir)) { echo $uploadDir['basedir']; }  ?>">
	<input type="hidden" id="uploadFileName" name="uploadfilename" value="">
        <input type = 'hidden' id = 'uploadedfilename' name = 'uploadedfilename' value = ''>
        <input type = 'hidden' id = 'upload_xml_realname' name = 'upload_xml_realname' value =''>
        <input type = 'hidden' id = 'current_file_version' name = 'current_file_version' value = ''>
        <input type = 'hidden' id = 'current_module' name = 'current_module' value = '<?php if(isset($_REQUEST['__module'])) { echo $_REQUEST['__module']; }  ?>' >
	</span>
	<!-- The global progress bar -->
         <div class="form-group" style="padding-bottom:20px;">
                                <table>
                                <tr>
				</div>
                                </div>
                                <div style="float:right;">
                                <input type='button' name='clearform' id='clearform' value='<?php echo __("Clear",'wp-advanced-importer'); ?>' onclick="Reload();"
                                class='btn btn-warning' style="margin-right:15px" />
                                <input type='submit' name='importfile' title = '<?php echo __('Next','wp-advanced-importer');?>' id='importfile' value='<?php echo $impCE->reduceStringLength(__("Next",'wp-advanced-importer'),'Next'); echo (" >>");?>' disabled
                                class='btn btn-primary'style="margin-right:15px"/>
                                </div>
                                </tr>
                                </table>
                                <div class="warning" id="warning" name="warning" style="display:none"></div>
                                <!-- The container for the uploaded files -->
                                <div id="files" class="files"></div>
                                   <br>
                                </div>
	<script>
	var check_upload_dir = document.getElementById('is_uploadfound').value;  
	if(check_upload_dir == 'notfound'){
                document.getElementById('browsefile').style.display = 'none';
		jQuery('#defaultpanel').css('visibility','hidden');
		jQuery('<p/>').text("").appendTo('#warning');
		jQuery( "#warning" ).empty();
		jQuery('#warning').css('display','inline');
		jQuery('<p/>').text("Warning:   Sorry. There is no uploads directory Please create it with write permission.").appendTo('#warning');
		jQuery('#warning').css('color','red');
		jQuery('#warning').css('font-weight','bold');
		jQuery('#progress .progress-bar').css('visibility','hidden');
	}
	else{
                jQuery(function () {
                'use strict';
                var url = (document.getElementById('pluginurl').value+'/plugins/<?php echo WP_CONST_ADVANCED_XML_IMP_SLUG;?>/modules/default/templates/index.php');
                var filesdata;
                var uploadPath = document.getElementById('uploaddir').value;
                function prepareUpload(event){
                        filesdata = event.target.files;
                        var curraction = '<?php echo $_REQUEST['__module']; ?>';
                        var frmdata = new FormData();
                        var uploadfile_data = jQuery('#fileupload').prop('files')[0];
                        frmdata.append('files', uploadfile_data);
                        frmdata.append('action','Advimpuploadfilehandle');
                        frmdata.append('curr_action', curraction);
                        frmdata.append('uploadPath', uploadPath);
                        jQuery.ajax({
                                url : ajaxurl,
                                type : 'post',
                                data : frmdata,
                                cache: false,
                                contentType : false,
                                processData: false,
                                success : function(data) {
                                        var fileobj =JSON.parse(data);
                                        jQuery.each(fileobj,function(objkey,objval){
                                                        jQuery.each(objval,function(o_key,file){
								if(file == '"Invalid XML"'){
                                                                        alert('Invalid XML');
                                                                        return false;
                                                                }       
                                                                document.getElementById('uploadFileName').value=file.name;
                                                                var filewithmodule = file.uploadedname.split(".");
                                                                var check_file = filewithmodule[filewithmodule.length - 1];
                                                                if(check_file != "xml" && check_file != "txt") {
                                                                        alert('Un Supported File Format');
                                                                        return false;
                                                                }
                                                                if(check_file == "xml"){
                                                                        var filenamexml = file.uploadedname.split(".xml");
                                                                        file.uploadedname = filenamexml[0] + "-<?php echo $_REQUEST['__module']; ?>" + ".xml";
                                                                }
                                                                if(check_file == "txt"){
                                                                        var filenametxt = file.uploadedname.split(".txt");
                                                                        file.uploadedname = filenametxt[0] + "-<?php echo $_REQUEST['__module']; ?>" + ".txt";
                                                                }
                                                                document.getElementById('upload_xml_realname').value = file.uploadedname; 
                                                                var get_version1 = file.name.split("-<?php echo $_REQUEST['__module']; ?>");
                                                                var get_version2 = get_version1[1].split(".xml");
                                                                var get_version3 = get_version2[0].split("-");
                                                                document.getElementById('current_file_version').value = get_version3[1];
                                                                jQuery('#uploadedfilename').val(file.uploadedname);
                                                                jQuery( "#filenamedisplay" ).empty();
                                                                if(file.size>1024 && file.size<(1024*1024))
                                                                {
                                                                        var fileSize =(file.size/1024).toFixed(2)+' kb';
                                                                }
                                                                else if(file.size>(1024*1024))
                                                                {
                                                                        var fileSize =(file.size/(1024*1024)).toFixed(2)+' mb';
                                                                }
                                                                else
                                                                {
                                                                        var fileSize= (file.size)+' byte';
                                                                }
                                                                jQuery('<p/>').text((file.name)+' - '+fileSize).appendTo('#filenamedisplay');
                                                                jQuery('#importfile').attr('disabled', false);
                                                                jQuery('#fileupload').prop('disabled', !jQuery.support.fileInput)
                                                                .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
                                                        });
                                        });

                                }
                        });
                }        
        jQuery('#fileupload').on('change', prepareUpload);
        jQuery('#fileupload').fileupload({
		url : url,
                progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                jQuery('#progress .progress-bar').css('width', progress + '%' );
                }
        });
});

	}
	</script>
	<input type = 'hidden' name = 'importid' id = 'importid' >
		</form>
	</div>
	</div>
	</td>
	</tr>
	<tr>
	<td>
	<form name='mappingConfig' action="<?php echo admin_url(); ?>admin.php?page=<?php echo WP_CONST_ADVANCED_XML_IMP_SLUG;?>/index.php&__module=<?php echo $_REQUEST['__module']?>&step=importoptions"  method="post" onsubmit="return import_xml();" >
	<div class='msg' id = 'showMsg' style = 'display:none;'></div>
	<?php $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] = $_POST;
	$wpxmlsettings=array();
        $custom_key = array();
	$wpxmlsettings=get_option('wpxmlfreesettings');
	$activeplugins = array();
        $activeplugins = get_option('active_plugins');
	?>
	<h3><?php echo __('Map XML to WP fields/attributes','wp-advanced-importer'); ?></h3>
         <?php if(isset($_REQUEST['step']) && $_REQUEST['step'] == 'mapping_settings' ) { ?>
	<div id='sec-two' <?php if($_REQUEST['step']!= 'mapping_settings'){ ?> style='display:none;' <?php } ?> >
	<div class='mappingsection'>
	<h2><div class="secondformheader"><?php echo __('Import Data Configuration','wp-advanced-importer'); ?></div></h2>
        <?php  
        if(isset($_FILES['inlineimages'])) {
                if(isset($_POST['uploadfilename']) && $_POST['uploadfilename'] != ''){
                        $get_file_name = $_POST['uploadfilename'];
                        $filehashkey = $impCE->convert_string2hash_key($get_file_name);
                }
                $uploaded_compressedFile = $_FILES['inlineimages']['tmp_name'];
                $get_basename_zipfile = explode('.', $_FILES['inlineimages']['name']);
                $basename_zipfile = $get_basename_zipfile[0];
                $location_to_extract = $uploadDir['basedir'] . '/smack_inline_images/' . $filehashkey;
                $extracted_image_location = $uploadDir['baseurl'] . '/smack_inline_images/' . $filehashkey;
		if(class_exists('ZipArchive')){
                $zip = new ZipArchive;
		if(!empty($uploaded_compressedFile)){
			if ($zip->open($uploaded_compressedFile) === TRUE) {
				$zip->extractTo($location_to_extract);
				$zip->close();
				$extracted_status = 1;
			} else {
				$extracted_status = 0;
			}
		}
		}
        }
        ?>
	<?php if(isset($_REQUEST['__module']) && $_REQUEST['__module']=='custompost'){ ?>
		<div class='importstatus' style='display:true;'>
			<input type="hidden" id="customposts" name="customposts" value="">
			<div style = 'float:left'> <label id="importalign"> <?php echo __('Select Post Type','wp-advanced-importer'); ?> </label> <span class="mandatory"> * </span> </div>
			<div style = 'float:left;margin-right:10px'> 
			<select name='custompostlist' id='custompostlist'>
			<option value='select'>---Select---</option>
			<?php
			$cust_post_list_count=0;
			foreach (get_post_types() as $key => $value) {
                               $cctm_post_type = array();
                                $cctm_post_type = get_option('cctm_data');
                                if(!empty($cctm_post_type)){
                                        foreach($cctm_post_type['post_type_defs'] as $cctmptkey => $cctmptval) {
                                                if($cctmptkey == $value){
                                                        $value = 'createdByCCTM';
                                                }
                                        }
                                }
                                $types_post_types = array();
                                $types_post_types = get_option('wpcf-custom-types');
                                if(!empty($types_post_types)) {
                                        foreach($types_post_types as $tptKey => $tptVal){
                                                if($tptKey == $value) {
                                                        $value = 'createdByTypes';
                                                }
                                        }
                                }
				if(in_array('custom-post-type-ui/custom-post-type-ui.php',$activeplugins)){
					if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group') && ($value != 'product') && ($value != 'product_variation') && ($value != 'shop_order') && ($value != 'shop_coupon') && ($value != 'acf') && ($value != 'createdByCCTM') && ($value != 'createdByTypes')) {?>
					<option id="<?php echo($value); ?>"> <?php echo($value);?> </option>
						<?php	
						$cust_post_list_count++;
					}
				}
				else
				{
				/*	$cust_post_ui = array();
                                       $cctm_post_type = array();
                                       $cctm_post_type = get_option('cctm_data');
                                       if(!empty($cctm_post_type)){
                                               foreach($cctm_post_type['post_type_defs'] as $cctmptkey => $cctmptval) {
                                                       if($cctmptkey == $value){
                                                               $value = 'createdByCCTM';
                                                       }
                                               }
                                       }
					$cust_post_ui = get_option('cpt_custom_post_types');
					foreach($cust_post_ui as $cust_list){
						if($cust_list['name']==$value)
						{
							$value='createdByCustomPostUI';
						}
					}
					if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group') && ($value != 'product') && ($value != 'product_variation') && ($value != 'shop_order') && ($value != 'shop_coupon') && ($value != 'acf') && ($value != 'createdByCustomPostUI') && ($value != 'createdByCCTM') && ($value != 'createdByTypes')) {?>
                                        	<option id="<?php echo($value); ?>"> <?php echo($value);?> </option>
                                                <?php                 
							$cust_post_list_count++;
					}*/
				}
			} ?>
			</select>
			<input type = 'hidden' id = 'cust_post_list_count' name = 'cust_post_list_count' value = '<?php echo $cust_post_list_count; ?>'>
			</div>
			<div style = 'float:left'> 
			<a href="#" class="tooltip">
			<img src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/help.png" />
			<span class="tooltipCustompost">
			<img class="callout" src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/callout.gif" />
			<strong><?php echo __('Select your custompost type','wp-advanced-importer'); ?></strong>
			<img src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/help.png" style="margin-top: 6px;float:right;" />
			</span>
			</a><label id='cust_post_empty' style='display:none'><?php echo __('CUSTOMPOST LIST IS EMPTY','wp-advanced-importer'); ?></label>
			</div>
			</div>
			<?php } ?>
			<?php echo $impCE->getImportDataConfiguration(); ?>
			</div>
			<div id='mappingheader' class='mappingheader' >
			<?php  
			$allcustomposts='';
			$filename='';
                        $records = '';
			if(isset($_POST['uploadfilename']) && $_POST['uploadfilename'] != ''){
                                $file_name = $_POST['uploadfilename'];
                                $filename = $impCE->convert_string2hash_key($file_name);
			}
                        if(isset($_POST['upload_xml_realname']) && $_POST['upload_xml_realname'] != '') {
                                $uploaded_xml_name = $_POST['upload_xml_realname'];
                        }
			$uploadxml_file = $uploadDir['basedir'] . '/' . 'ultimate_importer' . '/' . $filename;;

		        $xml_file = fopen($uploadxml_file,'r');
        		$xml_read = fread($xml_file , filesize($uploadxml_file));
        		fclose($xml_file);

        		$xml_arr = $xml_object->createArray($xml_read);
        		$xml_data = array();
        		$impCE->xml_file_data($xml_arr,$xml_data);
        		$reqarr = $impCE->xml_reqarr($xml_data);
        		$getrecords = $impCE->xml_importdata($xml_data);

			$getcustomposts=get_post_types();
			foreach($getcustomposts as $keys => $value)
			{
				if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group')) {
					$allcustomposts.=$value.',';
				}
			}
			?>
			<input type='button' class='btn btn-primary' name='addcustomfd' value='<?php echo $impCE->reduceStringLength(__('Add Custom Field','wp-advanced-importer'),'Add Custom Field') ;?>' style='margin-left:830px;margin-bottom:15px;background-color:#47A447' onclick = 'addcorecustomfield(CF_FIELDGRP);'>
                        <input type='hidden' id='addcorecustomfields' name='addcorecustomfields' value='' />	
			<table style="font-size: 12px;" class = "table table-striped" id='FIELDGRP'> 
			<tr>
			<div align='center' style='float:right;'>
			<?php $cnt = count($impCE->defCols) + 2;
			$cnt1 = count($impCE->headers);
                        $records = count($getrecords);
			?>
			<input type='hidden' id='h2' name='h2' value="<?php if(isset($cnt1)) { echo $cnt1; }  ?>"/>
			<input type='hidden' name='selectedImporter' id='selectedImporter' value="<?php if(isset($_REQUEST['__module'])) { echo $_REQUEST['__module']; }  ?>"/>
			<input type='hidden' id='current_record' name='current_record' value='0' />
			<input type='hidden' id='totRecords' name='totRecords' value='<?php if(isset($records)) { echo $records; }  ?>' />
			<input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>' />
			<input type='hidden' id='uploadedFile' name='uploadedFile' value="<?php if(isset($filename)) { echo  $filename; }  ?>" />
			<input type='hidden' id='nonceKey' name='wpnonce' value='<?php echo $nonce_Key; ?>' />
                        <!-- real uploaded filename -->
                        <input type='hidden' id='uploaded_xml_name' name='uploaded_xml_name' value="<?php if(isset($uploaded_xml_name)) { echo $uploaded_xml_name; }  ?>" />
			<input type='hidden' id='stepstatus' name='stepstatus' value='<?php if(isset($_REQUEST['step'])) { echo $_REQUEST['step']; }  ?>' />
			<input type='hidden' id='inline_image_location' name='inline_image_location' value='<?php if(isset($extracted_image_location)){ echo $extracted_image_location;}?>' />
			</div>
			</tr> 
			<?php
			$count = 0;
                        if (isset($_REQUEST['__module']) && $_REQUEST['__module'] != 'page') {
                                unset($impCE->defCols['menu_order']);
                                unset($impCE->defCols['wp_page_template']);
                        }
                        ?>
			<tr>
                        <td colspan='4' class="left_align columnheader" style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('WordPress Fields:','wp-advanced-importer'); ?></b>
                        </div>
                        </td>
                        </tr>
                        <tr>
                        <td class="left_align columnheader" style = 'padding-left:170px;'> <b><?php echo __('WP FIELDS','wp-advanced-importer'); ?></b> </td><td class="columnheader" style = 'padding-left:55px;'> <b><?php echo __('XML NODE','wp-advanced-importer'); ?></b> </td><td> </td><td></td></tr>
                        <?php
                        foreach ($impCE->defCols as $key => $value)
                        {
                                if(!strstr($key,'CF:') && !strstr($key,'SEO:')){?>

                        <tr>
                                <td class="left_align" style = 'padding-left:150px;'>
                        <input type='hidden' name ='fieldname<?php print($count); ?>' id = 'fieldname<?php print($count); ?>' value = <?php echo $key; ?> />
                        <label class='wpfields'><?php print('<b>'.$key.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$key.']'); ?></label>
                                </td>
		
				<td>
					<?php if($key == 'post_status'){ ?>
                                        <select name="mapping<?php print($count); ?>" id="mapping<?php print($count); ?>" onChange=changefield();>
                                        <?php }else{ ?>
                                        <select name="mapping<?php print($count); ?>" id="mapping<?php print($count);?>">
                                        <?php } ?> 
						<?php echo $impCE->xml_mappingbox($getrecords,$key,$count);?>
                                        </select>

                                </td>
                                <td>

				</td><td></td>
                                </tr>
                                        <?php
                                        $count++;
                        }
                        }
?>
			<input type='hidden' id='wpfields' name='wpfields' value='<?php echo($count) ?>' />
			</table>
<script>
</script>

			<table style="font-size: 12px;" class = "table table-striped" id='CF_FIELDGRP'>
                        <tr>

                        <td colspan = 5 class='left_align columnheader' style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('Custom Fields:','wp-advanced-importer'); ?></b>
                        </div>

                        </td>
                        </tr>
                        <?php
                        foreach($impCE->defCols as $key => $value){
                                if(strstr($key,'CF:')){
                        ?>
			
			<tr>
                                <td class="left_align" style='width:52%;padding-left:150px;'>
				<input type='hidden' name ='corefieldname<?php print($count); ?>' id = 'corefieldname<?php print($count); ?>' value = '<?php echo $key; ?>' />


                        <label class='wpfields'><?php print('<b>'.$value.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$value.']'); ?></label>
                                </td>
                                <td>
                                        <select name="coremapping<?php print($count); ?>" id="coremapping<?php print($count); ?>">
						<?php echo $impCE->xml_mappingbox($getrecords,$key,$count);?>
                                        </select>
				</td>
                                <td>
				</td><td></td>
                                </tr>
                                        <?php
                                        $count++;
                        }
                        }?>
			<input type='hidden' id='customfields' name='customfields' value='<?php echo($count) ?>' />
			</table>
			<table>
 <tr>
<td colspan= '4'>
<input type='button' class='btn btn-primary' name='addcustomfd' value='<?php echo $impCE->reduceStringLength(__('Add Custom Field','wp-advanced-importer'),'Add Custom Field') ;?>' style='margin-left:830px;margin-bottom:15px;margin-top:20px;background-color:#47A447;' onclick = 'addcorecustomfield(CF_FIELDGRP);'>
<input type='hidden' id='addcorecustomfields' name='addcorecustomfields' value='' />
</td>
</tr>
</table>
			<?php
                        $wpxmlfreesettings = get_option('wpxmlfreesettings');
                        $active_plugins = get_option('active_plugins');
                                if(in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)){
?>
			<table style="font-size: 12px;" class = "table table-striped" id='SEO_FIELDGRP'>
                        <tr>

                        <td colspan = 5 class='left_align columnheader' style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('SEO Fields:','wp-advanced-importer'); ?></b>
                        </div>

                        </td>
                        </tr>
                        <?php
                        foreach($impCE->defCols as $key => $value){
                                if(strstr($key,'SEO:')){
                                $value = str_replace('SEO:','',$value)
                        ?>

			<tr>
                                <td class="left_align" style='width:52%;padding-left:150px;'>
                                 <input type='hidden' name ='seofieldname<?php print($count); ?>' id = 'seofieldname<?php print($count); ?>' value = '<?php echo $key; ?>' />

                        <label class='wpfields'><?php print('<b>'.$value.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$value.']'); ?></label>
                                </td>
                                <td>
                                        <select name="seomapping<?php print($count); ?>" id="seomapping<?php print($count); ?>">
						<?php echo $impCE->xml_mappingbox($getrecords,$key,$count);?>
                                        </select>

                                </td>
                                <td>
				</td><td></td>
                                </tr>

					<?php
                                        $count++;
                        }
                        }?>
		<input type='hidden' id='seofields' name='seofields' value='<?php echo($count) ?>' />
		</table>
                <?php } ?>

                <?php $basic_count = $count - 1; ?>
                <input type="hidden" id="basic_count" name="basic_count" value="<?php echo $basic_count; ?>" />
                <input type="hidden" id="corecustomcount" name="corecustomcount" value=0 />

		<div>
	                <div class="goto_import_options" style='padding-left:350px;'>
                <div class="mappingactions"  style="margin-top:26px;">
                <input type='button' id='clear_mapping' title = '<?php echo __('Reset','wp-advanced-importer'); ?>' class='clear_mapping btn btn-warning' name='clear_mapping' value='<?php echo __('Reset','wp-advanced-importer');?>' onclick='clearMapping();' style = 'float:left'/>
                </div>
                <div class="mappingactions" >
                <input type='submit' id='goto_importer_setting' title = '<?php echo __('Next','wp-advanced-importer'); ?>' class='goto_importer_setting btn btn-info' name='goto_importer_setting' value='<?php echo $impCE->reduceStringLength(__('Next','wp-advanced-importer'),'Next'); ?> >>' />
                </div>
                </div> 
		</div>
		</div>
                <?php } ?>
		</div>
		</form>
		</td>
		</tr>
		<tr>
		<td>
		<h3><?php echo __('Settings and Performance','wp-advanced-importer'); ?></h3>
             <?php if(isset($_REQUEST['step']) && $_REQUEST['step']  == 'importoptions') { ?>
		<div id='sec-three' <?php if($_REQUEST['step']!= 'importoptions'){ ?> style='display:none;' <?php } ?> >
          <?php if(isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'])) { ?>
		<input type='hidden' id='current_record' name='current_record' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['current_record']; ?>' />
		<input type='hidden' id='tot_records' name='tot_records' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>' />
		<input type='hidden' id='checktotal' name='checktotal' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>' />
		<input type='hidden' id='stepstatus' name='stepstatus' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['stepstatus']; ?>' />
		<input type='hidden' id='selectedImporter' name='selectedImporter' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter']; ?>' />
            <?php } ?>                
            <?php if(isset($_POST)) { ?>
 		<input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>' />
		<input type='hidden' id='checkfile' name='checkfile' value='<?php echo $_POST['uploadedFile']; ?>' />
		<input type='hidden' id='uploadedFile' name='uploadedFile' value='<?php echo $_POST['uploadedFile']; ?>' />
		<input type='hidden' id='inline_image_location' name='location_inlineimages' value='<?php echo $_POST['inline_image_location']; ?>' />
                  <?php } ?>
		<!-- Import settings options -->
		<div class="postbox" id="options" style=" margin-bottom:0px;">
		<div class="inside">
                <label id="importalign"><input type ='radio' id='importNow' name='importMode' value='' onclick='choose_import_mode(this.id);' checked/> <?php echo __("Import right away",'wp-advanced-importer'); ?> </label> 
                                        <label id="importalign"><input type ='radio' id='scheduleNow' name='importMode' value='' onclick='choose_import_mode(this.id);' disabled/> <?php echo __("Schedule now",'wp-advanced-importer'); ?> <img src="<?php echo WP_CONTENT_URL; ?>/plugins/<?php echo WP_CONST_ADVANCED_XML_IMP_SLUG; ?>/images/pro_icon.gif" title="PRO Feature" /></label>
                  <div id='schedule' style='display:none'>
                                 <input type ='hidden' id='select_templatename' name='#select_templatename' value = '<?php if(isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'])) { echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'] ; } ?>'>
                                    </div>
 <div id='importrightaway' style='display:block'>
		<form method="POST">
		<ul id="settings">
		<li>
		<label id="importalign"><input name='duplicatecontent' id='duplicatecontent' type="checkbox" value=""> <?php echo __('Detect duplicate post content','wp-advanced-importer'); ?></label> <br>
		<input type='hidden' name='wpnoncekey' id='wpnoncekey' value='<?php echo $nonce_Key; ?>' />
		<label id="importalign"><input name='duplicatetitle' id='duplicatetitle' type="checkbox" value="" > <?php echo  __('Detect duplicate post title','wp-advanced-importer'); ?></label> <br>
		 <label id="importalign"><?php echo __('No. of posts per server request','wp-advanced-importer'); ?></label> <span class="mandatory" style="margin-left:-13px;margin-right:10px">*</span> <input name="importlimit" id="importlimit" type="text" value="1" placeholder="10" onblur="check_allnumeric(this.value);"></label> <?php echo $impCE->helpnotes(); ?><br>
			<span class='msg' id='server_request_warning' style="display:none;color:red;margin-left:-10px;"><?php echo __('You can set upto','wp-advanced-importer'); ?> <?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?><?php echo __('per request.','wp-advanced-importer'); ?></span>
                <input type="hidden" id="currentlimit" name="currentlimit" value="0"/>
		<input type="hidden" id="tmpcount" name="tmpcount" value="0" />
		<input type="hidden" id="terminateaction" name="terminateaction" value="continue" />
                <label id="innertitle"><?php echo __('Inline image options','wp-advanced-importer'); ?></label><br />
                <label id='importalign'> <input type ='checkbox' id='multiimage' name='multiimage' value = '' > <?php echo __('Insert Inline Images','wp-advanced-importer'); ?> <?php echo $impCE->inlinehelpnotes(); ?></label><br>
                <input type='hidden' id='inlineimagevalue' name='inlineimagevalue' value='none' />
		</li>
		</ul>
		<input id="startbutton" class="btn btn-primary" type="button" value="<?php echo __('Import Now','wp-advanced-importer'); ?>" onclick="importRecordsbySettings();" >
		<input id="terminatenow" class="btn btn-danger btn-sm" type="button" value="<?php echo __('Terminate Now','wp-advanced-importer'); ?>" style="display:none;" onclick="terminateProcess();" />
		<input class="btn btn-warning" type="button" value="<?php echo __('Reload','wp-advanced-importer'); ?>" id="importagain" style="display:none" onclick="import_again();" />
                <input id="continuebutton" class="btn btn-lg btn-primary" type="button" value="<?php echo __('Continue','wp-advanced-importer'); ?>" style="display:none;color: #ffffff;" onclick="continueprocess();">
		<div id="ajaxloader" style="display:none"><img src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/ajax-loader.gif"> <?php echo __('Processing...','wp-advanced-importer'); ?></div>
		<div class="clear"></div>
		</form>
                </div>
		<div class="clear"></div>
		<br>
		</div>
		</div>
                 <?php } ?>
		<!-- Code Ends Here-->
		</div>
		</td>
		</tr>
		</table>
		</div>
                   <div style="width:100%;">
                                               <div id="accordion">
                                               <table class="table-importer">
                                               <tr>
                                               <td>
                                               <h3><?php echo __("Summary",'wp-advanced-importer'); ?></h3>
                                                <div id='reportLog' class='postbox'  style='display:none;'>
                                                <input type='hidden' name = 'xml_version' id = 'xml_version' value = "<?php if(isset($_POST['uploaded_xml_name'])) { echo $_POST['uploaded_xml_name']; } ?>">
                                                <div id="logtabs" class="logcontainer">
                                                <div id="log" class='log'>
                                                </div>
                                                </div>
                                                </div>
                                                </td>
                                                </tr>
                                                </table>
                                                </div>
                                                </div>
                <!-- Promotion footer for other useful plugins -->
                <div class= "promobox" id="pluginpromo" style="width:98%;">
                <div class="accordion-group" >
                <div class="accordion-body in collapse">
                <div>
		<?php $impCE->common_footer(); ?>
                </div>
                </div>
                </div>
                </div> 
	</div>

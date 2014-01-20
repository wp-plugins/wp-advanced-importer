<?php
/**
 *Plugin Name: WP Advanced Importer
 *Plugin URI: http://www.smackcoders.com
 *Description: A plugin that helps to import the data's from a XML file.
 *Version: 1.1.0
 *Author: smackcoders.com
 *Author URI: http://www.smackcoders.com
 *
 * Copyright (C) 2013 Smackcoders (www.smackcoders.com)
 *
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @link http://www.smackcoders.com/blog/category/free-wordpress-plugins
 ***********************************************************************************************
 */
require_once (ABSPATH . 'wp-load.php');
require_once "SmackXMLImpCE.php";
require_once "class.renderxml.php";
global $impXMLCE, $XMLImpRen;
$impXMLCE = new SmackXMLImpCE ();
$XMLImpRen = new RenderXMLCE();
require_once "languages/" . $impXMLCE->user_language() . ".php";

/**
 * Admin menu settings
 */
function wp_importer_advanced()
{
	add_menu_page('XML importer settings', 'WP Importer Advanced', 'manage_options', 'upload_xml_file', 'upload_xml_file', WP_CONTENT_URL . "/plugins/wp-advanced-importer/images/icon.png");
}

/**
 *Function to load script files
 */
function LoadAdvancedScript()
{
    if (isset($_REQUEST['page'])) {
        if ($_REQUEST['page'] == 'upload_xml_file') {
            wp_register_script('wp_importer_advanced_scripts', WP_CONTENT_URL . "/plugins/wp-advanced-importer/wp_importer_advanced.js", array("jquery"));
        }
    }

    if (isset($_REQUEST['page'])) {
        if ($_REQUEST['page'] == 'upload_xml_file') {
            wp_enqueue_style('importer_styles', WP_CONTENT_URL . '/plugins/wp-advanced-importer/css/custom-style.css');
        }
    }

    wp_enqueue_script('wp_importer_advanced_scripts');

}

add_action('admin_enqueue_scripts', 'LoadAdvancedScript');
add_action("admin_menu", "wp_importer_advanced");

/**
 * Home page layout and importer
 */
function upload_xml_file()
{  
	global $impXMLCE, $XMLImpRen, $pluginActive, $custo_taxo;
	global $custom_array;
	global $wpdb;
	$mFieldsArr = '';
	if (!isset($_REQUEST['action']) || !$_REQUEST['action']) {
		?>
		<script>
			window.location.href = "<?php echo WP_PLUGIN_URL;?>/../../wp-admin/admin.php?page=upload_xml_file&action=post";
		</script>
			<?php
	}
	$importdir = $impXMLCE->getUploadDirectory();
	if (!$_REQUEST['action'] || (isset($_POST['post_xml']) && $_POST['post_xml']))
		echo "<input type = 'hidden' value ='dashboard' id='requestaction'>";
	else
		echo "<input type='hidden' value='" . $_REQUEST['action'] . "' id ='requestaction'>";
	echo '<input type="hidden" value="' . WP_CONTENT_URL . '" id="contenturl">';

	$custom_array = array();
	$mapping_section = array();?>
	<input type="hidden" name="versionedname" id="versionedname" value=""/>
	<input type="hidden" name="fileversion" id="fileversion" value=""/>
	<?php 
	$path_parts = pathinfo($_FILES ['xml_import'] ['name']);
	$fileExtension =$path_parts['extension'];
	if($_FILES ['xml_import'] ['tmp_name'])
		$impXMLCE->xmlFileName =  $_FILES ['xml_import'] ['tmp_name'];
	if($fileExtension)
		$impXMLCE->xmlFileExtn = $fileExtension;
	if(isset($_POST['filename']))
		$impXMLCE->xmlFileName = $_POST['filename'];
	if(isset($_POST['fileext']))
                $impXMLCE->xmlFileExtn = $_POST['fileext'];
	$data_rows = $impXMLCE->import_file_data($impXMLCE->xmlFileName, $impXMLCE->delim,$impXMLCE->xmlFileExtn); 
	$impXMLCE->move_file();
	$importedas_Arr = $_POST['importas'];
	?>
	<div class="smack-wrap" id="smack-content">
	<?php
	if (count($impXMLCE->headers) >= 1 && count($data_rows) >= 1) { 
		//set custom fields value
		$taxo = get_taxonomies();
		foreach ($taxo as $taxokey => $taxovalue) {
			if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format' && $taxokey != 'product_tag' && $taxokey != 'wpsc_product_category' && $taxokey != 'wpsc-variation') {
				$custo_taxo .= $taxokey . ',';
			}
		}
		$custo_taxo = substr($custo_taxo, 0, -1);
		?>
			<input type='hidden' name='cust_taxo' id='cust_taxo' value='<?php echo $custo_taxo; ?>'/>
			<input type="hidden" id="header_array" name="header_array" value="<?php print_r($impXMLCE->headers); ?>"/>
			<input type='hidden' name='realfilename' id='realfilename' value="<?php echo($_FILES['xml_import']['name']); ?>"/>
			<input type="hidden" name="version" id="version" value=""/>
<!--			<input type='hidden' name='selectedImporter' id='selectedImporter' value="<?php //echo $_REQUEST['action']; ?>"/> -->
			<?php 
		if (isset ($_POST ['Import'])) {
			$mapping_section['wp_advance_importer']['common'] = $_POST;
			update_option('wp_advanced_importer',$mapping_section);
			$getOption = get_option('wp_advanced_importer'); 
			if(in_array('Post', $getOption['wp_advance_importer']['common']['importas'])){ 
				require_once ('postmapping.php');
			}
			elseif(in_array('Page', $getOption['wp_advance_importer']['common']['importas'])){
				require_once ('pagemapping.php');
			}
			elseif(in_array('CustomPost', $getOption['wp_advance_importer']['common']['importas'])){
				require_once ('custompostmapping.php');
			}

		} 
		else if(isset ($_POST ['post_xml'])) { 
			$getOption = get_option('wp_advanced_importer'); 
			$mapping_section['wp_advance_importer']['post_xml'] = $_POST; 
			$mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
			update_option('wp_advanced_importer',$mapping_section);
			$getOption = get_option('wp_advanced_importer'); 
			if(in_array('Page', $getOption['wp_advance_importer']['common']['importas'])){
				require_once ('pagemapping.php');
			}
			elseif(in_array('CustomPost',$getOption['wp_advance_importer']['common']['importas'])){
				require_once ('custompostmapping.php');
			}else{
				$impXMLCE->processDataInWP();
				if ($impXMLCE->insPostCount != 0) {
					$messageString = $impXMLCE->insPostCount . " records are successfully Imported.";
					if (($impXMLCE->noPostAuthCount != 0) && (in_array('post_author', $_POST)))
						$messageString .= '<br>' . $impXMLCE->noPostAuthCount . " posts with no valid UserID/Name are assigned admin as author.";
					echo $XMLImpRen->showMessage('success', $messageString);
				} else if ($impXMLCE->insPostCount == 0){
					echo $XMLImpRen->showMessage('error', "Check your XML file and format.");
				}
			}
		}
		else if(isset ($_POST ['page_xml'])) {
			$getOption = get_option('wp_advanced_importer');
			$mapping_section['wp_advance_importer']['page_xml'] = $_POST;
			$mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
			update_option('wp_advanced_importer',$mapping_section);
			$getOption = get_option('wp_advanced_importer');
			if(in_array('CustomPost',$getOption['wp_advance_importer']['common']['importas'])){
				require_once ('custompostmapping.php');
			}else{
				die;
			}
		}
		else if(isset ($_POST ['custompost_xml'])) { 
			$getOption = get_option('wp_advanced_importer');
			$mapping_section['wp_advance_importer']['custompost_xml'] = $_POST;
			$mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
			$getOption = get_option('wp_advanced_importer');
			update_option('wp_advanced_importer',$mapping_section);
			$impXMLCE->processDataInWP();
			if ($impXMLCE->insPostCount != 0) {
				$messageString = $impXMLCE->insPostCount . " records are successfully Imported.";
				if (($impXMLCE->noPostAuthCount != 0) && (in_array('post_author', $_POST)))
					$messageString .= '<br>' . $impXMLCE->noPostAuthCount . " posts with no valid UserID/Name are assigned admin as author.";
				echo $XMLImpRen->showMessage('success', $messageString);
			} else if ($impXMLCE->insPostCount == 0){
				echo $XMLImpRen->showMessage('error', "Check your XML file and format.");
			}
		}
		else { ?>
			<div class="wrap" id="smack-content">
				<div class="smack-postform">
				<?php //echo $XMLImpRen->renderPostPage(); ?>
				</div>
				<div class="module-desc">
				<?php print($XMLImpRen->renderDesc()); ?>
				</div>
				</div>
				<?php
		}
	}
	else if(isset ($_POST ['post_xml']) || isset ($_POST ['page_xml']) || isset ($_POST ['custompost_xml'])){
             if(isset($_POST ['post_xml'])){
                $getOption = get_option('wp_advanced_importer');
                $mapping_section['wp_advance_importer']['post_xml'] = $_POST;
                $mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
		if(!in_array('post_xml', $mapping_section['wp_advance_importer'])){
	                update_option('wp_advanced_importer',$mapping_section);
		}
             }
	     if(isset($_POST ['page_xml'])){
		$getOption = get_option('wp_advanced_importer');
		$mapping_section['wp_advance_importer']['page_xml'] = $_POST;
		$mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
                if(!in_array('page_xml', $mapping_section['wp_advance_importer'])){
			update_option('wp_advanced_importer',$mapping_section);
		}
	     }
	     if(isset($_POST ['custompost_xml'])){
                $getOption = get_option('wp_advanced_importer');
                $mapping_section['wp_advance_importer']['custompost_xml'] = $_POST;
                $mapping_section['wp_advance_importer'] = array_merge($mapping_section['wp_advance_importer'],$getOption['wp_advance_importer']);
                if(!in_array('custompost_xml', $mapping_section['wp_advance_importer'])){
	                update_option('wp_advanced_importer',$mapping_section);
		}
	     }
	     $impXMLCE->processDataInWP();
	     if ($impXMLCE->insPostCount != 0) {
			$messageString = $impXMLCE->insPostCount . " records are successfully Imported.";
			if (($impXMLCE->noPostAuthCount != 0) && (in_array('post_author', $_POST)))
			    $messageString .= '<br>' . $impXMLCE->noPostAuthCount . " posts with no valid UserID/Name are assigned admin as author.";
			echo $XMLImpRen->showMessage('success', $messageString);
	     } else if ($impXMLCE->insPostCount == 0){
			echo $XMLImpRen->showMessage('error', "Check your XML file and format.");
	     }
	}
	else {  ?>
                <div class="wrap" id="smack-content">
                <div class="smack-postform">
                <?php echo $XMLImpRen->renderPostPage(); ?>
                </div>
                <div class="module-desc">
                <?php print($XMLImpRen->renderDesc()); ?>
                </div>
                </div>
        <?php
        }
}
?>

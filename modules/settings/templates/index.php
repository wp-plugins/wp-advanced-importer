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
global $wp_version, $wpdb;
$impCE = new WPAdvImporter_includes_helper();
$nonce_Key = $impCE->create_nonce_key();
if(! wp_verify_nonce($nonce_Key, 'smack_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');
?>
<!--<div style ='text-align:center;margin:0;color:red;font-size:smaller;'> <?php echo __('Your Required Settings Configuration Please Select Security and Performance tab','wp-advanced-importer'); ?> </div></br>-->
<div id="ShowMsg" style="display:none;"><p id="warning-msg" class="alert alert-warning"><?php echo $skinnyData['plugStatus'];?></p></div>
<?php if(isset($skinnyData['savesettings']) && $skinnyData['savesettings'] == 'done'){ ?>
        <div id="deletesuccess"><p class="alert alert-success"><?php echo __('Settings Saved','wp-advanced-importer'); ?></p></div>
                <?php
                $skinnyData['savesettings'] == 'notdone';
        ?>
                <script type="text/javascript">
                jQuery(document).ready( function() {
                                jQuery('#ShowMsg').delay(1000).fadeOut();
                                jQuery('#ShowMsg').css("display", "none");
                                jQuery('#deletesuccess').delay(1000).fadeOut();
                                });
        </script>
                <?php
}
$wpxmlsettings = get_option('wpxmlfreesettings');
$debugmode = isset($wpxmlsettings['debug_mode']) ? $wpxmlsettings['debug_mode'] : '';
$allow_import = isset($wpxmlsettings['enable_plugin_access_for_author']) ? $wpxmlsettings['enable_plugin_access_for_author'] : '';
global $wpdb; ?>
<div class="uifree-settings">
<form class="add:the-list: validate" style = "width:98%;" action="" name="importerSettings" method="post" enctype="multipart/form-data">
        <span><label class="activemenu"><?php echo __('General Settings','wp-advanced-importer'); ?></label></span>
<table class="settings-menu">
<tbody>
<tr>
<th><label>Error Handling</label></th>
<?php if($debugmode == 'enable_debug') {?>
<td><input type="checkbox" name = "debug_mode" value= "enable_debug" checked> <span>Debug Mode</span></td>
<?php } else {?>
<td><input type="checkbox" name = "debug_mode" value= "enable_debug"> <span>Debug Mode</span></td>
<?php } ?>
</tr>
<tr>
<th><label>Enable Import</label></th>
<?php if($allow_import == 'enable_plugin_access_for_author') {?>
<td><input type="checkbox" name='enable_plugin_access_for_author' value = 'enable_plugin_access_for_author' checked><span>Author/Editor Import</span></td>
<?php } else { ?>
<td><input type="checkbox" name='enable_plugin_access_for_author' value = 'enable_plugin_access_for_author'><span>Author/Editor Import</span></td>
<?php } ?>
</tr>
</tbody>
</table>
<br>
<button class="btnn btn-primary" onclick="saveSettings();" style="margin-left:7px;padding:5px;" value="Save" name="savesettings" type="submit"><?php echo __('Save Changes','wp-advanced-importer'); ?> </button> 
<p style ="padding-top:20px;">
<label class="activemenu"><?php echo __('Security and Performance','wp-advanced-importer'); ?></label>
</p>
<div id="section8" class="securityperformance">
                        <div class="databorder security-perfoemance">
                        <table class="table table-striped">
                        <tr><th colspan="3" >
                        <h3 id="innertitle">Minimum required php.ini values (Ini configured values)</h3>
                        </th></tr>
                        <tr><th>
                        <label>Variables</label>
                        </th><th class='ini-configured-values'>
                        <label>System values</label>
                        </th><th class='min-requirement-values'>
                        <label>Minimum Requirements</label>
                        </th></tr>
                        <tr><td>post_max_size </td><td class='ini-configured-values'><?php echo ini_get('post_max_size') ?></td><td class='min-requirement-values'>10M</td></tr>
                        <tr><td>auto_append_file</td><td class='ini-configured-values'>-<?php echo ini_get('auto_append_file') ?></td><td class='min-requirement-values'>-</td></tr>
                        <tr><td>auto_prepend_file </td><td class='ini-configured-values'>-<?php echo ini_get('auto_prepend_file') ?></td><td class='min-requirement-values'>-</td></tr>
                        <tr><td>upload_max_filesize </td><td class='ini-configured-values'><?php echo ini_get('upload_max_filesize') ?></td><td class='min-requirement-values'>2M</td></tr>
                        <tr><td>file_uploads </td><td class='ini-configured-values'><?php echo ini_get('file_uploads') ?></td><td class='min-requirement-values'>1</td></tr>
                        <tr><td>allow_url_fopen </td><td class='ini-configured-values'><?php echo ini_get('allow_url_fopen') ?></td><td class='min-requirement-values'>1</td></tr>
                        <tr><td>max_execution_time </td><td class='ini-configured-values'><?php echo ini_get('max_execution_time') ?></td><td class='min-requirement-values'>3000</td></tr>
                        <tr><td>max_input_time </td><td class='ini-configured-values'><?php echo ini_get('max_input_time') ?></td><td class='min-requirement-values'>3000</td></tr>
                        <tr><td>max_input_vars </td><td class='ini-configured-values'><?php echo ini_get('max_input_vars') ?></td><td class='min-requirement-values'>3000</td></tr>
                        <tr><td>memory_limit </td><td class='ini-configured-values'><?php echo ini_get('memory_limit') ?></td><td class='min-requirement-values'>99M</td></tr>
                        </table>
                        <h3 id="innertitle" colspan="2" >Required Loaders and Extentions:</h3>
                        <table class="table table-striped">
                        <?php $loaders_extensions = get_loaded_extensions();
                              $mod_security = apache_get_modules();
                       ?>
                       <!--<tr><td>IonCube Loader </td><td><?php if(in_array('ionCube Loader', $loaders_extensions)) {
                                        echo '<label style="color:green;">Yes</label>';
                                } else {
                                        echo '<label style="color:red;">No</label>';
                                } ?> </td><td></td></tr>-->
                        <tr><td>PDO </td><td><?php if(in_array('PDO', $loaders_extensions)) {
                                        echo '<label style="color:green;">Yes</label>';
                                } else {
                                        echo '<label style="color:red;">No</label>';
                                } ?></td><td></td></tr>
                        <tr><td>Curl </td><td><?php if(in_array('curl', $loaders_extensions)) {
                                        echo '<label style="color:green;">Yes</label>';
                                } else {
                                        echo '<label style="color:red;">No</label>';
                                } ?></td><td></td></tr>
                         <tr><td>Mod Security </td><td><?php if(in_array('mod_security.c', $mod_security)) {
                                        echo '<label style="color:green;">Yes</label>';
                                } else {
                                        echo '<label style="color:red;">No</label>';
                                } ?></td><td>
                                        <div style='float:left'>
                                                <a href="#" class="tooltip">
                                                        <img src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/help.png" style="margin-left:-74px;"/>
                                                        <span style="margin-left:20px;margin-top:-10px;width:150px;">
                                                                <img class="callout" src="<?php echo WP_CONST_ADVANCED_XML_IMP_DIR; ?>images/callout.gif"/>
                                                                <strong>htaccess settings:</strong>
                                                                <p>Locate the .htaccess file in Apache web root,if not create a new file named .htaccess and add the following:</p>
<b><?php echo '<IfModule mod_security.c>';?> SecFilterEngine Off SecFilterScanPOST Off <?php echo ' </IfModule>';?></b>

                                                        </span>
                                                </a>
                                        </div>
                                    </td></tr>

                        </table>
                        <h3 id="innertitle" colspan="2" >System Status:</h3>
                        <table class="table table-striped">
                        <tr><td class='debug-info-name'>WordPress Version</td><td><?php echo $wp_version; ?></td><td></td></tr>
                        <tr><td class='debug-info-name'>PHP Version</td><td><?php echo phpversion(); ?></td><td></td></tr>
                       <tr><td class='debug-info-name'>MySQL Version</td><td><?php echo $wpdb->db_version(); ?></td><td></td></tr>
                        <tr><td class='debug-info-name'>Server SoftWare</td><td><?php echo $_SERVER[ 'SERVER_SOFTWARE' ]; ?></td><td></td></tr>                        <tr><td class='debug-info-name'>Your User Agent</td><td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td><td></td></tr>
                        <tr><td class='debug-info-name'>WPDB Prefix</td><td><?php echo $wpdb->prefix; ?></td><td></td></tr>
                        <tr><td class='debug-info-name'>WP Multisite Mode</td><td><?php if ( is_multisite() ) { echo '<label style="color:green;">Enabled</label>'; } else { echo '<label style="color:red;">Disabled</label>'; } ?> </td><td></td></tr>
                        <tr><td class='debug-info-name'>WP Memory Limit</td><td><?php echo (int) ini_get('memory_limit'); ?></td><td></td></tr>
                        </table>
                        </div>
                </div>
</form>
</div>

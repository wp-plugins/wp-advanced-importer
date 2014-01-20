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

class RenderXMLCE
{

    /**
     * Render dashboard action
     */
    function setDashboardAction()
    {
        return '<div id = "requestaction" type = "hidden" value = "dashboard">';
    }

    /**
     * Shows status message
     */
    function showMessage($status, $message)
    {
        $content = "<div class = \"$status msg\"> $message</div><br><div align='center'><form name='importagain' action='' method='post'><input type='submit' name='import_again' id='import_again' value='Import Again' /></form></div>";
	return $content;
    }

    /**
     * Render description for each modules
     */
    function renderDesc()
    {
        return "<p>WP Importer Advanced Plugin helps you to manage the post,page and </br> custom post data's from a XML file.</p>
		<p>1. Admin can import the data's from any xml file.</p>
		<p>2. Can define the type of post and post status while importing.</p>
		<p>3. Provides header mapping feature to import the data's as your need.</p>
		<p>4. Users can map coloumn headers to existing fields or assign as custom fileds.</p>
		<p>5. Import unlimited datas as post.</p>
		<p>6. Make imported post as published or make it as draft.</p>
		<p>7. Added featured image import functionality.</p>
		<p>Configuring our plugin is as simple as that. If you have any questions, issues and request on new features, plaese visit <a href='http://www.smackcoders.com/blog/category/free-wordpress-plugins' target='_blank'>Smackcoders.com blog </a></p>
		<div align='center' style='margin-top:40px;'> 'While the scripts on this site are free, donations are greatly appreciated. '<br/><br/><a href='http://www.smackcoders.com/donate.html' target='_blank'><img src='" . WP_CONTENT_URL . "/plugins/wp-advanced-importer/images/paypal_donate_button.png' /></a><br/><br/><a href='http://www.smackcoders.com/' target='_blank'><img src='http://www.smackcoders.com/wp-content/uploads/2012/09/Smack_poweredby_200.png'></a>
		</div><br/>";
    }


    /**
     * Render post/page section
     */
    function renderPostPage()
    {
        $impCE = new SmackXMLImpCE (); 
	$getUsers = get_users(); 
	foreach($getUsers as $key => $value){
		if($value->ID){
			$users[$key]['ID'] = $value->ID;
			$users[$key]['user_login'] = $value->user_login;
		}
	}
        $postForm = ' <div class="msg" id = "showMsg" style = "display:none;"></div> <div style="float: left; margin-top: 11px; margin-right: 5px;"><img src = "' . WP_CONTENT_URL . '/plugins/wp-advanced-importer/images/Importicon_24.png"></div><div style="float:left;"><h2>' . $impCE->t('IMPORT_XML_FILE') . '</h2></div></br></br>';
	$postForm .= '<form class="add:the-list: validate" method="post"enctype="multipart/form-data" onsubmit="return file_exist();">
			<table class="importform"><tr>
			<td><label for="xml_import" class="uploadlabel" >'.$impCE->t('UPLOAD_FILE').'<span class="mandatory"> *</span></label></td>
			<td><input type="hidden" value="' . WP_CONTENT_URL . '" id="contenturl" /><input name="xml_import" id="xml_import" class="btn" type="file" value="" /></td></tr>';
	$postForm .= '<tr><td colspan="2">Assign Author as<b>:</b> admin('.$users[0]["user_login"].') <b>(or)</b> <select id="choose_author" name="choose_author">';
	foreach($users as $user){
		$postForm .= '<option value="'.$user["user_login"].'">'.$user["user_login"].'</option>';
	}			
	$postForm .= '</select> <b>(or)</b> <input type="text" name="postAuthor" id="postAuthor" /></td></tr>';
//	$postForm .= '<tr><td>Download and Import Attachments </td><td><input type="checkbox" name="download_available" id="download_available" value=""/></td></tr>';
	$postForm .= '<tr><td><label for="importas" > Choose what to import: </label></td>
			<td><select name="importas[]" id="importas" multiple="multiple">
			<!--<option>All</option>-->
			<option>Post</option>
			<option>Page</option>
			<option>CustomPost</option>
			</select><!--</td></tr>-->';
	$postForm .= '<!--<tr><td></td><td>--><button type="button" id="selectall" onclick="selectalloptions();">Select All</button>
			<button type="button" id="deselectall" onclick="deselectalloptions();">De-Select All</button></td></tr>';
        $postForm .= '</table>
                        <p>
                                <button type="submit" class="action addmarginright" name="Import" value="Import" align="right" onclick = "return validateFirstForm();"> Import</button>
                        </p>
                </form></br></br>';
        return $postForm;
    }
}
?>

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

function importAllPostStatus(selectedId, headerCount) {
    var select;
    var options;
    if (selectedId != 0) {
        for (var u = 0; u < headerCount; u++) {
            select = document.getElementById('mapping' + u);
            options = select.options;
            for (var o = 0; o < options.length; o++) {
                if (options[o].value == 'post_status') {
                    select.remove(o);
                }
            }
        }
    }
    else {
        for (var v = 0; v < headerCount; v++) {
            select = document.getElementById('mapping' + v);
            options = select.options;
            poststatus = 0;
            for (var o = 0; o < options.length; o++) {
                if (options[o].value == 'post_status')
                    poststatus = 1;
            }
            if (poststatus == 0) {
                var option = document.createElement("option");
                option.text = "post_status";
                select.add(option);
            }

        }
    }//exits post_status show hide
    if (selectedId == 3) {
        document.getElementById('postsPassword').style.display = "";
        document.getElementById('passwordlabel').style.display = "";
        document.getElementById('passwordhint').style.display = "";
    }
    else {
        document.getElementById('postsPassword').style.display = "none";
        document.getElementById('passwordlabel').style.display = "none";
        document.getElementById('passwordhint').style.display = "none";
    }
}

// Function for add customfield

function addcustomfield(myval, selected_id) { 
    var a = document.getElementById('h1').value;
    var importer = document.getElementById('selectedImporter').value;
    var aa = document.getElementById('h2').value;
    if (importer == 'custompost' || importer == 'post' || importer == 'page') {
       	var selected_dropdown = document.getElementById('mapping' + selected_id);
	var selected_value = selected_dropdown.value; 
	var prevoptionindex = document.getElementById('prevoptionindex').value;
	var prevoptionvalue = document.getElementById('prevoptionvalue').value;
	var mappedID = 'mapping' + selected_id;
	var add_prev_option = false;
	if(mappedID == prevoptionindex){
		add_prev_option = true;	
	}
        for (var i = 0; i < aa; i++) {
            var b = document.getElementById('mapping' + i).value;
	    var id = 'mapping' + i;
	    if(add_prev_option){
		if(i != selected_id){	
			jQuery('#'+id).append( new Option(prevoptionvalue,prevoptionvalue) );
		}
	    }
	    if(i != selected_id){
        	var x=document.getElementById('mapping' + i);
		jQuery('#'+id+' option[value="'+selected_value+'"]').remove();
	    }
            if (b == 'add_custom' + i) {
                document.getElementById('textbox' + i).style.display = "";
                document.getElementById('customspan' + i).style.display = "";
            }
            else {
                document.getElementById('textbox' + i).style.display = "none";
                document.getElementById('customspan' + i).style.display = "none";
            }
        }
	document.getElementById('prevoptionindex').value = 'mapping' + selected_id;
	var customField = selected_value.indexOf("add_custom");
	if(selected_value != '-- Select --' && customField != 0){
		document.getElementById('prevoptionvalue').value = selected_value;
	}
    }
/*    var header_count = document.getElementById('h2').value;
    for (var j = 0; j < header_count; j++) {
        var selected_value = document.getElementById('mapping' + j);
        var value1 = selected_value.options[selected_value.selectedIndex].value;
        if (j != selected_id) {
            if (myval == value1 && myval != '-- Select --') {
                var selected_dropdown = document.getElementById('mapping' + selected_id);
                selected_dropdown.selectedIndex = '-- Select --';
                showMapMessages('error', myval + ' is already selected!');
            }
	    //var x=document.getElementById('mapping' + j);
	    //x.remove(x.selectedIndex);
        }
    }*/
}

// Function for check file exist

function file_exist() {
    var requestaction = document.getElementById('requestaction').value;
    if (document.getElementById('xml_import').value == '') {
        showMessages('error', "Please attach your XML.");
        return false;
    }
    else {
	validateFirstForm();
        return true;
    }
}

//Function for check whether the import module checked or not
function validateFirstForm(){
	var selectedmodules = document.getElementById('importas');
	var selected_values = [];
	for (var i = 0; i < selectedmodules.options.length; i++) {
		if (selectedmodules.options[i].selected) {
			selected_values.push(selectedmodules.options[i].value);
		}
	}
/*	if(selected_values.length == 0){
		return false;
	}else{*/
		var requestaction = document.getElementById('requestaction').value;
		if (document.getElementById('xml_import').value == '') {
			showMessages('error', "Please attach your XML.");
			return false;
		}
		else if(selected_values.length == 0){
			showMessages('error', "Choose any options to import.");
			return false;
		}
		else {
			validateFirstForm();
			return true;
		}
//	}
}

//function show messages
function showMessages(alerttype, msg) {
    document.getElementById('showMsg').innerHTML = msg;
    document.getElementById('showMsg').className += ' ' + alerttype;
    document.getElementById('showMsg').style.display = '';
}

function showMapMessages(alerttype, msg) {
    jQuery("#showMsg").addClass("maperror");
    document.getElementById('showMsg').innerHTML = msg;
    document.getElementById('showMsg').className += ' ' + alerttype;
    document.getElementById('showMsg').style.display = '';
    jQuery("#showMsg").fadeOut(10000);
}

// Function for import xml

function import_xml() {
/*	var pagemapping = document.getElementById('Page').value;
	if(pagemapping == 'on'){
		document.getElementById('for_pages').style.display = "";
	}*/
/*    return true;
    var importer = document.getElementById('selectedImporter').value;
    var header_count = document.getElementById('h2').value;*/
/*    var array = new Array();
    var val1, val2, val3, val4, val5, val6, val7, error_msg;
    val1 = val2 = val3 = val4 = val5 = val6 = val7 = post_status_msg = 'Off';
    for (var i = 0; i < header_count; i++) {
        var e = document.getElementById("mapping" + i);
        var value = e.options[e.selectedIndex].value;
        array[i] = value;
    }
    if (importer == 'post' || importer == 'page' || importer == 'custompost') {
        var getSelectedIndex = document.getElementById('xml_importer_cat');
        var SelectedIndex = getSelectedIndex.value;
        var chk_status_in_xml;
        var post_status_msg;
        chk_status_in_xml = document.getElementById('importallwithps').value;
        if (chk_status_in_xml != 0)
            post_status_msg = 'On';

        for (var j = 0; j < array.length; j++) {
            if (array[j] == 'post_title') {
                val1 = 'On';
            }
            if (array[j] == 'post_content') {
                val2 = 'On';
            }
            if (post_status_msg == 'Off') {
                if (array[j] == 'post_status')
                    post_status_msg = 'On';
            }
        }
        if (val1 == 'On' && val2 == 'On' && SelectedIndex != '-- Select --' && post_status_msg == 'On') {
            return true;
        }
        else {
            error_msg = '';
            if (val1 == 'Off')
                error_msg += " post_title,";
            if (val2 == 'Off')
                error_msg += " post_content,";
            if (SelectedIndex == '-- Select --')
                error_msg += " post type";
            if (post_status_msg == 'Off')
                error_msg += " post_status";
            showMapMessages('error', 'Error: ' + error_msg + ' - Mandatory fields. Please map the fields to proceed.');
            return false;
        }
    }*/
}

// Select the Mapper for Post/Page
function slideonlyone(thechosenone, content_url) {
    jQuery('.newboxes2').each(function (index) {
        if (jQuery(this).attr("id") == thechosenone) {
            jQuery(this).slideDown(200);
            var id = jQuery(this).attr('id');
            jQuery("#" + id + "_img").attr('src', content_url + "/plugins/wp-advanced-importer/images/arrow_down.gif");
        }
        else {
            jQuery(this).slideUp(600);
            var id = jQuery(this).attr('id');
            jQuery("#" + id + "_img").attr('src', content_url + "/plugins/wp-advanced-importer/images/arrow_up.gif");

        }
    });
}

// Function to save plugin settings
function savePluginSettings() {
    window.setTimeout("showSuccessMessage()", 100);
    window.setTimeout("hideSuccessMessage()", 15000);
}

function showSuccessMessage() {
    document.getElementById('upgradetopro').style.display = "";
}

function hideSuccessMessage() {
    document.getElementById('upgradetopro').style.display = "none";
}

function clearmapping(){
	var total_mfields = document.getElementById('h2').value; 
	var mfields_arr = document.getElementById('mapping_fields_array').value;
	var n=mfields_arr.split(",");
	var options = '<option id="select">-- Select --</option>';
	for(var i=0;i<n.length;i++){
		options +="<option value='"+n[i]+"'>"+n[i]+"</option>";
	}
	for(var j=0;j<total_mfields;j++){
		document.getElementById('mapping'+j).innerHTML = options;
		document.getElementById('mapping'+j).innerHTML += "<option value='add_custom"+j+"'>Add Custom Field</option>";
		document.getElementById('textbox'+j).style.display = 'none';
		document.getElementById('customspan'+j).style.display = 'none';
	}	
}

// Enable/Disable WP-e-Commerce Custom Fields
function enablewpcustomfield(val){
        if(val == 'wpcustomfields'){
                document.getElementById('wpcustomfieldstr').style.display = '';
        }
        else{
                document.getElementById('wpcustomfields').checked = false;
                document.getElementById('wpcustomfieldstr').style.display = 'none';
        }
	savePluginSettings();
}

function selectalloptions(){
	var elements = document.getElementById("importas").options;
	for(var i = 0; i < elements.length; i++){
		elements[i].selected = true;
	}
}

function deselectalloptions(){
	var elements = document.getElementById("importas").options;
	for(var i = 0; i < elements.length; i++){
		if(elements[i].selected)
			elements[i].selected = false;
	}
}

function saveMapping(id){
	var pagemapping = document.getElementById('Page').value; //alert(pagemapping);
	var cpmapping = document.getElementById('CustomPost').value;
	if(pagemapping == 'on' && id == 'post_xml'){
		document.getElementById('for_pages').style.display = '';
		document.getElementById('for_posts').style.display = 'none';
		document.getElementById('for_customposts').style.display = 'none';
	}
	if(cpmapping == 'on' && id == 'page_xml'){
		document.getElementById('for_pages').style.display = 'none';
		document.getElementById('for_posts').style.display = 'none';
		document.getElementById('for_customposts').style.display = '';
	}
}


jQuery( document ).ready(function() {
		jQuery('.dropdown-toggle').dropdown('toggle');
		var checkmodule = document.getElementById('checkmodule').value;
		if(checkmodule != 'dashboard' && checkmodule !='support' && checkmodule != 'settings') {
		var get_log = document.getElementById('log').innerHTML;
		if (!jQuery.trim(jQuery('#log').html()).length) {      
		document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">'+ translateAlertString("NO LOGS YET NOW.")+'</p>';
		}

		}
		if (checkmodule == 'custompost') {
		var step = jQuery('#stepstatus').val();
		if (step == 'mapping_settings') {
		var cust_post_list_count = jQuery('#cust_post_list_count').val();
		if (cust_post_list_count == '0')
		document.getElementById('cust_post_empty').style.display = '';
		}
		}
		if (checkmodule != 'settings' && checkmodule !='support') {
		var checkfile = jQuery('#checkfile').val();
		var dir_path = jQuery('#dirpathval').val();
		var uploadedFile = jQuery('#uploadedFile').val();
		var noncekey = jQuery('#nonceKey').val();
		var get_log = jQuery('#log').val();
		var checkmodule = jQuery('#checkmodule').val();
		if (!jQuery.trim(jQuery('#log').html()).length) {
			if(checkmodule != 'dashboard') 
				document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">'+ translateAlertString("NO LOGS YET NOW.")+'</p>';
		}

		}
});

function goto_mapping(id){
	if(id == 'importfile'){
		var currentURL = document.URL; 
		var go_to_url = currentURL.replace("uploadfile","mapping_settings");
		window.location.assign(go_to_url);
		document.getElementById('sec-one').style.display='none';
		document.getElementById('sec-two').style.display='';
	}
}

function showcsvrow(){
	var i,j=0,index=0;
	mappedheaders = new Array();
	for(i=0;i<12;i++){
		csvmappingdata = document.getElementById('mapping'+i).value;
		if(csvmappingdata != '-- Select --'){
			mappedheaders[j] = csvmappingdata;
			j++;
		}
	}
	if(mappedheaders.length > index){
		var dir_path = jQuery('#dirpathval').val();
		var uploadedFile = jQuery('#uploadedFile').val();
		var noncekey = jQuery('#nonceKey').val();
		var checkmodule = jQuery('#checkmodule').val();
		if(uploadedFile != '' && select_delim != '') {
			var tmpLoc = jQuery('#tmpLoc').val();


			jQuery.ajax({
url: ajaxurl,
type: 'post',
data: {
'record_no' : '1',
'file_name' : uploadedFile,
'checkmodule' : checkmodule,
'temloc' : tmpLoc,
'dir_path' : dir_path,
'wpnonce' : noncekey,
'mappedheaders' : mappedheaders,
'action' : 'shownextrecords',
},
success: function (response) {
alert(response);
}
});
}
}
}


function showtemplatediv_wpuci(checked, div)
{
	if(checked)
		jQuery('#'+div).show();
	else
		jQuery('#'+div).hide();
}

function showtemplatediv_edit(checked, value)
{
	if(value == 'saveas')
		jQuery('#showtemplate_edit_div').show();
	else
		jQuery('#showtemplate_edit_div').hide();
}


function selectpoststatus()
{
	var poststate ='';
	var importer = document.getElementById('selectedImporter').value;
	if (importer == 'post' || importer == 'custompost')
		poststate = 12;
	if (importer == 'page' || importer == 'users')
		poststate = 11;
	if (importer == 'eshop')
		poststate = 24;	
	var ps = document.getElementById("importallwithps");
	var selectedpsindex = ps.options[ps.selectedIndex].value;
	if(selectedpsindex == 6){
		document.getElementById('globalpassword_label').style.display = "block";
		document.getElementById('globalpassword_text').style.display = "block";
		document.getElementById('globalpassword_txt').focus();
	}
	else{
		document.getElementById('globalpassword_label').style.display = "none";
		document.getElementById('globalpassword_text').style.display = "none";
	}
	var totdropdown= document.getElementById('h2').value;
	var total = parseInt(totdropdown);
	if(selectedpsindex!='0')
	{
		for(var i=0;i < poststate;i++)
		{

			dropdown = document.getElementById("fieldname"+i);
                        if(dropdown.value == "post_status"){
                        	document.getElementById("mapping"+i).selectedIndex = "0";
                        }

		}
	}
}

function changefield()
{
	var importer = document.getElementById('selectedImporter').value;
        if (importer == 'post' || importer == 'custompost')
                poststate = 12;
        if (importer == 'page' || importer == 'users')
                poststate = 11;
        if (importer == 'eshop')
                poststate = 24;
	for(var i=0;i < poststate;i++)
                {
                        dropdown = document.getElementById("fieldname"+i);
                        if(dropdown.value == "post_status"){

                        if(document.getElementById("mapping"+i).selectedIndex != 0)
                                document.getElementById("importallwithps").selectedIndex = "0";
                        }
        }
	var ps = document.getElementById("importallwithps");
        var selectedpsindex = ps.options[ps.selectedIndex].value;
        if(selectedpsindex == 0){
                document.getElementById('globalpassword_label').style.display = "none";
                document.getElementById('globalpassword_text').style.display = "none";
	}
}

// Function for add customfield

function clearMapping()
{
	var importer = document.getElementById('selectedImporter').value;
	var wpfield = document.getElementById('wpfields').value;
	for(var j=0;j<wpfield;j++){
		document.getElementById('mapping'+j).selectedIndex = "0";
	}
	if (importer != 'users') {
		var customfield = document.getElementById('customfields').value;
		for(var j=wpfield;j<parseInt(customfield);j++) {
			document.getElementById('coremapping'+j).selectedIndex = "0";
		}
		if(document.getElementById("seofields") && document.getElementById("addcorecustomfields")){
			var seofield = document.getElementById('seofields').value;
			var addcorecustomfield= document.getElementById('basic_count').value;
		}
		if(seofield != null && addcorecustomfield != null){
			for(var j=customfield;j<seofield;j++) {
				document.getElementById('seomapping'+j).selectedIndex = "0";
			}
			for(var j=seofield;j<=addcorecustomfield;j++) {
				document.getElementById('addcoremapping'+j).selectedIndex = "0";
			}
		}
		else if(document.getElementById("seofields")){
			var seofield = document.getElementById('seofields').value;
			if(seofield != null){
				for(var j=customfield;j<seofield;j++) {
					document.getElementById('seomapping'+j).selectedIndex = "0";
				}
			}
		}
		else if(document.getElementById("addcorecustomfields")){
			var addcorecustomfield= document.getElementById('basic_count').value;
			if(addcorecustomfield != null){
				for(var j=customfield;j<=addcorecustomfield;j++) {
					document.getElementById('addcoremapping'+j).selectedIndex = "0";
				}
			}
		}
	}
}


function shownotification(msg, alerts)
{
	var newclass;
	var divid = "notification_wp_csv";

	if(alerts == 'success')
		newclass = "alert alert-success";
	else if(alerts == 'danger')
		newclass = "alert alert-danger";
	else if(alerts == 'warning')
		newclass = "alert alert-warning";
	else
		newclass = "alert alert-info";

	jQuery('#'+divid).removeClass()
		jQuery('#'+divid).html(msg);
	jQuery('#'+divid).addClass(newclass);
	// Scroll
	jQuery('html,body').animate({
scrollTop: jQuery("#"+divid).offset().top},
'slow'); 
}

function translateAlertString(alertstring){
	var convertedStr = "";
	jQuery.ajax({
type:'POST',
url: ajaxurl,
async: false,
data: {
'action'      : 'trans_xmlalert_str',
'alertmsg': alertstring,
},
success:function(response)
{
convertedStr = response;
},
error: function(errorThrown){
console.log(errorThrown);
}
});
return convertedStr;
}

function import_xml() 
{
	var total = '';
	var importer = document.getElementById('selectedImporter').value;
	var header_count = document.getElementById('h2').value;
	var ps = document.getElementById("importallwithps");
	if (importer != 'users')
        var selectedpsindex = ps.options[ps.selectedIndex].value;
	var xmlarray = new Array();
	var wparray = new Array();
	var val1, val2, val3, val4, val5, val6, val7, error_msg, chk_status_in_csv, post_status_msg;
	val1 = val2 = val3 = val4 = val5 = val6 = val7 = post_status_msg = post_type = 'Off';
	if (importer == 'post' || importer == 'custompost') 
		total = 12;
	if (importer == 'page' || importer == 'users')
		total = 11;
	if (importer == 'eshop')
		total = 24;	
	for (var i = 0; i < total; i++) {
		var xmlvalue = document.getElementById("mapping" + i).value;
		var wpvalue = document.getElementById("fieldname" + i).value;
		xmlarray[i] = xmlvalue;
		wparray[i] = wpvalue;
	} 
	if (importer == 'post' || importer == 'page' || importer == 'custompost') { 
		if(importer == 'custompost') {
			var getSelectedIndex = document.getElementById('custompostlist');
			var SelectedIndex = getSelectedIndex.value;
			if( SelectedIndex != 'select')
				post_type='On';
		}

		if (selectedpsindex == 6){
                	var checkpwd = document.getElementById('globalpassword_txt').value;
                	if( checkpwd != '')
                        	val7='On';
        	}	

		chk_status_in_csv = document.getElementById('importallwithps').value;
		if (chk_status_in_csv != 0)
			post_status_msg = 'On';

		for (var j = 0; j < wparray.length; j++) {
			if (wparray[j] == 'post_title' && xmlarray[j] != '-- Select --') { 
				val1 = 'On';
			}
			if (post_status_msg == 'Off') {
				if (wparray[j] == 'post_status' && xmlarray[j] != '-- Select --')
					post_status_msg = 'On';
			}
		}

		if (selectedpsindex == 6){
	                if (importer != 'custompost' && val1 == 'On' && post_status_msg == 'On' && val7 == 'On') {
        	                return true;
                	 }
               		 else if (importer == 'custompost' && val1 == 'On'  && post_status_msg == 'On' && post_type=='On' && val7 == 'On') {
                        	 return true;
               		 }
               		 else {
                        	 error_msg = '';
                         	 if (val7 == 'Off')
                                	error_msg += "password";
                         	 if (val1 == 'Off')
                                	error_msg += " post_title";
                         	 if(importer == 'custompost') {
                                 	if (SelectedIndex == 'select')
                                        	 error_msg += " post_type";
                         	 }
                          if (post_status_msg == 'Off')
                                 error_msg += " post_status";
                        	 showMapMessages('error', 'Error: ' + error_msg + translateAlertString(' - Mandatory fields. Please map the fields to proceed.'));
                        	 return false;
               		  }

        	}
		else {
			if (importer != 'custompost' && val1 == 'On' && post_status_msg == 'On') {
				return true;
			}
			else if (importer == 'custompost' && val1 == 'On'  && post_status_msg == 'On' && post_type=='On') {
				return true;
			}
			else {
				error_msg = '';
				if (val1 == 'Off')
					error_msg += " post_title";
				if(importer == 'custompost') {
					if (SelectedIndex == 'select')
						error_msg += " post_type,";
				}
				if (post_status_msg == 'Off')
					error_msg += " post_status";
					showMapMessages('error', 'Error: ' + error_msg + translateAlertString(' - Mandatory fields. Please map the fields to proceed.'));
				return false;
			}
	 	}
	}

	// validation starts
	else if(importer == 'users'){
		for(var j=0;j<wparray.length;j++){
			if(wparray[j] == 'user_login' && xmlarray[j] != '-- Select --'){
				val1 = 'On';	
			}
			if(wparray[j] == 'user_email' && xmlarray[j] != '-- Select --'){
				val2 = 'On';
			}
			if(wparray[j] == 'role' && xmlarray[j] != '-- Select --'){
				val3 = 'On';
			}
		}
		if(val1 == 'On' && val2 == 'On' && val3 == 'On') {
			return true;
		}
		else{
			error_msg = '';
			if (val1 == 'Off')
				error_msg += "user_login";
			if (val2 == 'Off')
				error_msg += " user_email";
			if (val3 == 'Off')
				error_msg += " role";
			showMapMessages('error','Error: ' + error_msg + translateAlertString(' should be mapped.'));
			return false;
		}
	}
	// validation ends
}


function showMapMessages(alerttype, msg) {
	jQuery.ajax({
type : 'POST',
url : ajaxurl,
data : {
'action' : 'trans_alert_str',
'type' : alerttype,
'message' : msg,
},
success : function(response){
//      alert(response);
},
error : function(errorThrown){
console.log(errorThrown);
},
});
jQuery("#showMsg").addClass("maperror");
document.getElementById('showMsg').innerHTML = msg;
document.getElementById('showMsg').className += ' ' + alerttype;
document.getElementById('showMsg').style.display = '';
jQuery("#showMsg").fadeOut(10000);
}

function filezipopen()
{
	var advancemedia = document.getElementById('advance_media_handling').checked;
	if(advancemedia == true)
		document.getElementById('filezipup').style.display = '';
	else
		document.getElementById('filezipup').style.display = 'none';

}
function checkextension(filename)
{
	var allowedextension ={ '.zip' : 1 };
	var match = /\..+$/;
	var ext = filename.match(match);
	if (allowedextension[ext])
	{
		return true;
	}
	else
	{
		alert(translateAlertString("File must be .zip!"));
		//will clear the file input box.
		location.reload();
		return false;
	}

}


function inline_image_option(id) {
	document.getElementById('startbutton').disabled = false;
	var selected_option = document.getElementById(id).value;
	document.getElementById('inlineimagevalue').value = selected_option;
	if(selected_option == 'inlineimage_location') {
		var image_location = document.getElementById('imagelocation').value;
		document.getElementById('inlineimagevalue').value = image_location;
	}
}

function customimagelocation(val) {
	document.getElementById('inlineimagevalue').value = val;
}

function enableinlineimageoption() {
	var importinlineimage = document.getElementById('multiimage').checked;
	if(importinlineimage == true) {
		document.getElementById('inlineimageoption').style.display = '';
		document.getElementById('startbutton').disabled = true;
	} else {
		document.getElementById('inlineimageoption').style.display = 'none';
		document.getElementById('startbutton').disabled = false;
	}
}

function importRecordsbySettings(siteurl)
{
	var importlimit = document.getElementById('importlimit').value;
	var noncekey = document.getElementById('wpnoncekey').value; 
	var get_requested_count = importlimit; 
	var tot_no_of_records = document.getElementById('checktotal').value;
	var importas = document.getElementById('selectedImporter').value;
	var uploadedFile = document.getElementById('checkfile').value;
	var step = document.getElementById('stepstatus').value;
	var currentlimit = document.getElementById('currentlimit').value;
	var tmpCnt = document.getElementById('tmpcount').value;
	var no_of_tot_records = document.getElementById('tot_records').value;
	var importinlineimage = false;
	var imagehandling = false;
	var inline_image_location = false;
	var currentModule = document.getElementById('current_module').value;
	if(currentModule != 'users' && currentModule != 'comments') {
		importinlineimage = document.getElementById('multiimage').checked;
		imagehandling = document.getElementById('inlineimagevalue').value;
		inline_image_location = document.getElementById('inline_image_location').value;
	}
	var get_log = document.getElementById('log').innerHTML;
	document.getElementById('reportLog').style.display = '';
	document.getElementById('terminatenow').style.display = '';
	if(get_requested_count != '') {
	} else {
		document.getElementById('showMsg').style.display = "";
		document.getElementById('showMsg').innerHTML = '<p id="warning-msg" class="alert alert-warning">'+translateAlertString("Fill all mandatory fields.")+'</p>';			jQuery("#showMsg").fadeOut(10000);
		return false;
	}
	if(parseInt(get_requested_count) <= parseInt(no_of_tot_records)) {
		document.getElementById('server_request_warning').style.display = 'none';
	} else {
		document.getElementById('server_request_warning').style.display = '';
		return false;
	}
	if(get_log == '<p style="margin:15px;color:red;">NO LOGS YET NOW.</p>'){
		document.getElementById('log').innerHTML = '<p style="margin-left:10px;color:red;">'+translateAlertString("Your Import Is In Progress...")+'</p>';
		document.getElementById('startbutton').disabled = true;
	}
	document.getElementById('ajaxloader').style.display="";
	var tempCount = parseInt(tmpCnt);
	var totalCount = parseInt(tot_no_of_records);
	if(tempCount >= totalCount){
		document.getElementById('ajaxloader').style.display="none";
		document.getElementById('startbutton').style.display="none";
		document.getElementById('importagain').style.display="";
		document.getElementById('terminatenow').style.display = "none";
		return false;
	}
	var advancemedia = "";
	var dupContent = "";
	var dupTitle = "";
	if(importas == 'post' || importas == 'page' || importas == 'custompost' || importas == 'eshop'){
		advancemedia = document.getElementById('advance_media_handling').checked;
		dupContent = document.getElementById('duplicatecontent').checked;
		dupTitle = document.getElementById('duplicatetitle').checked;
	}
	var postdata = new Array();
	postdata = {'dupContent':dupContent,'dupTitle':dupTitle,'importlimit':importlimit,'limit':currentlimit,'totRecords':tot_no_of_records,'selectedImporter':importas,'uploadedFile':uploadedFile,'tmpcount':tmpCnt,'importinlineimage':importinlineimage,'inlineimagehandling':imagehandling,'inline_image_location':inline_image_location,'advance_media':advancemedia,'wpnonce':noncekey}

	var tmpLoc = document.getElementById('tmpLoc').value;
	jQuery.ajax({
type: 'POST',
url: ajaxurl,
data: {
'action'   : 'xmlimportByRequest',
'postdata' : postdata,
'siteurl'  : siteurl,
},
success:function(data) {
if(parseInt(tmpCnt) == parseInt(tot_no_of_records)){
document.getElementById('terminatenow').style.display = "none";
} 
if(parseInt(tmpCnt) < parseInt(tot_no_of_records)){
var terminate_action = document.getElementById('terminateaction').value;
currentlimit = parseInt(currentlimit)+parseInt(importlimit);
document.getElementById('currentlimit').value = currentlimit;
console.log('impLmt: '+importlimit+'totRecds: '+tot_no_of_records);
document.getElementById('tmpcount').value = parseInt(tmpCnt)+parseInt(importlimit);
if(terminate_action == 'continue'){
setTimeout(function(){importRecordsbySettings()},0);
} else {
	document.getElementById('log').innerHTML += data+'<br/>';
	if(parseInt(tmpCnt) < parseInt(tot_no_of_records)-1)
		document.getElementById('log').innerHTML += "<p style='margin-left:10px;color:red;'>"+translateAlertString('Import process has been terminated.')+"</p>";
	document.getElementById('ajaxloader').style.display="none";
	document.getElementById('startbutton').style.display = "none";
	document.getElementById('terminatenow').style.display = "none";
	document.getElementById('continuebutton').style.display = "";
	return false;
}
}else{
	document.getElementById('ajaxloader').style.display="none";
	document.getElementById('startbutton').style.display="none";
	document.getElementById('importagain').style.display="";
	return false;
}
document.getElementById('log').innerHTML += data+'<br/>';

},
error: function(errorThrown){
	       console.log(errorThrown);
       }
});
}

// Terminate import process
function terminateProcess(){
	document.getElementById('terminateaction').value = 'terminate';
}

function continueprocess() {
	var tot_no_of_records = document.getElementById('checktotal').value;
	var tmpCnt = document.getElementById('tmpcount').value;
	var currentlimit = document.getElementById('currentlimit').value;
	var importlimit = document.getElementById('importlimit').value;
	var tot_no_of_records = document.getElementById('checktotal').value;

	if (parseInt(tmpCnt) > parseInt(tot_no_of_records)) {
		document.getElementById('terminatenow').style.display = "none";
	} else {
		document.getElementById('terminatenow').style.display = "";
	}
	if (parseInt(tmpCnt) < parseInt(tot_no_of_records))
		document.getElementById('log').innerHTML += "<div style='margin-left:10px;color:green;'>"+translateAlertString(' Import process has been continued.')+"</div></br>";
	document.getElementById('ajaxloader').style.display = "";
	document.getElementById('startbutton').style.display = "";
	document.getElementById('continuebutton').style.display = "none";
	document.getElementById('terminateaction').value = 'continue';

	setTimeout(function () {
			importRecordsbySettings()
			}, 0);
}

function saveSettings(){ 
	jQuery(document).ready( function() {
			jQuery('#ShowMsg').delay(2000).fadeOut();
			});
}

function Reload(){
	window.location.reload();
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(typeof haystack[i] == 'object') {
			if(arrayCompare(haystack[i], needle)) {
				return true;
			}
		} else {
			if(haystack[i] == needle) {
				return true;
			}
		}
	}
	return false;
}

function import_again(){
	var get_current_url = document.getElementById('current_url').value;
	window.location.assign(get_current_url);
}
function sendemail2smackers(){
	var message_content = document.getElementById('message').value;
	var firstname = document.getElementById('firstname').value;
	var lastname = document.getElementById('lastname').value;
	if(message_content != '' && firstname != '' && lastname != '')
		return true;
	else
		document.getElementById('showMsg').style.display = '';
	document.getElementById('showMsg').innerHTML = '<p id="warning-msg" class="alert alert-warning">'+translateAlertString('Fill all mandatory fields.')+'</p>';
	jQuery("#showMsg").fadeOut(10000);
	return false;
}


function check_allnumeric(inputtxt)  
{  
	var numbers = /^[0-9]+$/;  
	if(inputtxt.match(numbers))  
	{  
		return true;
	}  
	else  
	{  
		if(inputtxt == '')
			alert(translateAlertString('Fill all mandatory fields.'));
		else
			alert(translateAlertString('Please enter numeric characters only'));  
		return false;  
	}  
}

function gotoback() {
	var currentURL = document.getElementById('current_url').value;
	var set_assigned_step = currentURL.replace("uploadfile","mapping_settings");
}

function addcorecustomfield(id){
	var table_id = id;
	var newrow = table_id.insertRow(-1);
	var count = document.getElementById('basic_count').value;
	count = parseInt(count)+1;
	newrow.id = 'custrow'+count;
	var filename = document.getElementById('uploadedFile').value;
	var row_count = document.getElementById('corecustomcount').value;
	jQuery.ajax({
url: ajaxurl,
type: 'post',
data: {
'filename' : filename,
'corecount' : count,
'action' : 'addwpcustomfd',
},
success: function (response) {
newrow.innerHTML = response;
row_count = parseInt(row_count) + 1;
document.getElementById('corecustomcount').value = row_count;
document.getElementById('basic_count').value = count;
}
});

}


function choose_import_method(id) {
	if(id == 'uploadfilefromcomputer') {
		document.getElementById('boxmethod1').style.border = "1px solid #ccc";
		document.getElementById('method1').style.display = '';
		document.getElementById('method1').style.height = '40px';
	}
}
function choose_import_mode(id) {
	if(id == 'importNow') {
		document.getElementById('importrightaway').style.display='';
		document.getElementById('reportLog').style.display='';
		document.getElementById('schedule').style.display='none';
	}
	if(id == 'scheduleNow') {
		document.getElementById('schedule').style.display='';
		document.getElementById('importrightaway').style.display='none';
		document.getElementById('reportLog').style.display='none';
	}
}


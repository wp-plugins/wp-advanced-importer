<div id="for_customposts" ><!-- Mapping for import CustomPosts -->
<form class="add:the-list: validate" name="secondform" id="secondform" method="post" onsubmit="return import_xml();" class="secondform">
<h3>Mapping for import custom posts</h3>
<div style="float: left; min-width: 45%">
<div style="width:50%;float:left;"><h3>Map Fields</h3></div><div style="width:50%;float:right;"><input type="button" name="remap" id="remap" value="Clear Mapping" onclick="clearmapping();" />
<a href="#" class="tooltip">
<img src="../wp-content/plugins/wp-advanced-importer/images/help.png" />
<span class="tooltipThree">
<img class="callout" src="../wp-content/plugins/wp-advanced-importer/images/callout.gif" />
<strong>Refresh to re-map fields</strong>
<img src="../wp-content/plugins/wp-advanced-importer/images/help.png" style="margin-top: 6px;float:right;" />
</span>
</a></div></br></br></br>
<div id='display_area'>
<?php 
$import_all = $import_post = $import_page = $import_custompost = 'off';
if($_POST['All']){
	$import_all = 'on';
}if($_POST['Post']){
	$import_post = 'on';
}if($_POST['Page']){
	$import_page = 'on';
}if($_POST['CustomPost']){
	$import_custompost = 'on';
}
?>
<input type="hidden" name="All" id="All" value="<?php echo $import_all; ?>">
<input type="hidden" name="Post" id="Post" value="<?php echo $import_post; ?>">
<input type="hidden" name="Page" id="Page" value="<?php echo $import_page; ?>">
<input type="hidden" name="CustomPost" id="CustomPost" value="<?php echo $import_custompost;?>">
<table style="font-size: 12px;">
<?php
$count = 0;
if ($_REQUEST['action'] == 'page') {
	unset($impXMLCE->defCols['post_category']);
	unset($impXMLCE->defCols['post_tag']);
}
foreach ($impXMLCE->headers as $key => $value) {
?>
<tr>
<td><label><?php print($value);?></label></td>
<td>
<select name="mapping<?php print($count); ?>" id="mapping<?php print($count); ?>" class='uiButton' onchange="addcustomfield(this.value,<?php echo $count; ?>);">
<option id="select">-- Select --</option>
<?php
foreach ($impXMLCE->defCols as $key1 => $value1) {
	if ($key1 == 'post_name')
		$key1 = 'post_slug';
	$strip_CF = strpos($key1, 'CF: '); //Auto mapping
	if ($strip_CF === 0) {
		$custom_key = substr($key1, 4);
	}
	?>
	<option value="<?php print($key1); ?>" <?php if($key1 == $value){ ?>selected<?php }?> >
	<?php
	if ($key1 != 'post_name'){
		print ($key1);
		$mappingFields_arr[$key1] = $key1;
	}else{
		print 'post_slug';
		$mappingFields_arr['post_slug'] = 'post_slug';
	}
	?>
	</option>
<?php
}
foreach (get_taxonomies() as $taxokey => $taxovalue) {
	if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format') {
	?>
	<option value="<?php print($taxokey); ?>"><?php print($taxovalue);?></option>
	<?php
	$mappingFields_arr[$taxovalue] = $taxovalue;
	}
}
?>
<option value="add_custom<?php print($count); ?>">Add Custom Field</option>
</select> 
<input class="customfieldtext" type="text" id="textbox<?php print($count); ?>" name="textbox<?php print($count); ?>" TITLE="Replace the default value" style="display: none;" value="<?php echo $value ?>"/>
<span style="display: none;" id="customspan<?php echo $count ?>">
<a href="#" class="tooltip">
<img src="../wp-content/plugins/wp-advanced-importer/images/help.png" />
<span class="tooltipFour">
<img class="callout" src="../wp-content/plugins/wp-advanced-importer/images/callout.gif" />
<strong>Give a name for your new custom field</strong>
<img src="../wp-content/plugins/wp-advanced-importer/images/help.png" style="margin-top: 6px;float:right;" />
</span>
</a> 
</span>
</td>
</tr>
<?php
$count++;
}
foreach($mappingFields_arr as $mkey => $mval){
$mFieldsArr .= $mkey.',';
}
$mFieldsArr = substr($mFieldsArr, 0, -1);
?>
</table>
<input type="hidden" id="mapping_fields_array" name="mapping_fields_array" value="<?php print_r($mFieldsArr); ?>"/>
</div>
<br/>
<?php
//second form exits ?>
<input type='hidden' name='filename' id='filename' value="<?php echo($_POST['filename']); ?>"/>
<input type='hidden' name='fileext' id='fileext' value="<?php echo($_POST['fileext']); ?>"/>
<?php
$explodeCsv = explode('.xml', $_FILES['xml_import']['name']);
$exactFIlename = $explodeCsv[0] . '-' . $_REQUEST['action'] . '.xml';
?>
<input type='hidden' name='uploadedFile' id='uploadedFile' value="<?php echo($impXMLCE->getUploadDirectory().'/'.$_FILES['xml_import']['name']);?>"/>
<input type='hidden' name='extension' id='extension' value="<?php echo($path_parts['extension']); ?>"/>
<button type='submit' class="action" name='custompost_xml' id='custompost_xml' value='CustomPostMapping' onclick="saveMapping(this.id);">Save Mapping</button>
</div>
</form>
</div><!-- Mapping for CustomPosts -->

<?php global $wpdb; ?>
 <div id="section8" class="securityperformance" style="display:block;">
                        <div id="data" class="databorder security-perfoemance" >
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
                        <h3 id="innertitle" colspan="2" >Debug Information:</h3>
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

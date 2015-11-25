<?php
/*********************************************************************************
* Plugin Name: WP Advanced Importer
* Description: A plugin that helps to import the data's from a XML file.
* Version: 3.0
* Author: smackcoders.com
* Author URI: https://www.smackcoders.com
*
* Copyright (C) Smackcoders 2014 - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* You can contact Smackcoders at email address info@smackcoders.com.
*********************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class UsersActions extends SkinnyActions {

	public function __construct()
	{
	}

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

	/**
	 * Mapping fields
	 */
	public $defCols = array(
			'user_login'    => null,
			'first_name'    => null,
			'last_name'     => null,
			'nickname'      => null,
			'user_email'    => null,
			'user_url'      => null,
			'aim'           => null,
			'yim'           => null,
			'jabber/gtalk'  => null,
			'role'          => null,
			'description'   => null,
			);

	public function getRoles(){
		global $wp_roles;
		$roles = array();
	        foreach($wp_roles->roles as $rkey => $rval){
			$roles[$rkey] = '';
        	        for($cnt=0;$cnt<count($rval['capabilities']);$cnt++){
                	        $findval = "level_".$cnt;
                        	if(array_key_exists($findval,$rval['capabilities']))
                                	$roles[$rkey] = $roles[$rkey].$cnt.',';
                	}
        	} 
	return $roles;
	}

	function get_availgroups($module) {
                        $groups = array();
                        if ($module == 'post' || $module == 'page' || $module == 'custompost' || $module == 'eshop') {
                                $groups = array('','core','addcore','seo');
                        }
                         if ($module == 'user') {
                                $groups = array('');
                        }
                return $groups;
        }


	/**
	 * function to map the xml file and process it
	 *
	 * @return boolean
	 */
	function processDataInWP($data_rows,$ret_array,$session_arr,$currentLimit)
	{ 	
		$impCE = new WPAdvImporter_includes_helper();
		$smack_taxo = array();
		$custom_array = array();
		$headr_count = $ret_array['h2'];
		for ($i = 0; $i < count($data_rows); $i++) {
			if (array_key_exists('mapping' . $i, $ret_array)) {
                                if($ret_array ['mapping' . $i] != '-- Select --'){
					if(array_key_exists($ret_array['mapping'.$i],$data_rows)){
                                                        $new_post[$ret_array['fieldname'.$i]] = $data_rows[$ret_array['mapping' . $i]];
                                        }
				}
			}
		}
		global $wpdb;
		$user_table = $wpdb->users;
		$UC = $wpdb->get_results("select count(ID) as users from $user_table");
		$initial_count = $UC[0]->users;
		$roles = $this->getRoles();
		$user_table = $wpdb->users;
		$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
		$keys = $wpdb->get_col( "
				SELECT meta_key
				FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%'
				ORDER BY meta_key
				LIMIT $limit" );
		foreach($new_post as $ckey => $cval){
			if($ckey == 'jabber/gtalk'){
				$data_array['jabber'] = $new_post[$ckey];
			}
			elseif($ckey == 'role'){
				$data_array_ckey = '';
				for($i=0 ; $i<=$new_post[$ckey] ; $i++){
					$data_array_ckey .= $i.",";
				}
				$data_array[$ckey]= $data_array_ckey;
			}
			else{
				$data_array[$ckey]=$new_post[$ckey];
			}
		}
		$data_array['user_pass'] = wp_generate_password( 12, false );
		$getUsers = $wpdb->get_results("select count(ID) as users from $user_table"); 
		$userscount = $getUsers[0]->users;
		foreach($roles as $rkey => $rval){
			if($rval == $data_array['role']){
				$data_array['role'] = $rkey;
			}
		}
		if(! array_key_exists($data_array['role'],$roles)){
			$data_array['role'] = 'subscriber';
		}
		$UserLogin = $data_array['user_login'];
 		$UserEmail = $data_array['user_email'];
		$user_table = $wpdb->users; 
		$user_id = '';
		$user_role= '';
		$getUserId = $wpdb->get_results("select ID from $user_table where user_email = '".$data_array["user_email"]."' or user_login = '".$data_array["user_login"]."'");
		if(!empty($getUserId)){
			$user_id = $getUserId[0]->ID;
		}
		if(is_array($getUserId) && !empty($getUserId)){
			$this->dupPostCount = $this->dupPostCount+1;
			$this->detailedLog[$currentLimit][] = "<b>Username</b> - " . $UserLogin . " , <b>E-mail</b> - " . $UserEmail . " - already exists(skipped) - found as duplicate.";
		}
		else{
			$user_id = wp_insert_user( $data_array );
			if(is_wp_error($user_id))
				return false;
			$user = new WP_User( $user_id );
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role )
					$user_role = $role;
			}
			if($user_id){
				$this->insPostCount++; // = $this->insPostCount+1;
			}

			$this->detailedLog[$currentLimit][] = "<b>Created User_ID: </b>" . $user_id ." - Success, <b>Username</b> - " . $UserLogin . " , <b>E-mail</b> - " . $UserEmail . " , <b>Role</b> - " . $user_role . " , <b>Verify Here</b> - <a href='" . get_edit_user_link( $user_id, true ) . "' target='_blank'>" . __( 'User Profile' ) . "</a>";

			$getUsers1 = $wpdb->get_results("select count(ID) as users from $user_table");
			$no_of_users = ($getUsers1[0]->users) - ($getUsers[0]->users);
			$termcount = $userscount+$no_of_users; 
			if($no_of_users > 0){
				$newUsers['user'][] = $user_id;
			}
			$current_user = wp_get_current_user();
			$admin_email = $current_user->user_email;
			$headers = "From: Administrator <$admin_email>" . "\r\n";
			$message = "Hi,You've been invited with the role of ".$user_role.". Here, your login details."."\n"."username: ".$data_array['user_login']."\n"."userpass: ".$data_array['user_pass']."\n"."Please click here to login ".wp_login_url();
			$emailaddress = $data_array['user_email'];
			$subject = 'Login Details';
			if(isset($_POST['send_password'])){
				wp_mail($emailaddress, $subject, $message, $headers);
			}
		}
		$UC1 = $wpdb->get_results("select count(ID) as users from $user_table");
		$last_count = $UC1[0]->users;
		$uploaded_file_name=$session_arr['uploadedFile'];
		$real_file_name = $session_arr['uploaded_xml_name'];
		$action=$session_arr['selectedImporter'];
		$created_records[$action][] = $user_id;
		$imported_as = 'Users';
		$keyword = $action;
	
		return $this->insPostCount;
	}

	/**
	 * The actions index method
	 * @param array $request
	 * @return array
	 */
	public function executeIndex($request)
	{
		// return an array of name value pairs to send data to the template
		$data = array();
		return $data;
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



}

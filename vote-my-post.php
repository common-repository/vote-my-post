<?php

/**
* Plugin Name: Vote My Post
* Version: 1.0
* Description: Provides up and downvote links for each of your posts
* Author: Seven Monks
*/

/**
* Start the session 
*/
session_start();

/**
* Plugin constants 
*/

// db table names
global $wpdb;
define( 'TABLE_1', $wpdb->prefix . 'posts' );
define( 'TABLE_2', $wpdb->prefix . 'post_vote_counts' );
define( 'TABLE_3', $wpdb->prefix . 'post_voting_mode' );
define( 'TABLE_4', $wpdb->prefix . 'registered_user_votes' );
define( 'TABLE_5', $wpdb->prefix . 'post_voting_style' );
define( 'TABLE_6', $wpdb->prefix . 'post_voting_settings' );

/**
* Check the version compatibility with your current WP version 
*/
global $wp_version;

$message = "This plugin requires WordPress 3.5.1 or higher. You are currently running on WordPress-" . $wp_version . ". If you want to continue using this plugin, please upgrade from <a href='http://codex.wordpress.org/Upgrading_WordPress' target='_blank'>here</a>";

// check version compatibility
if ( version_compare( $wp_version, '3.5.1', '<' ) ){
	exit( $message );
}

/**
* Activate the plugin
*/
function vmp_activate(){
	global $wpdb;
	
	// create the table to record up and down votes for each individual posts
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . TABLE_2 . " (
					id int(11) not null auto_increment,
					post_id bigint(20) null,
					upvote_count int(11) not null,
					downvote_count int(11) not null,
					primary key (id)
					) ENGINE=INNODB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
					
	// create the table to record which post is set for registered user voting and which one is for open voting
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . TABLE_3 . " (
					id int(11) not null auto_increment,
					post_id bigint(20) not null,
					vote_mode tinyint(1) not null,
					primary key (id)
					) ENGINE=INNODB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
					
	// create the table to record which registered user has voted for which post
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . TABLE_4 . " (
					id int(11) not null auto_increment,
					user_id bigint(20) not null,
					post_id bigint(20) not null,
					primary key (id)
					) ENGINE=INNODB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
					
	// create the table to record the positioning and orientation of voting links
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . TABLE_5 . " (
					id int(11) not null auto_increment,
					position tinyint(1) not null,
					orientation tinyint(1) not null,
					primary key (id)
					) ENGINE=INNODB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
					
	// create the table to record the positioning and orientation of voting links
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . TABLE_6 . " (
					id int not null auto_increment,
					repeat_voting tinyint not null default 1,
					voting_interval int not null default 120,
					row_per_page smallint not null default 5,
					max_link smallint not null default 7,
					neighbour smallint not null default 3,
					primary key (id)
					) ENGINE=INNODB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
					
	// load settings
	$settings = array();
	$settings = vmp_fetch_settings();
	
	define( 'REGISTERED_USER_REPEAT_VOTING', $settings[ 0 ] );
	define( 'COOKIE_EXPIRATION_TIME', $settings[ 1 ] );
	define( 'VMP_ROW_PER_PAGE', $settings[ 2 ] );
	define( 'VMP_MAX_PAGE_LINK', $settings[ 3 ] );
	define( 'VMP_NEIGHBOR', $settings[ 4 ] );
}
add_action( 'admin_init', 'vmp_activate' );

/**
* Deactivate the plugin
*/
function vmp_deactivate(){
	
}
register_deactivation_hook( __FILE__, 'vmp_deactivate' );

/**
* Add plugin menu to the dashboard menu panel
*/
function vmp_add_plugin_menu_tab(){
	add_menu_page( 'VMP', 'VMP', 'manage_options', 'add-vmp-menu-tab', 'vmp_add_vote_my_post_menu_tab', plugins_url( '/images/vmp-icon.gif', __FILE__ ) );
	add_submenu_page( 'add-vmp-menu-tab', 'Settings', 'Settings', 'manage_options', 'add-vmp-settings-menu-tab', 'vmp_add_settings_menu_tab' );
	add_submenu_page( 'add-vmp-menu-tab', 'Set Post Vote Mode', 'Set Post Vote Mode', 'manage_options', 'add-vmp-post-vote-mode-menu-tab', 'vmp_add_post_vote_mode_menu_tab' );
	add_submenu_page( 'add-vmp-menu-tab', 'Set Post Vote Style', 'Set Post Vote Style', 'manage_options', 'add-vmp-post-vote-style-menu-tab', 'vmp_add_post_vote_style_menu_tab' );
}
add_action( 'admin_menu', 'vmp_add_plugin_menu_tab' );

/**
* Display the main plugin page for managing individual post votes
*/
function vmp_add_vote_my_post_menu_tab(){
	if ( ! current_user_can( 'manage_options' ) ){
		exit( 'You don\'t have sufficient permissions to access this page' );
	}
	
	require_once( 'vmp-management-panel.php' );
}

/**
* Display the plugin page for user optional settings
*/
function vmp_add_settings_menu_tab(){
	if ( ! current_user_can( 'manage_options' ) ){
		exit( 'You don\'t have sufficient permissions to access this page' );
	}
	
	require_once( 'vmp-settings-panel.php' );
}

/**
* Display the plugin page for setting the voting mode for each post
*/
function vmp_add_post_vote_mode_menu_tab(){
	if ( ! current_user_can( 'manage_options' ) ){
		exit( 'You don\'t have sufficient permissions to access this page' );
	}
	
	require_once( 'vmp-set-post-vote-mode-panel.php' );
}

/**
* Display the plugin page for setting the position and orientation of voting links
*/
function vmp_add_post_vote_style_menu_tab(){
	if ( ! current_user_can( 'manage_options' ) ){
		exit( 'You don\'t have sufficient permissions to access this page' );
	}
	
	require_once( 'vmp-set-post-vote-style-panel.php' );
}

/**
* Add the voting links style
*/
function vmp_add_style(){
	wp_register_style( 'vmp-style', plugins_url( '/css/vmp-style.css', __FILE__ ) );
	wp_enqueue_style( 'vmp-style' );
}
add_action( 'admin_enqueue_scripts', 'vmp_add_style' );
add_action( 'wp_enqueue_scripts', 'vmp_add_style' );

/**
* Add the ajax vote counter and vote reset counter
*/
function vmp_add_scripts(){
	global $post;
	
	wp_enqueue_script( 'jquery' );
	
	wp_register_script( 'all-scripts', plugins_url( '/js/all-scripts.js', __FILE__ ), array( 'jquery' ), NULL, TRUE );
	wp_enqueue_script( 'all-scripts' );
	wp_localize_script( 'all-scripts', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'post_id' => $post->ID ) );
}
add_action( 'wp_enqueue_scripts', 'vmp_add_scripts' );
add_action( 'admin_enqueue_scripts', 'vmp_add_scripts' );

/**
* Fetch the latest positioning and orientation style
*/
function vmp_fetch_latest_post_voting_link_style(){
	global $wpdb;
	
	// fetch the latest style
	$query = "SELECT * FROM `" . TABLE_5 . "` WHERE `id` = ( SELECT MAX( `id` ) FROM `" . TABLE_5 . "` )";
	$latest_style = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $latest_style ) && empty( $latest_style ) ){ // no style found
		return FALSE;
	}else{ // existing style found
		$positioning = $latest_style[ 0 ][ 'position' ];
		$orientation = $latest_style[ 0 ][ 'orientation' ];
		
		return array( 'positioning' => $positioning, 'orientation' => $orientation );	
	}
}

/**
* Handle post voting links' positioning and orientation ajax submit event
*/
function vmp_set_post_voting_link_style(){
	global $wpdb;
	$html = '';
	
	// verify nonce
	$_wpnonce = 'set_post_voting_link_position_and_orientation';
	if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], $_wpnonce ) ){
		wp_die( 'Unauthorized access attempt' );
	}
	
	// server side user input validation
	if ( ! isset( $_REQUEST[ 'post_voting_link_position' ] ) ){
		$html .= '<span style="font-weight:bold;color:#f00;">Must specify a position<br/>';
	}
	if ( ! isset( $_REQUEST[ 'post_voting_link_orientation' ] ) ){
		$html .= '<span style="font-weight:bold;color:#f00;">Must specify an orientation<br/>';
	}
	
	// user input validation successful
	if ( empty( $html ) ){
		// get the existing style
		$query = "SELECT MAX( `id` ) AS `max_id` FROM `" . TABLE_5 . "`";
		$max_id = $wpdb->get_results( $query, ARRAY_A );		
		
		if ( is_array( $max_id ) && empty( $max_id[ 0 ][ 'max_id' ] ) ){ // no style found
			// add the new style
			$query = $wpdb->prepare( "INSERT INTO `" . TABLE_5 . "` VALUES ( NULL, %d, %d )", $_REQUEST[ 'post_voting_link_position' ], $_REQUEST[ 'post_voting_link_orientation' ] );	
			
		}else{ // existing style found
			// update existing style
			$query = $wpdb->prepare( "UPDATE `" . TABLE_5 . "` SET `position` = %d, `orientation` = %d WHERE `id` = %d", $_REQUEST[ 'post_voting_link_position' ], $_REQUEST[ 'post_voting_link_orientation' ], $max_id[ 0 ][ 'max_id' ] );
		}
		$flag = $wpdb->query( $query );
		
		if ( FALSE === $flag ){// error in sql query execution
			$html = 'Adding post voting links style failed';
		}else{ // sql query execution successful
			$html = 'Successfully added post voting links style';
		}
	}
	
	echo json_encode( array( 'html' => $html ) );
	
	die();
}
add_action( 'wp_ajax_set_post_voting_link_position_and_orientation_090713', 'vmp_set_post_voting_link_style' );

/**
* Fetch all posts
*/
function vmp_fetch_all_posts(){
	global $wpdb;
	
	$query = "SELECT `ID`, `post_title` FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'post' AND `post_status` = 'publish'";
	
	$total_page_links = vmp_get_total_page_links( $query );
	$posts = vmp_paginate( $query, $total_page_links );
	
	return array( 'posts' => $posts, 'total_page_links' => $total_page_links );
}

/**
* Fetch existing voting mode for a post
*/
function vmp_fetch_existing_voting_mode( $post_id ){
	global $wpdb;
	
	$query = "SELECT `vote_mode` FROM `" . TABLE_3 . "` WHERE `post_id` = ". $post_id;
	$result = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $result ) && empty( $result ) ){
		return FALSE;
	}else{
		return $result[ 0 ][ 'vote_mode' ];
	}
}

/**
* Handle a single post's voting mode
*/
function vmp_set_single_post_voting_mode(){
	global $wpdb;
	$html = '';
	
	if ( 1 < $_REQUEST[ 'post_voting_mode' ] ){ // user didn't choose any voting mode
		$html = 'Must select a voting mode';
	}
	
	if ( empty( $html ) ){
		// fetch the voting mode for this post
		$query = "SELECT `id` FROM `" . TABLE_3 . "` WHERE `post_id` = " . $_REQUEST[ 'post_id' ];
		$result = $wpdb->get_results( $query, ARRAY_A );
		
		if ( is_array( $result ) && empty( $result ) ){ // no record for this post
			// add record for this post
			$query = $wpdb->prepare( "INSERT INTO `" . TABLE_3 . "` VALUES ( NULL, %d, %d )", $_REQUEST[ 'post_id' ], $_REQUEST[ 'post_voting_mode' ] );
		}else{
			// update voting mode for this post
			$query = $wpdb->prepare( "UPDATE `" . TABLE_3 . "` SET `vote_mode` = %d WHERE `id` = %d", $_REQUEST[ 'post_voting_mode' ], $result[ 0 ][ 'id' ] );
		}
		$flag = $wpdb->query( $query );
		
		if ( FALSE === $flag ){ // error in sql query execution
			$html = 'Adding voting mode for this post failed';
		}else{ // sql query execution successful
			$html = 'Successfully added voting mode for this post';
		}
	}	
	
	echo json_encode( array( 'html' => $html ) );
	
	die();
}
add_action( 'wp_ajax_set_this_post_voting_mode_090813', 'vmp_set_single_post_voting_mode' );

/**
* Handle toggling voting mode for multiple posts
*/
function vmp_set_multiple_post_voting_mode(){
	global $wpdb;
	$html = '';
	$count = 1;
	$selected = FALSE;
	
	$_wpnonce = 'set_multiple_post_voting_mode';
	if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], $_wpnonce ) ){
		wp_die( 'Unauthorized access attempt' );
	}
	
	$post_voting_modes = $_REQUEST[ 'post_voting_modes' ];
	$post_ids = $_REQUEST[ 'post_ids' ];
	
	foreach ( $post_ids as $key => $value ){ // loop through all the posts
		if ( isset( $_REQUEST[ 'post_selector_' . $count ] ) ){ // if only this post is selected
			$selected = TRUE;			
			// fetch the voting mode for this post
			$query = "SELECT `id` FROM `" . TABLE_3 . "` WHERE `post_id` = " . $post_ids[ $key ];
			$result = $wpdb->get_results( $query, ARRAY_A );
			
			if ( is_array( $result ) && empty( $result ) ){ // no record for this post
				// add record for this post
				$query = $wpdb->prepare( "INSERT INTO `" . TABLE_3 . "` VALUES ( NULL, %d, %d )", $post_ids[ $key ], $post_voting_modes[ $key ] );
			}else{
				// update voting mode for this post
				$query = $wpdb->prepare( "UPDATE `" . TABLE_3 . "` SET `vote_mode` = %d WHERE `id` = %d", $post_voting_modes[ $key ], $result[ 0 ][ 'id' ] );
			}
			$flag = $wpdb->query( $query );
			
			if ( FALSE === $flag ){ // error in sql query execution
				$html = 'Adding voting mode for post with id ' . $post_ids[ $key ] . ' failed';
			}	 
		}
		
		$count++;
	}
	
	if ( FALSE === $selected ){ // user selected no posts
		$html = 'Select at least one post';
	}
	
	if ( TRUE === $selected && FALSE !== $flag ){ // operation successful
		$html = 'Voting mode for the selected posts added Successfully';
	}
	
	echo json_encode( array( 'html' => $html ) );
	
	die();
}
add_action( 'wp_ajax_set_multiple_post_voting_mode_090813', 'vmp_set_multiple_post_voting_mode' );

/**
* Fetch all the posts voted so far
*/
function vmp_fetch_all_voted_posts(){
	global $wpdb;
	
	$query = "SELECT `TBL_1`.`post_title`, `TBL_2`.* FROM `" . TABLE_2 . "` AS `TBL_2` INNER JOIN `" . $wpdb->prefix . "posts` AS `TBL_1` ON `TBL_2`.`post_id` = `TBL_1`.`ID`";
	
	$total_page_links = vmp_get_total_page_links( $query );
	$posts = vmp_paginate( $query, $total_page_links );
	
	return array( 'posts' => $posts, 'total_page_links' => $total_page_links );
}

/**
* Fetch post name
*/
function vmp_get_post_name( $id ){
	global $wpdb;
	
	$query = "SELECT post_name FROM " . $wpdb->prefix . "posts WHERE ID = " . $id;
	$result = $wpdb->get_results( $query, ARRAY_A );
	
	$post_name = ucwords( str_replace( '-', ' ', $result[ 0 ][ 'post_name' ] ) );
	
	return $post_name;
}

/**
* Handle the ajax vote count
*/
function vmp_count_vote(){	
	global $wpdb;
	$response_type = '';
	$html = '';
	$voting_mode = 0; // default
	
	// catch the ajax request params
	$post_id = $_REQUEST[ 'post_id' ];
	$voting_type = $_REQUEST[ 'type' ];
	
	// check the voting mode of this post
	$query = "SELECT `vote_mode` FROM `" . TABLE_3 . "` WHERE `post_id` = " . $post_id;
	$post_voting_mode = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $post_voting_mode ) && empty( $post_voting_mode ) ){ // voting mode is not yet set for this post
		// consider voting mode for this post as open. Anybody can vote.		
	}else{ // voting mode is set
		$voting_mode = $post_voting_mode[ 0 ][ 'vote_mode' ];
	}
	
	// give privilage accordingly
	if ( 1 == $voting_mode ){ // restricted voting mode, only a registered user can vote
		if ( ! is_user_logged_in() ){ // check if user is logged in
			$response_type = 'error';
			$html = 'You need to log in or register to vote for this post';	
		}else{ // logged in user
			// get the user id for the currently logged-in user 
			$current_user = wp_get_current_user();
	
			if ( 1 == REGISTERED_USER_REPEAT_VOTING ){ // registered user can vote same post more than once
				if ( isset( $_COOKIE[ 'post_' . $post_id ] ) && ! empty( $_COOKIE[ 'post_' . $post_id ] ) ){ // cannot revote till a specified time elapses
					$response_type = 'error';
					$html = 'You\'ve just voted this post. Please try after some time';
				}else{					
					// record vote for this post
					$flag = vmp_record_vote( $post_id, $voting_type );
					
					if ( FALSE === $flag ){
						$response_type = 'error';
						$html = 'Your vote was not casted. Please try again';
					}else{
						$response_type = 'success';
						
						setcookie( 'post_' . $post_id, 'voted', time() + COOKIE_EXPIRATION_TIME ); // set timer
						
						$query = "SELECT * FROM `" . TABLE_2 . "` WHERE `post_id` = " . $post_id;
						$result = $wpdb->get_results( $query, ARRAY_A );
						
						if ( 'upvote' == $voting_type ){
							$html = $result[ 0 ][ 'upvote_count' ];
						}else{
							$html = $result[ 0 ][ 'downvote_count' ];
						}
					}
				}
			}else{ // registered user can vote same post only once
				// check if (s)he has already voted for this post
				$query = "SELECT `id` FROM `" . TABLE_4 . "` WHERE post_id = " . $post_id . " AND user_id = " . $current_user->ID;
				$result = $wpdb->get_results( $query, ARRAY_A );
				
				if ( is_array( $result ) && ! empty( $result ) ){ // (s)he has voted already
					$response_type = 'error';
					$html = 'You have already voted for this post';
				}else{ // (s)he hasn't voted yet for this post
					// track registered user voting activity
					$query = $wpdb->prepare( "INSERT INTO `" . TABLE_4 . "` VALUES ( NULL, %d, %d )", $current_user->ID, $post_id );
					$wpdb->query( $query );
										
					// record vote for this post
					$flag = vmp_record_vote( $post_id, $voting_type );
					
					if ( FALSE === $flag ){
						$response_type = 'error';
						$html = 'Your vote was not casted. Please try again';
					}else{
						$response_type = 'success';
						
						setcookie( 'post_' . $post_id, 'voted', time() + COOKIE_EXPIRATION_TIME ); // set timer
						
						$query = "SELECT * FROM `" . TABLE_2 . "` WHERE `post_id` = " . $post_id;
						$result = $wpdb->get_results( $query, ARRAY_A );
						
						if ( 'upvote' == $voting_type ){
							$html = $result[ 0 ][ 'upvote_count' ];
						}else{
							$html = $result[ 0 ][ 'downvote_count' ];
						}
					}
				}
			}
		}
	}else{ // open vote mode
		if ( isset( $_COOKIE[ 'post_' . $post_id ] ) && ! empty( $_COOKIE[ 'post_' . $post_id ] ) ){ // cannot revote till a specified time elapses
			$response_type = 'error';
			$html = 'You\'ve just voted this post. Please try after some time';
		}else{			
			// record vote for this post
			$flag = vmp_record_vote( $post_id, $voting_type );
			
			if ( FALSE === $flag ){
				$response_type = 'error';
				$html = 'Your vote was not casted. Please try again';
			}else{
				$response_type = 'success';
				
				setcookie( 'post_' . $post_id, 'voted', time() + COOKIE_EXPIRATION_TIME ); // set timer
				
				$query = "SELECT * FROM `" . TABLE_2 . "` WHERE `post_id` = " . $post_id;
				$result = $wpdb->get_results( $query, ARRAY_A );
				
				if ( 'upvote' == $voting_type ){
					$html = $result[ 0 ][ 'upvote_count' ];
				}else{
					$html = $result[ 0 ][ 'downvote_count' ];
				}
			}
		}
	}
	
	echo json_encode( array( 'type' => $response_type, 'html' => $html ) );
		
	die();
}
add_action( 'wp_ajax_count_vote_090813', 'vmp_count_vote' );
add_action( 'wp_ajax_nopriv_count_vote_090813', 'vmp_count_vote' );

/**
* Record a vote 
*/
function vmp_record_vote( $post_id, $voting_type ){
	global $wpdb;
	$upvote_count = 0; // default
	$downvote_count = 0; // default
	
	$query = "SELECT * FROM `" . TABLE_2 . "` WHERE `post_id` = " . $post_id;
	$result = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $result ) && empty( $result ) ){ // no vote has been casted for this post
		// insert
		if ( 'upvote' == $voting_type ){
			$upvote_count = 1;
		}else{
			$downvote_count = 1;
		}
		$query = $wpdb->prepare( "INSERT INTO `" . TABLE_2 . "` VALUES ( NULL, %d, %d, %d )", $post_id, $upvote_count, $downvote_count );
		$flag = $wpdb->query( $query );
	}else{ // votes were already casted for this post
		if ( 'upvote' == $voting_type ){
			$upvote_count = intval( $result[ 0 ][ 'upvote_count' ] ) + 1;
			$downvote_count = intval( $result[ 0 ][ 'downvote_count' ] );
		}else{
			$upvote_count = intval( $result[ 0 ][ 'upvote_count' ] );
			$downvote_count = intval( $result[ 0 ][ 'downvote_count' ] ) + 1;
		}
		$query = $wpdb->prepare( "UPDATE `" . TABLE_2 . "` SET `upvote_count` = %d, `downvote_count` = %d WHERE `id` = %d ", $upvote_count, $downvote_count, $result[ 0 ][ 'id' ] ); // update vote counts
		$flag = $wpdb->query( $query );
	}
	
	if ( FALSE == $flag ){ // query execution failed
		return FALSE;
	}else{
		return TRUE;
	}
}

/**
* Create voting links
*/
function vmp_create_voting_links( $orientation ){
	global $post;
	global $wpdb;
	$up_count = 0;
	$down_count = 0;
	
	$post_id = $post->ID;	
	
	$query = "SELECT * FROM `" . TABLE_2 . "` WHERE `post_id` = " . $post_id;
	$result = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $result ) && ! empty( $result ) ){
		$up_count = $result[ 0 ][ 'upvote_count' ];
		$down_count = $result[ 0 ][ 'downvote_count' ];
	}
	
	if ( 0 == $orientation ){
		$class_1 = 'main_wrapper_left';
		$class_2 = 'voted_left';
	}else{
		$class_1 = 'main_wrapper_right';
		$class_2 = 'voted_right';
	}
	
	$html = "<div class='" . $class_1 . "'>
			 	<div class='first'><a href='javascript:void(0)' id='up_count' title='Click to like this post'><img valign='middle' src='" . plugins_url( '/images/upvote.gif', __FILE__ ) . "'/></a><div class='counter' id='vmp_up_counter'>" . $up_count . "</div></div>
				<div class='other'><a href='javascript:void(0)' id='down_count' title='Click to dislike this post'><img valign='middle' src='" . plugins_url( '/images/downvote.gif', __FILE__ ) . "'/></a><div class='counter' id='vmp_down_counter'>" . $down_count . "</div></div>
			 </div>
			 <div class='" . $class_2 . "' id='vmp_message'></div>
			 <br/><br/><br/>
			";
			 
	return $html;
}

/**
* Add the voting links
*/
function vmp_add_voting_links( $content ){
	global $wpdb;
	
	$query = "SELECT * FROM `" . TABLE_5 . "` WHERE `id` = ( SELECT MAX( `id` ) FROM `" . TABLE_5 . "` )";
	$post_voting_style = $wpdb->get_results( $query, ARRAY_A );
	
	$position = $post_voting_style[ 0 ][ 'position' ];
	$orientation = $post_voting_style[ 0 ][ 'orientation' ];
	
	if ( is_single() ){
		if ( 0 == $position ){
			return vmp_create_voting_links( $orientation ) . $content;
		}else{
			return $content . vmp_create_voting_links( $orientation );	
		}
	}else{
		return $content;
	}
}
add_filter( 'the_content', 'vmp_add_voting_links' );

/**
* Reset a selected post vote counter
*/
function vmp_reset_selected_post_vote_counter(){
	global $wpdb;
	
	$post_id = $_REQUEST[ 'id' ];
	
	$query = $wpdb->prepare( "UPDATE `" . TABLE_2 . "` SET `upvote_count` = %d, `downvote_count` = %d WHERE `post_id` = %d", 0, 0, $post_id );
	$flag = $wpdb->query( $query );
	
	$query = "SELECT COUNT( `id` ) FROM `" . TABLE_4 . "` WHERE `post_id` = " . $post_id;
	$count = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $count ) && ! empty( $count ) ){
		$query = "DELETE FROM `" . TABLE_4 . "` WHERE `post_id` = " . $post_id;
		$flag = $wpdb->query( $query );	
	}
	
	if ( FALSE === $flag ){
		$type = 'error';
		$html = 'Resetting the selected post vote counters failed';
	}else{
		$type = 'success';
		
		$query = "SELECT `TBL_1`.`post_title`, `TBL_2`.* FROM `" . TABLE_2 . "` AS `TBL_2` INNER JOIN `" . $wpdb->prefix . "posts` AS `TBL_1` ON `TBL_2`.`post_id` = `TBL_1`.`ID` WHERE `TBL_2`.`post_id` = " . $post_id;
		$post = $wpdb->get_results( $query, ARRAY_A );
		$id = $post[0][ 'id' ];
		$post_title = $post[ 0 ][ 'post_title' ];
		$upvote_count = $post[ 0 ][ 'upvote_count' ];
		$downvote_count = $post[ 0 ][ 'downvote_count' ];
		
		$html = '
					<td width="20%" align="left" valign="top">
						<input type="checkbox" name="post_selectors[]" class="post_selector"/>
						<input type="hidden" name="ids[]" value="' . $id . '"/>
						<input type="hidden" name="post_ids[]" value="' . $post_id . '"/>
					</td>
					<td width="20%" align="left" valign="top">' . $post_title . '</td>
					<td width="20%" align="left" valign="top">' . $upvote_count . '</td>
					<td width="20%" align="left" valign="top">' . $upvote_count . '</td>
					<td width="20%" align="left" valign="top">
						<a href="javascript:void(0);" class="button button-primary button-large reset_post_vote_counter">Reset</a>
					</td>
				';
	}
	
	echo json_encode( array( 'type' => $type, 'html' => $html ) );
	
	die();
}
add_action( 'wp_ajax_reset_this_post_vote_counter_092113', 'vmp_reset_selected_post_vote_counter' );

/**
* Handle reset vote counters for multiple selected posts
*/
function vmp_reset_multiple_post_vote_counters(){
	global $wpdb;
	$html = '';
	$selected = FALSE;
	
	$_wpnonce = 'reset_multiple_post_vote_counters';
	if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], $_wpnonce ) ){
		wp_die( 'Unauthorized access attempt' );
	}
	
	/*var_dump( $_REQUEST );
	exit();*/
	$post_selectors = $_REQUEST[ 'post_selectors' ];
	$ids = $_REQUEST[ 'ids' ];
	$post_ids = $_REQUEST[ 'post_ids' ];
	
	foreach ( $ids as $key => $value ){ // loop through all the posts
		if ( isset( $post_selectors[ $key ] ) ){ // if only this post is selected
			$selected = TRUE;			
			$query = $wpdb->prepare( "UPDATE `" . TABLE_2 . "` SET `upvote_count` = %d, `downvote_count` = %d WHERE `id` = %d", 0, 0, $value );
			$flag = $wpdb->query( $query );
			
			$query = "SELECT COUNT( `id` ) FROM `" . TABLE_4 . "` WHERE `post_id` = " . $post_ids[ $key ];
			$count = $wpdb->get_results( $query, ARRAY_A );
			
			if ( is_array( $count ) && ! empty( $count ) ){
				$query = "DELETE FROM `" . TABLE_4 . "` WHERE `post_id` = " . $post_ids[ $key ];
				$flag = $wpdb->query( $query );	
			}
						
			if ( FALSE === $flag ){ // error in sql query execution
				$html .= 'Reseting vote counter for post with id ' . $post_ids[ $key ] . ' failed';
			}	 
		}
	}
	
	if ( FALSE === $selected ){ // user selected no posts
		$html = 'Select at least one post';
	}
	
	if ( TRUE === $selected && FALSE !== $flag ){ // operation successful
		$html = 'Vote counters for the selected posts were reset successfully';
	}
	
	vmp_push_messages_in_session( $html );
	session_write_close();
	
	wp_redirect( $_SERVER[ 'HTTP_REFERER' ] );
	
	die();
}
add_action( 'admin_action_reset_multiple_post_vote_counters_092113', 'vmp_reset_multiple_post_vote_counters' );

/**
* Fetch the default values from the settings table
*/
function vmp_fetch_settings(){
	global $wpdb;
	$settings = array();
	
	// first check if the table has any user settings or empty
	$query = "SELECT * FROM `" . TABLE_6 . "` WHERE 1 = 1";
	$result = $wpdb->get_results( $query, ARRAY_A );
	
	if ( is_array( $result ) && ! empty( $result ) ){ // table contains user settings
		$settings = array( $result[ 0 ][ 'repeat_voting' ], $result[ 0 ][ 'voting_interval' ], $result[ 0 ][ 'row_per_page' ], $result[ 0 ][ 'max_link' ], $result[ 0 ][ 'neighbour' ] );	
	}else{ // return default settings
		$query = "SHOW COLUMNS FROM `" . TABLE_6 . "`";
		$results = $wpdb->get_results( $query, ARRAY_A );
		
		$count = 1;
		foreach ( $results as $result ){
			if ( 1 == $count ){
				$count++;
				continue;
			}else{
				$count++;
				array_push( $settings, $result[ 'Default' ] );
			}
		}	
	}
	
	return $settings;
}

/**
* Add user settings
*/
function vmp_add_user_settings(){
	global $wpdb;
	$html = '';
	
	$_wpnonce = 'set_plugin_options';
	if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], $_wpnonce ) ){
		wp_die( 'Unauthorized access attempt' );
	}
	
	$vmp_multiple_voting = $_REQUEST[ 'vmp_multiple_voting' ];
	$vmp_consecutive_voting_interval = $_REQUEST[ 'vmp_consecutive_voting_interval' ];
	$vmp_rows_per_page = $_REQUEST[ 'vmp_rows_per_page' ];
	$vmp_max_page_links = $_REQUEST[ 'vmp_max_page_links' ];
	$vmp_neighbours = $_REQUEST[ 'vmp_neighbours' ];
	
	// server side validation
	if ( empty( $vmp_consecutive_voting_interval ) ){
		$html .= 'A consecutive voting interval is required<br/>';
	}
	if ( empty( $vmp_rows_per_page ) ){
		$html .= 'Rows to display per page is required<br/>'; 
	}	
	if ( empty( $vmp_max_page_links ) ){
		$html .= 'Maximum number of page links to display is required<br/>';
	}
	if ( empty( $vmp_neighbours ) ){
		$html .= 'Maximum number of neighbouring links to display is required<br/>';
	}
	
	if ( empty( $html ) ){ // no validation error raised
		$multiple_voting = isset( $vmp_multiple_voting[ 0 ] ) ? 1 : 0;
		
		// first check if the table has any user settings or empty
		$query = "SELECT * FROM `" . TABLE_6 . "` WHERE 1 = 1";
		$result = $wpdb->get_results( $query, ARRAY_A );
		
		if ( is_array( $result ) && ! empty( $result ) ){ // user setting already exists, overwrite
			$query = $wpdb->prepare( "UPDATE `" . TABLE_6 . "` SET `repeat_voting` = %d, `voting_interval` = %d, `row_per_page` = %d, `max_link` = %d, `neighbour` = %d WHERE `id` = 1", $vmp_multiple_voting, $vmp_consecutive_voting_interval, $vmp_rows_per_page, $vmp_max_page_links, $vmp_neighbours );
			$flag = $wpdb->query( $query );
		}else{ // add user settings
			$query = $wpdb->prepare( "INSERT INTO `" . TABLE_6 . "` VALUES ( NULL, %d, %d, %d, %d, %d )", $vmp_multiple_voting, $vmp_consecutive_voting_interval, $vmp_rows_per_page, $vmp_max_page_links, $vmp_neighbours );
			$flag = $wpdb->query( $query );
		}
		
		if ( FALSE === $flag ){
			$html = 'Adding settings failed';
		}else{
			$html = 'Successfully added settings';
		}
	}
	
	vmp_push_messages_in_session( $html );
	session_write_close();
	
	wp_redirect( $_SERVER[ 'HTTP_REFERER' ] );
	
	die();
}
add_action( 'admin_action_set_plugin_options_092113', 'vmp_add_user_settings' );

/**
* Push raised messages in session
*/
function vmp_push_messages_in_session( $messages ){
	if ( ! isset( $_SESSION[ 'messages' ] ) ){
		$_SESSION[ 'messages' ] = $messages;
	}else if ( isset( $_SESSION[ 'messages' ] ) && empty( $_SESSION[ 'messages' ] ) ){
		$_SESSION[ 'messages' ] = $messages;
	}else{
		unset( $_SESSION[ 'messages' ] );
		$_SESSION[ 'messages' ] = $messages;
	}
}

/**
* Pagination methods
*/
function vmp_get_total_page_links( $query ){
	global $wpdb;
	
	$result = $wpdb->get_results( $query, ARRAY_A );	
	$total_rows = count( $result );	
	$total_page_links = ( VMP_ROW_PER_PAGE >= $total_rows ) ? FALSE : ceil( $total_rows / VMP_ROW_PER_PAGE );
	
	return $total_page_links;
}

function vmp_paginate( $query, $total_page_links ){
	global $wpdb;
	
	$p = $_REQUEST[ 'p' ];
		
	if ( $total_page_links ){ // PAGINATION REQUIRED; MODIFY THE MOTHER QUERY
		$start = ( ! $p ) ? 0 : ( $p - 1 ) * VMP_ROW_PER_PAGE;
		
		$query .= " LIMIT " . $start . ", " . VMP_ROW_PER_PAGE;	
	}
	
	$results = $wpdb->get_results( $query, ARRAY_A );
		
	return $results;
}

function vmp_display_page_links( $total_page_links, $referer_mode ){
	$p = $_REQUEST[ 'p' ];
	
	$page_links = ( VMP_MAX_PAGE_LINK >= $total_page_links ) ? $total_page_links : VMP_MAX_PAGE_LINK;
	
	if ( ! $p ){
		$lower_limit = 1;
		$upper_limit = $page_links;
	}else if ( $p == $total_page_links ){
		$lower_limit = ( $p - ( $page_links - 1 ) );
		$upper_limit = $p;
	}
	else{
		$lower_limit = ( 1 >= ( $p - VMP_NEIGHBOR ) ) ? 1 : ( $p - VMP_NEIGHBOR );
		
		if ( $total_page_links < ( $p + VMP_NEIGHBOR ) ){
			$upper_limit = $total_page_links;
		}else{
			if ( $page_links > ( $p + VMP_NEIGHBOR ) ){
				$upper_limit = ( $p + VMP_NEIGHBOR ) + ( $page_links - ( $p + VMP_NEIGHBOR ) );
			}
			else{
				$upper_limit = ( $p + VMP_NEIGHBOR );
			}
		}
	}
	
	if ( 'set_post_voting_mode' == $referer_mode ){
		$redirect_url = admin_url( 'admin.php?page=add-vmp-post-vote-mode-menu-tab' );	
	}else if ( 'reset_post_counters' == $referer_mode ){
		$redirect_url = admin_url( 'admin.php?page=add-vmp-menu-tab' );
	}else{
		
	}
		
	echo "<td align='center' valign='top' style='text-align:center;'><a href='" . $redirect_url . "&p=1'>First</a></td>";
	
	if ( 1 < $p ){
		echo "<td align='center' valign='top' style='text-align:center;'><a href='" . $redirect_url . "&p=" . ( $p - 1 ) . "'>Prev</a></td>";
	}
	
	for ( $i = $lower_limit; $i <= $upper_limit; $i++ ){
		echo "<td align='center' valign='top' style='text-align:center;'><a href='" . $redirect_url . "&p=" . $i . "'>" . $i . "</a></td>";
	}
	
	if ( $p < $total_page_links ){
		echo "<td align='center' valign='top' style='text-align:center;'><a href='" . $redirect_url . "&p=" . ( $p + 1 ) . "'>Next</a></td>";
	}
	
	echo "<td align='center' valign='top' style='text-align:center;'><a href='" . $redirect_url . "&p=" . $total_page_links . "'>Last</a></td>";
}

?>
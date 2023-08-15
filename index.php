<?php
/*
Plugin Name: Mong9 Editor
Plugin URI: https://mong9editor.com/
Description: The most advanced frontend drag & drop content editor. Mong9 Editor is a responsive page builder which can be used to extend the Classic Editor.
Tags: post, wysiwyg, content editor, drag & drop builder, page builder.
Version: 1.2.1
Author: Mong9 Team
Author URI: https://mong9editor.com/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mong9-editor

	Mong9 Editor is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	any later version.

	Mong9 Editor is distributed in the hope that it will be useful,
	Mong9 Editorbut WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	Copyright (c) 2019 Mong9 Team. All rights reserved.
*/

// You can set the constants below according to your own needs.
// 아래 상수는 본인에 맞게 설정하시면 됩니다.
$mode_m = 768; // mobile phone landscape settings(휴대폰 가로 설정값)
$mode_e = 576; // mobile phone vertical settings(휴대폰 세로 설정값)
$google_token = ''; // Google Maps Token (When using Google Maps, an authentication token is required.) // 구글지도 토큰(구글지도 사용시, 인증토큰이 필요합니다.)
$image_upload_size = 5; // Image upload capacity (5M) // 이미지 업로드 용량(5M)

$domain_info = get_mong9_domain_info();

define('MONG9','true');
define('MONG9_EDITOR_VERSION','1.2.1');
define('MONG9_EDITOR__MINIMUM_TINYMCE_VERSION','4');
define('MONG9_NOW_SITE_DOMAIN',$domain_info['domain_url']);
define('MONG9_NOW_SITE_DIR',$domain_info['domain_dir']);
define('MONG9_EDITOR__PLUGIN_URL',$domain_info['plugin_url']);
define('MONG9_EDITOR__PLUGIN_DIR',$domain_info['plugin_dir']);
define('MONG9_EDITOR_DELETE_LIMIT',100000);
define('MONG9_SCREEN_SIZE_m',(isset($_REQUEST['mode_m']) && $_REQUEST['mode_m'] != '') ? $_REQUEST['mode_m'] : $mode_m );
define('MONG9_SCREEN_SIZE_e',(isset($_REQUEST['mode_e']) && $_REQUEST['mode_e'] != '') ? $_REQUEST['mode_e'] : $mode_e );
define('MONG9_GOOGLE_TOKEN',(isset($_REQUEST['google_token']) && $_REQUEST['google_token'] != '') ? $_REQUEST['google_token'] : $google_token );
define('MONG9_UPLOAD_DIR',MONG9_EDITOR__PLUGIN_DIR .'data/'); // Image upload folder name(이미지 업로드 폴더명)
define('MONG9_IMAGE_UPLOAD_SIZE',$image_upload_size);

function mong9editor_int() {

	$mong9_editor_use = 1; // 사용가능

	define('MONG9_EDITOR_POSSIBLE',$mong9_editor_use);

	// mong9_action
	if (isset($_REQUEST['mong9_action']) && $_REQUEST['mong9_action'] != '') {

		mong9editor_parse_request($_REQUEST['mong9_action']);

	} else {

		// common
		mong9editor_enqueue_int();
		mong9editor_site_enqueue_scripts();

	}

} // function

// Parse 'mong9_action'
function mong9editor_parse_request($mong9_action = '') {

	if (MONG9_EDITOR_POSSIBLE == 1) {

		if ($mong9_action != '') {

			if (file_exists(MONG9_EDITOR__PLUGIN_DIR .'includes/'. $mong9_action .'.php')) {

				include MONG9_EDITOR__PLUGIN_DIR .'includes/'. $mong9_action .'.php';
				$func = 'mong9editor_' . $mong9_action;
				$func();
				exit();

			}

		}

    }

	print_m9_msg( m9_die_msg('Security check failed.') );
	exit();

} // function

// Mong9 int
function mong9editor_enqueue_int() {

	$mong9_window_url = MONG9_NOW_SITE_DOMAIN .'index.php?mong9_action=editor';

	$rn = "\n";
	$_script = '<script>';
	$_script .= "if (!M9_SET) { var M9_SET = {}; }". $rn;
	$_script .= "M9_SET['mong9_editor_use'] = '". MONG9_EDITOR_POSSIBLE . "';". $rn;
	$_script .= "M9_SET['mong9_url'] = '". MONG9_EDITOR__PLUGIN_URL ."';". $rn;
	$_script .= "M9_SET['mong9_screen_size'] = { 'm' : '". MONG9_SCREEN_SIZE_m ."' , 'e' : '". MONG9_SCREEN_SIZE_e ."' };". $rn;
	$_script .= "M9_SET['google_token'] = '". MONG9_GOOGLE_TOKEN ."';". $rn;
	$_script .= "M9_SET['mong9_window_url'] = '". $mong9_window_url . "';". $rn;
	$_script .= '</script>';

	echo $_script;

	mong9_enqueue_script('mong9-js',MONG9_EDITOR__PLUGIN_URL.'source/js/mong9.js');

} // function

// Add custom js,css in user mode
function mong9editor_site_enqueue_scripts() {

	mong9_enqueue_style('bootstrap-icons',MONG9_EDITOR__PLUGIN_URL.'source/etc/bootstrap-icons/bootstrap-icons.min.css');
	mong9_enqueue_style('mong9-base',MONG9_EDITOR__PLUGIN_URL.'source/css/mong9-base.css');
	mong9_enqueue_style('mong9',MONG9_EDITOR__PLUGIN_URL.'source/css/mong9.css');
	mong9_enqueue_style('mong9-m',MONG9_EDITOR__PLUGIN_URL.'source/css/mong9-m.css','','','all and (max-width: '. MONG9_SCREEN_SIZE_m .'px)');
	mong9_enqueue_style('mong9-e',MONG9_EDITOR__PLUGIN_URL.'source/css/mong9-e.css','','','all and (max-width: '. MONG9_SCREEN_SIZE_e .'px)');

} // function

// Mong9 Filter
function Mong9_Html_Convert_Filter($html) {
	require_once(MONG9_EDITOR__PLUGIN_DIR . 'includes/functions/content-filter.php');
	return Mong9_Html_Convert($html);
} // function

// print ajax message
function print_m9_msg($msg = '') {
	echo $msg;
	exit;	
} // function

function m9_die_msg($msg = '') {
//	return __($msg);
	return $msg;
} // function


function mong9_enqueue_script($Dname,$Durl) {
	echo '<script type="text/javascript" src="'. $Durl .'"></script>'."\n";
} // function

function mong9_enqueue_style($Dname,$Durl,$Detc='') {
	echo '<link rel="stylesheet" href="'. $Durl .'" '. ( ($Detc) ? $Detc : '' ) .'>';
} // function

function get_mong9_domain_info() {

	$domain = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$domain .= ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['HTTP_HOST'];
	$relative_path = preg_replace("`\/[^/]*\.php$`i", "/", $_SERVER['PHP_SELF']);
	$plugin_url = $domain.$relative_path;
	$domain_dir = preg_replace("`".$relative_path."$`i", "/", __DIR__);
	$escaped_path = preg_quote($relative_path, '/');
	$domain_dir = preg_replace('/' . $escaped_path . '$/', '',__DIR__.'/');

	$array = array(
		domain_url => $domain .'/',
		domain_dir => $domain_dir .'/',
		plugin_url => $plugin_url,
		plugin_dir => __DIR__ .'/',
	);

	return $array;

} // function

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Functions used only in Tinymce4 from below
// 아래부터는 Tinymce4 에만 사용되는 함수들
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

mong9editor_int();

?>
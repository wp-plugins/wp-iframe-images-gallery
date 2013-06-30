<?php

/*
Plugin Name: iFrame Images Gallery
Plugin URI: http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/
Description: iframe images gallery is a simple wordpress plugin to create horizontal image slideshow. Horizontal bar will be display below the images to scroll.
Author: Gopi.R
Version: 7.0
Author URI: http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/
Donate link: http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb, $wp_version;
define("WP_iframe_TABLE", $wpdb->prefix . "iframe_plugin");

define("WP_iframe_UNIQUE_NAME", "iframe-images-gallery");
define("WP_iframe_TITLE", "iFrame Images Gallery");
define('WP_iframe_LINK', 'Check official website for more information <a target="_blank" href="http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/">click here</a>');
define('WP_iframe_FAV', 'http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/');

function iframe( $group = "Group1", $width = "600" , $height = "220" ) 
{
	$arr = array();
	$arr["group"] = $group;
	$arr["width"] = $width;
	$arr["height"] = $height;
	echo iframe_shortcode($arr);
}

function iframe_install() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_iframe_TABLE . "'") != WP_iframe_TABLE) 
	{
		$sSql = "CREATE TABLE IF NOT EXISTS `". WP_iframe_TABLE . "` (";
		$sSql = $sSql . "`iframe_id` INT NOT NULL AUTO_INCREMENT ,";
		$sSql = $sSql . "`iframe_path` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`iframe_link` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`iframe_target` VARCHAR( 50 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_title` VARCHAR( 500 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_order` INT NOT NULL ,";
		$sSql = $sSql . "`iframe_status` VARCHAR( 10 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_type` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_extra1` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_extra2` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`iframe_date` datetime NOT NULL default '0000-00-00 00:00:00' ,";
		$sSql = $sSql . "PRIMARY KEY ( `iframe_id` )";
		$sSql = $sSql . ")";
		$wpdb->query($sSql);
		
		$IsSql = "INSERT INTO `". WP_iframe_TABLE . "` (`iframe_path`, `iframe_link`, `iframe_target` , `iframe_title` , `iframe_order` , `iframe_status` , `iframe_type` , `iframe_date`)"; 
		for($i=1; $i<=4; $i++)
		{
			$sSql = $IsSql . " VALUES ('".get_option('siteurl')."/wp-content/plugins/wp-iframe-images-gallery/images/250x167_$i.jpg', '#', '_blank', 'Image Title', '$i', 'YES', 'GROUP1', '0000-00-00 00:00:00');";
			$wpdb->query($sSql);
		}
	}
}

function iframe_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/image-management-edit.php');
			break;
		case 'add':
			include('pages/image-management-add.php');
			break;
		case 'set':
			include('pages/image-setting.php');
			break;
		default:
			include('pages/image-management-show.php');
			break;
	}
}

function iframe_shortcode( $atts ) 
{
	global $wpdb;
	$iframe_random = "";
	$img = "";
	$dreamscape = "";
		
	// [iframeimages group="Group1" width="600" height="220"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$iframe_type = $atts['group'];
	$iframe_width = $atts['width'];
	$iframe_height = $atts['height'];
	
	if(!is_numeric(@$iframe_width)) { @$iframe_width = 600 ;}
	if(!is_numeric(@$iframe_height)) { @$iframe_height = 300; }
	
	$sSql = "select iframe_path,iframe_link,iframe_target,iframe_title from ".WP_iframe_TABLE." where 1=1";
	if($iframe_type <> ""){ $sSql = $sSql . " and iframe_type='".$iframe_type."'"; }
	if($iframe_random == "YES"){ $sSql = $sSql . " ORDER BY RAND()"; }else{ $sSql = $sSql . " ORDER BY iframe_order"; }
	
	$data = $wpdb->get_results($sSql);
	
	$iframe_count = 0;
	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$img = $img. '<td>';
			if($data->iframe_link <> "") { $img = $img. '<a href="'.$data->iframe_link.'" target="'.$data->iframe_target.'">'; }
			$img = $img. '<img border="0" alt="'.$data->iframe_title.'" src="'.$data->iframe_path.'" />';
			if($data->iframe_link <> "") { $img = $img. '</a>'; }
			$img = $img. '</td>';
			//$img = $img. '<td></td>';
			$iframe_count++;
		}
	}	
	
	$dreamscape = $dreamscape. '<div>';
	  $dreamscape = $dreamscape. '<div style="height: '.$iframe_height.'px;margin: 20px auto 8px;right: auto;vertical-align: middle;width: '.$iframe_width.'px;">';
		  $dreamscape = $dreamscape. '<div style="height: 100px;margin: 0 auto;padding: 0;">';
			$dreamscape = $dreamscape. '<div style="height: '.$iframe_height.'px;overflow: auto;width: 100%;">';
			 $dreamscape = $dreamscape. ' <table cellspacing="0" cellpadding="0" border="0">';
				$dreamscape = $dreamscape. '<tbody><tr>';
				  $dreamscape = $dreamscape. $img;
				$dreamscape = $dreamscape. '</tr>';
			  $dreamscape = $dreamscape. '</tbody></table>';
			$dreamscape = $dreamscape. '</div>';
		  $dreamscape = $dreamscape. '</div>';
	  $dreamscape = $dreamscape. '</div>';
	$dreamscape = $dreamscape. '</div>';
	
	return $dreamscape;
}

function iframe_add_to_menu() 
{
	add_options_page('iFrame Images Gallery', 'iFrame Images Gallery', 'manage_options', 'iframe-images-gallery', 'iframe_admin_options' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'iframe_add_to_menu');
}

function iframe_deactivation()
{
	// No action required.
}

add_shortcode( 'iframeimages', 'iframe_shortcode' );
register_activation_hook(__FILE__, 'iframe_install');
add_action('admin_menu', 'iframe_add_to_menu');
register_deactivation_hook(__FILE__, 'iframe_deactivation');
?>
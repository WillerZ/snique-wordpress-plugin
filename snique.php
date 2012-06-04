<?php
/*
Plugin Name: snique
Plugin URI: http://blog.nomzit.com/snique
Description: Embeds encrypted messages in your blog in such a way that their presence is undetectable
Version: 1.0
Author: Phil Willoughby
Author URI: http://blog.nomzit.com/
*/

class snique
{
	function ApplySnique()
	{
		if ( strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false )
		{
			add_filter('wp_get_attachment_thumb_url', array(&$this, 'get_attachment_thumb_url'),20,2);
		}
	}
	
	function get_attachment_thumb_url($url,$id)
	{
		return site_url('/%07images/'.$id);
	}
}

if( !isset($mySnique)  )
{
	$mySnique = new snique();
	$mySnique->ApplySnique();
}
?>

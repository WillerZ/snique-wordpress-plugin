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
			add_filter('wp_get_attachment_thumb_file', array(&$this, 'get_attachment_thumb_file'),20,2);
		}
	}
	
	function get_attachment_thumb_file($file,$id)
	{
		$etag = get_post_meta($id,'snique_etag',true);
		if (strlen($etag) < 1)
		{
			srand(intval($num));
			$chars = 'abcdef0123456789';
			while (strlen($etag) < 32)
			{
				$etag .= $chars[rand(0, 16)];
			}
		}
		$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
			 stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : 
				 false ;
		if( false !== $if_none_match )
		{
			$tags = split( ", ", $if_none_match ) ;
			foreach( $tags as $tag )
			{
				if( $tag == $etag )
				{
					header( "HTTP/1.1 304 NOT MODIFIED" );
					exit;
				}
			}
		}
		header( "Cache-Control: public, max-age=60, no-transform, must-revalidate" );
		header( 'Etag: "'.$etag.'"' );
	}
}

if( !isset($mySnique)  )
{
	$mySnique = new snique();
	$mySnique->ApplySnique();
}
?>

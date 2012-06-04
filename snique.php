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
		$uri = $_SERVER['REQUEST_URI'];
		$images_prefix = site_url('/%07images/');
		$prefix_len = strlen($images_prefix);
		if ( strncmp($uri, $images_prefix, $prefix_len) == 0 ))
		{
			add_action('init', array(&$this, 'send_attachment_if_necessary'),1,0);
		}
		else
		{
			add_filter('wp_get_attachment_thumb_url', array(&$this, 'get_attachment_thumb_url'),20,2);
		}
	}
	
	function get_attachment_thumb_url($url,$id)
	{
		return site_url('/%07images/'.$id);
	}

	function send_attachment_if_necessary()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$images_prefix = site_url('/%07images/');
		$prefix_len = strlen($images_prefix);
		$image_id = intVal(substr($uri, $prefix_len));
		$image_file = get_attached_file( $image_id );
		$etag = get_post_meta($id,'snique_etag',true);
		if (strlen($etag) < 1)
		{
			srand($image_id);
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
				if( $tag == '"'.$etag.'"' )
				{
					header( "HTTP/1.1 304 NOT MODIFIED" );
					exit;
				}
			}
		}
		header( "Cache-Control: public, max-age=60, no-transform, must-revalidate" );
		header( 'Etag: "'.$etag.'"' );
		header( "Content-Length: ".strVal(filesize($image_file)));
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		header( "Content-Type: ".finfo_file($finfo,$image_file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}

if( !isset($mySnique)  )
{
	$mySnique = new snique();
	$mySnique->ApplySnique();
}
?>

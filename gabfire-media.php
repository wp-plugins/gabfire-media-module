<?php
if ( ! defined( 'ABSPATH' ) ) exit;  /* Exit if accessed directly */

/*
	Plugin Name: Gabfire Media Module
	Plugin URI: http://www.gabfirethemes.com
	Description: Gabfire Media Module extends the functionality of WordPress Featured Image to support Videos and Default Post Images.
	Author: Gabfire Themes
	Version: 0.1
	Author URI: http://www.gabfirethemes.com

    Copyright 2013 Gabfire Themes (email : info@gabfire.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

define( 'GABFIRE_WIDGETS_VERSION', '0.1');

/* Include BFI Thumb */
require_once( dirname( __FILE__ ) . '/bfi_thumb.php' );

/* Start Media Module */
@define( BFITHUMB_UPLOAD_DIR, 'gabfire_thumbs' );

/* HTML5 Video */
function gabfire_mediaplugin_html5video ($parameters){
	global $post, $video_mp4, $video_webm, $video_ogv, $caption_url;
	
	wp_enqueue_style('videojs-css', plugins_url() . '/gabfire-media-module/videojs/video-js.min.css');		
	wp_enqueue_script('videojs', 'http://vjs.zencdn.net/4.11/video.js');
		
	$video_mp4 = get_post_meta($post->ID, 'video-mp4', true);
	$video_webm = get_post_meta($post->ID, 'video-webm', true);
	$video_ogv = get_post_meta($post->ID, 'video-ogv', true);
	$caption_url = get_post_meta($post->ID, 'caption-url-1', true);
	
	if($caption_url != '') {
		$path = parse_url($caption_url, PHP_URL_PATH);
		$filename = basename($path);
		$captionname = strtok($filename, '.');
	}
	?>
	
	<video id="video_<?php echo $post->ID; ?>" class="video-js vjs-default-skin vjs-big-play-centered" width="<?php echo $parameters['media_width']; ?>" height="<?php echo $parameters['media_height']; ?>" data-setup='{ "controls": true, "autoplay": false, "preload": "auto" }'>
		<?php if ($video_mp4 != '') { ?><source src="<?php echo esc_url($video_mp4); ?>" type="video/mp4" /><?php } ?>
		<?php if ($video_webm != '') { ?><source src="<?php echo esc_url($video_webm); ?>" type="video/webm" /><?php } ?>
		<?php if ($video_ogv != '') { ?><source src="<?php echo esc_url($video_ogv); ?>" type="video/ogg" /><?php } ?>
		<?php if ($caption_url != '') { ?><track kind="captions" src="<?php echo esc_url($caption_url); ?>" srclang="<?php echo $captionname; ?>" label="<?php echo ucfirst($captionname); ?>"></track><?php } ?>
	</video>
	<?php
}	

/* Any video entered into custom field with name iframe */
function gabfire_mediaplugin_oembedvideo ($parameters){
	global $post, $gab_iframe;
	$gab_iframe = get_post_meta($post->ID, 'iframe', true);
	
	$videourl = wp_oembed_get($gab_iframe);
	preg_match('/src="([^"]+)"/', $videourl, $match);
	$videourl = $match[1];
	
	echo '
		<span class="cf_video '.$parameters['thumb_align'].'">		
			<iframe title="';the_title(''); echo '" src="'. esc_url($videourl) .'?wmode=opaque&amp;wmode=opaque&amp;showinfo=0&amp;autohide=1" width="'.$parameters['media_width'].'" height="'.$parameters['media_height'].'" allowfullscreen></iframe>
		</span>';
}

/* Lets get the featured image if video is disabled or no video avaiable */
function gabfire_mediaplugin_thumbnail ($parameters){
	global $post, $image;
	
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
	$url = $thumb['0'];
	
	if ($parameters['resize_type'] == 'c') { 
		$params = array( 'width' => $parameters['media_width'], 'height' => $parameters['media_height'] );
		
	} elseif ($parameters['resize_type'] == 'w') {  
		$params = array( 'width' => $parameters['media_width'] );
		
	} elseif ($parameters['resize_type'] == 'h') {
		$params = array( 'height' => $parameters['media_height'] );
	}
	
	$image = bfi_thumb( $url, $params );

	if ($parameters['link'] == 1) {
		echo '<a href="' . get_the_permalink() . '" rel="bookmark">';
	}
	
	if ($parameters['imgtag'] == 1) { 
		echo '<img src="';
	}
	
	echo $image;
	
	if ($parameters['imgtag'] == 1) {  
		echo '" class="'.$parameters['thumb_align'].'" alt="'. get_the_title() . '" title="'. get_the_title() . '" />'; 
	}
	
	if ($parameters['link'] == 1) {
		echo '</a>'; 
	} 
}

/* No Video, No Featured Image?
 * OK then lets see if there are any defautl image enabled in gabfire_media array, if yes, then lets call it.
 */ 
function gabfire_mediaplugin_default ($parameters) { 

	global $post;
	
	if ($parameters['link'] == 1) {
		echo "<a href='". get_permalink() . "' rel='bookmark'>";	
	}

	if ($parameters['imgtag'] == 1) { 
		echo "<img src='";
	}
		echo esc_url(get_template_directory_uri()) . "/images/thumbs/".$parameters['default_name'];
	
	if ($parameters['imgtag'] == 1) {  
		echo "' class='".$parameters['thumb_align']."' alt='" . get_the_title() ."' title='" . get_the_title() ."' />";
	}		
	
	if ($parameters['link'] == 1) {
		echo "</a>"; 
	}		
}

/* Now, lets run our function */
function gabfire_mediaplugin($parameters) 
{
	if ( !post_password_required() ) {
		global $post, $gab_iframe, $video_mp4, $video_webm, $video_ogv, $caption_url;
		$gab_iframe = get_post_meta($post->ID, 'iframe', true);
		$video_mp4 = get_post_meta($post->ID, 'video-mp4', true);
		$video_webm = get_post_meta($post->ID, 'video-webm', true);
		$video_ogv = get_post_meta($post->ID, 'video-ogv', true);
		$caption_url = get_post_meta($post->ID, 'caption-url-1', true);
		
		/* If we have installed and activated Gabfire Media Plugin */
		if ( function_exists( 'gabfire_mediaplugin_thumbnail' ) ) {
			
			if( ( ($video_mp4 != '') or ($video_webm != '') or ($video_ogv != '') )  and $parameters['enable_video'] == 1 ) { 
				gabfire_mediaplugin_html5video ($parameters);
			}
			elseif ($gab_iframe != '' and $parameters['enable_video'] == 1)	{ 
				gabfire_mediaplugin_oembedvideo ($parameters);
			}	
			elseif(has_post_thumbnail() and ($parameters['enable_thumb'] == 1))	{
				gabfire_mediaplugin_thumbnail ($parameters);
			}
			elseif($parameters['enable_default'] == 1) {
				gabfire_mediaplugin_default ($parameters);
			}
			
		} 
	}
}
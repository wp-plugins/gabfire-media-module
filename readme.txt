=== Gabfire Widget Pack ===
Contributors: gabfire
Tags: video, html5 video, oembed, videojs, featured image, post thumbnail
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gabfire Media Module extends the functionality of WordPress Featured Image to support Videos and Default Post Images.

== Description ==

Videos and pictures don't have to be difficult. Gabfire themes include a media module that makes embedding media simple.

This plugin is maintained by the folks over at http://www.gabfirethemes.com

= Sample Code =
<?php 
gabfire_media(array(
	'name' => 'figure', 
	'imgtag' => 1,
	'link' => 1,
	'enable_thumb' => 1,
	'enable_video' => 0, 
	'resize_type' => 'c', 
	'media_width' => 415, 
	'media_height' => 284, 
	'thumb_align' => 'alignnone',
	'enable_default' => 1,
	'default_name' => 'defaultimage.png'
)); 
?>

name -> Name of post thumbnail to be used thats going to be resized to display featured image
imgtag -> 1 or 0. Using this option you can add/remove '<img src' tag to image. 
link -> 1 or 0. If set 1, the image will have a link to post
enable_thumb -> 1 or 0. You may want to use this option function just to get featured post thumbnails
enable_video -> 1 or 0. You may want to use this option function just to get videos
resize_type -> c, w, or h. C will crop image to exact size. w resizes the width and calculates height in proportion. h resizes the height and calculates width in proportion.
thumb_align -> adds a class to media
enable_default -> 1 or 0. You can set a default image to display if post has no media
default_name -> name of image to display. The image path is yourtheme/images/thumbs directory.

= How to Add a Video =
# If you are going to use Youtube/Vimeo/Dailymotion -> copy video URL from browser bar -> add it via custom field to your post using key name *iframe*
# If you'd like to display a self hosted MP4, WEBM or OGV file, add them as below
## Custom field key name *video-mp4* and enter full file url into value field
## Custom field key name *video-webm* and enter full file url into value field
## Custom field key name *video-ogv* and enter full file url into value field
## To add a caption (SRT or VTT format) to the video, use custom field name *caption-url-1* and full file url into value field

== Installation ==

You can install the Gabfire Widget Pack from your WordPress Dashboard or manually via FTP.

= From WordPress Dashboard =

# Navigate to 'Plugins -> Add New' from your WordPress dashboard.
# Search for `Gabfire Widget Pack` and install it.
# Activate the plugin from Plugins menu.

= Manual Installation =

# Download `gabfire-media-module.zip`
# Unzip
# Upload the `gabfire-media-module` folder to your `/wp-content/plugins` directory (do not rename the folder)
# Activate the plugin from Plugins menu.


== Changelog ==
= 0.1 =
* Initial release
<?php
/* Adds a box to the main column on the Post and Page edit screens. */
function gabfiremedia_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {
		add_meta_box('gabfiremedia', __( 'Gabfire Video Fields', 'gabfire-media' ), 'gabfiremedia_meta_box_callback', $screen );
	}
}
add_action( 'add_meta_boxes', 'gabfiremedia_add_meta_box' );


/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function gabfiremedia_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'gabfiremedia_meta_box', 'gabfiremedia_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$values = get_post_custom( $post->ID );
	$video1 = isset( $values['iframe'] ) ? esc_attr( $values['iframe'][0] ) : '';
	$video2 = isset( $values['video-mp4'] ) ? esc_attr( $values['video-mp4'][0] ) : '';
	$video3 = isset( $values['video-webm'] ) ? esc_attr( $values['video-webm'][0] ) : '';
	$video4 = isset( $values['video-ogv'] ) ? esc_attr( $values['video-ogv'][0] ) : '';
	$poster = isset( $values['caption-url-1'] ) ? esc_attr( $values['video-poster'][0] ) : '';
	$caption1 = isset( $values['caption-url-1'] ) ? esc_attr( $values['caption-url-1'][0] ) : '';
	$caption2 = isset( $values['caption-url-2'] ) ? esc_attr( $values['caption-url-2'][0] ) : '';
	?>

	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('Video URL', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="iframe"><?php _e('You can add any Youtube, Vimeo, Dailymotion or Screenr video url into this box','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="iframe" id="iframe" value="<?php echo $video1; ?>" />
		</p>
	</div>
	
	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('MP4 Video', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="video-mp4"><?php _e('Enter a link to video file','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="video-mp4" id="video-mp4" value="<?php echo $video2; ?>" />
		</p>
	</div>	
	
	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('WebM Video', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="video-webm"><?php _e('Enter a link to video file','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="video-webm" id="video-webm" value="<?php echo $video3; ?>" />
		</p>
	</div>	

	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('OGV Video', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="video-ogv"><?php _e('Enter a link to video file','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="video-ogv" id="video-ogv" value="<?php echo $video4; ?>" />
		</p>
	</div>		
	
	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('Video Poster', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="video-poster"><?php _e('The image that will show on background while video has not yet started', 'gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="video-poster" id="video-poster" value="<?php echo $poster; ?>" />
		</p>
	</div>		
	
	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('Caption #1', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="caption-url-1"><?php _e('Enter a link to a SRT format file','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="caption-url-1" id="caption-url-1" value="<?php echo $caption1; ?>" />
		</p>
	</div>		

	<div class="gabfire_fieldgroup">
		<p class="gabfire_fieldcaption"><?php _e('Caption #2', 'gabfire-media'); ?></p>
		<p class="gabfire_fieldrow">
			<label for="caption-url-2"><?php _e('Enter a link to a SRT format file','gabfire-media'); ?></label>
			<input type="text" class="gabfire_textinput" name="caption-url-2" id="caption-url-2" value="<?php echo $caption2; ?>" />
		</p>
	</div>
	
	<p><?php _e('While deleting any of input fields above, make sure to delete it below in Custom Fields section as well.', 'gabfire'); ?></p>
<?php }

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function gabfiremedia_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['gabfiremedia_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['gabfiremedia_meta_box_nonce'], 'gabfiremedia_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	$allowed = array( 
	);	
	
	if( isset( $_POST['iframe'] ) && !empty( $_POST['iframe'] ) )
		update_post_meta( $post_id, 'iframe', wp_kses( $_POST['iframe'], $allowed ) );
	
	if( isset( $_POST['video-mp4'] ) && !empty( $_POST['video-mp4'] ) )
		update_post_meta( $post_id, 'video-mp4', wp_kses( $_POST['video-mp4'], $allowed ) );

	if( isset( $_POST['video-webm'] ) && !empty( $_POST['video-webm'] ) )
		update_post_meta( $post_id, 'video-webm', wp_kses( $_POST['video-webm'], $allowed ) );

	if( isset( $_POST['video-ogv'] ) && !empty( $_POST['video-ogv'] ) )
		update_post_meta( $post_id, 'video-ogv', wp_kses( $_POST['video-ogv'], $allowed ) );
	
	if( isset( $_POST['video-poster'] ) && !empty( $_POST['video-poster'] ) )
		update_post_meta( $post_id, 'video-poster', wp_kses( $_POST['video-poster'], $allowed ) );	

	if( isset( $_POST['caption-url-1'] ) && !empty( $_POST['caption-url-1'] ) )
		update_post_meta( $post_id, 'caption-url-1', wp_kses( $_POST['caption-url-1'], $allowed ) );

	if( isset( $_POST['caption-url-2'] ) && !empty( $_POST['caption-url-2'] ) )
		update_post_meta( $post_id, 'caption-url-2', wp_kses( $_POST['caption-url-2'], $allowed ) );	
}
add_action( 'save_post', 'gabfiremedia_save_meta_box_data' );

if ( !function_exists( 'gabfiremedia_custom_fields_css' ) ) {
	function gabfiremedia_custom_fields_css() {
		wp_enqueue_style('gabfire-customfields-style', plugins_url() .'/gabfire-media-module/style.css' );
	}
	
	add_action('admin_head-post.php', 'gabfiremedia_custom_fields_css');
	add_action('admin_head-post-new.php', 'gabfiremedia_custom_fields_css');
}
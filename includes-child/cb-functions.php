<?php

if( !\defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// add_action( 'admin_enqueue_scripts', 'cb_load_admin_assets' );
/**
 * Enqueue admin CSS and JS files.
 *
 * @since 1.0.0
 */
function cb_load_admin_assets() {

	$stylesheet_dir = get_stylesheet_directory_uri();

	$src = '/css/admin.css';
	wp_enqueue_style( 'cb-admin', $stylesheet_dir . $src, array(), VERSION );

	$src = '/js/admin.js';
	wp_enqueue_script( 'cb-admin', $stylesheet_dir . $src, array('jquery'), VERSION, true );

}

add_action( 'pre_ping', 'cb_disable_self_pings' );
/**
 * Prevent the child theme from being overwritten by a WordPress.org theme with the same name.
 *
 * See: http://wp-snippets.com/disable-self-trackbacks/
 *
 * @since 1.0.0
 *
 * @param mixed $links
 */
function cb_disable_self_pings(&$links) {

	foreach ( $links as $l => $link )
		if ( 0 === \mb_strpos( $link, home_url() ) )
			unset($links[$l]);

}

/**
 * Change WP JPEG compression (WP default is 90%).
 *
 * See: http://wpmu.org/how-to-change-jpeg-compression-in-wordpress/
 *
 * @since 1.0.0
 */
add_filter( 'jpeg_quality', 'cb_set_jpeg_quality' );
function cb_set_jpeg_quality() {

	return 80;

}

/**
 * Add new image sizes.
 *
 * See: http://wptheming.com/2014/04/features-wordpress-3-9/
 *
 * @since 1.0.0
 */
add_image_size( 'full-size', 1920, 1280, TRUE );
add_image_size( 'banner-size', 1920, 1080, TRUE );
add_image_size( 'large-size', 1280, 720, TRUE );
add_image_size( 'medium-size', 960, 540, TRUE );
add_image_size( 'small-size', 640, 360, TRUE );

add_filter( 'image_size_names_choose', 'cb_image_size_names_choose' );
/**
 * Add new image sizes to media size selection menu.
 *
 * See: http://wpdaily.co/top-10-snippets/
 *
 * @since 1.0.0
 */
function cb_image_size_names_choose($sizes) {

	$sizes['full-size'] = __( 'Custom - Full', CHILD_THEME_TEXT_DOMAIN );
	$sizes['banner-size'] = __( 'Custom - Banner', CHILD_THEME_TEXT_DOMAIN );
	$sizes['large-size'] = __( 'Custom - Large', CHILD_THEME_TEXT_DOMAIN );
	$sizes['medium-size'] = __( 'Custom - Medium', CHILD_THEME_TEXT_DOMAIN );
	$sizes['small-size'] = __( 'Custom - Small', CHILD_THEME_TEXT_DOMAIN );

	return $sizes;

}

// Controls the featured image sizes for UABB
add_filter('uabb_blog_posts_featured_image_sizes', 'cb_uabb_featured_image_sizes');
function cb_uabb_featured_image_sizes($size_arr) {
	
	$size_arr = [
		'full' => __('Full', 'uabb'),
		'large' => __('Large', 'uabb'),
		'medium' => __('Medium', 'uabb'),
		'thumbnail' => __('Thumbnail', 'uabb'),
		'custom' => __('Custom', 'uabb'),
		'full-size' => __('Custom - Full', 'uabb'),
		'banner-size' => __('Custom - Banner', 'uabb'),
		'large-size' => __('Custom - Large', 'uabb'),
		'medium-size' => __('Custom - Medium', 'uabb'),
		'small-size' => __('Custom - Small', 'uabb'),
	]; // Add your own size to this array.
	return $size_arr;
	
}

add_filter( 'upload_mimes', 'cb_enable_svg_uploads', 10, 1 );
/**
 * Enabled SVG uploads. Note that this could be a security issue, see: https://bjornjohansen.no/svg-in-wordpress.
 *
 * @since @since 1.0.0
 */
function cb_enable_svg_uploads($mimes) {

	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;

}

/*
 * Activate the Link Manager
 *
 * See: http://wordpressexperts.net/how-to-activate-link-manager-in-wordpress-3-5/
 *
 * @since 1.0.0
 */
// add_filter( 'pre_option_link_manager_enabled', '__return_true' );		// Activate

/*
 * Disable pingbacks
 *
 * See: http://wptavern.com/how-to-prevent-wordpress-from-participating-in-pingback-denial-of-service-attacks
 *
 * Still having pingback/trackback issues? This post might help: https://wordpress.org/support/topic/disabling-pingbackstrackbacks-on-pages#post-4046256
 *
 * @since 1.0.0
 */
add_filter( 'xmlrpc_methods', 'cb_remove_xmlrpc_pingback_ping' );
function cb_remove_xmlrpc_pingback_ping($methods) {

	unset($methods['pingback.ping']);

	return $methods;

}

/*
 * Disable XML-RPC
 *
 * See: https://wordpress.stackexchange.com/questions/78780/xmlrpc-enabled-filter-not-called
 *
 * @since 1.0.0
 */
if( \defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) exit;

/*
 * Automatically remove readme.html (and optionally xmlrpc.php) after a WP core update
 *
 * @since 1.0.0
 */
add_action( '_core_updated_successfully', 'cb_remove_files_on_upgrade' );
function cb_remove_files_on_upgrade() {

	if( \file_exists(ABSPATH . 'readme.html') )
		\unlink(ABSPATH . 'readme.html');

	if( \file_exists(ABSPATH . 'xmlrpc.php') )
		\unlink(ABSPATH . 'xmlrpc.php');

}

/*
 * Force secure cookie
 *
 * @since 1.0.0
 */
add_filter( 'secure_signon_cookie', '__return_true' );

/*
 * Prevent login with username (email only).
 *
 * @since 1.0.0
 */
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );

/*
 * Prevent non-SSL HTTP origins.
 *
 * @since 1.0.0
 */
add_filter( 'allowed_http_origins', 'cb_allowed_http_origins' );
function cb_allowed_http_origins($allowed_origins) {

	$whitelisted_origins = array();
	foreach( $allowed_origins as $origin ) {
		$url = \parse_url($origin);
		if( 'https' !== $url['scheme'] )
			continue;

		$whitelisted_origins[] = $origin;
	}

	return $whitelisted_origins;

}

/*
 * Add ACF site options admin menu
 *
 * @since 1.0.0
 */
if( \function_exists('acf_add_options_page') )
	acf_add_options_page('Site Options');


// --------------------------------------------------------------
// SN - Add WP search with shortcode
// --------------------------------------------------------------
function cb_search_form() {
	return get_search_form(false);
}
add_shortcode('display_search_form', 'cb_search_form');

// --------------------------------------------------------------
// SN - Disable Schema 
// --------------------------------------------------------------
add_filter( 'fl_theme_disable_schema', '__return_true' );
add_filter( 'fl_builder_disable_schema', '__return_true' );
add_filter( 'fl_post_grid_disable_schema', '__return_true' );
add_filter( 'fl_post_feed_disable_schema', '__return_true' );
add_filter( 'fl_schema_meta_author', '__return_null' );
add_filter( 'fl_schema_meta_publisher', '__return_null' );
add_filter( 'fl_schema_meta_general', '__return_null' );
add_filter( 'fl_schema_meta_comments', '__return_null' );
add_filter( 'fl_schema_meta_thumbnail', '__return_null' );

// --------------------------------------------------------------
// SN - Remove Emoji
// --------------------------------------------------------------
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// --------------------------------------------------------------
// SN - Remove dashicons in frontend for unauthenticated users
// --------------------------------------------------------------
add_action( 'wp_enqueue_scripts', 'cb_dequeue_dashicons' );
function cb_dequeue_dashicons() {
	if ( ! is_user_logged_in() ) {
		wp_deregister_style( 'dashicons' );
	}
}

// --------------------------------------------------------------
// SN - Remove Gutenberg Block Library CSS from loading on the frontend
// --------------------------------------------------------------
add_action( 'wp_enqueue_scripts', 'cb_remove_wp_block_library_css', 100 );
function cb_remove_wp_block_library_css(){
	if ( ! is_user_logged_in() || ! is_singular('post') ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
	}
}

// --------------------------------------------------------------
// SN - module enqueued google fonts
// Go to the Google Webfonts Helper site at https://google-webfonts-helper.herokuapp.com/fonts
// --------------------------------------------------------------// 
add_filter( 'fl_builder_google_fonts_pre_enqueue', function( $fonts ) {
	return array();
} );

// takes care of theme enqueues
add_action( 'wp_enqueue_scripts', function() {
	global $wp_styles;
	if ( isset( $wp_styles->queue ) ) {
		foreach ( $wp_styles->queue as $key => $handle ) {
			if ( false !== strpos( $handle, 'fl-builder-google-fonts-' ) ) {
				unset( $wp_styles->queue[ $key ] );
			}
		}
	}
}, 101 );

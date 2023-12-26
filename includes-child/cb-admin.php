<?php

if( !\defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Remove admin bar inline CSS
 *
 * @since 1.0.0
 */
add_theme_support( 'admin-bar', array('callback' => '__return_false') );

add_action(  'admin_bar_init', 'cb_remove_admin_bar_inline_css' );
function cb_remove_admin_bar_inline_css() {

	remove_action( 'wp_head', 'wp_admin_bar_header' );

}

/*
 * Remove admin bar avatar
 *
 * See: https://gist.github.com/ocean90/1723233
 *
 * @since 1.0.0
 */

add_action( 'admin_bar_menu', 'cb_hide_admin_bar_avatar', 0 );
function cb_hide_admin_bar_avatar() {

	add_filter( 'pre_option_show_avatars', '__return_zero' );

}

add_action( 'admin_bar_menu', 'cb_restore_avatars', 10 );
function cb_restore_avatars() {

	remove_filter( 'pre_option_show_avatars', '__return_zero' );

}

/*
 * Only show the admin bar to users who can at least use Posts
 *
 * @since 1.0.0
 */
add_filter( 'show_admin_bar', 'cb_maybe_hide_admin_bar', 99 );
function cb_maybe_hide_admin_bar($default) {

	return current_user_can( 'edit_posts' ) ? $default : false;

}

/*
 * Hide the WP welcome panel
 *
 * @since 1.0.0
 */
// remove_action( 'welcome_panel', 'wp_welcome_panel' );

/*
 * Remove the profile color scheme picker
 *
 * @since 1.0.0
 */
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

add_action( 'admin_init', 'cb_hide_update_nags' );
/**
 * Remove update nags for non-admins.
 *
 * @since 1.0.0
 */
function cb_hide_update_nags() {

	if( current_user_can('update_core') )
		return;

	remove_action( 'admin_notices', 'update_nag', 3  );
	remove_action( 'admin_notices', 'maintenance_nag', 10 );

}

add_action( 'admin_menu', 'cb_remove_dashboard_widgets' );
/**
 * Disable some or all of the default admin dashboard widgets.
 *
 * See: http://digwp.com/2010/10/customize-wordpress-dashboard/
 *
 * @since 1.x
 */
function cb_remove_dashboard_widgets() {

	remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );			// At a Glance
	remove_meta_box( 'dashboard_activity', 'dashboard', 'core' );			// Activity
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );		// Quick Draft
	remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );			// WordPress Events and News
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );	// Yoast SEO Posts Overview

}

add_action('widgets_init', 'cb_unregister_widgets');
/**
 * Disable some or all widgets.
 *
 * @since 1.0.0
 */
function cb_unregister_widgets() {

	global $wp_widget_factory;

	// Unregister all widgets, except for the whitelisted ones
	// This method will also unregister widgets added by plugins
	// $whitelisted_widgets = array('WP_Nav_Menu_Widget', 'WP_Widget_Custom_HTML', 'WP_Widget_Text');

	// $widgets = array_keys( $wp_widget_factory->widgets );
	// foreach( $widgets as $widget ) {
	// 	if( in_array($widget, $whitelisted_widgets, true) )
	// 		continue;
	//
	// 	unregister_widget( $widget );
	// }

	// ...or unregister individual widgets

	// Default widgets
	// unregister_widget( 'WP_Nav_Menu_Widget' );
	// unregister_widget( 'WP_Widget_Archives' );
	// unregister_widget( 'WP_Widget_Calendar' );
	// unregister_widget( 'WP_Widget_Categories' );
	// unregister_widget( 'WP_Widget_Custom_HTML' );
	// unregister_widget( 'WP_Widget_Media_Audio' );
	// unregister_widget( 'WP_Widget_Media_Gallery' );
	// unregister_widget( 'WP_Widget_Media_Image' );
	// unregister_widget( 'WP_Widget_Media_Video' );
	unregister_widget( 'WP_Widget_Meta' );
	// unregister_widget( 'WP_Widget_Pages' );
	// unregister_widget( 'WP_Widget_Recent_Comments' );
	// unregister_widget( 'WP_Widget_Recent_Posts' );
	// unregister_widget( 'WP_Widget_RSS' );
	// unregister_widget( 'WP_Widget_Search' );
	// unregister_widget( 'WP_Widget_Tag_Cloud' );
	// unregister_widget( 'WP_Widget_Text' );

}

add_filter( 'user_contactmethods', 'cb_user_contactmethods' );
/**
 * Updates the user profile contact method fields for today's popular sites.
 *
 * See: http://wpmu.org/shun-the-plugin-100-wordpress-code-snippets-from-across-the-net/
 *
 * @since 1.0.0
 */
function cb_user_contactmethods($fields) {

	// $fields['facebook'] = 'Facebook';											// Add Facebook
	// $fields['twitter'] = 'Twitter';												// Add Twitter
	// $fields['linkedin'] = 'LinkedIn';											// Add LinkedIn
	unset( $fields['aim'], $fields['yim'], $fields['jabber'] );						// Remove AIM, Yahoo IM, and Jabber / Google Talk

	return $fields;

}

add_action( 'admin_menu', 'cb_remove_dashboard_menus', 12 );
/**
 * Remove default admin dashboard menus.
 *
 * @since 1.0.0
 */
function cb_remove_dashboard_menus() {

	// remove_menu_page('index.php'); // Dashboard tab
	// remove_menu_page('edit.php'); // Posts
	// remove_menu_page('upload.php'); // Media
	// remove_menu_page('edit.php?post_type=page'); // Pages
	// remove_menu_page('edit-comments.php'); // Comments
	remove_submenu_page( 'genesis', 'genesis-plugins' );
	// remove_menu_page('themes.php'); // Appearance
	// remove_menu_page('plugins.php'); // Plugins
	// remove_menu_page('users.php'); // Users
	// remove_menu_page('tools.php'); // Tools
	// remove_menu_page('options-general.php'); // Settings

}

add_filter( 'login_errors', 'cb_login_errors' );
/**
 * Prevent the failed login notice from specifying whether the username or the password is incorrect.
 *
 * See: http://wpdaily.co/top-10-snippets/
 *
 * @since 1.0.0
 */
function cb_login_errors($text) {

	global $errors;

	if( empty($errors) )
		return $text;

	$codes = $errors->get_error_codes();
	if(
		\in_array('invalid_username', $codes, true) || \in_array('incorrect_password', $codes, true)
	) {
		return __( 'Invalid username or password.', CHILD_THEME_TEXT_DOMAIN );
	}

	return $text;

}

add_action( 'admin_head', 'cb_hide_admin_help_button' );
/**
 * Hide the top-right help pull-down button by adding some CSS to the admin <head>.
 *
 * See: http://speckyboy.com/2011/04/27/20-snippets-and-hacks-to-make-wordpress-user-friendly-for-your-clients/
 *
 * @since 1.0.0
 */
function cb_hide_admin_help_button() {

	?><style type="text/css">
		#contextual-help-link-wrap {
			display: none !important;
		}
	</style>
	<?php

}

add_action( 'admin_bar_menu', 'cb_admin_menu_plugins_node' );
/**
 * Add a plugins link to the appearance admin bar menu.
 *
 * @since 1.0.0
 */
function cb_admin_menu_plugins_node($wp_admin_bar) {

	if( !current_user_can('install_plugins') )
		return;

	$node = array(
		'parent' => 'appearance',
		'id'     => 'plugins',
		'title'  => __( 'Plugins', CHILD_THEME_TEXT_DOMAIN ),
		'href'   => admin_url('plugins.php'),
	);

	$wp_admin_bar->add_node( $node );

}

add_action( 'do_meta_boxes', 'cb_remove_meta_boxes' );
/**
 * Remove WP default meta boxes. You should always unhook 'Custom Fields', since it can be a large query.
 *
 * @since 1.0.0
 */
function cb_remove_meta_boxes() {

	// Post
	// remove_meta_box( 'authordiv', 'post', 'normal' );
	// remove_meta_box( 'categorydiv', 'post', 'side' );
	// remove_meta_box( 'commentsdiv', 'post', 'normal' );
	// remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
	remove_meta_box( 'postcustom', 'post', 'normal' );
	// remove_meta_box( 'postexcerpt', 'post', 'normal' );
	// remove_meta_box( 'postimagediv', 'post', 'side' );
	// remove_meta_box( 'revisionsdiv', 'post', 'normal' );
	// remove_meta_box( 'slugdiv', 'post', 'normal' );
	// remove_meta_box( 'submitdiv', 'post', 'side' );
	// remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
	// remove_meta_box( 'trackbacksdiv', 'post', 'normal' );

	// Page
	// remove_meta_box( 'authordiv', 'page', 'normal' );
	// remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
	// remove_meta_box( 'pageparentdiv', 'page', 'side' );
	remove_meta_box( 'postcustom', 'page', 'normal' );
	// remove_meta_box( 'postimagediv', 'page', 'side' );
	// remove_meta_box( 'slugdiv', 'page', 'normal' );
	// remove_meta_box( 'submitdiv', 'page', 'side' );

}

/**
 * Limit the number of items that can be shown at once on admin pages.
 * Too many items will cause timeouts on most servers.
 *
 * @since 1.0.0
 */
function cb_limit_items_per_page($per_page) {

	return \min( $per_page, 100 );

}

add_action( 'admin_init', 'cb_setup_per_page_limits' );
function cb_setup_per_page_limits() {

	$options = array(
		'edit_comments_per_page',
		'edit_page_per_page',
		'edit_post_per_page',
		'site_themes_network_per_page',
		'site_users_network_per_page',
		'sites_network_per_page',
		'themes_network_per_page',
		'users_network_per_page',
		'users_per_page',
	);

	// 'edit_{$post_type}_per_page'
	$post_types = get_post_types( array('_builtin' => false) );
	foreach( $post_types as $post_type )
		$options[] = 'edit_' . $post_type . '_per_page';

	foreach( $options as $option )
		add_filter( $option, 'cb_limit_items_per_page' );

}


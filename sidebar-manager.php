<?php
/**
 * Plugin Name:     Sidebar Manager
 * Plugin URI:      http://www.brainstormforce.com
 * Description:     This is the plugin to create custom siderbars to your site.
 * Version:         1.1.7
 * Author:          Brainstorm Force
 * Author URI:      https://www.brainstormforce.com/
 * Text Domain:     bsfsidebars
 *
 * @package         Custom_Sidebars
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Set constants.
 */
define( 'BSF_SB_FILE', __FILE__ );
define( 'BSF_SB_BASE', plugin_basename( BSF_SB_FILE ) );
define( 'BSF_SB_DIR', plugin_dir_path( BSF_SB_FILE ) );
define( 'BSF_SB_URL', plugins_url( '/', BSF_SB_FILE ) );
define( 'BSF_SB_VER', '1.1.7' );
define( 'BSF_SB_PREFIX', 'bsf-sb' );
define( 'BSF_SB_POST_TYPE', 'bsf-sidebar' );

require_once 'classes/class-bsf-sb-loader.php';

if ( is_admin() ) {
	// Admin Notice Library Settings.
	require_once BSF_SB_DIR . 'lib/notices/class-astra-notices.php';
}

// BSF Analytics library.
if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
	require_once BSF_SB_DIR . 'admin/bsf-analytics/class-bsf-analytics-loader.php';
}

$bsf_analytics = BSF_Analytics_Loader::get_instance();

$bsf_analytics->set_entity(
	array(
		'bsf' => array(
			'product_name'    => 'Sidebar Manager',
			'path'            => BSF_SB_DIR . 'admin/bsf-analytics',
			'author'          => 'Brainstorm Force',
			'time_to_display' => '+24 hours',
		),
	)
);

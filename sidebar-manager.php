<?php
/**
 * Plugin Name:     Sidebar Manager
 * Plugin URI:      http://www.brainstormforce.com
 * Description:     This is the plugin to create custom siderbars to your site.
 * Version:         1.1.1
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
define( 'BSF_SB_VER', '1.1.1' );
define( 'BSF_SB_PREFIX', 'bsf-sb' );
define( 'BSF_SB_POST_TYPE', 'bsf-sidebar' );

require_once 'classes/class-bsf-sb-loader.php';

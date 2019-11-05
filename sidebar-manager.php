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
require_once BSF_SB_DIR . 'includes/lib/notices/class-astra-notices.php';

if ( ! function_exists( 'register_notices' ) ) :

	/**
	 * Ask Theme Rating
	 *
	 * @since 1.4.0
	 */
	function register_notices() {
		$image_path = BSF_SB_URL . 'includes/assets/images/sidebar-manager-icon.png';
		Astra_Notices::add_notice(
			array(
				'id'                         => 'sidebar-manager-rating',
				'type'                       => '',
				'message'                    => sprintf(
					'<div class="notice-image">
						<img src="%1$s" class="custom-logo" alt="Sidebar Manager" itemprop="logo"></div> 
						<div class="notice-content">
							<div class="notice-heading">
								%2$s
							</div>
							%3$s<br />
							<div class="astra-review-notice-container">
								<a href="%4$s" class="astra-notice-close astra-review-notice button-primary" target="_blank">
								%5$s
								</a>
							<span class="dashicons dashicons-calendar"></span>
								<a href="#" data-repeat-notice-after="%6$s" class="astra-notice-close astra-review-notice">
								%7$s
								</a>
							<span class="dashicons dashicons-smiley"></span>
								<a href="#" class="astra-notice-close astra-review-notice">
								%8$s
								</a>
							</div>
						</div>',
					$image_path,
					__( 'Hello! Seems like you have used Astra Hooks to build this website — Thanks a ton!', 'bsfsidebars' ),
					__( 'Could you please do us a BIG favor and give it a 5-star rating on WordPress? This would boost our motivation and help other users make a comfortable decision while choosing the Astra Hooks.', 'bsfsidebars' ),
					'https://wordpress.org/support/plugin/sidebar-manager/reviews/?filter=5#new-post',
					__( 'Ok, you deserve it', 'bsfsidebars' ),
					MONTH_IN_SECONDS,
					__( 'Nope, maybe later', 'bsfsidebars' ),
					__( 'I already did', 'bsfsidebars' )
				),
				'repeat-notice-after'        => MONTH_IN_SECONDS,
				'priority'                   => 25,
				'display-with-other-notices' => false,
			)
		);
	}

	add_action( 'admin_notices', 'register_notices' );

endif;

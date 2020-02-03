<?php
/**
 * BSF_SB_Sidebar
 *
 * @package BSF Custom Sidebars
 */

if ( ! class_exists( 'BSF_SB_Sidebar' ) ) {

	/**
	 * BSF_SB_Sidebar initial setup
	 *
	 * @since 1.0.0
	 */
	final class BSF_SB_Sidebar {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $global_sidebar = null;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->load_actions();
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function load_actions() {
			/* Register Sidebars */
			add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

			/* Init Sidebars */
			add_action( 'get_header', array( $this, 'init_replace_sidebar' ) );
		}

		/**
		 * Register sidebars.
		 *
		 * @access public
		 * @return void
		 */
		public function register_sidebars() {

			$to_register = get_posts(
				array(
					'post_type'        => BSF_SB_POST_TYPE,
					'posts_per_page'   => -1,
					'suppress_filters' => 'false',
				)
			);

			if ( ! empty( $to_register ) ) {
				foreach ( $to_register as $index => $data ) {

					register_sidebar(
						array(
							'name'        => $data->post_title,
							'id'          => BSF_SB_PREFIX . '-' . $data->post_name,
							'description' => $data->post_excerpt,
						)
					);
				}
			}
		}

		/**
		 * Init Replace Sidebars.
		 *
		 * @access public
		 * @return void
		 */
		public function init_replace_sidebar() {
			/* Replace Sidebars */
			add_filter( 'sidebars_widgets', array( $this, 'replace_sidebars' ), 10, 1 );
		}

		/**
		 * Replace Sidebars.
		 *
		 * @access public
		 * @param array $sidebars array of current sidebars.
		 * @return array
		 */
		public function replace_sidebars( $sidebars ) {
			if ( ! is_admin() ) {

				if ( null === self::$global_sidebar ) {
					global $post;

					$option = array(
						'location'  => '_bsf-sb-location',
						'exclusion' => '_bsf-sb-exclusion',
						'users'     => '_bsf-sb-users',
					);

					$replace_sidebars = BSF_SB_Target_Rules_Fields::get_instance()->get_posts_by_conditions( BSF_SB_POST_TYPE, $option );

					if ( ! empty( $replace_sidebars ) ) {

						foreach ( $replace_sidebars as $i => $data ) {

							$post_replace_sidebar = get_post_meta( $data['id'], '_replace_this_sidebar', true );

							if ( false === $post_replace_sidebar || '' == $post_replace_sidebar ) {
								continue;
							}

							$post_name = isset( $data['post_name'] ) ? $data['post_name'] : '';

							$sidebar_id = BSF_SB_PREFIX . '-' . $post_name;

							if ( isset( $sidebars[ $post_replace_sidebar ] ) && isset( $sidebars[ $sidebar_id ] ) ) {

								$sidebars[ $post_replace_sidebar ] = $sidebars[ $sidebar_id ];
								unset( $sidebars[ $sidebar_id ] );
							}
						}

						self::$global_sidebar = $sidebars;
					}
				} else {

					$sidebars = self::$global_sidebar;
				}
			}

			return $sidebars;
		}
	}
}

BSF_SB_Sidebar::get_instance();

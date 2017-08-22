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
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
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
			add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
		}

		/**
		 * Register sidebars.
		 *
		 * @access public
		 * @return void
		 */
		public function register_sidebars() {
			
			$to_register = get_posts( array( 'post_type' => BSF_SB_POST_TYPE, 'posts_per_page' => -1, 'suppress_filters' => 'false' ) );

			if ( !empty( $to_register ) ) {
				foreach ( $to_register as $index => $data ) {
					
					register_sidebar( array(
						'name' 			=> $data->post_title,
						'id' 			=> BSF_SB_PREFIX . '-' . $data->post_name,
						'description' 	=> $data->post_excerpt
					) );
				}
			}
		}
	}
}

BSF_SB_Sidebar::get_instance();

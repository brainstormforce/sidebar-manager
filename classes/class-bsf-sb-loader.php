<?php
/**
 * BSF_SB_Loader
 *
 * @package BSF Custom Sidebars
 */

if ( ! class_exists( 'BSF_SB_Loader' ) ) {

	/**
	 * BSF_SB_Loader initial setup
	 *
	 * @since 1.0.0
	 */
	final class BSF_SB_Loader {

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
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->load_files();

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		/**
		 * Load plugin textdomain.
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'sidebar-manager' );
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function load_files() {
			/* Classes */
			require_once BSF_SB_DIR . 'classes/modules/target-rule/class-bsf-sb-target-rules-fields.php';
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-post-type.php';
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-sidebar.php';
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-metabox.php';

			require_once BSF_SB_DIR . 'classes/class-bsf-sb-white-label.php';
		}
	}
}

BSF_SB_Loader::get_instance();

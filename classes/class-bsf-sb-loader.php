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
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->define_constants();
			$this->load_files();
		}

		/**
		 * Define builder constants.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function define_constants() {
			define('BSF_SB_VER', '0.1.0');
			define('BSF_SB_FILE', trailingslashit(dirname(dirname(__FILE__))) . 'custom-sidebars.php');
			define('BSF_SB_DIR', plugin_dir_path(BSF_SB_FILE));
			define('BSF_SB_URL', plugins_url('/', BSF_SB_FILE));
			define('BSF_SB_PREFIX', 'bsf-sb');
			define('BSF_SB_POST_TYPE', 'bsf-sidebar');
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function load_files() {
			/* Classes */
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-post-type.php';
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-sidebar.php';
			require_once BSF_SB_DIR . 'classes/class-bsf-sb-metabox.php';
		}
	}
}

BSF_SB_Loader::get_instance();

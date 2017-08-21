<?php
/**
 * BSF_CS_Loader
 *
 * @package BSF Custom Sidebars
 */

if ( ! class_exists( 'BSF_CS_Loader' ) ) {

	/**
	 * BSF_CS_Loader initial setup
	 *
	 * @since 1.0.0
	 */
	final class BSF_CS_Loader {

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
			define('BSF_CS_VER', '0.1.0');
			define('BSF_CS_FILE', trailingslashit(dirname(dirname(__FILE__))) . 'custom-sidebars.php');
			define('BSF_CS_DIR', plugin_dir_path(BSF_CS_FILE));
			define('BSF_CS_URL', plugins_url('/', BSF_CS_FILE));
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		static private function load_files()
		{
			/* Classes */
			//require_once BSF_CS_DIR . 'classes/class-bsf-cs-model.php';
		}
	}
}

BSF_CS_Loader::get_instance();

<?php
/**
 * Astra Hooks White Label
 *
 * @package Astra Hooks
 * @since 1.0.1
 */

if ( ! class_exists( 'Bsf_Sb_White_Label' ) ) :

	/**
	 * Bsf_Sb_White_Label
	 *
	 * @since 1.0.1
	 */
	class Bsf_Sb_White_Label {

		/**
		 * Instance
		 *
		 * @since 1.0.1
		 * @var object Class Object.
		 * @access private
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.0.1
		 * @var array branding
		 * @access private
		 */
		private static $branding;

		/**
		 * Initiator
		 *
		 * @since 1.0.1
		 * @return object initialized object of class.
		 */
		public static function set_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.1
		 */
		public function __construct() {

			add_filter( 'all_plugins', array( $this, 'plugins_page' ) );
			add_filter( 'astra_addon_branding_options', __CLASS__ . '::settings' );
			add_action( 'astra_pro_white_label_add_form', __CLASS__ . '::add_white_lavel_form' );

			if ( is_admin() ) {
				// Display the link with the plugin meta.
				add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 4 );
			}
		}

		/**
		 * White labels the plugins page.
		 *
		 * @param array $plugins Plugins Array.
		 * @return array
		 */
		public function plugins_page( $plugins ) {

			if ( ! is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				return $plugins;
			}

			if ( ! isset( $plugins[ BSF_SB_BASE ] ) ) {
				return $plugins;
			}

			// Set White Labels.
			$name        = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'bsf-lw-sb', 'name' );
			$description = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'bsf-lw-sb', 'description' );
			$author      = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-agency', 'author' );
			$author_uri  = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-agency', 'author_url' );

			if ( ! empty( $name ) ) {
				$plugins[ BSF_SB_BASE ]['Name'] = $name;

				// Remove Plugin URI if Agency White Label name is set.
				$plugins[ BSF_SB_BASE ]['PluginURI'] = '';
			}

			if ( ! empty( $description ) ) {
				$plugins[ BSF_SB_BASE ]['Description'] = $description;
			}

			if ( ! empty( $author ) ) {
				$plugins[ BSF_SB_BASE ]['Author'] = $author;
			}

			if ( ! empty( $author_uri ) ) {
				$plugins[ BSF_SB_BASE ]['AuthorURI'] = $author_uri;
			}

			return $plugins;
		}

		/**
		 * Remove a "view details" link from the plugin list table
		 *
		 * @since 1.0.1
		 *
		 * @param array  $plugin_meta  List of links.
		 * @param string $plugin_file Relative path to the main plugin file from the plugins directory.
		 * @param array  $plugin_data  Data from the plugin headers.
		 * @return array
		 */
		public function plugin_links( $plugin_meta, $plugin_file, $plugin_data ) {

			if ( is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				if ( BSF_SB_BASE == $plugin_file ) {
					// Set White Labels.
					$name        = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'bsf-lw-sb', 'name' );
					$description = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'bsf-lw-sb', 'description' );

					if ( ! empty( $name ) ) {
						// Remove Plugin URI if Agency White Label name is set.
						unset( $plugin_meta[2] );
					}
				}
			}
			return $plugin_meta;
		}

		/**
		 * Add White Label setting's
		 *
		 * @since 1.0.1
		 *
		 * @param  array $settings White label setting.
		 * @return array
		 */
		public static function settings( $settings = array() ) {

			$settings['bsf-lw-sb'] = array(
				'name'        => '',
				'description' => '',
			);

			return $settings;
		}

		/**
		 * Add White Label form
		 *
		 * @since 1.0.1
		 *
		 * @param  array $settings White label setting.
		 * @return void
		 */
		public static function add_white_lavel_form( $settings = array() ) {
			require_once BSF_SB_DIR . 'includes/white-label.php';
		}

	}

	/**
	 * Kicking this off by calling 'set_instance()' method
	 */
	Bsf_Sb_White_Label::set_instance();

endif;

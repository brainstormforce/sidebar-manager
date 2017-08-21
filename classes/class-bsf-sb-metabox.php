<?php
/**
 * BSF_SB_Metabox
 *
 * @package BSF Custom Sidebars
 */

if ( ! class_exists( 'BSF_SB_Metabox' ) ) {

	/**
	 * BSF_SB_Metabox initial setup
	 *
	 * @since 1.0.0
	 */
	final class BSF_SB_Metabox {

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
			add_action( 'admin_menu', array( $this, 'metabox_actions' ), 25 );
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function metabox_actions() {
			add_meta_box( 'replace-this-sidebar', __( 'Sidebar To Replace', 'bsfsidebars' ), array( $this, 'replace_this_sidebar' ), BSF_SB_POST_TYPE, 'side', 'low' );
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function replace_this_sidebar() {
			$post_id 	= get_the_ID();
			$sidebars 	= $this->show_sidebars_to_replace();
			$selected	= get_post_meta( $post_id, '_replace_this_sidebar', true );
			
			$output = wp_nonce_field( BSF_SB_POST_TYPE, BSF_SB_POST_TYPE . '-nonce', true, false );
			
			if ( !empty( $sidebars ) ) {
				$output .= '<select name="replace_this_sidebar" class="widefat">';
					foreach ( $sidebars as $slug => $name ) {
						$output .= '<option value="' . $slug . '"' . selected( $selected, $slug, false ) . '>' . $name . '</option>';
					}
				$output .= '</select>';
			} else {
				$output .= '<p>' . __( 'Sidebars are not available.', 'bsfsidebars' ) . '</p>';
			}
			echo $output;
			// echo "<pre>";
			// var_dump( $output );
			// echo "</pre>";

		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function show_sidebars_to_replace() {
			global $wp_registered_sidebars;

			$sidebars_show = array();
			$sidebars_skip = array();

			if ( is_array( $wp_registered_sidebars ) ) {
				
				foreach( $wp_registered_sidebars as $slug => $data ) {
					$sidebars_show[$slug] = $data['name'];
				}
			}

			return $sidebars_show;
		}
	}
}

BSF_SB_Metabox::get_instance();

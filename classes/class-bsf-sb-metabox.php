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
			/* Title filter */
			add_filter( 'enter_title_here', array( $this, 'change_post_name_palceholder' ), 10, 1 );

			/* Setup metabox */
			add_action( 'admin_menu', array( $this, 'metabox_actions' ), 25 );

			/* Save meta data */
			add_action( 'save_post', array( $this, 'metabox_save' ), 10, 1 );
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function change_post_name_palceholder( $title ) {
			if ( get_post_type() == BSF_SB_POST_TYPE ) {
				$title = __( 'Enter sidebar title here', 'bsfsidebars' );
			}
			return $title;
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function metabox_actions() {
			/* Replace sidebar metabox */
			add_meta_box( 'replace-this-sidebar', __( 'Sidebar To Replace', 'bsfsidebars' ), array( $this, 'replace_this_sidebar' ), BSF_SB_POST_TYPE, 'side', 'low' );

			/* Remove the "Excerpt" meta box for the sidebars. */
			remove_meta_box( 'postexcerpt', BSF_SB_POST_TYPE, 'normal' );
			
			/* Sidebar Info */
			add_meta_box( 'sidebar-description', __( 'Description', 'bsfsidebars' ), array( $this, 'sidebar_description' ), BSF_SB_POST_TYPE, 'normal', 'core' );
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function metabox_save( $post_id ) {

			if ( get_post_type() != BSF_SB_POST_TYPE
				|| ( isset( $_POST[ BSF_SB_POST_TYPE . '-nonce'] ) && ! wp_verify_nonce( $_POST[ BSF_SB_POST_TYPE . '-nonce'], BSF_SB_POST_TYPE ) )
			) {
				return $post_id;
			}

			// Verify if this is an auto save routine.
      		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        		return $post_id;

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			if ( isset( $_POST['replace_this_sidebar'] ) ) {
				
				$replace_sidebar = esc_attr( $_POST['replace_this_sidebar'] );
				
				update_post_meta( $post_id, '_replace_this_sidebar', $replace_sidebar );
			}
		}
		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function sidebar_description( $post ) {
			$output  = '<label class="screen-reader-text" for="excerpt">' . __( 'Description', 'bsfsidebars' ) . '</label>';
			$output .= '<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt">' . $post->post_excerpt . '</textarea>';
			$output .= '<p>' . sprintf( __( 'Add an optional description fot the %sWidgets%s screen.', 'bsfsidebars' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ) . '</p>';

			echo $output;
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
					
					$output .= '<option value=""' . selected( $selected, '', false ) . '>' . __( 'None', 'bsfsidebars' ) . '</option>';
					
					foreach ( $sidebars as $slug => $name ) {
						
						if ( strrpos( $slug, BSF_SB_PREFIX ) !== false ) {
							continue;
						}

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

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
				self::$instance = new self();
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
		 * @param string $title post title.
		 * @return string title
		 */
		public function change_post_name_palceholder( $title ) {
			if ( get_post_type() == BSF_SB_POST_TYPE ) {
				$title = __( 'Enter sidebar title here', 'sidebar-manager' );
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
			/* Remove the "Excerpt" meta box for the sidebars. */
			remove_meta_box( 'postexcerpt', BSF_SB_POST_TYPE, 'normal' );

			/* Target Rule */
			add_meta_box( 'sidebar-settings', __( 'Sidebar Settings', 'sidebar-manager' ), array( $this, 'sidebar_settings' ), BSF_SB_POST_TYPE, 'normal', 'core' );
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @param int $post_id current id.
		 */
		public function metabox_save( $post_id ) {

			if ( ! isset( $_POST[ BSF_SB_POST_TYPE . '-nonce' ] ) ) {
				return;
			}

			if ( get_post_type() != BSF_SB_POST_TYPE
				|| ! wp_verify_nonce( $_POST[ BSF_SB_POST_TYPE . '-nonce' ], BSF_SB_POST_TYPE )
			) {
				return $post_id;
			}

			// Verify if this is an auto save routine.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$store_keys = array( 'bsf-sb-location', 'bsf-sb-exclusion' );

			foreach ( $store_keys as $key ) {

				$meta_value = BSF_SB_Target_Rules_Fields::get_format_rule_value( $_POST, $key );

				update_post_meta( $post_id, '_' . $key, $meta_value );
			}

			if ( isset( $_POST['bsf-sb-users'] ) ) {
				$bsf_sb_user_roles = array_map( 'sanitize_text_field', $_POST['bsf-sb-users'] );
				update_post_meta( $post_id, '_bsf-sb-users', $bsf_sb_user_roles );
			}

			if ( isset( $_POST['replace_this_sidebar'] ) ) {

				$replace_sidebar = sanitize_text_field( $_POST['replace_this_sidebar'] );

				update_post_meta( $post_id, '_replace_this_sidebar', $replace_sidebar );
			}
		}

		/**
		 * Target Rule.
		 *
		 * @since 1.0.0
		 * @param object $post post_object.
		 * @return void
		 */
		public function sidebar_settings( $post ) {

			$post_id = $post->ID;

			$include_locations = get_post_meta( $post_id, '_bsf-sb-location', true );
			$exclude_locations = get_post_meta( $post_id, '_bsf-sb-exclusion', true );
			$users             = get_post_meta( $post_id, '_bsf-sb-users', true );
			$replace_sidebar   = get_post_meta( $post_id, '_replace_this_sidebar', true );

			/* Get Sidebars to show in replace list */
			$sidebars = $this->show_sidebars_to_replace();

			$out                  = wp_nonce_field( BSF_SB_POST_TYPE, BSF_SB_POST_TYPE . '-nonce', true, false );
			$out                 .= '<table class="bsf-sb-table widefat">';
				$out             .= '<tbody>';
					$out         .= '<tr class="bsf-sb-row">';
						$out     .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Sidebar To Replace', 'sidebar-manager' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Choose which sidebar you want to replace. Select None to disable this sidebar.', 'sidebar-manager' ) . '"></i>';
						$out     .= '</td>';
						$out     .= '<td class="bsf-sb-row-content">';

			if ( ! empty( $sidebars ) ) {
				$out .= '<select name="replace_this_sidebar" class="widefat">';
				$out .= '<option value=""' . selected( $replace_sidebar, '', false ) . '>' . __( 'None', 'sidebar-manager' ) . '</option>';

				foreach ( $sidebars as $slug => $name ) {
					if ( strrpos( $slug, BSF_SB_PREFIX ) !== false ) {
						continue;
					}
					$out .= '<option value="' . esc_attr( $slug ) . '"' . selected( $replace_sidebar, $slug, false ) . '>' . esc_attr( $name ) . '</option>';
				}
				$out .= '</select>';
			} else {
				$out .= '<p>' . __( 'Sidebars are not available.', 'sidebar-manager' ) . '</p>';
			}

						$out .= '</td>';
					$out     .= '</tr>';

					$out         .= '<tr class="bsf-sb-row">';
						$out     .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Description', 'sidebar-manager' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Add an optional description fot the Widgets screen.', 'sidebar-manager' ) . '"></i>';
						$out     .= '</td>';
						$out     .= '<td class="bsf-sb-row-content">';
							$out .= '<input type="text" rows="1" name="excerpt" value="' . esc_attr( $post->post_excerpt ) . '">';
						$out     .= '</td>';
					$out         .= '</tr>';

					$out         .= '<tr class="bsf-sb-row">';
						$out     .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Display On', 'sidebar-manager' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Add locations for where this sidebar should appear.', 'sidebar-manager' ) . '"></i>';
						$out     .= '</td>';
						$out     .= '<td class="bsf-sb-row-content">';

							ob_start();
							BSF_SB_Target_Rules_Fields::target_rule_settings_field(
								'bsf-sb-location',
								array(
									'title'          => __( 'Display Rules', 'sidebar-manager' ),
									'value'          => '[{"type":"basic-global","specific":null}]',
									'tags'           => 'site,enable,target,pages',
									'rule_type'      => 'display',
									'add_rule_label' => __( 'Add Display Rule', 'sidebar-manager' ),
								),
								$include_locations
							);
							$out .= ob_get_clean();
						$out     .= '</td>';
					$out         .= '</tr>';

					$out         .= '<tr class="bsf-sb-row bsf-sb-hidden">';
						$out     .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Do Not Display On', 'sidebar-manager' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'This Sidebar will not appear at these locations.', 'sidebar-manager' ) . '"></i>';
						$out     .= '</td>';
						$out     .= '<td class="bsf-sb-row-content">';
							ob_start();
							BSF_SB_Target_Rules_Fields::target_rule_settings_field(
								'bsf-sb-exclusion',
								array(
									'title'          => __( 'Exclude On', 'sidebar-manager' ),
									'value'          => '[]',
									'tags'           => 'site,enable,target,pages',
									'add_rule_label' => __( 'Add Excludion Rule', 'sidebar-manager' ),
									'rule_type'      => 'exclude',
								),
								$exclude_locations
							);
							$out .= ob_get_clean();
						$out     .= '</td>';
					$out         .= '</tr>';

					$out         .= '<tr class="bsf-sb-row">';
						$out     .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'User Roles', 'sidebar-manager' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Target header based on user role.', 'sidebar-manager' ) . '"></i>';
						$out     .= '</td>';
						$out     .= '<td class="bsf-sb-row-content">';
							ob_start();
							BSF_SB_Target_Rules_Fields::target_user_role_settings_field(
								'bsf-sb-users',
								array(
									'title'          => __( 'Users', 'sidebar-manager' ),
									'value'          => '[]',
									'tags'           => 'site,enable,target,pages',
									'add_rule_label' => __( 'Add User Rule', 'sidebar-manager' ),
								),
								$users
							);
							$out .= ob_get_clean();
						$out     .= '</td>';
					$out         .= '</tr>';
				$out             .= '</tbody>';
			$out                 .= '</table>';

			echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return array of sidebars
		 */
		public function show_sidebars_to_replace() {
			global $wp_registered_sidebars;

			$sidebars_show = array();

			if ( is_array( $wp_registered_sidebars ) ) {

				foreach ( $wp_registered_sidebars as $slug => $data ) {
					$sidebars_show[ $slug ] = $data['name'];
				}
			}

			return $sidebars_show;
		}
	}
}

BSF_SB_Metabox::get_instance();

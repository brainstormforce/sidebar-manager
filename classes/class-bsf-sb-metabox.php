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
		 * @param string $title sidebar metabox title.
		 * @return string $title updated sidebar metabox title.
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
			/* Remove the "Excerpt" meta box for the sidebars. */
			remove_meta_box( 'postexcerpt', BSF_SB_POST_TYPE, 'normal' );

			/* Target Rule */
			add_meta_box( 'sidebar-settings', __( 'Sidebar Settings', 'bsfsidebars' ), array( $this, 'sidebar_settings' ), BSF_SB_POST_TYPE, 'normal', 'core' );
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @param int $post_id post_id.
		 * @return int $post_id post_id.
		 */
		public function metabox_save( $post_id ) {

			if ( get_post_type() != BSF_SB_POST_TYPE
				|| ( isset( $_POST[ BSF_SB_POST_TYPE . '-nonce' ] ) && ! wp_verify_nonce( $_POST[ BSF_SB_POST_TYPE . '-nonce' ], BSF_SB_POST_TYPE ) )
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
				$meta_value = array();
				if ( isset( $_POST[ $key ]['rule'] ) ) {
					$_POST[ $key ]['rule'] = array_unique( $_POST[ $key ]['rule'] );

					if ( isset( $_POST[ $key ]['specific'] ) ) {
						$_POST[ $key ]['specific'] = array_unique( $_POST[ $key ]['specific'] );
					}

					// Unset the specifics from rule. This will be readded conditionally in next condition.
					$index = array_search( '', $_POST[ $key ]['rule'] );
					if ( false !== $index ) {
						unset( $_POST[ $key ]['rule'][ $index ] );
					}
					$index = array_search( 'specifics', $_POST[ $key ]['rule'] );
					if ( false !== $index ) {
						unset( $_POST[ $key ]['rule'][ $index ] );

						// Only re-add the specifics key if there are specific rules added.
						if ( isset( $_POST[ $key ]['specific'] ) && is_array( $_POST[ $key ]['specific'] ) ) {
							array_push( $_POST[ $key ]['rule'], 'specifics' );
						}
					}

					foreach ( $_POST[ $key ] as $meta_key => $value ) {
						$meta_value[ $meta_key ] = array_map( 'esc_attr', $value );
					}
					if ( ! in_array( 'specifics', $meta_value['rule'] ) ) {
						$meta_value['specific'] = array();
					}
					if ( empty( $meta_value['rule'] ) ) {
						$meta_value = array();
					}
				}

				update_post_meta( $post_id, '_' . $key, $meta_value );
			}

			if ( isset( $_POST['bsf-sb-users'] ) ) {
				update_post_meta( $post_id, '_bsf-sb-users', $_POST['bsf-sb-users'] );
			}

			if ( isset( $_POST['replace_this_sidebar'] ) ) {

				$replace_sidebar = esc_attr( $_POST['replace_this_sidebar'] );

				update_post_meta( $post_id, '_replace_this_sidebar', $replace_sidebar );
			}
		}

		/**
		 * Target Rule.
		 *
		 * @since 1.0.0
		 * @param object $post post object.
		 * @return void
		 */
		public function sidebar_settings( $post ) {

			$post_id = $post->ID;

			$include_locations = get_post_meta( $post_id, '_bsf-sb-location', true );
			$exclude_locations = get_post_meta( $post_id, '_bsf-sb-exclusion', true );
			$users             = get_post_meta( $post_id, '_bsf-sb-users', true );
			$replace_sidebar   = get_post_meta( $post_id, '_replace_this_sidebar', true );

			/* Get Sidebars to show in replace list */
			$sidebars   = $this->show_sidebars_to_replace();

			$out = wp_nonce_field( BSF_SB_POST_TYPE, BSF_SB_POST_TYPE . '-nonce', true, false );
			$out .= '<table class="bsf-sb-table widefat">';
				$out .= '<tbody>';
					$out .= '<tr class="bsf-sb-row">';
						$out .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Sidebar To Replace', 'bsfsidebars' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Choose which sidebar you want to replace. Select None to disable this sidebar.', 'bsfsidebars' ) . '"></i>';
						$out .= '</td>';
						$out .= '<td class="bsf-sb-row-content">';

			if ( ! empty( $sidebars ) ) {
				$out .= '<select name="replace_this_sidebar" class="widefat">';
				$out .= '<option value=""' . selected( $replace_sidebar, '', false ) . '>' . __( 'None', 'bsfsidebars' ) . '</option>';

				foreach ( $sidebars as $slug => $name ) {
					if ( strrpos( $slug, BSF_SB_PREFIX ) !== false ) {
						continue;
					}
					$out .= '<option value="' . $slug . '"' . selected( $replace_sidebar, $slug, false ) . '>' . $name . '</option>';
				}
				$out .= '</select>';
			} else {
				$out .= '<p>' . __( 'Sidebars are not available.', 'bsfsidebars' ) . '</p>';
			}

						$out .= '</td>';
					$out .= '</tr>';

					$out .= '<tr class="bsf-sb-row">';
						$out .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Description', 'bsfsidebars' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Add an optional description fot the Widgets screen.', 'bsfsidebars' ) . '"></i>';
						$out .= '</td>';
						$out .= '<td class="bsf-sb-row-content">';
							$out .= '<input type="text" rows="1" name="excerpt" value="' . $post->post_excerpt . '">';
							// $out .= '<textarea rows="1" name="excerpt">' . $post->post_excerpt . '</textarea>';
						$out .= '</td>';
					$out .= '</tr>';

					$out .= '<tr class="bsf-sb-row">';
						$out .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Display On', 'bsfsidebars' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Add locations for where this sidebar should appear.', 'bsfsidebars' ) . '"></i>';
						$out .= '</td>';
						$out .= '<td class="bsf-sb-row-content">';

							ob_start();
							BSF_SB_Target_Rules_Fields::target_rule_settings_field(
								'bsf-sb-location',
								array(
									'title'          => __( 'Display Rules', 'bsfsidebars' ),
									'value'          => '[{"type":"basic-global","specific":null}]',
									'tags'           => 'site,enable,target,pages',
									'rule_type'      => 'display',
									'add_rule_label' => __( 'Add Display Rule', 'bsfsidebars' ),
								),
								$include_locations
							);
							$out .= ob_get_clean();
						$out .= '</td>';
					$out .= '</tr>';

					$out .= '<tr class="bsf-sb-row bsf-sb-hidden">';
						$out .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'Do Not Display On', 'bsfsidebars' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'This Sidebar will not appear at these locations.', 'bsfsidebars' ) . '"></i>';
						$out .= '</td>';
						$out .= '<td class="bsf-sb-row-content">';
							ob_start();
							BSF_SB_Target_Rules_Fields::target_rule_settings_field(
								'bsf-sb-exclusion',
								array(
									'title'          => __( 'Exclude On', 'bsfsidebars' ),
									'value'          => '[]',
									'tags'           => 'site,enable,target,pages',
									'add_rule_label' => __( 'Add Excludion Rule', 'bsfsidebars' ),
									'rule_type'      => 'exclude',
								),
								$exclude_locations
							);
							$out .= ob_get_clean();
						$out .= '</td>';
					$out .= '</tr>';

					$out .= '<tr class="bsf-sb-row">';
						$out .= '<td class="bsf-sb-row-heading">';
							$out .= '<label>' . esc_html__( 'User Roles', 'bsfsidebars' ) . '</label>';
							$out .= '<i class="bsf-sb-help dashicons dashicons-editor-help" title="' . esc_attr__( 'Target header based on user role.', 'bsfsidebars' ) . '"></i>';
						$out .= '</td>';
						$out .= '<td class="bsf-sb-row-content">';
							ob_start();
							BSF_SB_Target_Rules_Fields::target_user_role_settings_field(
								'bsf-sb-users',
								array(
									'title'          => __( 'Users', 'bsfsidebars' ),
									'value'          => '[]',
									'tags'           => 'site,enable,target,pages',
									'add_rule_label' => __( 'Add User Rule', 'bsfsidebars' ),
								),
								$users
							);
							$out .= ob_get_clean();
						$out .= '</td>';
					$out .= '</tr>';
				$out .= '</tbody>';
			$out .= '</table>';

			echo $out;
		}

		/**
		 * Replace sidebar metabox.
		 *
		 * @since 1.0.0
		 * @return array $sidebars_show sidebars.
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

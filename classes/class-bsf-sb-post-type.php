<?php
/**
 * BSF_SB_Post_Type
 *
 * @package BSF Custom Sidebars
 */

if ( ! class_exists( 'BSF_SB_Post_Type' ) ) {

	/**
	 * BSF_SB_Post_Type initial setup
	 *
	 * @since 1.0.0
	 */
	final class BSF_SB_Post_Type {

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
			$this->load_actions();
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function load_actions() {
			add_action( 'init', array( $this, 'register_post_type' ), 25 );
			add_action( 'admin_menu', array( $this, 'register_sidebar_manager_menu' ), 101 );
			add_action( 'admin_head', array( $this, 'menu_highlight' ) );

			if ( is_admin() ) {
				add_action( 'manage_bsf-sidebar_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
				// Filters.
				add_filter( 'manage_bsf-sidebar_posts_columns', array( $this, 'column_headings' ) );
			}
		}

		/**
		 * Adds or removes list table column headings.
		 *
		 * @param array $columns Array of columns.
		 * @return array
		 */
		public static function column_headings( $columns ) {

			unset( $columns['date'] );

			$columns['sidebar_display_rules'] = __( 'Display Rules', 'sidebar-manager' );
			$columns['date']                  = __( 'Date', 'sidebar-manager' );

			return $columns;
		}

		/**
		 * Adds the custom list table column content.
		 *
		 * @since 1.0
		 * @param array $column Name of column.
		 * @param int   $post_id Post id.
		 * @return void
		 */
		public function column_content( $column, $post_id ) {

			if ( 'sidebar_display_rules' == $column ) {

				$locations = get_post_meta( $post_id, '_bsf-sb-location', true );
				if ( ! empty( $locations ) ) {
					echo '<div class="ast-advanced-headers-location-wrap" style="margin-bottom: 5px;">';
					echo '<strong>Display: </strong>';
					$this->column_display_location_rules( $locations );
					echo '</div>';
				}

				$locations = get_post_meta( $post_id, '_bsf-sb-exclusion', true );
				if ( ! empty( $locations ) ) {
					echo '<div class="ast-advanced-headers-exclusion-wrap" style="margin-bottom: 5px;">';
					echo '<strong>Exclusion: </strong>';
					$this->column_display_location_rules( $locations );
					echo '</div>';
				}

				$users = get_post_meta( $post_id, '_bsf-sb-users', true );
				if ( isset( $users ) && is_array( $users ) ) {
					if ( isset( $users[0] ) && ! empty( $users[0] ) ) {
						$user_label = array();

						foreach ( $users as $user ) {
							$user_label[] = BSF_SB_Target_Rules_Fields::get_user_by_key( $user );
						}

						echo '<div class="ast-advanced-headers-users-wrap">';
						echo '<strong>Users: </strong>';
						$usr_label = join( ', ', $user_label );
						echo esc_html( $usr_label );
						echo '</div>';
					}
				}
			}
		}

		/**
		 * Get Markup of Location rules for Display rule column.
		 *
		 * @param array $locations Array of locations.
		 * @return void
		 */
		public function column_display_location_rules( $locations ) {

			$location_label = array();
			$index          = array_search( 'specifics', $locations['rule'] );
			if ( false !== $index && ! empty( $index ) ) {
				unset( $locations['rule'][ $index ] );
			}

			if ( isset( $locations['rule'] ) && is_array( $locations['rule'] ) ) {
				foreach ( $locations['rule'] as $location ) {
					$location_label[] = BSF_SB_Target_Rules_Fields::get_location_by_key( $location );
				}
			}
			if ( isset( $locations['specific'] ) && is_array( $locations['specific'] ) ) {
				foreach ( $locations['specific'] as $location ) {
					$location_label[] = BSF_SB_Target_Rules_Fields::get_location_by_key( $location );
				}
			}

			$lct = join( ', ', $location_label );
			echo esc_html( $lct );

		}

		/**
		 * Highlight themes.php and sidebars menu when editing sidebars.
		 *
		 * @since 1.1.1
		 * @return void
		 */
		public function menu_highlight() {
			global $parent_file, $submenu_file, $post_type;
			if ( BSF_SB_POST_TYPE == $post_type ) {
				$parent_file  = 'themes.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu_file = 'edit.php?post_type=' . BSF_SB_POST_TYPE; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		/**
		 * Register post-type for sidebars.
		 *
		 * @access public
		 * @return void
		 */
		public function register_post_type() {
			/* Allow users who can edit theme. */
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return;
			}

			$singular = __( 'Sidebar', 'sidebar-manager' );
			$plural   = __( 'Sidebars', 'sidebar-manager' );
			$rewrite  = array(
				'slug' => BSF_SB_POST_TYPE,
			);
			$supports = array( 'title', 'excerpt' );

			$labels = array(
				'name'               => _x( 'Sidebars', 'post type general name', 'sidebar-manager' ),
				'singular_name'      => _x( 'Sidebar', 'post type singular name', 'sidebar-manager' ),
				'menu_name'          => _x( 'Sidebars', 'admin menu', 'sidebar-manager' ),
				'add_new'            => __( 'Add New', 'sidebar-manager' ),
				/* translators: %s singular */
				'add_new_item'       => sprintf( __( 'Add New %s', 'sidebar-manager' ), $singular ),
				/* translators: %s singular */
				'edit_item'          => sprintf( __( 'Edit %s', 'sidebar-manager' ), $singular ),
				/* translators: %s singular */
				'new_item'           => sprintf( __( 'New %s', 'sidebar-manager' ), $singular ),
				'all_items'          => $plural,
				/* translators: %s singular */
				'view_item'          => sprintf( __( 'View %s', 'sidebar-manager' ), $singular ),
				/* translators: %s plural */
				'search_items'       => sprintf( __( 'Search %s', 'sidebar-manager' ), $plural ),
				/* translators: %s plural */
				'not_found'          => sprintf( __( 'No %s Found', 'sidebar-manager' ), $plural ),
				/* translators: %s plural */
				'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'sidebar-manager' ), $plural ),
				'parent_item_colon'  => '',

			);
			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_nav_menus'  => false,
				'show_in_admin_bar'  => false,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => $rewrite,
				'capability_type'    => 'post',
				'has_archive'        => BSF_SB_POST_TYPE,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => $supports,
			);
			register_post_type( BSF_SB_POST_TYPE, $args );
		}

		/**
		 * Register custom font menu
		 *
		 * @since 1.0.0
		 */
		public function register_sidebar_manager_menu() {

			$title = apply_filters( 'bsf_sidebar_manager_menu_title', __( 'Sidebars', 'sidebar-manager' ) );
			add_submenu_page(
				'themes.php',
				$title,
				$title,
				'edit_pages',
				'edit.php?post_type=' . BSF_SB_POST_TYPE
			);
		}
	}
}

BSF_SB_Post_Type::get_instance();

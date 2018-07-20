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
			add_action( 'init', array( $this, 'register_post_type' ), 25 );
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
				'show_in_menu'       => 'themes.php',
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
	}
}

BSF_SB_Post_Type::get_instance();

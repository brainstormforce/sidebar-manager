<?php
/**
 * BSF_SB_Target_Rules_Fields
 *
 * @package   BSF Custom Sidebars
 */

/**
 * Meta Boxes setup
 */
if ( ! class_exists( 'BSF_SB_Target_Rules_Fields' ) ) {

	/**
	 * Meta Boxes setup
	 */
	class BSF_SB_Target_Rules_Fields {


		/**
		 * Instance
		 *
		 * @since  1.0.0
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * Meta Option
		 *
		 * @since  1.0.0
		 *
		 * @var $meta_option
		 */
		private static $meta_option;

		/**
		 * Initiator
		 *
		 * @since  1.0.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'wp_ajax_bsf_sb_get_posts_by_query', array( $this, 'get_posts_by_query' ) );
		}

		/**
		 * Ajax handeler to return the posts based on the search query.
		 * When searching for the post/pages only titles are searched for.
		 *
		 * @since  1.0.0
		 */
		function get_posts_by_query() {

			$search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';
			$data          = array();
			$result        = array();

			$args = array(
				'public'   => true,
				'_builtin' => false,
			);

			$output     = 'names'; // names or objects, note names is the default.
			$operator   = 'and'; // 'and' or 'or'.
			$post_types = get_post_types( $args, $output, $operator );

			$post_types['Posts'] = 'post';
			$post_types['Pages'] = 'page';

			foreach ( $post_types as $key => $post_type ) {

				$data = array();

				add_filter( 'posts_search', array( $this, 'search_only_titles' ), 10, 2 );

				$query = new WP_Query(
					array(
						's'              => $search_string,
						'post_type'      => $post_type,
						'posts_per_page' => - 1,
					)
				);

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$title = get_the_title();
						$title .= ( 0 != $query->post->post_parent ) ? ' (' . get_the_title( $query->post->post_parent ) . ')' : '';
						$id     = get_the_id();
						$data[] = array(
							'id'   => 'post-' . $id,
							'text' => $title,
						);
					}
				}

				if ( is_array( $data ) && ! empty( $data ) ) {
					$result[] = array(
						'text'     => $key,
						'children' => $data,
					);
				}
			}

			$data = array();

			wp_reset_postdata();

			$args = array(
				'public' => true,
			);

			$output     = 'objects'; // names or objects, note names is the default.
			$operator   = 'and'; // 'and' or 'or'.
			$taxonomies = get_taxonomies( $args, $output, $operator );

			foreach ( $taxonomies as $taxonomy ) {
				$terms = get_terms(
					$taxonomy->name, array(
						'orderby'    => 'count',
						'hide_empty' => 0,
						'name__like' => $search_string,
					)
				);

				$data = array();

				$label = ucwords( $taxonomy->label );

				if ( ! empty( $terms ) ) {

					foreach ( $terms as $term ) {

						$data[] = array(
							'id'   => 'tax-' . $term->term_id,
							'text' => $term->name,
						);

					}
				}

				if ( is_array( $data ) && ! empty( $data ) ) {
					$result[] = array(
						'text'     => $label,
						'children' => $data,
					);
				}
			}

			// return the result in json.
			wp_send_json( $result );
		}

		/**
		 * Return search results only by post title.
		 * This is only run from get_posts_by_query()
		 *
		 * @param  (string)   $search   Search SQL for WHERE clause.
		 * @param  (WP_Query) $wp_query The current WP_Query object.
		 *
		 * @return (string) The Modified Search SQL for WHERE clause.
		 */
		function search_only_titles( $search, $wp_query ) {
			if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
				global $wpdb;

				$q = $wp_query->query_vars;
				$n = ! empty( $q['exact'] ) ? '' : '%';

				$search = array();

				foreach ( (array) $q['search_terms'] as $term ) {
					$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
				}

				if ( ! is_user_logged_in() ) {
					$search[] = "$wpdb->posts.post_password = ''";
				}

				$search = ' AND ' . implode( ' AND ', $search );
			}

			return $search;
		}

		/**
		 * Function Name: admin_styles.
		 * Function Description: admin_styles.
		 *
		 * @param string $hook string parameter.
		 */
		public function admin_styles( $hook ) {
			if ( 'post.php' == $hook && 'bsf-sidebar' == get_post_type() ) {
				
				wp_enqueue_script( 'bsf-sb-select2', BSF_SB_URL . 'classes/modules/target-rule/select2.js', array( 'jquery' ), BSF_SB_VER, true );
				wp_enqueue_script(
					'bsf-sb-target-rule', BSF_SB_URL . 'classes/modules/target-rule/target-rule.js', array(
						'jquery',
						'wp-util',
						'bsf-sb-select2',
					), BSF_SB_VER, true
				);
				wp_enqueue_script(
					'bsf-sb-user-role', BSF_SB_URL . 'classes/modules/target-rule/user-role.js', array(
						'jquery',
					), BSF_SB_VER, true
				);
				wp_enqueue_style( 'bsf-sb-select2', BSF_SB_URL . 'classes/modules/target-rule/select2.css', '', BSF_SB_VER );
				wp_enqueue_style( 'bsf-sb-target-rule', BSF_SB_URL . 'classes/modules/target-rule/target-rule.css', '', BSF_SB_VER );
			}
			
		}

		/**
		 * Function Name: target_rule_settings_field.
		 * Function Description: Function to handle new input type.
		 *
		 * @param string $name string parameter.
		 * @param string $settings string parameter.
		 * @param string $value string parameter.
		 */
		public static function target_rule_settings_field( $name, $settings, $value ) {
			$input_name     = $name;
			$type           = isset( $settings['type'] ) ? $settings['type'] : 'target_rule';
			$class          = isset( $settings['class'] ) ? $settings['class'] : '';
			$rule_type      = isset( $settings['rule_type'] ) ? $settings['rule_type'] : 'target_rule';
			$add_rule_label = isset( $settings['add_rule_label'] ) ? $settings['add_rule_label'] : __( 'Add Rule', 'bsfsidebars' );
			$saved_values   = $value;
			$output         = '';

			$args = array(
				'public'   => true,
				'_builtin' => true,
			);

			$builtin_post_types = get_post_types( $args, 'objects' );
			unset( $builtin_post_types['attachment'] );
			$builtin_taxonomies = get_taxonomies( $args, 'objects' );
			unset( $builtin_taxonomies['post_format'] );

			$args = array(
				'public'   => true,
				'_builtin' => false,
			);

			$custom_post_type  = get_post_types( $args, 'objects' );
			$custom_taxonomies = get_taxonomies( $args, 'objects' );

			$selection_options = array(
				'basic' => array(
					'label' => __( 'Basic', 'bsfsidebars' ),
					'value' => array(
						'basic-global'    => __( 'Entire Website', 'bsfsidebars' ),
						'basic-singulars' => __( 'All Singulars', 'bsfsidebars' ),
						'basic-archives'  => __( 'All Archives', 'bsfsidebars' ),
					),
				),

				'special-pages' => array(
					'label' => __( 'Special Pages', 'bsfsidebars' ),
					'value' => array(
						'special-404'    => __( '404 Page', 'bsfsidebars' ),
						'special-search' => __( 'Search Page', 'bsfsidebars' ),
						'special-blog'   => __( 'Blog / Posts Page', 'bsfsidebars' ),
						'special-front'  => __( 'Front Page', 'bsfsidebars' ),
						'special-date'   => __( 'Date Archive', 'bsfsidebars' ),
						'special-author' => __( 'Author Archive', 'bsfsidebars' ),
					),
				),
			);

			/* Builtin post types */
			foreach ( $builtin_post_types as $post_type ) {

				$post_opt = self::get_post_target_rule_options( $post_type, $builtin_taxonomies );

				$selection_options[ $post_opt['post_key'] ] = array(
					'label' => $post_opt['label'],
					'value' => $post_opt['value'],
				);
			}

			/* Custom post types */
			foreach ( $custom_post_type as $c_post_type ) {
				$post_opt = self::get_post_target_rule_options( $c_post_type, $custom_taxonomies );

				$selection_options[ $post_opt['post_key'] ] = array(
					'label' => $post_opt['label'],
					'value' => $post_opt['value'],
				);
			}

			$selection_options['specific-target'] = array(
				'label' => __( 'Specific Target', 'bsfsidebars' ),
				'value' => array(
					'specifics' => __( 'Specific Pages / Posts / Taxanomies, etc.', 'bsfsidebars' ),
				),
			);

			/* WP Template Format */
			$output .= '<script type="text/html" id="tmpl-bsf-sb-target-rule-' . $rule_type . '-condition">';
			$output .= '<div class="bsf-sb-target-rule-condition bsf-sb-target-rule-{{data.id}}" data-rule="{{data.id}}" >';
			$output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';
			/* Condition Selection */
			$output .= '<div class="target_rule-condition-wrap" >';
			$output .= '<select name="' . esc_attr( $input_name ) . '[rule][{{data.id}}]" class="target_rule-condition form-control bsf-sb-input">';
			$output .= '<option value="">' . __( 'Select', 'bsfsidebars' ) . '</option>';

			foreach ( $selection_options as $group => $group_data ) {

				$output .= '<optgroup label="' . $group_data['label'] . '">';
				foreach ( $group_data['value'] as $opt_key => $opt_value ) {
					$output .= '<option value="' . $opt_key . '">' . $opt_value . '</option>';
				}
				$output .= '</optgroup>';
			}
			$output .= '</select>';
			$output .= '</div>';

			$output .= '</div> <!-- bsf-sb-target-rule-condition -->';

			/* Specific page selection */
			$output .= '<div class="target_rule-specific-page-wrap" style="display:none">';
			$output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control bsf-sb-input " multiple="multiple">';
			$output .= '</select>';
			$output .= '</div>';

			$output .= '</script>';

			/* Wrapper Start */
			$output .= '<div class="bsf-sb-target-rule-wrapper bsf-sb-target-rule-' . $rule_type . '-on-wrap" data-type="' . $rule_type . '">';
			// $output .= '<input type="hidden" class="form-control bsf-sb-input bsf-sb-target_rule-input" name="' . esc_attr( $input_name ) . '" value=' . $value . ' />';
			$output .= '<div class="bsf-sb-target-rule-selector-wrapper bsf-sb-target-rule-' . $rule_type . '-on">';
			$output .= self::generate_target_rule_selector( $rule_type, $selection_options, $input_name, $saved_values, $add_rule_label );
			$output .= '</div>';

			/* Wrapper end */
			$output .= '</div>';

			echo $output;
		}

		/**
		 * Get target rules for generating the markup for rule selector.
		 *
		 * @since  1.0.0
		 *
		 * @param object $post_type post type parameter.
		 * @param object $taxonomies Taxanomies for creating the target rule markup.
		 */
		public static function get_post_target_rule_options( $post_type, $taxonomies ) {

			$post_key    = str_replace( ' ', '-', strtolower( $post_type->label ) );
			$post_label  = ucwords( $post_type->label );
			$post_name   = $post_type->name;
			$post_option = array();

			/* translators: %s percentage */
			$all_posts = sprintf( __( 'All %s', 'bsfsidebars' ), $post_label );
			/* translators: %s percentage */
			$all_archive = sprintf( __( 'All %s Archive', 'bsfsidebars' ), $post_label );

			$post_option[ $post_name . '|all' ]         = $all_posts;
			$post_option[ $post_name . '|all|archive' ] = $all_archive;

			foreach ( $taxonomies as $taxonomy ) {
				$tax_label = ucwords( $taxonomy->label );
				$tax_name  = $taxonomy->name;

				/* translators: %s percentage */
				$tax_archive = sprintf( __( 'All %s Archive', 'bsfsidebars' ), $tax_label );

				$post_option[ $post_name . '|all|taxarchive|' . $tax_name ] = $tax_archive;
			}

			$post_output['post_key'] = $post_key;
			$post_output['label']    = $post_label;
			$post_output['value']    = $post_option;

			return $post_output;
		}

		/**
		 * Generate markup for rendering the location selction.
		 *
		 * @since  1.0.0
		 * @param  String $type                 Rule type display|exclude.
		 * @param  Array  $selection_options     Array for available selection fields.
		 * @param  String $input_name           Input name for the settings.
		 * @param  Array  $saved_values          Array of saved valued.
		 * @param  String $add_rule_label       Label for the Add rule button.
		 *
		 * @return HTML Markup for for the location settings.
		 */
		public static function generate_target_rule_selector( $type, $selection_options, $input_name, $saved_values, $add_rule_label ) {

			$output = '<div class="target_rule-builder-wrap">';

			if ( ! is_array( $saved_values ) || ( is_array( $saved_values ) && empty( $saved_values ) ) ) {
				$saved_values                = array();
				$saved_values['rule'][0]     = '';
				$saved_values['specific'][0] = '';
			}

			$index = 0;

			foreach ( $saved_values['rule'] as $index => $data ) {

				$output .= '<div class="bsf-sb-target-rule-condition bsf-sb-target-rule-' . $index . '" data-rule="' . $index . '" >';
				/* Condition Selection */
				$output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';
				$output .= '<div class="target_rule-condition-wrap" >';
				$output .= '<select name="' . esc_attr( $input_name ) . '[rule][' . $index . ']" class="target_rule-condition form-control bsf-sb-input">';
				$output .= '<option value="">' . __( 'Select', 'bsfsidebars' ) . '</option>';

				foreach ( $selection_options as $group => $group_data ) {

					$output .= '<optgroup label="' . $group_data['label'] . '">';
					foreach ( $group_data['value'] as $opt_key => $opt_value ) {

						// specific rules.
						$selected = '';

						if ( $data == $opt_key ) {
							$selected = 'selected="selected"';
						}

						$output .= '<option value="' . $opt_key . '" ' . $selected . '>' . $opt_value . '</option>';
					}
					$output .= '</optgroup>';
				}
				$output .= '</select>';
				$output .= '</div>';

				$output .= '</div>';

				if ( 'specifics' != $data ) {
					/* Specific page selection */
					$output .= '<div class="target_rule-specific-page-wrap" style="display:none">';
					$output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control bsf-sb-input " multiple="multiple">';
					$output .= '</select>';
					$output .= '</div>';
				}
			}

			/* Specific page selection */
			$output .= '<div class="target_rule-specific-page-wrap" style="display:none">';
			$output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control bsf-sb-input " multiple="multiple">';

			if ( isset( $saved_values['specific'] ) && null != $saved_values['specific'] && is_array( $saved_values['specific'] ) ) {

				foreach ( $saved_values['specific'] as $data_key => $sel_value ) {
					// posts.
					if ( strpos( $sel_value, 'post-' ) !== false ) {
						$post_id    = (int) str_replace( 'post-', '', $sel_value );
						$post_title = get_the_title( $post_id );
						$output .= '<option value="post-' . $post_id . '" selected="selected" >' . $post_title . '</option>';
					}

						// taxonomy options.
					if ( strpos( $sel_value, 'tax-' ) !== false ) {
						$tax_id        = (int) str_replace( 'tax-', '', $sel_value );
						$term          = get_term( $tax_id );
						$term_taxonomy = ucfirst( str_replace( '_', ' ', $term->taxonomy ) );
						$output .= '<option value="tax-' . $tax_id . '" selected="selected" >' . $term->name . ' - ' . $term_taxonomy . '</option>';

					}
				}
			}
			$output .= '</select>';
			$output .= '</div>';

			$output .= '</div>';

			/* Add new rule */
			$output .= '<div class="target_rule-add-rule-wrap">';
			$output .= '<a href="#" class="button" data-rule-id="' . absint( $index ) . '" data-rule-type="' . $type . '">' . $add_rule_label . '</a>';
			$output .= '</div>';

			if ( 'display' == $type ) {
				/* Add new rule */
				$output .= '<div class="target_rule-add-exclusion-rule">';
				$output .= '<a href="#" class="button">' . __( 'Add Exclusion Rule', 'bsfsidebars' ) . '</a>';
				$output .= '</div>';
			}

			return $output;
		}

		/**
		 * Get current layout.
		 * Checks of the passed post id of the layout is to be displayed in the page.
		 *
		 * @param (String) $layout_id Layout ID.
		 *
		 * @return int|boolean If the current layout is to be displayed it will be returned back else a boolean will be passed.
		 */
		public function get_current_layout( $layout_id ) {
			$post_id        = ( ! is_404() && ! is_search() && ! is_archive() && ! is_home() ) ? get_the_id() : false;
			$current_layout = false;
			$is_exclude     = false;
			$is_user_role   = false;
			$display_on     = get_post_meta( $layout_id, '_bsf-sb-location', true );
			$exclude_on     = get_post_meta( $layout_id, '_bsf-sb-exclusion', true );
			$user_roles     = get_post_meta( $layout_id, '_bsf-sb-users', true );
			/* Parse Display On Condition */
			$is_display = $this->parse_layout_display_condition( $post_id, $display_on );

			if ( true == $is_display ) {
				/* Parse Exclude On Condition */
				$is_exclude = $this->parse_layout_display_condition( $post_id, $exclude_on );
				/* Parse User Role Condition */
				$is_user_role = $this->parse_user_role_condition( $post_id, $user_roles );
			}

			if ( $is_display && ! $is_exclude && $is_user_role ) {
				$current_layout = $layout_id;
			}

			// filter target page settings.
			$current_layout = apply_filters( 'bsf_cb_target_page_settings', $current_layout, $layout_id );

			return $current_layout;
		}

		/**
		 * Checks for the display condition for the current page/
		 *
		 * @param  int   $post_id Current post ID.
		 * @param  Array $rules   Array of rules Display on | Exclude on.
		 *
		 * @return Boolean      Returns true or false depending on if the $rules match for the current page and the layout is to be displayed.
		 */
		public function parse_layout_display_condition( $post_id, $rules ) {

			$show_popup        = false;
			$current_post_type = get_post_type( $post_id );

			if ( isset( $rules['rule'] ) && is_array( $rules['rule'] ) && ! empty( $rules['rule'] ) ) {
				foreach ( $rules['rule'] as $key => $rule ) {

					if ( strrpos( $rule, 'all' ) !== false ) {
						$rule_case = 'all';
					} else {
						$rule_case = $rule;
					}

					switch ( $rule_case ) {
						case 'basic-global':
							$show_popup = true;
							break;

						case 'basic-singulars':
							if ( is_singular() ) {
								$show_popup = true;
							}
							break;

						case 'basic-archives':
							if ( is_archive() ) {
								$show_popup = true;
							}
							break;

						case 'special-404':
							if ( is_404() ) {
								$show_popup = true;
							}
							break;

						case 'special-search':
							if ( is_search() ) {
								$show_popup = true;
							}
							break;

						case 'special-blog':
							if ( is_home() ) {
								$show_popup = true;
							}
							break;

						case 'special-front':
							if ( is_front_page() ) {
								$show_popup = true;
							}
							break;

						case 'special-date':
							if ( is_date() ) {
								$show_popup = true;
							}
							break;

						case 'special-author':
							if ( is_author() ) {
								$show_popup = true;
							}
							break;

						case 'all':
							$rule_data = explode( '|', $rule );

							$post_type     = isset( $rule_data[0] ) ? $rule_data[0] : false;
							$archieve_type = isset( $rule_data[2] ) ? $rule_data[2] : false;
							$taxonomy      = isset( $rule_data[3] ) ? $rule_data[3] : false;

							if ( false === $archieve_type ) {

								$current_post_type = get_post_type( $post_id );

								if ( false !== $post_id && $current_post_type == $post_type ) {

									$show_popup = true;
								}
							} else {

								if ( is_archive() ) {

									$current_post_type = get_post_type();
									if ( $current_post_type == $post_type ) {
										if ( 'archive' == $archieve_type ) {
											$show_popup = true;
										} elseif ( 'taxarchive' == $archieve_type ) {

											$obj              = get_queried_object();
											$current_taxonomy = '';
											if ( '' !== $obj && null !== $obj ) {
												$current_taxonomy = $obj->taxonomy;
											}

											if ( $current_taxonomy == $taxonomy ) {
												$show_popup = true;
											}
										}
									}
								}
							}
							break;

						case 'specifics':
							if ( isset( $rules['specific'] ) && is_array( $rules['specific'] ) ) {
								foreach ( $rules['specific'] as $specific_page ) {

									$specific_data      = explode( '-', $specific_page );
									$specific_post_type = isset( $specific_data[0] ) ? $specific_data[0] : false;
									$specific_post_id   = isset( $specific_data[1] ) ? $specific_data[1] : false;
									if ( 'post' == $specific_post_type ) {
										if ( $specific_post_id == $post_id ) {
											$show_popup = true;
										}
									} elseif ( 'tax' == $specific_post_type ) {
										$tax_id = get_queried_object_id();
										if ( $specific_post_id == $tax_id ) {
											$show_popup = true;
										}
									}
								}
							}
							break;

						default:
							break;
					}

					if ( $show_popup ) {
						break;
					}
				}
			}

			return $show_popup;
		}

		/**
		 * Function Name: target_user_role_settings_field.
		 * Function Description: Function to handle new input type.
		 *
		 * @param string $name string parameter.
		 * @param string $settings string parameter.
		 * @param string $value string parameter.
		 */
		public static function target_user_role_settings_field( $name, $settings, $value ) {
			$input_name     = $name;
			$type           = isset( $settings['type'] ) ? $settings['type'] : 'target_rule';
			$class          = isset( $settings['class'] ) ? $settings['class'] : '';
			$rule_type      = isset( $settings['rule_type'] ) ? $settings['rule_type'] : 'target_rule';
			$add_rule_label = isset( $settings['add_rule_label'] ) ? $settings['add_rule_label'] : __( 'Add Rule', 'bsfsidebars' );
			$saved_values   = $value;
			$output         = '';

			$selection_options = array(
				'basic' => array(
					'label' => __( 'Basic', 'bsfsidebars' ),
					'value' => array(
						'all'           => __( 'All', 'bsfsidebars' ),
						'logged-in'     => __( 'Logged In', 'bsfsidebars' ),
						'logged-out'    => __( 'Logged Out', 'bsfsidebars' ),
					),
				),

				'advanced' => array(
					'label' => __( 'Advanced', 'bsfsidebars' ),
					'value' => array(),
				),
			);

			/* User roles */
			$roles = get_editable_roles();

			foreach ( $roles as $slug => $data ) {
				$selection_options['advanced']['value'][ $slug ] = $data['name'];
			}

			/* WP Template Format */
			$output .= '<script type="text/html" id="tmpl-bsf-sb-user-role-condition">';
				$output .= '<div class="bsf-sb-user-role-condition bsf-sb-user-role-{{data.id}}" data-rule="{{data.id}}" >';
					$output .= '<span class="user_role-condition-delete dashicons dashicons-dismiss"></span>';
					/* Condition Selection */
					$output .= '<div class="user_role-condition-wrap" >';
						$output .= '<select name="' . esc_attr( $input_name ) . '[{{data.id}}]" class="user_role-condition form-control bsf-sb-input">';
						$output .= '<option value="">' . __( 'Select', 'bsfsidebars' ) . '</option>';

			foreach ( $selection_options as $group => $group_data ) {

				$output .= '<optgroup label="' . $group_data['label'] . '">';
				foreach ( $group_data['value'] as $opt_key => $opt_value ) {
					$output .= '<option value="' . $opt_key . '">' . $opt_value . '</option>';
				}
				$output .= '</optgroup>';
			}
						$output .= '</select>';
					$output .= '</div>';
				$output .= '</div> <!-- bsf-sb-user-role-condition -->';
			$output .= '</script>';

			if ( ! is_array( $saved_values ) || ( is_array( $saved_values ) && empty( $saved_values ) ) ) {

				$saved_values        = array();
				$saved_values[0]     = '';
			}

			$index = 0;

			$output .= '<div class="bsf-sb-user-role-wrapper bsf-sb-user-role-display-on-wrap" data-type="display">';
				$output .= '<div class="bsf-sb-user-role-selector-wrapper bsf-sb-user-role-display-on">';
					$output .= '<div class="user_role-builder-wrap">';
			foreach ( $saved_values as $index => $data ) {
				$output .= '<div class="bsf-sb-user-role-condition bsf-sb-user-role-' . $index . '" data-rule="' . $index . '" >';
					$output .= '<span class="user_role-condition-delete dashicons dashicons-dismiss"></span>';
					/* Condition Selection */
					$output .= '<div class="user_role-condition-wrap" >';
						$output .= '<select name="' . esc_attr( $input_name ) . '[' . $index . ']" class="user_role-condition form-control bsf-sb-input">';
						$output .= '<option value="">' . __( 'Select', 'bsfsidebars' ) . '</option>';

				foreach ( $selection_options as $group => $group_data ) {

					$output .= '<optgroup label="' . $group_data['label'] . '">';
					foreach ( $group_data['value'] as $opt_key => $opt_value ) {

						$output .= '<option value="' . $opt_key . '" ' . selected( $data, $opt_key, false ) . '>' . $opt_value . '</option>';
					}
					$output .= '</optgroup>';
				}
						$output .= '</select>';
					$output .= '</div>';
						$output .= '</div> <!-- bsf-sb-user-role-condition -->';
			}
					$output .= '</div>';
					/* Add new rule */
					$output .= '<div class="user_role-add-rule-wrap">';
					$output .= '<a href="#" class="button" data-rule-id="' . absint( $index ) . '">' . $add_rule_label . '</a>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

			echo $output;
		}

		/**
		 * Parse user role condition.
		 *
		 * @since  1.0.0
		 * @param  int   $post_id Post ID.
		 * @param  Array $rules   Current user rules.
		 *
		 * @return boolean  True = user condition passes. False = User condition does not pass.
		 */
		public function parse_user_role_condition( $post_id, $rules ) {

			$show_popup = false;

			if ( is_array( $rules ) && ! empty( $rules ) ) {

				foreach ( $rules as $i => $rule ) {

					switch ( $rule ) {
						case '':
						case 'all':
							$show_popup        = true;
							break;

						case 'logged-in':
							if ( is_user_logged_in() ) {
								$show_popup = true;
							}
							break;

						case 'logged-out':
							if ( ! is_user_logged_in() ) {
								$show_popup = true;
							}
							break;

						default:
							if ( is_user_logged_in() ) {

								$current_user = wp_get_current_user();

								if ( isset( $current_user->roles )
										&& is_array( $current_user->roles )
										&& in_array( $rule, $current_user->roles )
									) {

									$show_popup = true;
								}
							}
							break;
					}

					if ( $show_popup ) {
						break;
					}
				}
			}

			return $show_popup;
		}
	}
}// End if().

/**
 * Kicking this off by calling 'get_instance()' method
 */
BSF_SB_Target_Rules_Fields::get_instance();

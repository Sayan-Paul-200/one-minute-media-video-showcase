<?php

/**
 * Register the Video Category taxonomy.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Register the Video Category taxonomy for Video Case Studies.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Taxonomy_Video_Category {

	const TAXONOMY    = 'ommvs_video_category';
	const OBJECT_TYPE = 'video_case_study';

	const NONCE_ACTION = 'ommvs_save_video_category';
	const NONCE_NAME   = 'ommvs_video_category_nonce';
	const FIELD_NAME   = 'ommvs_video_category_term';

	const ADD_NONCE_ACTION = 'ommvs_add_video_category';

	/**
	 * Register the ommvs_video_category taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_taxonomy() {

		if ( function_exists( 'taxonomy_exists' ) && taxonomy_exists( self::TAXONOMY ) ) {
			return;
		}

		if ( ! function_exists( 'register_taxonomy' ) ) {
			return;
		}

		$labels = array(
			'name'                       => __( 'Video Categories', 'one-minute-media-video-showcase' ),
			'singular_name'              => __( 'Video Category', 'one-minute-media-video-showcase' ),
			'search_items'               => __( 'Search Video Categories', 'one-minute-media-video-showcase' ),
			'popular_items'              => __( 'Popular Video Categories', 'one-minute-media-video-showcase' ),
			'all_items'                  => __( 'All Video Categories', 'one-minute-media-video-showcase' ),
			'parent_item'                => __( 'Parent Video Category', 'one-minute-media-video-showcase' ),
			'parent_item_colon'          => __( 'Parent Video Category:', 'one-minute-media-video-showcase' ),
			'edit_item'                  => __( 'Edit Video Category', 'one-minute-media-video-showcase' ),
			'view_item'                  => __( 'View Video Category', 'one-minute-media-video-showcase' ),
			'update_item'                => __( 'Update Video Category', 'one-minute-media-video-showcase' ),
			'add_new_item'               => __( 'Add New Video Category', 'one-minute-media-video-showcase' ),
			'new_item_name'              => __( 'New Video Category Name', 'one-minute-media-video-showcase' ),
			'separate_items_with_commas' => __( 'Separate video categories with commas', 'one-minute-media-video-showcase' ),
			'add_or_remove_items'        => __( 'Add or remove video categories', 'one-minute-media-video-showcase' ),
			'choose_from_most_used'      => __( 'Choose from the most used video categories', 'one-minute-media-video-showcase' ),
			'not_found'                  => __( 'No video categories found.', 'one-minute-media-video-showcase' ),
			'no_terms'                   => __( 'No video categories', 'one-minute-media-video-showcase' ),
			'filter_by_item'             => __( 'Filter by video category', 'one-minute-media-video-showcase' ),
			'items_list_navigation'      => __( 'Video categories list navigation', 'one-minute-media-video-showcase' ),
			'items_list'                 => __( 'Video categories list', 'one-minute-media-video-showcase' ),
			'back_to_items'              => __( 'Back to video categories', 'one-minute-media-video-showcase' ),
			'menu_name'                  => __( 'Video Categories', 'one-minute-media-video-showcase' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Categories displayed with Video Case Study modal titles and related cards.', 'one-minute-media-video-showcase' ),
			'public'             => false,
			'publicly_queryable' => false,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_admin_column'  => true,
			'show_in_quick_edit' => false,
			'show_in_rest'       => true,
			'meta_box_cb'        => array( $this, 'render_single_select_metabox' ),
			'rewrite'            => false,
			'query_var'          => false,
		);

		register_taxonomy( self::TAXONOMY, array( self::OBJECT_TYPE ), $args );

	}

	/**
	 * Render a single-select metabox for Video Category terms.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Current post object.
	 * @param    array      $box     Metabox args.
	 */
	public function render_single_select_metabox( $post, $box = array() ) {

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$selected_term_id = $this->get_stable_selected_term_id( $post->ID );
		$terms            = get_terms(
			array(
				'taxonomy'   => self::TAXONOMY,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		?>
		<div class="ommvs-video-category-metabox" data-ommvs-video-category-metabox>
			<label class="screen-reader-text" for="ommvs-video-category-term">
				<?php esc_html_e( 'Video Category', 'one-minute-media-video-showcase' ); ?>
			</label>
			<select id="ommvs-video-category-term" name="<?php echo esc_attr( self::FIELD_NAME ); ?>" class="widefat" data-ommvs-video-category-select>
				<option value=""><?php esc_html_e( 'No category', 'one-minute-media-video-showcase' ); ?></option>
				<?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_term_id, $term->term_id ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<p class="description ommvs-video-category-metabox__note">
				<?php esc_html_e( 'Choose one category, or leave empty. Only one Video Category is used per Video Case Study.', 'one-minute-media-video-showcase' ); ?>
			</p>
			<?php if ( $this->current_user_can_manage_terms() ) : ?>
				<details class="ommvs-video-category-metabox__new" data-ommvs-video-category-add>
					<summary class="ommvs-video-category-metabox__toggle">
						<?php esc_html_e( '+ Add New Video Category', 'one-minute-media-video-showcase' ); ?>
					</summary>
					<div class="ommvs-video-category-metabox__form" data-ommvs-video-category-form>
						<label for="ommvs-video-category-new-name">
							<?php esc_html_e( 'New Video Category Name', 'one-minute-media-video-showcase' ); ?>
						</label>
						<div class="ommvs-video-category-metabox__form-row">
							<input type="text" id="ommvs-video-category-new-name" class="regular-text" data-ommvs-video-category-name>
							<button type="button" class="button" data-ommvs-video-category-submit>
								<?php esc_html_e( 'Add New Video Category', 'one-minute-media-video-showcase' ); ?>
							</button>
						</div>
						<p class="description ommvs-video-category-metabox__status" data-ommvs-video-category-status role="status" aria-live="polite"></p>
					</div>
				</details>
			<?php endif; ?>
			<p class="ommvs-video-category-metabox__manage">
				<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=' . self::TAXONOMY . '&post_type=' . self::OBJECT_TYPE ) ); ?>">
					<?php esc_html_e( 'Manage Video Categories', 'one-minute-media-video-showcase' ); ?>
				</a>
			</p>
		</div>
		<?php

	}

	/**
	 * Create a Video Category from the custom Video Case Study metabox.
	 *
	 * @since    1.0.0
	 */
	public function ajax_add_category() {

		check_ajax_referer( self::ADD_NONCE_ACTION, 'nonce' );

		if ( ! $this->current_user_can_manage_terms() ) {
			wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to add Video Categories.', 'one-minute-media-video-showcase' ),
				),
				403
			);
		}

		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$name = trim( $name );

		if ( '' === $name ) {
			wp_send_json_error(
				array(
					'message' => __( 'Enter a category name first.', 'one-minute-media-video-showcase' ),
				),
				400
			);
		}

		$existing = term_exists( $name, self::TAXONOMY );

		if ( is_array( $existing ) && ! empty( $existing['term_id'] ) ) {
			$term = get_term( absint( $existing['term_id'] ), self::TAXONOMY );

			if ( $term instanceof WP_Term ) {
				wp_send_json_success(
					array(
						'message' => __( 'Category already exists and is now selected.', 'one-minute-media-video-showcase' ),
						'term'    => $this->prepare_term_response( $term ),
					)
				);
			}
		}

		$inserted = wp_insert_term( $name, self::TAXONOMY );

		if ( is_wp_error( $inserted ) ) {
			$existing_term_id = absint( $inserted->get_error_data( 'term_exists' ) );

			if ( $existing_term_id ) {
				$term = get_term( $existing_term_id, self::TAXONOMY );

				if ( $term instanceof WP_Term ) {
					wp_send_json_success(
						array(
							'message' => __( 'Category already exists and is now selected.', 'one-minute-media-video-showcase' ),
							'term'    => $this->prepare_term_response( $term ),
						)
					);
				}
			}

			wp_send_json_error(
				array(
					'message' => $inserted->get_error_message(),
				),
				400
			);
		}

		$term = get_term( absint( $inserted['term_id'] ), self::TAXONOMY );

		if ( ! $term instanceof WP_Term ) {
			wp_send_json_error(
				array(
					'message' => __( 'The category was created, but could not be loaded. Please refresh the page.', 'one-minute-media-video-showcase' ),
				),
				500
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Category added and selected.', 'one-minute-media-video-showcase' ),
				'term'    => $this->prepare_term_response( $term ),
			)
		);

	}

	/**
	 * Enforce zero-or-one Video Category assignment on normal saves.
	 *
	 * @since    1.0.0
	 * @param    int        $post_id    Current post ID.
	 * @param    WP_Post    $post       Current post object.
	 */
	public function save_single_category( $post_id, $post = null ) {

		$post_id = absint( $post_id );

		if ( ! $post_id || ( $post instanceof WP_Post && self::OBJECT_TYPE !== $post->post_type ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! $this->current_user_can_assign_terms() ) {
			return;
		}

		if ( isset( $_POST[ self::NONCE_NAME ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			$term_id = isset( $_POST[ self::FIELD_NAME ] ) ? absint( wp_unslash( $_POST[ self::FIELD_NAME ] ) ) : 0;

			$this->set_single_category( $post_id, $term_id );
			return;
		}

		$this->collapse_multiple_categories( $post_id );

	}

	/**
	 * Set a single category term, or clear categories when no valid term is selected.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current post ID.
	 * @param    int    $term_id    Selected term ID.
	 */
	private function set_single_category( $post_id, $term_id ) {

		$term_id = absint( $term_id );

		if ( ! $term_id || ! $this->term_exists( $term_id ) ) {
			wp_set_object_terms( $post_id, array(), self::TAXONOMY, false );
			return;
		}

		wp_set_object_terms( $post_id, array( $term_id ), self::TAXONOMY, false );

	}

	/**
	 * Collapse legacy/external multiple category assignments to one stable term.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current post ID.
	 */
	private function collapse_multiple_categories( $post_id ) {

		$terms = $this->get_assigned_terms( $post_id );

		if ( count( $terms ) < 2 ) {
			return;
		}

		$term = reset( $terms );

		if ( $term instanceof WP_Term ) {
			wp_set_object_terms( $post_id, array( absint( $term->term_id ) ), self::TAXONOMY, false );
		}

	}

	/**
	 * Get the stable selected term ID for editor display.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current post ID.
	 * @return   int
	 */
	private function get_stable_selected_term_id( $post_id ) {

		$terms = $this->get_assigned_terms( $post_id );

		if ( empty( $terms ) ) {
			return 0;
		}

		$term = reset( $terms );

		return $term instanceof WP_Term ? absint( $term->term_id ) : 0;

	}

	/**
	 * Get assigned Video Category terms in stable term ID order.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current post ID.
	 * @return   WP_Term[]
	 */
	private function get_assigned_terms( $post_id ) {

		$terms = wp_get_object_terms(
			absint( $post_id ),
			self::TAXONOMY,
			array(
				'fields' => 'all',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		usort(
			$terms,
			static function ( $first_term, $second_term ) {
				return absint( $first_term->term_id ?? 0 ) <=> absint( $second_term->term_id ?? 0 );
			}
		);

		return $terms;

	}

	/**
	 * Check whether a term exists in the Video Category taxonomy.
	 *
	 * @since    1.0.0
	 * @param    int    $term_id    Term ID.
	 * @return   bool
	 */
	private function term_exists( $term_id ) {

		$term = get_term( absint( $term_id ), self::TAXONOMY );

		return $term instanceof WP_Term;

	}

	/**
	 * Check whether the current user can assign Video Category terms.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	private function current_user_can_assign_terms() {

		$taxonomy = get_taxonomy( self::TAXONOMY );

		if ( ! $taxonomy || empty( $taxonomy->cap->assign_terms ) ) {
			return current_user_can( 'edit_posts' );
		}

		return current_user_can( $taxonomy->cap->assign_terms );

	}

	/**
	 * Check whether the current user can create/manage Video Category terms.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	private function current_user_can_manage_terms() {

		$taxonomy = get_taxonomy( self::TAXONOMY );

		if ( ! $taxonomy || empty( $taxonomy->cap->manage_terms ) ) {
			return current_user_can( 'manage_categories' );
		}

		return current_user_can( $taxonomy->cap->manage_terms );

	}

	/**
	 * Prepare a term payload for the admin AJAX response.
	 *
	 * @since    1.0.0
	 * @param    WP_Term    $term    Category term.
	 * @return   array
	 */
	private function prepare_term_response( $term ) {

		return array(
			'id'   => absint( $term->term_id ),
			'name' => html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'slug' => $term->slug,
		);

	}

}

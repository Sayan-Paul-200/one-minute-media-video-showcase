<?php

/**
 * Register the Video Case Study custom post type.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Register the Video Case Study custom post type.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_CPT_Video {

	const POST_TYPE = 'video_case_study';

	const FILTER_ACTIVE       = 'ommvs_active_filter';
	const FILTER_CATEGORY     = 'ommvs_video_category_filter';
	const FILTER_REQUIRED     = 'ommvs_required_filter';
	const FILTER_VIMEO_STATUS = 'ommvs_vimeo_filter';

	/**
	 * Register the video_case_study custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {

		$post_type = self::POST_TYPE;

		if ( function_exists( 'post_type_exists' ) && post_type_exists( $post_type ) ) {
			return;
		}

		if ( ! function_exists( 'register_post_type' ) ) {
			return;
		}

		$labels = array(
			'name'                  => __( 'Video Case Studies', 'one-minute-media-video-showcase' ),
			'singular_name'         => __( 'Video Case Study', 'one-minute-media-video-showcase' ),
			'menu_name'             => __( 'Video Case Studies', 'one-minute-media-video-showcase' ),
			'name_admin_bar'        => __( 'Video Case Study', 'one-minute-media-video-showcase' ),
			'add_new'               => __( 'Add New', 'one-minute-media-video-showcase' ),
			'add_new_item'          => __( 'Add New Video Case Study', 'one-minute-media-video-showcase' ),
			'new_item'              => __( 'New Video Case Study', 'one-minute-media-video-showcase' ),
			'edit_item'             => __( 'Edit Video Case Study', 'one-minute-media-video-showcase' ),
			'view_item'             => __( 'View Video Case Study', 'one-minute-media-video-showcase' ),
			'view_items'            => __( 'View Video Case Studies', 'one-minute-media-video-showcase' ),
			'all_items'             => __( 'All Video Case Studies', 'one-minute-media-video-showcase' ),
			'search_items'          => __( 'Search Video Case Studies', 'one-minute-media-video-showcase' ),
			'parent_item_colon'     => __( 'Parent Video Case Study:', 'one-minute-media-video-showcase' ),
			'not_found'             => __( 'No video case studies found.', 'one-minute-media-video-showcase' ),
			'not_found_in_trash'    => __( 'No video case studies found in Trash.', 'one-minute-media-video-showcase' ),
			'archives'              => __( 'Video Case Study Archives', 'one-minute-media-video-showcase' ),
			'attributes'            => __( 'Video Case Study Attributes', 'one-minute-media-video-showcase' ),
			'insert_into_item'      => __( 'Insert into video case study', 'one-minute-media-video-showcase' ),
			'uploaded_to_this_item' => __( 'Uploaded to this video case study', 'one-minute-media-video-showcase' ),
			'filter_items_list'     => __( 'Filter video case studies list', 'one-minute-media-video-showcase' ),
			'items_list_navigation' => __( 'Video case studies list navigation', 'one-minute-media-video-showcase' ),
			'items_list'            => __( 'Video case studies list', 'one-minute-media-video-showcase' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Reusable video case studies for page-specific video showcase grids and modals.', 'one-minute-media-video-showcase' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
			'menu_icon'           => 'dashicons-video-alt3',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		);

		register_post_type( $post_type, $args );

	}

	/**
	 * Remove native slug UI from the non-public Video Case Study editor.
	 *
	 * The frontend hash is stored in the plugin-owned Hash Slug field, not in
	 * WordPress post_name.
	 *
	 * @since    1.0.0
	 * @param    WP_Post|null    $post    Current Video Case Study post.
	 */
	public function remove_slug_metabox( $post = null ) {

		unset( $post );

		remove_meta_box( 'slugdiv', self::POST_TYPE, 'normal' );

	}

	/**
	 * Add Video Case Study admin list-table columns.
	 *
	 * @since    1.0.0
	 * @param    array    $columns    Existing list-table columns.
	 * @return   array
	 */
	public function filter_admin_columns( $columns ) {

		$updated_columns = array();
		$taxonomy_column = 'taxonomy-' . $this->get_video_category_taxonomy();

		foreach ( $columns as $key => $label ) {
			if ( $taxonomy_column === $key ) {
				continue;
			}

			$updated_columns[ $key ] = $label;

			if ( 'cb' === $key ) {
				$updated_columns['ommvs_thumbnail'] = __( 'Thumbnail', 'one-minute-media-video-showcase' );
			}

			if ( 'title' === $key ) {
				$updated_columns['ommvs_hash_slug']            = __( 'Hash slug', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_active_status']        = __( 'Active', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_video_category']       = __( 'Video Category', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_vimeo_url_status']     = __( 'Vimeo URL', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_modal_content_status'] = __( 'Modal Content', 'one-minute-media-video-showcase' );
			}
		}

		return $updated_columns;

	}

	/**
	 * Render custom Video Case Study admin list-table column content.
	 *
	 * @since    1.0.0
	 * @param    string    $column     Current column name.
	 * @param    int       $post_id    Current Video Case Study post ID.
	 */
	public function render_admin_column( $column, $post_id ) {

		$post_id = absint( $post_id );

		switch ( $column ) {
			case 'ommvs_thumbnail':
				$this->render_thumbnail_column( $post_id );
				break;

			case 'ommvs_hash_slug':
				$this->render_hash_slug_column( $post_id );
				break;

			case 'ommvs_active_status':
				$this->render_active_status_column( $post_id );
				break;

			case 'ommvs_video_category':
				$this->render_video_category_column( $post_id );
				break;

			case 'ommvs_vimeo_url_status':
				$this->render_vimeo_url_status_column( $post_id );
				break;

			case 'ommvs_modal_content_status':
				$this->render_modal_content_status_column( $post_id );
				break;
		}

	}

	/**
	 * Render Video Case Study list-table filters.
	 *
	 * @since    1.0.0
	 * @param    string    $post_type    Current post type.
	 * @param    string    $which        Current table navigation position.
	 */
	public function render_admin_filters( $post_type, $which = 'top' ) {

		if ( self::POST_TYPE !== $post_type || 'top' !== $which ) {
			return;
		}

		$this->render_active_filter();
		$this->render_category_filter();
		$this->render_required_filter();
		$this->render_vimeo_filter();

	}

	/**
	 * Apply Video Case Study list-table filters to the main admin query.
	 *
	 * @since    1.0.0
	 * @param    WP_Query    $query    Current query.
	 */
	public function filter_admin_query( $query ) {

		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		$matched_ids = $this->get_filtered_admin_post_ids();

		if ( null === $matched_ids ) {
			return;
		}

		$existing_post_in = $query->get( 'post__in' );

		if ( is_array( $existing_post_in ) && ! empty( $existing_post_in ) ) {
			$matched_ids = array_values( array_intersect( array_map( 'absint', $existing_post_in ), $matched_ids ) );
		}

		$query->set( 'post__in', ! empty( $matched_ids ) ? $matched_ids : array( 0 ) );

	}

	/**
	 * Add Video Case Study list-table row actions.
	 *
	 * @since    1.0.0
	 * @param    array      $actions    Existing row actions.
	 * @param    WP_Post    $post       Current post.
	 * @return   array
	 */
	public function filter_row_actions( $actions, $post ) {

		if ( ! $post instanceof WP_Post || self::POST_TYPE !== $post->post_type ) {
			return $actions;
		}

		$hash_slug = $this->get_normalized_hash_slug( $post->ID );

		if ( '' !== $hash_slug ) {
			$actions['ommvs_copy_hash'] = sprintf(
				'<button type="button" class="button-link ommvs-admin-copy-hash" data-ommvs-copy-hash="%1$s">%2$s</button><span class="ommvs-admin-copy-hash__status" data-ommvs-copy-hash-status aria-live="polite"></span>',
				esc_attr( '#' . $hash_slug ),
				esc_html__( 'Copy hash', 'one-minute-media-video-showcase' )
			);
		}

		$actions['ommvs_data_health'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'edit.php?post_type=' . self::POST_TYPE . '&page=ommvs-data-health' ) ),
			esc_html__( 'Data Health', 'one-minute-media-video-showcase' )
		);

		return $actions;

	}

	/**
	 * Render the thumbnail column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_thumbnail_column( $post_id ) {

		$thumbnail_id = absint( get_post_meta( $post_id, OMMVS_Fields::FIELD_CARD_THUMBNAIL, true ) );

		if ( ! $thumbnail_id || 'attachment' !== get_post_type( $thumbnail_id ) ) {
			$thumbnail_id = absint( get_post_thumbnail_id( $post_id ) );
		}

		if ( ! $thumbnail_id || 'attachment' !== get_post_type( $thumbnail_id ) ) {
			$this->render_empty_column_value();
			return;
		}

		echo wp_kses_post(
			wp_get_attachment_image(
				$thumbnail_id,
				array( 64, 36 ),
				false,
				array(
					'class' => 'ommvs-admin-video-thumbnail__image',
				)
			)
		);

	}

	/**
	 * Render the hash slug column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_hash_slug_column( $post_id ) {

		$hash_slug = $this->get_normalized_hash_slug( $post_id );

		if ( '' === $hash_slug ) {
			$this->render_status_badge( __( 'Missing', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		printf(
			'<code class="ommvs-admin-hash">%s</code>',
			esc_html( '#' . $hash_slug )
		);

	}

	/**
	 * Render the active status column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_active_status_column( $post_id ) {

		$active = get_post_meta( $post_id, OMMVS_Fields::FIELD_IS_ACTIVE, true );

		if ( '' === $active || '0' !== (string) $active ) {
			$this->render_status_badge( __( 'Active', 'one-minute-media-video-showcase' ), 'success' );
			return;
		}

		$this->render_status_badge( __( 'Inactive', 'one-minute-media-video-showcase' ), 'muted' );

	}

	/**
	 * Render the Video Category column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_video_category_column( $post_id ) {

		$terms = $this->get_video_category_terms( $post_id );

		if ( empty( $terms ) ) {
			$this->render_status_badge( __( 'Missing', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		$display_term = reset( $terms );

		printf(
			'<span class="ommvs-admin-category">%s</span>',
			esc_html( $display_term->name )
		);

		if ( count( $terms ) > 1 ) {
			echo '<br>';
			$this->render_status_badge( __( 'Multiple', 'one-minute-media-video-showcase' ), 'warning' );
		}

	}

	/**
	 * Render the Vimeo URL status column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_vimeo_url_status_column( $post_id ) {

		$vimeo_url = trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );

		if ( '' === $vimeo_url ) {
			$this->render_status_badge( __( 'Missing', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		if ( ! OMMVS_Fields::is_valid_vimeo_url( $vimeo_url ) ) {
			$this->render_status_badge( __( 'Invalid', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		$vimeo_id = OMMVS_Fields::get_vimeo_video_id_from_url( $vimeo_url );

		$this->render_status_badge( __( 'Valid', 'one-minute-media-video-showcase' ), 'success' );

		if ( '' !== $vimeo_id ) {
			printf(
				'<br><code class="ommvs-admin-vimeo-id">%s</code>',
				esc_html( $vimeo_id )
			);
		}

	}

	/**
	 * Render the Modal Content status column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_modal_content_status_column( $post_id ) {

		$modal_content = get_post_meta( $post_id, OMMVS_Fields::FIELD_MODAL_CONTENT, true );

		if ( '' === trim( wp_strip_all_tags( (string) $modal_content ) ) ) {
			$this->render_status_badge( __( 'Missing', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		$this->render_status_badge( __( 'Ready', 'one-minute-media-video-showcase' ), 'success' );

	}

	/**
	 * Render the Active list-table filter.
	 *
	 * @since    1.0.0
	 */
	private function render_active_filter() {

		$selected = $this->get_current_filter_value( self::FILTER_ACTIVE );

		?>
		<label class="screen-reader-text" for="ommvs-active-filter"><?php esc_html_e( 'Filter by active status', 'one-minute-media-video-showcase' ); ?></label>
		<select id="ommvs-active-filter" name="<?php echo esc_attr( self::FILTER_ACTIVE ); ?>" class="ommvs-admin-list-filter">
			<option value=""><?php esc_html_e( 'All active statuses', 'one-minute-media-video-showcase' ); ?></option>
			<option value="active" <?php selected( $selected, 'active' ); ?>><?php esc_html_e( 'Active', 'one-minute-media-video-showcase' ); ?></option>
			<option value="inactive" <?php selected( $selected, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'one-minute-media-video-showcase' ); ?></option>
		</select>
		<?php

	}

	/**
	 * Render the Video Category list-table filter.
	 *
	 * @since    1.0.0
	 */
	private function render_category_filter() {

		$selected = $this->get_current_filter_value( self::FILTER_CATEGORY );
		$terms    = get_terms(
			array(
				'taxonomy'   => $this->get_video_category_taxonomy(),
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		?>
		<label class="screen-reader-text" for="ommvs-video-category-filter"><?php esc_html_e( 'Filter by Video Category', 'one-minute-media-video-showcase' ); ?></label>
		<select id="ommvs-video-category-filter" name="<?php echo esc_attr( self::FILTER_CATEGORY ); ?>" class="ommvs-admin-list-filter">
			<option value=""><?php esc_html_e( 'All video categories', 'one-minute-media-video-showcase' ); ?></option>
			<option value="none" <?php selected( $selected, 'none' ); ?>><?php esc_html_e( 'No category', 'one-minute-media-video-showcase' ); ?></option>
			<?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
				<?php foreach ( $terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected, (string) $term->term_id ); ?>>
						<?php echo esc_html( $term->name ); ?>
					</option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<?php

	}

	/**
	 * Render the required-field list-table filter.
	 *
	 * @since    1.0.0
	 */
	private function render_required_filter() {

		$selected = $this->get_current_filter_value( self::FILTER_REQUIRED );

		?>
		<label class="screen-reader-text" for="ommvs-required-filter"><?php esc_html_e( 'Filter by required field status', 'one-minute-media-video-showcase' ); ?></label>
		<select id="ommvs-required-filter" name="<?php echo esc_attr( self::FILTER_REQUIRED ); ?>" class="ommvs-admin-list-filter">
			<option value=""><?php esc_html_e( 'All required fields', 'one-minute-media-video-showcase' ); ?></option>
			<option value="missing" <?php selected( $selected, 'missing' ); ?>><?php esc_html_e( 'Missing required fields', 'one-minute-media-video-showcase' ); ?></option>
		</select>
		<?php

	}

	/**
	 * Render the Vimeo URL list-table filter.
	 *
	 * @since    1.0.0
	 */
	private function render_vimeo_filter() {

		$selected = $this->get_current_filter_value( self::FILTER_VIMEO_STATUS );

		?>
		<label class="screen-reader-text" for="ommvs-vimeo-filter"><?php esc_html_e( 'Filter by Vimeo URL status', 'one-minute-media-video-showcase' ); ?></label>
		<select id="ommvs-vimeo-filter" name="<?php echo esc_attr( self::FILTER_VIMEO_STATUS ); ?>" class="ommvs-admin-list-filter">
			<option value=""><?php esc_html_e( 'All Vimeo URL statuses', 'one-minute-media-video-showcase' ); ?></option>
			<option value="valid" <?php selected( $selected, 'valid' ); ?>><?php esc_html_e( 'Vimeo valid', 'one-minute-media-video-showcase' ); ?></option>
			<option value="missing" <?php selected( $selected, 'missing' ); ?>><?php esc_html_e( 'Vimeo missing', 'one-minute-media-video-showcase' ); ?></option>
			<option value="invalid" <?php selected( $selected, 'invalid' ); ?>><?php esc_html_e( 'Vimeo invalid', 'one-minute-media-video-showcase' ); ?></option>
		</select>
		<?php

	}

	/**
	 * Get filtered post IDs for list-table filters.
	 *
	 * @since    1.0.0
	 * @return   array|null
	 */
	private function get_filtered_admin_post_ids() {

		$all_ids        = $this->get_all_admin_video_ids();
		$filtered_ids   = null;
		$filter_results = array();

		$active_filter = $this->get_current_filter_value( self::FILTER_ACTIVE );

		if ( in_array( $active_filter, array( 'active', 'inactive' ), true ) ) {
			$filter_results[] = $this->get_active_filter_post_ids( $active_filter, $all_ids );
		}

		$category_filter = $this->get_current_filter_value( self::FILTER_CATEGORY );

		if ( 'none' === $category_filter || absint( $category_filter ) ) {
			$filter_results[] = $this->get_category_filter_post_ids( $category_filter, $all_ids );
		}

		$required_filter = $this->get_current_filter_value( self::FILTER_REQUIRED );

		if ( 'missing' === $required_filter ) {
			$filter_results[] = $this->get_required_filter_post_ids( $all_ids );
		}

		$vimeo_filter = $this->get_current_filter_value( self::FILTER_VIMEO_STATUS );

		if ( in_array( $vimeo_filter, array( 'valid', 'missing', 'invalid' ), true ) ) {
			$filter_results[] = $this->get_vimeo_filter_post_ids( $vimeo_filter, $all_ids );
		}

		if ( empty( $filter_results ) ) {
			return null;
		}

		foreach ( $filter_results as $result_ids ) {
			$filtered_ids = null === $filtered_ids
				? $result_ids
				: array_values( array_intersect( $filtered_ids, $result_ids ) );
		}

		return array_values( array_unique( array_map( 'absint', (array) $filtered_ids ) ) );

	}

	/**
	 * Get all Video Case Study IDs that list filters may inspect.
	 *
	 * @since    1.0.0
	 * @return   int[]
	 */
	private function get_all_admin_video_ids() {

		$ids = get_posts(
			array(
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_type'      => self::POST_TYPE,
				'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash' ),
				'posts_per_page' => -1,
			)
		);

		return array_values( array_map( 'absint', $ids ) );

	}

	/**
	 * Get post IDs matching the active-status filter.
	 *
	 * @since    1.0.0
	 * @param    string    $filter_value    Active filter value.
	 * @param    int[]     $post_ids        Candidate post IDs.
	 * @return   int[]
	 */
	private function get_active_filter_post_ids( $filter_value, $post_ids ) {

		$matched_ids = array();

		foreach ( $post_ids as $post_id ) {
			$active    = get_post_meta( $post_id, OMMVS_Fields::FIELD_IS_ACTIVE, true );
			$is_active = '' === $active || '0' !== (string) $active;

			if ( ( 'active' === $filter_value && $is_active ) || ( 'inactive' === $filter_value && ! $is_active ) ) {
				$matched_ids[] = $post_id;
			}
		}

		return $matched_ids;

	}

	/**
	 * Get post IDs matching the Video Category filter.
	 *
	 * @since    1.0.0
	 * @param    string    $filter_value    Category filter value.
	 * @param    int[]     $post_ids        Candidate post IDs.
	 * @return   int[]
	 */
	private function get_category_filter_post_ids( $filter_value, $post_ids ) {

		$matched_ids = array();
		$term_id     = absint( $filter_value );

		foreach ( $post_ids as $post_id ) {
			$terms = $this->get_video_category_terms( $post_id );

			if ( 'none' === $filter_value ) {
				if ( empty( $terms ) ) {
					$matched_ids[] = $post_id;
				}

				continue;
			}

			if ( ! $term_id ) {
				continue;
			}

			foreach ( $terms as $term ) {
				if ( $term_id === absint( $term->term_id ) ) {
					$matched_ids[] = $post_id;
					break;
				}
			}
		}

		return $matched_ids;

	}

	/**
	 * Get post IDs with missing required fields.
	 *
	 * @since    1.0.0
	 * @param    int[]    $post_ids    Candidate post IDs.
	 * @return   int[]
	 */
	private function get_required_filter_post_ids( $post_ids ) {

		$matched_ids = array();

		foreach ( $post_ids as $post_id ) {
			if ( $this->has_missing_required_fields( $post_id ) ) {
				$matched_ids[] = $post_id;
			}
		}

		return $matched_ids;

	}

	/**
	 * Get post IDs matching the Vimeo status filter.
	 *
	 * @since    1.0.0
	 * @param    string    $filter_value    Vimeo filter value.
	 * @param    int[]     $post_ids        Candidate post IDs.
	 * @return   int[]
	 */
	private function get_vimeo_filter_post_ids( $filter_value, $post_ids ) {

		$matched_ids = array();

		foreach ( $post_ids as $post_id ) {
			$vimeo_url = trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );
			$is_valid  = OMMVS_Fields::is_valid_vimeo_url( $vimeo_url );

			if ( 'missing' === $filter_value && '' === $vimeo_url ) {
				$matched_ids[] = $post_id;
				continue;
			}

			if ( 'invalid' === $filter_value && '' !== $vimeo_url && ! $is_valid ) {
				$matched_ids[] = $post_id;
				continue;
			}

			if ( 'valid' === $filter_value && $is_valid ) {
				$matched_ids[] = $post_id;
			}
		}

		return $matched_ids;

	}

	/**
	 * Determine whether a Video Case Study has missing required fields.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   bool
	 */
	private function has_missing_required_fields( $post_id ) {

		$thumbnail_id  = absint( get_post_meta( $post_id, OMMVS_Fields::FIELD_CARD_THUMBNAIL, true ) );
		$modal_content = get_post_meta( $post_id, OMMVS_Fields::FIELD_MODAL_CONTENT, true );
		$vimeo_url     = trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );

		return '' === $this->get_normalized_hash_slug( $post_id )
			|| '' === trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_CARD_TITLE, true ) )
			|| '' === trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_CARD_DESCRIPTION, true ) )
			|| ! $this->is_valid_attachment( $thumbnail_id )
			|| empty( $this->get_video_category_terms( $post_id ) )
			|| '' === trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_MODAL_TITLE, true ) )
			|| '' === trim( wp_strip_all_tags( (string) $modal_content ) )
			|| ! OMMVS_Fields::is_valid_vimeo_url( $vimeo_url );

	}

	/**
	 * Get a sanitized filter value from the current request.
	 *
	 * @since    1.0.0
	 * @param    string    $filter_key    Filter query string key.
	 * @return   string
	 */
	private function get_current_filter_value( $filter_key ) {

		return isset( $_GET[ $filter_key ] )
			? sanitize_key( wp_unslash( $_GET[ $filter_key ] ) )
			: '';

	}

	/**
	 * Get Video Category terms assigned to a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 * @return   WP_Term[]
	 */
	private function get_video_category_terms( $post_id ) {

		$terms = wp_get_object_terms(
			absint( $post_id ),
			$this->get_video_category_taxonomy(),
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
	 * Get a normalized hash slug for admin display and copy actions.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 * @return   string
	 */
	private function get_normalized_hash_slug( $post_id ) {

		$hash_slug = trim( sanitize_text_field( (string) get_post_meta( absint( $post_id ), OMMVS_Fields::FIELD_HASH_SLUG, true ) ) );

		return ltrim( $hash_slug, '#' );

	}

	/**
	 * Determine whether an attachment ID points to an attachment post.
	 *
	 * @since    1.0.0
	 * @param    int    $attachment_id    Attachment post ID.
	 * @return   bool
	 */
	private function is_valid_attachment( $attachment_id ) {

		return $attachment_id && 'attachment' === get_post_type( absint( $attachment_id ) );

	}

	/**
	 * Get the Video Category taxonomy slug.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_video_category_taxonomy() {

		return class_exists( 'OMMVS_Taxonomy_Video_Category' )
			? OMMVS_Taxonomy_Video_Category::TAXONOMY
			: 'ommvs_video_category';

	}

	/**
	 * Render a compact admin status badge.
	 *
	 * @since    1.0.0
	 * @param    string    $label    Badge label.
	 * @param    string    $type     Badge type.
	 */
	private function render_status_badge( $label, $type ) {

		printf(
			'<span class="ommvs-admin-status ommvs-admin-status--%1$s">%2$s</span>',
			esc_attr( sanitize_html_class( $type ) ),
			esc_html( $label )
		);

	}

	/**
	 * Render an empty admin column value.
	 *
	 * @since    1.0.0
	 */
	private function render_empty_column_value() {

		echo '<span class="ommvs-admin-muted">&mdash;</span>';

	}

}

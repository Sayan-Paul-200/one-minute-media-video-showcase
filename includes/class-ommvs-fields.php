<?php

/**
 * Field system foundation for the plugin.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Field system foundation for Video Case Study data, page placements, and settings.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Fields {

	const FIELD_HASH_SLUG         = 'ommvs_hash_slug';
	const FIELD_IS_ACTIVE         = 'ommvs_is_active';
	const FIELD_CARD_TITLE        = 'ommvs_card_title';
	const FIELD_CARD_DESCRIPTION  = 'ommvs_card_description';
	const FIELD_CARD_THUMBNAIL    = 'ommvs_card_thumbnail';
	const FIELD_MODAL_TITLE       = 'ommvs_modal_title';
	const FIELD_MODAL_OVERVIEW    = 'ommvs_modal_overview';
	const FIELD_VIDEO_PROVIDER    = 'ommvs_video_provider';
	const FIELD_VIDEO_ID          = 'ommvs_video_id';
	const FIELD_VIDEO_URL         = 'ommvs_video_url';
	const FIELD_RELATED_THUMBNAIL = 'ommvs_related_thumbnail';
	const FIELD_ADMIN_NOTES       = 'ommvs_admin_notes';

	const META_FEATURED_VIDEOS = 'ommvs_featured_videos';
	const META_MORE_VIDEOS     = 'ommvs_more_videos';

	const PLACEMENT_VIDEO = 'video';

	const FEATURED_VIDEOS_MAX = 6;

	const PAGE_PLACEMENTS_NONCE_ACTION = 'ommvs_save_page_placements';
	const PAGE_PLACEMENTS_NONCE_NAME   = 'ommvs_page_placements_nonce';

	const PAGE_PLACEMENT_NOTICE_TRANSIENT_PREFIX = 'ommvs_page_validation_';

	const OPTION_SETTINGS                        = 'ommvs_settings';
	const OPTION_PRODUCTION_OVERVIEW_LABEL       = 'production_overview_label';
	const OPTION_CREATIVE_SECTION_TITLE          = 'creative_section_title';
	const OPTION_CREATIVE_BULLETS                = 'creative_bullets';
	const OPTION_CTA_BUTTON_TEXT                 = 'cta_button_text';
	const OPTION_CTA_BUTTON_URL                  = 'cta_button_url';
	const OPTION_MODAL_FALLBACK_THUMBNAIL        = 'modal_fallback_thumbnail';

	/**
	 * Determine whether ACF Free/Pro APIs are available.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function is_acf_available() {

		return function_exists( 'acf_add_local_field_group' );

	}

	/**
	 * Register local field groups when ACF is available.
	 *
	 * @since    1.0.0
	 */
	public function register_field_groups() {

		if ( ! self::is_acf_available() ) {
			return;
		}

		$this->register_video_cpt_field_group();

	}

	/**
	 * Validate that a Video Case Study hash slug is unique and hash-ready.
	 *
	 * @since    1.0.0
	 * @param    bool|string    $valid    Existing ACF validation state.
	 * @param    mixed          $value    Submitted field value.
	 * @param    array          $field    ACF field settings.
	 * @param    string         $input    ACF input name.
	 * @return   bool|string
	 */
	public function validate_hash_slug_unique( $valid, $value, $field, $input ) {

		unset( $field, $input );

		if ( true !== $valid ) {
			return $valid;
		}

		$hash_slug = is_scalar( $value ) ? trim( sanitize_text_field( (string) $value ) ) : '';

		if ( '' === $hash_slug ) {
			return $valid;
		}

		if ( '#' === substr( $hash_slug, 0, 1 ) ) {
			return __( 'Store the hash slug without the leading # character.', 'one-minute-media-video-showcase' );
		}

		$current_post_id = $this->get_current_admin_post_id();
		$duplicate_ids   = get_posts(
			array(
				'post_type'        => 'video_case_study',
				'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'fields'           => 'ids',
				'numberposts'      => 1,
				'post__not_in'     => $current_post_id ? array( $current_post_id ) : array(),
				'meta_key'         => self::FIELD_HASH_SLUG,
				'meta_value'       => $hash_slug,
				'suppress_filters' => false,
			)
		);

		if ( ! empty( $duplicate_ids ) ) {
			return sprintf(
				/* translators: %s: duplicate hash slug. */
				__( 'The hash slug "%s" is already used by another Video Case Study.', 'one-minute-media-video-showcase' ),
				$hash_slug
			);
		}

		return $valid;

	}

	/**
	 * Register ACF Free fields for the Video Case Study custom post type.
	 *
	 * @since    1.0.0
	 */
	private function register_video_cpt_field_group() {

		acf_add_local_field_group(
			array(
				'key'                   => 'group_ommvs_video_showcase_details',
				'title'                 => __( 'Video Showcase Details', 'one-minute-media-video-showcase' ),
				'fields'                => array(
					array(
						'key'           => 'field_ommvs_hash_slug',
						'label'         => __( 'Hash Slug', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_HASH_SLUG,
						'type'          => 'text',
						'instructions'  => __( 'Legacy URL hash without the leading #, for example nick-kyrgios.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'placeholder'   => 'nick-kyrgios',
					),
					array(
						'key'           => 'field_ommvs_is_active',
						'label'         => __( 'Active', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_IS_ACTIVE,
						'type'          => 'true_false',
						'instructions'  => __( 'Inactive videos should not be used in page placements.', 'one-minute-media-video-showcase' ),
						'required'      => 0,
						'default_value' => 1,
						'ui'            => 1,
						'ui_on_text'    => __( 'Active', 'one-minute-media-video-showcase' ),
						'ui_off_text'   => __( 'Inactive', 'one-minute-media-video-showcase' ),
					),
					array(
						'key'           => 'field_ommvs_card_title',
						'label'         => __( 'Default Card Title', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_CARD_TITLE,
						'type'          => 'text',
						'instructions'  => __( 'Title used on video cards.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
					),
					array(
						'key'           => 'field_ommvs_card_description',
						'label'         => __( 'Default Card Description', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_CARD_DESCRIPTION,
						'type'          => 'textarea',
						'instructions'  => __( 'Short description used on video cards.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'rows'          => 3,
						'new_lines'     => '',
					),
					array(
						'key'            => 'field_ommvs_card_thumbnail',
						'label'          => __( 'Default Card Thumbnail', 'one-minute-media-video-showcase' ),
						'name'           => self::FIELD_CARD_THUMBNAIL,
						'type'           => 'image',
						'instructions'   => __( 'Default card thumbnail. Stored as an attachment ID for consistent rendering.', 'one-minute-media-video-showcase' ),
						'required'       => 1,
						'return_format'  => 'id',
						'preview_size'   => 'medium',
						'library'        => 'all',
					),
					array(
						'key'           => 'field_ommvs_modal_title',
						'label'         => __( 'Modal Title', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_MODAL_TITLE,
						'type'          => 'textarea',
						'instructions'  => __( 'Title displayed in the modal. This can differ from the card title.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'rows'          => 2,
						'new_lines'     => 'br',
					),
					array(
						'key'           => 'field_ommvs_modal_overview',
						'label'         => __( 'Production Overview', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_MODAL_OVERVIEW,
						'type'          => 'wysiwyg',
						'instructions'  => __( 'Overview text displayed inside the modal.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'tabs'          => 'visual',
						'toolbar'       => 'basic',
						'media_upload'  => 0,
						'delay'         => 0,
					),
					array(
						'key'           => 'field_ommvs_video_provider',
						'label'         => __( 'Video Provider', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_VIDEO_PROVIDER,
						'type'          => 'select',
						'instructions'  => __( 'Provider used to build the modal video embed.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'choices'       => array(
							'vimeo'   => __( 'Vimeo', 'one-minute-media-video-showcase' ),
							'youtube' => __( 'YouTube', 'one-minute-media-video-showcase' ),
							'url'     => __( 'Direct URL', 'one-minute-media-video-showcase' ),
						),
						'default_value' => 'vimeo',
						'allow_null'    => 0,
						'multiple'      => 0,
						'ui'            => 0,
						'return_format' => 'value',
					),
					array(
						'key'               => 'field_ommvs_video_id',
						'label'             => __( 'Video ID', 'one-minute-media-video-showcase' ),
						'name'              => self::FIELD_VIDEO_ID,
						'type'              => 'text',
						'instructions'      => __( 'Vimeo or YouTube video ID. Required when the provider is Vimeo or YouTube.', 'one-minute-media-video-showcase' ),
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_ommvs_video_provider',
									'operator' => '==',
									'value'    => 'vimeo',
								),
							),
							array(
								array(
									'field'    => 'field_ommvs_video_provider',
									'operator' => '==',
									'value'    => 'youtube',
								),
							),
						),
					),
					array(
						'key'               => 'field_ommvs_video_url',
						'label'             => __( 'Video URL', 'one-minute-media-video-showcase' ),
						'name'              => self::FIELD_VIDEO_URL,
						'type'              => 'url',
						'instructions'      => __( 'Direct video URL. Required when the provider is Direct URL.', 'one-minute-media-video-showcase' ),
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_ommvs_video_provider',
									'operator' => '==',
									'value'    => 'url',
								),
							),
						),
					),
					array(
						'key'            => 'field_ommvs_related_thumbnail',
						'label'          => __( 'Related Thumbnail Override', 'one-minute-media-video-showcase' ),
						'name'           => self::FIELD_RELATED_THUMBNAIL,
						'type'           => 'image',
						'instructions'   => __( 'Optional thumbnail for related cards. Leave empty to use the default card thumbnail.', 'one-minute-media-video-showcase' ),
						'required'       => 0,
						'return_format'  => 'id',
						'preview_size'   => 'medium',
						'library'        => 'all',
					),
					array(
						'key'           => 'field_ommvs_admin_notes',
						'label'         => __( 'Admin Notes', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_ADMIN_NOTES,
						'type'          => 'textarea',
						'instructions'  => __( 'Internal migration or editorial notes. Not rendered on the frontend.', 'one-minute-media-video-showcase' ),
						'required'      => 0,
						'rows'          => 4,
						'new_lines'     => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'video_case_study',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => __( 'Reusable video data used by 1 Minute Media video grids and modals.', 'one-minute-media-video-showcase' ),
				'show_in_rest'          => 0,
			)
		);

	}

	/**
	 * Register page-level placement metaboxes.
	 *
	 * @since    1.0.0
	 * @param    WP_Post|null    $post    The current page post object.
	 */
	public function register_page_placement_metaboxes( $post = null ) {

		unset( $post );

		add_meta_box(
			'ommvs-page-video-showcase',
			__( '1MM Video Showcase', 'one-minute-media-video-showcase' ),
			array( $this, 'render_page_placements_metabox' ),
			'page',
			'normal',
			'default'
		);

	}

	/**
	 * Render the page-level placement metabox.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    The current page post object.
	 */
	public function render_page_placements_metabox( $post ) {

		wp_nonce_field( self::PAGE_PLACEMENTS_NONCE_ACTION, self::PAGE_PLACEMENTS_NONCE_NAME );

		$video_options       = $this->get_video_options();
		$featured_placements = $this->get_page_placements( $post->ID, self::META_FEATURED_VIDEOS, self::FEATURED_VIDEOS_MAX );
		$more_placements     = $this->get_page_placements( $post->ID, self::META_MORE_VIDEOS );

		?>
		<div class="ommvs-page-placements">
			<?php
			$this->render_placement_section(
				self::META_FEATURED_VIDEOS,
				__( 'Featured Videos', 'one-minute-media-video-showcase' ),
				__( 'Ordered source list for page-specific related videos. Maximum 6 videos.', 'one-minute-media-video-showcase' ),
				$featured_placements,
				$video_options,
				self::FEATURED_VIDEOS_MAX
			);

			$this->render_placement_section(
				self::META_MORE_VIDEOS,
				__( 'More Videos', 'one-minute-media-video-showcase' ),
				__( 'Additional videos for this page. Related videos still come from Featured Videos.', 'one-minute-media-video-showcase' ),
				$more_placements,
				$video_options
			);
			?>
		</div>
		<?php

	}

	/**
	 * Save page-level placement metabox data.
	 *
	 * @since    1.0.0
	 * @param    int        $post_id    The current post ID.
	 * @param    WP_Post    $post       The current post object.
	 * @param    bool       $update     Whether this is an existing post being updated.
	 */
	public function save_page_placements( $post_id, $post, $update ) {

		unset( $update );

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if (
			! isset( $_POST[ self::PAGE_PLACEMENTS_NONCE_NAME ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::PAGE_PLACEMENTS_NONCE_NAME ] ) ), self::PAGE_PLACEMENTS_NONCE_ACTION )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw_featured = isset( $_POST[ self::META_FEATURED_VIDEOS ] ) && is_array( $_POST[ self::META_FEATURED_VIDEOS ] )
			? wp_unslash( $_POST[ self::META_FEATURED_VIDEOS ] )
			: array();
		$raw_more     = isset( $_POST[ self::META_MORE_VIDEOS ] ) && is_array( $_POST[ self::META_MORE_VIDEOS ] )
			? wp_unslash( $_POST[ self::META_MORE_VIDEOS ] )
			: array();
		$notices      = array();

		if ( $this->count_submitted_placement_rows( $raw_featured ) > self::FEATURED_VIDEOS_MAX ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: %d: maximum featured videos count. */
					__( 'Featured Videos are limited to %d rows. Extra submitted rows were ignored.', 'one-minute-media-video-showcase' ),
					(int) self::FEATURED_VIDEOS_MAX
				),
			);
		}

		$featured_placements = $this->sanitize_placement_rows( $raw_featured, self::FEATURED_VIDEOS_MAX );
		$more_placements     = $this->sanitize_placement_rows( $raw_more );
		$validated           = $this->validate_page_placements( $featured_placements, $more_placements, $notices );

		$this->update_placement_meta(
			$post_id,
			self::META_FEATURED_VIDEOS,
			$validated['featured']
		);

		$this->update_placement_meta(
			$post_id,
			self::META_MORE_VIDEOS,
			$validated['more']
		);

		$this->store_page_placement_notices( $post_id, $validated['notices'] );

	}

	/**
	 * Show page placement validation notices after a page save redirect.
	 *
	 * @since    1.0.0
	 */
	public function maybe_show_page_placement_notices() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'page' !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}

		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$transient_key = $this->get_page_placement_notice_transient_key( $post_id );
		$notices       = get_transient( $transient_key );

		if ( empty( $notices ) || ! is_array( $notices ) ) {
			return;
		}

		delete_transient( $transient_key );

		foreach ( $notices as $notice ) {
			$type    = isset( $notice['type'] ) && 'error' === $notice['type'] ? 'error' : 'warning';
			$message = isset( $notice['message'] ) ? (string) $notice['message'] : '';

			if ( '' === $message ) {
				continue;
			}

			?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
				<p><?php echo esc_html( $message ); ?></p>
			</div>
			<?php
		}

	}

	/**
	 * Validate page placement rows after sanitization.
	 *
	 * @since    1.0.0
	 * @param    array    $featured_placements    Sanitized Featured Videos rows.
	 * @param    array    $more_placements        Sanitized More Videos rows.
	 * @param    array    $notices                Existing notices.
	 * @return   array
	 */
	private function validate_page_placements( $featured_placements, $more_placements, $notices ) {

		$seen_video_ids       = array();
		$removed_duplicates   = array();
		$removed_inactive     = array();
		$missing_field_issues = array();

		$featured_placements = $this->validate_placement_group(
			$featured_placements,
			$seen_video_ids,
			$removed_duplicates,
			$removed_inactive,
			$missing_field_issues
		);

		$more_placements = $this->validate_placement_group(
			$more_placements,
			$seen_video_ids,
			$removed_duplicates,
			$removed_inactive,
			$missing_field_issues
		);

		if ( ! empty( $removed_duplicates ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: %s: comma-separated video labels. */
					__( 'Duplicate video placements were removed: %s.', 'one-minute-media-video-showcase' ),
					implode( ', ', array_unique( $removed_duplicates ) )
				),
			);
		}

		if ( ! empty( $removed_inactive ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: %s: comma-separated video labels. */
					__( 'Inactive videos were removed from this page: %s.', 'one-minute-media-video-showcase' ),
					implode( ', ', array_unique( $removed_inactive ) )
				),
			);
		}

		if ( ! empty( $missing_field_issues ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: %s: semicolon-separated video field issue summaries. */
					__( 'Some selected videos are missing required data and should be completed before frontend use: %s.', 'one-minute-media-video-showcase' ),
					implode( '; ', array_unique( $missing_field_issues ) )
				),
			);
		}

		if ( ( ! empty( $featured_placements ) || ! empty( $more_placements ) ) && count( $featured_placements ) < self::FEATURED_VIDEOS_MAX ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: 1: current featured videos count, 2: expected featured videos count. */
					__( 'This page has %1$d Featured Videos. The design expects %2$d where possible.', 'one-minute-media-video-showcase' ),
					count( $featured_placements ),
					(int) self::FEATURED_VIDEOS_MAX
				),
			);
		}

		return array(
			'featured' => array_values( $featured_placements ),
			'more'     => array_values( $more_placements ),
			'notices'  => $notices,
		);

	}

	/**
	 * Validate a single placement group.
	 *
	 * @since    1.0.0
	 * @param    array    $placements             Placement rows.
	 * @param    array    $seen_video_ids         Video IDs already accepted.
	 * @param    array    $removed_duplicates     Removed duplicate labels.
	 * @param    array    $removed_inactive       Removed inactive labels.
	 * @param    array    $missing_field_issues   Missing field warning labels.
	 * @return   array
	 */
	private function validate_placement_group( $placements, &$seen_video_ids, &$removed_duplicates, &$removed_inactive, &$missing_field_issues ) {

		$valid_placements = array();

		foreach ( $placements as $placement ) {
			$video_id = isset( $placement[ self::PLACEMENT_VIDEO ] ) ? absint( $placement[ self::PLACEMENT_VIDEO ] ) : 0;

			if ( ! $video_id ) {
				continue;
			}

			if ( isset( $seen_video_ids[ $video_id ] ) ) {
				$removed_duplicates[] = $this->get_video_admin_label( $video_id );
				continue;
			}

			$seen_video_ids[ $video_id ] = true;

			if ( ! $this->is_video_active( $video_id ) ) {
				$removed_inactive[] = $this->get_video_admin_label( $video_id );
				continue;
			}

			$field_issues = $this->get_video_required_field_issues( $video_id );

			if ( ! empty( $field_issues ) ) {
				$missing_field_issues[] = sprintf(
					'%1$s (%2$s)',
					$this->get_video_admin_label( $video_id ),
					implode( ', ', $field_issues )
				);
			}

			$valid_placements[] = $placement;
		}

		return $valid_placements;

	}

	/**
	 * Count submitted placement rows that include a selected video ID.
	 *
	 * @since    1.0.0
	 * @param    array    $rows    Raw submitted rows.
	 * @return   int
	 */
	private function count_submitted_placement_rows( $rows ) {

		if ( ! is_array( $rows ) ) {
			return 0;
		}

		$count = 0;

		foreach ( $rows as $row ) {
			if ( is_array( $row ) && ! empty( $row[ self::PLACEMENT_VIDEO ] ) ) {
				$count++;
			}
		}

		return $count;

	}

	/**
	 * Store page placement notices for display after redirect.
	 *
	 * @since    1.0.0
	 * @param    int      $post_id    Page post ID.
	 * @param    array    $notices    Notices to store.
	 */
	private function store_page_placement_notices( $post_id, $notices ) {

		$transient_key = $this->get_page_placement_notice_transient_key( $post_id );

		if ( empty( $notices ) ) {
			delete_transient( $transient_key );
			return;
		}

		set_transient( $transient_key, $notices, MINUTE_IN_SECONDS );

	}

	/**
	 * Build a user-scoped transient key for page placement notices.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Page post ID.
	 * @return   string
	 */
	private function get_page_placement_notice_transient_key( $post_id ) {

		return self::PAGE_PLACEMENT_NOTICE_TRANSIENT_PREFIX . get_current_user_id() . '_' . absint( $post_id );

	}

	/**
	 * Get saved placement rows for rendering.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id    Page post ID.
	 * @param    string    $meta_key   Placement meta key.
	 * @param    int       $limit      Optional max row count.
	 * @return   array
	 */
	private function get_page_placements( $post_id, $meta_key, $limit = 0 ) {

		$placements = get_post_meta( $post_id, $meta_key, true );

		if ( ! is_array( $placements ) ) {
			return array();
		}

		return $this->sanitize_placement_rows( $placements, $limit );

	}

	/**
	 * Retrieve Video Case Study options for placement selectors.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_video_options() {

		$video_posts = get_posts(
			array(
				'post_type'        => 'video_case_study',
				'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'numberposts'      => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

		$options = array();

		foreach ( $video_posts as $video_post ) {
			$title = get_the_title( $video_post );

			if ( '' === trim( $title ) ) {
				$title = sprintf(
					/* translators: %d: video post ID. */
					__( '(no title) #%d', 'one-minute-media-video-showcase' ),
					$video_post->ID
				);
			}

			$options[] = array(
				'id'    => (int) $video_post->ID,
				'title' => $title,
			);
		}

		return $options;

	}

	/**
	 * Render one placement section.
	 *
	 * @since    1.0.0
	 * @param    string    $meta_key      Placement meta key.
	 * @param    string    $title         Section title.
	 * @param    string    $description   Section description.
	 * @param    array     $placements    Existing placement rows.
	 * @param    array     $video_options Video selector options.
	 * @param    int       $max_rows      Optional max row count.
	 */
	private function render_placement_section( $meta_key, $title, $description, $placements, $video_options, $max_rows = 0 ) {

		?>
		<section class="ommvs-placement-section" data-ommvs-placement-section data-meta-key="<?php echo esc_attr( $meta_key ); ?>" data-max="<?php echo esc_attr( $max_rows ); ?>">
			<div class="ommvs-placement-section__header">
				<div>
					<h3><?php echo esc_html( $title ); ?></h3>
					<p><?php echo esc_html( $description ); ?></p>
				</div>
				<button type="button" class="button button-secondary ommvs-placement-section__add" data-ommvs-add-row>
					<?php esc_html_e( 'Add Video', 'one-minute-media-video-showcase' ); ?>
				</button>
			</div>

			<div class="ommvs-placement-section__limit" data-ommvs-limit-message hidden>
				<?php
				printf(
					/* translators: %d: maximum featured videos count. */
					esc_html__( 'Featured Videos are limited to %d rows.', 'one-minute-media-video-showcase' ),
					(int) self::FEATURED_VIDEOS_MAX
				);
				?>
			</div>

			<div class="ommvs-placement-rows" data-ommvs-rows>
				<?php
				foreach ( $placements as $index => $placement ) {
					$this->render_placement_row( $meta_key, (string) $index, $placement, $video_options );
				}
				?>
			</div>

			<p class="ommvs-placement-section__empty" data-ommvs-empty-message>
				<?php esc_html_e( 'No videos selected yet.', 'one-minute-media-video-showcase' ); ?>
			</p>

			<script type="text/html" data-ommvs-row-template>
				<?php $this->render_placement_row( $meta_key, '__index__', array(), $video_options ); ?>
			</script>
		</section>
		<?php

	}

	/**
	 * Render one placement row.
	 *
	 * @since    1.0.0
	 * @param    string    $meta_key      Placement meta key.
	 * @param    string    $index         Row index or template token.
	 * @param    array     $placement     Placement row data.
	 * @param    array     $video_options Video selector options.
	 */
	private function render_placement_row( $meta_key, $index, $placement, $video_options ) {

		$video_id   = isset( $placement[ self::PLACEMENT_VIDEO ] ) ? absint( $placement[ self::PLACEMENT_VIDEO ] ) : 0;
		$field_base = $meta_key . '[' . $index . ']';

		?>
		<div class="ommvs-placement-row" data-ommvs-row data-index="<?php echo esc_attr( $index ); ?>">
			<button type="button" class="ommvs-placement-row__handle" data-ommvs-row-handle aria-label="<?php esc_attr_e( 'Drag to reorder', 'one-minute-media-video-showcase' ); ?>">
				<span class="dashicons dashicons-menu" aria-hidden="true"></span>
			</button>

			<div class="ommvs-placement-row__fields">
				<label class="ommvs-placement-field ommvs-placement-field--video">
					<span><?php esc_html_e( 'Video Case Study', 'one-minute-media-video-showcase' ); ?></span>
					<select name="<?php echo esc_attr( $field_base . '[' . self::PLACEMENT_VIDEO . ']' ); ?>" data-ommvs-video-select>
						<option value=""><?php esc_html_e( 'Select a video', 'one-minute-media-video-showcase' ); ?></option>
						<?php if ( empty( $video_options ) ) : ?>
							<option value="" disabled><?php esc_html_e( 'No Video Case Studies found', 'one-minute-media-video-showcase' ); ?></option>
						<?php endif; ?>
						<?php foreach ( $video_options as $video_option ) : ?>
							<option value="<?php echo esc_attr( $video_option['id'] ); ?>" <?php selected( $video_id, $video_option['id'] ); ?>>
								<?php echo esc_html( $video_option['title'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			</div>

			<button type="button" class="button-link-delete ommvs-placement-row__remove" data-ommvs-remove-row>
				<?php esc_html_e( 'Remove Row', 'one-minute-media-video-showcase' ); ?>
			</button>
		</div>
		<?php

	}

	/**
	 * Sanitize submitted placement rows.
	 *
	 * @since    1.0.0
	 * @param    array    $rows     Raw rows.
	 * @param    int      $limit    Optional max row count.
	 * @return   array
	 */
	private function sanitize_placement_rows( $rows, $limit = 0 ) {

		if ( ! is_array( $rows ) ) {
			return array();
		}

		$sanitized_rows = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$video_id = isset( $row[ self::PLACEMENT_VIDEO ] ) ? absint( $row[ self::PLACEMENT_VIDEO ] ) : 0;

			if ( ! $this->is_valid_video_case_study( $video_id ) ) {
				continue;
			}

			$sanitized_rows[] = array(
				self::PLACEMENT_VIDEO => $video_id,
			);

			if ( $limit > 0 && count( $sanitized_rows ) >= $limit ) {
				break;
			}
		}

		return array_values( $sanitized_rows );

	}

	/**
	 * Update or delete a placement meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id      Page post ID.
	 * @param    string    $meta_key     Placement meta key.
	 * @param    array     $placements   Sanitized placement rows.
	 */
	private function update_placement_meta( $post_id, $meta_key, $placements ) {

		if ( empty( $placements ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		update_post_meta( $post_id, $meta_key, $placements );

	}

	/**
	 * Get the current post ID during ACF/admin validation.
	 *
	 * @since    1.0.0
	 * @return   int
	 */
	private function get_current_admin_post_id() {

		$post_id_keys = array( 'post_ID', 'post_id', '_acf_post_id' );

		foreach ( $post_id_keys as $post_id_key ) {
			if ( ! isset( $_POST[ $post_id_key ] ) ) {
				continue;
			}

			$raw_post_id = wp_unslash( $_POST[ $post_id_key ] );

			if ( ! is_scalar( $raw_post_id ) ) {
				continue;
			}

			if ( preg_match( '/^post_(\d+)$/', (string) $raw_post_id, $matches ) ) {
				return absint( $matches[1] );
			}

			$post_id = absint( $raw_post_id );

			if ( $post_id ) {
				return $post_id;
			}
		}

		return 0;

	}

	/**
	 * Determine whether a video is active.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video post ID.
	 * @return   bool
	 */
	private function is_video_active( $video_id ) {

		$active = get_post_meta( $video_id, self::FIELD_IS_ACTIVE, true );

		return '' === $active || '0' !== (string) $active;

	}

	/**
	 * List missing required fields for a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video post ID.
	 * @return   array
	 */
	private function get_video_required_field_issues( $video_id ) {

		$issues   = array();
		$provider = get_post_meta( $video_id, self::FIELD_VIDEO_PROVIDER, true );

		if ( '' === trim( (string) get_post_meta( $video_id, self::FIELD_HASH_SLUG, true ) ) ) {
			$issues[] = __( 'hash slug', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, self::FIELD_CARD_TITLE, true ) ) ) {
			$issues[] = __( 'default card title', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, self::FIELD_CARD_DESCRIPTION, true ) ) ) {
			$issues[] = __( 'default card description', 'one-minute-media-video-showcase' );
		}

		$thumbnail_id = absint( get_post_meta( $video_id, self::FIELD_CARD_THUMBNAIL, true ) );

		if ( ! $thumbnail_id || ! $this->is_valid_attachment( $thumbnail_id ) ) {
			$issues[] = __( 'default card thumbnail', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, self::FIELD_MODAL_TITLE, true ) ) ) {
			$issues[] = __( 'modal title', 'one-minute-media-video-showcase' );
		}

		$modal_overview = get_post_meta( $video_id, self::FIELD_MODAL_OVERVIEW, true );

		if ( '' === trim( wp_strip_all_tags( (string) $modal_overview ) ) ) {
			$issues[] = __( 'production overview', 'one-minute-media-video-showcase' );
		}

		if ( ! in_array( $provider, array( 'vimeo', 'youtube', 'url' ), true ) ) {
			$issues[] = __( 'video provider', 'one-minute-media-video-showcase' );
		}

		if ( in_array( $provider, array( 'vimeo', 'youtube' ), true ) && '' === trim( (string) get_post_meta( $video_id, self::FIELD_VIDEO_ID, true ) ) ) {
			$issues[] = __( 'video ID', 'one-minute-media-video-showcase' );
		}

		if ( 'url' === $provider ) {
			$video_url = trim( (string) get_post_meta( $video_id, self::FIELD_VIDEO_URL, true ) );

			if ( '' === $video_url || '' === esc_url_raw( $video_url ) ) {
				$issues[] = __( 'video URL', 'one-minute-media-video-showcase' );
			}
		}

		return $issues;

	}

	/**
	 * Get a readable admin label for a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video post ID.
	 * @return   string
	 */
	private function get_video_admin_label( $video_id ) {

		$title = get_the_title( $video_id );

		if ( '' === trim( (string) $title ) ) {
			return sprintf(
				/* translators: %d: video post ID. */
				__( 'Video #%d', 'one-minute-media-video-showcase' ),
				(int) $video_id
			);
		}

		return sprintf(
			/* translators: 1: video title, 2: video post ID. */
			__( '%1$s (#%2$d)', 'one-minute-media-video-showcase' ),
			$title,
			(int) $video_id
		);

	}

	/**
	 * Check that a selected video ID points to a Video Case Study post.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video post ID.
	 * @return   bool
	 */
	private function is_valid_video_case_study( $video_id ) {

		return $video_id > 0 && 'video_case_study' === get_post_type( $video_id );

	}

	/**
	 * Check that a selected thumbnail ID points to an attachment.
	 *
	 * @since    1.0.0
	 * @param    int    $attachment_id    Attachment ID.
	 * @return   bool
	 */
	private function is_valid_attachment( $attachment_id ) {

		return $attachment_id > 0 && 'attachment' === get_post_type( $attachment_id );

	}

	/**
	 * Show a safe admin notice when ACF Free is unavailable.
	 *
	 * @since    1.0.0
	 */
	public function maybe_show_missing_acf_notice() {

		if ( self::is_acf_available() ) {
			return;
		}

		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="notice notice-warning">
			<p>
				<?php
				echo esc_html__(
					'1 Minute Media Video Showcase: ACF Free is not active. Simple Video Case Study field groups will not be registered until ACF Free is active; plugin-owned metaboxes and settings will handle repeatable placement data in later field phases.',
					'one-minute-media-video-showcase'
				);
				?>
			</p>
		</div>
		<?php

	}

}

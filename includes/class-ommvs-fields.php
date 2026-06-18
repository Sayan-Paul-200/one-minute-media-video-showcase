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
	const FIELD_MODAL_CONTENT     = 'ommvs_modal_overview';
	const FIELD_VIDEO_PROVIDER    = 'ommvs_video_provider';
	const FIELD_VIDEO_ID          = 'ommvs_video_id';
	const FIELD_VIDEO_URL         = 'ommvs_video_url';
	const FIELD_RELATED_THUMBNAIL = 'ommvs_related_thumbnail';
	const FIELD_ADMIN_NOTES       = 'ommvs_admin_notes';

	const META_FEATURED_VIDEOS = 'ommvs_featured_videos';
	const META_MORE_VIDEOS     = 'ommvs_more_videos';

	const PLACEMENT_VIDEO = 'video';

	const FEATURED_VIDEOS_MAX = 6; // Deprecated: retained for backward compatibility only.

	const PAGE_PLACEMENTS_NONCE_ACTION = 'ommvs_save_page_placements';
	const PAGE_PLACEMENTS_NONCE_NAME   = 'ommvs_page_placements_nonce';

	const VIDEO_FALLBACK_NONCE_ACTION = 'ommvs_save_video_fallback_fields';
	const VIDEO_FALLBACK_NONCE_NAME   = 'ommvs_video_fallback_nonce';
	const VIDEO_FALLBACK_FIELD_GROUP  = 'ommvs_video_fallback';

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
	 * Determine whether a URL is a supported Vimeo video URL.
	 *
	 * @since    1.0.0
	 * @param    string    $url    URL value.
	 * @return   bool
	 */
	public static function is_valid_vimeo_url( $url ) {

		return '' !== self::get_vimeo_video_id_from_url( $url );

	}

	/**
	 * Extract a Vimeo video ID from a supported Vimeo URL.
	 *
	 * @since    1.0.0
	 * @param    string    $url    URL value.
	 * @return   string
	 */
	public static function get_vimeo_video_id_from_url( $url ) {

		$url = trim( (string) $url );

		if ( '' === $url || '' === esc_url_raw( $url ) ) {
			return '';
		}

		$parts = wp_parse_url( $url );

		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
			return '';
		}

		$scheme = strtolower( (string) $parts['scheme'] );
		$host   = strtolower( (string) $parts['host'] );

		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
			return '';
		}

		if ( ! in_array( $host, array( 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ), true ) ) {
			return '';
		}

		$path     = isset( $parts['path'] ) ? trim( (string) $parts['path'], '/' ) : '';
		$segments = '' !== $path ? explode( '/', $path ) : array();

		foreach ( $segments as $segment ) {
			if ( preg_match( '/^\d+$/', $segment ) ) {
				return $segment;
			}
		}

		return '';

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
	 * Validate that the video URL field contains a supported Vimeo URL.
	 *
	 * @since    1.0.0
	 * @param    bool|string    $valid    Existing ACF validation state.
	 * @param    mixed          $value    Submitted field value.
	 * @param    array          $field    ACF field settings.
	 * @param    string         $input    ACF input name.
	 * @return   bool|string
	 */
	public function validate_vimeo_video_url( $valid, $value, $field, $input ) {

		unset( $field, $input );

		if ( true !== $valid ) {
			return $valid;
		}

		$url = is_scalar( $value ) ? trim( (string) $value ) : '';

		if ( '' === $url ) {
			return $valid;
		}

		if ( ! self::is_valid_vimeo_url( $url ) ) {
			return __( 'Enter a valid Vimeo video URL, for example https://vimeo.com/879662317.', 'one-minute-media-video-showcase' );
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
						'label'         => __( 'Modal Content', 'one-minute-media-video-showcase' ),
						'name'          => self::FIELD_MODAL_CONTENT,
						'type'          => 'wysiwyg',
						'instructions'  => __( 'Full modal body content displayed under the modal title and category. Include headings, paragraphs, and bullet lists here.', 'one-minute-media-video-showcase' ),
						'required'      => 1,
						'tabs'          => 'all',
						'toolbar'       => 'full',
						'media_upload'  => 1,
						'delay'         => 0,
					),
					array(
						'key'          => 'field_ommvs_video_url',
						'label'        => __( 'Vimeo Video URL', 'one-minute-media-video-showcase' ),
						'name'         => self::FIELD_VIDEO_URL,
						'type'         => 'url',
						'instructions' => __( 'Paste the Vimeo video URL, for example https://vimeo.com/879662317.', 'one-minute-media-video-showcase' ),
						'required'     => 1,
						'placeholder'  => 'https://vimeo.com/879662317',
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
	 * Register fallback Video Case Study fields when ACF is unavailable.
	 *
	 * @since    1.0.0
	 * @param    WP_Post|null    $post    Current Video Case Study post.
	 */
	public function register_video_fallback_metaboxes( $post = null ) {

		unset( $post );

		if ( self::is_acf_available() ) {
			return;
		}

		add_meta_box(
			'ommvs-video-fallback-fields',
			__( 'Video Showcase Details', 'one-minute-media-video-showcase' ),
			array( $this, 'render_video_fallback_metabox' ),
			'video_case_study',
			'normal',
			'high'
		);

	}

	/**
	 * Render fallback Video Case Study fields.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Current Video Case Study post.
	 */
	public function render_video_fallback_metabox( $post ) {

		wp_nonce_field( self::VIDEO_FALLBACK_NONCE_ACTION, self::VIDEO_FALLBACK_NONCE_NAME );

		?>
		<div class="ommvs-video-fallback-fields">
			<p class="description">
				<?php esc_html_e( 'ACF Free is not active, so these plugin-owned fallback fields are saving directly to the same Video Case Study meta keys.', 'one-minute-media-video-showcase' ); ?>
			</p>

			<table class="form-table ommvs-video-fallback-fields__table" role="presentation">
				<tbody>
					<?php
					$this->render_fallback_text_field( $post->ID, self::FIELD_HASH_SLUG, __( 'Hash Slug', 'one-minute-media-video-showcase' ), __( 'Legacy URL hash without the leading #, for example nick-kyrgios.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_checkbox_field( $post->ID, self::FIELD_IS_ACTIVE, __( 'Active', 'one-minute-media-video-showcase' ), __( 'Inactive videos should not be used in page placements.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_text_field( $post->ID, self::FIELD_CARD_TITLE, __( 'Default Card Title', 'one-minute-media-video-showcase' ), __( 'Title used on video cards.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_textarea_field( $post->ID, self::FIELD_CARD_DESCRIPTION, __( 'Default Card Description', 'one-minute-media-video-showcase' ), __( 'Short description used on video cards.', 'one-minute-media-video-showcase' ), 3 );
					$this->render_fallback_thumbnail_field( $post->ID, self::FIELD_CARD_THUMBNAIL, __( 'Default Card Thumbnail', 'one-minute-media-video-showcase' ), __( 'Default card thumbnail. Stored as an attachment ID for consistent rendering.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_textarea_field( $post->ID, self::FIELD_MODAL_TITLE, __( 'Modal Title', 'one-minute-media-video-showcase' ), __( 'Title displayed in the modal. This can differ from the card title.', 'one-minute-media-video-showcase' ), 2 );
					$this->render_fallback_editor_field( $post->ID, self::FIELD_MODAL_CONTENT, __( 'Modal Content', 'one-minute-media-video-showcase' ), __( 'Full modal body content displayed under the modal title and category. Include headings, paragraphs, and bullet lists here.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_url_field( $post->ID, self::FIELD_VIDEO_URL, __( 'Vimeo Video URL', 'one-minute-media-video-showcase' ), __( 'Paste the Vimeo video URL, for example https://vimeo.com/879662317.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_thumbnail_field( $post->ID, self::FIELD_RELATED_THUMBNAIL, __( 'Related Thumbnail Override', 'one-minute-media-video-showcase' ), __( 'Optional thumbnail for related cards. Leave empty to use the default card thumbnail.', 'one-minute-media-video-showcase' ) );
					$this->render_fallback_textarea_field( $post->ID, self::FIELD_ADMIN_NOTES, __( 'Admin Notes', 'one-minute-media-video-showcase' ), __( 'Internal migration or editorial notes. Not rendered on the frontend.', 'one-minute-media-video-showcase' ), 4 );
					?>
				</tbody>
			</table>
		</div>
		<?php

	}

	/**
	 * Save fallback Video Case Study fields when ACF is unavailable.
	 *
	 * @since    1.0.0
	 * @param    int        $post_id    Current post ID.
	 * @param    WP_Post    $post       Current post object.
	 * @param    bool       $update     Whether this is an existing post being updated.
	 */
	public function save_video_fallback_fields( $post_id, $post, $update ) {

		unset( $update );

		if ( self::is_acf_available() || 'video_case_study' !== $post->post_type ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if (
			! isset( $_POST[ self::VIDEO_FALLBACK_NONCE_NAME ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::VIDEO_FALLBACK_NONCE_NAME ] ) ), self::VIDEO_FALLBACK_NONCE_ACTION )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw_fields = isset( $_POST[ self::VIDEO_FALLBACK_FIELD_GROUP ] ) && is_array( $_POST[ self::VIDEO_FALLBACK_FIELD_GROUP ] )
			? wp_unslash( $_POST[ self::VIDEO_FALLBACK_FIELD_GROUP ] )
			: array();

		$this->update_text_meta( $post_id, self::FIELD_HASH_SLUG, ltrim( $this->get_raw_fallback_value( $raw_fields, self::FIELD_HASH_SLUG ), '#' ) );
		$this->update_bool_meta( $post_id, self::FIELD_IS_ACTIVE, ! empty( $raw_fields[ self::FIELD_IS_ACTIVE ] ) );
		$this->update_text_meta( $post_id, self::FIELD_CARD_TITLE, $this->get_raw_fallback_value( $raw_fields, self::FIELD_CARD_TITLE ) );
		$this->update_textarea_meta( $post_id, self::FIELD_CARD_DESCRIPTION, $this->get_raw_fallback_value( $raw_fields, self::FIELD_CARD_DESCRIPTION ) );
		$this->update_attachment_meta( $post_id, self::FIELD_CARD_THUMBNAIL, $this->get_raw_fallback_value( $raw_fields, self::FIELD_CARD_THUMBNAIL ) );
		$this->update_textarea_meta( $post_id, self::FIELD_MODAL_TITLE, $this->get_raw_fallback_value( $raw_fields, self::FIELD_MODAL_TITLE ) );
		$this->update_html_meta( $post_id, self::FIELD_MODAL_CONTENT, $this->get_raw_fallback_value( $raw_fields, self::FIELD_MODAL_CONTENT ) );
		$this->update_vimeo_url_meta( $post_id, self::FIELD_VIDEO_URL, $this->get_raw_fallback_value( $raw_fields, self::FIELD_VIDEO_URL ) );
		$this->update_attachment_meta( $post_id, self::FIELD_RELATED_THUMBNAIL, $this->get_raw_fallback_value( $raw_fields, self::FIELD_RELATED_THUMBNAIL ) );
		$this->update_textarea_meta( $post_id, self::FIELD_ADMIN_NOTES, $this->get_raw_fallback_value( $raw_fields, self::FIELD_ADMIN_NOTES ) );

	}

	/**
	 * Render a fallback text field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 */
	private function render_fallback_text_field( $post_id, $meta_key, $label, $description ) {

		$this->render_fallback_input_field( $post_id, $meta_key, $label, $description, 'text' );

	}

	/**
	 * Render a fallback URL field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 */
	private function render_fallback_url_field( $post_id, $meta_key, $label, $description ) {

		$this->render_fallback_input_field( $post_id, $meta_key, $label, $description, 'url' );

	}

	/**
	 * Render a fallback input field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 * @param    string    $type           Input type.
	 */
	private function render_fallback_input_field( $post_id, $meta_key, $label, $description, $type ) {

		$field_id = 'ommvs-' . str_replace( '_', '-', $meta_key );
		$value    = (string) get_post_meta( $post_id, $meta_key, true );

		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<input
					type="<?php echo esc_attr( $type ); ?>"
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( self::VIDEO_FALLBACK_FIELD_GROUP . '[' . $meta_key . ']' ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					class="regular-text"
				/>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php

	}

	/**
	 * Render a fallback checkbox field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 */
	private function render_fallback_checkbox_field( $post_id, $meta_key, $label, $description ) {

		$field_id = 'ommvs-' . str_replace( '_', '-', $meta_key );
		$value    = get_post_meta( $post_id, $meta_key, true );
		$checked  = '' === $value || '0' !== (string) $value;

		?>
		<tr>
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td>
				<label for="<?php echo esc_attr( $field_id ); ?>">
					<input
						type="checkbox"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( self::VIDEO_FALLBACK_FIELD_GROUP . '[' . $meta_key . ']' ); ?>"
						value="1"
						<?php checked( $checked ); ?>
					/>
					<?php esc_html_e( 'Active', 'one-minute-media-video-showcase' ); ?>
				</label>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php

	}

	/**
	 * Render a fallback textarea field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 * @param    int       $rows           Textarea rows.
	 * @param    bool      $allow_html     Whether to show stored HTML.
	 */
	private function render_fallback_textarea_field( $post_id, $meta_key, $label, $description, $rows = 4, $allow_html = false ) {

		$field_id = 'ommvs-' . str_replace( '_', '-', $meta_key );
		$value    = (string) get_post_meta( $post_id, $meta_key, true );

		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<textarea
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( self::VIDEO_FALLBACK_FIELD_GROUP . '[' . $meta_key . ']' ); ?>"
					rows="<?php echo esc_attr( (string) absint( $rows ) ); ?>"
					class="large-text"
				><?php echo esc_textarea( $allow_html ? wp_kses_post( $value ) : $value ); ?></textarea>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php

	}

	/**
	 * Render a fallback rich editor field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 */
	private function render_fallback_editor_field( $post_id, $meta_key, $label, $description ) {

		$field_id = 'ommvs-' . str_replace( '_', '-', $meta_key );
		$value    = (string) get_post_meta( $post_id, $meta_key, true );

		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<div class="ommvs-video-fallback-editor">
					<?php
					wp_editor(
						wp_kses_post( $value ),
						$field_id,
						array(
							'textarea_name' => self::VIDEO_FALLBACK_FIELD_GROUP . '[' . $meta_key . ']',
							'textarea_rows' => 12,
							'media_buttons' => true,
							'teeny'         => false,
							'quicktags'     => true,
							'tinymce'       => true,
						)
					);
					?>
				</div>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php

	}

	/**
	 * Render a fallback thumbnail field row.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id        Video post ID.
	 * @param    string    $meta_key       Meta key.
	 * @param    string    $label          Field label.
	 * @param    string    $description    Field description.
	 */
	private function render_fallback_thumbnail_field( $post_id, $meta_key, $label, $description ) {

		$thumbnail_id = absint( get_post_meta( $post_id, $meta_key, true ) );

		if ( ! $this->is_valid_attachment( $thumbnail_id ) ) {
			$thumbnail_id = 0;
		}

		?>
		<tr>
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td>
				<div class="ommvs-placement-thumbnail ommvs-video-fallback-thumbnail" data-ommvs-thumbnail>
					<input
						type="hidden"
						name="<?php echo esc_attr( self::VIDEO_FALLBACK_FIELD_GROUP . '[' . $meta_key . ']' ); ?>"
						value="<?php echo esc_attr( (string) $thumbnail_id ); ?>"
						data-ommvs-thumbnail-id
					/>
					<div class="ommvs-placement-thumbnail__preview" data-ommvs-thumbnail-preview>
						<?php
						if ( $thumbnail_id ) {
							echo wp_kses_post(
								wp_get_attachment_image(
									$thumbnail_id,
									'thumbnail',
									false,
									array(
										'class' => 'ommvs-placement-thumbnail__image',
									)
								)
							);
						}
						?>
					</div>
					<div class="ommvs-placement-thumbnail__actions">
						<button type="button" class="button button-secondary" data-ommvs-select-thumbnail>
							<?php esc_html_e( 'Choose Image', 'one-minute-media-video-showcase' ); ?>
						</button>
						<button type="button" class="button-link-delete" data-ommvs-remove-thumbnail <?php echo $thumbnail_id ? '' : 'hidden'; ?>>
							<?php esc_html_e( 'Remove Image', 'one-minute-media-video-showcase' ); ?>
						</button>
					</div>
				</div>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php

	}

	/**
	 * Read one scalar fallback field value.
	 *
	 * @since    1.0.0
	 * @param    array     $fields      Raw fallback fields.
	 * @param    string    $meta_key    Meta key.
	 * @return   string
	 */
	private function get_raw_fallback_value( $fields, $meta_key ) {

		if ( ! isset( $fields[ $meta_key ] ) || is_array( $fields[ $meta_key ] ) ) {
			return '';
		}

		return (string) $fields[ $meta_key ];

	}

	/**
	 * Update a sanitized text meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw value.
	 */
	private function update_text_meta( $post_id, $meta_key, $value ) {

		$this->update_or_delete_meta( $post_id, $meta_key, sanitize_text_field( $value ) );

	}

	/**
	 * Update a sanitized textarea meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw value.
	 */
	private function update_textarea_meta( $post_id, $meta_key, $value ) {

		$this->update_or_delete_meta( $post_id, $meta_key, sanitize_textarea_field( $value ) );

	}

	/**
	 * Update a sanitized HTML meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw value.
	 */
	private function update_html_meta( $post_id, $meta_key, $value ) {

		$this->update_or_delete_meta( $post_id, $meta_key, wp_kses_post( $value ) );

	}

	/**
	 * Update a boolean meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    bool      $value       Boolean value.
	 */
	private function update_bool_meta( $post_id, $meta_key, $value ) {

		update_post_meta( $post_id, $meta_key, $value ? '1' : '0' );

	}

	/**
	 * Update the video provider meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id    Post ID.
	 * @param    string    $value      Raw provider value.
	 */
	private function update_provider_meta( $post_id, $value ) {

		$provider = sanitize_key( $value );

		if ( ! in_array( $provider, array( 'vimeo', 'youtube', 'url' ), true ) ) {
			delete_post_meta( $post_id, self::FIELD_VIDEO_PROVIDER );
			return;
		}

		update_post_meta( $post_id, self::FIELD_VIDEO_PROVIDER, $provider );

	}

	/**
	 * Update a URL meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw URL.
	 */
	private function update_url_meta( $post_id, $meta_key, $value ) {

		$url = trim( (string) $value );

		if ( '' === $url || ! $this->is_valid_direct_video_url( $url ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		update_post_meta( $post_id, $meta_key, esc_url_raw( $url ) );

	}

	/**
	 * Update a Vimeo URL meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw URL.
	 */
	private function update_vimeo_url_meta( $post_id, $meta_key, $value ) {

		$url = trim( (string) $value );

		if ( '' === $url || ! self::is_valid_vimeo_url( $url ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		update_post_meta( $post_id, $meta_key, esc_url_raw( $url ) );

	}

	/**
	 * Update an attachment ID meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Raw attachment ID.
	 */
	private function update_attachment_meta( $post_id, $meta_key, $value ) {

		$attachment_id = absint( $value );

		if ( ! $this->is_valid_attachment( $attachment_id ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		update_post_meta( $post_id, $meta_key, $attachment_id );

	}

	/**
	 * Update or delete a scalar meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $value       Sanitized value.
	 */
	private function update_or_delete_meta( $post_id, $meta_key, $value ) {

		if ( '' === trim( (string) $value ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		update_post_meta( $post_id, $meta_key, $value );

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
		$featured_placements = $this->get_page_placements( $post->ID, self::META_FEATURED_VIDEOS );
		$more_placements     = $this->get_page_placements( $post->ID, self::META_MORE_VIDEOS );

		?>
		<div class="ommvs-page-placements">
			<?php
			$this->render_placement_section(
				self::META_FEATURED_VIDEOS,
				__( 'Featured Videos', 'one-minute-media-video-showcase' ),
				__( 'Ordered source list for page-specific related videos.', 'one-minute-media-video-showcase' ),
				$featured_placements,
				$video_options
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

		$featured_placements = $this->sanitize_placement_rows( $raw_featured );
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
	 * @return   array
	 */
	private function get_page_placements( $post_id, $meta_key ) {

		$placements = get_post_meta( $post_id, $meta_key, true );

		if ( ! is_array( $placements ) ) {
			return array();
		}

		return $this->sanitize_placement_rows( $placements );

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
			$post_title = get_the_title( $video_post );
			$card_title = sanitize_text_field( (string) get_post_meta( $video_post->ID, self::FIELD_CARD_TITLE, true ) );
			$title      = '' !== trim( $card_title ) ? $card_title : $post_title;

			if ( '' === trim( $title ) ) {
				$title = sprintf(
					/* translators: %d: video post ID. */
					__( 'Video #%d', 'one-minute-media-video-showcase' ),
					$video_post->ID
				);
			}

			$options[] = array(
				'id'         => (int) $video_post->ID,
				'title'      => $title,
				'post_title' => $post_title,
			);
		}

		usort(
			$options,
			static function ( $first_option, $second_option ) {
				$title_compare = strcasecmp( (string) $first_option['title'], (string) $second_option['title'] );

				if ( 0 !== $title_compare ) {
					return $title_compare;
				}

				return absint( $first_option['id'] ?? 0 ) <=> absint( $second_option['id'] ?? 0 );
			}
		);

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
	 */
	private function render_placement_section( $meta_key, $title, $description, $placements, $video_options ) {

		?>
		<section class="ommvs-placement-section" data-ommvs-placement-section data-meta-key="<?php echo esc_attr( $meta_key ); ?>">
			<div class="ommvs-placement-section__header">
				<div>
					<h3><?php echo esc_html( $title ); ?></h3>
					<p><?php echo esc_html( $description ); ?></p>
				</div>
				<button type="button" class="button button-secondary ommvs-placement-section__add" data-ommvs-add-row>
					<?php esc_html_e( 'Add Video', 'one-minute-media-video-showcase' ); ?>
				</button>
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
							<option value="<?php echo esc_attr( $video_option['id'] ); ?>" data-ommvs-post-title="<?php echo esc_attr( $video_option['post_title'] ); ?>" <?php selected( $video_id, $video_option['id'] ); ?>>
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
	 * @return   array
	 */
	private function sanitize_placement_rows( $rows ) {

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

		$issues = array();

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

		$modal_content = get_post_meta( $video_id, self::FIELD_MODAL_CONTENT, true );

		if ( '' === trim( wp_strip_all_tags( (string) $modal_content ) ) ) {
			$issues[] = __( 'modal content', 'one-minute-media-video-showcase' );
		}

		$vimeo_url = trim( (string) get_post_meta( $video_id, self::FIELD_VIDEO_URL, true ) );

		if ( ! self::is_valid_vimeo_url( $vimeo_url ) ) {
			$issues[] = __( 'Vimeo video URL', 'one-minute-media-video-showcase' );
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
	 * Check whether a Direct URL video value is an absolute HTTP(S) URL.
	 *
	 * @since    1.0.0
	 * @param    string    $url    URL value.
	 * @return   bool
	 */
	private function is_valid_direct_video_url( $url ) {

		$url = trim( (string) $url );

		if ( '' === $url || '' === esc_url_raw( $url ) ) {
			return false;
		}

		$parts = wp_parse_url( $url );

		return is_array( $parts )
			&& ! empty( $parts['scheme'] )
			&& ! empty( $parts['host'] )
			&& in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true );

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
					'1 Minute Media Video Showcase: ACF Free is not active. Plugin-owned fallback fields are available on Video Case Study edit screens, but ACF Free is recommended for the full editing experience.',
					'one-minute-media-video-showcase'
				);
				?>
			</p>
		</div>
		<?php

	}

}

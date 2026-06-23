<?php

/**
 * Admin-only Video Case Study UI helpers.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Render read-only Video Case Study admin helpers.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Video_Admin_UI {

	/**
	 * Register the read-only readiness metabox.
	 *
	 * @since    1.0.0
	 */
	public function register_readiness_metabox() {

		add_meta_box(
			'ommvs-video-readiness',
			__( 'Video Readiness', 'one-minute-media-video-showcase' ),
			array( $this, 'render_readiness_metabox' ),
			'video_case_study',
			'side',
			'high'
		);

	}

	/**
	 * Render the read-only readiness metabox.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Current Video Case Study post.
	 */
	public function render_readiness_metabox( $post ) {

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$items    = $this->get_readiness_items( $post->ID );
		$is_ready = $this->is_ready( $items );

		?>
		<div class="ommvs-video-readiness">
			<div class="ommvs-video-readiness__summary">
				<?php
				$this->render_status_badge(
					$is_ready ? __( 'Ready for placement', 'one-minute-media-video-showcase' ) : __( 'Needs attention', 'one-minute-media-video-showcase' ),
					$is_ready ? 'success' : 'warning'
				);
				?>
				<p>
					<?php
					echo esc_html(
						$is_ready
							? __( 'This video has the required data for page placement and modal rendering.', 'one-minute-media-video-showcase' )
							: __( 'Review the warnings below before using this video in page placements.', 'one-minute-media-video-showcase' )
					);
					?>
				</p>
			</div>

			<ul class="ommvs-video-readiness__list">
				<?php foreach ( $items as $item ) : ?>
					<li class="ommvs-video-readiness__item">
						<div class="ommvs-video-readiness__item-header">
							<span class="ommvs-video-readiness__item-label"><?php echo esc_html( $item['label'] ); ?></span>
							<?php $this->render_status_badge( $item['status_label'], $item['status'] ); ?>
						</div>
						<?php if ( '' !== $item['detail'] ) : ?>
							<div class="ommvs-video-readiness__item-detail">
								<?php echo wp_kses_post( $item['detail'] ); ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="ommvs-video-readiness__action">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=video_case_study&page=ommvs-data-health' ) ); ?>">
					<?php esc_html_e( 'Open Data Health', 'one-minute-media-video-showcase' ); ?>
				</a>
			</p>
		</div>
		<?php

	}

	/**
	 * Get readiness items for one Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_readiness_items( $post_id ) {

		return array(
			$this->get_active_item( $post_id ),
			$this->get_hash_item( $post_id ),
			$this->get_text_meta_item( $post_id, OMMVS_Fields::FIELD_CARD_TITLE, __( 'Default Card Title', 'one-minute-media-video-showcase' ) ),
			$this->get_text_meta_item( $post_id, OMMVS_Fields::FIELD_CARD_DESCRIPTION, __( 'Default Card Description', 'one-minute-media-video-showcase' ) ),
			$this->get_thumbnail_item( $post_id ),
			$this->get_category_item( $post_id ),
			$this->get_text_meta_item( $post_id, OMMVS_Fields::FIELD_MODAL_TITLE, __( 'Modal Title', 'one-minute-media-video-showcase' ) ),
			$this->get_modal_content_item( $post_id ),
			$this->get_vimeo_url_item( $post_id ),
		);

	}

	/**
	 * Determine if every readiness item has passed.
	 *
	 * @since    1.0.0
	 * @param    array    $items    Readiness items.
	 * @return   bool
	 */
	private function is_ready( $items ) {

		foreach ( $items as $item ) {
			if ( 'success' !== $item['status'] ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Build the Active readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_active_item( $post_id ) {

		$active = get_post_meta( $post_id, OMMVS_Fields::FIELD_IS_ACTIVE, true );

		if ( '' === $active || '0' !== (string) $active ) {
			return $this->get_item(
				__( 'Active', 'one-minute-media-video-showcase' ),
				'success',
				__( 'Active', 'one-minute-media-video-showcase' ),
				__( 'Available for page placements.', 'one-minute-media-video-showcase' )
			);
		}

		return $this->get_item(
			__( 'Active', 'one-minute-media-video-showcase' ),
			'warning',
			__( 'Inactive', 'one-minute-media-video-showcase' ),
			__( 'Inactive videos are removed from placements and frontend data.', 'one-minute-media-video-showcase' )
		);

	}

	/**
	 * Build the Hash Slug readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_hash_item( $post_id ) {

		$hash_slug = trim( sanitize_text_field( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_HASH_SLUG, true ) ) );
		$hash_slug = ltrim( $hash_slug, '#' );

		if ( '' === $hash_slug ) {
			return $this->get_item(
				__( 'Hash Slug', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Add the legacy hash slug without the leading #.', 'one-minute-media-video-showcase' )
			);
		}

		return $this->get_item(
			__( 'Hash Slug', 'one-minute-media-video-showcase' ),
			'success',
			__( 'Ready', 'one-minute-media-video-showcase' ),
			sprintf(
				'<code class="ommvs-video-readiness__code">%s</code>',
				esc_html( '#' . $hash_slug )
			)
		);

	}

	/**
	 * Build a generic non-empty text-meta readiness item.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id     Video Case Study post ID.
	 * @param    string    $meta_key    Meta key.
	 * @param    string    $label       Readiness label.
	 * @return   array
	 */
	private function get_text_meta_item( $post_id, $meta_key, $label ) {

		$value = trim( (string) get_post_meta( $post_id, $meta_key, true ) );

		if ( '' === $value ) {
			return $this->get_item(
				$label,
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Required field is empty.', 'one-minute-media-video-showcase' )
			);
		}

		return $this->get_item(
			$label,
			'success',
			__( 'Ready', 'one-minute-media-video-showcase' ),
			''
		);

	}

	/**
	 * Build the Default Card Thumbnail readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_thumbnail_item( $post_id ) {

		$thumbnail_id = absint( get_post_meta( $post_id, OMMVS_Fields::FIELD_CARD_THUMBNAIL, true ) );

		if ( ! $this->is_valid_attachment( $thumbnail_id ) ) {
			return $this->get_item(
				__( 'Default Card Thumbnail', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Choose a valid frontend card thumbnail.', 'one-minute-media-video-showcase' )
			);
		}

		return $this->get_item(
			__( 'Default Card Thumbnail', 'one-minute-media-video-showcase' ),
			'success',
			__( 'Ready', 'one-minute-media-video-showcase' ),
			sprintf(
				/* translators: %d: attachment ID. */
				__( 'Attachment ID: %d', 'one-minute-media-video-showcase' ),
				$thumbnail_id
			)
		);

	}

	/**
	 * Build the Video Category readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_category_item( $post_id ) {

		$terms = $this->get_video_category_terms( $post_id );

		if ( empty( $terms ) ) {
			return $this->get_item(
				__( 'Video Category', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Assign one Video Category in the sidebar.', 'one-minute-media-video-showcase' )
			);
		}

		$term_names = array();

		foreach ( $terms as $term ) {
			$term_names[] = sanitize_text_field( (string) $term->name );
		}

		if ( count( $terms ) > 1 ) {
			return $this->get_item(
				__( 'Video Category', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Multiple', 'one-minute-media-video-showcase' ),
				sprintf(
					/* translators: %s: comma-separated category names. */
					__( 'Multiple categories assigned: %s', 'one-minute-media-video-showcase' ),
					implode( ', ', $term_names )
				)
			);
		}

		return $this->get_item(
			__( 'Video Category', 'one-minute-media-video-showcase' ),
			'success',
			__( 'Ready', 'one-minute-media-video-showcase' ),
			reset( $term_names )
		);

	}

	/**
	 * Build the Modal Content readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_modal_content_item( $post_id ) {

		$modal_content = get_post_meta( $post_id, OMMVS_Fields::FIELD_MODAL_CONTENT, true );

		if ( '' === trim( wp_strip_all_tags( (string) $modal_content ) ) ) {
			return $this->get_item(
				__( 'Modal Content', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Add rich content for the modal body.', 'one-minute-media-video-showcase' )
			);
		}

		return $this->get_item(
			__( 'Modal Content', 'one-minute-media-video-showcase' ),
			'success',
			__( 'Ready', 'one-minute-media-video-showcase' ),
			''
		);

	}

	/**
	 * Build the Vimeo URL readiness item.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_vimeo_url_item( $post_id ) {

		$vimeo_url = trim( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );

		if ( '' === $vimeo_url ) {
			return $this->get_item(
				__( 'Vimeo Video URL', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Missing', 'one-minute-media-video-showcase' ),
				__( 'Add a Vimeo video URL.', 'one-minute-media-video-showcase' )
			);
		}

		if ( ! OMMVS_Fields::is_valid_vimeo_url( $vimeo_url ) ) {
			return $this->get_item(
				__( 'Vimeo Video URL', 'one-minute-media-video-showcase' ),
				'warning',
				__( 'Invalid', 'one-minute-media-video-showcase' ),
				__( 'Only Vimeo URLs with a numeric video ID are accepted.', 'one-minute-media-video-showcase' )
			);
		}

		$vimeo_id = OMMVS_Fields::get_vimeo_video_id_from_url( $vimeo_url );

		return $this->get_item(
			__( 'Vimeo Video URL', 'one-minute-media-video-showcase' ),
			'success',
			__( 'Valid', 'one-minute-media-video-showcase' ),
			'' !== $vimeo_id
				? sprintf(
					'<code class="ommvs-video-readiness__code">%s</code>',
					esc_html( $vimeo_id )
				)
				: ''
		);

	}

	/**
	 * Build one readiness item shape.
	 *
	 * @since    1.0.0
	 * @param    string    $label           Item label.
	 * @param    string    $status          Status slug.
	 * @param    string    $status_label    Status label.
	 * @param    string    $detail          Detail text or safe HTML.
	 * @return   array
	 */
	private function get_item( $label, $status, $status_label, $detail ) {

		return array(
			'label'        => $label,
			'status'       => $status,
			'status_label' => $status_label,
			'detail'       => (string) $detail,
		);

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
	 * Get Video Category terms assigned to a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 * @return   WP_Term[]
	 */
	private function get_video_category_terms( $post_id ) {

		$taxonomy = class_exists( 'OMMVS_Taxonomy_Video_Category' )
			? OMMVS_Taxonomy_Video_Category::TAXONOMY
			: 'ommvs_video_category';

		$terms = wp_get_object_terms(
			absint( $post_id ),
			$taxonomy,
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
	 * Check that an attachment ID points to an attachment.
	 *
	 * @since    1.0.0
	 * @param    int    $attachment_id    Attachment ID.
	 * @return   bool
	 */
	private function is_valid_attachment( $attachment_id ) {

		return $attachment_id && 'attachment' === get_post_type( $attachment_id );

	}

}

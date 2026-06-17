<?php

/**
 * Data health helper for migration readiness.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Render read-only admin diagnostics for Video Case Study migration data.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Data_Health {

	const PAGE_SLUG  = 'ommvs-data-health';
	const CAPABILITY = 'manage_options';

	/**
	 * Register the data health admin page.
	 *
	 * @since    1.0.0
	 */
	public function register_admin_page() {

		add_submenu_page(
			'edit.php?post_type=video_case_study',
			__( 'Data Health', 'one-minute-media-video-showcase' ),
			__( 'Data Health', 'one-minute-media-video-showcase' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' )
		);

	}

	/**
	 * Render the data health admin page.
	 *
	 * @since    1.0.0
	 */
	public function render_admin_page() {

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'one-minute-media-video-showcase' ) );
		}

		$report = $this->get_report();

		?>
		<div class="wrap ommvs-data-health-page">
			<h1><?php esc_html_e( '1MM Video Showcase Data Health', 'one-minute-media-video-showcase' ); ?></h1>
			<p><?php esc_html_e( 'This read-only report helps find migration issues before replacing legacy Elementor video sections.', 'one-minute-media-video-showcase' ); ?></p>

			<?php $this->render_summary( $report ); ?>
			<?php $this->render_missing_fields_section( $report['missing_fields'] ); ?>
			<?php $this->render_multiple_categories_section( $report['multiple_categories'] ); ?>
			<?php $this->render_duplicate_hashes_section( $report['duplicate_hashes'] ); ?>
			<?php $this->render_duplicate_placements_section( $report['duplicate_placements'] ); ?>
			<?php $this->render_placement_integrity_section( $report['placement_integrity'] ); ?>
		</div>
		<?php

	}

	/**
	 * Build the full health report.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_report() {

		$video_posts = $this->get_video_posts();

		return array(
			'missing_fields'       => $this->get_videos_missing_required_fields( $video_posts ),
			'multiple_categories'  => $this->get_videos_with_multiple_categories( $video_posts ),
			'duplicate_hashes'     => $this->get_duplicate_hashes( $video_posts ),
			'duplicate_placements' => $this->get_duplicate_page_placements(),
			'placement_integrity'  => $this->get_page_placement_integrity_issues(),
		);

	}

	/**
	 * Get Video Case Study posts that are relevant for health checks.
	 *
	 * @since    1.0.0
	 * @return   WP_Post[]
	 */
	private function get_video_posts() {

		return get_posts(
			array(
				'post_type'        => 'video_case_study',
				'post_status'      => 'any',
				'numberposts'      => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

	}

	/**
	 * Find videos missing required fields.
	 *
	 * @since    1.0.0
	 * @param    WP_Post[]    $video_posts    Video Case Study posts.
	 * @return   array
	 */
	private function get_videos_missing_required_fields( $video_posts ) {

		$issues = array();

		foreach ( $video_posts as $video_post ) {
			$missing = $this->get_missing_video_fields( $video_post->ID );

			if ( empty( $missing ) ) {
				continue;
			}

			$issues[] = array(
				'video_id' => (int) $video_post->ID,
				'title'    => $this->get_post_admin_label( $video_post->ID, __( 'Video', 'one-minute-media-video-showcase' ) ),
				'edit_url' => get_edit_post_link( $video_post->ID, '' ),
				'fields'   => $missing,
			);
		}

		return $issues;

	}

	/**
	 * Get missing required field labels for one video.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   array
	 */
	private function get_missing_video_fields( $video_id ) {

		$missing = array();

		if ( '' === $this->get_normalized_hash_slug( $video_id ) ) {
			$missing[] = __( 'Hash slug', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_TITLE, true ) ) ) {
			$missing[] = __( 'Default card title', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_DESCRIPTION, true ) ) ) {
			$missing[] = __( 'Default card description', 'one-minute-media-video-showcase' );
		}

		$thumbnail_id = absint( get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_THUMBNAIL, true ) );

		if ( ! $this->is_valid_attachment( $thumbnail_id ) ) {
			$missing[] = __( 'Default card thumbnail', 'one-minute-media-video-showcase' );
		}

		if ( '' === trim( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_MODAL_TITLE, true ) ) ) {
			$missing[] = __( 'Modal title', 'one-minute-media-video-showcase' );
		}

		if ( empty( $this->get_video_category_terms( $video_id ) ) ) {
			$missing[] = __( 'Video category', 'one-minute-media-video-showcase' );
		}

		$modal_content = get_post_meta( $video_id, OMMVS_Fields::FIELD_MODAL_CONTENT, true );

		if ( '' === trim( wp_strip_all_tags( (string) $modal_content ) ) ) {
			$missing[] = __( 'Modal content', 'one-minute-media-video-showcase' );
		}

		$vimeo_url = trim( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );

		if ( ! OMMVS_Fields::is_valid_vimeo_url( $vimeo_url ) ) {
			$missing[] = __( 'Vimeo Video URL', 'one-minute-media-video-showcase' );
		}

		return $missing;

	}

	/**
	 * Find videos assigned to multiple categories.
	 *
	 * @since    1.0.0
	 * @param    WP_Post[]    $video_posts    Video Case Study posts.
	 * @return   array
	 */
	private function get_videos_with_multiple_categories( $video_posts ) {

		$issues = array();

		foreach ( $video_posts as $video_post ) {
			$terms = $this->get_video_category_terms( $video_post->ID );

			if ( count( $terms ) < 2 ) {
				continue;
			}

			$categories = array();

			foreach ( $terms as $term ) {
				$categories[] = sanitize_text_field( (string) $term->name );
			}

			$issues[] = array(
				'video_id'   => (int) $video_post->ID,
				'title'      => $this->get_post_admin_label( $video_post->ID, __( 'Video', 'one-minute-media-video-showcase' ) ),
				'edit_url'   => get_edit_post_link( $video_post->ID, '' ),
				'categories' => $categories,
			);
		}

		return $issues;

	}

	/**
	 * Find duplicate hash slugs.
	 *
	 * @since    1.0.0
	 * @param    WP_Post[]    $video_posts    Video Case Study posts.
	 * @return   array
	 */
	private function get_duplicate_hashes( $video_posts ) {

		$hash_groups = array();
		$duplicates  = array();

		foreach ( $video_posts as $video_post ) {
			$hash_slug = $this->get_normalized_hash_slug( $video_post->ID );

			if ( '' === $hash_slug ) {
				continue;
			}

			if ( ! isset( $hash_groups[ $hash_slug ] ) ) {
				$hash_groups[ $hash_slug ] = array();
			}

			$hash_groups[ $hash_slug ][] = array(
				'video_id' => (int) $video_post->ID,
				'title'    => $this->get_post_admin_label( $video_post->ID, __( 'Video', 'one-minute-media-video-showcase' ) ),
				'edit_url' => get_edit_post_link( $video_post->ID, '' ),
			);
		}

		foreach ( $hash_groups as $hash_slug => $videos ) {
			if ( count( $videos ) < 2 ) {
				continue;
			}

			$duplicates[] = array(
				'hash'   => $hash_slug,
				'videos' => $videos,
			);
		}

		return $duplicates;

	}

	/**
	 * Find pages with duplicate video placements.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_duplicate_page_placements() {

		$issues = array();
		$pages  = $this->get_migrated_pages();

		foreach ( $pages as $page ) {
			$featured_ids = $this->get_page_placement_ids( $page->ID, OMMVS_Fields::META_FEATURED_VIDEOS );
			$more_ids     = $this->get_page_placement_ids( $page->ID, OMMVS_Fields::META_MORE_VIDEOS );
			$video_groups = array();

			foreach ( $featured_ids as $video_id ) {
				$video_groups[ $video_id ][] = __( 'Featured Videos', 'one-minute-media-video-showcase' );
			}

			foreach ( $more_ids as $video_id ) {
				$video_groups[ $video_id ][] = __( 'More Videos', 'one-minute-media-video-showcase' );
			}

			foreach ( $video_groups as $video_id => $groups ) {
				if ( count( $groups ) < 2 ) {
					continue;
				}

				$issues[] = array(
					'page_id'     => (int) $page->ID,
					'page_title'  => $this->get_post_admin_label( $page->ID, __( 'Page', 'one-minute-media-video-showcase' ) ),
					'page_url'    => get_edit_post_link( $page->ID, '' ),
					'video_id'    => (int) $video_id,
					'video_title' => $this->get_post_admin_label( $video_id, __( 'Video', 'one-minute-media-video-showcase' ) ),
					'video_url'   => get_edit_post_link( $video_id, '' ),
					'groups'      => array_values( array_unique( $groups ) ),
				);
			}
		}

		return $issues;

	}

	/**
	 * Find page placements that reference missing, invalid, or inactive videos.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_page_placement_integrity_issues() {

		$issues = array();
		$pages  = $this->get_migrated_pages();

		foreach ( $pages as $page ) {
			$references = array_merge(
				$this->get_page_placement_references( $page->ID, OMMVS_Fields::META_FEATURED_VIDEOS, __( 'Featured Videos', 'one-minute-media-video-showcase' ) ),
				$this->get_page_placement_references( $page->ID, OMMVS_Fields::META_MORE_VIDEOS, __( 'More Videos', 'one-minute-media-video-showcase' ) )
			);

			foreach ( $references as $reference ) {
				$video_id = absint( $reference['video_id'] );
				$video    = $video_id ? get_post( $video_id ) : null;
				$issue    = '';

				if ( ! $video_id ) {
					$issue = __( 'Missing selected video', 'one-minute-media-video-showcase' );
				} elseif ( ! $video ) {
					$issue = __( 'Referenced post does not exist', 'one-minute-media-video-showcase' );
				} elseif ( 'video_case_study' !== $video->post_type ) {
					$issue = __( 'Referenced post is not a Video Case Study', 'one-minute-media-video-showcase' );
				} elseif ( 'trash' === $video->post_status ) {
					$issue = __( 'Referenced video is in Trash', 'one-minute-media-video-showcase' );
				} elseif ( ! $this->is_video_active( $video_id ) ) {
					$issue = __( 'Referenced video is inactive', 'one-minute-media-video-showcase' );
				}

				if ( '' === $issue ) {
					continue;
				}

				$issues[] = array(
					'page_id'     => (int) $page->ID,
					'page_title'  => $this->get_post_admin_label( $page->ID, __( 'Page', 'one-minute-media-video-showcase' ) ),
					'page_url'    => get_edit_post_link( $page->ID, '' ),
					'video_id'    => (int) $video_id,
					'video_title' => $video_id ? $this->get_post_admin_label( $video_id, __( 'Post', 'one-minute-media-video-showcase' ) ) : __( 'No video selected', 'one-minute-media-video-showcase' ),
					'video_url'   => $video_id ? get_edit_post_link( $video_id, '' ) : '',
					'group'       => $reference['group'],
					'position'    => (int) $reference['position'],
					'issue'       => $issue,
				);
			}
		}

		return $issues;

	}

	/**
	 * Get pages that have OMMVS placement metadata.
	 *
	 * @since    1.0.0
	 * @return   WP_Post[]
	 */
	private function get_migrated_pages() {

		return get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => 'any',
				'numberposts'      => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'meta_query'       => array(
					'relation' => 'OR',
					array(
						'key'     => OMMVS_Fields::META_FEATURED_VIDEOS,
						'compare' => 'EXISTS',
					),
					array(
						'key'     => OMMVS_Fields::META_MORE_VIDEOS,
						'compare' => 'EXISTS',
					),
				),
				'suppress_filters' => false,
			)
		);

	}

	/**
	 * Get placement video IDs from a page meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $page_id     Page post ID.
	 * @param    string    $meta_key    Placement meta key.
	 * @return   array
	 */
	private function get_page_placement_ids( $page_id, $meta_key ) {

		$placements = get_post_meta( $page_id, $meta_key, true );
		$ids        = array();

		if ( ! is_array( $placements ) ) {
			return $ids;
		}

		foreach ( $placements as $placement ) {
			$video_id = is_array( $placement )
				? absint( $placement[ OMMVS_Fields::PLACEMENT_VIDEO ] ?? 0 )
				: absint( $placement );

			if ( $video_id ) {
				$ids[] = $video_id;
			}
		}

		return $ids;

	}

	/**
	 * Get placement row references from a page meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $page_id        Page post ID.
	 * @param    string    $meta_key       Placement meta key.
	 * @param    string    $group_label    Human-readable placement group.
	 * @return   array
	 */
	private function get_page_placement_references( $page_id, $meta_key, $group_label ) {

		$placements = get_post_meta( $page_id, $meta_key, true );
		$references = array();

		if ( ! is_array( $placements ) ) {
			return $references;
		}

		foreach ( $placements as $index => $placement ) {
			$video_id = is_array( $placement )
				? absint( $placement[ OMMVS_Fields::PLACEMENT_VIDEO ] ?? 0 )
				: absint( $placement );

			$references[] = array(
				'video_id' => $video_id,
				'group'    => $group_label,
				'position' => absint( $index ) + 1,
			);
		}

		return $references;

	}

	/**
	 * Render summary cards.
	 *
	 * @since    1.0.0
	 * @param    array    $report    Health report.
	 */
	private function render_summary( $report ) {

		?>
		<div class="ommvs-health-summary">
			<?php
			$this->render_summary_item( __( 'Missing video fields', 'one-minute-media-video-showcase' ), count( $report['missing_fields'] ) );
			$this->render_summary_item( __( 'Multiple categories', 'one-minute-media-video-showcase' ), count( $report['multiple_categories'] ) );
			$this->render_summary_item( __( 'Duplicate hashes', 'one-minute-media-video-showcase' ), count( $report['duplicate_hashes'] ) );
			$this->render_summary_item( __( 'Duplicate placements', 'one-minute-media-video-showcase' ), count( $report['duplicate_placements'] ) );
			$this->render_summary_item( __( 'Placement integrity', 'one-minute-media-video-showcase' ), count( $report['placement_integrity'] ) );
			?>
		</div>
		<?php

	}

	/**
	 * Render one summary item.
	 *
	 * @since    1.0.0
	 * @param    string    $label    Summary label.
	 * @param    int       $count    Issue count.
	 */
	private function render_summary_item( $label, $count ) {

		?>
		<div class="ommvs-health-summary__item">
			<strong><?php echo esc_html( (string) absint( $count ) ); ?></strong>
			<span><?php echo esc_html( $label ); ?></span>
		</div>
		<?php

	}

	/**
	 * Render videos missing fields section.
	 *
	 * @since    1.0.0
	 * @param    array    $issues    Section issues.
	 */
	private function render_missing_fields_section( $issues ) {

		$this->render_section_open(
			__( 'Videos Missing Required Fields', 'one-minute-media-video-showcase' ),
			__( 'These Video Case Study posts should be completed before frontend migration testing.', 'one-minute-media-video-showcase' )
		);

		if ( empty( $issues ) ) {
			$this->render_no_issues();
			$this->render_section_close();
			return;
		}

		?>
		<table class="widefat striped ommvs-health-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Video', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Missing fields', 'one-minute-media-video-showcase' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $issues as $issue ) : ?>
					<tr>
						<td><?php $this->render_edit_link( $issue['title'], $issue['edit_url'] ); ?></td>
						<td><?php echo esc_html( implode( ', ', $issue['fields'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$this->render_section_close();

	}

	/**
	 * Render videos assigned to multiple categories section.
	 *
	 * @since    1.0.0
	 * @param    array    $issues    Section issues.
	 */
	private function render_multiple_categories_section( $issues ) {

		$this->render_section_open(
			__( 'Videos With Multiple Categories', 'one-minute-media-video-showcase' ),
			__( 'The frontend uses the lowest term ID as the display category when multiple categories are assigned.', 'one-minute-media-video-showcase' )
		);

		if ( empty( $issues ) ) {
			$this->render_no_issues();
			$this->render_section_close();
			return;
		}

		?>
		<table class="widefat striped ommvs-health-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Video', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Categories', 'one-minute-media-video-showcase' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $issues as $issue ) : ?>
					<tr>
						<td><?php $this->render_edit_link( $issue['title'], $issue['edit_url'] ); ?></td>
						<td><?php echo esc_html( implode( ', ', $issue['categories'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$this->render_section_close();

	}

	/**
	 * Render duplicate hashes section.
	 *
	 * @since    1.0.0
	 * @param    array    $issues    Section issues.
	 */
	private function render_duplicate_hashes_section( $issues ) {

		$this->render_section_open(
			__( 'Duplicate Hashes', 'one-minute-media-video-showcase' ),
			__( 'Hashes should be unique so hash-based modal opening can resolve one video on a page.', 'one-minute-media-video-showcase' )
		);

		if ( empty( $issues ) ) {
			$this->render_no_issues();
			$this->render_section_close();
			return;
		}

		?>
		<table class="widefat striped ommvs-health-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Hash', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Videos', 'one-minute-media-video-showcase' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $issues as $issue ) : ?>
					<tr>
						<td><code><?php echo esc_html( '#' . $issue['hash'] ); ?></code></td>
						<td><?php $this->render_link_list( $issue['videos'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$this->render_section_close();

	}

	/**
	 * Render placement integrity section.
	 *
	 * @since    1.0.0
	 * @param    array    $issues    Section issues.
	 */
	private function render_placement_integrity_section( $issues ) {

		$this->render_section_open(
			__( 'Placement Integrity', 'one-minute-media-video-showcase' ),
			__( 'These migrated page placements reference missing, invalid, trashed, or inactive videos.', 'one-minute-media-video-showcase' )
		);

		if ( empty( $issues ) ) {
			$this->render_no_issues();
			$this->render_section_close();
			return;
		}

		?>
		<table class="widefat striped ommvs-health-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Page', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Placement', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Video reference', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Issue', 'one-minute-media-video-showcase' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $issues as $issue ) : ?>
					<tr>
						<td><?php $this->render_edit_link( $issue['page_title'], $issue['page_url'] ); ?></td>
						<td>
							<?php
							echo esc_html(
								sprintf(
									/* translators: 1: placement group, 2: row number. */
									__( '%1$s row %2$d', 'one-minute-media-video-showcase' ),
									$issue['group'],
									(int) $issue['position']
								)
							);
							?>
						</td>
						<td><?php $this->render_edit_link( $issue['video_title'], $issue['video_url'] ); ?></td>
						<td><?php echo esc_html( $issue['issue'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$this->render_section_close();

	}

	/**
	 * Render duplicate placements section.
	 *
	 * @since    1.0.0
	 * @param    array    $issues    Section issues.
	 */
	private function render_duplicate_placements_section( $issues ) {

		$this->render_section_open(
			__( 'Duplicate Page Placements', 'one-minute-media-video-showcase' ),
			__( 'A page should not place the same video more than once across Featured Videos and More Videos.', 'one-minute-media-video-showcase' )
		);

		if ( empty( $issues ) ) {
			$this->render_no_issues();
			$this->render_section_close();
			return;
		}

		?>
		<table class="widefat striped ommvs-health-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Page', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Duplicate video', 'one-minute-media-video-showcase' ); ?></th>
					<th><?php esc_html_e( 'Placement groups', 'one-minute-media-video-showcase' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $issues as $issue ) : ?>
					<tr>
						<td><?php $this->render_edit_link( $issue['page_title'], $issue['page_url'] ); ?></td>
						<td><?php $this->render_edit_link( $issue['video_title'], $issue['video_url'] ); ?></td>
						<td><?php echo esc_html( implode( ', ', $issue['groups'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$this->render_section_close();

	}

	/**
	 * Render a health section opening wrapper.
	 *
	 * @since    1.0.0
	 * @param    string    $title          Section title.
	 * @param    string    $description    Section description.
	 */
	private function render_section_open( $title, $description ) {

		?>
		<section class="ommvs-health-section">
			<h2><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $description ); ?></p>
		<?php

	}

	/**
	 * Render a health section closing wrapper.
	 *
	 * @since    1.0.0
	 */
	private function render_section_close() {

		echo '</section>';

	}

	/**
	 * Render a no issues message.
	 *
	 * @since    1.0.0
	 */
	private function render_no_issues() {

		echo '<p class="ommvs-health-empty">' . esc_html__( 'No issues found.', 'one-minute-media-video-showcase' ) . '</p>';

	}

	/**
	 * Render an edit link, falling back to text when no URL exists.
	 *
	 * @since    1.0.0
	 * @param    string    $label    Link label.
	 * @param    string    $url      Edit URL.
	 */
	private function render_edit_link( $label, $url ) {

		if ( '' === (string) $url ) {
			echo esc_html( $label );
			return;
		}

		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			esc_html( $label )
		);

	}

	/**
	 * Render a comma-separated list of edit links.
	 *
	 * @since    1.0.0
	 * @param    array    $items    Link items with title and edit_url keys.
	 */
	private function render_link_list( $items ) {

		$links = array();

		foreach ( $items as $item ) {
			$title = isset( $item['title'] ) ? (string) $item['title'] : '';
			$url   = isset( $item['edit_url'] ) ? (string) $item['edit_url'] : '';

			if ( '' === $title ) {
				continue;
			}

			if ( '' === $url ) {
				$links[] = esc_html( $title );
				continue;
			}

			$links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $url ),
				esc_html( $title )
			);
		}

		echo wp_kses_post( implode( ', ', $links ) );

	}

	/**
	 * Get a normalized hash slug for comparison.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   string
	 */
	private function get_normalized_hash_slug( $video_id ) {

		$hash_slug = trim( sanitize_text_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_HASH_SLUG, true ) ) );

		return ltrim( $hash_slug, '#' );

	}

	/**
	 * Get Video Category terms assigned to a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   WP_Term[]
	 */
	private function get_video_category_terms( $video_id ) {

		$terms = wp_get_object_terms(
			absint( $video_id ),
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
	 * Get a readable post label for admin reports.
	 *
	 * @since    1.0.0
	 * @param    int       $post_id          Post ID.
	 * @param    string    $fallback_label   Fallback object label.
	 * @return   string
	 */
	private function get_post_admin_label( $post_id, $fallback_label ) {

		$title = get_the_title( $post_id );

		if ( '' === trim( (string) $title ) ) {
			return sprintf(
				/* translators: 1: object label, 2: post ID. */
				__( '%1$s #%2$d', 'one-minute-media-video-showcase' ),
				$fallback_label,
				(int) $post_id
			);
		}

		return sprintf(
			/* translators: 1: post title, 2: post ID. */
			__( '%1$s (#%2$d)', 'one-minute-media-video-showcase' ),
			$title,
			(int) $post_id
		);

	}

	/**
	 * Determine whether an attachment ID is valid.
	 *
	 * @since    1.0.0
	 * @param    int    $attachment_id    Attachment post ID.
	 * @return   bool
	 */
	private function is_valid_attachment( $attachment_id ) {

		return absint( $attachment_id ) > 0 && 'attachment' === get_post_type( $attachment_id );

	}

	/**
	 * Determine whether a Video Case Study is active.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   bool
	 */
	private function is_video_active( $video_id ) {

		$active = get_post_meta( absint( $video_id ), OMMVS_Fields::FIELD_IS_ACTIVE, true );

		return '' === $active || '0' !== (string) $active;

	}

}

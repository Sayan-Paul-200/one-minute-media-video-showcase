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

	/**
	 * Register the video_case_study custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {

		$post_type = 'video_case_study';

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

		remove_meta_box( 'slugdiv', 'video_case_study', 'normal' );

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

		foreach ( $columns as $key => $label ) {
			$updated_columns[ $key ] = $label;

			if ( 'cb' === $key ) {
				$updated_columns['ommvs_thumbnail'] = __( 'Thumbnail', 'one-minute-media-video-showcase' );
			}

			if ( 'title' === $key ) {
				$updated_columns['ommvs_hash_slug']      = __( 'Hash slug', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_active_status']  = __( 'Active status', 'one-minute-media-video-showcase' );
				$updated_columns['ommvs_video_provider'] = __( 'Video provider', 'one-minute-media-video-showcase' );
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

			case 'ommvs_video_provider':
				$this->render_video_provider_column( $post_id );
				break;
		}

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

		$hash_slug = trim( sanitize_text_field( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_HASH_SLUG, true ) ) );
		$hash_slug = ltrim( $hash_slug, '#' );

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
	 * Render the video provider column.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Current Video Case Study post ID.
	 */
	private function render_video_provider_column( $post_id ) {

		$provider = sanitize_key( (string) get_post_meta( $post_id, OMMVS_Fields::FIELD_VIDEO_PROVIDER, true ) );
		$labels   = array(
			'vimeo'   => __( 'Vimeo', 'one-minute-media-video-showcase' ),
			'youtube' => __( 'YouTube', 'one-minute-media-video-showcase' ),
			'url'     => __( 'Direct URL', 'one-minute-media-video-showcase' ),
		);

		if ( '' === $provider ) {
			$this->render_status_badge( __( 'Missing', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		if ( ! isset( $labels[ $provider ] ) ) {
			$this->render_status_badge( __( 'Unknown', 'one-minute-media-video-showcase' ), 'warning' );
			return;
		}

		echo esc_html( $labels[ $provider ] );

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

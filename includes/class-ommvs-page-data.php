<?php

/**
 * Page video data builder.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Build JSON-ready video data for a page.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Page_Data {

	const RELATED_LIMIT = 3;

	/**
	 * Build page data for video grids and the shared modal.
	 *
	 * @since    1.0.0
	 * @param    int    $page_id    Page post ID.
	 * @return   array
	 */
	public static function get_page_data( int $page_id ): array {

		$page_id = absint( $page_id );
		$data    = self::get_empty_data( $page_id );

		if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) {
			return $data;
		}

		$seen_video_ids = array();
		$featured_ids   = self::get_placement_video_ids(
			$page_id,
			OMMVS_Fields::META_FEATURED_VIDEOS,
			0,
			$seen_video_ids
		);
		$more_ids       = self::get_placement_video_ids(
			$page_id,
			OMMVS_Fields::META_MORE_VIDEOS,
			0,
			$seen_video_ids
		);
		$video_ids      = array_merge( $featured_ids, $more_ids );
		$videos         = array();
		$hash_map       = array();

		foreach ( $video_ids as $video_id ) {
			$video = self::get_video_data( $video_id );

			if ( empty( $video ) ) {
				continue;
			}

			$videos[ (string) $video_id ] = $video;

			if ( '' !== $video['hash'] && ! isset( $hash_map[ $video['hash'] ] ) ) {
				$hash_map[ $video['hash'] ] = $video_id;
			}
		}

		return array(
			'pageId'      => $page_id,
			'featuredIds' => array_values( $featured_ids ),
			'moreIds'     => array_values( $more_ids ),
			'videos'      => $videos,
			'hashMap'     => $hash_map,
			'relatedMap'  => self::get_related_map( array_keys( $videos ), $featured_ids ),
			'settings'    => $data['settings'],
		);

	}

	/**
	 * Get a safe empty page-data structure.
	 *
	 * @since    1.0.0
	 * @param    int    $page_id    Page post ID.
	 * @return   array
	 */
	private static function get_empty_data( $page_id ): array {

		return array(
			'pageId'      => absint( $page_id ),
			'featuredIds' => array(),
			'moreIds'     => array(),
			'videos'      => array(),
			'hashMap'     => array(),
			'relatedMap'  => array(),
			'settings'    => self::get_settings_data(),
		);

	}

	/**
	 * Read ordered placement video IDs from a page meta value.
	 *
	 * @since    1.0.0
	 * @param    int       $page_id          Page post ID.
	 * @param    string    $meta_key         Placement meta key.
	 * @param    int       $limit            Maximum accepted IDs. Zero means no limit.
	 * @param    array     $seen_video_ids   Video IDs already accepted across placement groups.
	 * @return   array
	 */
	private static function get_placement_video_ids( $page_id, $meta_key, $limit, &$seen_video_ids ): array {

		$placements = get_post_meta( $page_id, $meta_key, true );

		if ( ! is_array( $placements ) ) {
			return array();
		}

		$video_ids = array();
		$limit     = max( 0, absint( $limit ) );

		foreach ( $placements as $placement ) {
			$video_id = is_array( $placement )
				? absint( $placement[ OMMVS_Fields::PLACEMENT_VIDEO ] ?? 0 )
				: absint( $placement );

			if ( ! $video_id || isset( $seen_video_ids[ $video_id ] ) || ! self::is_frontend_video( $video_id ) ) {
				continue;
			}

			$video_ids[]                  = $video_id;
			$seen_video_ids[ $video_id ] = true;

			if ( $limit > 0 && count( $video_ids ) >= $limit ) {
				break;
			}
		}

		return $video_ids;

	}

	/**
	 * Build normalized data for one Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   array
	 */
	private static function get_video_data( $video_id ): array {

		if ( ! self::is_frontend_video( $video_id ) ) {
			return array();
		}

		$card_thumbnail_id    = absint( get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_THUMBNAIL, true ) );
		$related_thumbnail_id = absint( get_post_meta( $video_id, OMMVS_Fields::FIELD_RELATED_THUMBNAIL, true ) );
		$modal_content        = wp_kses_post( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_MODAL_CONTENT, true ) );
		$stored_video_url     = esc_url_raw( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_VIDEO_URL, true ) );
		$vimeo_url            = OMMVS_Fields::is_valid_vimeo_url( $stored_video_url ) ? $stored_video_url : '';
		$vimeo_id             = OMMVS_Fields::get_vimeo_video_id_from_url( $vimeo_url );
		$legacy_provider      = self::get_video_provider( $video_id );
		$legacy_video_id      = sanitize_text_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_VIDEO_ID, true ) );
		$compat_provider      = '' !== $vimeo_id ? 'vimeo' : $legacy_provider;
		$compat_video_id      = '' !== $vimeo_id ? $vimeo_id : $legacy_video_id;
		$compat_video_url     = '' !== $vimeo_url ? $vimeo_url : $stored_video_url;

		if ( ! $related_thumbnail_id ) {
			$related_thumbnail_id = $card_thumbnail_id;
		}

		return array(
			'id'          => absint( $video_id ),
			'hash'        => self::get_hash_slug( $video_id ),
			'category'    => self::get_video_category( $video_id ),
			'card'        => array(
				'title'       => sanitize_text_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_TITLE, true ) ),
				'description' => sanitize_textarea_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_CARD_DESCRIPTION, true ) ),
				'thumbnail'   => self::get_attachment_data( $card_thumbnail_id, 'large' ),
			),
			'modal'       => array(
				'title'    => sanitize_text_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_MODAL_TITLE, true ) ),
				'content'  => $modal_content,
				'overview' => $modal_content,
				'vimeoUrl' => $vimeo_url,
				'vimeoId'  => $vimeo_id,
				'provider' => $compat_provider,
				'videoId'  => $compat_video_id,
				'videoUrl' => $compat_video_url,
			),
			'relatedCard' => array(
				'thumbnail' => self::get_attachment_data( $related_thumbnail_id, 'medium' ),
			),
		);

	}

	/**
	 * Get the display category for a Video Case Study.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   array
	 */
	private static function get_video_category( $video_id ): array {

		$terms = wp_get_object_terms(
			absint( $video_id ),
			self::get_video_category_taxonomy(),
			array(
				'fields' => 'all',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return self::get_empty_category_data();
		}

		usort(
			$terms,
			static function ( $first_term, $second_term ) {
				return absint( $first_term->term_id ?? 0 ) <=> absint( $second_term->term_id ?? 0 );
			}
		);

		$term = reset( $terms );

		if ( ! $term instanceof WP_Term ) {
			return self::get_empty_category_data();
		}

		return array(
			'id'   => absint( $term->term_id ),
			'name' => sanitize_text_field( (string) $term->name ),
			'slug' => sanitize_title( (string) $term->slug ),
		);

	}

	/**
	 * Get an empty category data shape.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private static function get_empty_category_data(): array {

		return array(
			'id'   => 0,
			'name' => '',
			'slug' => '',
		);

	}

	/**
	 * Get the Video Category taxonomy slug.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private static function get_video_category_taxonomy() {

		return class_exists( 'OMMVS_Taxonomy_Video_Category' )
			? OMMVS_Taxonomy_Video_Category::TAXONOMY
			: 'ommvs_video_category';

	}

	/**
	 * Build the related-video map for all page videos.
	 *
	 * @since    1.0.0
	 * @param    array    $video_ids       Included page video IDs.
	 * @param    array    $featured_ids    Included Featured Videos IDs.
	 * @return   array
	 */
	private static function get_related_map( array $video_ids, array $featured_ids ): array {

		$related_map = array();

		foreach ( $video_ids as $video_id ) {
			$video_id                 = absint( $video_id );
			$related_map[ $video_id ] = OMMVS_Related_Videos::get_related_ids( $featured_ids, $video_id, self::RELATED_LIMIT );
		}

		return $related_map;

	}

	/**
	 * Normalize global settings for frontend page data.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private static function get_settings_data(): array {

		$settings     = OMMVS_Settings::get_settings();
		$thumbnail_id = absint( $settings[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] ?? 0 );

		return array(
			'productionOverviewLabel' => '',
			'creativeSectionTitle'    => '',
			'creativeBullets'         => array(),
			'cta'                     => array(
				'text' => sanitize_text_field( (string) ( $settings[ OMMVS_Fields::OPTION_CTA_BUTTON_TEXT ] ?? '' ) ),
				'url'  => esc_url_raw( (string) ( $settings[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ] ?? '' ) ),
			),
			'modalFallbackThumbnail'  => self::get_attachment_data( $thumbnail_id, 'large' ),
		);

	}

	/**
	 * Get normalized attachment data.
	 *
	 * @since    1.0.0
	 * @param    int       $attachment_id    Attachment ID.
	 * @param    string    $size             Requested image size.
	 * @return   array
	 */
	private static function get_attachment_data( $attachment_id, $size ): array {

		$attachment_id = absint( $attachment_id );

		if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
			return self::get_empty_attachment_data();
		}

		$image = wp_get_attachment_image_src( $attachment_id, $size );

		if ( ! $image ) {
			return self::get_empty_attachment_data();
		}

		return array(
			'id'     => $attachment_id,
			'url'    => esc_url_raw( $image[0] ),
			'alt'    => sanitize_text_field( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ),
			'width'  => isset( $image[1] ) ? absint( $image[1] ) : 0,
			'height' => isset( $image[2] ) ? absint( $image[2] ) : 0,
		);

	}

	/**
	 * Get an empty attachment data shape.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private static function get_empty_attachment_data(): array {

		return array(
			'id'     => 0,
			'url'    => '',
			'alt'    => '',
			'width'  => 0,
			'height' => 0,
		);

	}

	/**
	 * Get a safe hash slug without a leading hash mark.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   string
	 */
	private static function get_hash_slug( $video_id ) {

		$hash_slug = sanitize_text_field( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_HASH_SLUG, true ) );
		$hash_slug = trim( $hash_slug );

		return ltrim( $hash_slug, '#' );

	}

	/**
	 * Get a safe video provider value.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   string
	 */
	private static function get_video_provider( $video_id ) {

		$provider = sanitize_key( (string) get_post_meta( $video_id, OMMVS_Fields::FIELD_VIDEO_PROVIDER, true ) );

		return in_array( $provider, array( 'vimeo', 'youtube', 'url' ), true ) ? $provider : '';

	}

	/**
	 * Determine whether a Video Case Study can be included in frontend data.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   bool
	 */
	private static function is_frontend_video( $video_id ) {

		$video_id = absint( $video_id );

		if ( ! $video_id || 'video_case_study' !== get_post_type( $video_id ) ) {
			return false;
		}

		if ( 'publish' !== get_post_status( $video_id ) ) {
			return false;
		}

		return self::is_video_active( $video_id );

	}

	/**
	 * Determine whether a Video Case Study is active.
	 *
	 * @since    1.0.0
	 * @param    int    $video_id    Video Case Study post ID.
	 * @return   bool
	 */
	private static function is_video_active( $video_id ) {

		$active = get_post_meta( $video_id, OMMVS_Fields::FIELD_IS_ACTIVE, true );

		return '' === $active || '0' !== (string) $active;

	}

}

<?php

/**
 * Related-video calculation engine.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Calculate related videos from a page's ordered Featured Videos list.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Related_Videos {

	/**
	 * Get related video IDs for the current video.
	 *
	 * Related videos always come from the current page's Featured Videos list.
	 *
	 * @since    1.0.0
	 * @param    array    $featured_ids        Ordered Featured Videos IDs for the current page.
	 * @param    int      $current_video_id    Current Video Case Study ID.
	 * @param    int      $limit               Maximum number of related IDs to return.
	 * @return   array
	 */
	public static function get_related_ids( array $featured_ids, int $current_video_id, int $limit = 3 ): array {

		$limit = max( 0, (int) $limit );

		if ( 0 === $limit ) {
			return array();
		}

		$featured_ids     = self::normalize_ids( $featured_ids );
		$current_video_id = abs( (int) $current_video_id );

		if ( empty( $featured_ids ) ) {
			return array();
		}

		$related_ids = array();

		foreach ( $featured_ids as $featured_id ) {
			if ( $featured_id === $current_video_id ) {
				continue;
			}

			$related_ids[] = $featured_id;

			if ( count( $related_ids ) >= $limit ) {
				break;
			}
		}

		return $related_ids;

	}

	/**
	 * Normalize IDs to unique positive integers while preserving first-seen order.
	 *
	 * @since    1.0.0
	 * @param    array    $ids    Raw IDs.
	 * @return   array
	 */
	private static function normalize_ids( array $ids ): array {

		$normalized = array();
		$seen       = array();

		foreach ( $ids as $id ) {
			$id = abs( (int) $id );

			if ( 0 === $id || isset( $seen[ $id ] ) ) {
				continue;
			}

			$normalized[] = $id;
			$seen[ $id ] = true;
		}

		return $normalized;

	}

}

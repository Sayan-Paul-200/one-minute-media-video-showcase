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
			'show_in_rest'       => true,
			'rewrite'            => false,
			'query_var'          => false,
		);

		register_taxonomy( self::TAXONOMY, array( self::OBJECT_TYPE ), $args );

	}

}

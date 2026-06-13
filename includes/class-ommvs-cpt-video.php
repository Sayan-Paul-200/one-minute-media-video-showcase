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

}

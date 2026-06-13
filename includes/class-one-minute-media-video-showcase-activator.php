<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class One_Minute_Media_Video_Showcase_Activator {

	/**
	 * Register rewrite-dependent structures and flush rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$plugin_dir = defined( 'OMMVS_PLUGIN_DIR' )
			? OMMVS_PLUGIN_DIR
			: plugin_dir_path( dirname( __FILE__ ) );

		require_once $plugin_dir . 'includes/class-ommvs-cpt-video.php';

		if ( class_exists( 'OMMVS_CPT_Video' ) ) {
			$cpt_video = new OMMVS_CPT_Video();
			$cpt_video->register_post_type();
		}

		if ( function_exists( 'flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
		}

	}

}

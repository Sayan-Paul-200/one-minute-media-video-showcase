<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Sayan-Paul-200
 * @since             1.0.0
 * @package           One_Minute_Media_Video_Showcase
 *
 * @wordpress-plugin
 * Plugin Name:       1 Minute Media Video Showcase
 * Plugin URI:        https://hyperweblabs.in/
 * Description:       Adds reusable video case studies, Elementor-powered video grids, page-specific related video logic, and a single dynamic video popup system with URL hash support for the 1 Minute Media website.
 * Version:           1.0.0
 * Author:            Sayan Paul
 * Author URI:        https://github.com/Sayan-Paul-200/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       one-minute-media-video-showcase
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-one-minute-media-video-showcase-activator.php
 */
function activate_one_minute_media_video_showcase() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-one-minute-media-video-showcase-activator.php';
	One_Minute_Media_Video_Showcase_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-one-minute-media-video-showcase-deactivator.php
 */
function deactivate_one_minute_media_video_showcase() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-one-minute-media-video-showcase-deactivator.php';
	One_Minute_Media_Video_Showcase_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_one_minute_media_video_showcase' );
register_deactivation_hook( __FILE__, 'deactivate_one_minute_media_video_showcase' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-one-minute-media-video-showcase.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_one_minute_media_video_showcase() {

	$plugin = new One_Minute_Media_Video_Showcase();
	$plugin->run();

}
run_one_minute_media_video_showcase();

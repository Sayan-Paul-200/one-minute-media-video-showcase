<?php

/**
 * Public asset registration for the plugin.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Public asset registration for the plugin.
 *
 * Registers frontend CSS and JavaScript so later renderers can enqueue them
 * only when an OMMVS grid or modal is present.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Assets {

	const PUBLIC_STYLE_HANDLE = 'one-minute-media-video-showcase-public';
	const PUBLIC_SCRIPT_HANDLE = 'one-minute-media-video-showcase-public';

	/**
	 * Register public-facing assets.
	 *
	 * @since    1.0.0
	 */
	public function register_public_assets() {

		self::register_public_style();
		self::register_public_script();

	}

	/**
	 * Enqueue public-facing assets.
	 *
	 * Later phases call this from the Elementor widget or modal renderer when
	 * the video showcase is actually needed on the current page.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_public_assets() {

		if ( ! wp_style_is( self::PUBLIC_STYLE_HANDLE, 'registered' ) ) {
			self::register_public_style();
		}

		if ( ! wp_script_is( self::PUBLIC_SCRIPT_HANDLE, 'registered' ) ) {
			self::register_public_script();
		}

		wp_enqueue_style( self::PUBLIC_STYLE_HANDLE );
		wp_enqueue_script( self::PUBLIC_SCRIPT_HANDLE );

	}

	/**
	 * Get the public stylesheet handle.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_public_style_handle() {

		return self::PUBLIC_STYLE_HANDLE;

	}

	/**
	 * Get the public script handle.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_public_script_handle() {

		return self::PUBLIC_SCRIPT_HANDLE;

	}

	/**
	 * Register the public stylesheet.
	 *
	 * @since    1.0.0
	 */
	private static function register_public_style() {

		wp_register_style(
			self::PUBLIC_STYLE_HANDLE,
			self::get_asset_url( 'public/css/one-minute-media-video-showcase-public.css' ),
			array(),
			self::get_version(),
			'all'
		);

	}

	/**
	 * Register the public script.
	 *
	 * @since    1.0.0
	 */
	private static function register_public_script() {

		wp_register_script(
			self::PUBLIC_SCRIPT_HANDLE,
			self::get_asset_url( 'public/js/one-minute-media-video-showcase-public.js' ),
			array( 'jquery' ),
			self::get_version(),
			true
		);

	}

	/**
	 * Build a plugin asset URL.
	 *
	 * @since    1.0.0
	 * @param    string    $relative_path    Asset path relative to the plugin root.
	 * @return   string
	 */
	private static function get_asset_url( $relative_path ) {

		$plugin_url = defined( 'OMMVS_PLUGIN_URL' )
			? OMMVS_PLUGIN_URL
			: plugin_dir_url( dirname( __DIR__ ) . '/one-minute-media-video-showcase.php' );

		return trailingslashit( $plugin_url ) . ltrim( $relative_path, '/' );

	}

	/**
	 * Get the current plugin version for asset cache busting.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private static function get_version() {

		return defined( 'OMMVS_VERSION' ) ? OMMVS_VERSION : '1.0.0';

	}

}

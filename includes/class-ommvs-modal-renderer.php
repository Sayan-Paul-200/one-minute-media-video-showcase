<?php

/**
 * Shared frontend modal renderer.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Tracks and renders the shared frontend modal infrastructure.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Modal_Renderer {

	/**
	 * Whether the modal is required for the current request.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool
	 */
	private static $required = false;

	/**
	 * Page ID associated with the current modal request.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int
	 */
	private static $page_id = 0;

	/**
	 * Whether the footer renderer has already run.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool
	 */
	private static $rendered = false;

	/**
	 * Mark the shared modal as required for a page.
	 *
	 * @since    1.0.0
	 * @param    int    $page_id    Page post ID.
	 */
	public static function mark_required( int $page_id ): void {

		$page_id = absint( $page_id );

		if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) {
			return;
		}

		self::$required = true;

		if ( ! self::$page_id ) {
			self::$page_id = $page_id;
		}

		if ( class_exists( 'OMMVS_Assets' ) ) {
			OMMVS_Assets::enqueue_public_assets();
		}

	}

	/**
	 * Determine whether the modal is required for the current request.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function is_required(): bool {

		return self::$required;

	}

	/**
	 * Render the shared modal output once.
	 *
	 * This method owns the footer gate so page JSON and modal markup remain
	 * single-instance.
	 *
	 * @since    1.0.0
	 */
	public function render(): void {

		if ( ! self::is_required() || self::$rendered || ! self::$page_id ) {
			return;
		}

		self::$rendered = true;

		$template_path = $this->get_modal_template_path();

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		$ommvs_page_id = self::$page_id;

		$this->render_page_data_json();

		include $template_path;

	}

	/**
	 * Render current-page video data as parseable JSON for the frontend.
	 *
	 * @since    1.0.0
	 */
	private function render_page_data_json(): void {

		$data = class_exists( 'OMMVS_Page_Data' )
			? OMMVS_Page_Data::get_page_data( self::$page_id )
			: array();

		$json = wp_json_encode(
			$data,
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		);

		if ( false === $json ) {
			$json = '{}';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Encoded with wp_json_encode() and JSON_HEX flags for inline JSON context.
		echo '<script type="application/json" id="ommvs-page-data">' . $json . '</script>' . "\n";

	}

	/**
	 * Get the modal template path.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_modal_template_path(): string {

		$plugin_dir = defined( 'OMMVS_PLUGIN_DIR' ) ? OMMVS_PLUGIN_DIR : trailingslashit( dirname( __DIR__ ) );

		return trailingslashit( $plugin_dir ) . 'templates/modal.php';

	}

}

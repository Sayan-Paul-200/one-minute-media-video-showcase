<?php

/**
 * Elementor integration for the plugin.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Elementor integration for the plugin.
 *
 * Registers the plugin category and prepares a safe widget registration path.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Elementor {

	const CATEGORY_SLUG = 'ommvs';
	const WIDGET_CLASS  = 'OMMVS_Widget_Video_Grid';
	const WIDGET_FILE   = 'elementor/class-ommvs-widget-video-grid.php';

	/**
	 * Register the plugin Elementor category.
	 *
	 * @since    1.0.0
	 * @param    object    $elements_manager    Elementor elements manager.
	 */
	public function register_category( $elements_manager ) {

		if ( ! is_object( $elements_manager ) || ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$elements_manager->add_category(
			self::CATEGORY_SLUG,
			array(
				'title' => __( '1 Minute Media', 'one-minute-media-video-showcase' ),
				'icon'  => 'fa fa-plug',
			)
		);

	}

	/**
	 * Register plugin Elementor widgets.
	 *
	 * The widget file is optional so the plugin can fail safely if the widget
	 * class is not available.
	 *
	 * @since    1.0.0
	 * @param    object    $widgets_manager    Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {

		if ( ! is_object( $widgets_manager ) || ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		if ( ! class_exists( self::WIDGET_CLASS ) ) {
			$widget_file = $this->get_widget_file_path();

			if ( ! is_readable( $widget_file ) ) {
				return;
			}

			require_once $widget_file;
		}

		if ( ! class_exists( self::WIDGET_CLASS ) ) {
			return;
		}

		$widget = new OMMVS_Widget_Video_Grid();

		if ( method_exists( $widgets_manager, 'register' ) ) {
			$widgets_manager->register( $widget );
			return;
		}

		if ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
			$widgets_manager->register_widget_type( $widget );
		}

	}

	/**
	 * Get the future Video Grid widget class path.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_widget_file_path() {

		return trailingslashit( __DIR__ ) . self::WIDGET_FILE;

	}

}

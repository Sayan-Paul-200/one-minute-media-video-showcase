<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/admin
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class One_Minute_Media_Video_Showcase_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 */
	public function enqueue_styles( $hook_suffix = '' ) {

		if ( ! $this->should_enqueue_plugin_admin_styles( $hook_suffix ) ) {
			return;
		}

		if ( $this->is_page_edit_screen( $hook_suffix ) ) {
			wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'vendor/select2/select2.min.css', array(), '4.1.0-rc.0', 'all' );
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/one-minute-media-video-showcase-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {

		if ( ! $this->should_enqueue_plugin_admin_scripts( $hook_suffix ) ) {
			return;
		}

		$dependencies = array( 'jquery' );

		if ( $this->is_page_edit_screen( $hook_suffix ) ) {
			wp_enqueue_script(
				$this->plugin_name . '-select2',
				plugin_dir_url( __FILE__ ) . 'vendor/select2/select2.min.js',
				array( 'jquery' ),
				'4.1.0-rc.0',
				true
			);

			$dependencies[] = 'jquery-ui-sortable';
			$dependencies[] = $this->plugin_name . '-select2';
		}

		if ( $this->is_page_edit_screen( $hook_suffix ) || $this->is_settings_screen( $hook_suffix ) || $this->is_video_case_study_fallback_edit_screen( $hook_suffix ) ) {
			wp_enqueue_media();
		}

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/one-minute-media-video-showcase-admin.js',
			$dependencies,
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'ommvsAdmin',
			array(
				'strings' => array(
					'addingCategory'          => __( 'Adding category...', 'one-minute-media-video-showcase' ),
					'cardThumbnailHint'       => __( 'Frontend video cards use this Default Card Thumbnail. Native Featured Image is optional/admin-facing.', 'one-minute-media-video-showcase' ),
					'categoryAddFailed'       => __( 'Could not add the category. Please try again.', 'one-minute-media-video-showcase' ),
					'categoryNameRequired'    => __( 'Enter a category name first.', 'one-minute-media-video-showcase' ),
					'chooseThumbnail'         => __( 'Choose Thumbnail', 'one-minute-media-video-showcase' ),
					'copyHashCopied'          => __( 'Copied', 'one-minute-media-video-showcase' ),
					'copyHashFailed'          => __( 'Could not copy', 'one-minute-media-video-showcase' ),
					'hashLeadingHashWarning'  => __( 'Store this as "%s" without the leading #. The plugin will still open #hash URLs on the frontend.', 'one-minute-media-video-showcase' ),
					'hashPreview'             => __( 'Frontend hash preview: #%s', 'one-minute-media-video-showcase' ),
					'hashPreviewEmpty'        => __( 'Enter a hash slug to preview its frontend URL hash.', 'one-minute-media-video-showcase' ),
					'modalContentHint'        => __( 'Use normal headings, paragraphs, links, and bullet lists. Avoid pasted Elementor markup.', 'one-minute-media-video-showcase' ),
					'relatedThumbnailHint'    => __( 'Optional. Related cards fall back to the Default Card Thumbnail when this is empty.', 'one-minute-media-video-showcase' ),
					'useThumbnail'            => __( 'Use Thumbnail', 'one-minute-media-video-showcase' ),
					'vimeoDetected'           => __( 'Detected Vimeo ID: %s', 'one-minute-media-video-showcase' ),
					'vimeoEmpty'              => __( 'Paste a Vimeo URL, for example https://vimeo.com/879662317.', 'one-minute-media-video-showcase' ),
					'vimeoInvalid'            => __( 'This does not look like a supported Vimeo URL.', 'one-minute-media-video-showcase' ),
					'vimeoNumericOnly'        => __( 'Paste the full Vimeo URL, not only the numeric video ID.', 'one-minute-media-video-showcase' ),
				),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'videoCategoryNonce' => class_exists( 'OMMVS_Taxonomy_Video_Category' ) ? wp_create_nonce( OMMVS_Taxonomy_Video_Category::ADD_NONCE_ACTION ) : '',
			)
		);

	}

	/**
	 * Determine whether plugin admin styles should load on the current screen.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function should_enqueue_plugin_admin_styles( $hook_suffix ) {

		return $this->is_page_edit_screen( $hook_suffix )
			|| $this->is_settings_screen( $hook_suffix )
			|| $this->is_data_health_screen( $hook_suffix )
			|| $this->is_video_case_study_list_screen( $hook_suffix )
			|| $this->is_video_case_study_edit_screen( $hook_suffix )
			|| $this->is_video_case_study_fallback_edit_screen( $hook_suffix );

	}

	/**
	 * Determine whether plugin admin scripts should load on the current screen.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function should_enqueue_plugin_admin_scripts( $hook_suffix ) {

		return $this->is_page_edit_screen( $hook_suffix )
			|| $this->is_settings_screen( $hook_suffix )
			|| $this->is_video_case_study_list_screen( $hook_suffix )
			|| $this->is_video_case_study_edit_screen( $hook_suffix )
			|| $this->is_video_case_study_fallback_edit_screen( $hook_suffix );

	}

	/**
	 * Determine whether page placement assets should load on the current screen.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_page_edit_screen( $hook_suffix ) {

		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'page' === $screen->post_type ) {
			return true;
		}

		if ( isset( $_GET['post_type'] ) && 'page' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {
			return true;
		}

		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		return $post_id && 'page' === get_post_type( $post_id );

	}

	/**
	 * Determine whether settings assets should load on the current screen.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_settings_screen( $hook_suffix ) {

		return 'settings_page_ommvs-settings' === $hook_suffix;

	}

	/**
	 * Determine whether the current screen is the Data Health admin page.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_data_health_screen( $hook_suffix ) {

		return 'video_case_study_page_ommvs-data-health' === $hook_suffix;

	}

	/**
	 * Determine whether the current screen is a Video Case Study edit screen.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_video_case_study_edit_screen( $hook_suffix ) {

		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'video_case_study' === $screen->post_type ) {
			return true;
		}

		if ( isset( $_GET['post_type'] ) && 'video_case_study' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {
			return true;
		}

		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		return $post_id && 'video_case_study' === get_post_type( $post_id );

	}

	/**
	 * Determine whether fallback Video Case Study edit assets should load.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_video_case_study_fallback_edit_screen( $hook_suffix ) {

		if ( class_exists( 'OMMVS_Fields' ) && OMMVS_Fields::is_acf_available() ) {
			return false;
		}

		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'video_case_study' === $screen->post_type ) {
			return true;
		}

		if ( isset( $_GET['post_type'] ) && 'video_case_study' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {
			return true;
		}

		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		return $post_id && 'video_case_study' === get_post_type( $post_id );

	}

	/**
	 * Determine whether the current screen is the Video Case Study list table.
	 *
	 * @since    1.0.0
	 * @param    string    $hook_suffix    The current admin page hook suffix.
	 * @return   bool
	 */
	private function is_video_case_study_list_screen( $hook_suffix ) {

		if ( 'edit.php' !== $hook_suffix ) {
			return false;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'video_case_study' === $screen->post_type && 'edit' === $screen->base ) {
			return true;
		}

		return isset( $_GET['post_type'] ) && 'video_case_study' === sanitize_key( wp_unslash( $_GET['post_type'] ) );

	}

}

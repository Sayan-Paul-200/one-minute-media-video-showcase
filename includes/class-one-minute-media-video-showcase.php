<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class One_Minute_Media_Video_Showcase {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      One_Minute_Media_Video_Showcase_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION' ) ) {
			$this->version = ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'one-minute-media-video-showcase';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_cpt_hooks();
		$this->define_field_hooks();
		$this->define_settings_hooks();
		$this->define_admin_hooks();
		$this->define_asset_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - One_Minute_Media_Video_Showcase_Loader. Orchestrates the hooks of the plugin.
	 * - One_Minute_Media_Video_Showcase_i18n. Defines internationalization functionality.
	 * - One_Minute_Media_Video_Showcase_Admin. Defines all hooks for the admin area.
	 * - One_Minute_Media_Video_Showcase_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-one-minute-media-video-showcase-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-one-minute-media-video-showcase-i18n.php';

		/**
		 * The class responsible for registering the Video Case Study custom post type.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-cpt-video.php';

		/**
		 * The class responsible for defining field keys and field-system hooks.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-fields.php';

		/**
		 * The class responsible for global modal settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-settings.php';

		/**
		 * The class responsible for calculating related videos.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-related-videos.php';

		/**
		 * The class responsible for building page video data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-page-data.php';

		/**
		 * The class responsible for registering public assets.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ommvs-assets.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-one-minute-media-video-showcase-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-one-minute-media-video-showcase-public.php';

		$this->loader = new One_Minute_Media_Video_Showcase_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the One_Minute_Media_Video_Showcase_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new One_Minute_Media_Video_Showcase_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register custom post type hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_cpt_hooks() {

		$plugin_cpt_video = new OMMVS_CPT_Video();

		$this->loader->add_action( 'init', $plugin_cpt_video, 'register_post_type' );
		$this->loader->add_action( 'add_meta_boxes_video_case_study', $plugin_cpt_video, 'remove_slug_metabox' );

	}

	/**
	 * Register field-system hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_field_hooks() {

		$plugin_fields = new OMMVS_Fields();

		$this->loader->add_action( 'acf/init', $plugin_fields, 'register_field_groups' );
		$this->loader->add_filter( 'acf/validate_value/name=' . OMMVS_Fields::FIELD_HASH_SLUG, $plugin_fields, 'validate_hash_slug_unique', 10, 4 );
		$this->loader->add_action( 'add_meta_boxes_page', $plugin_fields, 'register_page_placement_metaboxes' );
		$this->loader->add_action( 'save_post_page', $plugin_fields, 'save_page_placements', 10, 3 );
		$this->loader->add_action( 'admin_notices', $plugin_fields, 'maybe_show_missing_acf_notice' );
		$this->loader->add_action( 'admin_notices', $plugin_fields, 'maybe_show_page_placement_notices' );

	}

	/**
	 * Register global settings hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_settings_hooks() {

		$plugin_settings = new OMMVS_Settings();

		$this->loader->add_action( 'admin_menu', $plugin_settings, 'register_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new One_Minute_Media_Video_Showcase_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register frontend asset hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_asset_hooks() {

		$plugin_assets = new OMMVS_Assets();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_assets, 'register_public_assets' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/*
		 * Public assets are registered by OMMVS_Assets and enqueued only by
		 * frontend renderers, such as the Elementor widget or modal renderer.
		 */

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    One_Minute_Media_Video_Showcase_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

<?php

/**
 * Global modal settings for the plugin.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 */

/**
 * Global modal settings for CTA and fallback media.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Settings {

	const OPTION_GROUP = 'ommvs_settings_group';
	const PAGE_SLUG    = 'ommvs-settings';
	const SECTION_MAIN = 'ommvs_settings_main';

	/**
	 * Get default global modal settings.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_defaults() {

		return array(
			OMMVS_Fields::OPTION_PRODUCTION_OVERVIEW_LABEL => __( 'Production Overview', 'one-minute-media-video-showcase' ),
			OMMVS_Fields::OPTION_CREATIVE_SECTION_TITLE    => __( '1 Minute Media Creative', 'one-minute-media-video-showcase' ),
			OMMVS_Fields::OPTION_CREATIVE_BULLETS          => array(
				__( 'Pre-Production', 'one-minute-media-video-showcase' ),
				__( 'Screen Design', 'one-minute-media-video-showcase' ),
				__( 'Live Action Multi-Cam Filming', 'one-minute-media-video-showcase' ),
				__( 'Motion Graphics', 'one-minute-media-video-showcase' ),
				__( 'Editing', 'one-minute-media-video-showcase' ),
			),
			OMMVS_Fields::OPTION_CTA_BUTTON_TEXT           => __( 'Get A Quick Quote', 'one-minute-media-video-showcase' ),
			OMMVS_Fields::OPTION_CTA_BUTTON_URL            => '/quote-form/',
			OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL  => 0,
		);

	}

	/**
	 * Get saved global modal settings merged with defaults.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_settings() {

		$defaults = self::get_defaults();
		$saved    = get_option( OMMVS_Fields::OPTION_SETTINGS, array() );

		if ( ! is_array( $saved ) ) {
			return $defaults;
		}

		$settings = wp_parse_args( $saved, $defaults );

		foreach ( $defaults as $key => $default_value ) {
			if ( is_array( $default_value ) ) {
				if ( empty( $settings[ $key ] ) || ! is_array( $settings[ $key ] ) ) {
					$settings[ $key ] = $default_value;
				}

				continue;
			}

			if ( '' === $settings[ $key ] || null === $settings[ $key ] ) {
				$settings[ $key ] = $default_value;
			}
		}

		$settings[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] = absint( $settings[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] );

		return $settings;

	}

	/**
	 * Register the global settings page under Settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings_page() {

		add_options_page(
			__( '1MM Video Showcase', 'one-minute-media-video-showcase' ),
			__( '1MM Video Showcase', 'one-minute-media-video-showcase' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);

	}

	/**
	 * Register Settings API fields.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {

		register_setting(
			self::OPTION_GROUP,
			OMMVS_Fields::OPTION_SETTINGS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			self::SECTION_MAIN,
			__( 'Global Modal CTA And Fallbacks', 'one-minute-media-video-showcase' ),
			array( $this, 'render_settings_section' ),
			self::PAGE_SLUG
		);

		$this->add_settings_field(
			OMMVS_Fields::OPTION_CTA_BUTTON_TEXT,
			__( 'CTA Button Text', 'one-minute-media-video-showcase' ),
			'render_text_field'
		);

		$this->add_settings_field(
			OMMVS_Fields::OPTION_CTA_BUTTON_URL,
			__( 'CTA Button URL', 'one-minute-media-video-showcase' ),
			'render_text_field'
		);

		$this->add_settings_field(
			OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL,
			__( 'Modal Fallback Thumbnail', 'one-minute-media-video-showcase' ),
			'render_thumbnail_field'
		);

	}

	/**
	 * Render settings page shell.
	 *
	 * @since    1.0.0
	 */
	public function render_settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap ommvs-settings-page">
			<h1><?php esc_html_e( '1MM Video Showcase Settings', 'one-minute-media-video-showcase' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php

	}

	/**
	 * Render the main settings section description.
	 *
	 * @since    1.0.0
	 */
	public function render_settings_section() {

		echo '<p>' . esc_html__( 'These settings control the shared modal CTA and fallback thumbnail. Per-video body content is managed on each Video Case Study.', 'one-minute-media-video-showcase' ) . '</p>';

	}

	/**
	 * Render a text setting field.
	 *
	 * @since    1.0.0
	 * @param    array    $args    Field arguments.
	 */
	public function render_text_field( $args ) {

		$key      = $args['key'];
		$settings = self::get_settings();

		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( OMMVS_Fields::OPTION_SETTINGS . '[' . $key . ']' ); ?>"
			value="<?php echo esc_attr( $settings[ $key ] ); ?>"
		>
		<?php

	}

	/**
	 * Render the fallback thumbnail setting.
	 *
	 * @since    1.0.0
	 */
	public function render_thumbnail_field( $args = array() ) {

		unset( $args );

		$settings        = self::get_settings();
		$thumbnail_id    = absint( $settings[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] );
		$thumbnail_image = $thumbnail_id ? wp_get_attachment_image( $thumbnail_id, 'thumbnail', false, array( 'class' => 'ommvs-placement-thumbnail__image' ) ) : '';

		?>
		<div class="ommvs-placement-thumbnail ommvs-settings-thumbnail" data-ommvs-thumbnail>
			<input
				type="hidden"
				name="<?php echo esc_attr( OMMVS_Fields::OPTION_SETTINGS . '[' . OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL . ']' ); ?>"
				value="<?php echo esc_attr( $thumbnail_id ); ?>"
				data-ommvs-thumbnail-id
			>
			<div class="ommvs-placement-thumbnail__preview" data-ommvs-thumbnail-preview>
				<?php
				if ( $thumbnail_image ) {
					echo wp_kses_post( $thumbnail_image );
				}
				?>
			</div>
			<div class="ommvs-placement-thumbnail__actions">
				<button type="button" class="button button-secondary" data-ommvs-select-thumbnail>
					<?php esc_html_e( 'Choose Thumbnail', 'one-minute-media-video-showcase' ); ?>
				</button>
				<button type="button" class="button-link-delete" data-ommvs-remove-thumbnail <?php echo $thumbnail_id ? '' : 'hidden'; ?>>
					<?php esc_html_e( 'Remove', 'one-minute-media-video-showcase' ); ?>
				</button>
			</div>
		</div>
		<?php

	}

	/**
	 * Sanitize global modal settings.
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw option input.
	 * @return   array
	 */
	public function sanitize_settings( $input ) {

		$defaults = self::get_defaults();
		$input    = is_array( $input ) ? $input : array();
		$existing = get_option( OMMVS_Fields::OPTION_SETTINGS, array() );

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$sanitized = wp_parse_args( $existing, $defaults );

		$sanitized[ OMMVS_Fields::OPTION_CTA_BUTTON_TEXT ]          = $this->sanitize_text_with_default( $input, OMMVS_Fields::OPTION_CTA_BUTTON_TEXT, $defaults );
		$sanitized[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ]           = $this->sanitize_cta_url( $input, $defaults );
		$sanitized[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] = $this->sanitize_thumbnail_id( $input );

		return $sanitized;

	}

	/**
	 * Register a single settings field.
	 *
	 * @since    1.0.0
	 * @param    string    $key       Setting key.
	 * @param    string    $label     Setting label.
	 * @param    string    $callback  Render callback.
	 */
	private function add_settings_field( $key, $label, $callback ) {

		add_settings_field(
			$key,
			$label,
			array( $this, $callback ),
			self::PAGE_SLUG,
			self::SECTION_MAIN,
			array(
				'key' => $key,
			)
		);

	}

	/**
	 * Sanitize a plain text setting and fall back if it is empty.
	 *
	 * @since    1.0.0
	 * @param    array     $input       Raw option input.
	 * @param    string    $key         Setting key.
	 * @param    array     $defaults    Default settings.
	 * @return   string
	 */
	private function sanitize_text_with_default( $input, $key, $defaults ) {

		$value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : '';

		return '' !== $value ? $value : $defaults[ $key ];

	}

	/**
	 * Sanitize CTA URL while allowing relative site paths.
	 *
	 * @since    1.0.0
	 * @param    array    $input       Raw option input.
	 * @param    array    $defaults    Default settings.
	 * @return   string
	 */
	private function sanitize_cta_url( $input, $defaults ) {

		$value = isset( $input[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ] ) && is_scalar( $input[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ] )
			? trim( wp_unslash( $input[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ] ) )
			: '';

		if ( '' === $value ) {
			return $defaults[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ];
		}

		if ( 0 === strpos( $value, '/' ) && 0 !== strpos( $value, '//' ) ) {
			return esc_url_raw( $value );
		}

		$url = esc_url_raw( $value );
		$url_scheme = $url ? wp_parse_url( $url, PHP_URL_SCHEME ) : '';

		if ( $url && in_array( $url_scheme, array( 'http', 'https' ), true ) ) {
			return $url;
		}

		add_settings_error(
			OMMVS_Fields::OPTION_SETTINGS,
			'ommvs_invalid_cta_url',
			__( 'CTA Button URL must be a relative site path beginning with / or a valid http/https URL. The default URL was restored.', 'one-minute-media-video-showcase' ),
			'warning'
		);

		return $defaults[ OMMVS_Fields::OPTION_CTA_BUTTON_URL ];

	}

	/**
	 * Sanitize the fallback thumbnail setting.
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw option input.
	 * @return   int
	 */
	private function sanitize_thumbnail_id( $input ) {

		$thumbnail_id = isset( $input[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] ) ? absint( $input[ OMMVS_Fields::OPTION_MODAL_FALLBACK_THUMBNAIL ] ) : 0;

		if ( $thumbnail_id && 'attachment' !== get_post_type( $thumbnail_id ) ) {
			add_settings_error(
				OMMVS_Fields::OPTION_SETTINGS,
				'ommvs_invalid_fallback_thumbnail',
				__( 'The selected modal fallback thumbnail is not a valid attachment and was removed.', 'one-minute-media-video-showcase' ),
				'warning'
			);

			return 0;
		}

		return $thumbnail_id;

	}

}

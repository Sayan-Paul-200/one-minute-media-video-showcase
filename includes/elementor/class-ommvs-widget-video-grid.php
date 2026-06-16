<?php

/**
 * Elementor Video Grid widget.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes/elementor
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Elementor widget for rendering page-assigned videos.
 *
 * @since      1.0.0
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/includes/elementor
 * @author     Sayan Paul <sayanpaul666.ap@gmail.com>
 */
class OMMVS_Widget_Video_Grid extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_name() {

		return 'ommvs-video-grid';

	}

	/**
	 * Get widget title.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_title() {

		return __( '1MM Video Grid', 'one-minute-media-video-showcase' );

	}

	/**
	 * Get widget icon.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_icon() {

		return 'eicon-video-camera';

	}

	/**
	 * Get widget categories.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function get_categories() {

		return array( OMMVS_Elementor::CATEGORY_SLUG );

	}

	/**
	 * Get style dependencies.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function get_style_depends() {

		return class_exists( 'OMMVS_Assets' ) ? array( OMMVS_Assets::get_public_style_handle() ) : array();

	}

	/**
	 * Get script dependencies.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function get_script_depends() {

		return class_exists( 'OMMVS_Assets' ) ? array( OMMVS_Assets::get_public_script_handle() ) : array();

	}

	/**
	 * Register widget controls.
	 *
	 * @since    1.0.0
	 */
	protected function register_controls() {

		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_style_controls();

	}

	/**
	 * Render widget output.
	 *
	 * @since    1.0.0
	 */
	protected function render() {

		if ( class_exists( 'OMMVS_Assets' ) ) {
			OMMVS_Assets::enqueue_public_assets();
		}

		$settings = $this->get_settings_for_display();
		$source   = $this->get_grid_source( $settings );
		$page_id  = $this->get_current_page_id();
		$data     = class_exists( 'OMMVS_Page_Data' ) ? OMMVS_Page_Data::get_page_data( $page_id ) : array();
		$ids      = $this->get_source_ids( $data, $source );
		$videos   = isset( $data['videos'] ) && is_array( $data['videos'] ) ? $data['videos'] : array();

		$this->add_render_attribute( 'grid', 'class', 'ommvs-video-grid' );
		$this->add_render_attribute( 'grid', 'data-ommvs-video-grid', '' );
		$this->add_render_attribute( 'grid', 'data-ommvs-source', $source );

		echo '<div ' . $this->get_render_attribute_string( 'grid' ) . '>';

		if ( empty( $ids ) ) {
			$this->render_empty_message( $settings );
			echo '</div>';
			return;
		}

		foreach ( $ids as $video_id ) {
			$video_key = (string) absint( $video_id );

			if ( empty( $videos[ $video_key ] ) || ! is_array( $videos[ $video_key ] ) ) {
				continue;
			}

			$this->render_video_card( $videos[ $video_key ], $this->should_show_description( $settings ) );
		}

		echo '</div>';

	}

	/**
	 * Register content controls.
	 *
	 * @since    1.0.0
	 */
	private function register_content_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'one-minute-media-video-showcase' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'one-minute-media-video-showcase' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'featured',
				'options' => array(
					'featured' => __( 'Featured Videos', 'one-minute-media-video-showcase' ),
					'more'     => __( 'More Videos', 'one-minute-media-video-showcase' ),
				),
			)
		);

		$this->add_control(
			'show_description',
			array(
				'label'        => __( 'Show Description', 'one-minute-media-video-showcase' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'one-minute-media-video-showcase' ),
				'label_off'    => __( 'Hide', 'one-minute-media-video-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'empty_message',
			array(
				'label'       => __( 'Empty Message', 'one-minute-media-video-showcase' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'No videos selected for this page.', 'one-minute-media-video-showcase' ),
				'placeholder' => __( 'No videos selected for this page.', 'one-minute-media-video-showcase' ),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register layout controls.
	 *
	 * @since    1.0.0
	 */
	private function register_layout_controls() {

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'one-minute-media-video-showcase' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'           => __( 'Columns', 'one-minute-media-video-showcase' ),
				'type'            => Controls_Manager::SELECT,
				'default'         => '3',
				'tablet_default'  => '2',
				'mobile_default'  => '1',
				'options'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'       => array(
					'{{WRAPPER}} .ommvs-video-grid' => 'display: grid; grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register style controls.
	 *
	 * @since    1.0.0
	 */
	private function register_style_controls() {

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Grid', 'one-minute-media-video-showcase' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => __( 'Card Gap', 'one-minute-media-video-showcase' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ommvs-video-grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_border_radius',
			array(
				'label'      => __( 'Border Radius', 'one-minute-media-video-showcase' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ommvs-video-card, {{WRAPPER}} .ommvs-video-card__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Get a valid source setting.
	 *
	 * @since    1.0.0
	 * @param    array    $settings    Widget display settings.
	 * @return   string
	 */
	private function get_grid_source( array $settings ) {

		$source = isset( $settings['source'] ) ? sanitize_key( $settings['source'] ) : 'featured';

		return in_array( $source, array( 'featured', 'more' ), true ) ? $source : 'featured';

	}

	/**
	 * Determine whether descriptions should render.
	 *
	 * @since    1.0.0
	 * @param    array    $settings    Widget display settings.
	 * @return   bool
	 */
	private function should_show_description( array $settings ) {

		return ! isset( $settings['show_description'] ) || 'yes' === $settings['show_description'];

	}

	/**
	 * Get source IDs from page data.
	 *
	 * @since    1.0.0
	 * @param    array     $data      Page data.
	 * @param    string    $source    Grid source.
	 * @return   array
	 */
	private function get_source_ids( array $data, $source ) {

		$key = 'more' === $source ? 'moreIds' : 'featuredIds';

		if ( empty( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'absint', $data[ $key ] ) ) );

	}

	/**
	 * Get the current page ID for frontend/editor rendering.
	 *
	 * @since    1.0.0
	 * @return   int
	 */
	private function get_current_page_id() {

		$post_id = absint( get_the_ID() );

		if ( $post_id && 'page' === get_post_type( $post_id ) ) {
			return $post_id;
		}

		$queried_id = absint( get_queried_object_id() );

		if ( $queried_id && 'page' === get_post_type( $queried_id ) ) {
			return $queried_id;
		}

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->documents ) ) {
			$document = \Elementor\Plugin::$instance->documents->get_current();

			if ( $document && method_exists( $document, 'get_main_id' ) ) {
				$document_id = absint( $document->get_main_id() );

				if ( $document_id && 'page' === get_post_type( $document_id ) ) {
					return $document_id;
				}
			}
		}

		return 0;

	}

	/**
	 * Render the empty grid message.
	 *
	 * @since    1.0.0
	 * @param    array    $settings    Widget display settings.
	 */
	private function render_empty_message( array $settings ) {

		$message = isset( $settings['empty_message'] ) ? sanitize_text_field( $settings['empty_message'] ) : '';

		if ( '' === $message ) {
			return;
		}

		echo '<p class="ommvs-video-grid__empty">' . esc_html( $message ) . '</p>';

	}

	/**
	 * Render one video card.
	 *
	 * @since    1.0.0
	 * @param    array    $video               Normalized video data.
	 * @param    bool     $show_description    Whether to render the card description.
	 */
	private function render_video_card( array $video, $show_description ) {

		$template_path = $this->get_card_template_path();

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		$ommvs_video            = $video;
		$ommvs_show_description = (bool) $show_description;

		include $template_path;

	}

	/**
	 * Get the video card template path.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_card_template_path() {

		$plugin_dir = defined( 'OMMVS_PLUGIN_DIR' ) ? OMMVS_PLUGIN_DIR : trailingslashit( dirname( __DIR__, 2 ) );

		return trailingslashit( $plugin_dir ) . 'templates/video-card.php';

	}

}

<?php

/**
 * Shared frontend video modal template.
 *
 * @link       https://github.com/Sayan-Paul-200
 * @since      1.0.0
 *
 * @package    One_Minute_Media_Video_Showcase
 * @subpackage One_Minute_Media_Video_Showcase/templates
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div id="ommvs-video-modal" class="ommvs-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ommvs-modal-title" hidden data-ommvs-modal>
	<div class="ommvs-modal__overlay" data-ommvs-modal-overlay></div>
	<div class="ommvs-modal__dialog" role="document">
		<button type="button" class="ommvs-modal__close" aria-label="<?php esc_attr_e( 'Close', 'one-minute-media-video-showcase' ); ?>" data-ommvs-modal-close>
			<?php esc_html_e( 'Close', 'one-minute-media-video-showcase' ); ?>
		</button>

		<div class="ommvs-modal__content">
			<div class="ommvs-modal__text">
				<h2 id="ommvs-modal-title" class="ommvs-modal__title" data-ommvs-modal-title></h2>
				<h3 class="ommvs-modal__overview-label" data-ommvs-modal-overview-label></h3>
				<div class="ommvs-modal__overview" data-ommvs-modal-overview></div>
				<h3 class="ommvs-modal__creative-title" data-ommvs-modal-creative-title></h3>
				<ul class="ommvs-modal__creative-list" data-ommvs-modal-creative-list></ul>
				<a class="ommvs-modal__cta" href="#" data-ommvs-modal-cta></a>
			</div>

			<div class="ommvs-modal__media">
				<div class="ommvs-modal__video-container" data-ommvs-modal-video></div>
				<div class="ommvs-modal__related">
					<h3 class="ommvs-modal__related-title" data-ommvs-modal-related-title>
						<?php esc_html_e( 'Related Videos', 'one-minute-media-video-showcase' ); ?>
					</h3>
					<div class="ommvs-modal__related-list" data-ommvs-modal-related></div>
				</div>
			</div>
		</div>
	</div>
</div>

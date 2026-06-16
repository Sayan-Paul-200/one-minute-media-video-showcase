<?php

/**
 * Video card template.
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

$ommvs_video            = isset( $ommvs_video ) && is_array( $ommvs_video ) ? $ommvs_video : array();
$ommvs_show_description = isset( $ommvs_show_description ) ? (bool) $ommvs_show_description : true;

$ommvs_video_id    = absint( $ommvs_video['id'] ?? 0 );
$ommvs_hash        = isset( $ommvs_video['hash'] ) ? ltrim( sanitize_text_field( (string) $ommvs_video['hash'] ), '#' ) : '';
$ommvs_card        = isset( $ommvs_video['card'] ) && is_array( $ommvs_video['card'] ) ? $ommvs_video['card'] : array();
$ommvs_title       = isset( $ommvs_card['title'] ) ? sanitize_text_field( (string) $ommvs_card['title'] ) : '';
$ommvs_description = isset( $ommvs_card['description'] ) ? sanitize_textarea_field( (string) $ommvs_card['description'] ) : '';
$ommvs_thumbnail   = isset( $ommvs_card['thumbnail'] ) && is_array( $ommvs_card['thumbnail'] ) ? $ommvs_card['thumbnail'] : array();
$ommvs_href        = '' !== $ommvs_hash ? '#' . rawurlencode( $ommvs_hash ) : '#';

if ( '' === $ommvs_title && $ommvs_video_id ) {
	$ommvs_title = get_the_title( $ommvs_video_id );
}

$ommvs_image_url = isset( $ommvs_thumbnail['url'] ) ? esc_url( $ommvs_thumbnail['url'] ) : '';
$ommvs_image_alt = isset( $ommvs_thumbnail['alt'] ) && '' !== $ommvs_thumbnail['alt']
	? sanitize_text_field( (string) $ommvs_thumbnail['alt'] )
	: $ommvs_title;
?>
<a href="<?php echo esc_url( $ommvs_href ); ?>" class="ommvs-video-card" data-video-id="<?php echo esc_attr( $ommvs_video_id ); ?>" data-video-hash="<?php echo esc_attr( $ommvs_hash ); ?>">
	<span class="ommvs-video-card__media">
		<?php if ( '' !== $ommvs_image_url ) : ?>
			<img class="ommvs-video-card__image" src="<?php echo esc_url( $ommvs_image_url ); ?>" alt="<?php echo esc_attr( $ommvs_image_alt ); ?>" loading="lazy">
		<?php else : ?>
			<span class="ommvs-video-card__image-placeholder" aria-hidden="true"></span>
		<?php endif; ?>
		<span class="ommvs-video-card__play" aria-hidden="true"></span>
	</span>
	<span class="ommvs-video-card__body">
		<span class="ommvs-video-card__title"><?php echo esc_html( $ommvs_title ); ?></span>
		<?php if ( $ommvs_show_description && '' !== $ommvs_description ) : ?>
			<span class="ommvs-video-card__description"><?php echo esc_html( $ommvs_description ); ?></span>
		<?php endif; ?>
	</span>
</a>

<?php
/**
 * Block template for Image & Text.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$eyebrow         = $attributes['eyebrow'] ?? '';
$heading         = $attributes['heading'] ?? '';
$body            = $attributes['body'] ?? '';
$checklist       = cbae_lines( $attributes['checklist'] ?? '' );
$checklist_label = $attributes['checklistLabel'] ?? '';
$image_id        = absint( $attributes['imageId'] ?? 0 );
$image_style     = in_array( $attributes['imageStyle'] ?? '', array( 'circle', 'photo' ), true ) ? $attributes['imageStyle'] : 'circle';
$image_position  = 'left' === ( $attributes['imagePosition'] ?? '' ) ? 'left' : 'right';
$primary_text    = $attributes['primaryText'] ?? '';
$primary_url     = $attributes['primaryUrl'] ?? '';
$secondary_text  = $attributes['secondaryText'] ?? '';
$secondary_url   = $attributes['secondaryUrl'] ?? '';

if ( ! $heading && ! trim( wp_strip_all_tags( $body ) ) ) {
	return;
}

$section_id = cbae_section_id( $attributes, 'cb-image-text' );
$classes    = cbae_section_class(
	$attributes,
	'cb-image-text',
	array(
		'cb-image-text--' . $image_style,
		'cb-image-text--image-' . $image_position,
	)
);

$image_html = $image_id ? wp_get_attachment_image( $image_id, 'large', false, array( 'loading' => 'lazy' ) ) : '';

ob_start();
?>
<div class="col-12 col-lg-6 cb-image-text__media-col">
	<div class="cb-image-text__media cb-image-text__media--<?php echo esc_attr( $image_style ); ?>">
		<?php
		if ( 'circle' === $image_style ) {
			$dot_count = 28;
			?>
		<svg class="cb-image-text__dots" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
			<?php
			for ( $i = 0; $i < $dot_count; $i++ ) {
				$angle = ( 2 * M_PI * $i ) / $dot_count;
				$cx    = round( 50 + 48 * cos( $angle ), 2 );
				$cy    = round( 50 + 48 * sin( $angle ), 2 );
				?>
			<circle cx="<?php echo esc_attr( $cx ); ?>" cy="<?php echo esc_attr( $cy ); ?>" r="1.1" fill="currentColor" />
				<?php
			}
			?>
		</svg>
			<?php
		}

		if ( $image_html ) {
			?>
		<figure class="cb-image-text__fig"><?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?></figure>
			<?php
		} else {
			?>
		<div class="cb-image-text__placeholder" aria-hidden="true"></div>
			<?php
		}
		?>
	</div>
</div>
<?php
$media_column = ob_get_clean();
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<div class="row align-items-center gap-5">
			<?php
			if ( 'left' === $image_position ) {
				echo $media_column; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			}
			?>
			<div class="col-12 col-lg-6 cb-image-text__copy">
				<?php
				if ( $eyebrow ) {
					?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php
				}

				if ( $heading ) {
					?>
				<h2><?php echo esc_html( $heading ); ?></h2>
					<?php
				}

				if ( $body ) {
					?>
				<div class="cb-prose"><?php echo wp_kses_post( $body ); ?></div>
					<?php
				}

				if ( $checklist ) {
					if ( $checklist_label ) {
						?>
				<span class="eyebrow cb-image-text__list-label"><?php echo esc_html( $checklist_label ); ?></span>
						<?php
					}
					?>
				<ul class="cb-check-list">
					<?php
					foreach ( $checklist as $item ) {
						?>
					<li><span class="cb-check"><?php echo cbae_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?></span><span><?php echo esc_html( $item ); ?></span></li>
						<?php
					}
					?>
				</ul>
					<?php
				}

				if ( $primary_url || $secondary_url ) {
					?>
				<div class="cb-image-text__actions">
					<?php
					echo cbae_button( $primary_text, $primary_url, 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cbae_button().
					echo cbae_button( $secondary_text, $secondary_url, 'btn btn-ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
					<?php
				}
				?>
			</div>
			<?php
			if ( 'left' !== $image_position ) {
				echo $media_column; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			}
			?>
		</div>
	</div>
</section>

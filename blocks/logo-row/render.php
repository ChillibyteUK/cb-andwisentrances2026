<?php
/**
 * Block template for Logo Row.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading     = $attributes['heading'] ?? '';
$intro       = $attributes['intro'] ?? '';
$size        = in_array( $attributes['size'] ?? '', array( 'sm', 'md', 'lg' ), true ) ? $attributes['size'] : 'md';
$show_labels = ! empty( $attributes['showLabels'] );
$image_id    = absint( $attributes['imageId'] ?? 0 );
$rows        = array();

foreach ( (array) ( $attributes['logos'] ?? array() ) as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$row_image = absint( $row['image'] ?? 0 );
	$row_label = trim( (string) ( $row['label'] ?? '' ) );

	if ( ! $row_image && '' === $row_label ) {
		continue;
	}

	$rows[] = array(
		'image' => $row_image,
		'label' => $row_label,
		'link'  => trim( (string) ( $row['link'] ?? '' ) ),
	);
}

if ( ! $rows ) {
	return;
}

// Side image: the compliance layout from andwis lifts - copy and logos on the
// left, the photo filling the right half of the band behind a curved edge.
$image_html = $image_id ? wp_get_attachment_image(
	$image_id,
	'full',
	false,
	array(
		'loading' => 'lazy',
		'sizes'   => '(min-width: 992px) 50vw, 100vw',
	)
) : '';

$extra_classes = array( 'cb-logo-row--' . $size );
if ( $image_html ) {
	$extra_classes[] = 'cb-logo-row--has-image';
}

$section_id = cbae_section_id( $attributes, 'cb-logo-row' );
$classes    = cbae_section_class( $attributes, 'cb-logo-row', $extra_classes );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<?php
	if ( $image_html ) {
		?>
	<div class="cb-logo-row__media">
		<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
		<svg class="cb-logo-row__curve" viewBox="0 0 66 1440" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 1440C39.83 1283.48 66 1019.4 66 720S39.83 156.52 0 0z" fill="currentColor" /></svg>
	</div>
		<?php
	}
	?>
	<div class="container">
		<div class="cb-logo-row__copy">
		<?php
		if ( $heading || $intro ) {
			?>
		<div class="cb-section-head<?php echo $image_html ? ' cb-section-head--start' : ''; ?>">
			<?php
			if ( $heading ) {
				?>
			<h2><?php echo esc_html( $heading ); ?></h2>
				<?php
			}

			if ( $intro ) {
				?>
			<p><?php echo esc_html( $intro ); ?></p>
				<?php
			}
			?>
		</div>
			<?php
		}
		?>
		<ul class="cb-logo-row__list">
			<?php
			foreach ( $rows as $row ) {
				$logo_html = '';

				if ( $row['image'] ) {
					$logo_html = wp_get_attachment_image(
						$row['image'],
						'medium',
						false,
						array(
							'alt'     => $row['label'],
							'loading' => 'lazy',
						)
					);
				}

				if ( ! $logo_html ) {
					$logo_html = '<span class="cb-logo-row__fallback">' . esc_html( $row['label'] ) . '</span>';
				}
				?>
			<li class="cb-logo-row__item">
				<?php
				if ( $row['link'] ) {
					?>
				<a class="cb-logo-row__link" href="<?php echo esc_url( $row['link'] ); ?>" target="_blank" rel="noopener"<?php echo $row['label'] ? ' aria-label="' . esc_attr( $row['label'] ) . '"' : ''; ?>><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output or escaped above. ?></a>
					<?php
				} else {
					?>
				<span class="cb-logo-row__link"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output or escaped above. ?></span>
					<?php
				}

				if ( $show_labels && $row['label'] ) {
					?>
				<span class="cb-logo-row__label"><?php echo esc_html( $row['label'] ); ?></span>
					<?php
				}
				?>
			</li>
				<?php
			}
			?>
		</ul>
		</div>
	</div>
</section>

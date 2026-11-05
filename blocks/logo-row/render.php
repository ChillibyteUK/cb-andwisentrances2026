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

$section_id = cbae_section_id( $attributes, 'cb-logo-row' );
$classes    = cbae_section_class( $attributes, 'cb-logo-row', array( 'cb-logo-row--' . $size ) );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<?php
		if ( $heading || $intro ) {
			?>
		<div class="cb-section-head">
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
</section>

<?php
/**
 * Block template for Logo Flow.
 *
 * Ported from the andwis lifts "CB Logo Flow" block: boxed legacy logos, an
 * arrow, then the brand they have become (an uploaded logo, or the site
 * logo when none is chosen). src/js/logo-flow.js brings the boxes in one
 * after another when the row scrolls into view; --i is each box's place in
 * that sequence and --n the number of boxes before the result.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading     = trim( (string) ( $attributes['heading'] ?? '' ) );
$intro       = trim( (string) ( $attributes['intro'] ?? '' ) );
$show_result = ! isset( $attributes['showResult'] ) || ! empty( $attributes['showResult'] );
$result_id   = absint( $attributes['resultId'] ?? 0 );
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
	);
}

if ( ! $rows && ! $show_result && ! $heading && ! $intro ) {
	return;
}

$result_html = '';

if ( $show_result ) {
	if ( $result_id ) {
		$result_html = wp_get_attachment_image( $result_id, 'medium', false, array( 'loading' => 'lazy' ) );
	}

	if ( ! $result_html ) {
		$result_html = cbae_logo( 'header' );
	}
}

$section_id = cbae_section_id( $attributes, 'cb-logo-flow' );
$classes    = cbae_section_class( $attributes, 'cb-logo-flow' );
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
		if ( $rows || $result_html ) {
			?>
		<div class="cb-logo-flow__flow" data-logo-flow style="--n:<?php echo esc_attr( count( $rows ) ); ?>;">
			<?php
			foreach ( $rows as $index => $row ) {
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
					$logo_html = '<span class="cb-logo-flow__fallback">' . esc_html( $row['label'] ) . '</span>';
				}
				?>
			<div class="cb-logo-flow__legacy" style="--i:<?php echo esc_attr( $index ); ?>;"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output or escaped above. ?></div>
				<?php
			}
			if ( $result_html ) {
				// The arrow and the result wrap to the next line together.
				?>
			<div class="cb-logo-flow__to">
				<?php
				if ( $rows ) {
					?>
				<svg class="cb-logo-flow__arrow" aria-hidden="true" viewBox="0 0 20 20" width="1em" height="1em" fill="currentColor"><polygon points="4,2 4,18 18,10"/></svg>
					<?php
				}
				?>
				<div class="cb-logo-flow__result"><?php echo $result_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output or the theme's own logo markup. ?></div>
			</div>
				<?php
			}
			?>
		</div>
			<?php
		}
		?>
	</div>
</section>

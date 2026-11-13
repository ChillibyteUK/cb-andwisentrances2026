<?php
/**
 * Block template for Content section.
 *
 * Text-only section: eyebrow, heading, body, an optional list (ticks,
 * numbered steps or plain rows) and buttons. "split" puts the heading in a
 * left column from the lg breakpoint; "stack" is a single measure-width column.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$eyebrow        = $attributes['eyebrow'] ?? '';
$heading        = $attributes['heading'] ?? '';
$body           = $attributes['body'] ?? '';
$items          = cbae_lines( $attributes['list'] ?? '' );
$list_style     = in_array( $attributes['listStyle'] ?? '', array( 'check', 'number', 'plain' ), true ) ? $attributes['listStyle'] : 'check';
$list_label     = $attributes['listLabel'] ?? '';
$primary_text   = $attributes['primaryText'] ?? '';
$primary_url    = $attributes['primaryUrl'] ?? '';
$secondary_text = $attributes['secondaryText'] ?? '';
$secondary_url  = $attributes['secondaryUrl'] ?? '';
$layout         = 'stack' === ( $attributes['layout'] ?? '' ) ? 'stack' : 'split';
$align          = 'center' === ( $attributes['align'] ?? '' ) ? 'center' : 'start';
$background     = in_array( $attributes['background'] ?? '', array( 'paper', 'sage-tint', 'sage', 'forest', 'mint' ), true ) ? $attributes['background'] : 'paper';
$has_body       = '' !== trim( wp_strip_all_tags( $body ) );

if ( ! $heading && ! $has_body && ! $items ) {
	return;
}

$attributes['background'] = $background;

$section_id = cbae_section_id( $attributes, 'cb-content' );
$classes    = cbae_section_class(
	$attributes,
	'cb-content',
	array(
		'cb-content--' . $layout,
		'cb-content--align-' . $align,
	)
);

// A mint pill is invisible on a mint background; use the forest fill there.
$primary_class = 'mint' === $background ? 'btn btn-forest' : 'btn';
$has_actions   = $primary_url || $secondary_url;
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<div class="cb-content__inner">
			<?php
			if ( $eyebrow || $heading ) {
				?>
			<div class="cb-content__head">
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
				?>
			</div>
				<?php
			}

			if ( $has_body || $items || $has_actions ) {
				?>
			<div class="cb-content__body">
				<?php
				if ( $has_body ) {
					?>
				<div class="cb-prose"><?php echo wp_kses_post( $body ); ?></div>
					<?php
				}

				if ( $items ) {
					if ( $list_label ) {
						?>
				<span class="eyebrow cb-content__list-label"><?php echo esc_html( $list_label ); ?></span>
						<?php
					}

					if ( 'number' === $list_style ) {
						?>
				<ol class="cb-steps" role="list">
						<?php
						foreach ( $items as $item ) {
							?>
					<li><span><?php echo esc_html( $item ); ?></span></li>
							<?php
						}
						?>
				</ol>
						<?php
					} elseif ( 'plain' === $list_style ) {
						?>
				<ul class="cb-plain-list">
						<?php
						foreach ( $items as $item ) {
							?>
					<li><?php echo esc_html( $item ); ?></li>
							<?php
						}
						?>
				</ul>
						<?php
					} else {
						?>
				<ul class="cb-check-list">
						<?php
						foreach ( $items as $item ) {
							?>
					<li><span class="cb-check"><?php echo cbae_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?></span><span><?php echo esc_html( $item ); ?></span></li>
							<?php
						}
						?>
				</ul>
						<?php
					}
				}

				if ( $has_actions ) {
					?>
				<div class="cb-content__actions">
					<?php
					echo cbae_button( $primary_text, $primary_url, $primary_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cbae_button().
					echo cbae_button( $secondary_text, $secondary_url, 'btn btn-ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
					<?php
				}
				?>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>

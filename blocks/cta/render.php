<?php
/**
 * Block template for CTA Band.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading       = $attributes['heading'] ?? '';
$body          = $attributes['body'] ?? '';
$use_site_ctas = ! isset( $attributes['useSiteCtas'] ) || $attributes['useSiteCtas'];
$layout        = $attributes['layout'] ?? 'split';

if ( $use_site_ctas ) {
	$ctas      = cbae_site_ctas();
	$primary   = $ctas['quote'];
	$secondary = $ctas['survey'];
} else {
	$primary   = array(
		'text' => $attributes['primaryText'] ?? '',
		'url'  => $attributes['primaryUrl'] ?? '',
	);
	$secondary = array(
		'text' => $attributes['secondaryText'] ?? '',
		'url'  => $attributes['secondaryUrl'] ?? '',
	);
}

if ( ! $heading || ! $primary['url'] ) {
	return;
}

$section_id = cbae_section_id( $attributes, 'cb-cta' );
$classes    = cbae_section_class( $attributes, 'cb-cta', array( 'cb-cta--' . $layout ) );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container cb-cta__inner">
		<div class="cb-cta__copy">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<?php
			if ( $body ) {
				?>
			<div class="cb-cta__body"><?php echo wp_kses_post( $body ); ?></div>
				<?php
			}
			?>
		</div>
		<div class="cb-cta__actions">
			<?php
			echo cbae_button( $primary['text'], $primary['url'], 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cbae_button().
			echo cbae_button( $secondary['text'], $secondary['url'], 'btn btn-ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
</section>

<?php
/**
 * Block template for Form.
 *
 * Gravity Forms wrapper section: heading, intro and a reassurance note
 * beside (split) or above (stack) the form in a white card. The need /
 * product / building presets are passed to Gravity Forms as field_values,
 * so they pre-select the fields whose dynamic population parameter names
 * match.
 *
 * The section class must stay `cb-form`: the Gravity Forms theming in
 * src/css/forms.css is scoped to `.cb-form .gform_wrapper`.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) ( $attributes['heading'] ?? '' ) );
$intro   = trim( (string) ( $attributes['intro'] ?? '' ) );
$note    = trim( (string) ( $attributes['note'] ?? '' ) );
$form_id = absint( $attributes['formId'] ?? 0 );
$layout  = 'stack' === ( $attributes['layout'] ?? '' ) ? 'stack' : 'split';

if ( ! $heading && ! $form_id ) {
	return;
}

if ( ! in_array( $attributes['background'] ?? '', array( 'paper', 'sage-tint', 'sage' ), true ) ) {
	$attributes['background'] = 'sage-tint';
}

$prefill = array_filter(
	array(
		'need'     => trim( (string) ( $attributes['need'] ?? '' ) ),
		'product'  => trim( (string) ( $attributes['product'] ?? '' ) ),
		'building' => trim( (string) ( $attributes['building'] ?? '' ) ),
	)
);

// Buffered so the card is dropped when there is nothing to put in it (no
// form chosen, or Gravity Forms inactive, for a visitor who cannot edit).
ob_start();
cbae_render_form( $form_id, $prefill );
$form_html = trim( ob_get_clean() );

$has_copy = $heading || $intro || $note;
$is_split = 'split' === $layout && $has_copy && '' !== $form_html;
$extra    = array( 'cb-form--' . $layout );

if ( ! $has_copy ) {
	$extra[] = 'cb-form--no-copy';
}

$section_id = cbae_section_id( $attributes, 'cb-form' );
$classes    = cbae_section_class( $attributes, 'cb-form', $extra );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<div class="<?php echo esc_attr( $is_split ? 'row gap-5 align-items-start cb-form__row' : 'cb-form__row' ); ?>">
			<?php
			if ( $has_copy ) {
				?>
			<div class="<?php echo esc_attr( $is_split ? 'col-12 col-lg-5 cb-form__copy' : 'cb-form__copy' ); ?>">
				<?php
				if ( $heading ) {
					?>
				<h2><?php echo esc_html( $heading ); ?></h2>
					<?php
				}

				if ( $intro ) {
					?>
				<p class="cb-form__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p>
					<?php
				}

				if ( $note ) {
					?>
				<p class="cb-form__note"><?php echo esc_html( $note ); ?></p>
					<?php
				}
				?>
			</div>
				<?php
			}

			if ( '' !== $form_html ) {
				?>
			<div class="<?php echo esc_attr( $is_split ? 'col-12 col-lg-7 cb-form__main' : 'cb-form__main' ); ?>">
				<div class="cb-form__card">
					<?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Gravity Forms markup, or the escaped placeholder from cbae_render_form(). ?>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>

<?php
/**
 * Block template for Stats.
 *
 * Each value is split into prefix / digits / suffix so src/js/counter.js can
 * count the digits up on scroll ("300+" renders as data-count="300"
 * data-suffix="+"). A value with no digits renders as plain text.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading = $attributes['heading'] ?? '';
$intro   = $attributes['intro'] ?? '';
$rows    = array();

foreach ( (array) ( $attributes['stats'] ?? array() ) as $row ) {
	$value = trim( (string) ( $row['value'] ?? '' ) );

	if ( '' === $value ) {
		continue;
	}

	$rows[] = array(
		'value' => $value,
		'label' => trim( (string) ( $row['label'] ?? '' ) ),
	);
}

if ( ! $rows ) {
	return;
}

$section_id = cbae_section_id( $attributes, 'cb-stats' );
$classes    = cbae_section_class( $attributes, 'cb-stats' );
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
		<div class="cb-stats__grid" data-stagger>
			<?php
			foreach ( $rows as $row ) {
				$count_attrs = '';

				if ( preg_match( '/^(\D*)(\d[\d,.]*)(.*)$/u', $row['value'], $m ) ) {
					$digits = rtrim( $m[2], '.,' );
					$suffix = substr( $m[2], strlen( $digits ) ) . $m[3];

					$count_attrs = ' data-count="' . esc_attr( str_replace( ',', '', $digits ) ) . '"'
						. ' data-prefix="' . esc_attr( $m[1] ) . '"'
						. ' data-suffix="' . esc_attr( $suffix ) . '"';
				}
				?>
			<div class="cb-stats__item cb-fade">
				<span class="cb-stats__value"<?php echo $count_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each attribute escaped above. ?>><?php echo esc_html( $row['value'] ); ?></span>
				<?php
				if ( $row['label'] ) {
					?>
				<span class="cb-stats__label"><?php echo esc_html( $row['label'] ); ?></span>
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

<?php
/**
 * Block template for Sectors Spotlight.
 *
 * Ported from the andwis lifts "CB Sectors" block. Copy on the left; on the
 * right a tall column of sector names, written twice: a faint base layer
 * (the real links) and a mint copy on top that is masked down to a circle
 * following the pointer (src/js/sectors-spotlight.js sets --x / --y). The
 * column is longer than the band and bleeds off the bottom, so it is padded
 * with repeats of the same names when the real list is short.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) ( $attributes['heading'] ?? '' ) );
$body    = trim( (string) ( $attributes['body'] ?? '' ) );
$source  = $attributes['sectorsSource'] ?? 'auto';
$rows    = $attributes['sectors'] ?? array();

// Sectors: [ label, url ] from the post type or the manual rows. A manual
// row needs a label; its link is optional.
$sectors = array();

if ( 'manual' === $source ) {
	foreach ( (array) $rows as $row ) {
		$label = trim( (string) ( $row['label'] ?? '' ) );

		if ( '' === $label ) {
			continue;
		}

		$sectors[] = array(
			'label' => $label,
			'url'   => trim( (string) ( $row['link'] ?? '' ) ),
		);
	}
} else {
	foreach ( cbae_list_posts( 'sector' ) as $sector ) {
		$sectors[] = array(
			'label' => get_the_title( $sector ),
			'url'   => get_permalink( $sector ),
		);
	}
}

if ( ! $sectors ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
<section class="cb-section"><div class="container"><p class="cb-empty"><?php esc_html_e( 'Sectors Spotlight: no sectors to show yet. Publish a sector, or switch the block to Manual and add some.', 'cb-andwisentrances2026' ); ?></p></div></section>
		<?php
	}
	return;
}

// The column must run past the bottom of the band: at least ten names, and
// always two more than the real ones so the last real name is never the one
// that gets cut off.
$real_count = count( $sectors );
$target     = max( 10, $real_count + 2 );
$display    = $sectors;

while ( count( $display ) < $target ) {
	$display = array_merge( $display, $sectors );
}

$display = array_slice( $display, 0, $target );

/**
 * Print one layer of the list. Both layers get identical markup so the mint
 * copy sits exactly on the base text: an anchor on one and bare text on the
 * other would shift the line boxes apart.
 *
 * @param array $items      Label/url pairs.
 * @param int   $real_count How many leading items are real rather than padding.
 * @param bool  $decorative Whether this is the mint overlay.
 * @return void
 */
$cbae_spotlight_list = function ( $items, $real_count, $decorative ) {
	foreach ( $items as $index => $item ) {
		// Padding repeats the same names, so it stays out of the tab order
		// and the accessibility tree. The overlay is decorative throughout.
		$padding = $index >= $real_count;
		$muted   = $decorative || $padding;
		?>
				<li<?php echo $padding ? ' class="is-padding"' : ''; ?><?php echo $padding && ! $decorative ? ' aria-hidden="true"' : ''; ?>>
					<?php
					if ( $item['url'] ) {
						?>
					<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $muted ? ' tabindex="-1"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
						<?php
					} else {
						?>
					<span><?php echo esc_html( $item['label'] ); ?></span>
						<?php
					}
					?>
				</li>
		<?php
	}
};

$section_id = cbae_section_id( $attributes, 'cb-spotlight' );
$classes    = cbae_section_class( $attributes, 'cb-spotlight', array( 'bg-forest' ) );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container cb-spotlight__inner">
		<div class="cb-spotlight__copy">
			<?php
			if ( $heading ) {
				?>
			<h2><?php echo esc_html( $heading ); ?></h2>
				<?php
			}
			if ( $body ) {
				?>
			<div class="cb-spotlight__body"><?php echo wp_kses_post( wpautop( esc_html( $body ) ) ); ?></div>
				<?php
			}
			?>
		</div>
		<div class="cb-spotlight__panel" data-spotlight>
			<ul class="cb-spotlight__base">
				<?php $cbae_spotlight_list( $display, $real_count, false ); ?>
			</ul>
			<ul class="cb-spotlight__glow" aria-hidden="true">
				<?php $cbae_spotlight_list( $display, $real_count, true ); ?>
			</ul>
		</div>
	</div>
</section>

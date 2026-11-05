<?php
/**
 * Block template for Hero.
 *
 * The heading is the page H1, so one is always rendered: the block's own
 * heading when set, otherwise the post title. With neither, nothing renders.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$eyebrow          = $attributes['eyebrow'] ?? '';
$heading          = trim( (string) ( $attributes['heading'] ?? '' ) );
$highlight        = trim( (string) ( $attributes['highlight'] ?? '' ) );
$subline          = $attributes['subline'] ?? '';
$intro            = $attributes['intro'] ?? '';
$bullets          = cbae_lines( $attributes['bullets'] ?? '' );
$image_id         = (int) ( $attributes['imageId'] ?? 0 );
$image_url        = $attributes['imageUrl'] ?? '';
$use_site_ctas    = ! isset( $attributes['useSiteCtas'] ) || $attributes['useSiteCtas'];
$size             = 'compact' === ( $attributes['size'] ?? 'full' ) ? 'compact' : 'full';
$show_curve       = ! isset( $attributes['showCurve'] ) || $attributes['showCurve'];
$show_breadcrumbs = ! isset( $attributes['showBreadcrumbs'] ) || $attributes['showBreadcrumbs'];

if ( '' === $heading ) {
	$heading = trim( (string) get_the_title() );
}

if ( '' === $heading ) {
	return;
}

if ( $image_id ) {
	$sized = wp_get_attachment_image_url( $image_id, 'full' );

	if ( $sized ) {
		$image_url = $sized;
	}
}

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

// Heading with the highlight phrase wrapped, escaping each piece separately.
$heading_html = esc_html( $heading );

if ( '' !== $highlight ) {
	$pos = stripos( $heading, $highlight );

	if ( false !== $pos ) {
		$heading_html = esc_html( substr( $heading, 0, $pos ) )
			. '<span class="cb-hero__highlight">' . esc_html( substr( $heading, $pos, strlen( $highlight ) ) ) . '</span>'
			. esc_html( substr( $heading, $pos + strlen( $highlight ) ) );
	}
}

$section_id = cbae_section_id( $attributes, 'cb-hero' );
$classes    = array( 'cb-hero', 'cb-hero--' . $size );

if ( $show_curve ) {
	$classes[] = 'cb-hero--curve';
}

if ( ! empty( $attributes['className'] ) ) {
	$classes[] = $attributes['className'];
}
?>
<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<?php
	if ( $image_url ) {
		?>
	<div class="cb-hero__bg" style="background-image:url('<?php echo esc_url( $image_url ); ?>');" role="presentation"></div>
		<?php
	}
	?>
	<div class="cb-hero__scrim"></div>
	<div class="container cb-hero__inner">
		<?php
		if ( $show_breadcrumbs && ! is_front_page() ) {
			cb_andwisentrances2026_render_breadcrumbs( cbae_breadcrumbs(), 'cb-breadcrumbs' );
		}

		if ( $eyebrow ) {
			?>
		<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php
		}
		?>
		<h1><?php echo $heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each piece escaped above. ?></h1>
		<?php
		if ( $subline ) {
			?>
		<p class="cb-hero__subline"><?php echo esc_html( $subline ); ?></p>
			<?php
		}

		if ( $intro ) {
			?>
		<p class="cb-hero__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p>
			<?php
		}

		if ( $bullets ) {
			?>
		<ul class="cb-check-list cb-hero__bullets">
			<?php
			foreach ( $bullets as $line ) {
				?>
			<li><span class="cb-check"><?php echo cbae_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG from the theme's own img/icons. ?></span> <?php echo esc_html( $line ); ?></li>
				<?php
			}
			?>
		</ul>
			<?php
		}

		if ( $primary['url'] || $secondary['url'] ) {
			?>
		<div class="cb-hero__cta">
			<?php
			echo cbae_button( $primary['text'], $primary['url'], 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cbae_button().
			echo cbae_button( $secondary['text'], $secondary['url'], 'btn btn-outline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
			<?php
		}
		?>
	</div>
</section>

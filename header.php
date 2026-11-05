<?php
/**
 * Header template.
 *
 * Fixed header (fixed-header.css). On a page that opens with the Hero block
 * (body.has-hero, set in inc/helpers.php) it starts transparent and goes
 * solid white once the page scrolls — src/js/header.js toggles .is-solid.
 *
 * @package cb-andwisentrances2026
 */

$cbae_ctas = cbae_site_ctas();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a class="skip-link" href="#main">Skip to content</a>
<?php wp_body_open(); ?>

<!-- HEADER-NAV:START -->
<header id="masthead">
	<nav class="navbar container" aria-label="Primary navigation">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="navbar-brand" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> home">
			<?php echo cbae_logo( 'header' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG / wp_get_attachment_image(). ?>
		</a>

		<div class="navbar-collapse" id="primary-menu">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'navbar-nav',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 2,
					'walker'         => new Andwis_Entrances_2026_Nav_Walker(),
				)
			);
			?>
			<div class="navbar-cta-mobile d-flex flex-wrap gap-2">
				<a class="btn btn-sm" href="<?php echo esc_url( $cbae_ctas['quote']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['quote']['text'] ); ?></a>
				<a class="btn btn-sm btn-ghost" href="<?php echo esc_url( $cbae_ctas['survey']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['survey']['text'] ); ?></a>
			</div>
		</div>

		<div class="navbar-actions">
			<a class="btn navbar-cta" href="<?php echo esc_url( $cbae_ctas['quote']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['quote']['text'] ); ?></a>

			<button class="navbar-toggler" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="Toggle navigation">
				<svg class="icon-open" width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true">
					<path d="M3 7h22M3 14h22M3 21h22" />
				</svg>
				<svg class="icon-close" width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true">
					<path d="M6 6l16 16M22 6L6 22" />
				</svg>
			</button>
		</div>
	</nav>
</header>
<!-- HEADER-NAV:END -->

<main id="main">

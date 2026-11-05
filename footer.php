<?php
/**
 * Footer template.
 *
 * Opens with the site-wide CTA band (Get a Quote / Book a Survey) from
 * Site-Wide Settings > CTAs. The band is skipped on the quote/survey/contact
 * pages themselves, on any page that ticks "Hide the site-wide band", and on
 * a page whose last block is already a CTA or Form block, so a page never
 * ends on two prompts.
 *
 * Then the forest footer: brand + tagline + group line, footer menu, contact
 * details, social links, legal links (looked up by slug, dropped if missing)
 * and the statutory company disclosure.
 *
 * @package cb-andwisentrances2026
 */

$cbae_ctas = cbae_site_ctas();

$show_band = ! is_404();

if ( is_singular() ) {
	$post_id = get_queried_object_id();

	if ( get_post_meta( $post_id, 'hide_global_cta', true ) ) {
		$show_band = false;
	}

	if ( is_page( array( 'get-a-quote', 'book-a-survey', 'contact' ) ) ) {
		$show_band = false;
	}

	$content = get_post_field( 'post_content', $post_id );
	$blocks  = has_blocks( $content ) ? array_values( array_filter( parse_blocks( $content ), fn( $b ) => ! empty( $b['blockName'] ) ) ) : array();
	$last    = $blocks ? end( $blocks )['blockName'] : '';

	if ( in_array( $last, array( 'cb-andwisentrances2026/cta', 'cb-andwisentrances2026/form' ), true ) ) {
		$show_band = false;
	}
}

$band_heading = cbae_setting( 'cta_heading', __( 'Ready to talk about your entrance?', 'cb-andwisentrances2026' ) );
$band_text    = cbae_setting( 'cta_text', __( 'Tell us about your building and we will recommend the right solution, with an honest view on cost and lead time.', 'cb-andwisentrances2026' ) );

$social = array();
foreach ( array(
	'linkedin'  => 'LinkedIn',
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
	'x'         => 'X',
	'youtube'   => 'YouTube',
) as $slug => $label ) {
	$url = cbae_setting( $slug . '_url' );
	if ( $url ) {
		$social[ $slug ] = array(
			'label' => $label,
			'url'   => $url,
		);
	}
}

$legal_pages = array();
foreach ( array(
	'privacy-policy'          => __( 'Privacy policy', 'cb-andwisentrances2026' ),
	'cookie-policy'           => __( 'Cookie policy', 'cb-andwisentrances2026' ),
	'terms-and-conditions'    => __( 'Terms & conditions', 'cb-andwisentrances2026' ),
	'accessibility-statement' => __( 'Accessibility', 'cb-andwisentrances2026' ),
) as $slug => $label ) {
	$legal_page = get_page_by_path( $slug );
	if ( $legal_page && 'publish' === $legal_page->post_status ) {
		$legal_pages[ get_permalink( $legal_page ) ] = $label;
	}
}

$legal_name   = trim( cbae_setting( 'registered_name' ) );
$legal_number = trim( cbae_setting( 'company_number' ) );
$legal_place  = trim( cbae_setting( 'place_of_registration' ) );
$legal_vat    = trim( cbae_setting( 'vat_number' ) );
$legal_office = trim( cbae_setting( 'registered_office' ) );
$legal_lines  = array();

if ( $legal_name ) {
	$registration = $legal_name;
	if ( $legal_place ) {
		$registration .= ' is registered in ' . $legal_place;
	}
	if ( $legal_number ) {
		$registration .= ( $legal_place ? ', ' : ' ' ) . 'company number ' . $legal_number;
	}
	$legal_lines[] = $registration . '.';
} elseif ( $legal_number ) {
	$legal_lines[] = 'Company number ' . $legal_number . '.';
}
if ( $legal_office ) {
	$legal_lines[] = 'Registered office: ' . implode( ', ', cbae_lines( $legal_office ) ) . '.';
}
if ( $legal_vat ) {
	$legal_lines[] = 'VAT registration number ' . $legal_vat . '.';
}

$phone   = cbae_setting( 'phone' );
$email   = cbae_setting( 'email' );
$address = cbae_setting( 'address' );
?>
</main>

<?php
if ( $show_band ) {
	?>
<section class="site-cta" aria-labelledby="site-cta-heading">
	<div class="container site-cta__inner">
		<div class="site-cta__copy">
			<h2 id="site-cta-heading"><?php echo esc_html( $band_heading ); ?></h2>
			<?php
			if ( $band_text ) {
				?>
			<p><?php echo esc_html( $band_text ); ?></p>
				<?php
			}
			?>
		</div>
		<div class="site-cta__actions">
			<a class="btn btn-forest" href="<?php echo esc_url( $cbae_ctas['quote']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['quote']['text'] ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( $cbae_ctas['survey']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['survey']['text'] ); ?></a>
		</div>
	</div>
</section>
	<?php
}
?>

<footer id="footer">
	<div class="container site-footer__main">
		<div class="site-footer__brand">
			<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> home">
				<?php echo cbae_logo( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG / wp_get_attachment_image(). ?>
			</a>
			<?php
			$tagline = cbae_setting( 'tagline', get_bloginfo( 'description' ) );
			if ( $tagline ) {
				?>
			<p><?php echo esc_html( $tagline ); ?></p>
				<?php
			}
			$group_url = cbae_setting( 'group_url', 'https://www.andwis.com' );
			?>
			<a class="site-footer__group" href="<?php echo esc_url( $group_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( cbae_setting( 'group_line', __( 'An andwis Group company', 'cb-andwisentrances2026' ) ) ); ?> <?php echo cbae_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php
			if ( $social ) {
				?>
			<div class="site-footer__social">
				<?php
				foreach ( $social as $slug => $item ) {
					?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" aria-label="<?php echo esc_attr( $item['label'] ); ?>" target="_blank" rel="noopener"><?php echo get_icon( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?></a>
					<?php
				}
				?>
			</div>
				<?php
			}
			?>
		</div>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'cb-andwisentrances2026' ); ?>">
			<h3><?php esc_html_e( 'Explore', 'cb-andwisentrances2026' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'menu_class'     => 'site-footer__menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Contact navigation', 'cb-andwisentrances2026' ); ?>">
			<h3><?php esc_html_e( 'Get in touch', 'cb-andwisentrances2026' ); ?></h3>
			<?php
			if ( has_nav_menu( 'footer_contact' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer_contact',
						'menu_class'     => 'site-footer__menu',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
			} else {
				?>
			<ul class="site-footer__menu">
				<li><a href="<?php echo esc_url( cbae_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'cb-andwisentrances2026' ); ?></a></li>
				<li><a href="<?php echo esc_url( cbae_page_url( 'locations' ) ); ?>"><?php esc_html_e( 'Locations', 'cb-andwisentrances2026' ); ?></a></li>
				<li><a href="<?php echo esc_url( cbae_page_url( 'careers' ) ); ?>"><?php esc_html_e( 'Careers', 'cb-andwisentrances2026' ); ?></a></li>
				<li><a href="<?php echo esc_url( $cbae_ctas['quote']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['quote']['text'] ); ?></a></li>
				<li><a href="<?php echo esc_url( $cbae_ctas['survey']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['survey']['text'] ); ?></a></li>
			</ul>
				<?php
			}
			?>
		</nav>

		<div class="site-footer__nav site-footer__contact">
			<h3><?php esc_html_e( 'Contact', 'cb-andwisentrances2026' ); ?></h3>
			<?php
			if ( $phone ) {
				?>
			<p><a href="tel:<?php echo esc_attr( parse_phone( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
				<?php
			}
			if ( $email ) {
				?>
			<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<?php
			}
			if ( $address ) {
				?>
			<p><?php echo nl2br( esc_html( $address ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() of an escaped string. ?></p>
				<?php
			}
			?>
		</div>
	</div>

	<div id="colophon">
		<div class="container">
			<div class="site-footer__legal-row">
				<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $legal_name ? $legal_name : get_bloginfo( 'name' ) ); ?></p>
				<?php
				if ( $legal_pages ) {
					?>
				<ul class="site-footer__legal-links">
					<?php
					foreach ( $legal_pages as $legal_url => $legal_label ) {
						?>
					<li><a href="<?php echo esc_url( $legal_url ); ?>"><?php echo esc_html( $legal_label ); ?></a></li>
						<?php
					}
					?>
				</ul>
					<?php
				}
				?>
			</div>
			<?php
			if ( $legal_lines ) {
				?>
			<p class="site-footer__legal-details"><?php echo implode( '<br>', array_map( 'esc_html', $legal_lines ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each line escaped. ?></p>
				<?php
			}
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

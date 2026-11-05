<?php
/**
 * Block template for Audience Router.
 *
 * Two routing groups side by side: "I need..." tiles (manual rows with an
 * icon) and "My sector is..." chips (every published sector, or manual rows).
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading          = $attributes['heading'] ?? '';
$intro            = $attributes['intro'] ?? '';
$needs_heading    = $attributes['needsHeading'] ?? '';
$needs_rows       = $attributes['needs'] ?? array();
$sectors_heading  = $attributes['sectorsHeading'] ?? '';
$sectors_source   = $attributes['sectorsSource'] ?? 'auto';
$sectors_rows     = $attributes['sectors'] ?? array();
$sectors_all_text = $attributes['sectorsAllText'] ?? '';
$sectors_all_url  = $attributes['sectorsAllUrl'] ?? '';

$allowed_icons = array( 'door', 'wrench', 'shield', 'bolt', 'building', 'phone', 'clock', 'download', 'pin', 'mail', 'quote' );

// Needs: label + url are required for a tile to mean anything.
$needs = array();

foreach ( (array) $needs_rows as $row ) {
	$label = trim( (string) ( $row['label'] ?? '' ) );
	$url   = trim( (string) ( $row['link'] ?? '' ) );

	if ( ! $label || ! $url ) {
		continue;
	}

	$icon = $row['icon'] ?? '';

	$needs[] = array(
		'label' => $label,
		'text'  => trim( (string) ( $row['text'] ?? '' ) ),
		'url'   => $url,
		'icon'  => in_array( $icon, $allowed_icons, true ) ? $icon : '',
	);
}

// Sectors: [ label, url ] from the post type or the manual rows.
$sectors = array();

if ( 'manual' === $sectors_source ) {
	foreach ( (array) $sectors_rows as $row ) {
		$label = trim( (string) ( $row['label'] ?? '' ) );
		$url   = trim( (string) ( $row['link'] ?? '' ) );

		if ( ! $label || ! $url ) {
			continue;
		}

		$sectors[] = array(
			'label' => $label,
			'url'   => $url,
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

if ( ! $sectors_all_url ) {
	$sectors_page = get_page_by_path( 'sectors' );

	if ( $sectors_page && 'publish' === $sectors_page->post_status ) {
		$sectors_all_url = cbae_page_url( 'sectors' );
	}
}

if ( ! $needs && ! $sectors && ! current_user_can( 'edit_posts' ) ) {
	return;
}

$section_id = cbae_section_id( $attributes, 'cb-router' );
$classes    = cbae_section_class( $attributes, 'cb-router' );
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
		if ( ! $needs && ! $sectors ) {
			?>
		<p class="cb-empty"><?php esc_html_e( 'Nothing to route to yet. Add some needs, or publish a sector.', 'cb-andwisentrances2026' ); ?></p>
			<?php
		} else {
			?>
		<div class="cb-router__groups">
			<?php
			if ( $needs ) {
				?>
			<div class="cb-router__group">
				<?php
				if ( $needs_heading ) {
					?>
				<h3><?php echo esc_html( $needs_heading ); ?></h3>
					<?php
				}
				?>
				<ul class="cb-router__needs">
					<?php
					foreach ( $needs as $need ) {
						?>
					<li>
						<a class="cb-router__need" href="<?php echo esc_url( $need['url'] ); ?>">
							<?php
							if ( $need['icon'] ) {
								?>
							<span class="cb-router__icon"><?php echo cbae_icon( $need['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme SVG. ?></span>
								<?php
							}
							?>
							<span class="cb-router__need-body">
								<strong><?php echo esc_html( $need['label'] ); ?></strong>
								<?php
								if ( $need['text'] ) {
									?>
								<span><?php echo esc_html( $need['text'] ); ?></span>
									<?php
								}
								?>
							</span>
							<?php echo cbae_icon( 'arrow-right', 'cb-router__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme SVG. ?>
						</a>
					</li>
						<?php
					}
					?>
				</ul>
			</div>
				<?php
			}
			if ( $sectors ) {
				?>
			<div class="cb-router__group">
				<?php
				if ( $sectors_heading ) {
					?>
				<h3><?php echo esc_html( $sectors_heading ); ?></h3>
					<?php
				}
				?>
				<ul class="cb-router__sectors">
					<?php
					foreach ( $sectors as $sector ) {
						?>
					<li><a class="cb-chip" href="<?php echo esc_url( $sector['url'] ); ?>"><?php echo esc_html( $sector['label'] ); ?></a></li>
						<?php
					}
					if ( $sectors_all_url ) {
						?>
					<li><a class="cb-chip cb-chip--all" href="<?php echo esc_url( $sectors_all_url ); ?>"><?php echo esc_html( $sectors_all_text ? $sectors_all_text : __( 'All sectors', 'cb-andwisentrances2026' ) ); ?></a></li>
						<?php
					}
					?>
				</ul>
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

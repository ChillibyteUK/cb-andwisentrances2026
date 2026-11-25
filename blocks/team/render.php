<?php
/**
 * Block template for Team.
 *
 * A grid of people for the leadership team and similar: a round portrait (a
 * silhouette placeholder until the photo arrives), name, role, an optional
 * short bio and a LinkedIn link. Names and photos live in the page content,
 * never in the theme.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) ( $attributes['heading'] ?? '' ) );
$intro   = trim( (string) ( $attributes['intro'] ?? '' ) );
$columns = in_array( $attributes['columns'] ?? '', array( '2', '3', '4' ), true ) ? $attributes['columns'] : '3';
$people  = array();

foreach ( (array) ( $attributes['people'] ?? array() ) as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$row_name = trim( (string) ( $row['name'] ?? '' ) );

	if ( '' === $row_name ) {
		continue;
	}

	$people[] = array(
		'image'    => absint( $row['image'] ?? 0 ),
		'name'     => $row_name,
		'role'     => trim( (string) ( $row['role'] ?? '' ) ),
		'bio'      => trim( (string) ( $row['bio'] ?? '' ) ),
		'linkedin' => trim( (string) ( $row['linkedin'] ?? '' ) ),
	);
}

if ( ! $people ) {
	return;
}

$section_id = cbae_section_id( $attributes, 'cb-team' );
$classes    = cbae_section_class( $attributes, 'cb-team' );
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
		<ul class="cb-team__grid cb-team__grid--<?php echo esc_attr( $columns ); ?>" data-stagger>
			<?php
			foreach ( $people as $person ) {
				$photo = $person['image'] ? wp_get_attachment_image(
					$person['image'],
					'medium_large',
					false,
					array(
						'alt'     => $person['name'],
						'loading' => 'lazy',
						'sizes'   => '11rem',
					)
				) : '';
				?>
			<li class="cb-team__person cb-fade">
				<div class="cb-team__photo<?php echo $photo ? '' : ' cb-team__photo--placeholder'; ?>">
					<?php
					if ( $photo ) {
						echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output.
					} else {
						?>
					<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="32" cy="25" r="11" fill="currentColor" /><path d="M10 64c0-13 9.5-22 22-22s22 9 22 22z" fill="currentColor" /></svg>
						<?php
					}
					?>
				</div>
				<h3 class="cb-team__name"><?php echo esc_html( $person['name'] ); ?></h3>
				<?php
				if ( $person['role'] ) {
					?>
				<p class="cb-team__role"><?php echo esc_html( $person['role'] ); ?></p>
					<?php
				}

				if ( $person['bio'] ) {
					?>
				<p class="cb-team__bio"><?php echo nl2br( esc_html( $person['bio'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() of an escaped string. ?></p>
					<?php
				}

				if ( $person['linkedin'] ) {
					?>
				<a class="cb-team__link" href="<?php echo esc_url( $person['linkedin'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person's name. */ __( '%s on LinkedIn', 'cb-andwisentrances2026' ), $person['name'] ) ); ?>">
					<?php echo get_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?>
					<span><?php esc_html_e( 'LinkedIn', 'cb-andwisentrances2026' ); ?></span>
				</a>
					<?php
				}
				?>
			</li>
				<?php
			}
			?>
		</ul>
	</div>
</section>

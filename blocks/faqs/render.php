<?php
/**
 * Block template for FAQs / accordion.
 *
 * Markup follows src/js/accordion.js: a [data-accordion] group, each row a
 * [data-accordion-item] holding a <button data-accordion-toggle> with
 * aria-expanded + aria-controls and a panel that is [hidden] while closed.
 * The script flips aria-expanded, the panel's hidden attribute and an
 * .is-open class on the item; the CSS styles the open state off those.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) ( $attributes['heading'] ?? '' ) );
$intro   = trim( (string) ( $attributes['intro'] ?? '' ) );
$schema  = ! isset( $attributes['schema'] ) || $attributes['schema'];
$layout  = 'stack' === ( $attributes['layout'] ?? '' ) ? 'stack' : 'split';
$rows    = array();

foreach ( (array) ( $attributes['items'] ?? array() ) as $row ) {
	$question = trim( (string) ( $row['question'] ?? '' ) );

	if ( '' === $question ) {
		continue;
	}

	$rows[] = array(
		'question' => $question,
		'answer'   => trim( (string) ( $row['answer'] ?? '' ) ),
	);
}

if ( ! $rows && ! current_user_can( 'edit_posts' ) ) {
	return;
}

if ( ! in_array( $attributes['background'] ?? '', array( 'paper', 'sage-tint', 'sage' ), true ) ) {
	$attributes['background'] = 'paper';
}

$has_head = $heading || $intro;
$is_split = 'split' === $layout && $has_head;
$extra    = array( 'cb-faqs--' . $layout );

if ( ! $has_head ) {
	$extra[] = 'cb-faqs--no-head';
}

$section_id = cbae_section_id( $attributes, 'cb-faqs' );
$classes    = cbae_section_class( $attributes, 'cb-faqs', $extra );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<div class="<?php echo esc_attr( $is_split ? 'row gap-5 cb-faqs__row' : 'cb-faqs__row' ); ?>">
			<?php
			if ( $has_head ) {
				?>
			<div class="<?php echo esc_attr( $is_split ? 'col-12 col-lg-4 cb-faqs__head' : 'cb-faqs__head' ); ?>">
				<div class="<?php echo esc_attr( $is_split ? 'cb-section-head cb-section-head--start' : 'cb-section-head' ); ?>">
					<?php
					if ( $heading ) {
						?>
					<h2><?php echo esc_html( $heading ); ?></h2>
						<?php
					}

					if ( $intro ) {
						?>
					<p><?php echo nl2br( esc_html( $intro ) ); ?></p>
						<?php
					}
					?>
				</div>
			</div>
				<?php
			}
			?>
			<div class="<?php echo esc_attr( $is_split ? 'col-12 col-lg-8 cb-faqs__body' : 'cb-faqs__body' ); ?>">
				<?php
				if ( ! $rows ) {
					?>
				<p class="cb-empty"><?php esc_html_e( 'No questions to show yet.', 'cb-andwisentrances2026' ); ?></p>
					<?php
				} else {
					?>
				<div class="cb-faqs__list" data-accordion>
					<?php
					foreach ( $rows as $row ) {
						$panel_id  = wp_unique_id( 'cb-faqs-panel-' );
						$toggle_id = $panel_id . '-toggle';
						?>
					<div class="cb-faqs__item" data-accordion-item>
						<h3 class="cb-faqs__question">
							<button type="button" class="cb-faqs__toggle" id="<?php echo esc_attr( $toggle_id ); ?>" data-accordion-toggle aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
								<span class="cb-faqs__label"><?php echo esc_html( $row['question'] ); ?></span>
								<span class="cb-faqs__icon" aria-hidden="true"><?php echo cbae_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?></span>
							</button>
						</h3>
						<div class="cb-faqs__panel" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $toggle_id ); ?>" hidden>
							<div class="cb-prose"><?php echo wp_kses_post( wpautop( $row['answer'] ) ); ?></div>
						</div>
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
	</div>
</section>
<?php
if ( $schema && $rows ) {
	$entities = array();

	foreach ( $rows as $row ) {
		// A space before each tag stops "</li><li>" running two words together
		// once the tags are stripped.
		$answer_text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( str_replace( '<', ' <', $row['answer'] ) ) ) );

		if ( '' === $answer_text ) {
			continue;
		}

		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $row['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $answer_text,
			),
		);
	}

	if ( $entities ) {
		$faq_schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
		?>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD, tags hex-encoded. ?></script>
		<?php
	}
}

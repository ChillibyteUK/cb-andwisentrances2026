<?php
/**
 * Block template for Table.
 *
 * Heading + intro + a two or three column data table (specifications,
 * accreditations, offices). The third column only exists when it has a
 * heading or any row fills it. Every body cell carries its column heading in
 * data-label so the CSS can stack each row as a card on small screens.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading          = $attributes['heading'] ?? '';
$intro            = $attributes['intro'] ?? '';
$note             = trim( (string) ( $attributes['note'] ?? '' ) );
$first_col_header = ! isset( $attributes['firstColHeader'] ) || $attributes['firstColHeader'];
$background       = in_array( $attributes['background'] ?? '', array( 'paper', 'sage-tint', 'sage' ), true ) ? $attributes['background'] : 'paper';
$heads            = array(
	trim( (string) ( $attributes['head1'] ?? '' ) ),
	trim( (string) ( $attributes['head2'] ?? '' ) ),
	trim( (string) ( $attributes['head3'] ?? '' ) ),
);
$rows             = array();
$has_third        = '' !== $heads[2];

foreach ( (array) ( $attributes['rows'] ?? array() ) as $row ) {
	$cells = array(
		trim( (string) ( $row['c1'] ?? '' ) ),
		trim( (string) ( $row['c2'] ?? '' ) ),
		trim( (string) ( $row['c3'] ?? '' ) ),
	);

	if ( '' === implode( '', $cells ) ) {
		continue;
	}

	if ( '' !== $cells[2] ) {
		$has_third = true;
	}

	$rows[] = $cells;
}

if ( ! $rows && ! current_user_can( 'edit_posts' ) ) {
	return;
}

$column_count = $has_third ? 3 : 2;
$heads        = array_slice( $heads, 0, $column_count );
$has_head     = '' !== implode( '', $heads );

$attributes['background'] = $background;

$section_id = cbae_section_id( $attributes, 'cb-table-block' );
$classes    = cbae_section_class( $attributes, 'cb-table-block' );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container">
		<?php
		if ( $heading || $intro ) {
			?>
		<div class="cb-section-head cb-section-head--start">
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
		if ( ! $rows ) {
			?>
		<p class="cb-empty"><?php esc_html_e( 'No table rows to show yet.', 'cb-andwisentrances2026' ); ?></p>
			<?php
		} else {
			?>
		<div class="cb-table-wrap">
			<table class="cb-table cb-table--cols-<?php echo esc_attr( $column_count ); ?>">
				<?php
				if ( $has_head ) {
					?>
				<thead>
					<tr>
						<?php
						foreach ( $heads as $head ) {
							?>
						<th scope="col"><?php echo esc_html( $head ); ?></th>
							<?php
						}
						?>
					</tr>
				</thead>
					<?php
				}
				?>
				<tbody>
					<?php
					foreach ( $rows as $cells ) {
						?>
					<tr>
						<?php
						for ( $i = 0; $i < $column_count; $i++ ) {
							$cell_tag = ( 0 === $i && $first_col_header ) ? 'th' : 'td';
							$scope    = 'th' === $cell_tag ? ' scope="row"' : '';
							?>
						<<?php echo $cell_tag . $scope; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed strings set above. ?> data-label="<?php echo esc_attr( $heads[ $i ] ); ?>"><?php echo nl2br( cbae_link_phone_numbers( $cells[ $i ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></<?php echo $cell_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php
						}
						?>
					</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
			<?php
			if ( '' !== $note ) {
				?>
		<p class="cb-table-block__note"><?php echo nl2br( esc_html( $note ) ); ?></p>
				<?php
			}
		}
		?>
	</div>
</section>

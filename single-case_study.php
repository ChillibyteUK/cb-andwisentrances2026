<?php
/**
 * Single case study.
 *
 * Fixed shape: forest head over the featured image with client, sectors and
 * products as chips; a facts strip; then the body (blocks — typically the
 * Case Study Summary block for challenge / what we did / result, plus
 * galleries and quotes); then three more case studies. The site-wide CTA
 * band follows in the footer.
 *
 * @package cb-andwisentrances2026
 */

get_header();
the_post();

$case_id  = get_the_ID();
$client   = get_post_meta( $case_id, 'client_name', true );
$location = get_post_meta( $case_id, 'location', true );
$year     = get_post_meta( $case_id, 'completed', true );
$sectors  = array_filter( array_map( 'get_post', cbae_related_ids( $case_id, 'related_sectors' ) ) );
$products = array_filter( array_map( 'get_post', cbae_related_ids( $case_id, 'related_products' ) ) );
$hero_url = get_the_post_thumbnail_url( $case_id, 'full' );
$hub      = get_page_by_path( 'case-studies' );

$facts = array_filter(
	array(
		__( 'Client', 'cb-andwisentrances2026' )   => $client,
		__( 'Location', 'cb-andwisentrances2026' ) => $location,
		__( 'Completed', 'cb-andwisentrances2026' ) => $year,
		__( 'Sector', 'cb-andwisentrances2026' )   => $sectors ? implode( ', ', wp_list_pluck( $sectors, 'post_title' ) ) : '',
		__( 'Products', 'cb-andwisentrances2026' ) => $products ? implode( ', ', wp_list_pluck( $products, 'post_title' ) ) : '',
	)
);
?>
<section class="cb-hero cb-hero--compact cb-hero--curve cb-case-study__hero">
	<?php
	if ( $hero_url ) {
		?>
	<div class="cb-hero__bg" style="background-image:url('<?php echo esc_url( $hero_url ); ?>');"></div>
		<?php
	}
	?>
	<div class="cb-hero__scrim"></div>
	<div class="container cb-hero__inner">
		<?php cb_andwisentrances2026_render_breadcrumbs( cbae_breadcrumbs(), 'cb-breadcrumbs' ); ?>
		<span class="eyebrow"><?php esc_html_e( 'Case study', 'cb-andwisentrances2026' ); ?></span>
		<h1><?php the_title(); ?></h1>
		<?php
		if ( $client ) {
			?>
		<p class="cb-hero__subline"><?php echo esc_html( $client ); ?></p>
			<?php
		}
		if ( $sectors || $products ) {
			?>
		<ul class="cb-hero__chips">
			<?php
			foreach ( array_merge( $sectors, $products ) as $chip ) {
				?>
			<li><a class="cb-tag" href="<?php echo esc_url( get_permalink( $chip ) ); ?>"><?php echo esc_html( get_the_title( $chip ) ); ?></a></li>
				<?php
			}
			?>
		</ul>
			<?php
		}
		?>
	</div>
</section>

<?php
if ( $facts ) {
	?>
<section class="cb-section cb-section--sm cb-case-study__facts">
	<div class="container">
		<dl class="cb-facts">
			<?php
			foreach ( $facts as $label => $value ) {
				?>
			<div class="cb-facts__item">
				<dt><?php echo esc_html( $label ); ?></dt>
				<dd><?php echo esc_html( $value ); ?></dd>
			</div>
				<?php
			}
			?>
		</dl>
	</div>
</section>
	<?php
}

the_content();

$more = get_posts(
	array(
		'post_type'      => 'case_study',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => array( $case_id ),
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	)
);

if ( $more ) {
	?>
<section class="cb-section bg-sage-tint cb-case-study__more">
	<div class="container">
		<div class="cb-section-head">
			<h2><?php esc_html_e( 'More case studies', 'cb-andwisentrances2026' ); ?></h2>
		</div>
		<div class="cb-card-grid">
			<?php
			foreach ( $more as $item ) {
				$chips = wp_list_pluck( array_filter( array_map( 'get_post', cbae_related_ids( $item->ID, 'related_sectors' ) ) ), 'post_title' );
				cbae_post_card( $item, array( 'meta' => array_slice( $chips, 0, 2 ) ) );
			}
			?>
		</div>
		<?php
		if ( $hub ) {
			?>
		<p class="cb-index__more"><a class="btn btn-ghost" href="<?php echo esc_url( get_permalink( $hub ) ); ?>"><?php esc_html_e( 'View all case studies', 'cb-andwisentrances2026' ); ?></a></p>
			<?php
		}
		?>
	</div>
</section>
	<?php
}

get_footer();

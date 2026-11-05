<?php
/**
 * 404 template.
 *
 * @package cb-andwisentrances2026
 */

get_header();
$cbae_ctas = cbae_site_ctas();
?>
<section class="cb-page-head">
	<div class="container">
		<span class="eyebrow"><?php esc_html_e( 'Error 404', 'cb-andwisentrances2026' ); ?></span>
		<h1><?php esc_html_e( 'That page is not here', 'cb-andwisentrances2026' ); ?></h1>
		<p><?php esc_html_e( 'The link may be out of date or the page may have moved. Try one of these instead.', 'cb-andwisentrances2026' ); ?></p>
	</div>
</section>
<section class="cb-section">
	<div class="container">
		<div class="d-flex flex-wrap gap-3">
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'cb-andwisentrances2026' ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( cbae_page_url( 'products' ) ); ?>"><?php esc_html_e( 'Browse products', 'cb-andwisentrances2026' ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( $cbae_ctas['quote']['url'] ); ?>"><?php echo esc_html( $cbae_ctas['quote']['text'] ); ?></a>
		</div>
	</div>
</section>
<?php
get_footer();

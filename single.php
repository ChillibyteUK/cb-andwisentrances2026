<?php
/**
 * Single news post.
 *
 * The same frame as every other page: the compact hero (featured image
 * behind it when there is one) with the category, date, reading time, title
 * and the hand-written excerpt as a standfirst; the article in a reading
 * column down the left, as on andwis lifts; then the three latest other
 * posts. Article styles are in src/css/components.css (.cb-post).
 *
 * @package cb-andwisentrances2026
 */

get_header();

while ( have_posts() ) {
	the_post();

	$cats    = get_the_category();
	$news_id = (int) get_option( 'page_for_posts' );
	?>
	<section class="cb-hero cb-hero--compact cb-hero--curve cb-post__head">
		<?php
		if ( has_post_thumbnail() ) {
			?>
		<div class="cb-hero__bg" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>');" role="presentation"></div>
			<?php
		}
		?>
		<div class="cb-hero__scrim"></div>
		<div class="container cb-hero__inner">
			<?php cb_andwisentrances2026_render_breadcrumbs( cb_andwisentrances2026_get_breadcrumbs(), 'cb-breadcrumbs' ); ?>
			<p class="cb-post__meta">
				<?php
				if ( $cats ) {
					?>
				<span class="cb-tag"><?php echo esc_html( $cats[0]->name ); ?></span>
					<?php
				}
				?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
				<span><?php echo esc_html( estimate_reading_time_in_minutes( get_the_content(), 250, true ) ); ?> <?php esc_html_e( 'min read', 'cb-andwisentrances2026' ); ?></span>
			</p>
			<h1><?php the_title(); ?></h1>
			<?php
			// Only a hand-written excerpt: an automatic one would repeat the
			// opening of the article underneath it.
			if ( has_excerpt() ) {
				?>
			<p class="cb-hero__intro"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
				<?php
			}
			?>
		</div>
	</section>

	<article <?php post_class( 'cb-post' ); ?>>
		<div class="container">
			<div class="cb-post__body cb-prose">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php

	$more = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'no_found_rows'  => true,
		)
	);

	if ( $more ) {
		?>
	<section class="cb-section bg-sage-tint cb-post__more">
		<div class="container">
			<div class="cb-section-head">
				<h2><?php esc_html_e( 'More news & resources', 'cb-andwisentrances2026' ); ?></h2>
			</div>
			<div class="cb-card-grid">
				<?php
				foreach ( $more as $item ) {
					cbae_post_card( $item );
				}
				?>
			</div>
			<?php
			if ( $news_id ) {
				?>
			<p class="cb-index__more"><a class="btn btn-ghost" href="<?php echo esc_url( get_permalink( $news_id ) ); ?>"><?php esc_html_e( 'All news & resources', 'cb-andwisentrances2026' ); ?></a></p>
				<?php
			}
			?>
		</div>
	</section>
		<?php
	}
}

get_footer();

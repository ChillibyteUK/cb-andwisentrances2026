<?php
/**
 * Single news post.
 *
 * Forest head with the category, date and title; featured image; the
 * article in a reading measure; then the three latest other posts.
 *
 * @package cb-andwisentrances2026
 */

get_header();

while ( have_posts() ) {
	the_post();

	$cats    = get_the_category();
	$news_id = (int) get_option( 'page_for_posts' );
	?>
	<section class="cb-page-head cb-post__head">
		<div class="container">
			<?php cb_andwisentrances2026_render_breadcrumbs( cb_andwisentrances2026_get_breadcrumbs(), 'cb-breadcrumbs' ); ?>
			<p class="cb-post__meta">
				<?php
				if ( $cats ) {
					?>
				<span class="cb-tag"><?php echo esc_html( $cats[0]->name ); ?></span>
					<?php
				}
				?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<span><?php echo esc_html( estimate_reading_time_in_minutes( get_the_content(), 250, true ) ); ?> <?php esc_html_e( 'min read', 'cb-andwisentrances2026' ); ?></span>
			</p>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>

	<article <?php post_class( 'cb-post' ); ?>>
		<?php
		if ( has_post_thumbnail() ) {
			?>
		<div class="container cb-post__hero">
			<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
		</div>
			<?php
		}
		?>
		<div class="cb-post__body">
			<?php the_content(); ?>
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

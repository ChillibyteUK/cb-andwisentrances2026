<?php
/**
 * News & resources hub (the Posts page set in Settings > Reading) and the
 * fallback for any other archive.
 *
 * Takes its title and intro from the posts page itself (title + excerpt),
 * lists posts as cards with a client-side category filter, then points to
 * the Downloads page.
 *
 * @package cb-andwisentrances2026
 */

get_header();

$news_page = is_home() ? get_post( (int) get_option( 'page_for_posts' ) ) : null;
$title     = $news_page ? get_the_title( $news_page ) : get_the_archive_title();
$intro     = $news_page ? $news_page->post_excerpt : get_the_archive_description();
$cats      = get_categories( array( 'hide_empty' => true ) );
$downloads = get_page_by_path( 'downloads' );
?>
<section class="cb-page-head">
	<div class="container">
		<?php
		if ( $news_page ) {
			cb_andwisentrances2026_render_breadcrumbs(
				array(
					array(
						'label' => __( 'Home', 'cb-andwisentrances2026' ),
						'url'   => home_url( '/' ),
					),
					array(
						'label' => $title,
						'url'   => '',
					),
				),
				'cb-breadcrumbs'
			);
		}
		?>
		<h1><?php echo esc_html( $title ); ?></h1>
		<?php
		if ( $intro ) {
			?>
		<p><?php echo esc_html( wp_strip_all_tags( $intro ) ); ?></p>
			<?php
		}
		?>
	</div>
</section>

<section class="cb-section cb-post-index" data-filter-root>
	<div class="container">
		<?php
		if ( count( $cats ) > 1 && is_home() ) {
			?>
		<div class="cb-chips" role="group" aria-label="<?php esc_attr_e( 'Filter by topic', 'cb-andwisentrances2026' ); ?>">
			<button type="button" class="cb-chip is-active" data-filter="*" aria-pressed="true"><?php esc_html_e( 'All', 'cb-andwisentrances2026' ); ?></button>
			<?php
			foreach ( $cats as $cat ) {
				?>
			<button type="button" class="cb-chip" data-filter="<?php echo esc_attr( $cat->slug ); ?>" aria-pressed="false"><?php echo esc_html( $cat->name ); ?></button>
				<?php
			}
			?>
		</div>
			<?php
		}

		if ( have_posts() ) {
			?>
		<div class="cb-card-grid" data-filter-items>
			<?php
			while ( have_posts() ) {
				the_post();
				$terms = wp_list_pluck( get_the_category(), 'slug' );
				?>
			<div data-filter-terms="<?php echo esc_attr( implode( ' ', $terms ) ); ?>">
				<?php cbae_post_card( get_post() ); ?>
			</div>
				<?php
			}
			?>
		</div>
		<p class="cb-empty" data-filter-empty hidden><?php esc_html_e( 'No articles match that topic yet.', 'cb-andwisentrances2026' ); ?></p>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'cb-pagination',
					'prev_text' => __( 'Newer', 'cb-andwisentrances2026' ),
					'next_text' => __( 'Older', 'cb-andwisentrances2026' ),
				)
			);
		} else {
			?>
		<p class="cb-empty"><?php esc_html_e( 'No articles published yet.', 'cb-andwisentrances2026' ); ?></p>
			<?php
		}
		?>
	</div>
</section>

<?php
if ( $downloads && 'publish' === $downloads->post_status ) {
	?>
<section class="cb-section bg-sage-tint">
	<div class="container">
		<div class="cb-section-head">
			<h2><?php echo esc_html( get_the_title( $downloads ) ); ?></h2>
			<?php
			if ( $downloads->post_excerpt ) {
				?>
			<p><?php echo esc_html( $downloads->post_excerpt ); ?></p>
				<?php
			}
			?>
			<p class="mt-4"><a class="btn" href="<?php echo esc_url( get_permalink( $downloads ) ); ?>"><?php esc_html_e( 'Browse downloads', 'cb-andwisentrances2026' ); ?></a></p>
		</div>
	</div>
</section>
	<?php
}

get_footer();

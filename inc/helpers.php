<?php
/**
 * Project-specific helpers for andwis entrances.
 *
 * Short `cbae_` prefix (andwis entrances) for project helpers, as opposed to
 * the skeleton's own `cb_andwisentrances2026_` prefix on the core files — the
 * two are easy to tell apart and the short one keeps templates readable.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Excerpt derived from the post's own content, ignoring the manual excerpt
 * field entirely.
 *
 * @param int|WP_Post|null $post   Post to excerpt. Defaults to the loop post.
 * @param int              $length Words to keep.
 * @return string Plain-text excerpt (unescaped — escape at output).
 */
function cb_andwisentrances2026_content_excerpt( $post = null, $length = 30 ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$text = strip_shortcodes( $post->post_content );
	$text = excerpt_remove_blocks( $text );
	$text = wp_strip_all_tags( $text );
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );

	if ( '' === $text ) {
		return '';
	}

	return wp_trim_words( $text, absint( $length ), '…' );
}

/**
 * Short teaser for a post wherever it appears as a card: the Card summary
 * meta field, then the manual excerpt, then the content.
 *
 * @param int|WP_Post $post  Post.
 * @param int         $words Words to keep.
 * @return string Plain text, unescaped.
 */
function cbae_card_summary( $post, $words = 24 ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$summary = (string) get_post_meta( $post->ID, 'card_summary', true );

	if ( '' === trim( $summary ) && '' !== trim( (string) $post->post_excerpt ) ) {
		$summary = $post->post_excerpt;
	}

	if ( '' === trim( $summary ) ) {
		$summary = cb_andwisentrances2026_content_excerpt( $post, $words );
	}

	return wp_trim_words( wp_strip_all_tags( $summary ), $words, '…' );
}

/**
 * Attachment ID for a post's card: the Card icon meta field, else the
 * featured image.
 *
 * @param int|WP_Post $post Post.
 * @return int
 */
function cbae_card_image_id( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return 0;
	}

	$icon = absint( get_post_meta( $post->ID, 'card_icon', true ) );

	if ( $icon && wp_attachment_is_image( $icon ) ) {
		return $icon;
	}

	return (int) get_post_thumbnail_id( $post->ID );
}

/**
 * Array of post IDs from a relationship meta field.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key, e.g. related_sectors.
 * @return int[]
 */
function cbae_related_ids( $post_id, $key ) {
	$ids = get_post_meta( $post_id, $key, true );

	return array_values( array_filter( array_map( 'absint', (array) $ids ) ) );
}

/**
 * Published posts of a type that reference $post_id in a relationship field
 * — the reverse of cbae_related_ids(). Used for "case studies in this
 * sector" and "sectors using this product".
 *
 * Relationship meta is stored as a serialised array of ints, hence the LIKE
 * against the serialised integer fragment (i:123;).
 *
 * @param string $post_type Post type to search.
 * @param string $key       Meta key on that post type.
 * @param int    $post_id   The referenced post.
 * @param int    $limit     Max results, -1 for all.
 * @return WP_Post[]
 */
function cbae_posts_referencing( $post_type, $key, $post_id, $limit = 3 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => $limit,
			'orderby'          => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'    => true,
			'suppress_filters' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'       => array(
				array(
					'key'     => $key,
					'value'   => 'i:' . $post_id . ';',
					'compare' => 'LIKE',
				),
			),
		)
	);
}

/**
 * Normalised card rows for the Cards block and the templates — every source
 * (manual rows, children, a post type, selected posts, related posts) ends
 * up in this shape so one piece of markup renders all of them.
 *
 * @param WP_Post[] $posts Posts.
 * @return array[] [ [ title, text, url, image_id, link_text ], ... ]
 */
function cbae_posts_to_cards( $posts ) {
	$cards = array();

	foreach ( $posts as $item ) {
		$item = get_post( $item );

		if ( ! $item ) {
			continue;
		}

		$cards[] = array(
			'title'     => get_the_title( $item ),
			'text'      => cbae_card_summary( $item ),
			'url'       => get_permalink( $item ),
			'image_id'  => cbae_card_image_id( $item ),
			'link_text' => 'case_study' === $item->post_type ? __( 'Read the case study', 'cb-andwisentrances2026' ) : __( 'Find out more', 'cb-andwisentrances2026' ),
			'post_type' => $item->post_type,
		);
	}

	return $cards;
}

/**
 * Standard "listing" query for a post type, ordered by menu order then title.
 *
 * @param string|string[] $post_type Post type(s).
 * @param int             $limit     Max posts, -1 for all.
 * @param array           $extra     Extra WP_Query args.
 * @return WP_Post[]
 */
function cbae_list_posts( $post_type, $limit = -1, $extra = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'        => $post_type,
				'post_status'      => 'publish',
				'posts_per_page'   => $limit,
				'orderby'          => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'no_found_rows'    => true,
				'suppress_filters' => false,
			),
			$extra
		)
	);
}

/**
 * Site-wide setting shortcut.
 *
 * @param string $key     Setting key.
 * @param string $default Fallback.
 * @return string
 */
function cbae_setting( $key, $default = '' ) {
	return cb_andwisentrances2026_get_setting( $key, $default );
}

/**
 * URL for a well-known page by slug, falling back to a path so links still
 * go somewhere sensible before the page exists.
 *
 * @param string $slug Page slug, e.g. get-a-quote.
 * @return string
 */
function cbae_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}

	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * The two site-wide CTAs. Each is [ text, url ]; the URLs are overridable on
 * Site-Wide Settings and default to the Get a Quote / Book a Survey pages.
 *
 * @return array{quote: array{text: string, url: string}, survey: array{text: string, url: string}}
 */
function cbae_site_ctas() {
	return array(
		'quote'  => array(
			'text' => cbae_setting( 'quote_cta_text', __( 'Get a Quote', 'cb-andwisentrances2026' ) ),
			'url'  => cbae_setting( 'quote_cta_url', cbae_page_url( 'get-a-quote' ) ),
		),
		'survey' => array(
			'text' => cbae_setting( 'survey_cta_text', __( 'Book a Survey', 'cb-andwisentrances2026' ) ),
			'url'  => cbae_setting( 'survey_cta_url', cbae_page_url( 'book-a-survey' ) ),
		),
	);
}

/**
 * Site logo markup — the uploaded logo from Site-Wide Settings when set,
 * otherwise an inline SVG built from the andwis wordmark with "entrances"
 * set in Poppins, which the CSS colours per context (paper over a hero,
 * forest on white, paper again in the footer).
 *
 * @param string $context 'header' or 'footer'.
 * @return string HTML.
 */
function cbae_logo( $context = 'header' ) {
	$ids = cb_andwisentrances2026_get_gallery_setting( 'footer' === $context ? 'logo_footer' : 'logo' );

	if ( empty( $ids ) && 'footer' === $context ) {
		$ids = cb_andwisentrances2026_get_gallery_setting( 'logo' );
	}

	if ( ! empty( $ids ) ) {
		$img = wp_get_attachment_image( $ids[0], 'full', false, array( 'class' => 'logo-img', 'alt' => get_bloginfo( 'name' ) ) );

		if ( $img ) {
			return $img;
		}
	}

	return '<svg xmlns="http://www.w3.org/2000/svg" role="img" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '" viewBox="0 0 352 34" class="logo-svg">'
		. '<path class="logo-andwis" d="M43.73 32.52v-12.6c0-1.07-.03-2.72-1.31-3.96-.77-.73-2.01-1.24-3.16-1.24s-2.34.47-3.08 1.12c-1.54 1.35-1.51 3.22-1.51 4.66v12.03h-6.66v-12.15c0-2.69.12-5.5 2.9-8.3 2.16-2.19 4.86-3.19 8.47-3.19s6.93 1.39 8.52 3.08c2.24 2.42 2.51 5.7 2.51 8.42v12.15h-6.68v-.02ZM116.02 9.19h7.16V32.5h-7.16z"/>'
		. '<path class="logo-andwis" d="M119.61 0c2.25 0 4.07 1.82 4.07 4.06s-1.82 4.06-4.07 4.06-4.07-1.82-4.07-4.06 1.82-4.06 4.07-4.06"/>'
		. '<path class="logo-andwis" d="M71.35 29.91c-2.04 1.99-4.12 2.72-6.9 2.72s-5.26-.9-7.28-2.72c-2.7-2.39-4.09-5.5-4.09-9.14s1.3-6.32 3.73-8.66c2.18-2.12 4.82-3.21 7.77-3.21s4.99.95 6.55 2.94V.89h6.85v31.63h-6.63v-2.61ZM65.98 26.45c2.95 0 5.42-2.42 5.42-5.63s-2.48-5.85-5.67-5.85-5.78 2.64-5.78 5.77 2.51 5.71 6.03 5.71"/>'
		. '<path class="logo-andwis" d="M131.94 25.35v.22c0 1.25 1 2.14 2.44 2.14s2.26-.72 2.26-1.97c0-1.84-1.81-1.75-4.62-2.64-3.9-1.22-5.79-3.31-5.79-6.7s3.23-7.5 8.61-7.5 7.65 2.64 7.9 6.92h-6.01c-.25-1.47-.8-2.09-1.98-2.09s-2.06.75-2.06 1.84c0 1.55 1.39 1.92 3.7 2.56 4.62 1.25 6.93 3.02 6.93 6.75s-3.57 7.8-9.02 7.8-8.15-2.72-8.6-7.3h6.26-.02v-.02ZM114.33 9.21 104.05 33.32l-7.3-15.11-6.68 15.11L79.69 9.21h7.1l4.22 9.64 3.18-7.04-1.2-2.6h6.21l4.28 10.06 3.75-10.06h7.1ZM18.3 29.9c-2.04 1.99-4.12 2.72-6.92 2.72s-5.26-.9-7.3-2.72C1.39 27.51 0 24.38 0 20.75s1.31-6.33 3.73-8.67c2.18-2.12 4.82-3.21 7.77-3.21s4.99.95 6.56 2.94v-2.61h6.86v23.28h-6.65v-2.61h.02v.02ZM12.91 26.44c2.95 0 5.42-2.42 5.42-5.63s-2.48-5.85-5.69-5.85-5.78 2.64-5.78 5.77 2.51 5.71 6.04 5.71"/>'
		. '<path class="logo-dot" d="M150.13 24.31c2.27 0 4.1 1.83 4.1 4.09s-1.84 4.09-4.1 4.09-4.1-1.83-4.1-4.09 1.84-4.09 4.1-4.09"/>'
		. '<text class="logo-word" x="158" y="32.3" font-size="33.5" font-weight="700" letter-spacing="-0.5">entrances</text>'
		. '</svg>';
}

/**
 * Inline SVG icon from img/icons/, with a class on the root element.
 *
 * @param string $name  Icon slug.
 * @param string $class Extra class.
 * @return string
 */
function cbae_icon( $name, $class = '' ) {
	$svg = get_icon( $name );

	if ( ! $svg ) {
		return '';
	}

	if ( $class ) {
		$svg = preg_replace( '/<svg\b/', '<svg class="' . esc_attr( $class ) . '"', $svg, 1 );
	}

	return $svg;
}

/**
 * Name of the first block in a post's content ('' if none).
 *
 * @param int|WP_Post|null $post Post.
 * @return string
 */
function cbae_first_block_name( $post = null ) {
	$post = get_post( $post );

	if ( ! $post || ! has_blocks( $post->post_content ) ) {
		return '';
	}

	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( ! empty( $block['blockName'] ) ) {
			return $block['blockName'];
		}
	}

	return '';
}

/**
 * Whether the current singular view opens with the Hero block, so the
 * header can start transparent over it (body.has-hero in nav.css).
 *
 * @return bool
 */
function cbae_starts_with_hero() {
	if ( ! is_singular() ) {
		return false;
	}

	return 'cb-andwisentrances2026/hero' === cbae_first_block_name();
}

/**
 * Body classes: has-hero when the page opens on the Hero block.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function cbae_body_class( $classes ) {
	if ( cbae_starts_with_hero() ) {
		$classes[] = 'has-hero';
	}

	return $classes;
}
add_filter( 'body_class', 'cbae_body_class' );

/**
 * Breadcrumb trail for the current view, extending the skeleton's page
 * breadcrumbs with the hub page for each custom post type.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{label: string, url: string}>
 */
function cbae_breadcrumbs( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$type    = get_post_type( $post_id );
	$hubs    = array(
		'product'    => 'products',
		'service'    => 'service-maintenance',
		'sector'     => 'sectors',
		'case_study' => 'case-studies',
		'vacancy'    => 'careers',
	);

	if ( ! isset( $hubs[ $type ] ) ) {
		return cb_andwisentrances2026_get_breadcrumbs( $post_id );
	}

	$crumbs = array(
		array(
			'label' => __( 'Home', 'cb-andwisentrances2026' ),
			'url'   => home_url( '/' ),
		),
	);

	$hub = get_page_by_path( $hubs[ $type ] );

	if ( $hub && 'publish' === $hub->post_status ) {
		$crumbs[] = array(
			'label' => get_the_title( $hub ),
			'url'   => get_permalink( $hub ),
		);
	}

	foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $ancestor_id ) {
		$crumbs[] = array(
			'label' => get_the_title( $ancestor_id ),
			'url'   => get_permalink( $ancestor_id ),
		);
	}

	$crumbs[] = array(
		'label' => get_the_title( $post_id ),
		'url'   => '',
	);

	return $crumbs;
}

/**
 * Fallback page head for a singular view that does not open with a Hero
 * block: forest band with breadcrumbs and the title, so no page ever starts
 * with the fixed header sitting over bare body copy.
 *
 * @return void
 */
function cbae_page_head() {
	if ( cbae_starts_with_hero() || is_front_page() ) {
		return;
	}
	?>
	<section class="cb-page-head">
		<div class="container">
			<?php cb_andwisentrances2026_render_breadcrumbs( cbae_breadcrumbs(), 'cb-breadcrumbs' ); ?>
			<h1><?php the_title(); ?></h1>
			<?php
			if ( has_excerpt() ) {
				?>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php
			}
			?>
		</div>
	</section>
	<?php
}

/**
 * Render one post card (news, case study or any post) — shared by the
 * index templates and the index blocks so they cannot drift apart.
 *
 * @param int|WP_Post $item  Post.
 * @param array       $args  meta (string[]) extra meta chips, words (int).
 * @return void
 */
function cbae_post_card( $item, $args = array() ) {
	$item = get_post( $item );

	if ( ! $item ) {
		return;
	}

	$image = cbae_card_image_id( $item );
	$meta  = $args['meta'] ?? array();
	$words = $args['words'] ?? 22;

	if ( 'post' === $item->post_type ) {
		$meta[] = get_the_date( '', $item );
		$cats   = get_the_category( $item->ID );
		if ( $cats ) {
			$meta[] = $cats[0]->name;
		}
	}

	$cta = 'case_study' === $item->post_type ? __( 'Read the case study', 'cb-andwisentrances2026' ) : __( 'Read more', 'cb-andwisentrances2026' );
	?>
	<article class="cb-post-card">
		<?php
		if ( $image ) {
			?>
		<div class="cb-post-card__media"><?php echo wp_get_attachment_image( $image, 'medium_large', false, array( 'loading' => 'lazy' ) ); ?></div>
			<?php
		}
		?>
		<div class="cb-post-card__body">
			<?php
			if ( $meta ) {
				?>
			<div class="cb-post-card__meta">
				<?php
				foreach ( $meta as $chip ) {
					?>
				<span><?php echo esc_html( $chip ); ?></span>
					<?php
				}
				?>
			</div>
				<?php
			}
			?>
			<h3><a href="<?php echo esc_url( get_permalink( $item ) ); ?>"><?php echo esc_html( get_the_title( $item ) ); ?></a></h3>
			<?php
			$summary = cbae_card_summary( $item, $words );
			if ( $summary ) {
				?>
			<p><?php echo esc_html( $summary ); ?></p>
				<?php
			}
			?>
			<span class="cb-post-card__cta" aria-hidden="true"><?php echo esc_html( $cta ); ?></span>
		</div>
	</article>
	<?php
}

/**
 * Render one tile card (forest) for a product/service/sector/page.
 *
 * @param array  $card    Row from cbae_posts_to_cards() or a manual Cards row.
 * @param string $variant 'forest' | 'light' | 'sage'.
 * @param string $media   'image' | 'icon' | 'none'.
 * @return void
 */
function cbae_tile( $card, $variant = 'forest', $media = 'image' ) {
	$classes = array( 'cb-tile' );

	if ( 'forest' !== $variant ) {
		$classes[] = 'cb-tile--' . $variant;
	}

	$image_id = (int) ( $card['image_id'] ?? 0 );
	$url      = $card['url'] ?? '';
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php
		if ( $image_id && 'image' === $media ) {
			?>
		<div class="cb-tile__media"><?php echo wp_get_attachment_image( $image_id, 'medium_large', false, array( 'loading' => 'lazy' ) ); ?></div>
			<?php
		} elseif ( $image_id && 'icon' === $media ) {
			?>
		<div class="cb-tile__icon"><?php echo wp_get_attachment_image( $image_id, 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?></div>
			<?php
		}
		if ( ! empty( $card['title'] ) ) {
			?>
		<h3><?php echo esc_html( $card['title'] ); ?></h3>
			<?php
		}
		if ( ! empty( $card['text'] ) ) {
			?>
		<p><?php echo esc_html( $card['text'] ); ?></p>
			<?php
		}
		if ( $url ) {
			?>
		<a class="cb-tile__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $card['link_text'] ?? __( 'Find out more', 'cb-andwisentrances2026' ) ); ?></a>
			<?php
		}
		?>
	</div>
	<?php
}

/**
 * Gravity Forms choices for a form-ID select, or an empty array when the
 * plugin isn't active. Used by the Form block (via REST) and the vacancy
 * application form setting.
 *
 * @return array<int, string> id => title.
 */
function cbae_gf_form_choices() {
	if ( ! class_exists( 'GFAPI' ) ) {
		return array();
	}

	$choices = array();

	foreach ( GFAPI::get_forms( true ) as $form ) {
		$choices[ (int) $form['id'] ] = $form['title'];
	}

	return $choices;
}

/**
 * Render a Gravity Form by ID, or a visible placeholder in the editor /
 * for admins when GF isn't active or the ID is empty.
 *
 * @param int   $form_id Form ID.
 * @param array $prefill Field prefill values (GF field_values).
 * @return void
 */
function cbae_render_form( $form_id, $prefill = array() ) {
	$form_id = absint( $form_id );

	if ( $form_id && function_exists( 'gravity_form' ) ) {
		gravity_form( $form_id, false, false, false, $prefill, true );
		return;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$message = $form_id
		? __( 'Gravity Forms is not active, so this form cannot render yet.', 'cb-andwisentrances2026' )
		: __( 'No form selected yet. Choose one in the block settings.', 'cb-andwisentrances2026' );
	?>
	<p class="cb-empty"><?php echo esc_html( $message ); ?></p>
	<?php
}

/**
 * Expose GF form choices + the vacancy form ID to the block editor so the
 * Form block can offer a dropdown instead of a raw ID.
 *
 * @return void
 */
function cbae_editor_data() {
	wp_add_inline_script(
		'wp-blocks',
		'window.cbaeEditorData = ' . wp_json_encode(
			array(
				'forms' => cbae_gf_form_choices(),
			)
		) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'cbae_editor_data' );

/**
 * Register Yoast-free fallback: nothing to do, Yoast provides per-page SEO
 * title/description fields. Kept as a note so nobody re-adds custom fields.
 */

/**
 * Move "news" posts under /news/ — permalink structure is set on activation
 * (see cbae_activation_defaults()); every CPT is registered with_front =>
 * false so none pick up the /news/ prefix.
 *
 * @return void
 */
function cbae_activation_defaults() {
	if ( get_option( 'permalink_structure' ) !== '/news/%postname%/' ) {
		update_option( 'permalink_structure', '/news/%postname%/' );
	}
}
add_action( 'after_switch_theme', 'cbae_activation_defaults', 5 );

/**
 * Plain hyphens: wptexturize() rewrites " - " to an en dash. House style is
 * a plain hyphen, so revert it on the content/title/excerpt filters.
 *
 * @param string $text Text.
 * @return string
 */
function cbae_plain_hyphens( $text ) {
	return str_replace( array( ' &#8211; ', ' – ' ), ' - ', $text );
}
add_filter( 'the_content', 'cbae_plain_hyphens', 20 );
add_filter( 'the_title', 'cbae_plain_hyphens', 20 );
add_filter( 'the_excerpt', 'cbae_plain_hyphens', 20 );

/**
 * Unique section id for a block instance: the HTML anchor when set, else a
 * generated one so in-page scripts always have a hook.
 *
 * @param array  $attributes Block attributes.
 * @param string $prefix     Prefix for generated IDs.
 * @return string
 */
function cbae_section_id( $attributes, $prefix ) {
	return ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : wp_unique_id( $prefix . '-' );
}

/**
 * Wrapper classes for a block section: block slug, optional background and
 * the editor's extra className.
 *
 * @param array    $attributes Block attributes.
 * @param string   $slug       Block slug, used as the base class.
 * @param string[] $extra      Further classes.
 * @return string
 */
function cbae_section_class( $attributes, $slug, $extra = array() ) {
	$classes = array_merge( array( $slug, 'cb-section' ), $extra );

	if ( ! empty( $attributes['background'] ) && 'paper' !== $attributes['background'] ) {
		$classes[] = 'bg-' . sanitize_html_class( $attributes['background'] );
	}

	if ( ! empty( $attributes['className'] ) ) {
		$classes[] = $attributes['className'];
	}

	return implode( ' ', array_filter( $classes ) );
}

/**
 * Button markup from a pair of text/url attributes.
 *
 * @param string $text   Label.
 * @param string $url    URL.
 * @param string $class  Button classes.
 * @param bool   $target Open in new tab.
 * @return string
 */
function cbae_button( $text, $url, $class = 'btn', $target = false ) {
	if ( ! $url ) {
		return '';
	}

	return '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"' . ( $target ? ' target="_blank" rel="noopener"' : '' ) . '>' . esc_html( $text ? $text : $url ) . '</a>';
}

/**
 * Lines from a textarea attribute, trimmed and with blanks removed.
 *
 * @param string $text Multi-line text.
 * @return string[]
 */
function cbae_lines( $text ) {
	$lines = preg_split( '/\r\n|\n|\r/', (string) $text );

	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

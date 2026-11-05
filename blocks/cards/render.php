<?php
/**
 * Block template for Cards.
 *
 * Every source (manual rows, child pages, a post type, chosen posts, this
 * page's related items) is resolved into the row shape cbae_posts_to_cards()
 * returns, so one loop renders all of them through cbae_tile().
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

$heading       = $attributes['heading'] ?? '';
$intro         = $attributes['intro'] ?? '';
$source        = $attributes['source'] ?? 'post_type';
$post_type     = $attributes['postType'] ?? 'product';
$top_level     = ! isset( $attributes['topLevelOnly'] ) || $attributes['topLevelOnly'];
$limit         = (int) ( $attributes['limit'] ?? 0 );
$selected      = array_values( array_filter( array_map( 'absint', (array) ( $attributes['selected'] ?? array() ) ) ) );
$related_key   = $attributes['relatedKey'] ?? 'related_products';
$rows          = $attributes['cards'] ?? array();
$style         = $attributes['style'] ?? 'forest';
$media         = $attributes['media'] ?? 'image';
$columns       = $attributes['columns'] ?? '3';
$link_text     = $attributes['linkText'] ?? '';
$view_all_text = $attributes['viewAllText'] ?? '';
$view_all_url  = $attributes['viewAllUrl'] ?? '';
$background    = $attributes['background'] ?? 'paper';

$allowed_types = array( 'product', 'service', 'sector', 'case_study', 'page', 'post' );
$pick_types    = array( 'product', 'service', 'sector', 'case_study', 'page' );
$per_page      = $limit > 0 ? $limit : -1;
$cards         = array();

switch ( $source ) {
	case 'manual':
		foreach ( (array) $rows as $row ) {
			if ( empty( $row['title'] ) && empty( $row['text'] ) ) {
				continue;
			}

			$row_link_text = ! empty( $row['linkText'] ) ? $row['linkText'] : ( $link_text ? $link_text : __( 'Find out more', 'cb-andwisentrances2026' ) );

			$cards[] = array(
				'title'     => $row['title'] ?? '',
				'text'      => $row['text'] ?? '',
				'url'       => $row['link'] ?? '',
				'image_id'  => (int) ( $row['image'] ?? 0 ),
				'link_text' => $row_link_text,
			);
		}
		break;

	case 'children':
		$parent_id = get_the_ID();

		if ( $parent_id ) {
			$children = get_posts(
				array(
					'post_type'        => get_post_type( $parent_id ),
					'post_parent'      => $parent_id,
					'post_status'      => 'publish',
					'posts_per_page'   => $per_page,
					'orderby'          => array(
						'menu_order' => 'ASC',
						'title'      => 'ASC',
					),
					'no_found_rows'    => true,
					'suppress_filters' => false,
				)
			);
			$cards    = cbae_posts_to_cards( $children );
		}
		break;

	case 'selected':
		if ( $selected ) {
			$cards = cbae_posts_to_cards(
				cbae_list_posts(
					$pick_types,
					-1,
					array(
						'post__in' => $selected,
						'orderby'  => 'post__in',
					)
				)
			);
		}
		break;

	case 'related':
		$related = get_the_ID() ? cbae_related_ids( get_the_ID(), $related_key ) : array();

		if ( $related ) {
			$cards = cbae_posts_to_cards(
				cbae_list_posts(
					$pick_types,
					-1,
					array(
						'post__in' => $related,
						'orderby'  => 'post__in',
					)
				)
			);
		}
		break;

	case 'post_type':
	default:
		if ( ! in_array( $post_type, $allowed_types, true ) ) {
			$post_type = 'product';
		}

		$cards = cbae_posts_to_cards(
			cbae_list_posts( $post_type, $per_page, $top_level ? array( 'post_parent' => 0 ) : array() )
		);
		break;
}

if ( $link_text ) {
	foreach ( $cards as &$card ) {
		$card['link_text'] = $link_text;
	}
	unset( $card );
}

if ( ! $cards && ! current_user_can( 'edit_posts' ) ) {
	return;
}

// A forest tile on a forest background is invisible; flip to the light tile.
if ( 'forest' === $background && 'forest' === $style ) {
	$style = 'light';
}

if ( ! in_array( $columns, array( '2', '3', '4' ), true ) ) {
	$columns = '3';
}

$section_id = cbae_section_id( $attributes, 'cb-cards' );
$classes    = cbae_section_class( $attributes, 'cb-cards' );
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
		if ( ! $cards ) {
			?>
		<p class="cb-empty"><?php esc_html_e( 'No cards to show yet.', 'cb-andwisentrances2026' ); ?></p>
			<?php
		} else {
			?>
		<div class="cb-card-grid cb-card-grid--<?php echo esc_attr( $columns ); ?>" data-stagger>
			<?php
			foreach ( $cards as $card ) {
				?>
			<div class="cb-fade">
				<?php cbae_tile( $card, $style, $media ); ?>
			</div>
				<?php
			}
			?>
		</div>
			<?php
			if ( $view_all_url ) {
				?>
		<p class="cb-index__more">
			<?php echo cbae_button( $view_all_text ? $view_all_text : __( 'View all', 'cb-andwisentrances2026' ), $view_all_url, 'btn btn-ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cbae_button(). ?>
		</p>
				<?php
			}
		}
		?>
	</div>
</section>

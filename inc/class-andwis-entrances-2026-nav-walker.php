<?php
/**
 * Primary nav walker.
 *
 * Top-level items with children become dropdown-toggle buttons (they never
 * navigate - the hub link lives inside the dropdown as its heading). The
 * dropdown's shape comes from a CSS class set on the parent menu item in
 * Appearance > Menus, matching the three UX designs:
 *
 *   mega   Two panels: an emerald list of the children on the left and
 *          "Case study examples" on the right, which swap to the case
 *          studies related to whichever child is hovered or focused.
 *   bar    A full-width emerald strip with the children centred in a row.
 *   (none) A forest panel hanging from the right of the bar: the hub link
 *          as its heading, then the children.
 *
 * The dropdown heading is the parent's "Title Attribute" field when set
 * (so Products can read "By product", About can read "About andwis
 * entrances"), else the parent's title; it links to the parent's URL unless
 * that is "#".
 *
 * On mobile every style collapses to the same nested list; the examples
 * panel is hidden there by CSS.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Andwis_Entrances_2026_Nav_Walker' ) ) {

	/**
	 * Custom nav walker.
	 */
	class Andwis_Entrances_2026_Nav_Walker extends Walker_Nav_Menu {

		/**
		 * Parent item of the submenu currently being written.
		 *
		 * @var WP_Post|null
		 */
		protected $parent = null;

		/**
		 * Dropdown style of the submenu currently being written.
		 *
		 * @var string mega|bar|panel
		 */
		protected $style = 'panel';

		/**
		 * Children collected while writing a mega submenu, for the examples
		 * panel appended in end_lvl().
		 *
		 * @var WP_Post[]
		 */
		protected $mega_children = array();

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param string   $output Passed by reference.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu args.
		 * @return void
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$id      = 'dropdown-' . $this->parent->ID;
			$output .= '<div class="dropdown-menu dropdown-menu--' . esc_attr( $this->style ) . '" id="' . esc_attr( $id ) . '">';

			if ( 'mega' === $this->style ) {
				$output .= '<div class="dropdown-mega__nav">' . $this->heading();
			} elseif ( 'panel' === $this->style ) {
				$output .= $this->heading();
			}

			$output .= '<ul class="dropdown-list">';
		}

		/**
		 * Ends the list after the elements are added.
		 *
		 * @param string   $output Passed by reference.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu args.
		 * @return void
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			$output .= '</ul>';

			if ( 'mega' === $this->style ) {
				$output .= '</div>' . $this->examples();
			}

			$output .= '</div>';
		}

		/**
		 * Starts the element output.
		 *
		 * @param string   $output Passed by reference.
		 * @param WP_Post  $item   Menu item.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu args.
		 * @param int      $id     Menu item ID.
		 * @return void
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes      = (array) $item->classes;
			$has_children = in_array( 'menu-item-has-children', $classes, true );
			$is_current   = in_array( 'current-menu-item', $classes, true );

			if ( 0 === $depth ) {
				$li_classes = array( 'nav-item' );

				if ( $has_children ) {
					$this->parent        = $item;
					$this->style         = in_array( 'mega', $classes, true ) ? 'mega' : ( in_array( 'bar', $classes, true ) ? 'bar' : 'panel' );
					$this->mega_children = array();
					$li_classes[]        = 'dropdown';
					$li_classes[]        = 'dropdown--' . $this->style;
				}

				if ( in_array( 'button', $classes, true ) ) {
					$li_classes[] = 'button';
				}

				$output .= '<li class="' . esc_attr( implode( ' ', $li_classes ) ) . '">';

				if ( $has_children ) {
					$output .= '<button type="button" class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="dropdown-' . esc_attr( $item->ID ) . '">';
					$output .= '<span>' . esc_html( $item->title ) . '</span>';
					$output .= '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" /></svg>';
					$output .= '</button>';
					return;
				}
			} else {
				$output .= '<li>';

				if ( 'mega' === $this->style ) {
					$this->mega_children[] = $item;
				}
			}

			$link_classes = array( 'nav-link' );
			if ( $is_current ) {
				$link_classes[] = 'active';
			}

			$output .= '<a class="' . esc_attr( implode( ' ', $link_classes ) ) . '" href="' . esc_url( $item->url ) . '"';
			if ( $is_current ) {
				$output .= ' aria-current="page"';
			}
			if ( $depth > 0 && 'mega' === $this->style ) {
				$output .= ' data-example="' . esc_attr( $item->ID ) . '"';
			}
			$output .= '>' . esc_html( $item->title ) . '</a>';
		}

		/**
		 * Ends the element output.
		 *
		 * @param string   $output Passed by reference.
		 * @param WP_Post  $item   Menu item.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu args.
		 * @return void
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= '</li>';
		}

		/**
		 * The dropdown heading: Title Attribute or title, linked to the hub.
		 *
		 * @return string
		 */
		protected function heading() {
			$text = trim( (string) $this->parent->attr_title );
			if ( '' === $text ) {
				$text = $this->parent->title;
			}

			$url = (string) $this->parent->url;
			$inner = esc_html( $text ) . ' ' . $this->chevron();

			if ( '' === $url || '#' === $url ) {
				return '<span class="dropdown-heading">' . $inner . '</span>';
			}

			return '<a class="dropdown-heading" href="' . esc_url( $url ) . '">' . $inner . '</a>';
		}

		/**
		 * Small right chevron used after headings and "See all" links.
		 *
		 * @return string
		 */
		protected function chevron() {
			return '<svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M4.5 2.5 8 6l-3.5 3.5" /></svg>';
		}

		/**
		 * The "Case study examples" panel for a mega dropdown: one hidden set
		 * per child, showing the case studies related to it (falling back to
		 * the latest), which src/js/nav-dropdown.js reveals on hover/focus.
		 * Skipped entirely when the site has no case studies yet.
		 *
		 * @return string
		 */
		protected function examples() {
			if ( ! $this->mega_children ) {
				return '';
			}

			$latest = get_posts(
				array(
					'post_type'      => 'case_study',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'no_found_rows'  => true,
				)
			);

			if ( ! $latest ) {
				return '';
			}

			$hub      = get_page_by_path( 'case-studies' );
			$hub_url  = $hub ? get_permalink( $hub ) : '';
			$see_all  = $hub_url ? '<a class="dropdown-mega__all" href="' . esc_url( $hub_url ) . '">' . esc_html__( 'See all case studies', 'cb-andwisentrances2026' ) . ' ' . $this->chevron() . '</a>' : '';
			$heading  = esc_html__( 'Case study examples', 'cb-andwisentrances2026' ) . ' ' . $this->chevron();
			$heading  = $hub_url ? '<a class="dropdown-heading" href="' . esc_url( $hub_url ) . '">' . $heading . '</a>' : '<span class="dropdown-heading">' . $heading . '</span>';
			$out      = '<div class="dropdown-mega__examples">' . $heading;
			$out     .= '<p class="dropdown-mega__for" data-example-label>' . esc_html( $this->mega_children[0]->title ) . '</p>';

			foreach ( $this->mega_children as $i => $child ) {
				$key   = 'sector' === $child->object ? 'related_sectors' : 'related_products';
				$items = 'post_type' === $child->type ? cbae_posts_referencing( 'case_study', $key, $child->object_id, 2 ) : array();
				if ( ! $items ) {
					$items = $latest;
				}

				$out .= '<ul class="dropdown-mega__set" data-example-set="' . esc_attr( $child->ID ) . '" data-example-title="' . esc_attr( $child->title ) . '"' . ( $i ? ' hidden' : '' ) . '>';
				foreach ( $items as $case ) {
					$sub = trim( implode( ', ', array_filter( array( get_post_meta( $case->ID, 'client_name', true ), get_post_meta( $case->ID, 'location', true ) ) ) ) );
					if ( '' === $sub ) {
						$sub = cbae_card_summary( $case, 10 );
					}
					$out .= '<li><a class="dropdown-mega__case" href="' . esc_url( get_permalink( $case ) ) . '">';
					$out .= cbae_icon( 'document' );
					$out .= '<span><strong>' . esc_html( get_the_title( $case ) ) . '</strong>';
					if ( $sub ) {
						$out .= '<small>' . esc_html( $sub ) . '</small>';
					}
					$out .= '</span></a></li>';
				}
				$out .= '</ul>';
			}

			return $out . $see_all . '</div>';
		}
	}
}

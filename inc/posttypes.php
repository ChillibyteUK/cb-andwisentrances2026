<?php
/**
 * Custom Post Types Registration
 *
 * Content model for andwis entrances:
 *
 * | Post type    | URL                                   | Hub                                   |
 * | ------------ | ------------------------------------- | ------------------------------------- |
 * | product      | /products/{hub}/{slug}/ (hierarchical) | Top-level product = hub (e.g. /products/industrial-doors/) |
 * | service      | /service-maintenance/{slug}/           | Page at /service-maintenance/         |
 * | sector       | /sectors/{slug}/                       | Page at /sectors/                     |
 * | case_study   | /case-studies/{slug}/                  | Page at /case-studies/                |
 * | vacancy      | /careers/vacancies/{slug}/             | Page at /careers/                     |
 * | post (news)  | /news/{slug}/                          | Posts page at /news/ (index.php)      |
 *
 * Products are hierarchical so the four product hubs and their product pages
 * live in one post type: a hub is simply a top-level product, and the Cards
 * block's "children of this page" source lists its products. Everything is
 * built from blocks and rendered through page.php, except case studies and
 * vacancies which have fixed-shape single templates.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared post type args.
 *
 * @param array $labels   Labels (name, singular_name).
 * @param array $override Args to merge over the defaults.
 * @return array
 */
function cbae_post_type_args( $labels, $override ) {
	$name     = $labels['name'];
	$singular = $labels['singular_name'];

	return array_merge(
		array(
			'labels'              => array(
				'name'               => $name,
				'singular_name'      => $singular,
				'add_new_item'       => 'Add New ' . $singular,
				'edit_item'          => 'Edit ' . $singular,
				'new_item'           => 'New ' . $singular,
				'view_item'          => 'View ' . $singular,
				'search_items'       => 'Search ' . $name,
				'not_found'          => 'No ' . strtolower( $name ) . ' found',
				'not_found_in_trash' => 'No ' . strtolower( $name ) . ' in trash',
				'parent_item_colon'  => 'Parent ' . $singular . ':',
			),
			'has_archive'         => false,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_in_nav_menus'   => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'custom-fields' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		),
		$override
	);
}

/**
 * Register custom post types for the theme.
 *
 * @return void
 */
function cb_andwisentrances2026_register_post_types() {
	register_post_type(
		'product',
		cbae_post_type_args(
			array(
				'name'          => 'Products',
				'singular_name' => 'Product',
			),
			array(
				'hierarchical'  => true,
				'menu_position' => 21,
				'menu_icon'     => 'dashicons-align-wide',
				'rewrite'       => array(
					'slug'       => 'products',
					'with_front' => false,
				),
			)
		)
	);

	register_post_type(
		'service',
		cbae_post_type_args(
			array(
				'name'          => 'Services',
				'singular_name' => 'Service',
			),
			array(
				'menu_position' => 22,
				'menu_icon'     => 'dashicons-admin-tools',
				'rewrite'       => array(
					'slug'       => 'service-maintenance',
					'with_front' => false,
				),
			)
		)
	);

	register_post_type(
		'sector',
		cbae_post_type_args(
			array(
				'name'          => 'Sectors',
				'singular_name' => 'Sector',
			),
			array(
				'menu_position' => 23,
				'menu_icon'     => 'dashicons-building',
				'rewrite'       => array(
					'slug'       => 'sectors',
					'with_front' => false,
				),
			)
		)
	);

	register_post_type(
		'case_study',
		cbae_post_type_args(
			array(
				'name'          => 'Case Studies',
				'singular_name' => 'Case Study',
			),
			array(
				'menu_position' => 24,
				'menu_icon'     => 'dashicons-portfolio',
				'rewrite'       => array(
					'slug'       => 'case-studies',
					'with_front' => false,
				),
			)
		)
	);

	register_post_type(
		'vacancy',
		cbae_post_type_args(
			array(
				'name'          => 'Vacancies',
				'singular_name' => 'Vacancy',
			),
			array(
				'menu_position' => 25,
				'menu_icon'     => 'dashicons-groups',
				'rewrite'       => array(
					'slug'       => 'careers/vacancies',
					'with_front' => false,
				),
			)
		)
	);
}
add_action( 'init', 'cb_andwisentrances2026_register_post_types' );

/**
 * Flush rewrite rules on theme activation so the custom post type slugs work.
 *
 * @return void
 */
function cbae_flush_rewrites() {
	cb_andwisentrances2026_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'cbae_flush_rewrites' );

/**
 * Serve page.php for singular views of any custom post type that has no
 * single-{post_type}.php of its own, instead of falling back to the
 * generic single.php. CPT content in this theme is built from the same
 * blocks as pages — single.php's hero/date treatment is for news posts.
 *
 * @param string $template Template path WordPress would otherwise use.
 * @return string
 */
function cb_andwisentrances2026_use_page_template_for_cpts( $template ) {
	if ( is_singular() && ! is_page() && ! is_singular( 'post' ) && 'single.php' === basename( $template ) ) {
		$page_template = get_query_template( 'page' );

		if ( $page_template ) {
			return $page_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'cb_andwisentrances2026_use_page_template_for_cpts' );

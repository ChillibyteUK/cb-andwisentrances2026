<?php
/**
 * Post meta — the ACF-free replacement for the "listing details" and
 * "case study details" field groups on andwis lifts.
 *
 * Every field is registered with show_in_rest so the block editor can read
 * and write it through useEntityProp(). The editing UI is the document
 * sidebar panel in blocks/_sidebar/src/index.js (enqueued below) — one panel
 * per post type, fields declared once in cbae_meta_fields() and mirrored to
 * the JS through wp_localize_script(), so adding a field is a one-place job.
 *
 * Read with get_post_meta( $id, 'card_summary', true ) etc. Relationship
 * fields (related_*) are arrays of post IDs.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta field definitions, keyed by post type. '*' applies to every block-built
 * type (pages, products, services, sectors, case studies).
 *
 * type: string | integer | boolean | ids (array of post IDs) | select | image
 * (attachment ID) | date (Y-m-d string).
 *
 * @return array
 */
function cbae_meta_fields() {
	return array(
		'*'          => array(
			'card_summary'    => array(
				'label' => 'Card summary',
				'type'  => 'string',
				'help'  => 'One short sentence shown wherever this page appears as a card (hubs, home page tiles, related lists). Falls back to the excerpt.',
				'multiline' => true,
			),
			'card_icon'       => array(
				'label' => 'Card icon / image',
				'type'  => 'image',
				'help'  => 'Optional. Falls back to the featured image.',
			),
			'hide_global_cta' => array(
				'label' => 'Hide the site-wide "Get a Quote / Book a Survey" band above the footer',
				'type'  => 'boolean',
			),
		),
		'product'    => array(
			'related_sectors' => array(
				'label'     => 'Related sectors',
				'type'      => 'ids',
				'post_type' => 'sector',
				'help'      => 'Sectors this product is most relevant to. Shown as "Where it is used" on the product page.',
			),
		),
		'service'    => array(
			'related_sectors' => array(
				'label'     => 'Related sectors',
				'type'      => 'ids',
				'post_type' => 'sector',
			),
		),
		'sector'     => array(
			'related_products' => array(
				'label'     => 'Relevant products & services',
				'type'      => 'ids',
				'post_type' => array( 'product', 'service' ),
				'help'      => 'Listed by a Cards block set to "Related to this page", in this order.',
			),
		),
		'case_study' => array(
			'client_name'      => array(
				'label' => 'Client / project name',
				'type'  => 'string',
				'help'  => 'Shown under the title. Leave empty where the client must stay anonymous.',
			),
			'location'         => array(
				'label' => 'Location',
				'type'  => 'string',
			),
			'completed'        => array(
				'label' => 'Completed',
				'type'  => 'string',
				'help'  => 'e.g. 2025',
			),
			'related_sectors'  => array(
				'label'     => 'Sectors',
				'type'      => 'ids',
				'post_type' => 'sector',
				'help'      => 'Drives the sector filter on the case studies hub and the related case studies on each sector page.',
			),
			'related_products' => array(
				'label'     => 'Products & services',
				'type'      => 'ids',
				'post_type' => array( 'product', 'service' ),
				'help'      => 'Drives the product filter and the related case studies on each product page.',
			),
		),
		'vacancy'    => array(
			'employment_type'  => array(
				'label'   => 'Employment type',
				'type'    => 'select',
				'options' => array(
					'FULL_TIME'  => 'Full time',
					'PART_TIME'  => 'Part time',
					'CONTRACTOR' => 'Contractor',
					'TEMPORARY'  => 'Temporary',
					'INTERN'     => 'Intern',
					'OTHER'      => 'Other',
				),
				'default' => 'FULL_TIME',
			),
			'tenure'           => array(
				'label'   => 'Tenure',
				'type'    => 'select',
				'options' => array(
					'permanent'      => 'Permanent',
					'temporary'      => 'Temporary',
					'contract'       => 'Contract',
					'fixed_term'     => 'Fixed term',
					'apprenticeship' => 'Apprenticeship',
				),
				'default' => 'permanent',
			),
			'location_type'    => array(
				'label'   => 'Location type',
				'type'    => 'select',
				'options' => array(
					'office' => 'On site',
					'hybrid' => 'Hybrid',
					'remote' => 'Remote',
				),
				'default' => 'office',
			),
			'address_locality' => array(
				'label' => 'Town / city',
				'type'  => 'string',
			),
			'address_region'   => array(
				'label' => 'Region / county',
				'type'  => 'string',
			),
			'postal_code'      => array(
				'label' => 'Postcode',
				'type'  => 'string',
			),
			'salary_type'      => array(
				'label'   => 'Salary',
				'type'    => 'select',
				'options' => array(
					'range'      => 'Salary range',
					'negotiable' => 'Competitive / on application',
				),
				'default' => 'range',
			),
			'minimum_salary'   => array(
				'label' => 'Minimum salary (£)',
				'type'  => 'integer',
			),
			'maximum_salary'   => array(
				'label' => 'Maximum salary (£)',
				'type'  => 'integer',
			),
			'salary_unit'      => array(
				'label'   => 'Salary period',
				'type'    => 'select',
				'options' => array(
					'YEAR'  => 'Per year',
					'MONTH' => 'Per month',
					'WEEK'  => 'Per week',
					'DAY'   => 'Per day',
					'HOUR'  => 'Per hour',
				),
				'default' => 'YEAR',
			),
			'valid_through'    => array(
				'label' => 'Closing date',
				'type'  => 'date',
				'help'  => 'Roles past this date drop out of the careers listing automatically.',
			),
		),
	);
}

/**
 * Post types that carry the shared '*' fields.
 *
 * @return string[]
 */
function cbae_meta_post_types() {
	return array( 'page', 'product', 'service', 'sector', 'case_study', 'vacancy', 'post' );
}

/**
 * Register every field with the REST API.
 *
 * @return void
 */
function cbae_register_meta() {
	$fields = cbae_meta_fields();

	foreach ( cbae_meta_post_types() as $post_type ) {
		$type_fields = array_merge( $fields['*'], $fields[ $post_type ] ?? array() );

		foreach ( $type_fields as $key => $field ) {
			$args = array(
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			);

			switch ( $field['type'] ) {
				case 'ids':
					$args['type']         = 'array';
					$args['default']      = array();
					$args['show_in_rest'] = array(
						'schema' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'integer' ),
						),
					);
					$args['sanitize_callback'] = function ( $value ) {
						return array_values( array_filter( array_map( 'absint', (array) $value ) ) );
					};
					break;
				case 'integer':
				case 'image':
					$args['type']              = 'integer';
					$args['default']           = 0;
					$args['sanitize_callback'] = 'absint';
					break;
				case 'boolean':
					$args['type']    = 'boolean';
					$args['default'] = false;
					break;
				default:
					$args['type']              = 'string';
					$args['default']           = $field['default'] ?? '';
					$args['sanitize_callback'] = 'sanitize_text_field';
			}

			register_post_meta( $post_type, $key, $args );
		}
	}
}
add_action( 'init', 'cbae_register_meta' );

/**
 * Enqueue the document-sidebar panel (blocks/_sidebar) and hand it the
 * field definitions for the post type being edited.
 *
 * @return void
 */
function cbae_enqueue_meta_sidebar() {
	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->post_type, cbae_meta_post_types(), true ) ) {
		return;
	}

	$asset_file = CB_ANDWISENTRANCES2026_DIR . '/blocks/_sidebar/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset  = require $asset_file;
	$fields = cbae_meta_fields();

	wp_enqueue_script(
		'cb-andwisentrances2026-meta-sidebar',
		get_template_directory_uri() . '/blocks/_sidebar/build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);

	wp_localize_script(
		'cb-andwisentrances2026-meta-sidebar',
		'cbaeMetaFields',
		array(
			'postType' => $screen->post_type,
			'fields'   => array_merge( $fields['*'], $fields[ $screen->post_type ] ?? array() ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'cbae_enqueue_meta_sidebar' );

/**
 * Admin-side styles for the sidebar panel (the editor iframe stylesheet
 * doesn't reach the document sidebar).
 *
 * @return void
 */
function cbae_enqueue_admin_css() {
	$abs = CB_ANDWISENTRANCES2026_DIR . '/css/admin.css';
	if ( file_exists( $abs ) ) {
		wp_enqueue_style( 'cb-andwisentrances2026-admin', get_template_directory_uri() . '/css/admin.css', array(), filemtime( $abs ) );
	}
}
add_action( 'enqueue_block_editor_assets', 'cbae_enqueue_admin_css' );

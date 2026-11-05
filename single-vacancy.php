<?php
/**
 * Single vacancy.
 *
 * Fixed structure HR fill in through the Vacancy Details sidebar panel plus
 * the block editor for the role copy (headings + paragraphs/lists), with the
 * application form (Gravity Forms, ID from Site-Wide Settings > Forms) in a
 * sticky aside. Emits Google JobPosting JSON-LD, ported from andwis lifts.
 *
 * @package cb-andwisentrances2026
 */

get_header();
the_post();

$job_id = get_the_ID();
$meta   = fn( $key ) => get_post_meta( $job_id, $key, true );

$employment_type = $meta( 'employment_type' ) ? $meta( 'employment_type' ) : 'FULL_TIME';
$tenure          = $meta( 'tenure' ) ? $meta( 'tenure' ) : 'permanent';
$location_type   = $meta( 'location_type' ) ? $meta( 'location_type' ) : 'office';
$locality        = $meta( 'address_locality' );
$region          = $meta( 'address_region' );
$postcode        = $meta( 'postal_code' );
$salary_type     = $meta( 'salary_type' ) ? $meta( 'salary_type' ) : 'range';
$min_salary      = (int) $meta( 'minimum_salary' );
$max_salary      = (int) $meta( 'maximum_salary' );
$salary_unit     = $meta( 'salary_unit' ) ? $meta( 'salary_unit' ) : 'YEAR';
$valid_through   = $meta( 'valid_through' );
$form_id         = (int) cbae_setting( 'vacancy_form_id' );
$hub             = get_page_by_path( 'careers' );

$fields        = cbae_meta_fields()['vacancy'];
$emp_label     = $fields['employment_type']['options'][ $employment_type ] ?? $employment_type;
$tenure_label  = $fields['tenure']['options'][ $tenure ] ?? $tenure;
$unit_map      = array(
	'YEAR'  => 'per year',
	'MONTH' => 'per month',
	'WEEK'  => 'per week',
	'DAY'   => 'per day',
	'HOUR'  => 'per hour',
);

if ( 'remote' === $location_type ) {
	$location_label = __( 'Remote', 'cb-andwisentrances2026' );
} elseif ( 'hybrid' === $location_type ) {
	$location_label = __( 'Hybrid', 'cb-andwisentrances2026' ) . ( $locality ? ' / ' . $locality : '' );
} else {
	$location_label = $locality ? $locality : __( 'On site', 'cb-andwisentrances2026' );
}

if ( 'range' === $salary_type && ( $min_salary || $max_salary ) ) {
	$unit_str = $unit_map[ $salary_unit ] ?? strtolower( $salary_unit );
	if ( $min_salary && $max_salary ) {
		$salary_label = '£' . number_format( $min_salary ) . ' - £' . number_format( $max_salary ) . ' ' . $unit_str;
	} else {
		$salary_label = '£' . number_format( $min_salary ? $min_salary : $max_salary ) . '+ ' . $unit_str;
	}
} else {
	$salary_label = __( 'Competitive', 'cb-andwisentrances2026' );
}

$badges = array_filter( array( $location_label, $tenure_label, $emp_label, $salary_label ) );
?>
<section class="cb-page-head cb-vacancy__head">
	<div class="container">
		<?php cb_andwisentrances2026_render_breadcrumbs( cbae_breadcrumbs(), 'cb-breadcrumbs' ); ?>
		<span class="eyebrow"><?php esc_html_e( 'Vacancy', 'cb-andwisentrances2026' ); ?></span>
		<h1><?php the_title(); ?></h1>
		<ul class="cb-vacancy__badges">
			<?php
			foreach ( $badges as $badge ) {
				?>
			<li><?php echo esc_html( $badge ); ?></li>
				<?php
			}
			if ( $valid_through ) {
				?>
			<li><?php esc_html_e( 'Closes', 'cb-andwisentrances2026' ); ?> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $valid_through ) ) ); ?></li>
				<?php
			}
			?>
		</ul>
	</div>
</section>

<section class="cb-section cb-vacancy">
	<div class="container">
		<div class="row gap-5">
			<div class="col-12 col-lg-7 cb-vacancy__body cb-prose">
				<?php the_content(); ?>
				<?php
				$equality = cbae_setting( 'equality_statement' );
				if ( $equality ) {
					?>
				<div class="cb-vacancy__equality"><?php echo wp_kses_post( wpautop( $equality ) ); ?></div>
					<?php
				}
				if ( $hub ) {
					?>
				<p><a class="btn-link" href="<?php echo esc_url( get_permalink( $hub ) ); ?>"><?php esc_html_e( 'All vacancies', 'cb-andwisentrances2026' ); ?> <?php echo cbae_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p>
					<?php
				}
				?>
			</div>
			<aside class="col-12 col-lg-5">
				<div class="cb-vacancy__aside cb-form">
					<h2><?php esc_html_e( 'Apply for this role', 'cb-andwisentrances2026' ); ?></h2>
					<?php cbae_render_form( $form_id, array( 'role' => get_the_title() ) ); ?>
				</div>
			</aside>
		</div>
	</div>
</section>
<?php
$schema = array(
	'@context'           => 'https://schema.org/',
	'@type'              => 'JobPosting',
	'title'              => get_the_title(),
	'description'        => wp_strip_all_tags( cb_andwisentrances2026_content_excerpt( $job_id, 120 ) ),
	'datePosted'         => get_the_date( 'Y-m-d' ),
	'employmentType'     => $employment_type,
	'hiringOrganization' => array(
		'@type'  => 'Organization',
		'name'   => get_bloginfo( 'name' ),
		'sameAs' => home_url(),
	),
);

if ( $valid_through ) {
	$schema['validThrough'] = $valid_through . 'T00:00:00Z';
}

if ( 'remote' === $location_type ) {
	$schema['jobLocationType']               = 'TELECOMMUTE';
	$schema['applicantLocationRequirements'] = array(
		'@type' => 'Country',
		'name'  => 'United Kingdom',
	);
} else {
	$schema['jobLocation'] = array(
		'@type'   => 'Place',
		'address' => array_merge(
			array( '@type' => 'PostalAddress' ),
			array_filter(
				array(
					'addressLocality' => $locality,
					'addressRegion'   => $region,
					'postalCode'      => $postcode,
					'addressCountry'  => 'GB',
				)
			)
		),
	);
}

if ( 'range' === $salary_type && ( $min_salary || $max_salary ) ) {
	$schema['baseSalary'] = array(
		'@type'    => 'MonetaryAmount',
		'currency' => 'GBP',
		'value'    => array(
			'@type'    => 'QuantitativeValue',
			'minValue' => (float) $min_salary,
			'maxValue' => (float) $max_salary,
			'unitText' => $salary_unit,
		),
	);
}

echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

get_footer();

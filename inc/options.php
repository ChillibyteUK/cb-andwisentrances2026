<?php
/**
 * Site-Wide Settings page — plain Settings API, one array option
 * (cb_andwisentrances2026_site_settings). Replaces the old ACF options page; read
 * values elsewhere in the theme with cb_andwisentrances2026_get_setting( $key ).
 *
 * The Icons tab (SVG upload straight into img/icons/) from the ACF version
 * of this page is deliberately not ported here — deferred to a future
 * plugin rather than rebuilt as part of dropping ACF.
 *
 * Includes field types beyond plain text/email/url inputs — `textarea`/
 * `code` (multi-line values, e.g. a pasted vendor script), `checkbox`,
 * `gallery` (a fixed multi-image list, e.g. an accreditation badge row) and
 * `repeater` (genuinely repeating structured rows, e.g. a client-logo
 * list) — plus a generic tabs pattern once there are enough sections to
 * make one long scrolling page unwieldy. The Scripts tab's three raw markup
 * slots (`custom_head`, `custom_body_open`, `custom_body_close`), gated by
 * `custom_scripts_logged_out_only`, are printed unescaped by design — see
 * inc/head-tags.php. The example fields below
 * (`example_gallery`, `example_repeater`) exist to demonstrate both field
 * types working end to end; rename or replace them with real per-project
 * fields.
 *
 * @package cb-andwisentrances2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option name for the single serialized settings array.
 *
 * @var string
 */
define( 'CB_ANDWISENTRANCES2026_SETTINGS_OPTION', 'cb_andwisentrances2026_site_settings' );

/**
 * Read one Site-Wide Settings value.
 *
 * @param string $key     Setting key, e.g. 'ga_property'.
 * @param string $default Fallback if the key isn't set.
 * @return string
 */
function cb_andwisentrances2026_get_setting( $key, $default = '' ) {
	$settings = get_option( CB_ANDWISENTRANCES2026_SETTINGS_OPTION, array() );
	return isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : $default;
}

/**
 * Read a `gallery`-type setting as an array of attachment IDs.
 *
 * Stored as a CSV string (see cb_andwisentrances2026_render_gallery_field()) — order
 * is the display order.
 *
 * @param string $key Setting key, e.g. 'example_gallery'.
 * @return int[]
 */
function cb_andwisentrances2026_get_gallery_setting( $key ) {
	$ids = array_filter( array_map( 'absint', explode( ',', cb_andwisentrances2026_get_setting( $key ) ) ) );
	return array_values( $ids );
}

/**
 * Register the settings page, section, and fields.
 *
 * @return void
 */
function cb_andwisentrances2026_register_settings_page() {
	add_menu_page(
		'Site-Wide Settings',
		'Site-Wide Settings',
		'edit_posts',
		'theme-general-settings',
		'cb_andwisentrances2026_render_settings_page',
		'dashicons-admin-generic',
		80
	);

	register_setting( 'cb_andwisentrances2026_settings', CB_ANDWISENTRANCES2026_SETTINGS_OPTION );

	add_settings_section( 'cb_andwisentrances2026_general', 'General', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_ctas', 'CTAs', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_social', 'Social', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_legal', 'Legal', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_forms', 'Forms', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_tracking', 'Tracking & Verification', '__return_false', 'theme-general-settings' );
	add_settings_section( 'cb_andwisentrances2026_scripts', 'Scripts', '__return_false', 'theme-general-settings' );

	$fields = array(
		'logo'                     => array(
			'label'       => 'Logo',
			'type'        => 'gallery',
			'section'     => 'cb_andwisentrances2026_general',
			'description' => 'One image (SVG or PNG). Shown in the header; leave empty to use the built-in andwis entrances wordmark.',
		),
		'logo_footer'              => array(
			'label'       => 'Footer logo',
			'type'        => 'gallery',
			'section'     => 'cb_andwisentrances2026_general',
			'description' => 'Optional white-on-forest version for the footer. Falls back to the logo above.',
		),
		'tagline'                  => array(
			'label'       => 'Tagline',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_general',
			'placeholder' => 'Automatic, industrial and revolving doors - installed, serviced and maintained.',
			'description' => 'Shown under the logo in the footer.',
		),
		'group_line'               => array(
			'label'       => 'Group line',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_general',
			'placeholder' => 'An andwis Group company',
		),
		'group_url'                => array(
			'label'       => 'Group URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_general',
			'placeholder' => 'https://www.andwis.com',
		),
		'email'                    => array(
			'label'   => 'Email',
			'type'    => 'email',
			'section' => 'cb_andwisentrances2026_general',
		),
		'phone'                    => array(
			'label'   => 'Phone',
			'type'    => 'text',
			'section' => 'cb_andwisentrances2026_general',
		),
		'address'                  => array(
			'label'       => 'Address',
			'type'        => 'textarea',
			'section'     => 'cb_andwisentrances2026_general',
			'description' => 'One line per line. Shown in the footer.',
		),
		'quote_cta_text'           => array(
			'label'       => 'Quote button text',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_ctas',
			'placeholder' => 'Get a Quote',
		),
		'quote_cta_url'            => array(
			'label'       => 'Quote button URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_ctas',
			'description' => 'Defaults to the page with the slug get-a-quote.',
		),
		'survey_cta_text'          => array(
			'label'       => 'Survey button text',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_ctas',
			'placeholder' => 'Book a Survey',
		),
		'survey_cta_url'           => array(
			'label'       => 'Survey button URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_ctas',
			'description' => 'Defaults to the page with the slug book-a-survey.',
		),
		'cta_heading'              => array(
			'label'       => 'Site-wide band heading',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_ctas',
			'placeholder' => 'Ready to talk about your entrance?',
			'description' => 'The Get a Quote / Book a Survey band shown above the footer on every page.',
		),
		'cta_text'                 => array(
			'label'   => 'Site-wide band text',
			'type'    => 'textarea',
			'section' => 'cb_andwisentrances2026_ctas',
			'rows'    => 3,
		),
		'linkedin_url'             => array(
			'label'       => 'LinkedIn URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_social',
			'placeholder' => 'https://www.linkedin.com/company/...',
			'description' => 'Leave blank to hide this icon.',
		),
		'facebook_url'             => array(
			'label'       => 'Facebook URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_social',
			'placeholder' => 'https://facebook.com/...',
		),
		'instagram_url'            => array(
			'label'       => 'Instagram URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_social',
			'placeholder' => 'https://instagram.com/...',
		),
		'x_url'                    => array(
			'label'       => 'X (Twitter) URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_social',
			'placeholder' => 'https://x.com/...',
		),
		'youtube_url'              => array(
			'label'       => 'YouTube URL',
			'type'        => 'url',
			'section'     => 'cb_andwisentrances2026_social',
			'placeholder' => 'https://www.youtube.com/@...',
		),
		'registered_name'          => array(
			'label'       => 'Registered company name',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_legal',
			'placeholder' => 'e.g. andwis entrances Limited',
		),
		'company_number'           => array(
			'label'   => 'Company registration number',
			'type'    => 'text',
			'section' => 'cb_andwisentrances2026_legal',
		),
		'place_of_registration'    => array(
			'label'       => 'Place of registration',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_legal',
			'placeholder' => 'England and Wales',
		),
		'vat_number'               => array(
			'label'   => 'VAT registration number',
			'type'    => 'text',
			'section' => 'cb_andwisentrances2026_legal',
		),
		'registered_office'        => array(
			'label'       => 'Registered office address',
			'type'        => 'textarea',
			'section'     => 'cb_andwisentrances2026_legal',
			'description' => 'One line per line. Shown in the footer as a single line, separated by commas.',
		),
		'vacancy_form_id'          => array(
			'label'       => 'Vacancy application form ID',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_forms',
			'description' => 'Gravity Forms ID of the careers application form, shown beside every vacancy.',
		),
		'equality_statement'       => array(
			'label'       => 'Equality & inclusion statement',
			'type'        => 'textarea',
			'section'     => 'cb_andwisentrances2026_forms',
			'description' => 'Appended to every vacancy.',
		),
		'ga_property'              => array(
			'label'       => 'GA Property',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_tracking',
			'placeholder' => 'G-XXXXXXX',
			'description' => 'Google Analytics measurement ID. Only fires for logged-out visitors.',
		),
		'gtm_property'             => array(
			'label'       => 'GTM Property',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_tracking',
			'placeholder' => 'GTM-XXXXXXX',
			'description' => 'Google Tag Manager container ID. Only fires for logged-out visitors.',
		),
		'google_site_verification' => array(
			'label'       => 'Google Site Verification',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_tracking',
			'description' => 'Content value of the google-site-verification meta tag.',
		),
		'bing_site_verification'   => array(
			'label'       => 'Bing Site Verification',
			'type'        => 'text',
			'section'     => 'cb_andwisentrances2026_tracking',
			'description' => 'Content value of the msvalidate.01 meta tag.',
		),
		'custom_head'              => array(
			'label'       => 'Head',
			'type'        => 'code',
			'section'     => 'cb_andwisentrances2026_scripts',
			'description' => 'Printed in <head> after the managed GA/GTM tags. Paste vendor snippets verbatim, <script> tags and all - nothing is escaped or filtered.',
		),
		'custom_body_open'         => array(
			'label'       => 'Body Open',
			'type'        => 'code',
			'section'     => 'cb_andwisentrances2026_scripts',
			'description' => 'Printed immediately after <body> opens, after the GTM noscript fallback.',
		),
		'custom_body_close'        => array(
			'label'       => 'Body Close',
			'type'        => 'code',
			'section'     => 'cb_andwisentrances2026_scripts',
			'description' => 'Printed just before </body>.',
		),
		'custom_scripts_logged_out_only' => array(
			'label'       => 'Logged-Out Visitors Only',
			'type'        => 'checkbox',
			'section'     => 'cb_andwisentrances2026_scripts',
			'default'     => '1',
			'description' => 'On by default, matching GA/GTM. Untick if a slot holds something every visitor should see, e.g. a chat widget.',
		),
	);

	foreach ( $fields as $key => $field ) {
		add_settings_field(
			$key,
			$field['label'],
			'cb_andwisentrances2026_render_settings_field',
			'theme-general-settings',
			$field['section'],
			array_merge( $field, array( 'key' => $key ) )
		);
	}
}
add_action( 'admin_menu', 'cb_andwisentrances2026_register_settings_page' );

/**
 * Read a `repeater`-type setting as an array of row arrays.
 *
 * WordPress's Settings API stores whatever nested array structure the form
 * posts (no sanitize_callback is registered — see register_setting() above),
 * so rows survive as-is; this just guards the case where the key was never
 * set at all.
 *
 * @param string $key Setting key, e.g. 'example_repeater'.
 * @return array[]
 */
function cb_andwisentrances2026_get_repeater_setting( $key ) {
	$rows = cb_andwisentrances2026_get_setting( $key, array() );
	return is_array( $rows ) ? $rows : array();
}

/**
 * Enqueue the media modal and settings-page admin scripts, settings page only.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function cb_andwisentrances2026_settings_page_assets( $hook_suffix ) {
	if ( 'toplevel_page_theme-general-settings' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();

	// filemtime() versioning (not the static theme Version): these are
	// hand-maintained admin scripts, and a static version serves stale JS
	// after deploys.
	$admin_scripts = array(
		'cb-andwisentrances2026-gallery-field'     => array( 'js/gallery-field.js', array( 'jquery' ) ),
		'cb-andwisentrances2026-settings-repeater' => array( 'js/repeater-field.js', array( 'jquery' ) ),
		'cb-andwisentrances2026-tabs'              => array( 'js/tabs.js', array() ),
	);

	foreach ( $admin_scripts as $handle => $script ) {
		$abs = get_stylesheet_directory() . '/' . $script[0];

		if ( file_exists( $abs ) ) {
			wp_enqueue_script(
				$handle,
				get_stylesheet_directory_uri() . '/' . $script[0],
				$script[1],
				filemtime( $abs ),
				true
			);
		}
	}
}
add_action( 'admin_enqueue_scripts', 'cb_andwisentrances2026_settings_page_assets' );

/**
 * Render a single settings field — text/email/url input, a textarea, a
 * richtext classic editor, a gallery picker, or a generic repeater.
 *
 * @param array $args Field args: key, type, placeholder, description.
 * @return void
 */
function cb_andwisentrances2026_render_settings_field( $args ) {
	if ( 'gallery' === $args['type'] ) {
		cb_andwisentrances2026_render_gallery_field( $args );
		return;
	}

	if ( 'repeater' === $args['type'] ) {
		cb_andwisentrances2026_render_repeater_field( $args );
		return;
	}

	if ( 'richtext' === $args['type'] ) {
		cb_andwisentrances2026_render_richtext_field( $args );
		return;
	}

	if ( 'textarea' === $args['type'] || 'code' === $args['type'] ) {
		cb_andwisentrances2026_render_textarea_field( $args );
		return;
	}

	if ( 'checkbox' === $args['type'] ) {
		cb_andwisentrances2026_render_checkbox_field( $args );
		return;
	}

	$value = cb_andwisentrances2026_get_setting( $args['key'] );
	?>
	<input
		type="<?php echo esc_attr( $args['type'] ); ?>"
		id="<?php echo esc_attr( $args['key'] ); ?>"
		name="<?php echo esc_attr( CB_ANDWISENTRANCES2026_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $args['key'] ); ?>]"
		value="<?php echo esc_attr( $value ); ?>"
		placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>"
		class="regular-text"
	>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `textarea`- or `code`-type field — for multi-line values like a
 * postal address or a pasted vendor script. `<input type="textarea">` is not
 * a real input type (browsers silently degrade it to a single-line
 * `type="text"`), so this needs its own branch rather than falling through to
 * the generic input above.
 *
 * `code` differs only in presentation — monospace, no spellcheck/autocorrect,
 * and taller by default. The stored value is a plain string either way;
 * nothing here decides whether it's escaped on output, that's the caller's
 * job (see inc/head-tags.php, which prints the script slots verbatim).
 *
 * @param array $args Field args: key, type, placeholder, rows, description.
 * @return void
 */
function cb_andwisentrances2026_render_textarea_field( $args ) {
	$value   = cb_andwisentrances2026_get_setting( $args['key'] );
	$is_code = 'code' === $args['type'];
	?>
	<textarea
		id="<?php echo esc_attr( $args['key'] ); ?>"
		name="<?php echo esc_attr( CB_ANDWISENTRANCES2026_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $args['key'] ); ?>]"
		placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>"
		rows="<?php echo (int) ( $args['rows'] ?? ( $is_code ? 10 : 4 ) ); ?>"
		class="large-text"
		<?php if ( $is_code ) : ?>
		spellcheck="false"
		autocapitalize="off"
		autocomplete="off"
		style="font-family: Consolas, Monaco, monospace; font-size: 12px; white-space: pre; overflow-wrap: normal; overflow-x: auto;"
		<?php endif; ?>
	><?php echo esc_textarea( $value ); ?></textarea>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `richtext`-type field — a full classic editor (wp_editor) for
 * values that need real formatting rather than plain paragraphs. Stored as
 * HTML like anything else; escape with wp_kses_post() on output.
 *
 * @param array $args Field args: key, rows, description.
 * @return void
 */
function cb_andwisentrances2026_render_richtext_field( $args ) {
	$value = cb_andwisentrances2026_get_setting( $args['key'] );
	wp_editor(
		$value,
		'cb_andwisentrances2026_' . $args['key'],
		array(
			'textarea_name' => sprintf( '%s[%s]', CB_ANDWISENTRANCES2026_SETTINGS_OPTION, $args['key'] ),
			'textarea_rows' => $args['rows'] ?? 8,
			'media_buttons' => false,
		)
	);
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `checkbox`-type field.
 *
 * An unticked checkbox posts nothing at all, which is indistinguishable from
 * "never saved" — so a paired hidden input submits '0' first and the checkbox
 * overwrites it with '1' when ticked. That's what makes a default-on checkbox
 * possible: absent means genuinely-never-saved (fall back to `default`),
 * '0' means deliberately unticked.
 *
 * Note this only works because cb_andwisentrances2026_get_setting() treats '' as
 * unset but returns '0' as-is — and '0' is falsey in PHP, so callers can just
 * test the returned value.
 *
 * @param array $args Field args: key, label, default, description.
 * @return void
 */
function cb_andwisentrances2026_render_checkbox_field( $args ) {
	$value = cb_andwisentrances2026_get_setting( $args['key'], $args['default'] ?? '0' );
	$name  = sprintf( '%s[%s]', CB_ANDWISENTRANCES2026_SETTINGS_OPTION, $args['key'] );
	?>
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
	<label>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="1"
			<?php checked( '1', $value ); ?>
		>
		<?php echo esc_html( $args['checkbox_label'] ?? 'Enabled' ); ?>
	</label>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `gallery`-type field — a hidden CSV-of-IDs input plus a
 * thumbnail strip, driven by core's native "Edit Gallery" frame
 * (wp.media.gallery.edit — the same UI as a Gallery block: hover-remove,
 * in-modal drag reorder, Add to Gallery panel). Selection order is the
 * display order; after that, each thumbnail is HTML5-draggable (see
 * js/gallery-field.js) to reorder without reopening the picker — dragging
 * updates both the DOM order and the hidden input's CSV value.
 *
 * @param array $args Field args: key, description.
 * @return void
 */
function cb_andwisentrances2026_render_gallery_field( $args ) {
	$ids = cb_andwisentrances2026_get_gallery_setting( $args['key'] );
	?>
	<div class="cb-andwisentrances2026-gallery-field">
		<input
			type="hidden"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="<?php echo esc_attr( CB_ANDWISENTRANCES2026_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $args['key'] ); ?>]"
			value="<?php echo esc_attr( implode( ',', $ids ) ); ?>"
		>
		<ul class="cb-andwisentrances2026-gallery-field__preview" style="display: flex; flex-wrap: wrap; gap: 8px; padding: 0; margin: 0 0 8px; list-style: none;">
			<?php
			foreach ( $ids as $id ) {
				$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
				if ( ! $thumb ) {
					continue;
				}
				?>
				<li draggable="true" data-id="<?php echo esc_attr( $id ); ?>" style="cursor: grab;"><img src="<?php echo esc_url( $thumb[0] ); ?>" alt="" style="width: 80px; height: 80px; object-fit: contain; background: #fff; border: 1px solid #ccc; pointer-events: none;"></li>
				<?php
			}
			?>
		</ul>
		<?php if ( ! empty( $ids ) ) : ?>
			<p class="description" style="margin-top: -4px;"><?php esc_html_e( 'Drag thumbnails to reorder.', 'cb-andwisentrances2026' ); ?></p>
		<?php endif; ?>
		<p>
			<button type="button" class="button cb-andwisentrances2026-gallery-field__select">Edit Gallery</button>
			<button type="button" class="button cb-andwisentrances2026-gallery-field__clear">Clear</button>
		</p>
	</div>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a generic repeater field — rows of declaratively-configured
 * sub-fields (text, image or file), driven by js/repeater-field.js for add/
 * remove/reorder and per-row media selection.
 *
 * Row indexes in submitted field names don't need to be sequential — the
 * Settings API stores whatever nested array PHP builds from the posted
 * field names, and PHP preserves array insertion (= form field submission
 * = DOM) order regardless of the actual key values, so JS reordering rows
 * in the DOM is enough; nothing needs renumbering.
 *
 * @param array $args Field args: key, sub_fields, description.
 * @return void
 */
function cb_andwisentrances2026_render_repeater_field( $args ) {
	$key        = $args['key'];
	$sub_fields = $args['sub_fields'];
	$rows       = cb_andwisentrances2026_get_repeater_setting( $key );
	?>
	<div class="cb-andwisentrances2026-settings-repeater" data-repeater-key="<?php echo esc_attr( $key ); ?>">
		<div style="display: flex; align-items: center; gap: 12px; padding: 0 12px; margin-bottom: 4px;">
			<span style="flex: none; width: 22px;"></span>
			<?php
			foreach ( $sub_fields as $sub_field ) {
				$width = 'image' === $sub_field['type'] ? 'flex: none; width: 64px;' : 'flex: 1 1 0%; min-width: 0;';
				?>
			<span style="<?php echo esc_attr( $width ); ?> font-size: 12px; font-weight: 600; color: #1d2327;"><?php echo esc_html( $sub_field['label'] ); ?></span>
				<?php
			}
			?>
			<span style="flex: none; width: 92px;"></span>
		</div>
		<div class="cb-andwisentrances2026-settings-repeater__rows">
			<?php
			$number = 0;
			foreach ( $rows as $index => $row ) {
				++$number;
				echo cb_andwisentrances2026_render_repeater_row( $key, $index, $sub_fields, $row, $number ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally.
			}
			?>
		</div>
		<template class="cb-andwisentrances2026-settings-repeater__template">
			<?php echo cb_andwisentrances2026_render_repeater_row( $key, '__INDEX__', $sub_fields, array(), 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally. ?>
		</template>
		<p>
			<button type="button" class="button button-primary cb-andwisentrances2026-settings-repeater__add-row">Add row</button>
		</p>
	</div>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render one repeater row's markup. Shared between already-saved rows and
 * the empty `<template>` row js/repeater-field.js clones for "Add row".
 *
 * @param string     $key        Repeater setting key.
 * @param int|string $index      Row index, or the literal '__INDEX__' placeholder.
 * @param array      $sub_fields Sub-field config: [ name => [ label, type ] ].
 * @param array      $row        Existing row values, keyed by sub-field name.
 * @param int        $number     1-based display position — purely visual, unrelated
 *                                to $index; js/repeater-field.js keeps it in sync
 *                                with DOM order after any add/remove/move.
 * @return string
 */
function cb_andwisentrances2026_render_repeater_row( $key, $index, $sub_fields, $row, $number ) {
	ob_start();
	?>
	<div class="cb-andwisentrances2026-settings-repeater__row" style="display: flex; align-items: flex-end; gap: 12px; border: 1px solid #ccc; padding: 12px; margin-bottom: 8px;">
		<span
			class="cb-andwisentrances2026-settings-repeater__number"
			style="flex: none; align-self: center; display: flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #f0f0f1; font-size: 12px; font-weight: 600; color: #50575e;"
		><?php echo (int) $number; ?></span>
		<?php
		foreach ( $sub_fields as $sub_key => $sub_field ) {
			$name  = sprintf( '%s[%s][%s][%s]', CB_ANDWISENTRANCES2026_SETTINGS_OPTION, $key, $index, $sub_key );
			$value = $row[ $sub_key ] ?? '';

			if ( 'image' === $sub_field['type'] ) {
				$thumb = $value ? wp_get_attachment_image_src( absint( $value ), 'thumbnail' ) : false;
				?>
			<div
				class="cb-andwisentrances2026-settings-repeater__image"
				style="flex: none; position: relative; width: 64px; height: 64px; background: #fff; border: 1px solid #ccc;"
			>
				<img
					src="<?php echo $thumb ? esc_url( $thumb[0] ) : ''; ?>"
					alt=""
					style="width: 100%; height: 100%; object-fit: contain; display: <?php echo $thumb ? 'block' : 'none'; ?>;"
				>
				<input
					type="hidden"
					class="cb-andwisentrances2026-settings-repeater__image-input"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
				<div style="position: absolute; inset: auto 0 0 0; display: flex; background: rgba(0, 0, 0, 0.6);">
					<button
						type="button"
						class="cb-andwisentrances2026-settings-repeater__select-image"
						data-select-label="Select <?php echo esc_attr( $sub_field['label'] ); ?>"
						title="<?php echo esc_attr( ( $thumb ? 'Replace ' : 'Select ' ) . $sub_field['label'] ); ?>"
						style="flex: 1; background: none; border: none; color: #fff; cursor: pointer; padding: 2px 0; font-size: 11px; line-height: 1;"
					>&#9998;</button>
					<button
						type="button"
						class="cb-andwisentrances2026-settings-repeater__clear-image"
						title="Clear"
						style="flex: 1; background: none; border: none; color: #fff; cursor: pointer; padding: 2px 0; font-size: 13px; line-height: 1; <?php echo $thumb ? '' : 'display: none;'; ?>"
					>&times;</button>
				</div>
			</div>
				<?php
			} elseif ( 'file' === $sub_field['type'] ) {
				$file_name = $value ? basename( get_attached_file( absint( $value ) ) ) : '';
				?>
			<div
				class="cb-andwisentrances2026-settings-repeater__file"
				style="flex: 1 1 0%; min-width: 0;"
			>
				<span
					class="cb-andwisentrances2026-settings-repeater__file-name"
					style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px; margin-bottom: 4px;"
				><?php echo $file_name ? esc_html( $file_name ) : 'No file selected.'; ?></span>
				<input
					type="hidden"
					class="cb-andwisentrances2026-settings-repeater__image-input"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
				<button
					type="button"
					class="button cb-andwisentrances2026-settings-repeater__select-image"
					data-select-label="Select <?php echo esc_attr( $sub_field['label'] ); ?>"
					data-button-text="Use this file"
				>Select file</button>
				<button
					type="button"
					class="button cb-andwisentrances2026-settings-repeater__clear-image"
					<?php echo $file_name ? '' : 'style="display: none;"'; ?>
				>Clear</button>
			</div>
				<?php
			} else {
				?>
			<input
				type="text"
				class="regular-text"
				style="flex: 1 1 0%; min-width: 0; width: 100%;"
				aria-label="<?php echo esc_attr( $sub_field['label'] ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
			>
				<?php
			}
		}
		?>
		<div class="cb-andwisentrances2026-settings-repeater__row-actions" style="flex: none; display: flex; gap: 4px;">
			<button type="button" class="button cb-andwisentrances2026-settings-repeater__move-up" title="Move up">&#9650;</button>
			<button type="button" class="button cb-andwisentrances2026-settings-repeater__move-down" title="Move down">&#9660;</button>
			<button type="button" class="button cb-andwisentrances2026-settings-repeater__remove-row" title="Remove">&times;</button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Settings page HTML — one tab per registered section, using
 * js/tabs.js's generic [data-tabs] contract (see that file's docblock)
 * rather than anything Settings-API-specific, so the same markup pattern
 * can be reused wherever tabs are next needed, including a future
 * block-editor equivalent.
 *
 * Replaces do_settings_sections() with a manual per-section loop — that
 * function always renders every section for a page in one continuous flow,
 * with no way to render one section at a time into its own tab panel.
 *
 * @return void
 */
function cb_andwisentrances2026_render_settings_page() {
	global $wp_settings_sections;

	$sections = $wp_settings_sections['theme-general-settings'] ?? array();
	?>
	<div class="wrap">
		<h1>Site-Wide Settings</h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'cb_andwisentrances2026_settings' ); ?>
			<div class="cb-andwisentrances2026-tabs" data-tabs>
				<h2 class="nav-tab-wrapper" data-tabs-nav>
					<?php
					$is_first = true;
					foreach ( $sections as $section_id => $section ) {
						$class = 'nav-tab' . ( $is_first ? ' nav-tab-active' : '' );
						?>
					<a href="#" class="<?php echo esc_attr( $class ); ?>" data-tabs-target="<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section['title'] ); ?></a>
						<?php
						$is_first = false;
					}
					?>
				</h2>
				<?php
				$is_first = true;
				foreach ( $sections as $section_id => $section ) {
					?>
				<div class="cb-andwisentrances2026-tabs__panel" data-tabs-panel="<?php echo esc_attr( $section_id ); ?>" style="padding-top: 20px;" <?php echo $is_first ? '' : 'hidden'; ?>>
					<?php
					if ( is_callable( $section['callback'] ) ) {
						call_user_func( $section['callback'], $section );
					}
					?>
					<table class="form-table" role="presentation">
						<?php do_settings_fields( 'theme-general-settings', $section_id ); ?>
					</table>
				</div>
					<?php
					$is_first = false;
				}
				?>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

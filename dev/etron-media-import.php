<?php
/**
 * Import the Etron media library into this site from its public REST API.
 *
 * Run from the site root (inside the devkinsta_fpm container locally):
 *
 *   wp eval-file wp-content/themes/cb-andwisentrances2026/dev/etron-media-import.php dry --user=1   list only
 *   wp eval-file wp-content/themes/cb-andwisentrances2026/dev/etron-media-import.php --user=1       import
 *
 * Originals only (WordPress regenerates sizes). Each file keeps its original
 * year/month upload folder, upload date, title, caption, description and alt
 * text, and records where it came from in _etron_source_url / _etron_source_id
 * so a re-run skips what is already here.
 */
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$dry  = isset( $args[0] ) && 'dry' === $args[0];
$base = 'https://etron.co.uk/wp-json/wp/v2/media';
$ua   = array( 'user-agent' => 'Mozilla/5.0 (compatible; site-migration)', 'timeout' => 60 );

$items = array();
for ( $page = 1; $page <= 20; $page++ ) {
	$res = wp_remote_get( add_query_arg( array( 'per_page' => 100, 'page' => $page, 'orderby' => 'date', 'order' => 'asc', '_fields' => 'id,date,slug,mime_type,source_url,alt_text,title,caption,description' ), $base ), $ua );
	if ( is_wp_error( $res ) ) {
		echo 'LIST ERROR ' . $res->get_error_message() . "\n";
		return;
	}
	$batch = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( ! is_array( $batch ) || ! $batch || isset( $batch['code'] ) ) {
		break;
	}
	$items = array_merge( $items, $batch );
	if ( count( $batch ) < 100 ) {
		break;
	}
}
echo count( $items ) . " items listed\n";

global $wpdb;
$done = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_etron_source_url'" );
$done = array_flip( $done );

$stats = array( 'imported' => 0, 'skipped' => 0, 'failed' => 0, 'bytes' => 0 );

foreach ( $items as $item ) {
	$url = $item['source_url'];

	if ( isset( $done[ $url ] ) ) {
		$stats['skipped']++;
		continue;
	}

	if ( $dry ) {
		echo $item['date'] . '  ' . $item['mime_type'] . '  ' . basename( $url ) . "\n";
		continue;
	}

	$tmp = download_url( $url, 120 );
	if ( is_wp_error( $tmp ) ) {
		echo 'FAIL download ' . $url . ' : ' . $tmp->get_error_message() . "\n";
		$stats['failed']++;
		continue;
	}

	$bytes = filesize( $tmp );
	$time  = gmdate( 'Y/m', strtotime( $item['date'] ) );
	$upload = array( 'name' => wp_basename( wp_parse_url( $url, PHP_URL_PATH ) ), 'tmp_name' => $tmp );
	$file   = wp_handle_sideload( $upload, array( 'test_form' => false ), $time );

	if ( isset( $file['error'] ) ) {
		echo 'FAIL sideload ' . $url . ' : ' . $file['error'] . "\n";
		@unlink( $tmp );
		$stats['failed']++;
		continue;
	}

	$strip = fn( $html ) => trim( wp_strip_all_tags( html_entity_decode( (string) $html, ENT_QUOTES, 'UTF-8' ) ) );
	$title = $strip( $item['title']['rendered'] ?? '' );
	$id    = wp_insert_attachment(
		wp_slash(
			array(
				'post_mime_type' => $file['type'],
				'post_title'     => '' !== $title ? $title : pathinfo( $file['file'], PATHINFO_FILENAME ),
				'post_excerpt'   => $strip( $item['caption']['rendered'] ?? '' ),
				// The REST "description" is the attachment page HTML, not something anyone wrote.
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_date'      => $item['date'],
				'post_date_gmt'  => get_gmt_from_date( $item['date'] ),
			)
		),
		$file['file'],
		0,
		true
	);

	if ( is_wp_error( $id ) ) {
		echo 'FAIL attach ' . $url . ' : ' . $id->get_error_message() . "\n";
		$stats['failed']++;
		continue;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file['file'] ) );
	if ( '' !== trim( (string) $item['alt_text'] ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $strip( $item['alt_text'] ) ) );
	}
	update_post_meta( $id, '_etron_source_url', esc_url_raw( $url ) );
	update_post_meta( $id, '_etron_source_id', (int) $item['id'] );

	$stats['imported']++;
	$stats['bytes'] += $bytes;
	echo 'ok ' . $id . ' ' . wp_make_link_relative( wp_get_attachment_url( $id ) ) . "\n";
}

printf( "imported %d, skipped %d, failed %d, %.1f MB downloaded\n", $stats['imported'], $stats['skipped'], $stats['failed'], $stats['bytes'] / 1048576 );

<?php
/**
 * Page template — also serves products, services and sectors (see
 * inc/posttypes.php). Layouts are built from blocks; each block renders its
 * own full-width <section> with its own .container, so the_content() is
 * deliberately not wrapped here. A page that does not open with the Hero
 * block gets the forest page head so it never starts under the fixed header.
 *
 * @package cb-andwisentrances2026
 */

get_header();

while ( have_posts() ) {
	the_post();
	cbae_page_head();
	the_content();
}

get_footer();

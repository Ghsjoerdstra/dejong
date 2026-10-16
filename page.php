<?php
/**
 * Pagina: header uit de paginavelden (inc/header-velden.php), daarna de blokken (inc/blocks.php).
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	bdj_toon_paginaheader( get_the_ID() );
	the_content();
endwhile;

get_footer();

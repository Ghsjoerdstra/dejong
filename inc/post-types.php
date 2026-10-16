<?php
/**
 * Berichttypes: Projecten (met activiteit) en Vacatures.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_post_type( 'project', array(
		'labels'             => array(
			'name'          => 'Projecten',
			'singular_name' => 'Project',
			'add_new'       => 'Nieuw project',
			'add_new_item'  => 'Nieuw project toevoegen',
			'edit_item'     => 'Project bewerken',
			'all_items'     => 'Alle projecten',
			'search_items'  => 'Projecten zoeken',
			'not_found'     => 'Geen projecten gevonden',
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_rest'       => false,
		'menu_icon'          => 'dashicons-building',
		'menu_position'      => 21,
		'supports'           => array( 'title', 'page-attributes' ),
		'publicly_queryable' => false,
	) );

	register_taxonomy( 'activiteit', 'project', array(
		'labels'            => array(
			'name'          => 'Activiteiten',
			'singular_name' => 'Activiteit',
			'all_items'     => 'Alle activiteiten',
			'edit_item'     => 'Activiteit bewerken',
			'add_new_item'  => 'Nieuwe activiteit',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'meta_box_cb'       => 'post_categories_meta_box',
		'description'       => 'Koppel een project aan een activiteit. De slug moet gelijk zijn aan de slug van de activiteitpagina (bv. "wonen").',
	) );

	register_post_type( 'vacature', array(
		'labels'             => array(
			'name'          => 'Vacatures',
			'singular_name' => 'Vacature',
			'add_new'       => 'Nieuwe vacature',
			'add_new_item'  => 'Nieuwe vacature toevoegen',
			'edit_item'     => 'Vacature bewerken',
			'all_items'     => 'Alle vacatures',
			'not_found'     => 'Geen vacatures gevonden',
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_rest'       => false, // geen blokeditor: vacatures vul je in met vaste velden
		'menu_icon'          => 'dashicons-groups',
		'menu_position'      => 22,
		'supports'           => array( 'title', 'page-attributes' ),
		'publicly_queryable' => false,
	) );
} );

/**
 * Projecten bij een activiteit (slug van de term = slug van de pagina).
 *
 * @return WP_Post[]
 */
function bdj_projecten( $activiteit_slug ) {
	return get_posts( array(
		'post_type'      => 'project',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'tax_query'      => array(
			array(
				'taxonomy' => 'activiteit',
				'field'    => 'slug',
				'terms'    => $activiteit_slug,
			),
		),
	) );
}

/** Foto-ID's van een project (veld "galerij", komma-gescheiden). */
function bdj_project_fotos( $post_id ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post_id, '_bdj_galerij', true ) ) ) );
	return array_values( $ids );
}

// Kolommen in het projectoverzicht in de admin.
add_filter( 'manage_project_posts_columns', function ( $kolommen ) {
	$nieuw = array();
	foreach ( $kolommen as $key => $label ) {
		if ( 'title' === $key ) {
			$nieuw['bdj_foto'] = 'Foto';
		}
		$nieuw[ $key ] = $label;
		if ( 'title' === $key ) {
			$nieuw['bdj_plaats'] = 'Plaats';
			$nieuw['bdj_aantal'] = "Foto's";
		}
	}
	return $nieuw;
} );

add_action( 'manage_project_posts_custom_column', function ( $kolom, $post_id ) {
	$fotos = bdj_project_fotos( $post_id );
	if ( 'bdj_foto' === $kolom && $fotos ) {
		echo wp_get_attachment_image( $fotos[0], array( 80, 54 ) );
	} elseif ( 'bdj_plaats' === $kolom ) {
		echo esc_html( get_post_meta( $post_id, '_bdj_plaats', true ) );
	} elseif ( 'bdj_aantal' === $kolom ) {
		echo count( $fotos );
	}
}, 10, 2 );

/**
 * HTML van een uitgeklapte vacature, opgebouwd uit de velden.
 * Valt terug op de oude inhoud (post_content) als de velden nog leeg zijn.
 */
function bdj_vacature_inhoud( $vacature ) {
	$id    = $vacature->ID;
	$intro = get_post_meta( $id, '_bdj_intro', true );
	$delen = '';
	foreach ( bdj_vacature_onderdelen( $id ) as $deel ) {
		$titel  = isset( $deel['titel'] ) ? $deel['titel'] : '';
		$soort  = isset( $deel['soort'] ) ? $deel['soort'] : 'lijst';
		$inhoud = trim( isset( $deel['inhoud'] ) ? (string) $deel['inhoud'] : '' );
		if ( '' === $inhoud ) {
			continue;
		}
		$delen .= '<div class="vacature-deel">';
		if ( $titel ) {
			$delen .= '<h4>' . esc_html( $titel ) . '</h4>';
		}
		if ( 'tekst' === $soort ) {
			$delen .= bdj_tekst( $inhoud );
		} else {
			$punten = array_filter( array_map( function ( $regel ) {
				return trim( ltrim( trim( $regel ), "-*• \t" ) );
			}, preg_split( '/\r\n|\r|\n/', $inhoud ) ) );
			$delen .= '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $punten ) ) . '</li></ul>';
		}
		$delen .= '</div>';
	}

	if ( ! $intro && ! $delen ) {
		return apply_filters( 'the_content', $vacature->post_content );
	}
	return ( $intro ? '<div class="breed">' . bdj_tekst( $intro ) . '</div>' : '' ) . $delen;
}


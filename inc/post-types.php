<?php
/**
 * Berichttypes: Projecten en Vacatures.
 * Een project hoort bij een activiteitpagina (meta "_bdj_activiteit" = pagina-ID).
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

	// Oude koppeling (categorie met dezelfde slug als de activiteitpagina). Verborgen; alleen nog nodig om
	// bestaande projecten over te zetten naar de nieuwe koppeling (zie bdj_migreer_activiteiten()).
	register_taxonomy( 'activiteit', 'project', array(
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => true,
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
 * ID van een activiteitpagina, op basis van een ID of een slug (oude blokken bewaren een slug).
 */
function bdj_activiteit_id( $waarde ) {
	if ( is_numeric( $waarde ) ) {
		return (int) $waarde;
	}
	foreach ( bdj_activiteiten() as $act ) {
		if ( $act->post_name === $waarde ) {
			return $act->ID;
		}
	}
	return 0;
}

/** Activiteitpagina's als keuzelijst (ID => titel). */
function bdj_activiteit_opties( $leeg ) {
	$opties = array( '' => $leeg );
	foreach ( bdj_activiteiten() as $act ) {
		$opties[ $act->ID ] = get_the_title( $act );
	}
	return $opties;
}

/**
 * Projecten bij een activiteitpagina.
 *
 * @param int|string $activiteit Pagina-ID of slug van de activiteitpagina.
 * @return WP_Post[]
 */
function bdj_projecten( $activiteit ) {
	$id = bdj_activiteit_id( $activiteit );
	if ( ! $id ) {
		return array();
	}
	return get_posts( array(
		'post_type'      => 'project',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'meta_key'       => '_bdj_activiteit',
		'meta_value'     => $id,
	) );
}

/**
 * Eenmalig: projecten met de oude categorie koppelen aan de activiteitpagina met dezelfde slug.
 */
function bdj_migreer_activiteiten() {
	foreach ( get_posts( array( 'post_type' => 'project', 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $project ) {
		if ( get_post_meta( $project->ID, '_bdj_activiteit', true ) ) {
			continue;
		}
		$termen = wp_get_object_terms( $project->ID, 'activiteit', array( 'fields' => 'slugs' ) );
		if ( is_wp_error( $termen ) || ! $termen ) {
			continue;
		}
		$id = bdj_activiteit_id( $termen[0] );
		if ( $id ) {
			update_post_meta( $project->ID, '_bdj_activiteit', $id );
		}
	}
}

// Op init (niet admin_init), zodat de voorkant ook direct klopt na een update.
add_action( 'init', function () {
	if ( get_option( 'bdj_activiteit_migratie' ) ) {
		return;
	}
	bdj_migreer_activiteiten();
	update_option( 'bdj_activiteit_migratie', 1 );
}, 20 );

// Filter op activiteit boven het projectoverzicht.
add_action( 'restrict_manage_posts', function ( $post_type ) {
	if ( 'project' !== $post_type ) {
		return;
	}
	$huidig = isset( $_GET['bdj_activiteit'] ) ? absint( $_GET['bdj_activiteit'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	echo '<select name="bdj_activiteit">';
	foreach ( bdj_activiteit_opties( 'Alle activiteiten' ) as $id => $titel ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $id ), selected( $huidig, (int) $id, false ), esc_html( $titel ) );
	}
	echo '</select>';
} );

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'project' !== $query->get( 'post_type' ) || empty( $_GET['bdj_activiteit'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$query->set( 'meta_key', '_bdj_activiteit' );
	$query->set( 'meta_value', absint( $_GET['bdj_activiteit'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
} );

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
			$nieuw['bdj_activiteit'] = 'Activiteit';
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
	} elseif ( 'bdj_activiteit' === $kolom ) {
		$id = (int) get_post_meta( $post_id, '_bdj_activiteit', true );
		echo $id ? esc_html( get_the_title( $id ) ) : '<span style="color:#b32d2e">Geen activiteit</span>';
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


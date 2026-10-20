<?php
/**
 * Berichttypes: Activiteiten, Projecten en Vacatures.
 * Een project hoort bij een activiteit (meta "_bdj_activiteit" = ID van de activiteit).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	// Activiteiten: één plek voor titel, korte tekst, icoon, foto's en de inhoud van de activiteitpagina.
	register_post_type( 'activiteit', array(
		'labels'             => array(
			'name'               => 'Activiteiten',
			'singular_name'      => 'Activiteit',
			'add_new'            => 'Nieuwe activiteit',
			'add_new_item'       => 'Nieuwe activiteit toevoegen',
			'edit_item'          => 'Activiteit bewerken',
			'view_item'          => 'Activiteit bekijken',
			'all_items'          => 'Alle activiteiten',
			'search_items'       => 'Activiteiten zoeken',
			'not_found'          => 'Geen activiteiten gevonden',
			'not_found_in_trash' => 'Geen activiteiten in de prullenbak',
			'item_published'     => 'Activiteit gepubliceerd.',
			'item_updated'       => 'Activiteit bijgewerkt.',
		),
		'public'             => true,
		'show_in_rest'       => true,
		'show_in_nav_menus'  => true,
		'menu_icon'          => 'dashicons-screenoptions',
		'menu_position'      => 20,
		'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
		'has_archive'        => false,
		'rewrite'            => false, // eigen URL's zonder voorvoegsel (/wonen/), zie hieronder
		'query_var'          => 'activiteit',
	) );

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
 * ID van een activiteit, op basis van een ID of een slug (oude blokken bewaren een slug).
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

/** Activiteiten als keuzelijst (ID => titel). */
function bdj_activiteit_opties( $leeg ) {
	$opties = array( '' => $leeg );
	foreach ( bdj_activiteiten() as $act ) {
		$opties[ $act->ID ] = get_the_title( $act );
	}
	return $opties;
}

/**
 * Projecten bij een activiteit.
 *
 * @param int|string $activiteit ID of slug van de activiteit.
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

/*
 * Volgorde van de projecten bij een activiteit, vanuit het Projecten-blok in de editor.
 * De volgorde staat in menu_order van de projecten, dus geldt voor alle Projecten-blokken van die activiteit.
 */
add_action( 'rest_api_init', function () {
	// Alle projecten, om in het Projecten-blok zelf een selectie te maken.
	register_rest_route( 'bdj/v1', '/projecten', array(
		'methods'             => 'GET',
		'permission_callback' => function () {
			return current_user_can( 'edit_posts' );
		},
		'callback'            => function () {
			$projecten = get_posts( array(
				'post_type'      => 'project',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			) );
			return array_map( function ( $project ) {
				$act = (int) get_post_meta( $project->ID, '_bdj_activiteit', true );
				return array(
					'id'         => $project->ID,
					'titel'      => get_the_title( $project ),
					'activiteit' => $act ? get_the_title( $act ) : '',
				);
			}, $projecten );
		},
	) );

	register_rest_route( 'bdj/v1', '/projectvolgorde', array(
		array(
			'methods'             => 'GET',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'callback'            => function ( $request ) {
				return array_map( function ( $project ) {
					return array( 'id' => $project->ID, 'titel' => get_the_title( $project ) );
				}, bdj_projecten( $request['activiteit'] ) );
			},
		),
		array(
			'methods'             => 'POST',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'callback'            => function ( $request ) {
				foreach ( array_values( (array) $request['ids'] ) as $volgorde => $id ) {
					$id = absint( $id );
					if ( 'project' === get_post_type( $id ) && current_user_can( 'edit_post', $id ) ) {
						wp_update_post( array( 'ID' => $id, 'menu_order' => $volgorde ) );
					}
				}
				return true;
			},
		),
	) );
} );

/* -------------------------------------------------------------------------
 * URL's van activiteiten: direct onder de hoofdmap (/wonen/), net als voorheen als pagina.
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {
	$slugs = array();
	foreach ( bdj_activiteiten() as $act ) {
		$slugs[] = $act->post_name;
		add_rewrite_rule( '^' . preg_quote( $act->post_name, '/' ) . '/?$', 'index.php?activiteit=' . $act->post_name, 'top' );
	}
	// Regels opnieuw opslaan als er een activiteit bij komt, weggaat of een andere slug krijgt.
	$sleutel = implode( ',', $slugs );
	if ( get_option( 'bdj_activiteit_slugs' ) !== $sleutel ) {
		update_option( 'bdj_activiteit_slugs', $sleutel );
		flush_rewrite_rules( false );
	}
}, 20 );

add_filter( 'post_type_link', function ( $url, $post ) {
	if ( 'activiteit' === $post->post_type && 'publish' === $post->post_status ) {
		return home_url( user_trailingslashit( $post->post_name ) );
	}
	return $url;
}, 10, 2 );

// Geen pagina en activiteit met dezelfde slug (anders is één van de twee niet bereikbaar).
add_filter( 'wp_unique_post_slug_is_bad_hierarchical_slug', function ( $slecht, $slug, $post_type ) {
	if ( 'page' === $post_type && get_page_by_path( $slug, OBJECT, 'activiteit' ) ) {
		return true;
	}
	return $slecht;
}, 10, 3 );

add_filter( 'wp_unique_post_slug_is_bad_flat_slug', function ( $slecht, $slug, $post_type ) {
	if ( 'activiteit' === $post_type && get_page_by_path( $slug, OBJECT, 'page' ) ) {
		return true;
	}
	return $slecht;
}, 10, 3 );

/**
 * Eenmalig: pagina's met het oude sjabloon "Activiteit" omzetten naar het berichttype Activiteit.
 * ID, inhoud, velden en gekoppelde projecten blijven gelijk.
 */
add_action( 'init', function () {
	if ( get_option( 'bdj_activiteit_cpt' ) ) {
		return;
	}
	$paginas = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-templates/activiteit.php',
	) );
	foreach ( $paginas as $pagina ) {
		set_post_type( $pagina->ID, 'activiteit' );
		delete_post_meta( $pagina->ID, '_wp_page_template' );
		if ( $pagina->post_parent ) {
			wp_update_post( array( 'ID' => $pagina->ID, 'post_parent' => 0 ) );
		}
		// Menu-items die naar deze pagina wezen.
		$menu_items = get_posts( array(
			'post_type'      => 'nav_menu_item',
			'posts_per_page' => -1,
			'meta_key'       => '_menu_item_object_id',
			'meta_value'     => $pagina->ID,
		) );
		foreach ( $menu_items as $item ) {
			if ( 'page' === get_post_meta( $item->ID, '_menu_item_object', true ) ) {
				update_post_meta( $item->ID, '_menu_item_object', 'activiteit' );
			}
		}
	}
	update_option( 'bdj_activiteit_cpt', 1 );
	delete_option( 'bdj_activiteit_slugs' ); // URL-regels opnieuw opbouwen
}, 15 );

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


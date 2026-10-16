<?php
/**
 * Hulpfuncties voor templates.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG uit assets/img/icons (voor iconen die van kleur wisselen via currentColor).
 */
function bdj_svg( $naam ) {
	static $cache = array();
	if ( ! isset( $cache[ $naam ] ) ) {
		$pad             = BDJ_DIR . '/assets/img/icons/' . sanitize_file_name( $naam ) . '.svg';
		$cache[ $naam ] = file_exists( $pad ) ? trim( file_get_contents( $pad ) ) : '';
	}
	return $cache[ $naam ];
}

/** URL van een theme-afbeelding. */
function bdj_asset( $pad ) {
	return BDJ_URI . '/assets/img/' . ltrim( $pad, '/' );
}

/** <img> van een theme-icoon. */
function bdj_icon_img( $naam, $alt = '' ) {
	return sprintf( '<img src="%s" alt="%s">', esc_url( bdj_asset( 'icons/' . $naam . '.svg' ) ), esc_attr( $alt ) );
}

/**
 * De gepubliceerde activiteiten, op volgorde.
 *
 * @return WP_Post[]
 */
function bdj_activiteiten() {
	static $lijst = null;
	if ( null === $lijst ) {
		$lijst = get_posts( array(
			'post_type'      => 'activiteit',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		) );
	}
	return $lijst;
}

/** Icoon-naam bij een activiteit (op basis van de slug, of het veld "icoon"). */
function bdj_activiteit_icoon( $post ) {
	$icoon = get_post_meta( $post->ID, '_bdj_icoon', true );
	if ( ! $icoon ) {
		$icoon = $post->post_name;
	}
	$bekend = array( 'wonen', 'utiliteit', 'food', 'bedrijfspand-industrie', 'logistiek-warehousing', 'verbouw-onderhoud', 'versterkingen-ncg' );
	return in_array( $icoon, $bekend, true ) ? $icoon : 'wonen';
}

/** <img> van het icoon van een activiteit: eigen afbeelding of het theme-icoon. */
function bdj_activiteit_icoon_img( $post ) {
	$id = (int) get_post_meta( $post->ID, '_bdj_icoon_afbeelding', true );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image( $id, 'medium', false, array( 'alt' => '' ) );
	}
	return bdj_icon_img( 'act-' . bdj_activiteit_icoon( $post ) );
}

/** Icoon in de iconenbalk: eigen afbeelding of de inline SVG (die van kleur wisselt). */
function bdj_activiteit_tab_icoon( $post ) {
	$id = (int) get_post_meta( $post->ID, '_bdj_tab_afbeelding', true );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image( $id, 'thumbnail', false, array( 'alt' => '' ) );
	}
	return bdj_svg( 'tab-' . bdj_activiteit_icoon( $post ) );
}

/** Is de huidige pagina een activiteit? */
function bdj_is_activiteit() {
	return is_singular( 'activiteit' );
}

/**
 * Veldwaarde met standaardwaarde.
 */
function bdj_veld( $key, $default = '', $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$waarde  = get_post_meta( $post_id, '_bdj_' . $key, true );
	return ( '' === $waarde || null === $waarde ) ? $default : $waarde;
}

/**
 * Afbeelding-URL uit een veld (attachment-ID), met een fallback-URL.
 */
function bdj_veld_afbeelding( $key, $fallback = '', $size = 'large', $post_id = null ) {
	$id = (int) bdj_veld( $key, 0, $post_id );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, $size );
		if ( $url ) {
			return $url;
		}
	}
	return $fallback;
}

/** Customizer-waarde met standaard uit bdj_customizer_defaults(). */
function bdj_optie( $key ) {
	$defaults = bdj_customizer_defaults();
	return get_theme_mod( 'bdj_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/** Customizer-afbeelding (attachment-ID of URL) met fallback. */
function bdj_optie_afbeelding( $key, $fallback ) {
	$waarde = get_theme_mod( 'bdj_' . $key );
	if ( is_numeric( $waarde ) && $waarde ) {
		$url = wp_get_attachment_image_url( (int) $waarde, 'large' );
		return $url ? $url : $fallback;
	}
	return $waarde ? $waarde : $fallback;
}

/** Tekst met alinea's (voor textarea-velden). */
function bdj_tekst( $tekst ) {
	return wpautop( wp_kses_post( $tekst ) );
}

/** Telefoonnummer naar tel:-link. */
function bdj_tel_link( $nummer ) {
	$schoon = preg_replace( '/[^0-9+]/', '', $nummer );
	if ( 0 === strpos( $schoon, '0' ) ) {
		$schoon = '+31' . substr( $schoon, 1 );
	}
	return 'tel:' . $schoon;
}

/**
 * Items uit het hoofdmenu (plat), met 'actief'-status.
 *
 * @return array[] Elk item: titel, url, classes, actief, is_activiteiten.
 */
function bdj_menu_items() {
	$locaties = get_nav_menu_locations();
	$items    = array();
	if ( ! empty( $locaties['hoofdmenu'] ) ) {
		$menu_items = wp_get_nav_menu_items( $locaties['hoofdmenu'] );
		if ( $menu_items ) {
			foreach ( $menu_items as $item ) {
				if ( $item->menu_item_parent ) {
					continue;
				}
				$classes = array_filter( (array) $item->classes );
				$is_act  = in_array( 'menu-activiteiten', $classes, true );
				$actief  = $is_act ? bdj_is_activiteit() : ( is_singular() && (int) $item->object_id === get_queried_object_id() && 'post_type' === $item->type );
				$items[] = array(
					'titel'           => $item->title,
					'url'             => $item->url,
					'classes'         => $classes,
					'actief'          => $actief,
					'is_activiteiten' => $is_act,
				);
			}
		}
	}

	if ( ! $items ) {
		// Fallback als er nog geen menu is: activiteiten + alle pagina's op het hoogste niveau.
		$items[] = array( 'titel' => 'Activiteiten', 'url' => home_url( '/#activiteiten' ), 'classes' => array( 'menu-activiteiten' ), 'actief' => bdj_is_activiteit(), 'is_activiteiten' => true );
		foreach ( get_pages( array( 'parent' => 0, 'sort_column' => 'menu_order' ) ) as $pagina ) {
			if ( (int) get_option( 'page_on_front' ) === $pagina->ID ) {
				continue;
			}
			$items[] = array( 'titel' => $pagina->post_title, 'url' => get_permalink( $pagina ), 'classes' => array(), 'actief' => is_page( $pagina->ID ), 'is_activiteiten' => false );
		}
	}
	return $items;
}

// Body-class als de pagina niet met een header-blok begint (dan schuift de inhoud onder de menubalk vandaan).
add_filter( 'body_class', function ( $classes ) {
	if ( is_singular() && ! bdj_toont_header() ) {
		$classes[] = 'zonder-header';
	}
	return $classes;
} );

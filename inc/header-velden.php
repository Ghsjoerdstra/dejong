<?php
/**
 * Paginaheader als vaste velden (geen Gutenberg-blok).
 *
 * De velden staan in de editor bovenaan het tabblad "Pagina" (paneel "Header", assets/js/paneel-header.js)
 * en worden in page.php boven de blokken getoond.
 */

defined( 'ABSPATH' ) || exit;

/** Headervelden met type en standaardwaarde. */
function bdj_header_velden() {
	return array(
		'_bdj_header_tonen'      => array( 'boolean', true ),
		'_bdj_header_afbeelding' => array( 'integer', 0 ),
		'_bdj_header_titel'      => array( 'string', '' ),
		'_bdj_header_link_tekst' => array( 'string', '' ),
		'_bdj_header_link_url'   => array( 'string', '' ),
		'_bdj_header_hoogte'     => array( 'string', 'groot' ),
	);
}

add_action( 'init', function () {
	foreach ( bdj_header_velden() as $key => $info ) {
		list( $type, $standaard ) = $info;
		foreach ( array( 'page', 'activiteit' ) as $post_type ) {
			register_post_meta( $post_type, $key, array(
				'type'              => $type,
				'single'            => true,
				'default'           => $standaard,
				'show_in_rest'      => true,
				'sanitize_callback' => 'boolean' === $type ? 'rest_sanitize_boolean' : ( 'integer' === $type ? 'absint' : 'sanitize_text_field' ),
				'auth_callback'     => function ( $toegestaan, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			) );
		}
	}
} );

/** Headergegevens van een pagina (met standaardwaarden). */
function bdj_header_gegevens( $post_id ) {
	$uit = array();
	foreach ( bdj_header_velden() as $key => $info ) {
		$waarde = metadata_exists( 'post', $post_id, $key ) ? get_post_meta( $post_id, $key, true ) : $info[1];
		$uit[ substr( $key, strlen( '_bdj_header_' ) ) ] = $waarde;
	}
	$uit['tonen'] = (bool) $uit['tonen'] && '0' !== $uit['tonen'];
	return $uit;
}

/** Toont de huidige pagina een header? */
function bdj_toont_header( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'page', 'activiteit' ), true ) ) {
		return false;
	}
	$h = bdj_header_gegevens( $post_id );
	return $h['tonen'];
}

/** Header van een pagina tonen (zelfde opmaak als voorheen het blok). */
function bdj_toon_paginaheader( $post_id ) {
	$h = bdj_header_gegevens( $post_id );
	if ( ! $h['tonen'] ) {
		return;
	}
	bdj_render_header( array(
		'afbeelding' => (int) $h['afbeelding'],
		'titel'      => $h['titel'],
		'link_tekst' => $h['link_tekst'],
		'link_url'   => $h['link_url'],
		'hoogte'     => $h['hoogte'],
	) );
}

// Editor: paneel "Header" + voorbeeld bovenaan het canvas.
add_action( 'enqueue_block_editor_assets', function () {
	if ( ! in_array( get_post_type(), array( 'page', 'activiteit' ), true ) ) {
		return;
	}
	wp_enqueue_script(
		'bdj-paneel-header',
		BDJ_URI . '/assets/js/paneel-header.js',
		array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-block-editor' ),
		BDJ_VERSION,
		true
	);
} );

/**
 * Bestaande pagina's omzetten: een Header-blok bovenaan de inhoud wordt naar de headervelden verplaatst.
 *
 * @return array Overzicht per pagina.
 */
function bdj_headers_omzetten() {
	$uit = array();
	foreach ( get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1, 'post_status' => 'any' ) ) as $pagina ) {
		$blokken = parse_blocks( $pagina->post_content );
		$gevonden = false;
		foreach ( $blokken as $i => $blok ) {
			if ( 'bdj/header' !== $blok['blockName'] ) {
				continue;
			}
			$a = $blok['attrs'];
			update_post_meta( $pagina->ID, '_bdj_header_tonen', true );
			update_post_meta( $pagina->ID, '_bdj_header_afbeelding', isset( $a['afbeelding'] ) ? (int) $a['afbeelding'] : 0 );
			update_post_meta( $pagina->ID, '_bdj_header_titel', isset( $a['titel'] ) ? $a['titel'] : '' );
			update_post_meta( $pagina->ID, '_bdj_header_link_tekst', isset( $a['link_tekst'] ) ? $a['link_tekst'] : '' );
			update_post_meta( $pagina->ID, '_bdj_header_link_url', isset( $a['link_url'] ) ? $a['link_url'] : '' );
			update_post_meta( $pagina->ID, '_bdj_header_hoogte', isset( $a['hoogte'] ) ? $a['hoogte'] : 'groot' );
			unset( $blokken[ $i ] );
			$gevonden = true;
			break;
		}
		if ( $gevonden ) {
			$inhoud = '';
			foreach ( $blokken as $blok ) {
				if ( null === $blok['blockName'] && '' === trim( $blok['innerHTML'] ) ) {
					continue; // lege witruimte tussen blokken
				}
				$inhoud .= serialize_block( $blok ) . "\n\n";
			}
			wp_update_post( wp_slash( array( 'ID' => $pagina->ID, 'post_content' => trim( $inhoud ) ) ) );
		} elseif ( ! metadata_exists( 'post', $pagina->ID, '_bdj_header_tonen' ) ) {
			update_post_meta( $pagina->ID, '_bdj_header_tonen', false ); // had geen header
		}
		$uit[ $pagina->post_name ] = $gevonden ? 'omgezet' : 'geen header-blok';
	}
	return $uit;
}

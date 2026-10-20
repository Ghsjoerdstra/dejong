<?php
/**
 * Gutenberg-blokken van het theme (categorie "Bouwgroep DE Jong" in de blokkenlijst).
 *
 * Alle blokken zijn "dynamisch": de velden staan in de zijbalk van de editor, de HTML wordt
 * door PHP gemaakt (ook voor het live voorbeeld in de editor). Eén schema hieronder bepaalt
 * zowel de velden in de editor als de attributen van het blok.
 *
 * Veldtypes: text, textarea, select, toggle, number, image, gallery, repeater.
 * Optie 'toon' => array( veld => waarde ) toont een veld alleen in die situatie
 *   (waarde '!' = alleen als dat veld gevuld is, true/false voor toggles, of een lijst met waarden).
 * Optie 'paneel' groepeert velden in de zijbalk.
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Iconen die je in blokken kunt kiezen
 * ---------------------------------------------------------------------- */

/** Grote iconen (rijen met icoon). Sleutel = bestandsnaam in assets/img/icons. */
function bdj_iconen_groot() {
	return array(
		''                           => '— Geen icoon —',
		'over-verbinden'             => 'Verbinden (oranje)',
		'over-meedenken'             => 'Meedenken (oranje)',
		'over-organiseren'           => 'Organiseren (oranje)',
		'over-realiseren'            => 'Realiseren (oranje)',
		'act-wonen'                  => 'Woning (blauw)',
		'act-utiliteit'              => 'Gebouw (blauw)',
		'act-food'                   => 'Bord en bestek (blauw)',
		'act-bedrijfspand-industrie' => 'Fabriek (blauw)',
		'act-logistiek-warehousing'  => 'Loods (blauw)',
		'act-verbouw-onderhoud'      => 'Steiger (blauw)',
		'act-versterkingen-ncg'      => 'Versterking (blauw)',
	);
}

/** Kleine iconen (stappen in de tijdlijn). */
function bdj_iconen_klein() {
	return array(
		''                 => '— Geen icoon —',
		'stap-verbinden'   => 'Verbinden',
		'stap-meedenken'   => 'Meedenken',
		'stap-organiseren' => 'Organiseren',
		'stap-realiseren'  => 'Realiseren',
	);
}

/** Veilig icoon-<img> (alleen bekende iconen). */
function bdj_blok_icoon( $naam, $toegestaan ) {
	return ( $naam && isset( $toegestaan[ $naam ] ) ) ? bdj_icon_img( $naam ) : '';
}

/* -------------------------------------------------------------------------
 * Schema
 * ---------------------------------------------------------------------- */

function bdj_blok_schema() {
	$band_opties = array(
		'geen'     => 'Geen',
		'grijs'    => 'Grijs vlak (loopt door in een grijs blok eronder, bv. Logo\'s)',
		'leeg'     => 'Blauwe band zonder inhoud',
		'kolommen' => 'Blauwe band met twee tekstkolommen',
		'tijdlijn' => 'Blauwe band met tijdlijn (4 stappen)',
	);

	$overlap = array(
		'overlap' => array( 'label' => 'Over het blok erboven schuiven', 'type' => 'toggle', 'default' => true, 'hint' => 'Aan: het witte vak valt deels over de header erboven. Uit: het vak staat los.', 'paneel' => 'Weergave' ),
	);

	$kolommen = array(
		'links'  => array( 'label' => 'Linkerkolom', 'type' => 'textarea', 'paneel' => 'Blauwe band', 'toon' => array( 'band' => 'kolommen' ) ),
		'rechts' => array( 'label' => 'Rechterkolom', 'type' => 'textarea', 'paneel' => 'Blauwe band', 'toon' => array( 'band' => 'kolommen' ) ),
	);

	$stappen = array();
	foreach ( array( 1 => 'verbinden', 2 => 'meedenken', 3 => 'organiseren', 4 => 'realiseren' ) as $nr => $icoon ) {
		$stappen[ "stap{$nr}_icoon" ] = array( 'label' => "Stap {$nr} – icoon", 'type' => 'select', 'opties' => bdj_iconen_klein(), 'default' => 'stap-' . $icoon, 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );
		$stappen[ "stap{$nr}_afbeelding" ] = array( 'label' => "Stap {$nr} – of een eigen afbeelding", 'type' => 'image', 'hint' => 'Gaat voor het icoon.', 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );
		$stappen[ "stap{$nr}_titel" ] = array( 'label' => "Stap {$nr} – titel", 'type' => 'text', 'default' => ucfirst( $icoon ), 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );
		$stappen[ "stap{$nr}_tekst" ] = array( 'label' => "Stap {$nr} – tekst", 'type' => 'textarea', 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );
	}
	$stappen['meer_tekst'] = array( 'label' => 'Link onder de tijdlijn – tekst', 'type' => 'text', 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );
	$stappen['meer_url']   = array( 'label' => 'Link onder de tijdlijn – URL', 'type' => 'text', 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) );

	return array(
		'header'              => array(
			'titel'        => 'Header',
			'beschrijving' => 'Grote foto over de volle breedte, optioneel met titel en link.',
			'icoon'        => 'cover-image',
			'velden'       => array(
				'afbeelding' => array( 'label' => 'Foto', 'type' => 'image', 'hint' => 'Leeg = de uitgelichte afbeelding van de pagina.' ),
				'titel'      => array( 'label' => 'Titel op de foto', 'type' => 'text', 'hint' => 'Leeg = geen tekstvak op de foto.' ),
				'link_tekst' => array( 'label' => 'Linktekst', 'type' => 'text', 'toon' => array( 'titel' => '!' ) ),
				'link_url'   => array( 'label' => 'Link-URL', 'type' => 'text', 'hint' => 'Bv. /contact/ of een volledige URL.', 'toon' => array( 'titel' => '!' ) ),
				'hoogte'     => array( 'label' => 'Hoogte', 'type' => 'select', 'opties' => array( 'groot' => 'Groot (800px)', 'middel' => 'Middel (600px)', 'klein' => 'Klein (400px)' ), 'default' => 'groot', 'paneel' => 'Weergave' ),
			),
		),
		'tekstvak'            => array(
			'titel'        => 'Tekstvak',
			'beschrijving' => 'Wit vak met kop en tekst, optioneel over de header en met een blauwe band eronder.',
			'icoon'        => 'align-center',
			'velden'       => array_merge( array(
				'kop'        => array( 'label' => 'Kop', 'type' => 'text', 'hint' => 'Leeg = paginatitel.' ),
				'kop_niveau' => array( 'label' => 'Soort kop', 'type' => 'select', 'opties' => array( 'h1' => 'Hoofdkop van de pagina (H1)', 'h2' => 'Tussenkop (H2)' ), 'default' => 'h1' ),
				'tekst'      => array( 'label' => 'Tekst', 'type' => 'textarea' ),
			), $overlap, array(
				'tekst_mobiel' => array( 'label' => 'Tekst tonen op mobiel', 'type' => 'toggle', 'default' => true, 'hint' => 'Uit: op mobiel alleen de kop.', 'paneel' => 'Weergave' ),
				'band'         => array( 'label' => 'Band eronder', 'type' => 'select', 'opties' => $band_opties, 'default' => 'leeg', 'paneel' => 'Blauwe band' ),
				'tijdlijn_kort' => array( 'label' => 'Op mobiel alleen de stappen (zonder teksten)', 'type' => 'toggle', 'default' => true, 'paneel' => 'Tijdlijn', 'toon' => array( 'band' => 'tijdlijn' ) ),
			), $kolommen, $stappen ),
		),
		'vacatures'           => array(
			'titel'        => 'Vacaturelijst',
			'beschrijving' => 'Wit vak met alle vacatures (beheer via "Vacatures" in het menu) als uitklapbare balken.',
			'icoon'        => 'groups',
			'velden'       => array_merge( array(
				'kop'           => array( 'label' => 'Kop', 'type' => 'text', 'hint' => 'Leeg = paginatitel.' ),
				'tekst'         => array( 'label' => 'Tekst boven de vacatures', 'type' => 'textarea' ),
				'contact_tekst' => array( 'label' => 'Tekst onder de vacatures', 'type' => 'textarea', 'hint' => 'HTML toegestaan, bv. <strong> en <a href="...">.' ),
				'geen_tekst'    => array( 'label' => 'Tekst als er geen vacatures zijn', 'type' => 'textarea', 'default' => 'Er zijn op dit moment geen openstaande vacatures.' ),
			), $overlap, array(
				'band' => array( 'label' => 'Blauwe band eronder', 'type' => 'select', 'opties' => array( 'geen' => $band_opties['geen'], 'leeg' => $band_opties['leeg'], 'kolommen' => $band_opties['kolommen'] ), 'default' => 'leeg', 'paneel' => 'Blauwe band' ),
			), $kolommen ),
		),
		'contactformulier'    => array(
			'titel'        => 'Contactformulier',
			'beschrijving' => 'Wit vak met contactgegevens (uit de Customizer) en een werkend contactformulier.',
			'icoon'        => 'email',
			'velden'       => array_merge( array(
				'kop'       => array( 'label' => 'Kop', 'type' => 'text', 'hint' => 'Leeg = paginatitel.' ),
				'gegevens'  => array( 'label' => 'Contactgegevens naast het formulier tonen', 'type' => 'toggle', 'default' => true ),
				'ontvanger' => array( 'label' => 'Berichten sturen naar', 'type' => 'text', 'hint' => 'Leeg = het e-mailadres uit de Customizer.', 'paneel' => 'Formulier' ),
				'knop'      => array( 'label' => 'Knoptekst', 'type' => 'text', 'default' => 'Verstuur', 'paneel' => 'Formulier' ),
				'bedankt'   => array( 'label' => 'Melding na verzenden', 'type' => 'text', 'default' => 'Bedankt voor uw bericht! We nemen zo snel mogelijk contact met u op.', 'paneel' => 'Formulier' ),
				'ico_tel'   => array( 'label' => 'Icoon telefoon', 'type' => 'image', 'hint' => 'Leeg = het standaardicoon.', 'paneel' => 'Iconen', 'toon' => array( 'gegevens' => true ) ),
				'ico_mail'  => array( 'label' => 'Icoon e-mail', 'type' => 'image', 'hint' => 'Leeg = het standaardicoon.', 'paneel' => 'Iconen', 'toon' => array( 'gegevens' => true ) ),
			), $overlap, array(
				'band' => array( 'label' => 'Blauwe band eronder', 'type' => 'select', 'opties' => array( 'geen' => $band_opties['geen'], 'leeg' => $band_opties['leeg'] ), 'default' => 'leeg', 'paneel' => 'Blauwe band' ),
			) ),
		),
		'activiteiten-slider' => array(
			'titel'        => 'Activiteitenslider',
			'beschrijving' => 'Grote foto met een kaart per activiteit en een iconenbalk. Titel, tekst, knop, foto en icoon pas je aan bij de activiteit zelf (menu "Activiteiten").',
			'icoon'        => 'images-alt2',
			'velden'       => array(
				'start'        => array( 'label' => 'Eerst getoonde activiteit', 'type' => 'select', 'opties' => 'activiteiten' ),
				'mobiel_boven' => array( 'label' => 'Op mobiel direct onder de header tonen', 'type' => 'toggle', 'default' => false, 'hint' => 'Handig op de homepage: op mobiel komt de slider dan vóór de andere blokken.', 'paneel' => 'Weergave' ),
			),
		),
		'icoon-rijen'         => array(
			'titel'        => 'Rijen met icoon',
			'beschrijving' => 'Rijen met een groot icoon (of eigen afbeelding) links en tekst rechts, gescheiden door stippellijnen.',
			'icoon'        => 'list-view',
			'velden'       => array(
				'rijen' => array(
					'label'   => 'Rijen',
					'type'    => 'repeater',
					'velden'  => array(
						'titel'      => array( 'label' => 'Titel', 'type' => 'text' ),
						'tekst'      => array( 'label' => 'Tekst', 'type' => 'textarea' ),
						'icoon'      => array( 'label' => 'Icoon', 'type' => 'select', 'opties' => bdj_iconen_groot() ),
						'afbeelding' => array( 'label' => 'Of een eigen afbeelding', 'type' => 'image', 'hint' => 'Gaat voor het icoon.' ),
					),
					'default' => array( array( 'titel' => '', 'tekst' => '', 'icoon' => 'over-verbinden', 'afbeelding' => 0 ) ),
				),
			),
		),
		'logos'               => array(
			'titel'        => 'Logo\'s',
			'beschrijving' => 'Grijs vlak met logo\'s in witte vakken, bijvoorbeeld keurmerken of partners.',
			'icoon'        => 'awards',
			'velden'       => array(
				'titel'   => array( 'label' => 'Titel (optioneel)', 'type' => 'text' ),
				'logos'   => array( 'label' => 'Logo\'s', 'type' => 'gallery', 'hint' => 'Leeg = de 4 standaard keurmerken van het theme.' ),
				'per_rij' => array( 'label' => 'Logo\'s per rij', 'type' => 'select', 'opties' => array( '2' => '2', '3' => '3', '4' => '4' ), 'default' => '2', 'paneel' => 'Weergave' ),
			),
		),
		'grote-kop'           => array(
			'titel'        => 'Grote kop met tekst',
			'beschrijving' => 'Grote kop met een korte tekst eronder, met veel witruimte.',
			'icoon'        => 'heading',
			'velden'       => array(
				'titel'  => array( 'label' => 'Kop', 'type' => 'text' ),
				'tekst'  => array( 'label' => 'Tekst', 'type' => 'textarea' ),
				'kleur'  => array( 'label' => 'Kleur van de kop', 'type' => 'select', 'opties' => array( 'oranje' => 'Oranje', 'blauw' => 'Blauw' ), 'default' => 'oranje', 'paneel' => 'Weergave' ),
				'ruimte' => array( 'label' => 'Witruimte', 'type' => 'select', 'opties' => array( 'veel' => 'Veel', 'normaal' => 'Normaal' ), 'default' => 'veel', 'paneel' => 'Weergave' ),
			),
		),
		'call-to-action'      => array(
			'titel'        => 'Blauw blok met foto',
			'beschrijving' => 'Blauw vlak met kop, tekst en knop naast een foto.',
			'icoon'        => 'megaphone',
			'velden'       => array(
				'titel'      => array( 'label' => 'Kop', 'type' => 'text' ),
				'tekst'      => array( 'label' => 'Tekst', 'type' => 'textarea' ),
				'knop_tekst' => array( 'label' => 'Knoptekst', 'type' => 'text' ),
				'knop_url'   => array( 'label' => 'Knop-URL', 'type' => 'text', 'hint' => 'Leeg = de pagina met het contactformulier.' ),
				'afbeelding' => array( 'label' => 'Foto', 'type' => 'image', 'hint' => 'Leeg = de standaardfoto (kantoor).' ),
				'foto_links' => array( 'label' => 'Foto links (tekst rechts)', 'type' => 'toggle', 'default' => false, 'paneel' => 'Weergave' ),
			),
		),
		'activiteiten-tegels' => array(
			'titel'        => 'Activiteitentegels',
			'beschrijving' => 'Lichtblauw vlak met een tegel per activiteit.',
			'icoon'        => 'grid-view',
			'velden'       => array(
				'titel'   => array( 'label' => 'Kop', 'type' => 'text' ),
				'behalve' => array( 'label' => 'Huidige activiteit overslaan', 'type' => 'toggle', 'default' => true ),
			),
		),
		'projecten'           => array(
			'titel'        => 'Projecten',
			'beschrijving' => 'Projectkaarten met fotoslider, twee naast elkaar. Projecten beheer je onder "Projecten".',
			'icoon'        => 'building',
			'velden'       => array(
				'modus'      => array( 'label' => 'Welke projecten', 'type' => 'select', 'opties' => array( 'activiteit' => 'Alle projecten van een activiteit', 'selectie' => 'Zelf projecten kiezen' ), 'default' => 'activiteit' ),
				'activiteit' => array( 'label' => 'Activiteit', 'type' => 'select', 'opties' => 'activiteit_paginas', 'hint' => 'Automatisch = de projecten van de activiteit waar dit blok op staat.', 'toon' => array( 'modus' => 'activiteit' ) ),
				'vanaf'      => array( 'label' => 'Eerste … projecten overslaan', 'type' => 'number', 'default' => 0, 'hint' => 'Tekst tussen de projecten? Zet twee Projecten-blokken met een Tekstkolom ertussen: 1e blok 4 tonen, 2e blok 4 overslaan.', 'toon' => array( 'modus' => 'activiteit' ) ),
				'aantal'     => array( 'label' => 'Aantal tonen (0 = alle)', 'type' => 'number', 'default' => 0, 'toon' => array( 'modus' => 'activiteit' ) ),
				'volgorde'   => array( 'label' => 'Volgorde', 'type' => 'projectvolgorde', 'hint' => 'Wordt direct opgeslagen en geldt voor alle Projecten-blokken van deze activiteit. Vetgedrukt = zichtbaar in dit blok.', 'toon' => array( 'modus' => 'activiteit' ) ),
				'projecten'  => array( 'label' => 'Projecten in dit blok', 'type' => 'projectselectie', 'hint' => 'Alleen voor dit blok; de volgorde hier is de volgorde op de pagina.', 'toon' => array( 'modus' => 'selectie' ) ),
			),
		),
		'tekstkolom'          => array(
			'titel'        => 'Tekstkolom',
			'beschrijving' => 'Smalle, gecentreerde tekstkolom met veel witruimte.',
			'icoon'        => 'text',
			'velden'       => array(
				'kop'   => array( 'label' => 'Kop (optioneel)', 'type' => 'text' ),
				'tekst' => array( 'label' => 'Tekst', 'type' => 'textarea' ),
			),
		),
		'tekstkolommen'       => array(
			'titel'        => 'Tekstkolommen',
			'beschrijving' => 'Eén tot drie tekstkolommen naast elkaar. Lege kolommen worden overgeslagen.',
			'icoon'        => 'columns',
			'velden'       => array(
				'kolom1' => array( 'label' => 'Kolom 1', 'type' => 'textarea' ),
				'kolom2' => array( 'label' => 'Kolom 2', 'type' => 'textarea' ),
				'kolom3' => array( 'label' => 'Kolom 3', 'type' => 'textarea' ),
			),
		),
	);
}

/** Opties die uit de database komen (activiteiten, termen). */
function bdj_blok_opties( $soort ) {
	$opties = array( '' => '— Automatisch —' );
	if ( 'activiteiten' === $soort ) {
		$opties = array( '' => '— De eerste —' );
		foreach ( bdj_activiteiten() as $act ) {
			$opties[ $act->post_name ] = get_the_title( $act );
		}
	} elseif ( 'activiteit_paginas' === $soort ) {
		$opties = bdj_activiteit_opties( '— Automatisch (deze pagina) —' );
	}
	return $opties;
}

/** Attribuut-definitie bij een veld. */
function bdj_blok_attribuut( $veld ) {
	switch ( $veld['type'] ) {
		case 'toggle':
			$attr = array( 'type' => 'boolean', 'default' => false );
			break;
		case 'number':
		case 'image':
			$attr = array( 'type' => 'integer', 'default' => 0 );
			break;
		case 'gallery':
		case 'projectselectie':
			$attr = array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'integer' ) );
			break;
		case 'repeater':
			$attr = array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'object' ) );
			break;
		default:
			$attr = array( 'type' => 'string', 'default' => '' );
	}
	if ( array_key_exists( 'default', $veld ) ) {
		$attr['default'] = $veld['default'];
	}
	return $attr;
}

/* -------------------------------------------------------------------------
 * Registratie
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {
	wp_register_script(
		'bdj-blokken',
		BDJ_URI . '/assets/js/blokken.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'wp-core-data', 'wp-api-fetch' ),
		BDJ_VERSION,
		true
	);

	foreach ( bdj_blok_schema() as $naam => $blok ) {
		$attributen = array();
		foreach ( $blok['velden'] as $key => $veld ) {
			if ( 'projectvolgorde' === $veld['type'] ) {
				continue; // geen attribuut: staat in menu_order van de projecten
			}
			$attributen[ $key ] = bdj_blok_attribuut( $veld );
		}
		register_block_type( 'bdj/' . $naam, array(
			'api_version'     => 3,
			'title'           => $blok['titel'],
			'description'     => $blok['beschrijving'],
			'category'        => 'bouwgroep-dejong',
			'icon'            => $blok['icoon'],
			'attributes'      => $attributen,
			// De header is geen blok meer (zie inc/header-velden.php); oud blok blijft werken maar staat niet in de blokkenlijst.
			'supports'        => array( 'html' => false, 'customClassName' => false, 'className' => false, 'inserter' => 'header' !== $naam ),
			'editor_script'   => 'bdj-blokken',
			'render_callback' => function ( $attr ) use ( $naam ) {
				$functie = 'bdj_render_' . str_replace( '-', '_', $naam );
				if ( ! function_exists( $functie ) ) {
					return '';
				}
				ob_start();
				$functie( $attr );
				return ob_get_clean();
			},
		) );
	}
} );

// Schema + opties naar de editor (velden in de zijbalk).
add_action( 'enqueue_block_editor_assets', function () {
	$schema = bdj_blok_schema();
	foreach ( $schema as &$blok ) {
		foreach ( $blok['velden'] as &$veld ) {
			if ( isset( $veld['opties'] ) && is_string( $veld['opties'] ) ) {
				$veld['opties'] = bdj_blok_opties( $veld['opties'] );
			}
		}
	}
	unset( $blok, $veld );
	wp_add_inline_script( 'bdj-blokken', 'window.bdjBlokken = ' . wp_json_encode( $schema ) . ';', 'before' );
	wp_enqueue_style( 'bdj-editor', BDJ_URI . '/assets/css/editor.css', array(), BDJ_VERSION );
} );

// Eigen categorie bovenaan in de blokkenlijst.
add_filter( 'block_categories_all', function ( $categorieen ) {
	array_unshift( $categorieen, array( 'slug' => 'bouwgroep-dejong', 'title' => 'Bouwgroep DE Jong', 'icon' => null ) );
	return $categorieen;
} );

// Theme-CSS ook in de editor (iframe), zodat het voorbeeld er hetzelfde uitziet.
add_action( 'enqueue_block_assets', function () {
	if ( is_admin() ) {
		wp_enqueue_style( 'bdj-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@300;400;500;700&display=swap', array(), null );
		wp_enqueue_style( 'bdj-main-editor', BDJ_URI . '/assets/css/main.css', array( 'bdj-fonts' ), BDJ_VERSION );
		wp_enqueue_style( 'bdj-editor-canvas', BDJ_URI . '/assets/css/editor-canvas.css', array( 'bdj-main-editor' ), BDJ_VERSION );
	}
} );

/* -------------------------------------------------------------------------
 * Hulpjes voor de renderfuncties
 * ---------------------------------------------------------------------- */

/** Pagina-ID, ook in het editorvoorbeeld (REST-verzoek met ?post_id=). */
function bdj_blok_post_id() {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_GET['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return absint( $_GET['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	return get_the_ID();
}

function bdj_blok_afbeelding_url( $id, $size = 'large' ) {
	$url = $id ? wp_get_attachment_image_url( (int) $id, $size ) : '';
	return $url ? $url : '';
}

/** Interne link "/pad/" omzetten naar volledige URL. */
function bdj_blok_url( $url ) {
	if ( $url && '/' === $url[0] && ( ! isset( $url[1] ) || '/' !== $url[1] ) ) {
		return home_url( $url );
	}
	return $url;
}

/** URL van de pagina met het contactformulier. */
function bdj_contact_url() {
	static $url = null;
	if ( null === $url ) {
		$url = home_url( '/contact/' );
		foreach ( get_pages() as $pagina ) {
			if ( has_block( 'bdj/contactformulier', $pagina ) ) {
				$url = get_permalink( $pagina );
				break;
			}
		}
	}
	return $url;
}

/** Kop van een wit vak: veld of paginatitel. */
function bdj_blok_kop( $a ) {
	return ! empty( $a['kop'] ) ? $a['kop'] : get_the_title( bdj_blok_post_id() );
}

/** Class-attribuut van het witte vak (overlap aan/uit). */
function bdj_vak_class( $a ) {
	return 'intro-vak' . ( empty( $a['overlap'] ) ? ' los' : '' );
}

/** Blauwe band onder een wit vak (tekstvak, vacaturelijst, contactformulier). */
function bdj_render_band( $a ) {
	$band = isset( $a['band'] ) ? $a['band'] : 'leeg';
	if ( 'geen' === $band ) {
		return;
	}
	if ( 'grijs' === $band ) {
		echo '<div class="grijs-band" aria-hidden="true"></div>';
		return;
	}
	$kort = ( 'tijdlijn' === $band && ! empty( $a['tijdlijn_kort'] ) ) ? ' tijdlijn-kort' : '';
	echo '<section class="blauw-band' . esc_attr( $kort ) . '"><div class="inner">';
	if ( 'kolommen' === $band ) {
		echo '<div class="twee-kolom"><div>' . bdj_tekst( $a['links'] ) . '</div><div>' . bdj_tekst( $a['rechts'] ) . '</div></div>'; // phpcs:ignore
	} elseif ( 'tijdlijn' === $band ) {
		$klein = bdj_iconen_klein();
		echo '<div class="tijdlijn">';
		foreach ( array( 'links' => array( 1, 3 ), 'rechts' => array( 2, 4 ) ) as $kant => $nrs ) {
			echo '<div class="kolom ' . esc_attr( $kant ) . '">';
			foreach ( $nrs as $nr ) {
				$eigen = isset( $a[ "stap{$nr}_afbeelding" ] ) ? (int) $a[ "stap{$nr}_afbeelding" ] : 0;
				$icoon = $eigen ? wp_get_attachment_image( $eigen, 'thumbnail', false, array( 'alt' => '' ) ) : '';
				if ( ! $icoon ) {
					$icoon = bdj_blok_icoon( $a[ "stap{$nr}_icoon" ], $klein );
				}
				$titel = esc_html( $a[ "stap{$nr}_titel" ] );
				echo '<h3 class="stap-' . (int) $nr . '">' . ( 'links' === $kant ? $titel . ' ' . $icoon : $icoon . ' ' . $titel ) . '</h3>'; // phpcs:ignore
				echo '<p class="stap-' . (int) $nr . '">' . esc_html( $a[ "stap{$nr}_tekst" ] ) . '</p>';
			}
			echo '</div>';
		}
		echo '</div>';
		if ( $a['meer_tekst'] && $a['meer_url'] ) {
			printf( '<div class="meer"><a class="link-lijn" href="%s">%s</a></div>', esc_url( bdj_blok_url( $a['meer_url'] ) ), esc_html( $a['meer_tekst'] ) );
		}
	}
	echo '</div></section>';
}

/** Kop-element met veilig niveau. */
function bdj_kop_html( $tekst, $niveau = 'h1' ) {
	$niveau = in_array( $niveau, array( 'h1', 'h2' ), true ) ? $niveau : 'h1';
	return sprintf( '<%1$s class="vak-kop">%2$s</%1$s>', $niveau, esc_html( $tekst ) );
}

/* -------------------------------------------------------------------------
 * Renderfuncties per blok
 * ---------------------------------------------------------------------- */

function bdj_render_header( $a ) {
	$post_id = bdj_blok_post_id();
	$id      = $a['afbeelding'] ? (int) $a['afbeelding'] : (int) get_post_thumbnail_id( $post_id );
	$hoogte  = in_array( $a['hoogte'], array( 'middel', 'klein' ), true ) ? ' hero-' . $a['hoogte'] : '';
	?>
	<section class="hero<?php echo esc_attr( $hoogte ); ?>">
		<?php
		if ( $id ) {
			echo wp_get_attachment_image( $id, 'bdj-hero', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => esc_attr( get_the_title( $post_id ) ) ) );
		}
		?>
		<?php if ( $a['titel'] ) : ?>
			<div class="hero-tekst">
				<div class="hero-vak">
					<div class="titel"><?php echo esc_html( $a['titel'] ); ?></div>
					<?php if ( $a['link_tekst'] && $a['link_url'] ) : ?>
						<a class="link-lijn" href="<?php echo esc_url( bdj_blok_url( $a['link_url'] ) ); ?>"><?php echo esc_html( $a['link_tekst'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

function bdj_render_tekstvak( $a ) {
	?>
	<section class="<?php echo esc_attr( bdj_vak_class( $a ) ); ?>">
		<?php echo bdj_kop_html( bdj_blok_kop( $a ), $a['kop_niveau'] ); // phpcs:ignore ?>
		<?php if ( $a['tekst'] ) : ?>
			<div class="intro-tekst<?php echo empty( $a['tekst_mobiel'] ) ? ' niet-mobiel' : ''; ?>"><div class="smal"><?php echo bdj_tekst( $a['tekst'] ); // phpcs:ignore ?></div></div>
		<?php endif; ?>
	</section>
	<?php
	bdj_render_band( $a );
}

function bdj_render_vacatures( $a ) {
	$vacatures = get_posts( array(
		'post_type'      => 'vacature',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );
	?>
	<section class="<?php echo esc_attr( bdj_vak_class( $a ) ); ?>">
		<?php echo bdj_kop_html( bdj_blok_kop( $a ) ); // phpcs:ignore ?>
		<div class="intro-tekst">
			<?php if ( $a['tekst'] ) : ?>
				<div class="smal" style="padding-bottom:64px"><?php echo bdj_tekst( $a['tekst'] ); // phpcs:ignore ?></div>
			<?php endif; ?>
			<div class="vacature-lijst">
				<?php if ( ! $vacatures ) : ?>
					<div class="geen-vacatures"><?php echo bdj_tekst( $a['geen_tekst'] ); // phpcs:ignore ?></div>
				<?php endif; ?>
				<?php foreach ( $vacatures as $vacature ) : ?>
					<details class="vacature">
						<summary>
							<span class="toggle"><?php echo bdj_svg( 'vacature-pijl' ); // phpcs:ignore ?></span>
							<span class="titel-wrap"><span class="titel"><?php echo esc_html( get_the_title( $vacature ) ); ?></span></span>
						</summary>
						<div class="vacature-inhoud"><?php echo bdj_vacature_inhoud( $vacature ); // phpcs:ignore ?></div>
					</details>
				<?php endforeach; ?>
			</div>
			<?php if ( $a['contact_tekst'] ) : ?>
				<div class="vacature-contact"><?php echo bdj_tekst( $a['contact_tekst'] ); // phpcs:ignore ?></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	bdj_render_band( $a );
}

function bdj_render_contactformulier( $a ) {
	$telefoon = bdj_optie( 'telefoon' );
	$email    = bdj_optie( 'email' );
	$post_id  = bdj_blok_post_id();
	$ico_tel  = bdj_blok_afbeelding_url( $a['ico_tel'], 'thumbnail' );
	$ico_mail = bdj_blok_afbeelding_url( $a['ico_mail'], 'thumbnail' );
	?>
	<section class="<?php echo esc_attr( bdj_vak_class( $a ) ); ?>">
		<?php echo bdj_kop_html( bdj_blok_kop( $a ) ); // phpcs:ignore ?>
		<div class="intro-tekst">
			<div class="contact-vak<?php echo $a['gegevens'] ? '' : ' zonder-gegevens'; ?>" id="formulier">
				<?php if ( $a['gegevens'] ) : ?>
					<div class="contact-info">
						<h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
						<p><?php echo esc_html( bdj_optie( 'adres' ) ); ?><br><?php echo esc_html( bdj_optie( 'postcode' ) ); ?></p>
						<?php if ( $telefoon ) : ?>
							<div class="contact-regel"><img src="<?php echo esc_url( $ico_tel ? $ico_tel : bdj_asset( 'ico-tel.png' ) ); ?>" alt=""><a href="<?php echo esc_attr( bdj_tel_link( $telefoon ) ); ?>"><?php echo esc_html( $telefoon ); ?></a></div>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<div class="contact-regel"><img src="<?php echo esc_url( $ico_mail ? $ico_mail : bdj_asset( 'ico-mail.png' ) ); ?>" alt=""><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></div>
						<?php endif; ?>
						<p>
							<?php if ( bdj_optie( 'kvk' ) ) : ?>KvK <?php echo esc_html( bdj_optie( 'kvk' ) ); ?><br><?php endif; ?>
							<?php if ( bdj_optie( 'btw' ) ) : ?>BTW <?php echo esc_html( bdj_optie( 'btw' ) ); ?><?php endif; ?>
						</p>
					</div>
				<?php endif; ?>
				<form class="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<?php echo bdj_contact_melding( $a['bedankt'] ); // phpcs:ignore ?>
					<input type="hidden" name="action" value="bdj_contact">
					<input type="hidden" name="_bdj_pagina" value="<?php echo (int) $post_id; ?>">
					<input type="hidden" name="_bdj_terug" value="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
					<?php wp_nonce_field( 'bdj_contact', '_bdj_nonce' ); ?>
					<label>Uw naam<input type="text" name="naam" required autocomplete="name"></label>
					<label>Uw e-mailadres<input type="email" name="email" required autocomplete="email"></label>
					<label>Uw telefoonnummer<input type="tel" name="telefoon" autocomplete="tel"></label>
					<label>Uw bericht<textarea name="bericht" required></textarea></label>
					<label class="hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
					<p class="verstuur"><button type="submit" class="btn"><?php echo esc_html( $a['knop'] ? $a['knop'] : 'Verstuur' ); ?></button></p>
				</form>
			</div>
		</div>
	</section>
	<?php
	bdj_render_band( $a );
}

function bdj_render_activiteiten_slider( $a ) {
	get_template_part( 'template-parts/activiteiten-slider', null, array( 'start' => $a['start'], 'mobiel_boven' => ! empty( $a['mobiel_boven'] ) ) );
}

function bdj_render_icoon_rijen( $a ) {
	$rijen = array_filter( (array) $a['rijen'], function ( $r ) {
		return ! empty( $r['titel'] ) || ! empty( $r['tekst'] );
	} );
	if ( ! $rijen ) {
		return;
	}
	$groot = bdj_iconen_groot();
	?>
	<section class="over-wit">
		<?php foreach ( $rijen as $rij ) : ?>
			<?php
			$beeld = '';
			if ( ! empty( $rij['afbeelding'] ) ) {
				$beeld = wp_get_attachment_image( (int) $rij['afbeelding'], 'medium' );
			}
			if ( ! $beeld && ! empty( $rij['icoon'] ) ) {
				$beeld = bdj_blok_icoon( $rij['icoon'], $groot );
			}
			?>
			<div class="over-rij<?php echo $beeld ? '' : ' zonder-icoon'; ?>">
				<?php if ( $beeld ) : ?><div class="links"><?php echo $beeld; // phpcs:ignore ?></div><?php endif; ?>
				<div class="rechts">
					<?php if ( ! empty( $rij['titel'] ) ) : ?><h2><?php echo esc_html( $rij['titel'] ); ?></h2><?php endif; ?>
					<?php echo bdj_tekst( isset( $rij['tekst'] ) ? $rij['tekst'] : '' ); // phpcs:ignore ?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>
	<?php
}

function bdj_render_logos( $a ) {
	$logos = array();
	foreach ( (array) $a['logos'] as $id ) {
		$url = bdj_blok_afbeelding_url( $id, 'medium_large' );
		if ( $url ) {
			$alt     = get_post_meta( $id, '_wp_attachment_image_alt', true );
			$logos[] = array( $url, $alt ? $alt : get_the_title( $id ) );
		}
	}
	if ( ! $logos ) {
		for ( $i = 1; $i <= 4; $i++ ) {
			$logos[] = array( bdj_asset( "icons/keurmerk-{$i}.svg" ), 'Keurmerk' );
		}
	}
	$per_rij = in_array( $a['per_rij'], array( '2', '3', '4' ), true ) ? (int) $a['per_rij'] : 2;
	?>
	<section class="keurmerken">
		<?php if ( $a['titel'] ) : ?><h2 class="logos-titel"><?php echo esc_html( $a['titel'] ); ?></h2><?php endif; ?>
		<div class="logo-grid" style="--kolommen:<?php echo (int) $per_rij; ?>">
			<?php foreach ( $logos as $logo ) : ?>
				<div><img src="<?php echo esc_url( $logo[0] ); ?>" alt="<?php echo esc_attr( $logo[1] ); ?>"></div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

function bdj_render_grote_kop( $a ) {
	if ( ! $a['titel'] && ! $a['tekst'] ) {
		return;
	}
	$classes = 'idee';
	if ( 'blauw' === $a['kleur'] ) {
		$classes .= ' blauw';
	}
	if ( 'normaal' === $a['ruimte'] ) {
		$classes .= ' compact';
	}
	?>
	<section class="<?php echo esc_attr( $classes ); ?>">
		<?php if ( $a['titel'] ) : ?><h2><?php echo esc_html( $a['titel'] ); ?></h2><?php endif; ?>
		<?php echo bdj_tekst( $a['tekst'] ); // phpcs:ignore ?>
	</section>
	<?php
}

function bdj_render_call_to_action( $a ) {
	$post_id = bdj_blok_post_id();
	$foto    = bdj_blok_afbeelding_url( $a['afbeelding'] );
	if ( ! $foto ) {
		$foto = bdj_asset( 'verbinden.jpg' );
	}
	$url = $a['knop_url'] ? bdj_blok_url( $a['knop_url'] ) : bdj_contact_url();
	?>
	<section class="cta<?php echo $a['foto_links'] ? ' foto-links' : ''; ?>">
		<div class="tekst">
			<?php if ( $a['titel'] ) : ?><h2><?php echo esc_html( $a['titel'] ); ?></h2><?php endif; ?>
			<?php if ( $a['tekst'] ) : ?><?php echo bdj_tekst( $a['tekst'] ); // phpcs:ignore ?><?php endif; ?>
			<?php if ( $a['knop_tekst'] ) : ?><a class="btn btn-wit" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $a['knop_tekst'] ); ?></a><?php endif; ?>
		</div>
		<div class="beeld" style="background-image:url('<?php echo esc_url( $foto ); ?>')"></div>
	</section>
	<?php
}

function bdj_render_activiteiten_tegels( $a ) {
	$behalve = $a['behalve'] ? (int) bdj_blok_post_id() : 0;
	?>
	<section class="andere">
		<div class="inner">
			<?php if ( $a['titel'] ) : ?><h2><?php echo esc_html( $a['titel'] ); ?></h2><?php endif; ?>
			<div class="andere-grid">
				<?php foreach ( bdj_activiteiten() as $act ) : ?>
					<?php if ( $act->ID === $behalve ) { continue; } ?>
					<a href="<?php echo esc_url( get_permalink( $act ) ); ?>"><?php echo bdj_activiteit_icoon_img( $act ); // phpcs:ignore ?><span><?php echo esc_html( get_the_title( $act ) ); ?></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function bdj_render_projecten( $a ) {
	if ( 'selectie' === $a['modus'] ) {
		$ids   = array_filter( array_map( 'absint', (array) $a['projecten'] ) );
		$lijst = $ids ? get_posts( array(
			'post_type'      => 'project',
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'orderby'        => 'post__in',
		) ) : array();
		if ( ! $lijst && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			echo '<p class="bdj-leeg">Nog geen projecten gekozen voor dit blok.</p>';
		}
	} else {
		$act   = $a['activiteit'] ? bdj_activiteit_id( $a['activiteit'] ) : bdj_blok_post_id();
		$lijst = bdj_projecten( $act );
		$lijst = array_slice( $lijst, max( 0, (int) $a['vanaf'] ), (int) $a['aantal'] > 0 ? (int) $a['aantal'] : null );
		if ( ! $lijst && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			echo '<p class="bdj-leeg">Geen projecten gevonden voor "' . esc_html( get_the_title( $act ) ) . '" (of alle projecten zijn overgeslagen). Koppel projecten via Projecten → Activiteit.</p>';
		}
	}
	if ( ! $lijst ) {
		return;
	}
	echo '<section class="projecten-blok">';
	foreach ( array_chunk( $lijst, 2 ) as $rij ) {
		echo '<div class="project-rij">';
		foreach ( $rij as $project ) {
			get_template_part( 'template-parts/project', null, array( 'project' => $project ) );
		}
		echo '</div>';
	}
	echo '</section>';
}

function bdj_render_tekstkolom( $a ) {
	if ( ! $a['tekst'] && ! $a['kop'] ) {
		return;
	}
	echo '<section class="project-intro">';
	if ( $a['kop'] ) {
		echo '<h2>' . esc_html( $a['kop'] ) . '</h2>';
	}
	echo bdj_tekst( $a['tekst'] ) . '</section>'; // phpcs:ignore
}

function bdj_render_tekstkolommen( $a ) {
	$kolommen = array_filter( array( $a['kolom1'], $a['kolom2'], $a['kolom3'] ) );
	if ( ! $kolommen ) {
		return;
	}
	echo '<section class="drie-kolom" style="--kolommen:' . count( $kolommen ) . '">';
	foreach ( $kolommen as $kolom ) {
		echo '<div>' . bdj_tekst( $kolom ) . '</div>'; // phpcs:ignore
	}
	echo '</section>';
}

/* -------------------------------------------------------------------------
 * Hulpmiddel voor de import: blok-markup maken
 * ---------------------------------------------------------------------- */

function bdj_blok( $naam, $attr = array() ) {
	$json = $attr ? ' ' . wp_json_encode( $attr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
	return '<!-- wp:bdj/' . $naam . $json . " /-->\n\n";
}

<?php
/**
 * Startinhoud importeren: pagina's, menu, activiteiten, projecten (met foto's), vacatures.
 *
 * Te vinden onder Gereedschap → "DE Jong importeren". Veilig om opnieuw te draaien:
 * bestaande pagina's/projecten/afbeeldingen worden hergebruikt, niet dubbel aangemaakt.
 */

defined( 'ABSPATH' ) || exit;

define( 'BDJ_BRON', 'https://www.bouwgroepdejong.nl' );

/* -------------------------------------------------------------------------
 * Data
 * ---------------------------------------------------------------------- */

function bdj_import_activiteiten() {
	return array(
		'wonen'                  => array( 'Wonen', 'woningbouw', 'wonen-3-3840.jpg', 'activities-wonen-19d42fdc.jpg', 'Van vrijstaande woning tot appartementencomplex: we beginnen bij uw woonwensen en brengen de juiste partijen bij elkaar.' ),
		'utiliteit'              => array( 'Utiliteit', 'utiliteit', 'utiliteit-3-3840.jpg', 'activities-utiliteit-e9ebae37.jpg', 'Winkels, kantoren en maatschappelijk vastgoed. We denken mee over functie, planning en budget, zodat het pand werkt zoals bedoeld.' ),
		'food'                   => array( 'Food', 'food', 'food-3840.jpg', 'activities-food-eca43820.jpg', 'Bouwen voor de foodsector vraagt om kennis van hygiëne, logistiek en regelgeving. Die kennis zetten we vanaf dag één in.' ),
		'bedrijfspand-industrie' => array( 'Bedrijfspand / industrie', 'bedrijfspand / industrie', 'bedrijfspand-industrie-2-3840.jpg', 'activities-bedrijfspand-industrie-56152467.jpg', 'Een bedrijfspand dat past bij uw processen en kan meegroeien met uw organisatie, van ontwerp tot oplevering.' ),
		'logistiek-warehousing'  => array( 'Logistiek / warehousing', 'logistiek / warehousing', 'logistiek-warehousing-3840.jpg', 'activities-logistiek-warehousing-5dedfe81.jpg', 'Ruime hallen en efficiënte opslag. We plannen slim, zodat uw logistieke locatie snel en zonder verrassingen in gebruik gaat.' ),
		'verbouw-onderhoud'      => array( 'Verbouw / onderhoud', 'verbouw / onderhoud', 'verbouw-onderhoud-3840.jpg', 'activities-verbouw-onderhoud-e464dd62.jpg', 'Uitbreiden, renoveren of onderhouden: we werken zorgvuldig en houden rekening met het gebruik van het pand.' ),
		'versterkingen-ncg'      => array( 'Versterkingen NCG', 'versterkingen NCG', 'versterkingen-ncg-3840.jpg', 'activities-versterkingen-ncg-87babc05.jpg', 'Versterkingsprojecten in het aardbevingsgebied voeren we uit met aandacht voor de bewoners en een heldere planning.' ),
	);
}

/** Projecten: activiteit => [ [titel, plaats, map, [foto-nummers]] ]. */
function bdj_import_projecten() {
	$p = array(
		'wonen'                  => array(
			array( 'Loodswoningen', 'Leeuwarden', 'Loodswoningen Leeuwarden', array( 1, 2, 3, 4, 5 ) ),
			array( '10 appartementen', 'Appelscha', '10 appartementen Appelscha', array( 1, 2, 3, 4 ) ),
			array( '2 onder 1 kap', 'Joure', '2 onder 1 kap Joure', array( 1, 2, 3 ) ),
			array( '2 onder 1 kap woningen', 'Nooitgedacht', '2 onder 1 kap woningen Nooitgedacht', array( 1, 2, 3 ) ),
			array( '3 appartementen', 'Oosterwolde', '3 appartementen Oosterwolde', array( 1, 2, 3, 4 ) ),
			array( '10 woningen', 'Grou', '10 woningen Grou', array( 1, 2 ) ),
			array( 'Appartementen', 'Heerenveen', 'Appartementen Heerenveen', array( 1, 2, 3 ) ),
			array( '10 woningen', 'Leeuwarden', '10 woningen Leeuwarden', array( 1, 2, 3 ) ),
			array( '17 woningen', 'Joure', '17 woningen Joure', array( 1, 2, 3 ) ),
			array( '55 woningen', 'Appelscha', '55 woningen Appelscha', array( 1, 2, 3, 4 ) ),
			array( 'Appartementen', 'Oosterwolde', 'Appartementen Oosterwolde', array( 1, 2, 3 ) ),
			array( 'Woning', 'Donkerbroek', 'Woning Donkerbroek', array( 1, 2, 3 ) ),
			array( 'Woning', 'Eelderwolde', 'Woning Eelderwolde', array( 1, 2, 3, 4, 5 ) ),
			array( 'Woning', 'Wommels', 'Woning Wommels', array( 1, 2, 3, 4 ) ),
			array( 'Woning', 'Zorgvlied', 'Woning Zorgvlied', array( 1, 2, 3, 4 ) ),
			array( 'Woningen', 'Techum', 'Woningen Techum', array( 1, 2, 3, 4 ) ),
		),
		'utiliteit'              => array(
			array( 'Nieuwbouw supermarkt', 'Zwolle', 'Nieuwbouw supermarkt Zwolle', array( 1, 2, 3 ) ),
			array( 'Supermarkt', 'Berlikum', 'Supermarkt Berlikum', array( 1, 2, 3 ) ),
			array( 'Supermarkt', 'Assen', 'Supermarkt Assen', array( 1, 2, 3, 4 ) ),
			array( 'Supermarkt', 'Leek', 'Supermarkt Leek', array( 1, 2, 3 ) ),
			array( 'Uitvaartfaciliteit', 'Groningen', 'Uitvaartfaciliteit Groningen', array( 1, 2, 3, 4, 5 ) ),
			array( 'Supermarkt', 'Akkrum', 'Supermarkt Akkrum', array( 1, 2 ) ),
			array( 'Supermarkt', 'Ommen', 'Supermarkt Ommen', array( 4, 1, 2, 3, 5 ) ),
			array( 'Supermarkt', 'Appelscha', 'Supermarkt Appelscha', array( 1, 2, 3 ) ),
			array( 'Kantoren', 'Drachten', 'Kantoren Drachten', array( 1, 2, 3 ) ),
			array( 'Restaurant', 'Oldeberkoop', 'Restaurant Oldeberkoop', array( 1, 2, 3, 4, 5 ) ),
		),
		'food'                   => array(
			array( 'Bedrijfspand', 'Purmerend', 'Bedrijfspand Purmerend', array( 1, 2, 3, 4, 5 ) ),
			array( 'Verbouw groothandel', 'Oosterwolde', 'Verbouw groothandel Oosterwolde', array( 1, 2, 3, 4, 5, 6 ) ),
		),
		'bedrijfspand-industrie' => array(
			array( 'Bedrijfspand Oosterwolde 7', '', 'Bedrijfspand Oosterwolde 7', array( 1, 2 ) ),
			array( 'Bedrijfspand Oosterwolde 2', '', 'Bedrijfspand Oosterwolde 2', array( 1, 2, 3, 4, 5 ) ),
			array( 'Bedrijfspand Gorredijk', '', 'Bedrijfspand Gorredijk', array( 1, 2, 3, 4, 5 ) ),
			array( 'Bedrijfspand Oosterwolde 4', '', 'Bedrijfspand Oosterwolde 4', array( 2, 1, 3 ) ),
			array( 'Bedrijfspand Oosterwolde 5', '', 'Bedrijfspand Oosterwolde 5', array( 1, 2 ) ),
			array( 'Bedrijfspand Oosterwolde 6', '', 'Bedrijfspand Oosterwolde 6', array( 1, 2, 3 ) ),
			array( 'Bedrijfspand Oosterwolde', '', 'Bedrijfspand Oosterwolde', array( 1, 2, 3, 4 ) ),
			array( 'Boerderij', '', 'Boerderij', array( 1, 2, 3 ) ),
		),
		'logistiek-warehousing'  => array(
			array( 'Groothandel', 'Oosterwolde', 'Groothandel Oosterwolde', array( 1, 2, 3, 4 ) ),
			array( 'Werkplaats', 'Haulerwijk', 'Werkplaats Haulerwijk', array( 1, 2, 3, 4, 5, 6 ) ),
		),
		'verbouw-onderhoud'      => array(
			array( 'Aanbouw restaurant', 'Makkum', 'Aanbouw restaurant Makkum', array( 1, 2, 3 ) ),
			array( 'Renovatie gevel winkel', 'Sneek', 'Renovatie gevel winkel Sneek', array( 1, 2, 3 ) ),
			array( 'Renovatie school', 'Oosterwolde', 'Renovatie school Oosterwolde', array( 1, 2, 3, 4 ) ),
			array( 'Verbouw badkamer', '', 'Verbouw badkamer', array( 1, 2, 3, 4 ) ),
			array( 'Verbouw snackbar', 'Meppel', 'Verbouw Snackbar Meppel', array( 1, 2, 3 ) ),
		),
		'versterkingen-ncg'      => array(
			array( 'Versterking', 'Ten Boer', 'Versterking Ten Boer', array( 1, 2, 3, 4, 5, 6 ) ),
			array( 'Versterking', 'Ten Post', 'Versterking Ten Post', array( 1, 2, 3, 4, 5, 6 ) ),
		),
	);
	return $p;
}

/** Lijst van importtaken. Taak 0 = basis, daarna afbeeldingen en projecten. */
function bdj_import_taken() {
	$taken   = array( array( 'basis' ) );
	$headers = array(
		'home'              => 'home-3840.jpg',
		'over-ons'          => 'over-ons-3840.jpg',
		'kwaliteitsborging' => 'kwaliteitsborging-3840.jpg',
		'vacatures'         => 'vacatures-3-3840.jpg',
		'contact'           => 'contact-3840.jpg',
	);
	foreach ( $headers as $slug => $bestand ) {
		$taken[] = array( 'header', $slug, BDJ_BRON . '/assets/img/headers/' . $bestand );
	}
	foreach ( bdj_import_activiteiten() as $slug => $a ) {
		$taken[] = array( 'header', $slug, BDJ_BRON . '/assets/img/headers/' . $a[2] );
		$taken[] = array( 'slider', $slug, BDJ_BRON . '/build/assets/' . $a[3] );
	}
	foreach ( bdj_import_projecten() as $act => $projecten ) {
		foreach ( $projecten as $i => $project ) {
			$taken[] = array( 'project', $act, $i );
		}
	}
	return $taken;
}

/* -------------------------------------------------------------------------
 * Uitvoeren
 * ---------------------------------------------------------------------- */

function bdj_import_voer_uit( $index ) {
	$taken = bdj_import_taken();
	if ( ! isset( $taken[ $index ] ) ) {
		return new WP_Error( 'bdj', 'Onbekende taak' );
	}
	$taak = $taken[ $index ];

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	switch ( $taak[0] ) {
		case 'basis':
			return bdj_import_basis();

		case 'header':
			$pagina = get_page_by_path( $taak[1] );
			if ( ! $pagina ) {
				return 'Pagina ' . $taak[1] . ' niet gevonden';
			}
			$id = bdj_import_afbeelding( $taak[2], $pagina->ID );
			if ( is_wp_error( $id ) ) {
				return $id;
			}
			set_post_thumbnail( $pagina->ID, $id );
			return 'Headerfoto ' . $taak[1];

		case 'slider':
			$pagina = get_page_by_path( $taak[1] );
			if ( ! $pagina ) {
				return 'Pagina ' . $taak[1] . ' niet gevonden';
			}
			$id = bdj_import_afbeelding( $taak[2], $pagina->ID );
			if ( is_wp_error( $id ) ) {
				return $id;
			}
			update_post_meta( $pagina->ID, '_bdj_slider_afbeelding', $id );
			return 'Sliderfoto ' . $taak[1];

		case 'project':
			return bdj_import_project( $taak[1], $taak[2] );
	}
	return '';
}

/** Afbeelding ophalen (of bestaande hergebruiken op basis van de bron-URL). */
function bdj_import_afbeelding( $url, $post_id = 0, $titel = null ) {
	$bestaand = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_bdj_bron',
		'meta_value'     => $url,
	) );
	if ( $bestaand ) {
		return (int) $bestaand[0];
	}
	$id = media_sideload_image( $url, $post_id, $titel, 'id' );
	if ( ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_bdj_bron', $url );
	}
	return $id;
}

/** Pagina aanmaken of bijwerken (op slug). */
function bdj_import_pagina( $slug, $titel, $template, $inhoud = '', $meta = array(), $extra = array() ) {
	$type   = isset( $extra['post_type'] ) ? $extra['post_type'] : 'page';
	$pagina = get_page_by_path( $slug, OBJECT, $type );
	$data   = array_merge( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $titel,
		'post_name'    => $slug,
		'post_content' => $inhoud,
	), $extra );
	// wp_insert_post() haalt backslashes weg; blok-JSON bevat backslashes (regeleinden, aanhalingstekens) en moet dus 'geslasht' worden.
	$data = wp_slash( $data );
	if ( $pagina ) {
		$data['ID'] = $pagina->ID;
		$id         = wp_update_post( $data );
	} else {
		$id = wp_insert_post( $data );
	}
	if ( 'page' === $type ) {
		update_post_meta( $id, '_wp_page_template', $template ? $template : 'default' );
	}
	// Oude sectievelden (van vóór de blokken) opruimen; activiteit-gegevens blijven.
	foreach ( array_keys( get_post_meta( $id ) ) as $key ) {
		if ( 0 === strpos( $key, '_bdj_' ) && ! in_array( $key, array( '_bdj_icoon', '_bdj_knoptekst', '_bdj_slider_afbeelding', '_bdj_header_tonen', '_bdj_header_afbeelding', '_bdj_header_titel', '_bdj_header_link_tekst', '_bdj_header_link_url', '_bdj_header_hoogte' ), true ) ) {
			delete_post_meta( $id, $key );
		}
	}
	if ( ! array_key_exists( 'header_tonen', $meta ) ) {
		$meta['header_tonen'] = true;
	}
	foreach ( $meta as $key => $waarde ) {
		update_post_meta( $id, '_bdj_' . $key, $waarde );
	}
	return $id;
}

/** Alinea's als blokken. */
function bdj_blokken( $alineas ) {
	$uit = '';
	foreach ( (array) $alineas as $a ) {
		$uit .= "<!-- wp:paragraph -->\n<p>" . $a . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return $uit;
}

function bdj_import_basis() {
	// Algemene instellingen.
	update_option( 'blogname', 'Bouwgroep DE Jong' );
	update_option( 'blogdescription', '' );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	// Voorbeeldinhoud van WordPress naar de prullenbak.
	foreach ( array( 'sample-page', 'voorbeeld-pagina' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( $p ) {
			wp_trash_post( $p->ID );
		}
	}
	$hallo = get_posts( array( 'name' => 'hello-world', 'post_type' => 'post', 'posts_per_page' => 1 ) );
	if ( $hallo ) {
		wp_trash_post( $hallo[0]->ID );
	}

	// Pagina's (als blokken).
	$idee = bdj_blok( 'grote-kop', array(
		'titel' => 'Bouwen begint bij een goed idee.',
		'tekst' => 'Wij denken met u mee en leggen verbindingen tussen alle partijen die nodig zijn om uw project te realiseren. We zijn open over onze werkwijze en blijven doorvragen tot het doel helder is. Zo komen we samen tot het beste plan.',
	) );
	$cta  = bdj_blok( 'call-to-action', array(
		'titel'      => 'Kennismaken met Bouwgroep DE Jong',
		'tekst'      => 'Een bouwproject begint lang voordat de eerste paal de grond in gaat. Hoe goed die voorbereiding is, bepaalt het eindresultaat. Wij zijn tevreden als u dat ook bent.',
		'knop_tekst' => 'Laten we verbinden',
	) );
	$tegels = bdj_blok( 'activiteiten-tegels', array( 'titel' => 'Onze andere activiteiten' ) );

	$home = bdj_import_pagina( 'home', 'Home', '', bdj_blok( 'tekstvak', array(
		'kop'         => 'De essentie van bouwen',
		'tekst'       => 'Een gebouw neerzetten is het eindpunt van een heel traject. Juist de stappen vóór de bouw bepalen of een project slaagt. Wij noemen het een succes als u als opdrachtgever bereikt wat u voor ogen had. Daarom draait het bij ons om verbinden, meedenken en organiseren, nog voordat we gaan bouwen.',
		'band'         => 'tijdlijn',
		'tekst_mobiel' => false,
		'stap1_tekst' => 'Betrek ons vroeg in het proces. Zo profiteert u vanaf het begin van onze kennis, ons netwerk van partners en onze creativiteit.',
		'stap2_tekst' => 'We stellen de juiste vragen, zodat we samen helder krijgen wat u écht nodig heeft en daar gericht naartoe kunnen werken.',
		'stap3_tekst' => 'Een goed georganiseerd proces maakt het verschil. We stemmen alle bouwpartners op elkaar af, zodat alles op tijd op zijn plek valt.',
		'stap4_tekst' => 'Dan gaan we bouwen. Met een vast team zetten we het plan om in een resultaat waar u trots op bent.',
		'meer_tekst'  => 'Meer hierover',
		'meer_url'    => '/over-ons/',
	) ) . bdj_blok( 'activiteiten-slider', array( 'mobiel_boven' => true ) ) . $idee . $cta, array(
		'header_tonen'      => true,
		'header_titel'      => 'Samen naar één compleet plan',
		'header_link_tekst' => 'Lees hoe we dat doen',
		'header_link_url'   => '/over-ons/',
	), array( 'menu_order' => 0 ) );

	$over = bdj_import_pagina( 'over-ons', 'Over ons', '', bdj_blok( 'tekstvak', array(
		'kop'    => 'Verbinden, meedenken en organiseren vóórdat we gaan bouwen',
		'tekst'  => 'Plaatshoudertekst: een korte inleiding over wie Bouwgroep DE Jong is, waar het bedrijf voor staat en hoe het samenwerkt met opdrachtgevers.',
		'band'   => 'kolommen',
		'links'  => "Bouwgroep DE Jong is een middelgroot bouwbedrijf uit Oosterwolde. We bouwen woningen, bedrijfspanden en utiliteitsgebouwen in heel Noord-Nederland.\n\nPlaatshoudertekst: vertel hier iets over de geschiedenis van het bedrijf en het team.",
		'rechts' => "Onze kracht zit in de voorbereiding. Door vroeg betrokken te zijn, kunnen we meedenken over ontwerp, planning en kosten.\n\nPlaatshoudertekst: vertel hier wat opdrachtgevers aan die werkwijze hebben.",
	) ) . bdj_blok( 'icoon-rijen', array( 'rijen' => array(
		array( 'icoon' => 'over-verbinden', 'titel' => 'Verbinden', 'tekst' => "Wij zijn de eerste stap op weg naar een geslaagd bouwproject. Door ons vroeg aan tafel te vragen, profiteert u direct van onze ervaring, ons netwerk en onze ideeën.\n\nIn de voorbereidende fase brengen we samen met u alles in kaart: wensen, randvoorwaarden, planning en budget. We selecteren de juiste partners en zorgen dat iedereen vanaf het begin dezelfde kant op kijkt." ),
		array( 'icoon' => 'over-meedenken', 'titel' => 'Meedenken', 'tekst' => "We vragen door tot we begrijpen wat u werkelijk nodig heeft. Pas dan kunnen we gericht werken aan het doel achter de vraag.\n\nU brengt het idee in, wij helpen het uit te werken tot een plan dat klopt, met eerlijk advies over wat wel en niet slim is." ),
		array( 'icoon' => 'over-organiseren', 'titel' => 'Organiseren', 'tekst' => 'Een strak georganiseerd proces is de basis van elk goed project. We stemmen de werkzaamheden van alle bouwpartners op elkaar af en bewaken planning, kwaliteit en kosten, zodat er geen tijd en geld verloren gaat.' ),
		array( 'icoon' => 'over-realiseren', 'titel' => 'Realiseren', 'tekst' => "Dan komt het moment waarop het plan werkelijkheid wordt. Samen met een vast team van vakmensen bouwen we wat we samen hebben bedacht.\n\nUitvoerders, timmerlieden en vaste onderaannemers maken het team compleet. Korte lijnen en duidelijke afspraken zorgen ervoor dat u altijd weet waar u aan toe bent." ),
	) ) ) . bdj_blok( 'activiteiten-slider', array( 'start' => 'bedrijfspand-industrie' ) ) . $idee . $cta, array(), array( 'menu_order' => 10 ) );

	$kwal = bdj_import_pagina( 'kwaliteitsborging', 'Kwaliteitsborging', '', bdj_blok( 'tekstvak', array(
		'kop'   => 'Wij doen er alles aan om een kwalitatief goed gebouw op te leveren',
		'tekst' => 'Met de Wet kwaliteitsborging voor het bouwen werken we samen met een onafhankelijke kwaliteitsborger, die tijdens het hele bouwproces controleert of het werk aan de eisen voldoet.',
		'band'  => 'grijs',
	) ) . bdj_blok( 'logos' ) . bdj_blok( 'activiteiten-slider' ) . $idee . $cta, array(), array( 'menu_order' => 20 ) );

	$vac = bdj_import_pagina( 'vacatures', 'Vacatures', '', bdj_blok( 'vacatures', array(
		'kop'           => 'Onze vacatures',
		'band'          => 'kolommen',
		'tekst'         => 'Bouwgroep DE Jong is een gezond en groeiend bouwbedrijf in Oosterwolde. We zoeken mensen die graag samenwerken en trots zijn op hun vak.',
		'contact_tekst' => "<strong>Interesse? Kennismaken kan heel laagdrempelig, gewoon met een kop koffie.</strong>\n\n<strong>Bel, mail of stuur een WhatsApp-bericht:</strong>\n\nJeep van Strien<br><a href=\"tel:+31653921009\">06 53 92 10 09</a><br><a href=\"mailto:j.vanstrien@bouwgroepdejong.nl\">j.vanstrien@bouwgroepdejong.nl</a>\n\n(elk gesprek wordt vertrouwelijk behandeld)",
		'links'         => "Werken bij Bouwgroep DE Jong betekent werken in een compact team met korte lijnen.\n\nPlaatshoudertekst: vertel hier over de sfeer en de collega's.",
		'rechts'        => "We bouwen aan afwisselende projecten in heel Noord-Nederland.\n\nPlaatshoudertekst: vertel hier over opleiding, doorgroei en arbeidsvoorwaarden.",
	) ) . bdj_blok( 'activiteiten-tegels', array( 'titel' => 'Onze activiteiten', 'behalve' => false ) ) . $cta, array(), array( 'menu_order' => 30 ) );

	$contact = bdj_import_pagina( 'contact', 'Contact', '', bdj_blok( 'contactformulier', array( 'kop' => 'Neem contact met ons op' ) ), array(), array( 'menu_order' => 40 ) );

	// Activiteiten (pagina + term).
	$volgorde = 1;
	foreach ( bdj_import_activiteiten() as $slug => $a ) {
		$inhoud = bdj_blok( 'tekstvak', array( 'kop' => str_replace( ' / ', '/', $a[0] ), 'band' => 'geen' ) )
			. bdj_blok( 'projecten', array( 'aantal' => 4 ) )
			. bdj_blok( 'tekstkolom', array( 'tekst' => $a[4] . " Op deze pagina ziet u een selectie van projecten die we de afgelopen jaren hebben gerealiseerd.\n\nPlaatshoudertekst: hier komt een korte introductie over deze activiteit, wat de klant ervan mag verwachten en waarin Bouwgroep DE Jong zich onderscheidt." ) )
			. bdj_blok( 'projecten', array( 'vanaf' => 4 ) )
			. bdj_blok( 'tekstkolommen', array(
				'kolom1' => 'Plaatshoudertekst kolom 1. Beschrijf hier wat deze activiteit inhoudt en welke onderdelen er bij een project komen kijken, van functionaliteit tot uitstraling.',
				'kolom2' => 'Plaatshoudertekst kolom 2. Beschrijf hier voor wie u bouwt, bijvoorbeeld particulieren, ondernemers of ontwikkelaars, en hoe de samenwerking verloopt.',
				'kolom3' => 'Plaatshoudertekst kolom 3. Sluit af met de werkwijze: vanuit de wensen van de opdrachtgever samen naar het beste plan qua prijs, planning en resultaat.',
			) )
			. $tegels
			. $cta;
		bdj_import_pagina( $slug, $a[0], '', $inhoud, array(
			'knoptekst' => 'Bekijk projecten in ' . $a[1],
		), array( 'post_type' => 'activiteit', 'menu_order' => 100 + $volgorde, 'post_excerpt' => $a[4] ) );

		$volgorde++;
	}

	// Voorpagina instellen.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );

	// Hoofdmenu.
	$menu = wp_get_nav_menu_object( 'Hoofdmenu' );
	$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( 'Hoofdmenu' );
	if ( ! $menu || ! wp_get_nav_menu_items( $menu_id ) ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'   => 'Activiteiten',
			'menu-item-url'     => home_url( '/#activiteiten' ),
			'menu-item-classes' => 'menu-activiteiten',
			'menu-item-status'  => 'publish',
			'menu-item-type'    => 'custom',
		) );
		foreach ( array( $over, $kwal, $vac, $contact ) as $pagina_id ) {
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-object-id' => $pagina_id,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
		}
	}
	$locaties              = get_theme_mod( 'nav_menu_locations', array() );
	$locaties['hoofdmenu'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locaties );

	// Vacatures.
	bdj_import_vacatures();

	flush_rewrite_rules();
	return 'Pagina\'s, menu, activiteiten, vacatures en instellingen aangemaakt';
}

function bdj_vacature_html( $intro, $blokken ) {
	$kol = array( '', '' );
	$i   = 0;
	foreach ( $blokken as $kop => $items ) {
		$html = "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$kop}</h3>\n<!-- /wp:heading -->\n\n";
		if ( is_array( $items ) ) {
			$li    = implode( '', array_map( function ( $x ) {
				return "<!-- wp:list-item -->\n<li>{$x}</li>\n<!-- /wp:list-item -->\n";
			}, $items ) );
			$html .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
		} else {
			$html .= "<!-- wp:paragraph -->\n<p>{$items}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		$kol[ $i % 2 ] .= $html;
		$i++;
	}
	return bdj_blokken( $intro )
		. "<!-- wp:columns -->\n<div class=\"wp-block-columns\">"
		. "<!-- wp:column -->\n<div class=\"wp-block-column\">" . $kol[0] . "</div>\n<!-- /wp:column -->\n\n"
		. "<!-- wp:column -->\n<div class=\"wp-block-column\">" . $kol[1] . "</div>\n<!-- /wp:column -->"
		. "</div>\n<!-- /wp:columns -->";
}

/** Vacature vullen: introductie + onderdelen; oude blokinhoud leegmaken. */
function bdj_vacature_velden_vullen( $vacature_id, $intro, $blokken ) {
	update_post_meta( $vacature_id, '_bdj_intro', $intro );
	$lijst = array();
	foreach ( $blokken as $kop => $items ) {
		$lijst[] = array(
			'titel'  => $kop,
			'soort'  => is_array( $items ) ? 'lijst' : 'tekst',
			'inhoud' => is_array( $items ) ? implode( "\n", $items ) : $items,
		);
	}
	bdj_vacature_onderdelen_opslaan( $vacature_id, $lijst );
	wp_update_post( array( 'ID' => $vacature_id, 'post_content' => '' ) );
}

function bdj_import_vacatures() {
	$vacatures = array(
		'Bouwkundig uitvoerder (m/v)' => array(
			'Als uitvoerder sta je er bij ons niet alleen voor. Je werkt in een klein, hecht team en hebt veel vrijheid om je werk zelf in te richten.',
			array(
				'Rol'                    => 'Je bent de spil op de bouwplaats. Je stuurt het team aan, bewaakt samen met de projectleider de planning en zorgt voor een veilige werkplek.',
				'Kennis en vaardigheden' => array( 'Ervaring als bouwkundig uitvoerder of vergelijkbaar' ),
				'Eigenschappen'          => array( 'Communicatief vaardig', 'Verbindend', 'Praktisch', 'Oplossingsgericht' ),
				'Wat bieden wij'         => array( 'Afwisselende projecten', 'Veel vrijheid en werkplezier', 'Vast contract', 'Bovengemiddeld salaris' ),
			),
		),
		'Projectleider (m/v)'         => array(
			'Je zoekt een goede balans tussen vrijheid en verantwoordelijkheid, in een bedrijf waar je echt invloed hebt op het resultaat.',
			array(
				'Rol'                    => 'Je bent de verbinder en organisator van het project: van voorbereiding en inkoop tot overleg met opdrachtgever en onderaannemers.',
				'Kennis en vaardigheden' => array( 'Bouwkundige achtergrond', 'Ervaring met werkvoorbereiding', 'Goed met Office en digitale tools' ),
				'Eigenschappen'          => array( 'MBO/HBO werk- en denkniveau', 'Nauwkeurig en proactief', 'Loyaal' ),
				'Wat bieden wij'         => array( 'Fulltime baan', 'Vast contract', 'Auto van de zaak', 'Doorgroeimogelijkheden' ),
			),
		),
		'Allround timmerman (m/v)'    => array(
			'Je bent graag met je vak bezig en zoekt een team waarin je gewaardeerd wordt om wat je kunt.',
			array(
				'Rol'                    => 'Je werkt mee aan uiteenlopende projecten, van woningbouw tot utiliteit, en pakt zelfstandig je werk op.',
				'Kennis en vaardigheden' => array( 'MBO werk- en denkniveau', 'Ervaring in de bouw' ),
				'Eigenschappen'          => array( 'Goede samenwerker', 'Nauwkeurig', 'Denkt actief mee' ),
				'Wat bieden wij'         => array( 'Fulltime baan', 'Goede arbeidsvoorwaarden (cao)', 'Vast contract', 'Hecht team' ),
			),
		),
		'Medewerker schoonmaak (m/v)' => array(
			'Een nette werkomgeving is belangrijk voor ons team en onze bezoekers. Jij zorgt daarvoor.',
			array(
				'Wat ga je doen?' => array( 'Kantoor, kantine en sanitair schoonmaken', 'Voorraad bijhouden' ),
				'Wie ben jij?'    => array( 'Netjes en zorgvuldig', 'Zelfstandig en betrouwbaar' ),
				'Wat bieden wij?' => array( 'Prettige werkomgeving', '2 x 4 uur per week', 'Marktconforme voorwaarden' ),
			),
		),
	);
	$volgorde = 0;
	foreach ( $vacatures as $titel => $v ) {
		$bestaand = get_posts( array( 'post_type' => 'vacature', 'title' => $titel, 'posts_per_page' => 1, 'post_status' => 'any' ) );
		if ( $bestaand ) {
			$vacature_id = $bestaand[0]->ID;
			$volgorde++;
		} else {
			$vacature_id = wp_insert_post( array(
				'post_type'   => 'vacature',
				'post_status' => 'publish',
				'post_title'  => $titel,
				'menu_order'  => $volgorde++,
			) );
		}
		bdj_vacature_velden_vullen( $vacature_id, $v[0], $v[1] );
	}
}

function bdj_import_project( $act, $index ) {
	$lijst = bdj_import_projecten();
	if ( ! isset( $lijst[ $act ][ $index ] ) ) {
		return 'Onbekend project';
	}
	list( $titel, $plaats, $map, $nummers ) = $lijst[ $act ][ $index ];

	// Bestaand project zoeken (zelfde titel + plaats + activiteit).
	$bestaand = get_posts( array(
		'post_type'      => 'project',
		'post_status'    => 'any',
		'title'          => $titel,
		'posts_per_page' => -1,
		'meta_key'       => '_bdj_activiteit',
		'meta_value'     => bdj_activiteit_id( $act ),
	) );
	$project_id = 0;
	foreach ( $bestaand as $p ) {
		if ( get_post_meta( $p->ID, '_bdj_plaats', true ) === $plaats ) {
			$project_id = $p->ID;
		}
	}
	if ( ! $project_id ) {
		$project_id = wp_insert_post( array(
			'post_type'   => 'project',
			'post_status' => 'publish',
			'post_title'  => $titel,
			'menu_order'  => $index,
		) );
		update_post_meta( $project_id, '_bdj_activiteit', bdj_activiteit_id( $act ) );
		update_post_meta( $project_id, '_bdj_plaats', $plaats );
	}

	$ids    = array();
	$fouten = array();
	foreach ( $nummers as $nr ) {
		$url = BDJ_BRON . '/assets/img/' . $act . '/' . rawurlencode( $map ) . '/' . $nr . '.jpg';
		$id  = bdj_import_afbeelding( $url, $project_id, trim( $titel . ' ' . $plaats ) );
		if ( is_wp_error( $id ) ) {
			$fouten[] = $nr . ': ' . $id->get_error_message();
		} else {
			$ids[] = $id;
		}
	}
	update_post_meta( $project_id, '_bdj_galerij', implode( ',', $ids ) );

	return sprintf( 'Project %s %s: %d foto\'s%s', $titel, $plaats, count( $ids ), $fouten ? ' (fouten: ' . implode( '; ', $fouten ) . ')' : '' );
}

/* -------------------------------------------------------------------------
 * Beheerpagina (Gereedschap → DE Jong importeren)
 * ---------------------------------------------------------------------- */

add_action( 'admin_menu', function () {
	add_management_page( 'DE Jong importeren', 'DE Jong importeren', 'manage_options', 'bdj-import', 'bdj_import_pagina_render' );
} );

function bdj_import_pagina_render() {
	$aantal = count( bdj_import_taken() );
	?>
	<div class="wrap">
		<h1>Startinhoud Bouwgroep DE Jong importeren</h1>
		<p>Maakt de pagina's, het menu, de activiteiten, de projecten (met foto's van de huidige website), de vacatures en de basisinstellingen aan.<br>
			Je kunt dit veilig opnieuw draaien: bestaande pagina's worden bijgewerkt en foto's worden niet dubbel opgehaald.</p>
		<p><strong>Let op:</strong> teksten op bestaande pagina's worden overschreven met de starttekst.</p>
		<p><button class="button button-primary" id="bdj-start">Importeren starten (<?php echo (int) $aantal; ?> stappen)</button></p>
		<progress id="bdj-voortgang" max="<?php echo (int) $aantal; ?>" value="0" style="width:100%;max-width:600px;height:20px;display:none"></progress>
		<ol id="bdj-log" style="max-height:400px;overflow:auto"></ol>
	</div>
	<script>
	( function () {
		const knop = document.getElementById( 'bdj-start' );
		const log = document.getElementById( 'bdj-log' );
		const balk = document.getElementById( 'bdj-voortgang' );
		const totaal = <?php echo (int) $aantal; ?>;
		const nonce = <?php echo wp_json_encode( wp_create_nonce( 'bdj_import' ) ); ?>;
		knop.addEventListener( 'click', async () => {
			knop.disabled = true;
			balk.style.display = 'block';
			for ( let i = 0; i < totaal; i++ ) {
				const body = new URLSearchParams( { action: 'bdj_import', stap: i, _wpnonce: nonce } );
				let tekst;
				try {
					const r = await fetch( ajaxurl, { method: 'POST', body } );
					const j = await r.json();
					tekst = j.success ? j.data : 'FOUT: ' + j.data;
				} catch ( e ) {
					tekst = 'FOUT: ' + e.message;
				}
				const li = document.createElement( 'li' );
				li.textContent = tekst;
				log.appendChild( li );
				log.scrollTop = log.scrollHeight;
				balk.value = i + 1;
			}
			const klaar = document.createElement( 'p' );
			klaar.innerHTML = '<strong>Klaar!</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Bekijk de website</a>';
			log.after( klaar );
		} );
	} )();
	</script>
	<?php
}

add_action( 'wp_ajax_bdj_import', function () {
	check_ajax_referer( 'bdj_import' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Geen rechten' );
	}
	set_time_limit( 300 );
	$res = bdj_import_voer_uit( isset( $_POST['stap'] ) ? absint( $_POST['stap'] ) : 0 );
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( $res->get_error_message() );
	}
	wp_send_json_success( $res );
} );

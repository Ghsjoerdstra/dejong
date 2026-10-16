/**
 * Vacature-editor: onderdelen als kaartjes.
 * Elk kaartje: titel, schakelaar Tekst/Opsomming en de inhoud (bij een opsomming losse punten).
 * Alles wordt bijgehouden in het verborgen veld #bdj-vac-data (JSON).
 */
( function ( $ ) {
	var $data = $( '#bdj-vac-data' );
	var $delen = $( '#bdj-vac-delen' );
	if ( ! $data.length ) {
		return;
	}

	function esc( s ) {
		return $( '<div>' ).text( s || '' ).html();
	}

	/* ---------- Opbouw ---------- */

	function puntHtml( tekst ) {
		return '<li class="bdj-punt"><span class="bdj-bol" aria-hidden="true"></span>' +
			'<input type="text" class="bdj-punt-tekst" value="' + esc( tekst ) + '" placeholder="Nieuw punt…">' +
			'<button type="button" class="bdj-punt-weg" aria-label="Punt verwijderen">×</button></li>';
	}

	function deelHtml( deel ) {
		var lijst = deel.soort !== 'tekst';
		var punten = ( deel.inhoud || '' ).split( /\r?\n/ ).filter( function ( p ) { return p.trim() !== ''; } );
		if ( ! punten.length ) {
			punten = [ '' ];
		}
		return '<div class="bdj-vac-deel" data-soort="' + ( lijst ? 'lijst' : 'tekst' ) + '">' +
			'<div class="bdj-deel-kop">' +
				'<span class="dashicons dashicons-move bdj-greep" title="Verslepen"></span>' +
				'<input type="text" class="bdj-deel-titel" value="' + esc( deel.titel ) + '" placeholder="Titel, bv. Wat bieden wij">' +
				'<button type="button" class="bdj-deel-weg" title="Onderdeel verwijderen"><span class="dashicons dashicons-trash"></span></button>' +
			'</div>' +
			'<div class="bdj-schakelaar" role="group" aria-label="Weergave">' +
				'<button type="button" data-soort="lijst" class="' + ( lijst ? 'actief' : '' ) + '"><span class="dashicons dashicons-editor-ul"></span> Opsomming</button>' +
				'<button type="button" data-soort="tekst" class="' + ( lijst ? '' : 'actief' ) + '"><span class="dashicons dashicons-editor-paragraph"></span> Tekst</button>' +
			'</div>' +
			'<div class="bdj-deel-lijst"' + ( lijst ? '' : ' hidden' ) + '>' +
				'<ul class="bdj-punten">' + punten.map( puntHtml ).join( '' ) + '</ul>' +
				'<button type="button" class="button-link bdj-punt-nieuw">+ Punt toevoegen</button>' +
			'</div>' +
			'<textarea class="bdj-deel-tekst" rows="4" placeholder="Schrijf hier de tekst…"' + ( lijst ? ' hidden' : '' ) + '>' + esc( lijst ? '' : deel.inhoud ) + '</textarea>' +
		'</div>';
	}

	function render( lijst ) {
		$delen.html( lijst.map( deelHtml ).join( '' ) );
		$delen.find( '.bdj-punten' ).sortable( { handle: '.bdj-bol', axis: 'y', update: bewaar } );
	}

	/* ---------- Opslaan in het verborgen veld ---------- */

	function lees( $deel ) {
		var soort = $deel.attr( 'data-soort' );
		var inhoud = soort === 'lijst'
			? $deel.find( '.bdj-punt-tekst' ).map( function () { return this.value.trim(); } ).get().filter( Boolean ).join( '\n' )
			: $deel.find( '.bdj-deel-tekst' ).val();
		return { titel: $deel.find( '.bdj-deel-titel' ).val(), soort: soort, inhoud: inhoud };
	}

	function bewaar() {
		var lijst = $delen.children( '.bdj-vac-deel' ).map( function () { return lees( $( this ) ); } ).get();
		$data.val( JSON.stringify( lijst ) );
	}

	/* ---------- Interactie ---------- */

	$delen.sortable( { handle: '.bdj-greep', items: '> .bdj-vac-deel', placeholder: 'bdj-vac-plek', forcePlaceholderSize: true, update: bewaar } );

	$delen.on( 'input change', 'input, textarea', bewaar );

	// Tekst <-> opsomming wisselen (inhoud gaat mee)
	$delen.on( 'click', '.bdj-schakelaar button', function () {
		var $deel = $( this ).closest( '.bdj-vac-deel' );
		var nieuw = $( this ).data( 'soort' );
		if ( $deel.attr( 'data-soort' ) === nieuw ) {
			return;
		}
		var huidig = lees( $deel );
		var $nieuw = $( deelHtml( { titel: huidig.titel, soort: nieuw, inhoud: huidig.inhoud } ) );
		$deel.replaceWith( $nieuw );
		$nieuw.find( '.bdj-punten' ).sortable( { handle: '.bdj-bol', axis: 'y', update: bewaar } );
		bewaar();
	} );

	// Punten
	function nieuwPunt( $na, tekst ) {
		var $p = $( puntHtml( tekst || '' ) );
		$na.after( $p );
		$p.find( 'input' ).trigger( 'focus' );
		bewaar();
		return $p;
	}

	$delen.on( 'click', '.bdj-punt-nieuw', function () {
		var $ul = $( this ).siblings( '.bdj-punten' );
		var $leeg = $ul.find( '.bdj-punt-tekst' ).filter( function () { return ! this.value.trim(); } ).first();
		if ( $leeg.length ) {
			$leeg.trigger( 'focus' );
			return;
		}
		var $p = $( puntHtml( '' ) );
		$ul.append( $p );
		$p.find( 'input' ).trigger( 'focus' );
	} );

	$delen.on( 'keydown', '.bdj-punt-tekst', function ( e ) {
		var $li = $( this ).closest( 'li' );
		if ( e.key === 'Enter' ) {
			e.preventDefault(); // niet het hele formulier versturen
			nieuwPunt( $li );
		} else if ( e.key === 'Backspace' && this.value === '' && $li.siblings().length ) {
			e.preventDefault();
			var $vorige = $li.prev().length ? $li.prev() : $li.next();
			$li.remove();
			$vorige.find( 'input' ).trigger( 'focus' ).each( function () { this.setSelectionRange( this.value.length, this.value.length ); } );
			bewaar();
		} else if ( e.key === 'ArrowUp' && $li.prev().length ) {
			e.preventDefault(); $li.prev().find( 'input' ).trigger( 'focus' );
		} else if ( e.key === 'ArrowDown' && $li.next().length ) {
			e.preventDefault(); $li.next().find( 'input' ).trigger( 'focus' );
		}
	} );

	// Meerdere regels plakken = meerdere punten
	$delen.on( 'paste', '.bdj-punt-tekst', function ( e ) {
		var tekst = ( e.originalEvent.clipboardData || window.clipboardData ).getData( 'text' );
		var regels = tekst.split( /\r?\n/ ).map( function ( r ) { return r.replace( /^\s*[-*•]\s*/, '' ).trim(); } ).filter( Boolean );
		if ( regels.length < 2 ) {
			return;
		}
		e.preventDefault();
		var $li = $( this ).closest( 'li' );
		if ( ! this.value.trim() ) {
			this.value = regels.shift();
		}
		regels.forEach( function ( r ) { $li = nieuwPunt( $li, r ); } );
	} );

	$delen.on( 'click', '.bdj-punt-weg', function () {
		var $li = $( this ).closest( 'li' );
		if ( $li.siblings().length ) {
			$li.remove();
		} else {
			$li.find( 'input' ).val( '' );
		}
		bewaar();
	} );

	// Onderdelen toevoegen / verwijderen
	$( '#bdj-vac-nieuw' ).on( 'click', function () {
		var $d = $( deelHtml( { titel: '', soort: 'lijst', inhoud: '' } ) );
		$delen.append( $d );
		$d.find( '.bdj-punten' ).sortable( { handle: '.bdj-bol', axis: 'y', update: bewaar } );
		$d.find( '.bdj-deel-titel' ).trigger( 'focus' );
		bewaar();
	} );

	$delen.on( 'click', '.bdj-deel-weg', function () {
		var $deel = $( this ).closest( '.bdj-vac-deel' );
		var heeftInhoud = lees( $deel ).inhoud.trim() !== '';
		if ( heeftInhoud && ! window.confirm( 'Dit onderdeel met inhoud verwijderen?' ) ) {
			return;
		}
		$deel.remove();
		bewaar();
	} );

	// Enter in een titel: naar de inhoud springen i.p.v. het formulier versturen
	$delen.on( 'keydown', '.bdj-deel-titel', function ( e ) {
		if ( e.key === 'Enter' ) {
			e.preventDefault();
			var $deel = $( this ).closest( '.bdj-vac-deel' );
			$deel.find( $deel.attr( 'data-soort' ) === 'lijst' ? '.bdj-punt-tekst:first' : '.bdj-deel-tekst' ).trigger( 'focus' );
		}
	} );

	render( JSON.parse( $data.val() || '[]' ) );
	bewaar();
} )( jQuery );

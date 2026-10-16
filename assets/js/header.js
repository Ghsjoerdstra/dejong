/* Header kleiner maken zodra de bezoeker scrolt (zoals op de live site). */
( function () {
	var header = document.querySelector( '.site-header' );
	if ( ! header ) {
		return;
	}
	var bezig = false;
	function bijwerken() {
		header.classList.toggle( 'gescrold', window.scrollY > 40 );
		bezig = false;
	}
	window.addEventListener( 'scroll', function () {
		if ( ! bezig ) {
			bezig = true;
			window.requestAnimationFrame( bijwerken );
		}
	}, { passive: true } );
	bijwerken();
} )();

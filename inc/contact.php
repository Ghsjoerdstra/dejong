<?php
/**
 * Contactformulier: verwerkt via admin-post.php en verstuurt met wp_mail().
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_bdj_contact', 'bdj_verwerk_contact' );
add_action( 'admin_post_bdj_contact', 'bdj_verwerk_contact' );

function bdj_verwerk_contact() {
	$terug = isset( $_POST['_bdj_terug'] ) ? esc_url_raw( wp_unslash( $_POST['_bdj_terug'] ) ) : home_url( '/' );
	$terug = wp_validate_redirect( $terug, home_url( '/' ) );

	if ( ! isset( $_POST['_bdj_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_bdj_nonce'] ), 'bdj_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'verzonden', 'fout', $terug ) . '#formulier' );
		exit;
	}

	// Honeypot: dit veld is onzichtbaar; als het gevuld is, is het een bot.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'verzonden', 'ok', $terug ) . '#formulier' );
		exit;
	}

	$naam     = isset( $_POST['naam'] ) ? sanitize_text_field( wp_unslash( $_POST['naam'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$telefoon = isset( $_POST['telefoon'] ) ? sanitize_text_field( wp_unslash( $_POST['telefoon'] ) ) : '';
	$bericht  = isset( $_POST['bericht'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bericht'] ) ) : '';

	if ( ! $naam || ! is_email( $email ) || ! $bericht ) {
		wp_safe_redirect( add_query_arg( 'verzonden', 'onvolledig', $terug ) . '#formulier' );
		exit;
	}

	$pagina_id = isset( $_POST['_bdj_pagina'] ) ? absint( $_POST['_bdj_pagina'] ) : 0;
	$ontvanger = '';
	if ( $pagina_id ) {
		foreach ( parse_blocks( get_post_field( 'post_content', $pagina_id ) ) as $blok ) {
			if ( 'bdj/contactformulier' === $blok['blockName'] && ! empty( $blok['attrs']['ontvanger'] ) ) {
				$ontvanger = $blok['attrs']['ontvanger'];
			}
		}
	}
	if ( ! is_email( $ontvanger ) ) {
		$ontvanger = bdj_optie( 'email' );
	}
	if ( ! is_email( $ontvanger ) ) {
		$ontvanger = get_option( 'admin_email' );
	}

	$onderwerp = sprintf( 'Contactformulier %s: bericht van %s', get_bloginfo( 'name' ), $naam );
	$inhoud    = "Naam: {$naam}\nE-mail: {$email}\nTelefoon: {$telefoon}\n\nBericht:\n{$bericht}\n";
	$headers   = array( 'Reply-To: ' . $naam . ' <' . $email . '>' );

	$gelukt = wp_mail( $ontvanger, $onderwerp, $inhoud, $headers );

	wp_safe_redirect( add_query_arg( 'verzonden', $gelukt ? 'ok' : 'fout', $terug ) . '#formulier' );
	exit;
}

/** Melding na verzenden. */
function bdj_contact_melding( $bedankt = '' ) {
	if ( empty( $_GET['verzonden'] ) ) {
		return '';
	}
	$status    = sanitize_key( $_GET['verzonden'] );
	$meldingen = array(
		'ok'         => $bedankt ? $bedankt : 'Bedankt voor uw bericht! We nemen zo snel mogelijk contact met u op.',
		'onvolledig' => 'Vul alstublieft uw naam, een geldig e-mailadres en uw bericht in.',
		'fout'       => 'Er ging iets mis bij het versturen. Probeer het opnieuw of neem telefonisch contact op.',
	);
	if ( ! isset( $meldingen[ $status ] ) ) {
		return '';
	}
	return sprintf( '<p class="melding melding-%1$s" role="status">%2$s</p>', esc_attr( $status ), esc_html( $meldingen[ $status ] ) );
}

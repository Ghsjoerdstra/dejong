<?php
/**
 * Eenvoudige vacature-editor: introductie + een vrij aantal onderdelen (kaartjes).
 *
 * Opslag: meta "_bdj_intro" (tekst) en "_bdj_onderdelen" (JSON: [{titel, soort, inhoud}]).
 * soort = "lijst" (inhoud = punten, één per regel) of "tekst".
 */

defined( 'ABSPATH' ) || exit;

/** Standaard-onderdelen voor een nieuwe vacature. */
function bdj_vacature_standaard_onderdelen() {
	return array(
		array( 'titel' => 'Rol', 'soort' => 'tekst', 'inhoud' => '' ),
		array( 'titel' => 'Kennis en vaardigheden', 'soort' => 'lijst', 'inhoud' => '' ),
		array( 'titel' => 'Eigenschappen', 'soort' => 'lijst', 'inhoud' => '' ),
		array( 'titel' => 'Wat bieden wij', 'soort' => 'lijst', 'inhoud' => '' ),
	);
}

/**
 * Onderdelen van een vacature. Leest ook de oudere velden (deel1..4) als die nog bestaan.
 *
 * @return array[]
 */
function bdj_vacature_onderdelen( $post_id ) {
	$json = get_post_meta( $post_id, '_bdj_onderdelen', true );
	if ( $json ) {
		$lijst = json_decode( $json, true );
		if ( is_array( $lijst ) ) {
			return $lijst;
		}
	}
	$lijst = array();
	for ( $nr = 1; $nr <= 4; $nr++ ) {
		$inhoud = (string) get_post_meta( $post_id, "_bdj_deel{$nr}_inhoud", true );
		$titel  = (string) get_post_meta( $post_id, "_bdj_deel{$nr}_titel", true );
		if ( '' !== trim( $inhoud ) || '' !== trim( $titel ) ) {
			$lijst[] = array(
				'titel'  => $titel,
				'soort'  => 'tekst' === get_post_meta( $post_id, "_bdj_deel{$nr}_soort", true ) ? 'tekst' : 'lijst',
				'inhoud' => $inhoud,
			);
		}
	}
	return $lijst;
}

/** Onderdelen opslaan (en oude velden opruimen). */
function bdj_vacature_onderdelen_opslaan( $post_id, $lijst ) {
	$schoon = array();
	foreach ( (array) $lijst as $deel ) {
		$titel  = isset( $deel['titel'] ) ? sanitize_text_field( $deel['titel'] ) : '';
		$soort  = isset( $deel['soort'] ) && 'tekst' === $deel['soort'] ? 'tekst' : 'lijst';
		$inhoud = isset( $deel['inhoud'] ) ? (string) $deel['inhoud'] : '';
		if ( 'lijst' === $soort ) {
			$punten = array_filter( array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', $inhoud ) ), 'strlen' );
			$inhoud = implode( "\n", $punten );
		} else {
			$inhoud = wp_kses_post( $inhoud );
		}
		if ( '' === $titel && '' === trim( $inhoud ) ) {
			continue;
		}
		$schoon[] = array( 'titel' => $titel, 'soort' => $soort, 'inhoud' => $inhoud );
	}
	update_post_meta( $post_id, '_bdj_onderdelen', wp_slash( wp_json_encode( $schoon, JSON_UNESCAPED_UNICODE ) ) );
	for ( $nr = 1; $nr <= 4; $nr++ ) {
		delete_post_meta( $post_id, "_bdj_deel{$nr}_titel" );
		delete_post_meta( $post_id, "_bdj_deel{$nr}_soort" );
		delete_post_meta( $post_id, "_bdj_deel{$nr}_inhoud" );
	}
}

/* -------------------------------------------------------------------------
 * Metabox
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes_vacature', function ( $post ) {
	add_meta_box( 'bdj_vacature', 'Inhoud van de vacature', 'bdj_vacature_metabox', 'vacature', 'normal', 'high' );
} );

function bdj_vacature_metabox( $post ) {
	$onderdelen = bdj_vacature_onderdelen( $post->ID );
	if ( ! $onderdelen && 'auto-draft' === $post->post_status ) {
		$onderdelen = bdj_vacature_standaard_onderdelen();
	}
	wp_nonce_field( 'bdj_vacature', 'bdj_vacature_nonce' );
	?>
	<div class="bdj-vac">
		<label class="bdj-vac-label" for="bdj-vac-intro">Introductie</label>
		<textarea id="bdj-vac-intro" name="bdj_vac_intro" rows="3" placeholder="Korte tekst bovenaan de vacature…"><?php echo esc_textarea( get_post_meta( $post->ID, '_bdj_intro', true ) ); ?></textarea>

		<div class="bdj-vac-kop">
			<span class="bdj-vac-label">Onderdelen</span>
			<span class="bdj-vac-tip">Versleep een kaartje aan <span class="dashicons dashicons-move"></span> om de volgorde te wijzigen.</span>
		</div>

		<input type="hidden" name="bdj_vac_onderdelen" id="bdj-vac-data" value="<?php echo esc_attr( wp_json_encode( $onderdelen, JSON_UNESCAPED_UNICODE ) ); ?>">
		<div class="bdj-vac-delen" id="bdj-vac-delen"></div>
		<button type="button" class="button bdj-vac-nieuw" id="bdj-vac-nieuw"><span class="dashicons dashicons-plus-alt2"></span> Onderdeel toevoegen</button>
		<noscript><p>JavaScript is nodig om de onderdelen te bewerken.</p></noscript>
	</div>
	<?php
}

add_action( 'save_post_vacature', function ( $post_id ) {
	if ( ! isset( $_POST['bdj_vacature_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bdj_vacature_nonce'] ), 'bdj_vacature' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_bdj_intro', isset( $_POST['bdj_vac_intro'] ) ? wp_kses_post( wp_unslash( $_POST['bdj_vac_intro'] ) ) : '' );
	if ( isset( $_POST['bdj_vac_onderdelen'] ) ) {
		$lijst = json_decode( wp_unslash( $_POST['bdj_vac_onderdelen'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- wordt per veld geschoond
		if ( is_array( $lijst ) ) {
			bdj_vacature_onderdelen_opslaan( $post_id, $lijst );
		}
	}
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'vacature' !== get_post_type() ) {
		return;
	}
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'bdj-vacature', BDJ_URI . '/assets/js/admin-vacature.js', array( 'jquery', 'jquery-ui-sortable' ), BDJ_VERSION, true );
	wp_enqueue_style( 'bdj-vacature', BDJ_URI . '/assets/css/admin-vacature.css', array(), BDJ_VERSION );
} );

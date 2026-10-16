<?php
/**
 * Eenvoudige eigen velden (zonder plugin) per paginatemplate en voor projecten.
 *
 * Veldtypes: text, textarea, image (attachment-ID), gallery (komma-gescheiden ID's), select.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Velddefinities per template. Sleutel = template-bestand ('' = standaardpagina, 'front' = voorpagina).
 */
function bdj_velddefinities() {
	return array(
		'page-templates/activiteit.php' => array(
			'titel'  => 'Activiteit (slider, menu en tegels)',
			'velden' => array(
				'icoon'             => array(
					'label'  => 'Icoon',
					'type'   => 'select',
					'opties' => array(
						''                       => 'Automatisch (op basis van de slug)',
						'wonen'                  => 'Wonen',
						'utiliteit'              => 'Utiliteit',
						'food'                   => 'Food',
						'bedrijfspand-industrie' => 'Bedrijfspand / industrie',
						'logistiek-warehousing'  => 'Logistiek / warehousing',
						'verbouw-onderhoud'      => 'Verbouw / onderhoud',
						'versterkingen-ncg'      => 'Versterkingen NCG',
					),
				),
				'knoptekst'         => array( 'label' => 'Knoptekst in de activiteitenslider', 'type' => 'text', 'hint' => 'Bv. "Bekijk projecten in woningbouw". De korte tekst in de slider is de samenvatting (excerpt) van deze pagina.' ),
				'slider_afbeelding' => array( 'label' => 'Achtergrondfoto in de activiteitenslider', 'type' => 'image' ),
				'icoon_afbeelding'  => array( 'label' => 'Eigen icoon (slider en tegels)', 'type' => 'image', 'hint' => 'Gaat voor het gekozen icoon.' ),
				'tab_afbeelding'    => array( 'label' => 'Eigen icoon in de iconenbalk', 'type' => 'image', 'hint' => 'Verandert niet van kleur bij aanwijzen of selecteren.' ),
			),
		),
	);
}

/** Velden voor projecten. */
function bdj_project_velden() {
	return array(
		'plaats'  => array( 'label' => 'Plaats', 'type' => 'text' ),
		'galerij' => array( 'label' => "Foto's", 'type' => 'gallery', 'hint' => 'De eerste foto is de foto die als eerste getoond wordt. Sleep om de volgorde te wijzigen.' ),
	);
}

/** Velddefinities die bij een bericht horen. */
function bdj_velden_voor( $post ) {
	if ( 'project' === $post->post_type ) {
		return array( 'Projectgegevens' => bdj_project_velden() );
	}
	if ( 'page' !== $post->post_type || use_block_editor_for_post( $post ) ) {
		return array(); // pagina's: zie het paneel "Activiteit" in de blokeditor (assets/js/paneel-activiteit.js)
	}
	$defs    = bdj_velddefinities();
	$groepen = array();
	$template = get_page_template_slug( $post );
	if ( $template && isset( $defs[ $template ] ) ) {
		$groepen[ $defs[ $template ]['titel'] ] = $defs[ $template ]['velden'];
	}
	return $groepen;
}

add_action( 'add_meta_boxes', function ( $post_type, $post ) {
	if ( ! in_array( $post_type, array( 'page', 'project' ), true ) ) { // vacatures: zie inc/vacature-editor.php
		return;
	}
	foreach ( bdj_velden_voor( $post ) as $titel => $velden ) {
		add_meta_box(
			'bdj_' . sanitize_title( $titel ),
			$titel,
			'bdj_render_metabox',
			$post_type,
			'page' === $post_type ? 'side' : 'normal',
			'default',
			array( 'velden' => $velden )
		);
	}
}, 10, 2 );

function bdj_render_metabox( $post, $box ) {
	wp_nonce_field( 'bdj_velden', 'bdj_velden_nonce' );
	echo '<div class="bdj-velden">';
	foreach ( $box['args']['velden'] as $key => $veld ) {
		$naam   = 'bdj_veld[' . $key . ']';
		$waarde = get_post_meta( $post->ID, '_bdj_' . $key, true );
		// Nieuw bericht: standaardwaarde invullen.
		if ( '' === $waarde && 'auto-draft' === $post->post_status && isset( $veld['default'] ) ) {
			$waarde = $veld['default'];
		}
		// Kopje boven een groep velden (bv. "Onderdeel 2").
		if ( ! empty( $veld['groep'] ) && ( ! isset( $vorige_groep ) || $vorige_groep !== $veld['groep'] ) ) {
			printf( '<h3 class="bdj-groep">%s</h3>', esc_html( $veld['groep'] ) );
		}
		$vorige_groep = isset( $veld['groep'] ) ? $veld['groep'] : null;
		echo '<div class="bdj-veld">';
		printf( '<label for="bdj-%1$s"><strong>%2$s</strong></label>', esc_attr( $key ), esc_html( $veld['label'] ) );

		switch ( $veld['type'] ) {
			case 'textarea':
				printf( '<textarea id="bdj-%1$s" name="%2$s" rows="4">%3$s</textarea>', esc_attr( $key ), esc_attr( $naam ), esc_textarea( $waarde ) );
				break;

			case 'select':
				printf( '<select id="bdj-%1$s" name="%2$s">', esc_attr( $key ), esc_attr( $naam ) );
				foreach ( $veld['opties'] as $optie => $label ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $optie ), selected( $waarde, $optie, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;

			case 'image':
				$url = $waarde ? wp_get_attachment_image_url( (int) $waarde, 'medium' ) : '';
				printf(
					'<div class="bdj-afbeelding" data-multiple="0"><input type="hidden" name="%1$s" value="%2$s"><div class="bdj-preview">%3$s</div><button type="button" class="button bdj-kies">Afbeelding kiezen</button> <button type="button" class="button-link bdj-wis"%4$s>Verwijderen</button></div>',
					esc_attr( $naam ),
					esc_attr( $waarde ),
					$url ? '<img src="' . esc_url( $url ) . '" alt="">' : '',
					$waarde ? '' : ' hidden'
				);
				break;

			case 'gallery':
				$ids = array_filter( array_map( 'absint', explode( ',', (string) $waarde ) ) );
				echo '<div class="bdj-afbeelding bdj-galerij" data-multiple="1">';
				printf( '<input type="hidden" name="%1$s" value="%2$s">', esc_attr( $naam ), esc_attr( implode( ',', $ids ) ) );
				echo '<ul class="bdj-preview">';
				foreach ( $ids as $id ) {
					printf( '<li data-id="%1$d">%2$s<button type="button" class="bdj-weg" aria-label="Verwijderen">×</button></li>', $id, wp_get_attachment_image( $id, 'thumbnail' ) );
				}
				echo '</ul><button type="button" class="button bdj-kies">Foto\'s toevoegen</button></div>';
				break;

			default:
				printf( '<input type="text" id="bdj-%1$s" name="%2$s" value="%3$s" class="widefat">', esc_attr( $key ), esc_attr( $naam ), esc_attr( $waarde ) );
		}

		if ( ! empty( $veld['hint'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $veld['hint'] ) );
		}
		echo '</div>';
	}
	echo '</div>';
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['bdj_velden_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bdj_velden_nonce'] ), 'bdj_velden' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$invoer = isset( $_POST['bdj_veld'] ) ? (array) wp_unslash( $_POST['bdj_veld'] ) : array();
	foreach ( bdj_velden_voor( $post ) as $velden ) {
		foreach ( $velden as $key => $veld ) {
			if ( ! array_key_exists( $key, $invoer ) ) {
				continue;
			}
			$waarde = $invoer[ $key ];
			switch ( $veld['type'] ) {
				case 'textarea':
					$waarde = wp_kses_post( $waarde );
					break;
				case 'image':
					$waarde = absint( $waarde ) ? absint( $waarde ) : '';
					break;
				case 'gallery':
					$waarde = implode( ',', array_filter( array_map( 'absint', explode( ',', $waarde ) ) ) );
					break;
				default:
					$waarde = sanitize_text_field( $waarde );
			}
			if ( '' === $waarde ) {
				delete_post_meta( $post_id, '_bdj_' . $key );
			} else {
				update_post_meta( $post_id, '_bdj_' . $key, $waarde );
			}
		}
	}
}, 10, 2 );

// Media-kiezer (JS + CSS) in de editor.
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'bdj-admin-velden', BDJ_URI . '/assets/js/admin-velden.js', array( 'jquery', 'jquery-ui-sortable' ), BDJ_VERSION, true );
	wp_add_inline_style( 'wp-admin', '
		.bdj-veld { margin: 0 0 16px; }
		.bdj-veld label { display: block; margin-bottom: 4px; }
		.bdj-veld textarea, .bdj-veld select { width: 100%; }
		.bdj-groep { margin: 28px 0 12px; padding-top: 16px; border-top: 1px solid #dcdcde; font-size: 14px; }
		.bdj-preview img { max-width: 220px; height: auto; display: block; margin: 6px 0; background: #f0f0f1; }
		.bdj-galerij .bdj-preview { display: flex; flex-wrap: wrap; gap: 8px; margin: 6px 0; }
		.bdj-galerij .bdj-preview li { position: relative; cursor: move; margin: 0; }
		.bdj-galerij .bdj-preview img { width: 100px; height: 100px; object-fit: cover; margin: 0; }
		.bdj-weg { position: absolute; top: 2px; right: 2px; border: 0; background: #b32d2e; color: #fff; width: 22px; height: 22px; border-radius: 50%; cursor: pointer; line-height: 1; }
	' );
} );

/* -------------------------------------------------------------------------
 * Activiteit-velden als paneel in de blokeditor (tabblad Pagina)
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {
	$velden = array(
		'_bdj_icoon'             => 'string',
		'_bdj_knoptekst'         => 'string',
		'_bdj_slider_afbeelding' => 'integer',
		'_bdj_icoon_afbeelding'  => 'integer',
		'_bdj_tab_afbeelding'    => 'integer',
	);
	foreach ( $velden as $key => $type ) {
		register_post_meta( 'page', $key, array(
			'type'              => $type,
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'integer' === $type ? 'absint' : 'sanitize_text_field',
			'auth_callback'     => function ( $toegestaan, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
			},
		) );
	}
} );

add_action( 'enqueue_block_editor_assets', function () {
	if ( 'page' !== get_post_type() ) {
		return;
	}
	wp_enqueue_script(
		'bdj-paneel-activiteit',
		BDJ_URI . '/assets/js/paneel-activiteit.js',
		array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-block-editor' ),
		BDJ_VERSION,
		true
	);
	$opties = bdj_velddefinities();
	wp_add_inline_script( 'bdj-paneel-activiteit', 'window.bdjIconen = ' . wp_json_encode( $opties['page-templates/activiteit.php']['velden']['icoon']['opties'] ) . ';', 'before' );
} );

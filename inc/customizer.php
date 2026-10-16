<?php
/**
 * Customizer: contactgegevens en terugkerende blokken.
 * Te vinden onder Weergave → Customizer → "Bouwgroep DE Jong".
 */

defined( 'ABSPATH' ) || exit;

function bdj_customizer_defaults() {
	return array(
		'adres'        => 'Hornleger 2',
		'postcode'     => '8431 HL Oosterwolde',
		'telefoon'     => '0516 51 22 80',
		'email'        => 'info@bouwgroepdejong.nl',
		'kvk'          => '01028857',
		'btw'          => 'NL003409533B01',
		'sbb_tonen'    => 1,
	);
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$d = bdj_customizer_defaults();

	$wp_customize->add_panel( 'bdj', array(
		'title'    => 'Bouwgroep DE Jong',
		'priority' => 30,
	) );

	$secties = array(
		'bdj_contact' => array(
			'titel'  => 'Contactgegevens',
			'velden' => array(
				'adres'    => array( 'Adres', 'text' ),
				'postcode' => array( 'Postcode en plaats', 'text' ),
				'telefoon' => array( 'Telefoon', 'text' ),
				'email'    => array( 'E-mail', 'email' ),
				'kvk'      => array( 'KvK-nummer', 'text' ),
				'btw'      => array( 'BTW-nummer', 'text' ),
			),
		),
		'bdj_overig'  => array(
			'titel'  => 'Footer',
			'velden' => array(
				'sbb_tonen' => array( 'SBB-leerbedrijf in de footer tonen', 'checkbox' ),
			),
		),
	);

	foreach ( $secties as $sectie => $info ) {
		$wp_customize->add_section( $sectie, array( 'title' => $info['titel'], 'panel' => 'bdj' ) );
		foreach ( $info['velden'] as $key => $veld ) {
			list( $label, $type ) = $veld;
			$wp_customize->add_setting( 'bdj_' . $key, array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => 'textarea' === $type ? 'sanitize_textarea_field' : ( 'image' === $type ? 'absint' : ( 'checkbox' === $type ? 'absint' : 'sanitize_text_field' ) ),
			) );
			if ( 'image' === $type ) {
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'bdj_' . $key, array(
					'label'     => $label,
					'section'   => $sectie,
					'mime_type' => 'image',
				) ) );
			} else {
				$wp_customize->add_control( 'bdj_' . $key, array(
					'label'   => $label,
					'section' => $sectie,
					'type'    => $type,
				) );
			}
		}
	}
} );

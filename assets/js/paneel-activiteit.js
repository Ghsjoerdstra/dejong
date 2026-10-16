/**
 * Paneel "Activiteit" in het tabblad Pagina van de blokeditor.
 * Alleen zichtbaar als de pagina het template "Activiteit" heeft.
 * Deze gegevens worden gebruikt in de activiteitenslider, het submenu en de activiteitentegels.
 */
( function ( wp, iconen ) {
	var el = wp.element.createElement;
	var c = wp.components;
	var be = wp.blockEditor;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );

	/** Afbeelding kiezen / vervangen / verwijderen voor één meta-veld. */
	function AfbeeldingVeld( props ) {
		var id = props.waarde || 0;
		var media = useSelect( function ( select ) {
			return id ? select( 'core' ).getEntityRecord( 'postType', 'attachment', id ) : null;
		}, [ id ] );
		var url = media && ( ( media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url ) || media.source_url );

		return el( 'div', { style: { marginBottom: 16 } },
			el( 'p', { className: 'bdj-veld-label' }, props.label ),
			url ? el( 'img', { src: url, alt: '', style: { maxWidth: props.klein ? 80 : '100%', height: 'auto', display: 'block', marginBottom: 8, background: '#f0f0f1' } } ) : null,
			el( be.MediaUploadCheck, null,
				el( be.MediaUpload, {
					allowedTypes: [ 'image' ],
					value: id,
					onSelect: function ( m ) { props.onChange( m.id ); },
					render: function ( o ) {
						return el( c.Button, { variant: 'secondary', onClick: o.open }, id ? 'Andere afbeelding' : 'Afbeelding kiezen' );
					}
				} )
			),
			id ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onChange( 0 ); }, style: { marginLeft: 8 } }, 'Verwijderen' ) : null,
			props.help ? el( 'p', { className: 'components-base-control__help', style: { marginTop: 8 } }, props.help ) : null
		);
	}

	function Paneel() {
		var info = useSelect( function ( select ) {
			var editor = select( 'core/editor' );
			return {
				type: editor.getCurrentPostType(),
				template: editor.getEditedPostAttribute( 'template' )
			};
		}, [] );
		var metaProp = useEntityProp( 'postType', 'page', 'meta' );
		var meta = metaProp[ 0 ] || {};
		var setMeta = metaProp[ 1 ];

		if ( info.type !== 'page' || info.template !== 'page-templates/activiteit.php' ) {
			return null;
		}

		function zet( key, waarde ) {
			var nieuw = {};
			nieuw[ key ] = waarde;
			setMeta( Object.assign( {}, meta, nieuw ) );
		}

		return el( Panel, { name: 'bdj-activiteit', title: 'Activiteit', className: 'bdj-paneel-activiteit' },
			el( 'p', { className: 'components-base-control__help' }, 'Gebruikt in de activiteitenslider, het submenu en de activiteitentegels. De korte tekst in de slider is de samenvatting van deze pagina.' ),
			el( c.SelectControl, {
				label: 'Icoon',
				value: meta._bdj_icoon || '',
				options: Object.keys( iconen ).map( function ( k ) { return { value: k, label: iconen[ k ] }; } ),
				onChange: function ( v ) { zet( '_bdj_icoon', v ); },
				__next40pxDefaultSize: true,
				__nextHasNoMarginBottom: true
			} ),
			el( 'div', { style: { height: 16 } } ),
			el( c.TextControl, {
				label: 'Knoptekst in de slider',
				help: 'Bv. "Bekijk projecten in woningbouw".',
				value: meta._bdj_knoptekst || '',
				onChange: function ( v ) { zet( '_bdj_knoptekst', v ); },
				__next40pxDefaultSize: true,
				__nextHasNoMarginBottom: true
			} ),
			el( 'div', { style: { height: 16 } } ),
			el( AfbeeldingVeld, {
				label: 'Foto in de slider',
				waarde: meta._bdj_slider_afbeelding,
				onChange: function ( v ) { zet( '_bdj_slider_afbeelding', v ); }
			} ),
			el( AfbeeldingVeld, {
				label: 'Eigen icoon (slider en tegels)',
				help: 'Gaat voor het gekozen icoon. Bij voorkeur een PNG met transparante achtergrond.',
				klein: true,
				waarde: meta._bdj_icoon_afbeelding,
				onChange: function ( v ) { zet( '_bdj_icoon_afbeelding', v ); }
			} ),
			el( AfbeeldingVeld, {
				label: 'Eigen icoon in de iconenbalk',
				help: 'Let op: een eigen afbeelding verandert niet van kleur bij aanwijzen of selecteren, zoals de standaardiconen wel doen.',
				klein: true,
				waarde: meta._bdj_tab_afbeelding,
				onChange: function ( v ) { zet( '_bdj_tab_afbeelding', v ); }
			} )
		);
	}

	if ( Panel ) {
		wp.plugins.registerPlugin( 'bdj-paneel-activiteit', { render: Paneel } );
	}
} )( window.wp, window.bdjIconen || {} );

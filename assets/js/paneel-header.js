/**
 * Paginaheader in de blokeditor (geen blok):
 * - paneel "Header" bovenaan het tabblad Pagina met de velden;
 * - voorbeeld van de header bovenaan het canvas; klikken opent het paneel.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var useEffect = wp.element.useEffect;
	var useState = wp.element.useState;
	var createPortal = wp.element.createPortal;
	var c = wp.components;
	var be = wp.blockEditor;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var useEntityProp = wp.coreData.useEntityProp;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	var PANEEL = 'bdj-header/bdj-header';

	function useHeader() {
		var metaProp = useEntityProp( 'postType', 'page', 'meta' );
		var meta = metaProp[ 0 ] || {};
		var setMeta = metaProp[ 1 ];
		var info = useSelect( function ( select ) {
			var editor = select( 'core/editor' );
			var featured = editor.getEditedPostAttribute( 'featured_media' );
			var fotoId = meta._bdj_header_afbeelding || featured || 0;
			var media = fotoId ? select( 'core' ).getEntityRecord( 'postType', 'attachment', fotoId ) : null;
			return {
				type: editor.getCurrentPostType(),
				featured: featured,
				fotoUrl: media && ( ( media.media_details && media.media_details.sizes && media.media_details.sizes.large && media.media_details.sizes.large.source_url ) || media.source_url ),
				eigenFoto: meta._bdj_header_afbeelding ? media : null
			};
		}, [ meta._bdj_header_afbeelding ] );
		function zet( key, waarde ) {
			var nieuw = {};
			nieuw[ key ] = waarde;
			setMeta( Object.assign( {}, meta, nieuw ) );
		}
		return { meta: meta, zet: zet, info: info };
	}

	/* ---------- Paneel met velden ---------- */

	function HeaderPaneel() {
		var h = useHeader();
		var m = h.meta;
		if ( h.info.type !== 'page' ) {
			return null;
		}
		var tonen = m._bdj_header_tonen !== false;
		var eigenUrl = h.info.eigenFoto && ( ( h.info.eigenFoto.media_details && h.info.eigenFoto.media_details.sizes && h.info.eigenFoto.media_details.sizes.medium && h.info.eigenFoto.media_details.sizes.medium.source_url ) || h.info.eigenFoto.source_url );
		var ruimte = el( 'div', { style: { height: 16 } } );

		return el( Panel, { name: 'bdj-header', title: 'Header', className: 'bdj-paneel-header', initialOpen: true },
			el( c.ToggleControl, {
				label: 'Header tonen',
				checked: tonen,
				onChange: function ( v ) { h.zet( '_bdj_header_tonen', v ); },
				__nextHasNoMarginBottom: true
			} ),
			tonen ? el( wp.element.Fragment, null,
				ruimte,
				el( 'p', { className: 'bdj-veld-label' }, 'Foto' ),
				eigenUrl ? el( 'img', { src: eigenUrl, alt: '', style: { width: '100%', height: 'auto', display: 'block', marginBottom: 8 } } ) : null,
				el( be.MediaUploadCheck, null,
					el( be.MediaUpload, {
						allowedTypes: [ 'image' ],
						value: m._bdj_header_afbeelding || 0,
						onSelect: function ( media ) { h.zet( '_bdj_header_afbeelding', media.id ); },
						render: function ( o ) { return el( c.Button, { variant: 'secondary', onClick: o.open }, m._bdj_header_afbeelding ? 'Andere foto' : 'Foto kiezen' ); }
					} )
				),
				m._bdj_header_afbeelding ? el( c.Button, { variant: 'link', isDestructive: true, style: { marginLeft: 8 }, onClick: function () { h.zet( '_bdj_header_afbeelding', 0 ); } }, 'Verwijderen' ) : null,
				el( 'p', { className: 'components-base-control__help' }, m._bdj_header_afbeelding ? '' : ( h.info.featured ? 'Nu wordt de uitgelichte afbeelding gebruikt.' : 'Geen foto gekozen: kies er een, of stel een uitgelichte afbeelding in.' ) ),
				ruimte,
				el( c.TextControl, {
					label: 'Titel op de foto',
					help: 'Leeg = geen tekstvak op de foto.',
					value: m._bdj_header_titel || '',
					onChange: function ( v ) { h.zet( '_bdj_header_titel', v ); },
					__next40pxDefaultSize: true, __nextHasNoMarginBottom: true
				} ),
				m._bdj_header_titel ? el( wp.element.Fragment, null,
					ruimte,
					el( c.TextControl, {
						label: 'Linktekst',
						value: m._bdj_header_link_tekst || '',
						onChange: function ( v ) { h.zet( '_bdj_header_link_tekst', v ); },
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true
					} ),
					ruimte,
					el( c.TextControl, {
						label: 'Link-URL',
						help: 'Bv. /contact/ of een volledige URL.',
						value: m._bdj_header_link_url || '',
						onChange: function ( v ) { h.zet( '_bdj_header_link_url', v ); },
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true
					} )
				) : null,
				ruimte,
				el( c.SelectControl, {
					label: 'Hoogte',
					value: m._bdj_header_hoogte || 'groot',
					options: [
						{ value: 'groot', label: 'Groot (800px)' },
						{ value: 'middel', label: 'Middel (600px)' },
						{ value: 'klein', label: 'Klein (400px)' }
					],
					onChange: function ( v ) { h.zet( '_bdj_header_hoogte', v ); },
					__next40pxDefaultSize: true, __nextHasNoMarginBottom: true
				} )
			) : null
		);
	}

	/* ---------- Voorbeeld bovenaan het canvas ---------- */

	function zoekCanvas() {
		var frame = document.querySelector( 'iframe[name="editor-canvas"]' );
		var doc = frame && frame.contentDocument ? frame.contentDocument : document;
		return doc.querySelector( '.editor-styles-wrapper' );
	}

	function HeaderVoorbeeld() {
		var h = useHeader();
		var m = h.meta;
		var houder = useState( null );
		var doel = houder[ 0 ], setDoel = houder[ 1 ];
		var editorDispatch = useDispatch( 'core/editor' );
		var editPostDispatch = useDispatch( 'core/edit-post' );
		var paneelOpen = useSelect( function ( select ) {
			var e = select( 'core/editor' );
			return e.isEditorPanelOpened ? e.isEditorPanelOpened( PANEEL ) : true;
		}, [] );

		// Houder bovenaan het canvas plaatsen (en terugzetten als de editor het canvas opnieuw opbouwt).
		useEffect( function () {
			function plaats() {
				var wrapper = zoekCanvas();
				if ( ! wrapper ) return;
				var bestaand = wrapper.querySelector( ':scope > .bdj-header-voorbeeld' );
				if ( ! bestaand ) {
					bestaand = wrapper.ownerDocument.createElement( 'div' );
					bestaand.className = 'bdj-header-voorbeeld';
					wrapper.insertBefore( bestaand, wrapper.firstChild );
				}
				if ( bestaand !== doel ) setDoel( bestaand );
			}
			plaats();
			var timer = setInterval( plaats, 800 );
			return function () { clearInterval( timer ); };
		}, [ doel ] );

		if ( h.info.type !== 'page' || ! doel ) {
			return null;
		}

		function openPaneel() {
			if ( editPostDispatch && editPostDispatch.openGeneralSidebar ) {
				editPostDispatch.openGeneralSidebar( 'edit-post/document' );
			}
			if ( ! paneelOpen && editorDispatch.toggleEditorPanelOpened ) {
				editorDispatch.toggleEditorPanelOpened( PANEEL );
			}
		}

		var tonen = m._bdj_header_tonen !== false;
		var inhoud;
		if ( ! tonen ) {
			inhoud = el( 'button', { type: 'button', className: 'bdj-header-uit', onClick: openPaneel }, 'Deze pagina heeft geen header — klik om er een in te stellen' );
		} else {
			var hoogte = m._bdj_header_hoogte && m._bdj_header_hoogte !== 'groot' ? ' hero-' + m._bdj_header_hoogte : '';
			inhoud = el( 'div', { className: 'bdj-header-klik', role: 'button', tabIndex: 0, onClick: openPaneel, onKeyDown: function ( e ) { if ( e.key === 'Enter' ) openPaneel(); } },
				el( 'span', { className: 'bdj-blok-label bdj-header-label' }, 'Header — klik om te bewerken' ),
				el( 'section', { className: 'hero' + hoogte },
					h.info.fotoUrl ? el( 'img', { src: h.info.fotoUrl, alt: '' } ) : el( 'div', { className: 'bdj-header-geen-foto' }, 'Nog geen foto gekozen' ),
					m._bdj_header_titel ? el( 'div', { className: 'hero-tekst' },
						el( 'div', { className: 'hero-vak' },
							el( 'div', { className: 'titel' }, m._bdj_header_titel ),
							m._bdj_header_link_tekst ? el( 'span', { className: 'link-lijn' }, m._bdj_header_link_tekst ) : null
						)
					) : null
				)
			);
		}
		return createPortal( inhoud, doel );
	}

	if ( Panel ) {
		wp.plugins.registerPlugin( 'bdj-header', {
			render: function () {
				return el( wp.element.Fragment, null, el( HeaderPaneel ), el( HeaderVoorbeeld ) );
			}
		} );
	}
} )( window.wp );

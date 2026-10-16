/**
 * Bouwgroep DE Jong – blokken in de editor.
 *
 * Eén generiek editor-component voor alle blokken: de velden komen uit het schema
 * (window.bdjBlokken, aangeleverd door inc/blocks.php) en het voorbeeld wordt door PHP
 * gerenderd via ServerSideRender. Geen build-stap nodig.
 */
( function ( wp, schema ) {
	if ( ! wp || ! schema ) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var c = wp.components;
	var useSelect = wp.data.useSelect;
	var ServerSideRender = wp.serverSideRender;

	/* ---------- Velden ---------- */

	function zichtbaar( veld, attrs ) {
		if ( ! veld.toon ) {
			return true;
		}
		return Object.keys( veld.toon ).every( function ( key ) {
			var eis = veld.toon[ key ];
			var waarde = attrs[ key ];
			if ( eis === '!' ) {
				return !! waarde; // alleen tonen als het andere veld gevuld is
			}
			return Array.isArray( eis ) ? eis.indexOf( waarde ) !== -1 : waarde === eis;
		} );
	}

	function opties( obj ) {
		return Object.keys( obj ).map( function ( k ) {
			return { value: k, label: obj[ k ] };
		} );
	}

	function Afbeelding( props ) {
		var id = props.value;
		var media = useSelect( function ( select ) {
			return id ? select( 'core' ).getEntityRecord( 'postType', 'attachment', id ) : null;
		}, [ id ] );
		var url = media && ( ( media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url ) || media.source_url );

		return el( 'div', { className: 'bdj-veld-afbeelding' },
			el( 'p', { className: 'bdj-veld-label' }, props.label ),
			url ? el( 'img', { src: url, alt: '' } ) : null,
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
			id ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onChange( 0 ); } }, 'Verwijderen' ) : null,
			props.hint ? el( 'p', { className: 'components-base-control__help' }, props.hint ) : null
		);
	}

	function Galerij( props ) {
		var ids = props.value || [];
		var media = useSelect( function ( select ) {
			return ids.map( function ( id ) { return select( 'core' ).getEntityRecord( 'postType', 'attachment', id ); } );
		}, [ ids.join( ',' ) ] );

		function verplaats( i, richting ) {
			var nieuw = ids.slice();
			var j = i + richting;
			if ( j < 0 || j >= nieuw.length ) return;
			var tmp = nieuw[ i ]; nieuw[ i ] = nieuw[ j ]; nieuw[ j ] = tmp;
			props.onChange( nieuw );
		}

		return el( 'div', { className: 'bdj-veld-galerij' },
			el( 'p', { className: 'bdj-veld-label' }, props.label ),
			el( 'ul', null, ids.map( function ( id, i ) {
				var m = media[ i ];
				var url = m && ( ( m.media_details && m.media_details.sizes && m.media_details.sizes.thumbnail && m.media_details.sizes.thumbnail.source_url ) || m.source_url );
				return el( 'li', { key: id },
					url ? el( 'img', { src: url, alt: '' } ) : el( 'span', { className: 'bdj-laden' }, '…' ),
					el( 'div', { className: 'bdj-knoppen' },
						el( c.Button, { icon: 'arrow-left-alt2', label: 'Naar voren', size: 'small', onClick: function () { verplaats( i, -1 ); } } ),
						el( c.Button, { icon: 'arrow-right-alt2', label: 'Naar achteren', size: 'small', onClick: function () { verplaats( i, 1 ); } } ),
						el( c.Button, { icon: 'no-alt', label: 'Verwijderen', size: 'small', isDestructive: true, onClick: function () { props.onChange( ids.filter( function ( x ) { return x !== id; } ) ); } } )
					)
				);
			} ) ),
			el( be.MediaUploadCheck, null,
				el( be.MediaUpload, {
					allowedTypes: [ 'image' ],
					multiple: true,
					gallery: false,
					value: ids,
					onSelect: function ( lijst ) {
						var nieuw = ids.slice();
						lijst.forEach( function ( m ) { if ( nieuw.indexOf( m.id ) === -1 ) nieuw.push( m.id ); } );
						props.onChange( nieuw );
					},
					render: function ( o ) {
						return el( c.Button, { variant: 'secondary', onClick: o.open }, 'Afbeeldingen toevoegen' );
					}
				} )
			),
			props.hint ? el( 'p', { className: 'components-base-control__help' }, props.hint ) : null
		);
	}

	function Herhaler( props ) {
		var rijen = props.value || [];
		var sub = props.veld.velden;

		function wijzig( i, key, waarde ) {
			var nieuw = rijen.map( function ( r, j ) {
				if ( j !== i ) return r;
				var kopie = Object.assign( {}, r );
				kopie[ key ] = waarde;
				return kopie;
			} );
			props.onChange( nieuw );
		}
		function verplaats( i, richting ) {
			var nieuw = rijen.slice();
			var j = i + richting;
			if ( j < 0 || j >= nieuw.length ) return;
			var tmp = nieuw[ i ]; nieuw[ i ] = nieuw[ j ]; nieuw[ j ] = tmp;
			props.onChange( nieuw );
		}
		function leegItem() {
			var item = {};
			Object.keys( sub ).forEach( function ( k ) {
				item[ k ] = sub[ k ].opties ? Object.keys( sub[ k ].opties )[ 0 ] : '';
			} );
			return item;
		}

		return el( 'div', { className: 'bdj-veld-herhaler' },
			rijen.map( function ( rij, i ) {
				return el( c.PanelBody, { key: i, title: ( i + 1 ) + '. ' + ( rij.titel || '(zonder titel)' ), initialOpen: false },
					Object.keys( sub ).map( function ( key ) {
						return veldControl( key, sub[ key ], rij[ key ], function ( w ) { wijzig( i, key, w ); } );
					} ),
					el( 'div', { className: 'bdj-knoppen' },
						el( c.Button, { icon: 'arrow-up-alt2', label: 'Omhoog', onClick: function () { verplaats( i, -1 ); } } ),
						el( c.Button, { icon: 'arrow-down-alt2', label: 'Omlaag', onClick: function () { verplaats( i, 1 ); } } ),
						el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onChange( rijen.filter( function ( r, j ) { return j !== i; } ) ); } }, 'Rij verwijderen' )
					)
				);
			} ),
			el( c.Button, { variant: 'secondary', onClick: function () { props.onChange( rijen.concat( [ leegItem() ] ) ); } }, '+ Rij toevoegen' )
		);
	}

	function veldControl( key, veld, waarde, onChange ) {
		var basis = { key: key, label: veld.label, help: veld.hint || undefined, __nextHasNoMarginBottom: true };
		switch ( veld.type ) {
			case 'textarea':
				return el( c.TextareaControl, Object.assign( basis, { value: waarde || '', rows: 5, onChange: onChange } ) );
			case 'select':
				return el( c.SelectControl, Object.assign( basis, { value: waarde || '', options: opties( veld.opties ), onChange: onChange, __next40pxDefaultSize: true } ) );
			case 'toggle':
				return el( c.ToggleControl, Object.assign( basis, { checked: !! waarde, onChange: onChange } ) );
			case 'number':
				return el( c.TextControl, Object.assign( basis, { type: 'number', min: 0, value: waarde || 0, onChange: function ( v ) { onChange( parseInt( v, 10 ) || 0 ); }, __next40pxDefaultSize: true } ) );
			case 'image':
				return el( Afbeelding, { key: key, label: veld.label, hint: veld.hint, value: waarde || 0, onChange: onChange } );
			case 'gallery':
				return el( Galerij, { key: key, label: veld.label, hint: veld.hint, value: waarde || [], onChange: onChange } );
			case 'repeater':
				return el( Herhaler, { key: key, veld: veld, value: waarde || [], onChange: onChange } );
			default:
				return el( c.TextControl, Object.assign( basis, { value: waarde || '', onChange: onChange, __next40pxDefaultSize: true } ) );
		}
	}

	/* ---------- Blokken registreren ---------- */

	Object.keys( schema ).forEach( function ( naam ) {
		var blok = schema[ naam ];
		var blokNaam = 'bdj/' + naam;

		wp.blocks.registerBlockType( blokNaam, {
			edit: function ( props ) {
				var attrs = props.attributes;
				var blockProps = be.useBlockProps( { className: 'bdj-blok-editor' } );
				var postId = useSelect( function ( select ) {
					var editor = select( 'core/editor' );
					return editor ? editor.getCurrentPostId() : 0;
				}, [] );

				// Velden per paneel groeperen.
				var panelen = {};
				Object.keys( blok.velden ).forEach( function ( key ) {
					var veld = blok.velden[ key ];
					if ( ! zichtbaar( veld, attrs ) ) return;
					var paneel = veld.paneel || 'Inhoud';
					( panelen[ paneel ] = panelen[ paneel ] || [] ).push(
						veldControl( key, veld, attrs[ key ], function ( waarde ) {
							var nieuw = {};
							nieuw[ key ] = waarde;
							props.setAttributes( nieuw );
						} )
					);
				} );

				return el( Fragment, null,
					el( be.InspectorControls, null,
						Object.keys( panelen ).map( function ( titel, i ) {
							return el( c.PanelBody, { key: titel, title: titel, initialOpen: i === 0 },
								el( 'div', { className: 'bdj-velden' }, panelen[ titel ] )
							);
						} )
					),
					el( 'div', blockProps,
						el( 'div', { className: 'bdj-blok-label' }, blok.titel ),
						el( ServerSideRender, {
							block: blokNaam,
							attributes: attrs,
							httpMethod: 'POST',
							urlQueryArgs: { post_id: postId },
							EmptyResponsePlaceholder: function () {
								return el( c.Placeholder, { icon: blok.icoon, label: blok.titel, instructions: 'Vul de velden in de zijbalk in.' } );
							}
						} )
					)
				);
			},
			save: function () {
				return null; // dynamisch blok: PHP maakt de HTML
			}
		} );
	} );
} )( window.wp, window.bdjBlokken );

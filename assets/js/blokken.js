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

	/** Volgorde van de projecten bij een activiteit; slaat direct op in menu_order (zie inc/post-types.php). */
	function ProjectVolgorde( props ) {
		var state = wp.element.useState( null );
		var lijst = state[ 0 ];
		var zetLijst = state[ 1 ];
		var bezig = wp.element.useState( false );
		var pad = '/bdj/v1/projectvolgorde';
		var activiteit = props.activiteit || props.postId;

		wp.element.useEffect( function () {
			zetLijst( null );
			if ( ! activiteit ) return;
			wp.apiFetch( { path: pad + '?activiteit=' + encodeURIComponent( activiteit ) } ).then( zetLijst, function () { zetLijst( [] ); } );
		}, [ activiteit ] );

		function verplaats( i, richting ) {
			var j = i + richting;
			if ( j < 0 || j >= lijst.length ) return;
			var nieuw = lijst.slice();
			var tmp = nieuw[ i ]; nieuw[ i ] = nieuw[ j ]; nieuw[ j ] = tmp;
			zetLijst( nieuw );
			bezig[ 1 ]( true );
			wp.apiFetch( { path: pad, method: 'POST', data: { ids: nieuw.map( function ( p ) { return p.id; } ) } } )
				.then( props.onOpgeslagen )
				.finally( function () { bezig[ 1 ]( false ); } );
		}

		var van = props.vanaf || 0;
		var tot = props.aantal > 0 ? van + props.aantal : Infinity;

		return el( 'div', { className: 'bdj-veld-volgorde' },
			el( 'p', { className: 'bdj-veld-label' }, props.label, bezig[ 0 ] ? el( c.Spinner ) : null ),
			lijst === null
				? el( c.Spinner )
				: lijst.length
					? el( 'ol', null, lijst.map( function ( p, i ) {
						return el( 'li', { key: p.id, className: i >= van && i < tot ? 'is-zichtbaar' : '' },
							el( 'span', null, p.titel ),
							el( c.Button, { icon: 'arrow-up-alt2', label: 'Omhoog', size: 'small', disabled: i === 0, onClick: function () { verplaats( i, -1 ); } } ),
							el( c.Button, { icon: 'arrow-down-alt2', label: 'Omlaag', size: 'small', disabled: i === lijst.length - 1, onClick: function () { verplaats( i, 1 ); } } )
						);
					} ) )
					: el( 'p', null, 'Geen projecten bij deze activiteit.' ),
			props.hint ? el( 'p', { className: 'components-base-control__help' }, props.hint ) : null
		);
	}

	/** Zelf projecten kiezen voor één blok (attribuut met project-ID's, in volgorde). */
	var alleProjecten = null; // één keer ophalen, gedeeld door alle blokken
	function ProjectSelectie( props ) {
		var ids = props.value || [];
		var state = wp.element.useState( alleProjecten );
		var projecten = state[ 0 ];
		var zetProjecten = state[ 1 ];

		wp.element.useEffect( function () {
			if ( projecten ) return;
			wp.apiFetch( { path: '/bdj/v1/projecten' } ).then( function ( lijst ) {
				alleProjecten = lijst;
				zetProjecten( lijst );
			}, function () { zetProjecten( [] ); } );
		}, [] );

		if ( ! projecten ) {
			return el( 'div', { className: 'bdj-veld-volgorde' }, el( 'p', { className: 'bdj-veld-label' }, props.label ), el( c.Spinner ) );
		}

		var perId = {};
		projecten.forEach( function ( p ) { perId[ p.id ] = p; } );
		function naam( p ) {
			return p.titel + ( p.activiteit ? ' — ' + p.activiteit : '' );
		}
		function verplaats( i, richting ) {
			var j = i + richting;
			if ( j < 0 || j >= ids.length ) return;
			var nieuw = ids.slice();
			var tmp = nieuw[ i ]; nieuw[ i ] = nieuw[ j ]; nieuw[ j ] = tmp;
			props.onChange( nieuw );
		}

		return el( 'div', { className: 'bdj-veld-volgorde' },
			el( 'p', { className: 'bdj-veld-label' }, props.label ),
			ids.length
				? el( 'ol', null, ids.map( function ( id, i ) {
					return el( 'li', { key: id, className: 'is-zichtbaar' },
						el( 'span', null, perId[ id ] ? naam( perId[ id ] ) : '(verwijderd project)' ),
						el( c.Button, { icon: 'arrow-up-alt2', label: 'Omhoog', size: 'small', disabled: i === 0, onClick: function () { verplaats( i, -1 ); } } ),
						el( c.Button, { icon: 'arrow-down-alt2', label: 'Omlaag', size: 'small', disabled: i === ids.length - 1, onClick: function () { verplaats( i, 1 ); } } ),
						el( c.Button, { icon: 'no-alt', label: 'Uit dit blok halen', size: 'small', isDestructive: true, onClick: function () { props.onChange( ids.filter( function ( x ) { return x !== id; } ) ); } } )
					);
				} ) )
				: el( 'p', null, 'Nog geen projecten gekozen.' ),
			el( c.ComboboxControl, {
				label: 'Project toevoegen',
				value: null,
				__nextHasNoMarginBottom: true,
				__next40pxDefaultSize: true,
				options: projecten.filter( function ( p ) { return ids.indexOf( p.id ) === -1; } ).map( function ( p ) {
					return { value: String( p.id ), label: naam( p ) };
				} ),
				onChange: function ( v ) {
					if ( v ) props.onChange( ids.concat( [ parseInt( v, 10 ) ] ) );
				}
			} ),
			props.hint ? el( 'p', { className: 'components-base-control__help' }, props.hint ) : null
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
			case 'projectselectie':
				return el( ProjectSelectie, { key: key, label: veld.label, hint: veld.hint, value: waarde || [], onChange: onChange } );
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
				var isOpen = wp.element.useState( false );
				var versie = wp.element.useState( 0 ); // na het opslaan van de projectvolgorde het voorbeeld verversen
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
					if ( veld.type === 'projectvolgorde' ) {
						( panelen[ paneel ] = panelen[ paneel ] || [] ).push( el( ProjectVolgorde, {
							key: key, label: veld.label, hint: veld.hint, activiteit: attrs.activiteit, postId: postId, vanaf: attrs.vanaf, aantal: attrs.aantal,
							onOpgeslagen: function () { versie[ 1 ]( versie[ 0 ] + 1 ); }
						} ) );
						return;
					}
					( panelen[ paneel ] = panelen[ paneel ] || [] ).push(
						veldControl( key, veld, attrs[ key ], function ( waarde ) {
							var nieuw = {};
							nieuw[ key ] = waarde;
							props.setAttributes( nieuw );
						} )
					);
				} );

				var titels = Object.keys( panelen );
				var open = isOpen[ 0 ];
				var zetOpen = isOpen[ 1 ];
				var sluit = function () { zetOpen( false ); };

				return el( Fragment, null,
					el( be.BlockControls, { group: 'other' },
						el( c.ToolbarButton, { icon: 'edit', label: 'Blok bewerken', text: 'Bewerken', onClick: function () { zetOpen( true ); } } )
					),
					el( be.InspectorControls, null,
						el( c.PanelBody, null,
							el( c.Button, { variant: 'primary', icon: 'edit', onClick: function () { zetOpen( true ); }, className: 'bdj-open-popup' }, 'Blok bewerken' ),
							el( 'p', { className: 'components-base-control__help' }, 'Tip: dubbelklik op het blok om het direct te bewerken.' )
						)
					),
					open ? el( c.Modal, { title: blok.titel + ' bewerken', onRequestClose: sluit, size: 'medium', className: 'bdj-popup' },
						titels.length > 1
							? el( c.TabPanel, {
								className: 'bdj-popup-tabs',
								tabs: titels.map( function ( t ) { return { name: t, title: t }; } )
							}, function ( tab ) {
								return el( 'div', { className: 'bdj-velden' }, panelen[ tab.name ] );
							} )
							: el( 'div', { className: 'bdj-velden' }, panelen[ titels[ 0 ] ] ),
						el( 'div', { className: 'bdj-popup-voet' },
							el( c.Button, { variant: 'primary', onClick: sluit }, 'Klaar' )
						)
					) : null,
					el( 'div', Object.assign( {}, blockProps, { onDoubleClick: function () { zetOpen( true ); } } ),
						el( 'div', { className: 'bdj-blok-label' }, blok.titel ),
						el( ServerSideRender, {
							block: blokNaam,
							attributes: attrs,
							httpMethod: 'POST',
							urlQueryArgs: versie[ 0 ] ? { post_id: postId, v: versie[ 0 ] } : { post_id: postId },
							EmptyResponsePlaceholder: function () {
								return el( c.Placeholder, { icon: blok.icoon, label: blok.titel, instructions: 'Dubbelklik op het blok of klik op "Bewerken" om de inhoud in te vullen.' } );
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

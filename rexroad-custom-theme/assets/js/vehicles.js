/**
 * /vehicles/ directory: progressive-enhancement live filtering.
 *
 * The full directory is already server-rendered in the HTML. This
 * script only toggles the `hidden` attribute on already-present
 * elements as the visitor types — no fetch, no XHR, no rebuilding of
 * content. The search <form> still submits as a normal GET request if
 * JavaScript is unavailable, and PHP renders the same matches
 * server-side in that case.
 */
( function () {
	'use strict';

	function normalize( value ) {
		return ( value || '' ).toLowerCase().replace( /[^a-z0-9]/g, '' );
	}

	function init() {
		var input = document.getElementById( 'rexroad-vehicle-search-input' );
		var status = document.getElementById( 'rexroad-vehicle-search-status' );
		var groups = document.querySelectorAll( '.rr-vehicle-group' );

		if ( ! input || groups.length === 0 ) {
			return;
		}

		var serverRenderedStatus = status ? status.textContent : '';

		function applyFilter() {
			var needle = normalize( input.value );
			var visibleModelCount = 0;

			if ( '' === needle ) {
				groups.forEach( function ( group ) {
					group.hidden = false;
					group.querySelectorAll( '.rr-vehicle-make' ).forEach( function ( make ) {
						make.hidden = false;
						make.querySelectorAll( '.rr-vehicle-model' ).forEach( function ( model ) {
							model.hidden = false;
						} );
					} );
				} );

				if ( status ) {
					status.textContent = serverRenderedStatus;
				}
				return;
			}

			groups.forEach( function ( group ) {
				var groupHasVisibleMake = false;

				group.querySelectorAll( '.rr-vehicle-make' ).forEach( function ( make ) {
					var makeMatches = ( make.getAttribute( 'data-search-make' ) || '' ).indexOf( needle ) !== -1;
					var makeHasVisibleModel = false;

					make.querySelectorAll( '.rr-vehicle-model' ).forEach( function ( model ) {
						var modelMatches = makeMatches || ( model.getAttribute( 'data-search-model' ) || '' ).indexOf( needle ) !== -1;
						model.hidden = ! modelMatches;
						if ( modelMatches ) {
							makeHasVisibleModel = true;
							visibleModelCount++;
						}
					} );

					var showMake = makeMatches || makeHasVisibleModel;
					make.hidden = ! showMake;
					if ( showMake ) {
						groupHasVisibleMake = true;
					}
				} );

				group.hidden = ! groupHasVisibleMake;
			} );

			if ( status ) {
				status.textContent = visibleModelCount + ' matching model' + ( 1 === visibleModelCount ? '' : 's' ) + ' shown below.';
			}
		}

		input.addEventListener( 'input', applyFilter );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

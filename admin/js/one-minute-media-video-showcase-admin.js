(function( $ ) {
	'use strict';

	var settings = window.ommvsAdmin || {};

	function getRows( $section ) {
		return $section.find( '[data-ommvs-rows]' ).children( '[data-ommvs-row]' );
	}

	function escapeRegExp( value ) {
		return value.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );
	}

	function reindexSection( $section ) {
		var metaKey = $section.data( 'meta-key' );
		var namePattern = new RegExp( escapeRegExp( metaKey ) + '\\[[^\\]]+\\]' );

		getRows( $section ).each( function( index ) {
			var $row = $( this );

			$row.attr( 'data-index', index );

			$row.find( '[name]' ).each( function() {
				this.name = this.name.replace( namePattern, metaKey + '[' + index + ']' );
			} );
		} );
	}

	function updateSectionState( $section ) {
		var rowCount = getRows( $section ).length;

		$section.find( '[data-ommvs-empty-message]' ).prop( 'hidden', rowCount > 0 );
	}

	function initializeVideoSelects( $context ) {
		if ( ! $.fn.select2 ) {
			return;
		}

		$context.find( '[data-ommvs-video-select]' ).each( function() {
			var $select = $( this );

			if ( $select.hasClass( 'select2-hidden-accessible' ) ) {
				return;
			}

			$select.select2( {
				allowClear: true,
				placeholder: $select.find( 'option:first' ).text(),
				width: '100%'
			} );
		} );
	}

	function initializeSection( $section ) {
		var $rows = $section.find( '[data-ommvs-rows]' );

		if ( $.fn.sortable ) {
			$rows.sortable( {
				cancel: 'input,textarea,select,option',
				handle: '[data-ommvs-row-handle]',
				items: '[data-ommvs-row]',
				placeholder: 'ommvs-placement-row--placeholder',
				update: function() {
					reindexSection( $section );
					updateSectionState( $section );
				}
			} );
		}

		reindexSection( $section );
		initializeVideoSelects( $section );
		updateSectionState( $section );
	}

	function getTemplateHtml( $section ) {
		return $.trim( $section.find( '[data-ommvs-row-template]' ).html() || '' );
	}

	function addRow( $section ) {
		var rowCount = getRows( $section ).length;
		var template = getTemplateHtml( $section );
		var html;

		if ( ! template ) {
			return;
		}

		html = template.replace( /__index__/g, rowCount );
		$section.find( '[data-ommvs-rows]' ).append( html );

		reindexSection( $section );
		initializeVideoSelects( $section );
		updateSectionState( $section );
	}

	function setThumbnail( $thumbnail, attachment ) {
		var url = attachment.url;
		var alt = attachment.alt || '';

		if ( attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url ) {
			url = attachment.sizes.thumbnail.url;
		}

		$thumbnail.find( '[data-ommvs-thumbnail-id]' ).val( attachment.id );
		$thumbnail.find( '[data-ommvs-thumbnail-preview]' ).empty().append(
			$( '<img />', {
				alt: alt,
				class: 'ommvs-placement-thumbnail__image',
				src: url
			} )
		);
		$thumbnail.find( '[data-ommvs-remove-thumbnail]' ).prop( 'hidden', false );
	}

	function clearThumbnail( $thumbnail ) {
		$thumbnail.find( '[data-ommvs-thumbnail-id]' ).val( '' );
		$thumbnail.find( '[data-ommvs-thumbnail-preview]' ).empty();
		$thumbnail.find( '[data-ommvs-remove-thumbnail]' ).prop( 'hidden', true );
	}

	function openThumbnailFrame( $thumbnail ) {
		var frame = wp.media( {
			title: settings.strings && settings.strings.chooseThumbnail ? settings.strings.chooseThumbnail : 'Choose Thumbnail',
			button: {
				text: settings.strings && settings.strings.useThumbnail ? settings.strings.useThumbnail : 'Use Thumbnail'
			},
			library: {
				type: 'image'
			},
			multiple: false
		} );

		frame.on( 'select', function() {
			var attachment = frame.state().get( 'selection' ).first().toJSON();

			setThumbnail( $thumbnail, attachment );
		} );

		frame.open();
	}

	$( function() {
		$( '[data-ommvs-placement-section]' ).each( function() {
			initializeSection( $( this ) );
		} );

		$( document ).on( 'click', '[data-ommvs-add-row]', function() {
			addRow( $( this ).closest( '[data-ommvs-placement-section]' ) );
		} );

		$( document ).on( 'click', '[data-ommvs-remove-row]', function() {
			var $section = $( this ).closest( '[data-ommvs-placement-section]' );
			var $row = $( this ).closest( '[data-ommvs-row]' );

			if ( $.fn.select2 ) {
				$row.find( '[data-ommvs-video-select].select2-hidden-accessible' ).select2( 'destroy' );
			}

			$row.remove();
			reindexSection( $section );
			updateSectionState( $section );
		} );

		$( document ).on( 'click', '[data-ommvs-select-thumbnail]', function() {
			openThumbnailFrame( $( this ).closest( '[data-ommvs-thumbnail]' ) );
		} );

		$( document ).on( 'click', '[data-ommvs-remove-thumbnail]', function() {
			clearThumbnail( $( this ).closest( '[data-ommvs-thumbnail]' ) );
		} );
	} );

})( jQuery );

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

	function normalizeMatcherValue( value ) {
		return $.trim( String( value || '' ) ).toLowerCase();
	}

	function matchVideoSelectOption( params, data ) {
		var term = normalizeMatcherValue( params.term );
		var text;
		var postTitle;

		if ( '' === term ) {
			return data;
		}

		if ( data.children && data.children.length ) {
			var matchedGroup = $.extend( true, {}, data );

			matchedGroup.children = data.children.filter( function( child ) {
				return null !== matchVideoSelectOption( params, child );
			} );

			return matchedGroup.children.length ? matchedGroup : null;
		}

		text = normalizeMatcherValue( data.text );
		postTitle = data.element ? normalizeMatcherValue( $( data.element ).data( 'ommvs-post-title' ) ) : '';

		return -1 !== text.indexOf( term ) || -1 !== postTitle.indexOf( term ) ? data : null;
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
				matcher: matchVideoSelectOption,
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

	function isElementFullyVisible( element ) {
		var rect;
		var viewportHeight;
		var viewportWidth;

		if ( ! element || ! element.getBoundingClientRect ) {
			return true;
		}

		rect = element.getBoundingClientRect();
		viewportHeight = window.innerHeight || document.documentElement.clientHeight;
		viewportWidth = window.innerWidth || document.documentElement.clientWidth;

		return rect.top >= 0 && rect.left >= 0 && rect.bottom <= viewportHeight && rect.right <= viewportWidth;
	}

	function scrollRowIntoView( $row ) {
		var element = $row.get( 0 );

		if ( ! element || isElementFullyVisible( element ) || ! element.scrollIntoView ) {
			return;
		}

		try {
			element.scrollIntoView( {
				behavior: 'smooth',
				block: 'center',
				inline: 'nearest'
			} );
		} catch ( error ) {
			element.scrollIntoView();
		}
	}

	function focusRowVideoSelect( $row ) {
		var $select = $row.find( '[data-ommvs-video-select]' ).first();

		if ( ! $select.length ) {
			return;
		}

		window.setTimeout( function() {
			if ( ! $select.closest( 'body' ).length ) {
				return;
			}

			if ( $.fn.select2 && $select.hasClass( 'select2-hidden-accessible' ) ) {
				$select.select2( 'open' );
				return;
			}

			$select.trigger( 'focus' );
		}, 300 );
	}

	function getAdminString( key, fallback ) {
		return settings.strings && settings.strings[ key ] ? settings.strings[ key ] : fallback;
	}

	function setVideoCategoryStatus( $metabox, message, isError ) {
		var $status = $metabox.find( '[data-ommvs-video-category-status]' ).first();

		if ( ! $status.length ) {
			return;
		}

		$status
			.toggleClass( 'is-error', !! isError )
			.text( message || '' );
	}

	function sortVideoCategoryOptions( $select ) {
		var $emptyOption = $select.find( 'option[value=""]' ).first();
		var options = $select.find( 'option' ).not( $emptyOption ).get();

		options.sort( function( firstOption, secondOption ) {
			return $.trim( $( firstOption ).text() ).localeCompare( $.trim( $( secondOption ).text() ), undefined, {
				sensitivity: 'base'
			} );
		} );

		$select.empty().append( $emptyOption ).append( options );
	}

	function selectVideoCategoryTerm( $select, term ) {
		var termId = term && term.id ? String( term.id ) : '';
		var termName = term && term.name ? String( term.name ) : '';
		var $option;

		if ( ! termId || ! termName ) {
			return;
		}

		$option = $select.find( 'option[value="' + termId.replace( /"/g, '\\"' ) + '"]' );

		if ( ! $option.length ) {
			$option = $( '<option />', {
				text: termName,
				value: termId
			} );

			$select.append( $option );
			sortVideoCategoryOptions( $select );
			$option = $select.find( 'option[value="' + termId.replace( /"/g, '\\"' ) + '"]' );
		} else {
			$option.text( termName );
		}

		$select.val( termId ).trigger( 'change' );
	}

	function addVideoCategory( $button ) {
		var $metabox = $button.closest( '[data-ommvs-video-category-metabox]' );
		var $input = $metabox.find( '[data-ommvs-video-category-name]' ).first();
		var $select = $metabox.find( '[data-ommvs-video-category-select]' ).first();
		var name = $.trim( $input.val() || '' );
		var ajaxUrl = settings.ajaxUrl || window.ajaxurl || '';

		if ( ! name ) {
			setVideoCategoryStatus( $metabox, getAdminString( 'categoryNameRequired', 'Enter a category name first.' ), true );
			$input.trigger( 'focus' );
			return;
		}

		if ( ! ajaxUrl || ! settings.videoCategoryNonce ) {
			setVideoCategoryStatus( $metabox, getAdminString( 'categoryAddFailed', 'Could not add the category. Please try again.' ), true );
			return;
		}

		$button.prop( 'disabled', true );
		setVideoCategoryStatus( $metabox, getAdminString( 'addingCategory', 'Adding category...' ), false );

		$.ajax( {
			url: ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ommvs_add_video_category',
				nonce: settings.videoCategoryNonce,
				name: name
			}
		} )
			.done( function( response ) {
				var message = response && response.data && response.data.message ? response.data.message : '';
				var term = response && response.data && response.data.term ? response.data.term : null;

				if ( ! response || ! response.success || ! term ) {
					setVideoCategoryStatus( $metabox, message || getAdminString( 'categoryAddFailed', 'Could not add the category. Please try again.' ), true );
					return;
				}

				selectVideoCategoryTerm( $select, term );
				$input.val( '' );
				setVideoCategoryStatus( $metabox, message, false );
				$select.trigger( 'focus' );
			} )
			.fail( function( xhr ) {
				var message = xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ? xhr.responseJSON.data.message : '';

				setVideoCategoryStatus( $metabox, message || getAdminString( 'categoryAddFailed', 'Could not add the category. Please try again.' ), true );
			} )
			.always( function() {
				$button.prop( 'disabled', false );
			} );
	}

	function addRow( $section ) {
		var rowCount = getRows( $section ).length;
		var template = getTemplateHtml( $section );
		var html;
		var $newRow;

		if ( ! template ) {
			return;
		}

		html = template.replace( /__index__/g, rowCount );
		$newRow = $( html );
		$section.find( '[data-ommvs-rows]' ).append( $newRow );

		reindexSection( $section );
		initializeVideoSelects( $newRow );
		updateSectionState( $section );
		scrollRowIntoView( $newRow );
		focusRowVideoSelect( $newRow );
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

		$( document ).on( 'click', '[data-ommvs-video-category-submit]', function() {
			addVideoCategory( $( this ) );
		} );

		$( document ).on( 'keydown', '[data-ommvs-video-category-name]', function( event ) {
			if ( 13 !== event.which ) {
				return;
			}

			event.preventDefault();
			addVideoCategory( $( this ).closest( '[data-ommvs-video-category-form]' ).find( '[data-ommvs-video-category-submit]' ).first() );
		} );
	} );

})( jQuery );

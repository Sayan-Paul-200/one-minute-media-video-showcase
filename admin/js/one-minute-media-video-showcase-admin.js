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

	function formatAdminString( key, fallback, value ) {
		return getAdminString( key, fallback ).replace( '%s', value );
	}

	function getHintTarget( $element ) {
		var $target = $element.closest( '.acf-input, .ommvs-video-fallback-field__control' ).first();

		return $target.length ? $target : $element.parent();
	}

	function ensureInlineHint( $element, key ) {
		var $target = getHintTarget( $element );
		var selector = '[data-ommvs-inline-hint="' + key + '"]';
		var $hint = $target.find( selector ).first();

		if ( $hint.length ) {
			return $hint;
		}

		$hint = $( '<div />', {
			'aria-live': 'polite',
			'class': 'ommvs-admin-inline-hint ommvs-admin-inline-hint--neutral',
			'data-ommvs-inline-hint': key,
			'role': 'status'
		} );

		$target.append( $hint );

		return $hint;
	}

	function setInlineHint( $hint, type, message ) {
		$hint
			.removeClass( 'ommvs-admin-inline-hint--neutral ommvs-admin-inline-hint--success ommvs-admin-inline-hint--warning' )
			.addClass( 'ommvs-admin-inline-hint--' + type )
			.text( message || '' );
	}

	function getNormalizedHashSlug( value ) {
		return $.trim( String( value || '' ) ).replace( /^#+/, '' );
	}

	function updateHashHint( $input, $hint ) {
		var rawValue = $.trim( String( $input.val() || '' ) );
		var hashSlug = getNormalizedHashSlug( rawValue );

		if ( '' === rawValue ) {
			setInlineHint( $hint, 'neutral', getAdminString( 'hashPreviewEmpty', 'Enter a hash slug to preview its frontend URL hash.' ) );
			return;
		}

		if ( '#' === rawValue.charAt( 0 ) ) {
			setInlineHint(
				$hint,
				'warning',
				formatAdminString( 'hashLeadingHashWarning', 'Store this as "%s" without the leading #. The plugin will still open #hash URLs on the frontend.', hashSlug || rawValue )
			);
			return;
		}

		setInlineHint(
			$hint,
			'success',
			formatAdminString( 'hashPreview', 'Frontend hash preview: #%s', hashSlug )
		);
	}

	function initializeHashHints() {
		$( '.ommvs-acf-field--hash-slug input, .ommvs-video-fallback-field--ommvs-hash-slug input' ).each( function() {
			var $input = $( this );
			var $hint;

			if ( $input.data( 'ommvs-hash-hint-ready' ) ) {
				return;
			}

			$input.data( 'ommvs-hash-hint-ready', true );
			$hint = ensureInlineHint( $input, 'hash' );
			updateHashHint( $input, $hint );

			$input.on( 'input change', function() {
				updateHashHint( $input, $hint );
			} );
		} );
	}

	function parseVimeoVideoId( value ) {
		var rawValue = $.trim( String( value || '' ) );
		var url;
		var host;
		var protocol;
		var segments;
		var index;

		if ( '' === rawValue ) {
			return {
				state: 'empty',
				id: ''
			};
		}

		if ( /^\d+$/.test( rawValue ) ) {
			return {
				state: 'numeric',
				id: rawValue
			};
		}

		try {
			url = new URL( rawValue );
		} catch ( error ) {
			return {
				state: 'invalid',
				id: ''
			};
		}

		protocol = String( url.protocol || '' ).replace( ':', '' ).toLowerCase();
		host = String( url.hostname || '' ).toLowerCase();

		if ( -1 === $.inArray( protocol, [ 'http', 'https' ] ) || -1 === $.inArray( host, [ 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ] ) ) {
			return {
				state: 'invalid',
				id: ''
			};
		}

		segments = String( url.pathname || '' ).replace( /^\/+|\/+$/g, '' ).split( '/' );

		for ( index = 0; index < segments.length; index++ ) {
			if ( /^\d+$/.test( segments[ index ] ) ) {
				return {
					state: 'valid',
					id: segments[ index ]
				};
			}
		}

		return {
			state: 'invalid',
			id: ''
		};
	}

	function updateVimeoHint( $input, $hint ) {
		var parsed = parseVimeoVideoId( $input.val() );

		if ( 'empty' === parsed.state ) {
			setInlineHint( $hint, 'neutral', getAdminString( 'vimeoEmpty', 'Paste a Vimeo URL, for example https://vimeo.com/879662317.' ) );
			return;
		}

		if ( 'numeric' === parsed.state ) {
			setInlineHint( $hint, 'warning', getAdminString( 'vimeoNumericOnly', 'Paste the full Vimeo URL, not only the numeric video ID.' ) );
			return;
		}

		if ( 'valid' === parsed.state ) {
			setInlineHint(
				$hint,
				'success',
				formatAdminString( 'vimeoDetected', 'Detected Vimeo ID: %s', parsed.id )
			);
			return;
		}

		setInlineHint( $hint, 'warning', getAdminString( 'vimeoInvalid', 'This does not look like a supported Vimeo URL.' ) );
	}

	function initializeVimeoHints() {
		$( '.ommvs-acf-field--video-url input, .ommvs-video-fallback-field--ommvs-video-url input' ).each( function() {
			var $input = $( this );
			var $hint;

			if ( $input.data( 'ommvs-vimeo-hint-ready' ) ) {
				return;
			}

			$input.data( 'ommvs-vimeo-hint-ready', true );
			$hint = ensureInlineHint( $input, 'vimeo' );
			updateVimeoHint( $input, $hint );

			$input.on( 'input change', function() {
				updateVimeoHint( $input, $hint );
			} );
		} );
	}

	function addStaticHint( selector, key, message, type ) {
		$( selector ).each( function() {
			var $field = $( this );
			var $target = $field.find( '.acf-input, .ommvs-video-fallback-field__control' ).first();
			var $hint;

			if ( ! $target.length || $field.data( 'ommvs-static-hint-' + key ) ) {
				return;
			}

			$field.data( 'ommvs-static-hint-' + key, true );
			$hint = ensureInlineHint( $target, key );
			setInlineHint( $hint, type || 'neutral', message );
		} );
	}

	function initializeStaticVideoAdminHints() {
		addStaticHint(
			'.ommvs-acf-field--card-thumbnail, .ommvs-video-fallback-field--ommvs-card-thumbnail',
			'card-thumbnail',
			getAdminString( 'cardThumbnailHint', 'Frontend video cards use this Default Card Thumbnail. Native Featured Image is optional/admin-facing.' ),
			'neutral'
		);

		addStaticHint(
			'.ommvs-acf-field--related-thumbnail, .ommvs-video-fallback-field--ommvs-related-thumbnail',
			'related-thumbnail',
			getAdminString( 'relatedThumbnailHint', 'Optional. Related cards fall back to the Default Card Thumbnail when this is empty.' ),
			'neutral'
		);

		addStaticHint(
			'.ommvs-acf-field--modal-content, .ommvs-video-fallback-field--ommvs-modal-overview',
			'modal-content',
			getAdminString( 'modalContentHint', 'Use normal headings, paragraphs, links, and bullet lists. Avoid pasted Elementor markup.' ),
			'neutral'
		);
	}

	function initializeVideoAdminHints() {
		initializeHashHints();
		initializeVimeoHints();
		initializeStaticVideoAdminHints();
	}

	function setVideoCategoryStatus( $metabox, message, isError, autoClear ) {
		var $status = $metabox.find( '[data-ommvs-video-category-status]' ).first();

		if ( ! $status.length ) {
			return;
		}

		window.clearTimeout( $status.data( 'ommvs-video-category-timeout' ) );

		$status
			.toggleClass( 'is-error', !! isError )
			.text( message || '' );

		if ( autoClear && message ) {
			$status.data(
				'ommvs-video-category-timeout',
				window.setTimeout( function() {
					$status.removeClass( 'is-error' ).text( '' );
				}, 4000 )
			);
		}
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
				setVideoCategoryStatus( $metabox, message, false, true );
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

	function copyTextToClipboard( text ) {
		var deferred;
		var textarea;
		var successful;

		if ( window.navigator && window.navigator.clipboard && window.navigator.clipboard.writeText ) {
			return window.navigator.clipboard.writeText( text );
		}

		deferred = $.Deferred();
		textarea = document.createElement( 'textarea' );

		textarea.value = text;
		textarea.setAttribute( 'readonly', 'readonly' );
		textarea.style.position = 'fixed';
		textarea.style.top = '-9999px';
		textarea.style.left = '-9999px';

		document.body.appendChild( textarea );
		textarea.select();

		try {
			successful = document.execCommand( 'copy' );
		} catch ( error ) {
			successful = false;
		}

		document.body.removeChild( textarea );

		if ( successful ) {
			deferred.resolve();
		} else {
			deferred.reject();
		}

		return deferred.promise();
	}

	function setCopyHashStatus( $button, message, isError ) {
		var $status = $button.siblings( '[data-ommvs-copy-hash-status]' ).first();

		if ( ! $status.length ) {
			$status = $( '<span />', {
				'aria-live': 'polite',
				'class': 'ommvs-admin-copy-hash__status',
				'data-ommvs-copy-hash-status': ''
			} );

			$button.after( $status );
		}

		window.clearTimeout( $status.data( 'ommvs-copy-hash-timeout' ) );

		$status
			.toggleClass( 'is-error', !! isError )
			.text( message || '' );

		$status.data(
			'ommvs-copy-hash-timeout',
			window.setTimeout( function() {
				$status.removeClass( 'is-error' ).text( '' );
			}, 2200 )
		);
	}

	function copyHash( $button ) {
		var hash = String( $button.data( 'ommvs-copy-hash' ) || '' );

		if ( ! hash ) {
			return;
		}

		$.when( copyTextToClipboard( hash ) )
			.done( function() {
				setCopyHashStatus( $button, getAdminString( 'copyHashCopied', 'Copied' ), false );
			} )
			.fail( function() {
				setCopyHashStatus( $button, getAdminString( 'copyHashFailed', 'Could not copy' ), true );
			} );
	}

	$( function() {
		initializeVideoAdminHints();

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

		$( document ).on( 'click', '[data-ommvs-copy-hash]', function( event ) {
			event.preventDefault();
			copyHash( $( this ) );
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

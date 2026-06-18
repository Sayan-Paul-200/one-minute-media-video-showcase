(function() {
	'use strict';

	var pageData = null;
	var modal = {};
	var isOpen = false;
	var activeVideoId = 0;
	var previousFocus = null;
	var scrollPosition = 0;
	var originalBodyStyles = {};
	var previousScrollRestoration = null;
	var debugEnabled = isDebugEnabled();
	var performanceMarks = {};

	markPerformance( 'controller-load' );

	function onReady( callback ) {
		if ( 'loading' === document.readyState ) {
			document.addEventListener( 'DOMContentLoaded', callback );
			return;
		}

		callback();
	}

	function init() {
		markPerformance( 'init-start' );

		pageData = parsePageData();

		if ( ! pageData ) {
			return;
		}

		modal = cacheModalNodes();

		if ( ! modal.root ) {
			return;
		}

		ensureModalAccessibilityState();

		bindEvents();
		openInitialHash();
	}

	function parsePageData() {
		var dataNode = document.getElementById( 'ommvs-page-data' );
		var data = null;

		if ( ! dataNode || ! dataNode.textContent ) {
			return null;
		}

		try {
			data = JSON.parse( dataNode.textContent );
		} catch ( error ) {
			return null;
		}

		if ( ! isObject( data ) || ! isObject( data.videos ) ) {
			return null;
		}

		if ( ! isObject( data.hashMap ) ) {
			data.hashMap = {};
		}

		if ( ! isObject( data.relatedMap ) ) {
			data.relatedMap = {};
		}

		if ( ! isObject( data.settings ) ) {
			data.settings = {};
		}

		return data;
	}

	function cacheModalNodes() {
		var root = document.querySelector( '[data-ommvs-modal]' );
		var nodes = {
			root: root
		};

		if ( ! root ) {
			return nodes;
		}

		nodes.overlay = root.querySelector( '[data-ommvs-modal-overlay]' );
		nodes.close = root.querySelector( '[data-ommvs-modal-close]' );
		nodes.title = root.querySelector( '[data-ommvs-modal-title]' );
		nodes.category = root.querySelector( '[data-ommvs-modal-category]' );
		nodes.content = root.querySelector( '[data-ommvs-modal-content]' );
		nodes.cta = root.querySelector( '[data-ommvs-modal-cta]' );
		nodes.video = root.querySelector( '[data-ommvs-modal-video]' );
		nodes.related = root.querySelector( '[data-ommvs-modal-related]' );
		nodes.relatedTitle = root.querySelector( '[data-ommvs-modal-related-title]' );
		nodes.relatedWrapper = nodes.related ? nodes.related.closest( '.ommvs-modal__related' ) : null;

		return nodes;
	}

	function bindEvents() {
		document.addEventListener( 'click', handleDocumentClick );
		document.addEventListener( 'keydown', handleDocumentKeydown );
		window.addEventListener( 'popstate', handlePopState );
	}

	function handleDocumentClick( event ) {
		var closeTarget = closestElement( event.target, '[data-ommvs-modal-close], [data-ommvs-modal-overlay]' );
		var relatedCard = closestElement( event.target, '[data-ommvs-related-video-id]' );
		var videoCard = closestElement( event.target, '.ommvs-video-card' );
		var videoId = 0;

		if ( closeTarget && modal.root.contains( closeTarget ) ) {
			event.preventDefault();
			closeModal();
			return;
		}

		if ( relatedCard && modal.root.contains( relatedCard ) ) {
			videoId = parseVideoId( relatedCard.getAttribute( 'data-ommvs-related-video-id' ) );

			if ( videoId ) {
				event.preventDefault();
				openModal( videoId, {
					updateHash: true
				} );
			}

			return;
		}

		if ( ! videoCard ) {
			return;
		}

		videoId = parseVideoId( videoCard.getAttribute( 'data-video-id' ) );

		if ( ! videoId || ! getVideoById( videoId ) ) {
			return;
		}

		event.preventDefault();
		openModal( videoId, {
			trigger: videoCard,
			updateHash: true
		} );
	}

	function handleDocumentKeydown( event ) {
		if ( ! isOpen ) {
			return;
		}

		if ( 'Escape' === event.key ) {
			event.preventDefault();
			closeModal();
			return;
		}

		if ( 'Tab' === event.key ) {
			trapFocus( event );
		}
	}

	function handlePopState() {
		var videoId = getVideoIdFromHash( window.location.hash );

		if ( videoId ) {
			openModal( videoId, {
				updateHash: false,
				restoreFocus: false
			} );
			return;
		}

		if ( isOpen ) {
			closeModal( {
				updateHash: false,
				restoreFocus: false
			} );
		}
	}

	function openInitialHash() {
		var videoId = getVideoIdFromHash( window.location.hash );

		if ( ! videoId ) {
			return;
		}

		markPerformance( 'initial-hash-detected' );

		window.setTimeout( function() {
			openModal( videoId, {
				updateHash: false,
				restoreFocus: false
			} );
		}, 0 );
	}

	function openModal( videoId, options ) {
		var video = getVideoById( videoId );
		var wasOpen = isOpen;

		options = options || {};

		if ( ! video ) {
			return false;
		}

		if ( ! isOpen ) {
			previousFocus = options.trigger || document.activeElement;
			lockBodyScroll();
		}

		activeVideoId = parseVideoId( video.id );

		markPerformance( 'modal-render-start' );
		renderModal( video );

		isOpen = true;
		setModalOpenState( true );
		markPerformance( 'modal-open-state-applied' );

		if ( false !== options.updateHash ) {
			setHash( video.hash, wasOpen );
		}

		focusModal();

		return true;
	}

	function closeModal( options ) {
		options = options || {};

		if ( ! isOpen ) {
			return;
		}

		clearNode( modal.video );
		clearNode( modal.related );

		activeVideoId = 0;
		isOpen = false;
		setModalOpenState( false );

		unlockBodyScroll( options );

		if ( false !== options.updateHash ) {
			clearHash();
		}

		if ( false !== options.restoreFocus && isFocusableElement( previousFocus ) ) {
			focusElement( previousFocus );
		}

		previousFocus = null;
	}

	function renderModal( video ) {
		var settings = pageData.settings || {};
		var modalData = isObject( video.modal ) ? video.modal : {};
		var cardData = isObject( video.card ) ? video.card : {};
		var cta = isObject( settings.cta ) ? settings.cta : {};

		setText( modal.title, modalData.title || cardData.title || '' );
		setOptionalText( modal.category, getCategoryName( video ) );
		setOptionalHtml( modal.content, getModalContent( modalData ) );
		renderCta( cta );
		renderVideo( video );
		renderRelated( video.id );
	}

	function renderCta( cta ) {
		var text = isScalar( cta.text ) ? String( cta.text ).trim() : '';
		var url = isScalar( cta.url ) ? String( cta.url ).trim() : '';

		if ( ! modal.cta ) {
			return;
		}

		if ( '' === text || '' === url || ! isSafeUrl( url ) ) {
			modal.cta.hidden = true;
			modal.cta.removeAttribute( 'href' );
			modal.cta.textContent = '';
			return;
		}

		modal.cta.hidden = false;
		modal.cta.href = url;
		modal.cta.textContent = text;
	}

	function renderVideo( video ) {
		var iframe = null;

		clearNode( modal.video );

		if ( ! modal.video ) {
			return;
		}

		iframe = buildIframe( video );

		if ( iframe ) {
			modal.video.appendChild( iframe );
			markPerformance( 'iframe-appended' );
		}
	}

	function buildIframe( video ) {
		var modalData = isObject( video.modal ) ? video.modal : {};
		var vimeoId = getVimeoId( modalData );
		var title = modalData.title || ( isObject( video.card ) ? video.card.title : '' ) || 'Video';
		var src = '';
		var iframe = null;

		if ( '' !== vimeoId ) {
			src = 'https://player.vimeo.com/video/' + encodeURIComponent( vimeoId ) + '?autoplay=1&playsinline=1&autopause=0&title=0&portrait=0&byline=0';
		}

		if ( '' === src ) {
			return null;
		}

		iframe = document.createElement( 'iframe' );
		iframe.className = 'ommvs-modal__iframe';
		iframe.src = src;
		iframe.title = String( title );
		iframe.allow = 'autoplay; fullscreen; picture-in-picture';
		iframe.referrerPolicy = 'strict-origin-when-cross-origin';
		iframe.setAttribute( 'data-ommvs-video-provider', 'vimeo' );
		iframe.setAttribute( 'frameborder', '0' );

		return iframe;
	}

	function renderRelated( videoId ) {
		var relatedIds = getRelatedIds( videoId );

		clearNode( modal.related );

		if ( modal.relatedWrapper ) {
			modal.relatedWrapper.hidden = 0 === relatedIds.length;
		}

		if ( modal.relatedTitle ) {
			modal.relatedTitle.hidden = 0 === relatedIds.length;
		}

		if ( ! modal.related ) {
			return;
		}

		relatedIds.forEach( function( relatedId ) {
			var relatedVideo = getVideoById( relatedId );
			var relatedCard = null;

			if ( ! relatedVideo ) {
				return;
			}

			relatedCard = buildRelatedCard( relatedVideo );

			if ( relatedCard ) {
				modal.related.appendChild( relatedCard );
			}
		} );
	}

	function buildRelatedCard( video ) {
		var thumbnail = getRelatedThumbnail( video );
		var button = document.createElement( 'button' );
		var media = document.createElement( 'span' );
		var body = document.createElement( 'span' );
		var title = document.createElement( 'span' );
		var category = document.createElement( 'span' );
		var image = null;
		var videoId = parseVideoId( video.id );
		var videoHash = isScalar( video.hash ) ? normalizeHash( video.hash ) : '';
		var videoTitle = getRelatedTitle( video );
		var categoryName = getCategoryName( video );

		if ( ! videoId ) {
			return null;
		}

		button.type = 'button';
		button.className = 'ommvs-modal__related-card';
		button.setAttribute( 'data-ommvs-related-video-id', String( videoId ) );
		button.setAttribute( 'aria-label', 'Open ' + videoTitle );

		if ( '' !== videoHash ) {
			button.setAttribute( 'data-ommvs-related-video-hash', videoHash );
		}

		media.className = 'ommvs-modal__related-card-media';

		if ( thumbnail.url ) {
			image = document.createElement( 'img' );
			image.className = 'ommvs-modal__related-card-image';
			image.src = thumbnail.url;
			image.alt = thumbnail.alt || videoTitle;
			image.loading = 'lazy';
			media.appendChild( image );
		}

		body.className = 'ommvs-modal__related-card-body';
		title.className = 'ommvs-modal__related-card-title';
		title.textContent = videoTitle;

		body.appendChild( title );

		if ( '' !== categoryName ) {
			category.className = 'ommvs-modal__related-card-category';
			category.textContent = categoryName;
			body.appendChild( category );
		}

		button.appendChild( media );
		button.appendChild( body );

		return button;
	}

	function getRelatedTitle( video ) {
		var modalData = isObject( video.modal ) ? video.modal : {};
		var cardData = isObject( video.card ) ? video.card : {};

		if ( isScalar( modalData.title ) && '' !== String( modalData.title ).trim() ) {
			return String( modalData.title ).trim();
		}

		if ( isScalar( cardData.title ) && '' !== String( cardData.title ).trim() ) {
			return String( cardData.title ).trim();
		}

		return 'Video';
	}

	function getCategoryName( video ) {
		var category = isObject( video.category ) ? video.category : {};

		return isScalar( category.name ) ? String( category.name ).trim() : '';
	}

	function getModalContent( modalData ) {
		if ( isScalar( modalData.content ) && '' !== String( modalData.content ).trim() ) {
			return String( modalData.content );
		}

		if ( isScalar( modalData.overview ) && '' !== String( modalData.overview ).trim() ) {
			return String( modalData.overview );
		}

		return '';
	}

	function getVimeoId( modalData ) {
		var vimeoId = isScalar( modalData.vimeoId ) ? String( modalData.vimeoId ).trim() : '';

		if ( /^\d+$/.test( vimeoId ) ) {
			return vimeoId;
		}

		return getVimeoIdFromUrl( modalData.vimeoUrl );
	}

	function getVimeoIdFromUrl( url ) {
		var parser = null;
		var host = '';
		var segments = [];
		var videoIndex = -1;

		if ( ! isScalar( url ) || '' === String( url ).trim() ) {
			return '';
		}

		try {
			parser = new URL( String( url ).trim() );
		} catch ( error ) {
			return '';
		}

		host = parser.hostname.toLowerCase();

		if ( -1 === [ 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ].indexOf( host ) ) {
			return '';
		}

		segments = parser.pathname.split( '/' ).filter( Boolean );

		if ( 'player.vimeo.com' === host ) {
			videoIndex = segments.indexOf( 'video' );

			if ( videoIndex >= 0 && segments[ videoIndex + 1 ] && /^\d+$/.test( segments[ videoIndex + 1 ] ) ) {
				return segments[ videoIndex + 1 ];
			}
		}

		for ( var index = 0; index < segments.length; index++ ) {
			if ( /^\d+$/.test( segments[ index ] ) ) {
				return segments[ index ];
			}
		}

		return '';
	}

	function getRelatedThumbnail( video ) {
		var relatedCard = isObject( video.relatedCard ) ? video.relatedCard : {};
		var cardData = isObject( video.card ) ? video.card : {};
		var settings = isObject( pageData.settings ) ? pageData.settings : {};

		if ( isObject( relatedCard.thumbnail ) && relatedCard.thumbnail.url ) {
			return relatedCard.thumbnail;
		}

		if ( isObject( cardData.thumbnail ) && cardData.thumbnail.url ) {
			return cardData.thumbnail;
		}

		if ( isObject( settings.modalFallbackThumbnail ) && settings.modalFallbackThumbnail.url ) {
			return settings.modalFallbackThumbnail;
		}

		return {};
	}

	function getRelatedIds( videoId ) {
		var relatedIds = [];
		var key = String( parseVideoId( videoId ) );

		if ( pageData.relatedMap && Array.isArray( pageData.relatedMap[ key ] ) ) {
			relatedIds = pageData.relatedMap[ key ];
		}

		return relatedIds.map( parseVideoId ).filter( function( relatedId ) {
			return relatedId && relatedId !== activeVideoId && !! getVideoById( relatedId );
		} ).slice( 0, 3 );
	}

	function getVideoById( videoId ) {
		var key = String( parseVideoId( videoId ) );

		if ( ! key || ! pageData.videos[ key ] || ! isObject( pageData.videos[ key ] ) ) {
			return null;
		}

		return pageData.videos[ key ];
	}

	function getVideoIdFromHash( hash ) {
		var normalizedHash = normalizeHash( hash );
		var videoId = 0;

		if ( '' === normalizedHash || ! pageData.hashMap || ! Object.prototype.hasOwnProperty.call( pageData.hashMap, normalizedHash ) ) {
			return 0;
		}

		videoId = parseVideoId( pageData.hashMap[ normalizedHash ] );

		return getVideoById( videoId ) ? videoId : 0;
	}

	function normalizeHash( hash ) {
		var normalized = isScalar( hash ) ? String( hash ).trim() : '';

		if ( '#' === normalized.charAt( 0 ) ) {
			normalized = normalized.substring( 1 );
		}

		try {
			normalized = decodeURIComponent( normalized );
		} catch ( error ) {
			return '';
		}

		return normalized;
	}

	function setHash( hash, replaceExisting ) {
		var normalizedHash = normalizeHash( hash );
		var url = '';

		if ( '' === normalizedHash || ! window.history || ! window.history.pushState ) {
			return;
		}

		if ( normalizeHash( window.location.hash ) === normalizedHash ) {
			return;
		}

		url = window.location.pathname + window.location.search + '#' + encodeURIComponent( normalizedHash );

		if ( replaceExisting && window.history.replaceState ) {
			// Related-video switches happen inside one modal session, so replace the active hash entry.
			window.history.replaceState( {
				ommvsVideoId: activeVideoId
			}, '', url );
			return;
		}

		// Opening from a card creates a browser-history entry that Back can close through popstate.
		window.history.pushState( {
			ommvsVideoId: activeVideoId
		}, '', url );
	}

	function clearHash() {
		if ( ! window.history || ! window.history.replaceState ) {
			return;
		}

		window.history.replaceState( {}, '', window.location.pathname + window.location.search );
	}

	function lockBodyScroll() {
		scrollPosition = window.pageYOffset || document.documentElement.scrollTop || 0;
		originalBodyStyles = {
			overflow: document.body.style.overflow,
			position: document.body.style.position,
			top: document.body.style.top,
			width: document.body.style.width
		};

		setManualScrollRestoration();

		document.body.style.overflow = 'hidden';
		document.body.style.position = 'fixed';
		document.body.style.top = '-' + scrollPosition + 'px';
		document.body.style.width = '100%';
	}

	function unlockBodyScroll( options ) {
		options = options || {};

		document.body.style.overflow = originalBodyStyles.overflow || '';
		document.body.style.position = originalBodyStyles.position || '';
		document.body.style.top = originalBodyStyles.top || '';
		document.body.style.width = originalBodyStyles.width || '';

		if ( false !== options.restoreScroll ) {
			window.scrollTo( 0, scrollPosition );
		}

		restoreScrollRestoration();
	}

	function setManualScrollRestoration() {
		if ( ! window.history || ! ( 'scrollRestoration' in window.history ) ) {
			return;
		}

		previousScrollRestoration = window.history.scrollRestoration;
		window.history.scrollRestoration = 'manual';
	}

	function restoreScrollRestoration() {
		if ( null === previousScrollRestoration || ! window.history || ! ( 'scrollRestoration' in window.history ) ) {
			return;
		}

		window.history.scrollRestoration = previousScrollRestoration;
		previousScrollRestoration = null;
	}

	function ensureModalAccessibilityState() {
		modal.root.setAttribute( 'role', 'dialog' );
		modal.root.setAttribute( 'aria-modal', 'true' );

		if ( ! modal.root.hasAttribute( 'aria-labelledby' ) && modal.title && modal.title.id ) {
			modal.root.setAttribute( 'aria-labelledby', modal.title.id );
		}

		if ( ! modal.root.hasAttribute( 'tabindex' ) ) {
			modal.root.setAttribute( 'tabindex', '-1' );
		}

		setModalOpenState( false );
	}

	function setModalOpenState( open ) {
		modal.root.hidden = ! open;
		modal.root.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
		document.documentElement.classList.toggle( 'ommvs-modal-is-open', open );
		document.body.classList.toggle( 'ommvs-modal-is-open', open );
	}

	function focusModal() {
		var target = modal.close || modal.root;

		focusElement( target );
	}

	function trapFocus( event ) {
		var focusable = getFocusableModalElements();
		var first = focusable[0];
		var last = focusable[ focusable.length - 1 ];

		if ( ! first || ! last ) {
			event.preventDefault();
			focusModal();
			return;
		}

		if ( ! modal.root.contains( document.activeElement ) ) {
			event.preventDefault();
			focusElement( event.shiftKey ? last : first );
			return;
		}

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			focusElement( last );
			return;
		}

		if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			focusElement( first );
		}
	}

	function getFocusableModalElements() {
		var selector = [
			'a[href]',
			'button:not([disabled])',
			'input:not([disabled])',
			'select:not([disabled])',
			'textarea:not([disabled])',
			'[tabindex]:not([tabindex="-1"])'
		].join( ',' );

		return Array.prototype.slice.call( modal.root.querySelectorAll( selector ) ).filter( function( element ) {
			return isFocusableElement( element );
		} );
	}

	function closestElement( target, selector ) {
		if ( ! target ) {
			return null;
		}

		if ( 1 !== target.nodeType ) {
			target = target.parentElement;
		}

		if ( ! target || 'function' !== typeof target.closest ) {
			return null;
		}

		return target.closest( selector );
	}

	function focusElement( element ) {
		if ( ! isFocusableElement( element ) ) {
			return;
		}

		try {
			element.focus( {
				preventScroll: true
			} );
		} catch ( error ) {
			element.focus();
		}
	}

	function isFocusableElement( element ) {
		if ( ! element || 'function' !== typeof element.focus ) {
			return false;
		}

		if ( ! document.documentElement.contains( element ) ) {
			return false;
		}

		if ( element.hidden || element.closest( '[hidden]' ) ) {
			return false;
		}

		if ( element.disabled ) {
			return false;
		}

		return true;
	}

	function setText( node, value ) {
		if ( node ) {
			node.textContent = isScalar( value ) ? String( value ) : '';
		}
	}

	function setOptionalText( node, value ) {
		var text = isScalar( value ) ? String( value ).trim() : '';

		if ( ! node ) {
			return;
		}

		node.hidden = '' === text;
		node.textContent = text;
	}

	function setOptionalHtml( node, value ) {
		var html = isScalar( value ) ? String( value ) : '';

		if ( ! node ) {
			return;
		}

		node.hidden = '' === html.trim();
		node.innerHTML = html;
	}

	function clearNode( node ) {
		if ( ! node ) {
			return;
		}

		while ( node.firstChild ) {
			node.removeChild( node.firstChild );
		}
	}

	function parseVideoId( value ) {
		var videoId = parseInt( value, 10 );

		return isNaN( videoId ) || videoId <= 0 ? 0 : videoId;
	}

	function isSafeUrl( url ) {
		var parser = null;

		if ( ! isScalar( url ) || '' === String( url ).trim() ) {
			return false;
		}

		try {
			parser = new URL( String( url ), window.location.origin );
		} catch ( error ) {
			return false;
		}

		return 'http:' === parser.protocol || 'https:' === parser.protocol;
	}

	function isObject( value ) {
		return !! value && 'object' === typeof value && ! Array.isArray( value );
	}

	function isScalar( value ) {
		return 'string' === typeof value || 'number' === typeof value || 'boolean' === typeof value;
	}

	function isDebugEnabled() {
		if ( true === window.ommvsDebug ) {
			return true;
		}

		if ( ! window.location || ! window.location.search ) {
			return false;
		}

		try {
			return '1' === new URLSearchParams( window.location.search ).get( 'ommvs_debug' );
		} catch ( error ) {
			return /(?:^\?|&)ommvs_debug=1(?:&|$)/.test( window.location.search );
		}
	}

	function getPerformanceNow() {
		if ( window.performance && 'function' === typeof window.performance.now ) {
			return window.performance.now();
		}

		return Date.now();
	}

	function markPerformance( name ) {
		var time = 0;
		var delta = 0;

		if ( ! debugEnabled ) {
			return;
		}

		time = getPerformanceNow();
		performanceMarks[ name ] = time;
		delta = performanceMarks[ 'controller-load' ] ? Math.round( time - performanceMarks[ 'controller-load' ] ) : 0;

		if ( window.performance && 'function' === typeof window.performance.mark ) {
			try {
				window.performance.mark( 'ommvs:' + name );
			} catch ( error ) {
				// Browser performance marks are diagnostic only.
			}
		}

		if ( window.console && 'function' === typeof window.console.debug ) {
			window.console.debug( '[OMMVS]', name, delta + 'ms' );
		}
	}

	onReady( init );

}());

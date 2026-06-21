/* ===========================================================
   Liel Video Hero — handle: liel-video-hero
   Single-video hero. No carousel / Swiper required.

   End-of-video handling per the widget's `End Behavior` control:
     - 'fallback' (DEFAULT): when the video duration elapses, add
       .liel-video-hero--ended on the wrapper. The CSS fades the
       iframe out so the poster image underneath is shown.
     - 'reload': just before the video ends, force-reload the iframe
       src with a cache-buster query param so it restarts. This is a
       best-effort loop fallback for providers (BunnyCDN) whose
       loop=true URL param is unreliable.
     - 'none': do nothing — rely entirely on the provider's loop.

   Requires data-duration > 0 on the iframe (set via widget control).
   =========================================================== */
( function () {
  'use strict';

  var IFRAME_SEL = '.liel-video-hero__video--iframe';

  function reloadIframeCacheBust( iframe ) {
    var src = iframe.getAttribute( 'src' ) || '';
    if ( ! src || src === 'about:blank' ) { return; }
    var sep = src.indexOf( '?' ) >= 0 ? '&' : '?';
    // Strip a prior _t param so they don't pile up
    var clean = src.replace( /([?&])_t=\d+(&|$)/, function ( _m, pre, post ) {
      return post === '&' ? pre : '';
    } ).replace( /[?&]$/, '' );
    var newSrc = clean + ( clean.indexOf( '?' ) >= 0 ? '&' : '?' ) + '_t=' + Date.now();
    iframe.setAttribute( 'src', newSrc );
  }

  function scheduleEndBehavior( iframe ) {
    if ( iframe.dataset.lielScheduled === '1' ) { return; }
    iframe.dataset.lielScheduled = '1';

    var duration = parseInt( iframe.getAttribute( 'data-duration' ), 10 ) || 0;
    var behavior = iframe.getAttribute( 'data-end-behavior' ) || 'fallback';
    if ( duration < 2 || behavior === 'none' ) { return; }

    var wrap = iframe.closest( '.liel-video-hero' );
    if ( ! wrap ) { return; }

    if ( behavior === 'fallback' ) {
      // Fade iframe out at the end -> poster image shows.
      setTimeout( function () {
        wrap.classList.add( 'liel-video-hero--ended' );
      }, duration * 1000 );
      return;
    }

    if ( behavior === 'reload' ) {
      // Briefly fade ~0.5s before the end, reload (cache-bust), fade back in.
      var lead = duration > 4 ? 1 : 0;
      setInterval( function () {
        wrap.classList.add( 'liel-video-hero--ended' );
        reloadIframeCacheBust( iframe );
        setTimeout( function () {
          wrap.classList.remove( 'liel-video-hero--ended' );
        }, 1500 );
      }, ( duration - lead ) * 1000 );
    }
  }

  function scanAndSchedule() {
    document.querySelectorAll( IFRAME_SEL ).forEach( scheduleEndBehavior );
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', scanAndSchedule );
  } else {
    scanAndSchedule();
  }
  // Re-scan for late-mounted widgets (Elementor editor re-renders, etc.)
  window.setTimeout( scanAndSchedule, 1000 );
  window.setTimeout( scanAndSchedule, 3000 );
}() );

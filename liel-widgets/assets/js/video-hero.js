/* ===========================================================
   Liel Video Hero — handle: liel-video-hero
   Single-video hero. No carousel/Swiper required.
   Adds an iframe loop fallback in case the provider's loop=true
   URL param is ignored (BunnyCDN, etc.) — on an "ended"-style
   postMessage we reload the iframe src to restart playback;
   autoplay=true in the URL kicks it off again.
   =========================================================== */
( function () {
  'use strict';

  window.addEventListener( 'message', function ( ev ) {
    var data = ev.data;
    var name = '';
    if ( typeof data === 'string' ) {
      name = data;
    } else if ( data && typeof data === 'object' ) {
      name = ( data.event || data.type || data.name || '' ).toString();
    }
    name = name.toLowerCase();
    if ( name !== 'ended' && name !== 'end' && name !== 'finished' && name !== 'video:ended' && name !== 'complete' ) {
      return;
    }
    document.querySelectorAll( '.liel-video-hero__video--iframe' ).forEach( function ( iframe ) {
      if ( iframe.contentWindow === ev.source ) {
        var src = iframe.src;
        iframe.src = 'about:blank';
        setTimeout( function () { iframe.src = src; }, 50 );
      }
    } );
  } );
}() );

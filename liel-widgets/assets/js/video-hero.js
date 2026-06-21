/* ===========================================================
   Liel Video Hero — handle: liel-video-hero
   Single-video hero. No carousel/Swiper required.

   BunnyCDN's iframe player inconsistently honors the loop=true
   URL param AND doesn't reliably send an "ended" postMessage.
   This script applies three layers of loop insurance:

     1. listens for any "ended"-style postMessage and restarts
        the iframe by reloading its src;
     2. periodically sends "play" commands to BunnyCDN iframes
        in multiple known formats (BunnyCDN accepts several);
     3. as a last-resort heartbeat, if no playback signal arrives
        for `STALL_MS` it reloads the iframe.
   =========================================================== */
( function () {
  'use strict';

  var IFRAME_SEL  = '.liel-video-hero__video--iframe';
  var POKE_MS     = 30000;   // 30s — re-send "play" command
  var STALL_MS    = 120000;  // 2 min — reload iframe if no signal

  var lastSignal = new WeakMap();

  function reloadIframe( iframe ) {
    var src = iframe.src;
    iframe.src = 'about:blank';
    setTimeout( function () { iframe.src = src; }, 50 );
  }

  function sendPlay( iframe ) {
    if ( ! iframe.contentWindow ) { return; }
    var formats = [
      { event:   'play' },
      { command: 'play' },
      { action:  'play' },
      { method:  'play' },
      'play'
    ];
    formats.forEach( function ( msg ) {
      try { iframe.contentWindow.postMessage( msg, '*' ); } catch ( e ) {}
    } );
  }

  // --- 1 + 3: listen for any message from our iframes, detect "ended" / "stall"
  window.addEventListener( 'message', function ( ev ) {
    document.querySelectorAll( IFRAME_SEL ).forEach( function ( iframe ) {
      if ( iframe.contentWindow !== ev.source ) { return; }

      lastSignal.set( iframe, Date.now() );

      var data = ev.data;
      var name = '';
      if ( typeof data === 'string' ) {
        name = data;
      } else if ( data && typeof data === 'object' ) {
        name = ( data.event || data.type || data.name || data.action || '' ).toString();
      }
      name = name.toLowerCase();
      if ( name === 'ended' || name === 'end' || name === 'finished' || name === 'video:ended' || name === 'complete' ) {
        reloadIframe( iframe );
      }
    } );
  } );

  // --- 2: periodic poke + heartbeat sweep
  setInterval( function () {
    var now = Date.now();
    document.querySelectorAll( IFRAME_SEL ).forEach( function ( iframe ) {
      sendPlay( iframe );

      var last = lastSignal.get( iframe );
      if ( last && ( now - last ) > STALL_MS ) {
        // No signal for STALL_MS — iframe probably parked at end. Reload.
        lastSignal.set( iframe, now );
        reloadIframe( iframe );
      }
    } );
  }, POKE_MS );

  // Seed lastSignal so the stall timer doesn't fire on initial load
  document.addEventListener( 'DOMContentLoaded', function () {
    document.querySelectorAll( IFRAME_SEL ).forEach( function ( iframe ) {
      lastSignal.set( iframe, Date.now() );
    } );
  } );
}() );

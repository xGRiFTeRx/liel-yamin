/* ===========================================================
   Liel Video Hero — handle: liel-video-hero
   Single-video hero. No carousel/Swiper required.

   Loop strategy for iframe videos (BunnyCDN especially):
     PRIMARY (deterministic):
       If the widget's Video Duration > 0, schedule a setInterval that
       reloads the iframe src every (duration - 1) seconds. The video
       restarts via the autoplay=true URL param. This is the bulletproof
       path — it works regardless of whether the provider honors loop=true.

     FALLBACK (best-effort, used when duration = 0):
       - Listen for any "ended"-style postMessage and reload iframe src.
       - Periodically poke the iframe with "play" commands in several
         formats (BunnyCDN/Vimeo/YouTube accept different shapes).
       - Heartbeat: if no message from a player for 2 min, reload.
   =========================================================== */
( function () {
  'use strict';

  var IFRAME_SEL  = '.liel-video-hero__video--iframe';
  var POKE_MS     = 30000;   // 30s — re-send "play" command (fallback path)
  var STALL_MS    = 120000;  // 2 min — reload iframe if no signal (fallback path)

  var lastSignal = new WeakMap();
  var scheduled  = new WeakMap(); // marks iframes that already have a precise-timer

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

  // --- PRIMARY: deterministic reload from data-duration ---
  function schedulePreciseLoop( iframe ) {
    if ( scheduled.get( iframe ) ) { return; }
    var loop     = iframe.getAttribute( 'data-loop' ) === '1';
    var duration = parseInt( iframe.getAttribute( 'data-duration' ), 10 ) || 0;
    if ( ! loop || duration < 2 ) { return; }
    var lead = duration > 4 ? 1 : 0; // reload 1s early so the cut is hidden by autoplay
    var interval = ( duration - lead ) * 1000;
    setInterval( function () { reloadIframe( iframe ); }, interval );
    scheduled.set( iframe, true );
  }

  function scanAndSchedule() {
    document.querySelectorAll( IFRAME_SEL ).forEach( schedulePreciseLoop );
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', scanAndSchedule );
  } else {
    scanAndSchedule();
  }
  // Re-scan after Elementor editor re-renders or late-mounted widgets
  window.setTimeout( scanAndSchedule, 1000 );
  window.setTimeout( scanAndSchedule, 3000 );

  // --- FALLBACK: listen for any message from our iframes, detect end/stall ---
  window.addEventListener( 'message', function ( ev ) {
    document.querySelectorAll( IFRAME_SEL ).forEach( function ( iframe ) {
      if ( iframe.contentWindow !== ev.source ) { return; }

      lastSignal.set( iframe, Date.now() );

      // Skip end-detection if precise timer is in charge
      if ( scheduled.get( iframe ) ) { return; }

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

  // --- FALLBACK: periodic poke + heartbeat sweep (only for iframes without precise timer) ---
  setInterval( function () {
    var now = Date.now();
    document.querySelectorAll( IFRAME_SEL ).forEach( function ( iframe ) {
      if ( scheduled.get( iframe ) ) { return; }
      sendPlay( iframe );
      var last = lastSignal.get( iframe );
      if ( last && ( now - last ) > STALL_MS ) {
        lastSignal.set( iframe, now );
        reloadIframe( iframe );
      }
    } );
  }, POKE_MS );
}() );

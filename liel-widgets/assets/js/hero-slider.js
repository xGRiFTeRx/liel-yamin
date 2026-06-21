/* ===========================================================
   Liel Hero Slider — handle: liel-hero-slider
   Per-widget JS (manually registered in Liel_Plugin::register_scripts).
   Initializes Swiper for each .liel-hero on the page and on Elementor
   editor preview re-renders.
   =========================================================== */
( function ( $ ) {
  'use strict';

  function initHeroSlider( $scope ) {
    var el = $scope.find( '.liel-hero' ).get( 0 );
    if ( ! el || typeof Swiper === 'undefined' ) {
      return;
    }

    var cfg = {};
    try {
      cfg = JSON.parse( el.getAttribute( 'data-liel-hero' ) ) || {};
    } catch ( e ) {
      cfg = {};
    }

    var options = {
      slidesPerView : 1,
      loop          : !! cfg.loop,
      speed         : cfg.speed || 800,
      grabCursor    : true
    };

    if ( cfg.autoplay ) {
      options.autoplay = { delay: cfg.delay || 5000, disableOnInteraction: false };
    }
    if ( cfg.dots ) {
      options.pagination = { el: el.querySelector( '.swiper-pagination' ), clickable: true };
    }
    if ( cfg.arrows ) {
      options.navigation = {
        nextEl: el.querySelector( '.liel-hero__arrow--next' ),
        prevEl: el.querySelector( '.liel-hero__arrow--prev' )
      };
    }

    // Pause non-active videos so only the visible slide plays
    options.on = {
      slideChange: function () {
        el.querySelectorAll( 'video' ).forEach( function ( v ) { v.pause(); } );
        var active = el.querySelector( '.swiper-slide-active video' );
        if ( active ) { active.play().catch( function () {} ); }
      }
    };

    // Re-init cleanly inside the Elementor editor
    if ( el.swiper ) {
      try { el.swiper.destroy( true, true ); } catch ( e ) {}
    }

    new Swiper( el, options );
  }

  $( window ).on( 'elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/liel-hero-slider.default',
      initHeroSlider
    );
  } );

}( jQuery ) );

/* ===========================================================
   Liel Bridal Widgets — shared frontend JS
   Per-section behaviour (carousels, etc.) added as we build.
   =========================================================== */
( function ( $ ) {
  'use strict';

  /* ---------------------------------------------------------
     Hero Slider  (widget: liel_hero_slider)
     --------------------------------------------------------- */
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
      slidesPerView: 1,
      loop: !! cfg.loop,
      speed: cfg.speed || 800,
      grabCursor: true
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

    // Re-init cleanly inside the Elementor editor.
    if ( el.swiper ) {
      try { el.swiper.destroy( true, true ); } catch ( e ) {}
    }

    new Swiper( el, options );
  }

  $( window ).on( 'elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/liel_hero_slider.default',
      initHeroSlider
    );
  } );

}( jQuery ) );

(function ($) {
    "use strict";

    // People can ask their phone/computer for less motion (accessibility setting)
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;


    // Old full-screen spinner (removed from the pages; kept harmless if one is left)
    $('#spinner').removeClass('show');


    // Scroll reveal. Anything already on screen when this script runs is
    // left alone — otherwise WOW would hide it and fade it in again (a flash).
    // Only sections further down animate as you scroll to them.
    var viewportBottom = window.innerHeight || document.documentElement.clientHeight;
    $('.wow').each(function () {
        if (reduceMotion || this.getBoundingClientRect().top < viewportBottom) {
            $(this).removeClass('wow');
        }
    });

    // Shorter delays so content never waits long to appear
    if (!reduceMotion && typeof WOW === 'function') {
        $('.wow[data-wow-delay]').each(function () {
            var d = parseFloat($(this).attr('data-wow-delay')) || 0;
            $(this).attr('data-wow-delay', Math.min(d * 0.5, 0.3).toFixed(2) + 's');
        });
        new WOW({ offset: 40, mobile: true }).init();
    }


    // Navbar shadow + back-to-top button, checked at most once per frame
    var $nav = $('.navbar.fixed-top');
    var $toTop = $('.back-to-top');
    var ticking = false;
    function onScroll() {
        var y = window.pageYOffset || document.documentElement.scrollTop;
        $nav.toggleClass('is-scrolled', y > 20);
        $toTop.toggleClass('is-visible', y > 300);
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(onScroll);
        }
    }, { passive: true });
    onScroll();

    $toTop.on('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });


    // Hero slider: no auto-play for reduced motion
    var heroEl = document.getElementById('header-carousel');
    if (heroEl && window.bootstrap && reduceMotion) {
        // (Bootstrap 5.0 has no getOrCreateInstance)
        var hero = bootstrap.Carousel.getInstance(heroEl) || new bootstrap.Carousel(heroEl, { interval: false });
        hero.pause();
        heroEl.removeAttribute('data-bs-ride');
    }


    // Facts counter (numbers already show the final value without it)
    if (!reduceMotion && $.fn.counterUp) {
        $('[data-toggle="counter-up"]').counterUp({
            delay: 10,
            time: 1500
        });
    }


    // Portfolio isotope and filter
    if ($.fn.isotope) {
        var portfolioIsotope = $('.portfolio-container').isotope({
            itemSelector: '.portfolio-item',
            layoutMode: 'fitRows',
            transitionDuration: reduceMotion ? 0 : '0.45s'
        });
        // Images load lazily, so re-arrange the grid each time one finishes
        $('.portfolio-container img').on('load', function () {
            portfolioIsotope.isotope('layout');
        });
        $('#portfolio-flters li').on('click', function () {
            $("#portfolio-flters li").removeClass('active');
            $(this).addClass('active');

            portfolioIsotope.isotope({filter: $(this).data('filter')});
        });
    }


    // Testimonials carousel: calmer timing, pauses while you read
    if ($.fn.owlCarousel) {
        $(".testimonial-carousel").owlCarousel({
            autoplay: !reduceMotion,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            smartSpeed: reduceMotion ? 0 : 700,
            items: 1,
            dots: false,
            loop: true,
            nav: true,
            navText : [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ]
        });
    }

})(jQuery);

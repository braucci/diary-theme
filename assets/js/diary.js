/**
 * Diary — interazioni front-end
 */
(function () {
    'use strict';

    // Toggle menu mobile
    var nav = document.getElementById('site-navigation');
    if (nav) {
        var toggle = nav.querySelector('.menu-toggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var expanded = nav.classList.toggle('toggled');
                toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            });
        }
    }

    // Back to top
    var btn = document.getElementById('backToTop');
    if (btn) {
        window.addEventListener('scroll', function () {
            btn.style.display = (window.scrollY > 600) ? 'block' : 'none';
        });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();

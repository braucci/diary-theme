/**
 * Diary — interazioni front-end (progressive enhancement)
 * Il menu mobile funziona SENZA questo script (checkbox-hack puro CSS).
 * Qui aggiungiamo solo migliorie opzionali.
 */
(function () {
    'use strict';

    // Chiude il menu mobile dopo aver toccato una voce
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.main-navigation a');
        if (link) {
            var cb = document.getElementById('diary-menu-checkbox');
            if (cb) cb.checked = false;
        }
    });

    // Back to top
    function toggleBackToTop() {
        var btn = document.getElementById('backToTop');
        if (btn) btn.style.display = (window.scrollY > 600) ? 'block' : 'none';
    }
    window.addEventListener('scroll', toggleBackToTop, { passive: true });
    document.addEventListener('DOMContentLoaded', toggleBackToTop);

    document.addEventListener('click', function (e) {
        if (e.target.closest('#backToTop')) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
})();

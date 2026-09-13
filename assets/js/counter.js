/**
 * Diary — contatore visite
 * Registra la visita via AJAX (non intercettato dalla cache)
 * e scrive i numeri nel contatore in fondo alla home.
 */
(function () {
    'use strict';

    var cfg = window.DiaryCounter || {};
    if (!cfg.ajaxUrl) return;

    /* Conta una sola visita per sessione di navigazione:
       ricaricare la stessa pagina non gonfia il contatore. */
    var already = false;
    try {
        already = sessionStorage.getItem('diaryVisitCounted') === '1';
    } catch (e) { /* sessionStorage non disponibile: conta comunque */ }

    var action = already ? 'diary_counts' : 'diary_hit';

    function render(data) {
        var elTotal = document.getElementById('diary-visits-total');
        var elToday = document.getElementById('diary-visits-today');
        var wrap    = document.getElementById('diary-visit-counter');
        if (!data) return;
        if (elTotal) elTotal.textContent = formatNumber(data.total);
        if (elToday) elToday.textContent = formatNumber(data.today);
        if (wrap) wrap.classList.add('is-loaded');
    }

    function formatNumber(n) {
        return Number(n || 0).toLocaleString('it-IT');
    }

    fetch(cfg.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: 'action=' + encodeURIComponent(action)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
        if (res && res.success) {
            render(res.data);
            if (!already) {
                try { sessionStorage.setItem('diaryVisitCounted', '1'); } catch (e) {}
            }
        }
    })
    .catch(function () { /* silenzio: il contatore è un extra, non blocca nulla */ });
})();

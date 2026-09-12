/**
 * Diary — Planner: note personali su schermata post-it dedicata.
 * Il post-it si apre sopra il calendario (non dentro la cella),
 * quindi è sempre leggibile e non viene tagliato.
 */
(function () {
    'use strict';

    var cfg     = window.DiaryPlanner || {};
    var canEdit = String(cfg.canEdit) === '1';

    var screenEl, sheetEl, dateEl, viewEl, textEl, editorEl, taEl;
    var currentCell = null;

    function init() {
        screenEl = document.getElementById('diary-postit-screen');
        if (!screenEl) return;
        sheetEl  = screenEl.querySelector('.postit-sheet');
        dateEl   = screenEl.querySelector('.postit-date');
        viewEl   = screenEl.querySelector('.postit-view');
        textEl   = screenEl.querySelector('.postit-text');
        editorEl = screenEl.querySelector('.postit-editor');
        taEl     = screenEl.querySelector('.postit-textarea');
    }

    function openScreen(cell, editMode) {
        if (!screenEl) return;
        currentCell = cell;

        var note  = cell.getAttribute('data-note') || '';
        var label = cell.getAttribute('data-label') || '';

        dateEl.textContent = label;
        textEl.innerHTML   = note ? escapeHtml(note).replace(/\n/g, '<br>') : '';
        if (taEl) taEl.value = note;

        // Se non c'è nota e posso editare, apro direttamente in scrittura
        var wantEdit = editMode || (canEdit && !note);

        if (wantEdit && canEdit) {
            viewEl.setAttribute('hidden', '');
            editorEl.removeAttribute('hidden');
        } else {
            editorEl && editorEl.setAttribute('hidden', '');
            viewEl.removeAttribute('hidden');
        }

        screenEl.removeAttribute('hidden');
        document.body.classList.add('postit-open');
        requestAnimationFrame(function () {
            screenEl.classList.add('is-open');
            if (wantEdit && taEl) taEl.focus();
        });
    }

    function closeScreen() {
        if (!screenEl) return;
        screenEl.classList.remove('is-open');
        document.body.classList.remove('postit-open');
        setTimeout(function () { screenEl.setAttribute('hidden', ''); }, 180);
        currentCell = null;
    }

    function ajax(action, data, done) {
        var body = 'action=' + encodeURIComponent(action) +
                   '&nonce=' + encodeURIComponent(cfg.nonce);
        Object.keys(data).forEach(function (k) {
            body += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
        });
        fetch(cfg.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            credentials: 'same-origin',
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(done)
        .catch(function () { done({ success: false }); });
    }

    function save() {
        if (!currentCell || !taEl) return;
        var date = currentCell.getAttribute('data-date');
        var note = taEl.value;
        ajax('diary_save_note', { date: date, note: note }, function (res) {
            if (res && res.success) {
                var d = res.data || {};
                updateCell(currentCell, d.note || '', d.preview || '');
                if (d.note) {
                    textEl.innerHTML = escapeHtml(d.note).replace(/\n/g, '<br>');
                    editorEl.setAttribute('hidden', '');
                    viewEl.removeAttribute('hidden');
                } else {
                    closeScreen();
                }
            } else {
                alert('Non è stato possibile salvare la nota.');
            }
        });
    }

    function remove() {
        if (!currentCell) return;
        if (!confirm('Eliminare questa nota?')) return;
        var date = currentCell.getAttribute('data-date');
        ajax('diary_delete_note', { date: date }, function (res) {
            if (res && res.success) {
                updateCell(currentCell, '', '');
                closeScreen();
            } else {
                alert('Non è stato possibile eliminare la nota.');
            }
        });
    }

    function updateCell(cell, note, preview) {
        var item   = cell.querySelector('.planner-note-item');
        var prevEl = cell.querySelector('.planner-note-preview');
        cell.setAttribute('data-note', note);
        if (note) {
            cell.classList.add('planner-ha-nota');
            if (prevEl) prevEl.textContent = preview;
            if (item)   item.removeAttribute('hidden');
        } else {
            cell.classList.remove('planner-ha-nota');
            if (prevEl) prevEl.textContent = '';
            if (item)   item.setAttribute('hidden', '');
        }
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    document.addEventListener('DOMContentLoaded', init);
    if (document.readyState !== 'loading') init();

    document.addEventListener('click', function (e) {
        // Apre la nota in lettura
        var pin = e.target.closest('.planner-note-pin');
        if (pin) { e.preventDefault(); openScreen(pin.closest('.planner-cell'), false); return; }

        // "+" : apre direttamente in scrittura
        if (canEdit) {
            var add = e.target.closest('.planner-add-note');
            if (add) { e.preventDefault(); openScreen(add.closest('.planner-cell'), true); return; }
        }

        // Chiusura
        if (e.target.closest('.postit-close') || e.target.closest('.postit-backdrop')) {
            e.preventDefault(); closeScreen(); return;
        }

        if (canEdit) {
            if (e.target.closest('.postit-edit')) {
                e.preventDefault();
                viewEl.setAttribute('hidden', '');
                editorEl.removeAttribute('hidden');
                if (taEl) taEl.focus();
                return;
            }
            if (e.target.closest('.postit-save'))   { e.preventDefault(); save();   return; }
            if (e.target.closest('.postit-delete')) { e.preventDefault(); remove(); return; }
            if (e.target.closest('.postit-cancel')) {
                e.preventDefault();
                if (currentCell && currentCell.getAttribute('data-note')) {
                    editorEl.setAttribute('hidden', '');
                    viewEl.removeAttribute('hidden');
                } else {
                    closeScreen();
                }
                return;
            }
        }
    });

    // ESC chiude
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && screenEl && screenEl.classList.contains('is-open')) {
            closeScreen();
        }
    });
})();

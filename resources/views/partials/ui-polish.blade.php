{{--
    System-wide finishing touches: smooth motion, hover and focus states, and nicer scrollbars.
    Included by partials.system-feedback, so every page gets it without editing each one.
    Only adds to the pages' own styles. Containers never get a transform, because that would
    move position:fixed modals and menus inside them; motion that moves things is kept to the
    modal box itself and small icons. Everything calms down for "reduce motion" users.
--}}
<style>
    /* ── Page: content fades in instead of popping ── */
    .content { animation: ui-fade-in .32s ease-out; }
    @keyframes ui-fade-in { from { opacity: 0; } to { opacity: 1; } }

    /* ── Modals: the backdrop fades, the box rises and settles ── */
    .modal-overlay.open, .type-modal-overlay.open { animation: ui-fade-in .18s ease-out; }
    .modal-overlay.open > .modal, .type-modal-overlay.open > .type-modal { animation: ui-pop-in .26s var(--sf-ease-out, cubic-bezier(.2, .8, .2, 1)); }
    @keyframes ui-pop-in { from { opacity: 0; transform: translateY(10px) scale(.97); } to { opacity: 1; transform: none; } }

    /* The sidebar has its own styles and motion (partials.sidebar). */

    /* ── Cards: lift with a shadow on hover (no movement, so nothing inside shifts) ── */
    .stat-card, .folder-card, .tmpl-card, .req-card, .site-card, .file-row, .review-card, .lib-card {
        transition: box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
    }
    .stat-card:hover, .folder-card:hover, .tmpl-card:hover, .req-card:hover, .review-card:hover, .lib-card:hover {
        box-shadow: 0 6px 18px rgba(15, 37, 87, .08); border-color: #d5dbe8;
    }
    .stat-icon { transition: transform .2s ease; }
    .stat-card:hover .stat-icon { transform: scale(1.06); }

    /* ── Buttons: smooth colour changes and a small press ── */
    button, .btn-save, .btn-cancel, .btn-view, .btn-del, .btn-type, a[class^="btn"], a[class*=" btn"] {
        transition: background-color .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease, opacity .16s ease, transform .1s ease;
    }
    button:not(:disabled):active, a[class^="btn"]:active, a[class*=" btn"]:active { transform: scale(.98); }
    button:disabled, .btn-save:disabled { cursor: not-allowed; }

    /* ── Fields: a clear focus ring ── */
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { transition: border-color .15s ease, box-shadow .15s ease; }
    input:not([type=checkbox]):not([type=radio]):not([type=file]):focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .14);
    }

    /* ── Keyboard users always see where they are ── */
    a:focus-visible, button:focus-visible, summary:focus-visible, [tabindex]:focus-visible {
        outline: 2px solid #2563eb; outline-offset: 2px; border-radius: 4px;
    }

    /* ── Tables: the row under the pointer is easy to follow ── */
    tbody tr { transition: background-color .15s ease; }

    /* ── Thin, quiet scrollbars in the main area ── */
    .content { scrollbar-width: thin; scrollbar-color: #c9d0dd transparent; }
    .content::-webkit-scrollbar { width: 8px; height: 8px; }
    .content::-webkit-scrollbar-thumb { background: #c9d0dd; border-radius: 8px; }
    .content::-webkit-scrollbar-track { background: transparent; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
    }
</style>

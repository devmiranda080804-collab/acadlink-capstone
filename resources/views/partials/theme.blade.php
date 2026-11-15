{{--
    System theme: gives every page the look of the sidebar and the dashboards — the
    Plus Jakarta Sans font, navy and gold, soft rounded cards, clean tables and buttons.
    Style only: no markup or behaviour changes. It applies only to pages with the sidebar
    (everything after .sb), so the login, the file viewer, the document editor and the
    downloaded documents keep their own look. Included by partials.system-feedback.
--}}
<style>
    :root {
        --th-navy: #0f2557; --th-navy-2: #1b3d7a; --th-gold: #f5b700; --th-gold-ink: #0b1d47;
        --th-ink: #14213d; --th-muted: #667085; --th-line: #e7eaf1; --th-line-2: #d5dbe8; --th-bg: #f4f6fb;
        --th-radius: 16px; --th-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05);
    }

    /* ── Base ── */
    .sb ~ * { font-family: 'Plus Jakarta Sans', Arial, sans-serif; }
    :is(.sb ~ *) :is(button, input, select, textarea) { font-family: inherit; }
    .sb ~ .main, :is(.sb ~ *) .content { background: var(--th-bg); }
    :is(.sb ~ *) .content { color: var(--th-ink); }

    /* ── Top bar ── */
    :is(.sb ~ *) .topnav { background: rgba(255, 255, 255, .92); border-bottom: 1px solid var(--th-line); backdrop-filter: saturate(1.4) blur(6px); }
    :is(.sb ~ *) .user-name { font-weight: 700; color: var(--th-ink); }
    :is(.sb ~ *) .user-email { color: var(--th-muted); }
    :is(.sb ~ *) .user-avatar { background: var(--th-navy); color: #fff; font-weight: 800; }

    /* ── Page heading ── */
    :is(.sb ~ *) .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.3px; color: var(--th-ink); }
    :is(.sb ~ *) .page-sub { font-size: 13.5px; color: var(--th-muted); font-weight: 500; }

    /* ── Stat cards (like the dashboard KPIs) ── */
    :is(.sb ~ *) .stat-card { border-radius: var(--th-radius); border: 1px solid var(--th-line); box-shadow: var(--th-shadow); background: #fff; }
    :is(.sb ~ *) .stat-icon { border-radius: 12px; }
    :is(.sb ~ *) .stat-icon.blue   { background: #eff4ff; color: #2563eb; }
    :is(.sb ~ *) .stat-icon.green  { background: #ecfdf3; color: #16a34a; }
    :is(.sb ~ *) .stat-icon.amber  { background: #fff7e6; color: #d97706; }
    :is(.sb ~ *) .stat-icon.purple { background: #f4f0ff; color: #7c3aed; }
    :is(.sb ~ *) .stat-icon.red    { background: #fef2f2; color: #dc2626; }
    :is(.sb ~ *) .stat-value { font-weight: 800; letter-spacing: -.4px; color: var(--th-ink); }
    :is(.sb ~ *) .stat-label { color: var(--th-muted); font-weight: 600; }

    /* ── Cards and panels ── */
    :is(.sb ~ *) :is(.panel, .site-card, .settings-card, .req-card, .tmpl-card, .folder-card, .review-card, .form-panel, .folder-panel, .cal-card, .report-card) {
        border-radius: var(--th-radius); border-color: var(--th-line); box-shadow: var(--th-shadow);
    }
    :is(.sb ~ *) :is(.panel-title, .report-title, .settings-card-title, .site-card-title) { font-weight: 800; color: var(--th-ink); }

    /* ── Tables ── */
    :is(.sb ~ *) :is(.acc-table, .files-table, .report-table, .filing-table, .assign-table, .log-table) { border-collapse: separate; border-spacing: 0; }
    :is(.sb ~ *) :is(.acc-table, .files-table, .report-table, .filing-table, .assign-table, .log-table) th {
        background: #f8f9fc; color: #475467; font-size: 11.5px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; border-bottom: 1px solid var(--th-line);
    }
    :is(.sb ~ *) :is(.acc-table, .files-table, .report-table, .filing-table, .assign-table, .log-table) td { border-bottom: 1px solid #f1f3f8; }
    :is(.sb ~ *) :is(.acc-table, .files-table, .report-table, .filing-table, .assign-table, .log-table) tbody tr:hover td { background: #f8f9fc; }

    /* ── Buttons: one colour for every main action — navy, like the sidebar. Gold stays an
          accent (the active menu item and badges), never a button. Red is kept only for
          actions that delete or remove something. ── */
    :is(.sb ~ *) :is(.btn-save, .btn-create, .btn-add, .btn-share, .btn-new-doc, .btn-submit, .btn-upload, .btn-assign,
        .btn-approve, .btn-validate, .btn-post, .btn-add-event, .btn-save-modal, .btn-generate) {
        background: var(--th-navy); color: #fff; border: 1px solid var(--th-navy); border-radius: 10px; font-weight: 700;
    }
    :is(.sb ~ *) :is(.btn-save, .btn-create, .btn-add, .btn-share, .btn-new-doc, .btn-submit, .btn-upload, .btn-assign,
        .btn-approve, .btn-validate, .btn-post, .btn-add-event, .btn-save-modal, .btn-generate):hover:not(:disabled) {
        background: var(--th-navy-2); border-color: var(--th-navy-2);
    }
    :is(.sb ~ *) .btn-cancel { background: #fff; border: 1px solid var(--th-line-2); color: #344054; border-radius: 10px; font-weight: 700; }
    :is(.sb ~ *) .btn-cancel:hover { background: #f8f9fc; }
    :is(.sb ~ *) :is(.btn-view, .btn-back) { border-radius: 10px; font-weight: 700; }
    :is(.sb ~ *) :is(.btn-danger, .btn-del) { border-radius: 10px; }

    /* ── Fields ── */
    :is(.sb ~ *) :is(.modal-field, .field, .settings-field, .site-field, .form-group) :is(input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea),
    :is(.sb ~ *) :is(.filter-select, .search-box input, .search-wrap input) {
        border-radius: 10px; border-color: var(--th-line-2);
    }
    :is(.sb ~ *) :is(input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea):focus { border-color: var(--th-navy-2); }
    :is(.sb ~ *) :is(.modal-field, .field, .settings-field, .form-group) > label { font-weight: 700; color: #344054; }

    /* ── Tabs and filters: the active one in navy, like the sidebar ── */
    :is(.sb ~ *) :is(.filter-tab, .role-tab, .sub-tab) { border-radius: 999px; font-weight: 700; }
    :is(.sb ~ *) :is(.filter-tab, .role-tab, .sub-tab).active { background: var(--th-navy); color: #fff; border-color: var(--th-navy); }
    :is(.sb ~ *) :is(.cms-tab, .tab-item) { font-weight: 700; }
    :is(.sb ~ *) :is(.cms-tab, .tab-item).active { color: var(--th-navy); border-bottom-color: var(--th-gold); }
    :is(.sb ~ *) .page-btn { border-radius: 8px; font-weight: 700; }
    :is(.sb ~ *) .page-btn.active { background: var(--th-navy); border-color: var(--th-navy); color: #fff; }

    /* ── Modals: one look for every dialog in the system, on every page (the document editor
          too) — a thin navy bar on top fading into a soft navy wash behind the title, a gold
          accent under the title, softer fields and a footer bar for the buttons. Only the look
          changes; sizes and behaviour stay. "html" makes these win over each page's own styles. ── */
    html :is(.modal-overlay, .type-modal-overlay, .library-overlay, .history-overlay, .cf-overlay, .qtype-overlay, #logout-confirm-overlay, .sf-confirm) {
        background: rgba(11, 29, 71, .48) !important; backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
    }
    html :is(.modal, .type-modal, .cf-modal, .return-modal, .qtype-modal, .history-box, .library-box, .sf-confirm-box, #logout-confirm-overlay > div) {
        border-radius: 20px !important; border: 1px solid rgba(15, 37, 87, .08) !important;
        box-shadow: 0 30px 70px rgba(11, 29, 71, .30), 0 2px 6px rgba(11, 29, 71, .08) !important;
        background:
            linear-gradient(90deg, #0f2557, #2f5aa8) top / 100% 4px no-repeat,
            linear-gradient(180deg, #eef3fc 0, rgba(238, 243, 252, 0) 110px) top / 100% 110px no-repeat,
            #fff !important;
        font-family: 'Plus Jakarta Sans', Arial, sans-serif !important;
    }
    html :is(.modal, .type-modal, .return-modal, .history-box, .library-box) { padding-top: 28px; }
    html :is(.modal-title, .return-modal h3, .type-modal .site-card-title, .history-heading, .library-heading, .sf-confirm-title) {
        font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 19px; font-weight: 800; color: var(--th-ink); letter-spacing: -.2px;
    }
    html :is(.modal-title, .history-heading, .library-heading)::after { content: ""; display: block; width: 34px; height: 3px; border-radius: 3px; background: var(--th-gold); margin-top: 8px; }
    html :is(.modal-hint, .modal-sub, .history-note, .library-note, .sf-confirm-msg) { color: var(--th-muted); font-size: 13px; line-height: 1.55; }
    html .modal-field > label { font-size: 12.5px; font-weight: 700; color: #344054; }
    html .modal-field :is(input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea) {
        background-color: #f9fafc; border: 1px solid var(--th-line-2); border-radius: 10px; transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
    }
    html .modal-field :is(input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea):focus { background-color: #fff; border-color: var(--th-navy-2); box-shadow: 0 0 0 3px rgba(37, 99, 235, .14); }
    html .modal-field input[type=file] { padding: 8px; border: 1px dashed var(--th-line-2); border-radius: 10px; background: #f9fafc; width: 100%; }
    html :is(.modal-actions, .type-modal-actions) { margin-top: 22px; padding-top: 16px; border-top: 1px solid #eef0f5; gap: 10px; }
    html :is(.modal-actions, .type-modal-actions) :is(.btn-save, .btn-cancel, .btn-danger) { min-width: 96px; padding-top: 10px; padding-bottom: 10px; border-radius: 10px; }
    html .modal-error { border-radius: 10px; border-left: 4px solid #dc2626; background: #fef2f2; color: #991b1b; }
    html :is(.modal-warning, .modal-note) { border-radius: 10px; }
    /* The system's own confirm dialog (confirmAction): same frame, its own icon and buttons. */
    html .sf-confirm-box { padding-top: 30px !important; }
    html .sf-btn { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-weight: 700; border-radius: 10px; }
    /* The log-out confirmation, which is written with inline styles on every page. */
    html #logout-confirm-overlay > div { padding: 30px 26px 22px !important; width: 380px !important; }
    html #logout-confirm-overlay > div > div:first-child { font-size: 18px !important; font-weight: 800 !important; color: var(--th-ink) !important; font-family: inherit !important; }
    html #logout-confirm-overlay > div > div:nth-child(2) { font-family: inherit !important; color: var(--th-muted) !important; }
    html #logout-confirm-overlay button { border-radius: 10px !important; padding: 10px 20px !important; font-family: inherit !important; font-weight: 700 !important; }

    /* ── Messages ── */
    :is(.sb ~ *) :is(.alert-success, .alert-error) { border-radius: 12px; font-weight: 600; }
    :is(.sb ~ *) :is(.announcement-banner) { border-radius: 14px; }
    :is(.sb ~ *) :is(.empty-state, .ann-empty) { color: var(--th-muted); }
</style>

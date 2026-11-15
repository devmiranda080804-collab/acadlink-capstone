{{-- Dashboard look shared by every role: a navy hero, floating KPI cards and clean panels,
     in the sidebar's colours and font. Everything is scoped under .db. --}}
<style>
    .db { --db-navy: #0f2557; --db-navy-2: #0b1d47; --db-gold: #f5b700; --db-ink: #14213d; --db-muted: #667085; --db-line: #e7eaf1;
        --db-blue: #2563eb; --db-green: #16a34a; --db-amber: #d97706; --db-red: #dc2626; --db-violet: #7c3aed;
        font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: var(--db-ink); width: 100%; }
    .db * { box-sizing: border-box; }
    .db .db-icon { width: 18px; height: 18px; flex-shrink: 0; }

    /* ── Hero ── */
    .db-hero { position: relative; overflow: hidden; border-radius: 20px; padding: 28px 30px 62px; color: #fff;
        background: radial-gradient(1200px 300px at 85% -20%, rgba(245, 183, 0, .18), transparent 60%), linear-gradient(135deg, #0b1d47 0%, #1b3d7a 60%, #0f2557 100%); }
    .db-hero::before { content: ""; position: absolute; inset: 0; background-image: repeating-linear-gradient(45deg, rgba(255, 255, 255, .035) 0 2px, transparent 2px 22px); pointer-events: none; }
    .db-hero-seal { position: absolute; right: -40px; top: -50px; width: 260px; height: 260px; border-radius: 50%; background-size: contain; background-repeat: no-repeat; opacity: .09; pointer-events: none; }
    .db-hero-inner { position: relative; display: flex; justify-content: space-between; gap: 24px; align-items: flex-start; }
    .db-eyebrow { display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 12px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; color: #c9d6f0; }
    .db-chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; background: rgba(245, 183, 0, .16); color: #ffd85c;
        border: 1px solid rgba(245, 183, 0, .35); font-size: 11.5px; letter-spacing: .3px; text-transform: none; font-weight: 700; }
    .db-greeting { margin: 10px 0 6px; font-size: clamp(22px, 2.4vw, 30px); font-weight: 800; line-height: 1.2; letter-spacing: -.3px; }
    .db-greeting span { color: var(--db-gold); }
    .db-hero-sub { color: #c9d6f0; font-size: 14.5px; font-weight: 500; max-width: 640px; }
    .db-hero-sub strong { color: #fff; }
    .db-date { flex-shrink: 0; text-align: center; padding: 12px 18px; border-radius: 16px; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .14); backdrop-filter: blur(4px); min-width: 108px; }
    .db-date .day { font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #ffd85c; }
    .db-date .num { font-size: 34px; font-weight: 800; line-height: 1.05; }
    .db-date .month { font-size: 12.5px; color: #c9d6f0; font-weight: 600; }
    .db-actions { position: relative; display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
    .db-action { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 700;
        text-decoration: none; color: #fff; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .18); transition: background-color .18s ease, transform .18s ease, box-shadow .18s ease; }
    .db-action:hover { background: rgba(255, 255, 255, .18); transform: translateY(-1px); }
    /* On the navy hero the main action is the same navy family turned around: white with navy text. */
    .db-action.primary { background: #fff; color: var(--db-navy); border-color: #fff; box-shadow: 0 8px 20px rgba(0, 0, 0, .18); }
    .db-action.primary:hover { background: #eef2fa; }

    /* ── Notice (unread announcements) ── */
    .db-notice { display: flex; align-items: center; gap: 14px; margin: 18px 0 0; padding: 14px 18px; border-radius: 14px; background: #fffbeb; border: 1px solid #fde68a; }
    .db-notice-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--db-gold); color: var(--db-navy-2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .db-notice-text { flex: 1; min-width: 0; font-size: 13.5px; color: #78350f; }
    .db-notice-text strong { display: block; color: #451a03; font-size: 14px; }
    .db-notice-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #92400e; text-decoration: none; white-space: nowrap; }
    .db-notice-link:hover { text-decoration: underline; }

    /* ── KPI cards, floating over the hero ── */
    .db-kpis { position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin: -40px 18px 0; }
    .db-kpi { position: relative; display: flex; flex-direction: column; gap: 12px; padding: 18px 18px 14px; border-radius: 16px; background: #fff;
        border: 1px solid var(--db-line); box-shadow: 0 10px 30px rgba(15, 37, 87, .08); text-decoration: none; color: inherit; overflow: hidden;
        transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease; }
    .db-kpi::before { content: ""; position: absolute; left: 0; top: 0; right: 0; height: 4px; background: var(--tone); }
    a.db-kpi:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(15, 37, 87, .14); border-color: #d5dbe8; }
    .db-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .db-kpi-label { font-size: 13px; font-weight: 700; color: var(--db-muted); }
    .db-kpi-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--tone); background: var(--tone-soft); }
    .db-kpi-icon .db-icon { width: 20px; height: 20px; }
    .db-kpi-value { font-size: 34px; font-weight: 800; line-height: 1; letter-spacing: -.5px; color: var(--db-ink); }
    .db-kpi-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding-top: 10px; border-top: 1px dashed var(--db-line); font-size: 12.5px; color: var(--db-muted); font-weight: 600; }
    .db-kpi-foot .go { display: inline-flex; align-items: center; gap: 4px; color: var(--db-navy); font-weight: 700; }
    .db-kpi-foot .go .db-icon { width: 14px; height: 14px; transition: transform .18s ease; }
    a.db-kpi:hover .go .db-icon { transform: translateX(3px); }
    .tone-blue   { --tone: var(--db-blue);   --tone-soft: #eff4ff; }
    .tone-green  { --tone: var(--db-green);  --tone-soft: #ecfdf3; }
    .tone-amber  { --tone: var(--db-amber);  --tone-soft: #fff7e6; }
    .tone-violet { --tone: var(--db-violet); --tone-soft: #f4f0ff; }
    .tone-red    { --tone: var(--db-red);    --tone-soft: #fef2f2; }
    .tone-gold   { --tone: #c99400;          --tone-soft: #fff8db; }

    /* ── Panels ── */
    .db-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 18px; margin-top: 22px; }
    .db-panel { background: #fff; border: 1px solid var(--db-line); border-radius: 16px; padding: 18px 18px 8px; min-width: 0; }
    .db-panel.wide { grid-column: 1 / -1; }
    .db-panel-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .db-panel-icon { width: 34px; height: 34px; border-radius: 10px; background: #eef2fa; color: var(--db-navy); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .db-panel-title { font-size: 15.5px; font-weight: 800; color: var(--db-ink); }
    .db-count { margin-left: 2px; padding: 2px 8px; border-radius: 999px; background: #eef2fa; color: var(--db-navy); font-size: 11.5px; font-weight: 800; }
    .db-more { margin-left: auto; display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 999px; border: 1px solid var(--db-line);
        font-size: 12.5px; font-weight: 700; color: var(--db-navy); text-decoration: none; transition: background-color .18s ease, border-color .18s ease; white-space: nowrap; }
    .db-more:hover { background: #f4f6fb; border-color: #d5dbe8; }
    .db-more .db-icon { width: 14px; height: 14px; }

    .db-row { display: flex; align-items: center; gap: 12px; padding: 11px 8px; margin: 0 -8px; border-radius: 12px; transition: background-color .15s ease; }
    .db-row + .db-row { border-top: 1px solid #f1f3f8; }
    .db-row:hover { background: #f8f9fc; }
    .db-row-main { flex: 1; min-width: 0; }
    .db-row-title { font-size: 14px; font-weight: 700; color: var(--db-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .db-row-meta { font-size: 12.5px; color: var(--db-muted); font-weight: 500; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .db-file { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 10.5px; font-weight: 800; letter-spacing: .3px; }
    .db-file.pdf { background: #fef2f2; color: #b91c1c; }
    .db-file.doc { background: #eff4ff; color: #1d4ed8; }
    .db-file.xls { background: #ecfdf3; color: #15803d; }
    .db-file.ppt { background: #fff7e6; color: #c2410c; }
    .db-file.other { background: #f2f4f7; color: #475467; }
    .db-pill { padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 800; white-space: nowrap; }
    .db-pill.new { background: #eff4ff; color: #1d4ed8; }
    .db-pill.wait { background: #fff7e6; color: #b45309; }
    .db-pill.back { background: #fef2f2; color: #b91c1c; }
    .db-pill.ok { background: #ecfdf3; color: #15803d; }
    .db-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 26px 10px 30px; color: var(--db-muted); font-size: 13.5px; text-align: center; }
    .db-empty-icon { width: 46px; height: 46px; border-radius: 50%; background: #f2f4f7; color: #98a2b3; display: flex; align-items: center; justify-content: center; }

    /* ── Activities timeline ── */
    .db-timeline { position: relative; }
    .db-event { display: flex; gap: 14px; padding: 10px 8px; margin: 0 -8px; border-radius: 12px; transition: background-color .15s ease; }
    .db-event:hover { background: #f8f9fc; }
    .db-event-date { width: 52px; flex-shrink: 0; text-align: center; border-radius: 12px; overflow: hidden; border: 1px solid var(--db-line); background: #fff; }
    .db-event-date .m { background: var(--db-navy); color: var(--db-gold); font-size: 10.5px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; padding: 3px 0; }
    .db-event-date .d { font-size: 20px; font-weight: 800; padding: 3px 0 4px; color: var(--db-ink); }
    .db-event-when { display: inline-block; margin-top: 4px; font-size: 11.5px; font-weight: 700; color: var(--db-navy); background: #eef2fa; border-radius: 999px; padding: 2px 8px; }
    .db-event-when.today { background: var(--db-gold); color: var(--db-navy-2); }
    .db-row-meta .db-icon { width: 13px; height: 13px; vertical-align: -2px; margin-right: 3px; }

    /* ── Faculty courses ── */
    .db-courses { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 12px; padding: 4px 0 12px; }
    .db-course { padding: 14px; border-radius: 14px; border: 1px solid var(--db-line); background: linear-gradient(180deg, #fff, #fafbfe); transition: border-color .18s ease, box-shadow .18s ease; }
    .db-course:hover { border-color: #cfd7e8; box-shadow: 0 8px 20px rgba(15, 37, 87, .07); }
    .db-course-code { display: inline-block; padding: 3px 9px; border-radius: 8px; background: var(--db-navy); color: #fff; font-size: 12px; font-weight: 800; letter-spacing: .3px; }
    .db-course-title { margin-top: 8px; font-size: 14px; font-weight: 700; line-height: 1.35; }
    .db-course-meta { margin-top: 6px; font-size: 12px; color: var(--db-muted); font-weight: 600; }

    /* ── Motion: sections rise in one after another ── */
    .db-rise { animation: db-rise .5s cubic-bezier(.2, .8, .2, 1) both; }
    @keyframes db-rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    .db-kpis .db-kpi:nth-child(1) { animation-delay: .06s; } .db-kpis .db-kpi:nth-child(2) { animation-delay: .12s; }
    .db-kpis .db-kpi:nth-child(3) { animation-delay: .18s; } .db-kpis .db-kpi:nth-child(4) { animation-delay: .24s; }
    .db-grid > .db-panel:nth-child(1) { animation-delay: .22s; } .db-grid > .db-panel:nth-child(2) { animation-delay: .3s; }
    .db-grid > .db-panel:nth-child(3) { animation-delay: .38s; } .db-grid > .db-panel:nth-child(4) { animation-delay: .46s; }

    @media (max-width: 760px) {
        .db-hero { padding: 22px 20px 56px; }
        .db-hero-inner { flex-direction: column; }
        .db-date { display: none; }
        .db-kpis { margin: -38px 8px 0; }
        .db-grid { grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) { .db-rise { animation: none; } a.db-kpi:hover, .db-action:hover { transform: none; } }
</style>
<script>
    // Counts each KPI number up from zero once, unless the viewer prefers less motion.
    document.addEventListener('DOMContentLoaded', function () {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        document.querySelectorAll('.db-kpi-value[data-count]').forEach(function (el) {
            var target = parseInt(el.dataset.count, 10) || 0;
            if (target < 2) return;
            var start = null, duration = Math.min(900, 300 + target * 20);
            function step(ts) {
                if (!start) start = ts;
                var p = Math.min(1, (ts - start) / duration);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(step);
            }
            el.textContent = '0';
            requestAnimationFrame(step);
        });
    });
</script>

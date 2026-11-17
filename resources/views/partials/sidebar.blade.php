{{--
    The sidebar of every page, for every role (menu in App\Support\SidebarMenu).
    Grouped sections, line icons, the active page as a gold pill, badges for new items,
    and the signed-in user at the bottom with Collapse sidebar and Sign out.
    The collapsed state is remembered on this browser.
--}}
@php
    $sbUser = auth()->user();
    $sbSections = \App\Support\SidebarMenu::sectionsFor($sbUser->role, $navPermissions ?? []);
    $sbBadges = [
        'newTemplatesCount'        => $newTemplatesCount ?? 0,
        'urgentSubmissionsCount'   => $urgentSubmissionsCount ?? 0,
        'unreadAnnouncementsCount' => $unreadAnnouncementsCount ?? 0,
    ];
    $sbSubtitle = $sbUser->role === 'faculty'
        ? trim('Faculty · ' . implode(', ', $sbUser->programCodes()), ' ·')
        : \App\Support\SidebarMenu::ROLE_LABELS[$sbUser->role] ?? ucfirst($sbUser->role);
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap">
<style>
    aside.sidebar.sb {
        --sb-bg: #0f2557; --sb-bg-2: #0a1a40; --sb-text: #d6e0f2; --sb-muted: #8ea2c8; --sb-hover: rgba(255, 255, 255, .07);
        --sb-gold: #f5b700; --sb-red: #c12035; --sb-blue: #273f89;
        position: relative; width: 264px; flex-shrink: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden; color: var(--sb-text);
        background:
            repeating-linear-gradient(135deg, rgba(255, 255, 255, .025) 0 2px, transparent 2px 18px),
            linear-gradient(180deg, var(--sb-bg) 0%, var(--sb-bg-2) 100%);
        font-family: 'Plus Jakarta Sans', Arial, sans-serif; transition: width .22s ease; border-right: 1px solid rgba(255, 255, 255, .06);
    }
    .sb * { box-sizing: border-box; }
    /* A faint CBMA seal behind the lower part of the menu. */
    .sb-watermark { position: absolute; left: 50%; bottom: 110px; width: 230px; height: 230px; transform: translateX(-50%); border-radius: 50%;
        background-size: contain; background-repeat: no-repeat; background-position: center; opacity: .045; pointer-events: none; }

    /* Brand: the CBMA seal in the middle with a gold ring, and the letterhead stripe */
    .sb-brand { position: relative; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 22px 16px 16px; }
    .sb-brand-logo { width: 62px; height: 62px; border-radius: 50%; padding: 3px; background: linear-gradient(135deg, #ffd85c, var(--sb-gold) 55%, #c99400);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .28); flex-shrink: 0; transition: width .22s ease, height .22s ease; }
    .sb-brand-logo img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; background: #0b1d47; display: block; }
    .sb-brand-text { min-width: 0; margin-top: 10px; }
    .sb-brand-name { display: block; color: #fff; font-size: 20px; font-weight: 800; letter-spacing: 2.5px; line-height: 1.1; }
    .sb-brand-sub { display: block; color: var(--sb-muted); font-size: 12px; font-weight: 600; margin-top: 3px; letter-spacing: .3px; }
    .sb-stripe { display: flex; height: 3px; margin: 0 18px; border-radius: 3px; overflow: hidden; }
    .sb-stripe span { flex: 1; }
    .sb-stripe span:nth-child(1) { background: var(--sb-red); }
    .sb-stripe span:nth-child(2) { background: var(--sb-gold); }
    .sb-stripe span:nth-child(3) { background: #5b7fd6; }

    /* Menu */
    .sb-nav { position: relative; flex: 1; overflow-y: auto; padding: 6px 12px 14px; scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, .18) transparent; }
    .sb-nav::-webkit-scrollbar { width: 6px; }
    .sb-nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .18); border-radius: 6px; }
    .sb-section { margin-top: 14px; }
    .sb-section-title { display: flex; align-items: center; gap: 8px; padding: 6px 12px 8px; color: var(--sb-muted); font-size: 11px; font-weight: 800; letter-spacing: 1.4px; text-transform: uppercase; }
    .sb-section-title::before { content: ""; width: 10px; height: 2px; border-radius: 2px; background: var(--sb-gold); opacity: .85; }
    .sb-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px; }
    .sb-link { position: relative; display: flex; align-items: center; gap: 13px; padding: 11px 12px 11px 14px; border-radius: 10px; color: var(--sb-text);
        text-decoration: none; font-size: 15px; font-weight: 600; line-height: 1.25; transition: background-color .18s ease, color .18s ease; }
    .sb-link::before { content: ""; position: absolute; left: 0; top: 9px; bottom: 9px; width: 4px; border-radius: 0 4px 4px 0; background: var(--sb-gold);
        transform: scaleY(0); transition: transform .2s ease; }
    .sb-link:hover { background: var(--sb-hover); color: #fff; }
    .sb-link .sb-icon { width: 20px; height: 20px; flex-shrink: 0; opacity: .85; transition: transform .18s ease, opacity .18s ease, color .18s ease; }
    .sb-link:hover .sb-icon { opacity: 1; transform: scale(1.08); }
    /* Active page: a gold edge, a gold icon and a soft gold-tinted highlight */
    .sb-link.is-active { background: linear-gradient(90deg, rgba(245, 183, 0, .16), rgba(255, 255, 255, .05)); color: #fff; font-weight: 700; }
    .sb-link.is-active::before { transform: scaleY(1); }
    .sb-link.is-active .sb-icon { color: var(--sb-gold); opacity: 1; }
    .sb-label { flex: 1; min-width: 0; }
    .sb-badge { min-width: 22px; height: 22px; padding: 0 6px; border-radius: 11px; background: var(--sb-red); color: #fff;
        font-size: 11.5px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 0 0 2px rgba(10, 26, 64, .6); }
    .sb-link:focus-visible, .sb-action:focus-visible { outline: 2px solid var(--sb-gold); outline-offset: 2px; }

    /* User card and actions */
    .sb-footer { position: relative; padding: 12px; }
    .sb-card { border-radius: 14px; background: rgba(255, 255, 255, .06); border: 1px solid rgba(255, 255, 255, .08); padding: 10px; }
    .sb-user { display: flex; align-items: center; gap: 11px; padding: 2px 2px 10px; }
    .sb-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--sb-bg); color: #fff; font-weight: 800; font-size: 14px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 0 2px var(--sb-gold); }
    .sb-user-text { min-width: 0; }
    .sb-user-name { display: block; color: #fff; font-size: 14.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-user-role { display: block; color: var(--sb-muted); font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .sb-action { display: flex; align-items: center; justify-content: center; gap: 7px; padding: 9px 8px; border: 1px solid rgba(255, 255, 255, .1); border-radius: 10px; background: rgba(255, 255, 255, .04);
        cursor: pointer; color: var(--sb-text); font: 600 13px 'Plus Jakarta Sans', Arial, sans-serif; transition: background-color .18s ease, color .18s ease, border-color .18s ease; }
    .sb-action:hover { background: rgba(255, 255, 255, .1); color: #fff; }
    .sb-action.sb-signout:hover { background: rgba(193, 32, 53, .35); border-color: rgba(193, 32, 53, .6); }
    .sb-action .sb-icon { width: 17px; height: 17px; flex-shrink: 0; transition: transform .22s ease; }

    /* Collapsed: icons only, with the label as a tooltip */
    aside.sidebar.sb.is-collapsed { width: 78px; }
    .sb.is-collapsed .sb-brand { padding: 18px 0 14px; }
    .sb.is-collapsed .sb-brand-logo { width: 46px; height: 46px; }
    .sb.is-collapsed .sb-stripe { margin: 0 14px; }
    .sb.is-collapsed .sb-brand-text, .sb.is-collapsed .sb-section-title, .sb.is-collapsed .sb-label,
    .sb.is-collapsed .sb-user-text, .sb.is-collapsed .sb-action span, .sb.is-collapsed .sb-watermark { display: none; }
    .sb.is-collapsed .sb-section { margin-top: 10px; border-top: 1px solid rgba(255, 255, 255, .06); padding-top: 8px; }
    .sb.is-collapsed .sb-section:first-child { border-top: 0; }
    .sb.is-collapsed .sb-link { justify-content: center; padding: 11px 0; }
    .sb.is-collapsed .sb-badge { position: absolute; top: 4px; right: 8px; min-width: 18px; height: 18px; font-size: 10px; padding: 0 4px; }
    .sb.is-collapsed .sb-footer { padding: 10px 8px; }
    .sb.is-collapsed .sb-card { padding: 8px 4px; background: none; border: 0; }
    .sb.is-collapsed .sb-user { justify-content: center; padding: 0 0 10px; }
    .sb.is-collapsed .sb-actions { grid-template-columns: 1fr; }
    .sb.is-collapsed .sb-collapse .sb-icon { transform: rotate(180deg); }

    @media (prefers-reduced-motion: reduce) { aside.sidebar.sb, .sb-link, .sb-link::before, .sb-icon { transition: none !important; } }
</style>

<aside class="sidebar sb" id="app-sidebar" aria-label="Main menu">
    <script>
        // Applied before the menu paints, so a collapsed sidebar doesn't flash open.
        try { if (localStorage.getItem('sb-collapsed') === '1') document.getElementById('app-sidebar').classList.add('is-collapsed'); } catch (e) {}
    </script>

    <div class="sb-watermark" style="background-image:url('{{ \App\Support\SiteContent::imageUrl('login_logo') }}')"></div>

    <div class="sb-brand">
        <div class="sb-brand-logo"><img src="{{ \App\Support\SiteContent::imageUrl('login_logo') }}" alt="Logo"></div>
        <div class="sb-brand-text">
            <span class="sb-brand-name">{{ \App\Support\SiteContent::text('sidebar_name') }}</span>
            <span class="sb-brand-sub">{{ \App\Support\SiteContent::text('sidebar_subtitle') }}</span>
        </div>
    </div>
    <div class="sb-stripe" aria-hidden="true"><span></span><span></span><span></span></div>

    <nav class="sb-nav">
        @foreach($sbSections as $sectionLabel => $items)
            <div class="sb-section">
                <span class="sb-section-title">{{ $sectionLabel }}</span>
                <ul class="sb-list">
                    @foreach($items as [$perm, $label, $url, $pattern, $icon, $badgeVar])
                        @php
                            // A page shown for another (the file viewer) can say which item stays highlighted.
                            $active = isset($sbActivePath) ? \Illuminate\Support\Str::is($pattern, $sbActivePath) : request()->is($pattern);
                            $badge = $badgeVar ? ($sbBadges[$badgeVar] ?? 0) : 0;
                        @endphp
                        <li>
                            <a href="{{ url($url) }}" class="sb-link {{ $active ? 'is-active' : '' }}" title="{{ $label }}" @if($active) aria-current="page" @endif>
                                {!! \App\Support\SidebarMenu::icon($icon) !!}
                                <span class="sb-label">{{ $label }}</span>
                                @if($badge > 0)
                                    <span class="sb-badge" aria-label="{{ $badge }} new">{{ $badge }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="sb-footer">
        <div class="sb-card">
            <div class="sb-user" title="{{ $sbUser->name }} — {{ $sbSubtitle }}">
                <div class="sb-avatar">{{ $sbUser->initials }}</div>
                <div class="sb-user-text">
                    <span class="sb-user-name">{{ $sbUser->name }}</span>
                    <span class="sb-user-role">{{ $sbSubtitle }}</span>
                </div>
            </div>
            <div class="sb-actions">
                <button type="button" class="sb-action sb-collapse" title="Collapse sidebar" onclick="toggleSidebar()">{!! \App\Support\SidebarMenu::icon('collapse') !!}<span>Collapse</span></button>
                <button type="button" class="sb-action sb-signout" title="Sign out" onclick="typeof openLogoutModal === 'function' ? openLogoutModal() : document.getElementById('logout-form').submit()">{!! \App\Support\SidebarMenu::icon('logout') !!}<span>Sign out</span></button>
            </div>
        </div>
        <form id="logout-form" method="POST" action="{{ url('/logout') }}" style="display:none;">@csrf</form>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('app-sidebar');
            var collapsed = sidebar.classList.toggle('is-collapsed');
            try { localStorage.setItem('sb-collapsed', collapsed ? '1' : '0'); } catch (e) {}
            var button = sidebar.querySelector('.sb-collapse');
            button.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
            button.querySelector('span').textContent = collapsed ? 'Expand' : 'Collapse';
        }
        (function () {
            var sidebar = document.getElementById('app-sidebar');
            if (sidebar.classList.contains('is-collapsed')) {
                var button = sidebar.querySelector('.sb-collapse');
                button.title = 'Expand sidebar';
                button.querySelector('span').textContent = 'Expand';
            }
        })();
    </script>
</aside>

{{-- Loading look when a page is opened from the menu: the page's title, tabs, filters and side menus show right away,
     and only the parts that hold data (table rows, card grids, lists, the panel beside a side menu) show a shimmering
     skeleton drawn from their own content for a moment. Nothing on the page is changed; the skeleton sits on top. --}}
@php
    $sbDataParts = 'tbody, .cms-main, .co-main, .cc-main, .eg-main, .st-layout > :not(.st-nav), .rp-layout > :not(.rp-nav), .repo-layout > :not(nav), .ann-panel, .files-panel, .folder-panel, .report-panel, .summary-row, .db-kpis, .db-panel > :not(.db-panel-head), [class$="-grid"], [class*="-grid "], [class$="-list"], [class*="-list "]';
@endphp
<style>
    html.sb-arriving .sb ~ * :is({!! $sbDataParts !!}) { visibility: hidden; }
    .sb-skel-hide { visibility: hidden !important; }
    .sb-skel { position: fixed; z-index: 800; overflow: hidden; pointer-events: none; transition: opacity .25s ease; }
    .sb-skel.is-done { opacity: 0; }
    .sb-skel i { position: absolute; display: block; }
    .sb-skel .skx.skl { box-shadow: inset 0 0 0 1px #e7eaf1; }
    .sb-skel .skf { background: #fff; box-shadow: inset 0 0 0 1px #e1e6ef; }
    .sb-skel .skn { background: #eef1f6; }
    /* Text, icons, pictures and buttons shimmer; the light sweeps across the whole screen at once. */
    .sb-skel .skb { background: linear-gradient(100deg, #e2e7f1 40%, #f3f6fb 50%, #e2e7f1 60%) fixed; background-size: 200% 100%; animation: sk-shimmer 1.3s linear infinite; }
    @keyframes sk-shimmer { from { background-position: 100% 0; } to { background-position: -100% 0; } }
    @media (prefers-reduced-motion: reduce) { .sb-skel .skb { animation: none; } }
</style>
<span id="sb-loader-label" role="status" aria-live="polite" style="position:fixed;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);"></span>
<script>
    (function () {
        var KEY = 'sb-nav-loading';
        var PARTS = @json($sbDataParts);
        var root = document.documentElement;
        var label = document.getElementById('sb-loader-label');
        var sidebar = document.getElementById('app-sidebar');
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var SHOW_FOR = reduce ? 150 : 650;     // how long the data parts stay as a skeleton
        var MAX_ITEMS = 300;                   // shapes per part
        var SKIP = /^(SCRIPT|STYLE|LINK|META|TEMPLATE|NOSCRIPT|BR|OPTION)$/;
        var SOLID = /^(IMG|SVG|CANVAS|VIDEO|IFRAME|BUTTON|PROGRESS|METER)$/;
        var FIELD = /^(INPUT|SELECT|TEXTAREA)$/;

        // Shapes from an earlier version of this loading screen are no longer used.
        try { Object.keys(localStorage).forEach(function (k) { if (k.indexOf('sb-skel:') === 0) localStorage.removeItem(k); }); } catch (e) {}

        // Opening a page from the menu: remember it, so the next page knows to show its loading look.
        document.querySelectorAll('#app-sidebar .sb-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || link.target === '_blank') return;
                try { sessionStorage.setItem(KEY, (link.querySelector('.sb-label') || link).textContent.trim()); } catch (err) {}
            });
        });

        var arriving = null;
        try { arriving = sessionStorage.getItem(KEY); sessionStorage.removeItem(KEY); } catch (e) {}
        if (!arriving || !sidebar) return;

        var started = Date.now();
        root.classList.add('sb-arriving');                 // hide the data parts until their skeleton is ready
        var failsafe = setTimeout(function () { root.classList.remove('sb-arriving'); }, 3000);
        label.textContent = 'Loading ' + arriving;

        function radius(cs, w, h) { return Math.min(Math.round(parseFloat(cs.borderTopLeftRadius) || 0), Math.round(Math.min(w, h) / 2)); }
        function isClear(c) { return !c || c === 'transparent' || /,\s*0\)$/.test(c); }
        function isDark(c) { var m = c.match(/\d+(\.\d+)?/g); return m && (0.299 * m[0] + 0.587 * m[1] + 0.114 * m[2]) < 140; }
        function backdrop(el) {
            for (var n = el; n && n.nodeType === 1; n = n.parentElement) {
                var c = getComputedStyle(n).backgroundColor;
                if (!isClear(c)) return isDark(c) ? '#e6ebf4' : c;
            }
            return '#fff';
        }

        // Draw a skeleton of one data part, from what is inside it, in a layer placed exactly over it.
        function skeleton(part) {
            var box = part.getBoundingClientRect();
            var bg = backdrop(part.tagName === 'TBODY' ? part.closest('table') || part : part);
            var html = '', count = 0, range = document.createRange();

            function add(cls, r, rad, extra, clip) {
                if (count >= MAX_ITEMS) return;
                var l = Math.max(r.left, clip.l), t = Math.max(r.top, clip.t), rt = Math.min(r.left + r.width, clip.r), b = Math.min(r.top + r.height, clip.b);
                if (rt - l < 2 || b - t < 1) return;
                count++;
                html += '<i class="' + cls + '" style="left:' + Math.round(l - box.left) + 'px;top:' + Math.round(t - box.top) + 'px;width:' + Math.round(rt - l) + 'px;height:' + Math.max(1, Math.round(b - t)) + 'px;border-radius:' + rad + 'px;' + (extra || '') + '"></i>';
            }

            function walk(el, clip, parentBg) {
                if (count >= MAX_ITEMS || SKIP.test(el.tagName.toUpperCase())) return;
                var r = el.getBoundingClientRect();
                if (r.top >= clip.b || r.bottom <= clip.t || (r.width < 3 && r.height < 3)) return;
                var cs = getComputedStyle(el);
                if (cs.display === 'none' || cs.visibility === 'hidden' || cs.position === 'fixed') return;
                var tag = el.tagName.toUpperCase();

                if (FIELD.test(tag)) {
                    if (el.type === 'hidden') return;
                    if (el.type === 'checkbox' || el.type === 'radio') add('skb', r, radius(cs, r.width, r.height), '', clip);
                    else add('skx skf', r, radius(cs, r.width, r.height), '', clip);
                    return;
                }
                if (SOLID.test(tag)) { add('skb', r, Math.max(radius(cs, r.width, r.height), 4), '', clip); return; }

                if (el !== part) {
                    var c = cs.backgroundColor, hasBg = !isClear(c) && c !== parentBg, hasImg = cs.backgroundImage !== 'none';
                    var bt = parseFloat(cs.borderTopWidth), bb = parseFloat(cs.borderBottomWidth), bl = parseFloat(cs.borderLeftWidth), br = parseFloat(cs.borderRightWidth);
                    var outlined = (bt > 0 && bb > 0 && bl > 0 && br > 0) || cs.boxShadow !== 'none';
                    if ((hasBg || hasImg || outlined) && r.width >= 20 && r.height >= 14) {
                        var tone = (hasImg || (hasBg && isDark(c))) ? '#e6ebf4' : (hasBg ? c : bg);
                        add('skx' + (outlined ? ' skl' : ''), r, radius(cs, r.width, r.height), 'background:' + tone, clip);
                        if (hasBg) parentBg = c;
                    } else {
                        // Row lines (a border on one side only) stay as thin lines.
                        if (bt > 0) add('skn', { left: r.left, top: r.top, width: r.width, height: 1 }, 0, '', clip);
                        if (bb > 0) add('skn', { left: r.left, top: r.bottom - 1, width: r.width, height: 1 }, 0, '', clip);
                    }
                }
                if (cs.overflowX !== 'visible' || cs.overflowY !== 'visible') {
                    clip = { l: Math.max(clip.l, r.left), t: Math.max(clip.t, r.top), r: Math.min(clip.r, r.right), b: Math.min(clip.b, r.bottom) };
                }

                for (var n = el.firstChild; n; n = n.nextSibling) {
                    if (n.nodeType === 3) {
                        if (!n.nodeValue.trim()) continue;
                        range.selectNodeContents(n);
                        var lines = range.getClientRects();
                        for (var i = 0; i < lines.length && i < 6; i++) {
                            var lr = lines[i];
                            if (lr.width < 2) continue;
                            var h = Math.max(6, Math.round(lr.height * 0.58));
                            add('skb', { left: lr.left, top: lr.top + (lr.height - h) / 2, width: lr.width, height: h }, 5, '', clip);
                        }
                    } else if (n.nodeType === 1) {
                        walk(n, clip, parentBg);
                    }
                }
            }

            var screen = { l: box.left, t: Math.max(box.top, 0), r: box.right, b: Math.min(box.bottom, window.innerHeight) };
            if (screen.b - screen.t < 4) return null;
            walk(part, screen, bg);

            var layer = document.createElement('div');
            layer.className = 'sb-skel';
            layer.setAttribute('aria-hidden', 'true');
            var pcs = getComputedStyle(part);
            layer.style.cssText = 'left:' + box.left + 'px;top:' + screen.t + 'px;width:' + box.width + 'px;height:' + (screen.b - screen.t) + 'px;border-radius:' + pcs.borderRadius + ';';
            layer.innerHTML = '<div style="position:absolute;left:0;right:0;top:' + (box.top - screen.t) + 'px;height:' + box.height + 'px;">' + html + '</div>';
            return layer;
        }

        var layers = [], hidden = [];
        function finish() {
            hidden.forEach(function (part) { part.classList.remove('sb-skel-hide'); });
            hidden = [];
            window.removeEventListener('scroll', finish, true);
            window.removeEventListener('resize', finish);
            layers.forEach(function (layer) {
                layer.classList.add('is-done');
                setTimeout(function () { layer.remove(); }, 300);
            });
            layers = [];
            label.textContent = '';
        }

        function build() {
            clearTimeout(failsafe);
            root.classList.remove('sb-arriving');
            var parts = [];
            Array.prototype.forEach.call(document.querySelectorAll(PARTS), function (el) {
                if (sidebar.contains(el) || !(sidebar.compareDocumentPosition(el) & Node.DOCUMENT_POSITION_FOLLOWING)) return;
                if (el.closest('.modal-overlay, .type-modal-overlay, [role="dialog"], .sb-skel')) return;
                var r = el.getBoundingClientRect();
                if (r.width < 20 || r.height < 12 || r.top >= window.innerHeight || r.bottom <= 0) return;
                // A part inside another part is already covered by the outer one.
                for (var i = 0; i < parts.length; i++) if (parts[i].contains(el)) return;
                parts = parts.filter(function (p) { return !el.contains(p); });
                parts.push(el);
            });
            // Measure every part first, then hide the real data under the skeleton (all before the next paint).
            var made = parts.map(function (part) { return [part, skeleton(part)]; });
            made.forEach(function (pair) {
                if (!pair[1]) return;
                pair[0].classList.add('sb-skel-hide');
                hidden.push(pair[0]);
                document.body.appendChild(pair[1]);
                layers.push(pair[1]);
            });
            // The layers sit over the page, so moving the page ends the loading look right away.
            window.addEventListener('scroll', finish, true);
            window.addEventListener('resize', finish);
            var done = function () { setTimeout(finish, Math.max(0, SHOW_FOR - (Date.now() - started))); };
            if (document.readyState === 'complete') done(); else window.addEventListener('load', done);
        }

        // Wait for the page's own scripts to fill it in, then draw.
        document.addEventListener('DOMContentLoaded', function () { setTimeout(build, 0); });

        // Coming back with the browser's Back button must never leave anything hidden.
        window.addEventListener('pageshow', function (e) { if (e.persisted) { root.classList.remove('sb-arriving'); finish(); } });
    })();
</script>

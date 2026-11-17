{{-- One part at a time inside a System CMS tab (Document Layouts, Dropdown Lists, Site Content):
     pills on top (data-part-group / data-part-key), panels below (data-part-panel / data-part-key).
     The part last used is remembered on this browser; after a save the server reopens the saved one. --}}
<script>
    function showCmsPart(group, key) {
        document.querySelectorAll('[data-part-group="' + group + '"]').forEach(function (tab) {
            tab.setAttribute('aria-selected', tab.dataset.partKey === key ? 'true' : 'false');
        });
        document.querySelectorAll('[data-part-panel="' + group + '"]').forEach(function (panel) {
            panel.hidden = panel.dataset.partKey !== key;
        });
        try { localStorage.setItem('cms-part-' + group, key); } catch (e) {}
    }
    // serverChose: the page was opened right after saving one part, so keep that one open.
    function restoreCmsPart(group, serverChose) {
        if (serverChose) return;
        var key = null;
        try { key = localStorage.getItem('cms-part-' + group); } catch (e) {}
        if (key && document.querySelector('[data-part-panel="' + group + '"][data-part-key="' + key + '"]')) showCmsPart(group, key);
    }
    // A required field in a hidden part can't be shown by the browser; open its part first.
    document.addEventListener('invalid', function (e) {
        var panel = e.target.closest && e.target.closest('[data-part-panel][hidden]');
        if (panel) showCmsPart(panel.dataset.partPanel, panel.dataset.partKey);
    }, true);
</script>

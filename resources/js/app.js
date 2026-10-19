const sidebar = document.querySelector('#app-sidebar');
const toggle = document.querySelector('[data-sidebar-toggle]');
const backdrop = document.querySelector('.sidebar-backdrop');
const mobile = window.matchMedia('(max-width: 991px)');
let collapsed = false;
try { collapsed = localStorage.getItem('helpdesk-sidebar') === 'collapsed'; } catch {}
function setNavigation(open = false) {
    document.body.classList.toggle('sidebar-collapsed', !mobile.matches && collapsed);
    document.body.classList.toggle('sidebar-open', mobile.matches && open);
    if (backdrop) backdrop.hidden = !(mobile.matches && open);
    if (sidebar) sidebar.inert = mobile.matches && !open;
    toggle?.setAttribute('aria-expanded', String(mobile.matches ? open : !collapsed));
}
toggle?.addEventListener('click', () => {
    if (mobile.matches) {
        const open = !document.body.classList.contains('sidebar-open');
        setNavigation(open);
        if (open) sidebar?.querySelector('[data-sidebar-dismiss]')?.focus();
    } else {
        collapsed = !collapsed;
        try { localStorage.setItem('helpdesk-sidebar', collapsed ? 'collapsed' : 'expanded'); } catch {}
        setNavigation();
    }
});
document.querySelectorAll('[data-sidebar-dismiss]').forEach(button => button.addEventListener('click', () => { setNavigation(); toggle?.focus(); }));
mobile.addEventListener('change', () => setNavigation());
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        if (document.body.classList.contains('sidebar-open')) { setNavigation(); toggle?.focus(); }
        document.querySelectorAll('.profile-menu[open]').forEach(menu => { menu.open = false; menu.querySelector('summary')?.focus(); });
    }
    if (event.key === 'Tab' && mobile.matches && document.body.classList.contains('sidebar-open')) {
        const controls = [...sidebar.querySelectorAll('a[href], button')];
        const first = controls[0], last = controls.at(-1);
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
});
document.addEventListener('click', event => document.querySelectorAll('.profile-menu[open]').forEach(menu => { if (!menu.contains(event.target)) menu.open = false; }));
setNavigation();

document.querySelectorAll('[data-ticket-table]').forEach(panel => {
    const rows = [...panel.querySelectorAll('[data-ticket-row]')];
    const search = panel.querySelector('[data-ticket-search]');
    const sort = panel.querySelector('[data-ticket-sort]');
    const empty = panel.querySelector('[data-ticket-empty]');
    function update() {
        const query = search.value.trim().toLocaleLowerCase();
        const order = sort.value;
        const sorted = [...rows].sort((a, b) => order === 'subject'
            ? a.querySelector('[data-ticket-subject]').textContent.localeCompare(b.querySelector('[data-ticket-subject]').textContent)
            : order === 'priority' ? Number(b.dataset.priority) - Number(a.dataset.priority) || Number(b.dataset.created) - Number(a.dataset.created)
            : order === 'oldest' ? Number(a.dataset.created) - Number(b.dataset.created) : Number(b.dataset.created) - Number(a.dataset.created));
        let visible = 0;
        sorted.forEach(row => { row.hidden = !row.textContent.toLocaleLowerCase().includes(query); if (!row.hidden) visible++; empty.before(row); });
        empty.hidden = visible > 0;
        panel.querySelector('[data-empty-title]').textContent = rows.length ? 'No matching tickets' : 'Your queue is clear';
        panel.querySelector('[data-empty-message]').textContent = rows.length ? 'Try another search or open the full ticket list.' : 'New support requests will appear here.';
        panel.querySelector('[data-table-count]').textContent = `Showing ${visible} of ${rows.length} recent tickets`;
    }
    search.addEventListener('input', update);
    sort.addEventListener('change', update);
});

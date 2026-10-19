const namespace = 'http://www.w3.org/2000/svg';
const colors = ['#4f8cff', '#a78bfa', '#34d399', '#fbbf24'];
function element(tag, attributes = {}, text) {
    const node = document.createElementNS(namespace, tag);
    Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, value));
    if (text !== undefined) node.textContent = text;
    return node;
}
function point(svg, node, label) {
    node.setAttribute('tabindex', '0');
    node.setAttribute('role', 'img');
    node.setAttribute('aria-label', label);
    node.append(element('title', {}, label));
    svg.append(node);
}
document.querySelectorAll('[data-dashboard-chart]').forEach((panel, index) => {
    const labels = JSON.parse(panel.dataset.chartLabels);
    const values = JSON.parse(panel.dataset.chartValues).map(value => Math.max(0, Number(value) || 0));
    const total = values.reduce((sum, value) => sum + value, 0);
    if (!total) return;
    const type = panel.dataset.chartType;
    const svg = element('svg', { viewBox: type === 'doughnut' ? '0 0 320 220' : '0 0 600 230', 'aria-hidden': 'false' });
    if (type === 'doughnut') {
        const circumference = 2 * Math.PI * 76;
        svg.append(element('circle', { cx: 160, cy: 110, r: 76, fill: 'none', stroke: 'var(--border-color)', 'stroke-width': 22 }));
        let offset = 0;
        values.forEach((value, i) => {
            if (!value) return;
            const length = value / total * circumference;
            point(svg, element('circle', { cx: 160, cy: 110, r: 76, fill: 'none', stroke: colors[i], 'stroke-width': 22, 'stroke-dasharray': `${Math.max(0, length - 3)} ${circumference}`, 'stroke-dashoffset': -offset, transform: 'rotate(-90 160 110)', class: 'chart-mark' }), `${labels[i]}: ${value} tickets`);
            offset += length;
        });
        svg.append(element('text', { x: 160, y: 109, 'text-anchor': 'middle', class: 'chart-center-value' }, total));
        svg.append(element('text', { x: 160, y: 134, 'text-anchor': 'middle', class: 'chart-axis-label' }, 'TOTAL TICKETS'));
    } else {
        const left = 45, right = 578, top = 22, bottom = 190;
        const maximum = Math.max(...values, 1);
        const step = Math.max(1, Math.ceil(maximum / 4));
        const ceiling = step * 4;
        for (let tick = 0; tick <= 4; tick++) {
            const y = bottom - tick / 4 * (bottom - top);
            svg.append(element('line', { x1: left, x2: right, y1: y, y2: y, class: 'chart-grid-line' }));
            svg.append(element('text', { x: left - 12, y: y + 4, 'text-anchor': 'end', class: 'chart-axis-label' }, tick * step));
        }
        const positions = values.map((value, i) => ({ x: type === 'bar' ? left + (i + 0.5) / values.length * (right - left) : left + i / (values.length - 1) * (right - left), y: bottom - value / ceiling * (bottom - top) }));
        if (type === 'line') {
            const defs = element('defs');
            const gradient = element('linearGradient', { id: `activity-fill-${index}`, x1: 0, y1: 0, x2: 0, y2: 1 });
            gradient.append(element('stop', { offset: '0%', 'stop-color': '#4f8cff', 'stop-opacity': '.28' }), element('stop', { offset: '100%', 'stop-color': '#4f8cff', 'stop-opacity': '0' }));
            defs.append(gradient); svg.append(defs);
            const path = positions.map(({ x, y }, i) => `${i ? 'L' : 'M'} ${x} ${y}`).join(' ');
            svg.append(element('path', { d: `${path} L ${right} ${bottom} L ${left} ${bottom} Z`, fill: `url(#activity-fill-${index})` }));
            svg.append(element('path', { d: path, fill: 'none', stroke: '#4f8cff', 'stroke-width': 3, 'stroke-linejoin': 'round' }));
        }
        positions.forEach(({ x, y }, i) => {
            const mark = type === 'bar' ? element('rect', { x: x - 29, y, width: 58, height: bottom - y, rx: 6, fill: ['#34d399', '#4f8cff', '#fbbf24', '#f87171'][i], class: 'chart-mark' }) : element('circle', { cx: x, cy: y, r: 5, fill: 'var(--surface)', stroke: '#4f8cff', 'stroke-width': 3, class: 'chart-mark' });
            point(svg, mark, `${labels[i]}: ${values[i]} tickets`);
            svg.append(element('text', { x, y: 216, 'text-anchor': 'middle', class: 'chart-axis-label' }, labels[i]));
            if (type === 'bar') svg.append(element('text', { x, y: y - 8, 'text-anchor': 'middle', class: 'chart-bar-value' }, values[i]));
        });
    }
    panel.querySelector('[data-chart-surface]').append(svg);
    panel.querySelector('[data-chart-loading]')?.remove();
});

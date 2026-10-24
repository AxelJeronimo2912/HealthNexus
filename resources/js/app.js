import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

/* ───────────── Gráficas: estilo unificado HealthNexus ─────────────
 * Todas las gráficas de Chart.js heredan tipografía, colores y tooltips.
 * Las vistas pueden seguir definiendo colores propios; esto solo fija
 * los valores por defecto para que el conjunto se vea consistente.
 */
const palette = {
    brand: '#3A40A6',
    accent: '#7E5AE6',
    sky: '#0EA5E9',
    emerald: '#10B981',
    amber: '#F59E0B',
    rose: '#F43F5E',
    slate: '#94A3B8',
};
window.HN_PALETTE = palette;

Chart.defaults.font.family = "'Inter', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#64748B';
Chart.defaults.borderColor = 'rgba(15, 23, 42, 0.06)';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.tooltip.backgroundColor = '#141739';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { weight: '600' };
Chart.defaults.elements.bar.borderRadius = 6;
Chart.defaults.elements.line.tension = 0.35;
Chart.defaults.elements.point.radius = 3;
Chart.defaults.elements.point.hoverRadius = 5;

// ApexCharts (si alguna vista lo carga por CDN) toma estos valores globales
window.Apex = {
    chart: { fontFamily: "'Inter', ui-sans-serif, system-ui, sans-serif", toolbar: { show: false } },
    colors: Object.values(palette),
    grid: { borderColor: 'rgba(15, 23, 42, 0.06)' },
    tooltip: { theme: 'dark' },
};

/* ───────────── Layout: sidebar y paleta de comandos ───────────── */
document.addEventListener('alpine:init', () => {
    Alpine.data('hnLayout', () => ({
        // Sidebar fijado (expandido) en escritorio; se recuerda entre visitas
        pinned: (() => {
            try { return localStorage.getItem('hn.sidebar.pinned') !== '0'; } catch (e) { return true; }
        })(),
        // Drawer en móvil
        mobileOpen: false,
        // Paleta de comandos (Ctrl/⌘ + K)
        paletteOpen: false,

        init() {
            document.documentElement.classList.add('hn-ready');
        },

        togglePinned() {
            this.pinned = !this.pinned;
            try { localStorage.setItem('hn.sidebar.pinned', this.pinned ? '1' : '0'); } catch (e) {}
        },
    }));

    Alpine.data('hnPalette', (items = [], pacientesUrl = null) => ({
        q: '',
        active: 0,
        items,
        get results() {
            const q = this.q.trim().toLowerCase();
            const norm = (s) => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
            let list = q
                ? this.items.filter((i) => norm(i.label + ' ' + i.section).includes(norm(q)))
                : this.items;
            list = list.slice(0, 8);
            if (q && pacientesUrl) {
                list = [
                    ...list,
                    {
                        label: `Buscar paciente “${this.q.trim()}”`,
                        section: 'Pacientes',
                        url: `${pacientesUrl}?buscar=${encodeURIComponent(this.q.trim())}`,
                        search: true,
                    },
                ];
            }
            return list;
        },
        open() {
            this.q = '';
            this.active = 0;
            this.$nextTick(() => this.$refs.input?.focus());
        },
        move(d) {
            const n = this.results.length;
            if (!n) return;
            this.active = (this.active + d + n) % n;
        },
        go(item) {
            const target = item ?? this.results[this.active];
            if (target) window.location.href = target.url;
        },
    }));
});

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

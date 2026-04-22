import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Title,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Title);

/**
 * Read a CSS custom property from the document root.
 *
 * @param {string} name  e.g. '--color-text'
 * @returns {string}
 */
function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

/**
 * Build Chart.js color options from the current theme CSS variables.
 *
 * @returns {{ text: string, muted: string, border: string, accent: string, surface: string }}
 */
function themeColors() {
    return {
        text:    cssVar('--color-text'),
        muted:   cssVar('--color-text-muted'),
        border:  cssVar('--color-border'),
        accent:  cssVar('--color-accent'),
        surface: cssVar('--color-surface'),
    };
}

/**
 * Alpine component that renders a Chart.js bar chart of occurrence counts
 * binned by geologic period (earlyInterval). Re-renders when the theme changes.
 *
 * @param {{ occurrences: Array }} params
 */
export function taxonCharts({ occurrences }) {
    return {
        chart:    null,
        observer: null,

        destroy() {
            this.observer?.disconnect();
            this.observer = null;
            this.chart?.destroy();
            this.chart = null;
        },

        init() {
            // Bin by earlyInterval; track maxMa values per bin for sort order.
            const bins = {};
            const binMa = {};

            occurrences.forEach((occ) => {
                const period = occ.earlyInterval ?? 'Unknown';
                bins[period] = (bins[period] ?? 0) + 1;
                if (occ.maxMa != null) {
                    (binMa[period] ??= []).push(occ.maxMa);
                }
            });

            // Sort oldest-first by average maxMa descending.
            const sorted = Object.entries(bins).sort(([a], [b]) => {
                const avg = (key) =>
                    binMa[key]?.length
                        ? binMa[key].reduce((s, v) => s + v, 0) / binMa[key].length
                        : 0;
                return avg(b) - avg(a);
            });

            this.labels = sorted.map(([label]) => label);
            this.data   = sorted.map(([, count]) => count);

            this.render();

            // Re-render whenever the data-theme attribute changes.
            this.observer = new MutationObserver(() => {
                this.chart?.destroy();
                this.render();
            });
            this.observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme'],
            });
        },

        render() {
            const canvas = document.getElementById('period-chart');
            if (!canvas) return;

            const c = themeColors();

            this.chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: this.labels,
                    datasets: [
                        {
                            data:            this.data,
                            backgroundColor: c.accent,
                        },
                    ],
                },
                options: {
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.surface,
                            titleColor:      c.text,
                            bodyColor:       c.muted,
                            borderColor:     c.border,
                            borderWidth:     1,
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${ctx.parsed.y} occurrences`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: { color: c.muted },
                            grid:  { color: c.border },
                            title: {
                                display: true,
                                text:    'Geologic Period',
                                color:   c.muted,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: c.muted },
                            grid:  { color: c.border },
                            title: {
                                display: true,
                                text:    'Occurrences',
                                color:   c.muted,
                            },
                        },
                    },
                },
            });
        },
    };
}
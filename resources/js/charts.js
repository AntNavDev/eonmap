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
 * Alpine component that renders a Chart.js bar chart of occurrence counts
 * binned by geologic period (earlyInterval).
 *
 * @param {{ occurrences: Array }} params
 */
export function taxonCharts({ occurrences }) {
    return {
        chart: null,

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

            const labels = sorted.map(([label]) => label);
            const data = sorted.map(([, count]) => count);

            const canvas = document.getElementById('period-chart');
            if (!canvas) return;

            this.chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            data,
                            backgroundColor: '#0096B4',
                        },
                    ],
                },
                options: {
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${ctx.parsed.y} occurrences`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Geologic Period' },
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Occurrences' },
                        },
                    },
                },
            });
        },
    };
}
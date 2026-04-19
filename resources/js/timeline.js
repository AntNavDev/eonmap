import { Timeline, DataSet } from 'vis-timeline/standalone';
import 'vis-timeline/styles/vis-timeline-graph2d.min.css';

/**
 * Map a geologic age in Ma to a JavaScript Date.
 *
 * Approximation: 1 Ma is treated as 1 second before the Unix epoch
 * (i.e., 0 Ma = epoch, 540 Ma = -540,000 ms). This keeps all values
 * within the safe JS Date range while preserving proportional spacing
 * on the timeline axis. Axis labels are overridden to show Ma values.
 *
 * @param {number} ma
 * @returns {Date}
 */
function maToDate(ma) {
    return new Date(-ma * 1000);
}

/**
 * Alpine component that renders a vis-timeline showing the full temporal
 * range of a taxon across all its occurrences.
 *
 * @param {{ occurrences: Array, name: string }} params
 */
export function taxonTimeline({ occurrences, name }) {
    return {
        timeline: null,

        destroy() {
            this.timeline?.destroy();
            this.timeline = null;
        },

        init() {
            const maValues = occurrences.map((o) => o.maxMa).filter((v) => v != null);
            const miValues = occurrences.map((o) => o.minMa).filter((v) => v != null);

            if (maValues.length === 0 || miValues.length === 0) return;

            const maxMa = Math.max(...maValues);
            const minMa = Math.min(...miValues);

            const container = document.getElementById('taxon-timeline');
            if (!container) return;

            const items = new DataSet([
                {
                    id: 1,
                    content: name,
                    start: maToDate(maxMa),
                    end: maToDate(minMa),
                    group: 1,
                },
            ]);

            const groups = new DataSet([{ id: 1, content: '' }]);

            this.timeline = new Timeline(container, items, groups, {
                start: maToDate(540),
                end: maToDate(0),
                selectable: false,
                editable: false,
                moveable: false,
                zoomable: false,
                showMajorLabels: true,
                showMinorLabels: true,
                format: {
                    majorLabels: (date) => {
                        const ma = Math.round(-date.getTime() / 1000);
                        return ma + ' Ma';
                    },
                    minorLabels: (date) => {
                        const ma = Math.round(-date.getTime() / 1000);
                        return ma;
                    },
                },
            });
        },
    };
}
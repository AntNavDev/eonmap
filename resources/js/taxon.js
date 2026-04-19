import { taxonCharts } from './charts.js';
import { taxonTimeline } from './timeline.js';
import { taxonMiniMap } from './map.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('taxonCharts', taxonCharts);
    window.Alpine.data('taxonTimeline', taxonTimeline);
    window.Alpine.data('taxonMiniMap', taxonMiniMap);
});
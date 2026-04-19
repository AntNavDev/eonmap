import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.markercluster';
import 'leaflet-draw/dist/leaflet.draw.css';
import 'leaflet-draw';
import 'leaflet.heat';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

// Vite bundles assets differently to webpack — provide explicit URLs for
// Leaflet's default marker icons so they resolve correctly at runtime.
import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIconUrl,
    iconRetinaUrl: markerIcon2xUrl,
    shadowUrl: markerShadowUrl,
});

const TILE_LAYERS = {
    osm: {
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        options: {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        },
    },
    esri: {
        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        options: {
            attribution:
                'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
            maxZoom: 18,
        },
    },
    carto: {
        url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
        options: {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19,
        },
    },
};

function fossilMap() {
    return {
        map: null,
        markerClusterGroup: null,
        heatLayer: null,
        drawControl: null,
        tileLayers: {},
        currentOccurrences: [],
        heatmapMode: false,
        paleoMode: false,
        basemapKey: 'osm',

        init() {
            this.map = L.map('eonmap-map', {
                center: [20, 0],
                zoom: 2,
            });

            // Build all tile layers but only add the default one
            Object.entries(TILE_LAYERS).forEach(([key, { url, options }]) => {
                this.tileLayers[key] = L.tileLayer(url, options);
            });
            this.tileLayers.osm.addTo(this.map);

            // Marker cluster group
            this.markerClusterGroup = L.markerClusterGroup();
            this.map.addLayer(this.markerClusterGroup);

            // Feature group to hold drawn shapes
            const drawnItems = new L.FeatureGroup();
            this.map.addLayer(drawnItems);

            // Draw control — rectangle only
            this.drawControl = new L.Control.Draw({
                draw: {
                    rectangle: true,
                    polyline: false,
                    polygon: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                },
                edit: { featureGroup: drawnItems },
            });
            this.map.addControl(this.drawControl);

            // Push drawn rectangle bounds into the OccurrenceFilters Livewire
            // component via a global Livewire event — OccurrenceFilters listens
            // for 'bbox-set' with #[On('bbox-set')].
            this.map.on(L.Draw.Event.CREATED, (event) => {
                drawnItems.clearLayers();
                drawnItems.addLayer(event.layer);

                const bounds = event.layer.getBounds();
                window.Livewire.dispatch('bbox-set', {
                    lngMin: bounds.getWest(),
                    lngMax: bounds.getEast(),
                    latMin: bounds.getSouth(),
                    latMax: bounds.getNorth(),
                });
            });

            // Initialise Tom Select on the taxon input
            const basenameEl = document.getElementById('baseName');
            if (basenameEl) {
                new TomSelect('#baseName', {
                    create: false,
                    maxItems: 1,
                });
            }
        },

        /**
         * Re-renders all markers from the given occurrences array. Called
         * whenever the occurrences-loaded browser event fires.
         *
         * @param {Array} occurrences - Serialised OccurrenceDTO objects.
         */
        updateMarkers(occurrences) {
            this.currentOccurrences = occurrences;
            this.markerClusterGroup.clearLayers();

            if (this.heatLayer) {
                this.map.removeLayer(this.heatLayer);
                this.heatLayer = null;
            }

            const heatPoints = [];

            occurrences.forEach((occ) => {
                const lat = this.paleoMode ? occ.paleolat : occ.lat;
                const lng = this.paleoMode ? occ.paleolng : occ.lng;

                if (lat == null || lng == null) return;

                const ageRange = occ.maxMa != null
                    ? ` (${occ.maxMa}&ndash;${occ.minMa} Ma)`
                    : '';

                const popup = `
                    <div style="min-width:180px;line-height:1.4">
                        <p style="font-weight:600;margin:0 0 2px">${occ.acceptedName}</p>
                        <p style="font-size:.8em;color:#888;margin:0 0 6px">${occ.acceptedRank}</p>
                        <p style="font-size:.85em;margin:0 0 2px">
                            ${occ.earlyInterval ?? '?'} &mdash; ${occ.lateInterval ?? '?'}${ageRange}
                        </p>
                        ${occ.environment ? `<p style="font-size:.85em;margin:0 0 2px">Env: ${occ.environment}</p>` : ''}
                        ${occ.formation  ? `<p style="font-size:.85em;margin:0 0 2px">Formation: ${occ.formation}</p>` : ''}
                        ${occ.country    ? `<p style="font-size:.85em;margin:0 0 6px">Country: ${occ.country}</p>` : ''}
                        <a href="/occurrences/${occ.occurrenceNo}" style="font-size:.85em;color:#0096B4">
                            View occurrence &rarr;
                        </a>
                    </div>
                `;

                L.marker([lat, lng])
                    .bindPopup(popup)
                    .addTo(this.markerClusterGroup);

                heatPoints.push([lat, lng]);
            });

            if (this.heatmapMode && heatPoints.length > 0) {
                this.heatLayer = L.heatLayer(heatPoints, { radius: 25 });
                this.map.addLayer(this.heatLayer);
            }
        },

        /**
         * Toggle the heatmap layer on/off.
         */
        toggleHeatmap() {
            this.heatmapMode = !this.heatmapMode;

            if (this.heatmapMode) {
                const points = this.currentOccurrences
                    .map((occ) => [
                        this.paleoMode ? occ.paleolat : occ.lat,
                        this.paleoMode ? occ.paleolng : occ.lng,
                    ])
                    .filter(([lat, lng]) => lat != null && lng != null);

                if (points.length > 0) {
                    this.heatLayer = L.heatLayer(points, { radius: 25 });
                    this.map.addLayer(this.heatLayer);
                }
            } else if (this.heatLayer) {
                this.map.removeLayer(this.heatLayer);
                this.heatLayer = null;
            }
        },

        /**
         * Toggle between modern and reconstructed paleocoordinates, then
         * re-render markers using the alternate coordinate set.
         */
        togglePaleoMode() {
            this.paleoMode = !this.paleoMode;
            this.updateMarkers(this.currentOccurrences);
        },

        /**
         * Swap the active base tile layer.
         *
         * @param {'osm'|'esri'|'carto'} key
         */
        switchBasemap(key) {
            if (this.tileLayers[this.basemapKey]) {
                this.map.removeLayer(this.tileLayers[this.basemapKey]);
            }
            this.basemapKey = key;
            if (this.tileLayers[key]) {
                this.tileLayers[key].addTo(this.map);
            }
        },
    };
}

/**
 * Alpine component for the taxon page mini-map. Renders all occurrences
 * as clustered markers and fits the map bounds to the full marker set.
 *
 * @param {{ occurrences: Array }} params
 */
export function taxonMiniMap({ occurrences }) {
    return {
        map: null,
        markerClusterGroup: null,

        destroy() {
            this.map?.remove();
            this.map = null;
        },

        init() {
            this.map = L.map('taxon-map', {
                center: [20, 0],
                zoom: 2,
                scrollWheelZoom: false,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(this.map);

            this.markerClusterGroup = L.markerClusterGroup();
            this.map.addLayer(this.markerClusterGroup);

            occurrences.forEach((occ) => {
                if (occ.lat == null || occ.lng == null) return;

                L.marker([occ.lat, occ.lng])
                    .bindPopup(
                        `<strong>${occ.acceptedName}</strong><br>` +
                        `<a href="/occurrences/${occ.occurrenceNo}" style="color:#0096B4">View occurrence &rarr;</a>`
                    )
                    .addTo(this.markerClusterGroup);
            });

            if (this.markerClusterGroup.getLayers().length > 0) {
                this.map.fitBounds(this.markerClusterGroup.getBounds(), {
                    padding: [20, 20],
                });
            }
        },
    };
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('fossilMap', fossilMap);
});
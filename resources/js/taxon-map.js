import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.markercluster';

import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIconUrl,
    iconRetinaUrl: markerIcon2xUrl,
    shadowUrl: markerShadowUrl,
});

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
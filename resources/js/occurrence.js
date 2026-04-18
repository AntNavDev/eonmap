import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

// Fix Leaflet default icon paths for Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIconUrl,
    iconRetinaUrl: markerIcon2xUrl,
    shadowUrl: markerShadowUrl,
});

/**
 * Alpine component for the occurrence detail mini-map.
 *
 * @param {number|null} lat
 * @param {number|null} lng
 * @param {string} taxonName
 */
function occurrenceMiniMap(lat, lng, taxonName) {
    return {
        init() {
            if (lat == null || lng == null) return;

            const map = L.map('occurrence-mini-map', {
                center: [lat, lng],
                zoom: 8,
                scrollWheelZoom: false,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(map);

            L.marker([lat, lng])
                .bindPopup(`<strong>${taxonName}</strong>`)
                .addTo(map)
                .openPopup();
        },
    };
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('occurrenceMiniMap', occurrenceMiniMap);
});
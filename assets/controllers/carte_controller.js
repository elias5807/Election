import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';

export default class extends Controller {
    static values = {
        poles: Array,
        alertes: Array
    }

    connect() {
        this.initIcons();
        this.initMap();
    }

    initIcons() {
        const commonOptions = {
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [40, 65],
            iconAnchor: [20, 65],
            popupAnchor: [1, -60],
            shadowSize: [65, 65]
        };

        this.icons = {
            blue: new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', ...commonOptions }),
            red: new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', ...commonOptions }),
            orange: new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png', ...commonOptions })
        };
    }

    initMap() {
        // 'this.element' correspond à la div possédant le data-controller
        this.map = L.map(this.element).setView([49.894, 2.302], 12);
        
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.map);

        this.renderMarkers();
    }

    renderMarkers() {
        const SEUIL_TRACT_CRITIQUE = 1000;

        this.polesValue.forEach(pole => {
            if (!pole.lattitude || !pole.longitude) return;

            const alerteLogistique = this.alertesValue.find(a => a.nom_pole === pole.nomPole);
            const stockTracts = parseInt(pole.tract || 0);
            
            let iconeAUtiliser = this.icons.blue;
            let htmlAlerte = '';

            if (alerteLogistique) {
                iconeAUtiliser = this.icons.red;
                htmlAlerte = `<div style="background-color: #ffe6e6; border: 1px solid red; color: #cc0000; padding: 5px; margin-bottom: 10px; border-radius: 4px;"><strong>⚠️ MANQUE :</strong><br>${alerteLogistique.manquants.join(', ')}</div>`;
            } else if (stockTracts <= SEUIL_TRACT_CRITIQUE) {
                iconeAUtiliser = this.icons.orange;
                htmlAlerte = `<div style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; padding: 5px; margin-bottom: 10px; border-radius: 4px;"><strong>⚠️ TRACTS ÉPUISÉS</strong><br>Stock critique (${stockTracts})</div>`;
            }

            const content = `
                <div style="min-width: 200px; font-family: sans-serif; font-size: 14px;">
                    <h3 style="margin: 0 0 10px 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px;">${pole.nomPole}</h3>
                    ${htmlAlerte}
                    <ul style="padding-left: 20px; margin: 0; line-height: 1.6;">
                        <li><b>UNEF :</b> ${pole.unef || 0}</li>
                        <li><b>UE :</b> ${pole.ue || 0}</li>
                        <li><b>UNI :</b> ${pole.uni || 0}</li>
                        <li style="margin-top:5px; border-top: 1px solid #eee; padding-top:5px;"><b>Tracts :</b> ${stockTracts}</li>
                        <li><b>Affluence :</b> ${pole.affluence || pole.afluence || 'N/A'}</li>
                    </ul>
                </div>
            `;

            // Marqueur réel
            const marker = L.marker([pole.lattitude, pole.longitude], { 
                icon: iconeAUtiliser,
                zIndexOffset: 1000 
            }).addTo(this.map).bindPopup(content);

            // Hitbox fantôme pour mobile
            const ghostZone = L.circleMarker([pole.lattitude, pole.longitude], {
                radius: 30,
                stroke: false,
                fillOpacity: 0.0
            }).addTo(this.map);

            ghostZone.on('click', () => marker.openPopup());
        });
    }

    disconnect() {
        // Nettoyage de la carte quand on quitte la page
        if (this.map) {
            this.map.remove();
        }
    }
}
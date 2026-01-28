document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');

    // 1. RÉCUPÉRATION DES DONNÉES
    const poles = JSON.parse(mapElement.dataset.poles || '[]');
    const alertes = JSON.parse(mapElement.dataset.alertes || '[]');

    // 2. CONFIGURATION DES ICÔNES (XXL)
    const commonIconOptions = {
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [40, 65],
        iconAnchor: [20, 65],
        popupAnchor: [1, -60],
        shadowSize: [65, 65]
    };

    const IconeBleue = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', ...commonIconOptions });
    const IconeRouge = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', ...commonIconOptions });
    const IconeOrange = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png', ...commonIconOptions });

    // 3. INITIALISATION DE LA CARTE
    const map = L.map('map').setView([49.894, 2.302], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // 4. BOUCLE PRINCIPALE
    poles.forEach(function(pole) {
        if (!pole.lattitude || !pole.longitude) return;

        let affluenceValue = pole.affluence !== undefined ? pole.affluence : (pole.afluence || 'N/A');
        const alerteLogistique = alertes.find(a => a.nom_pole === pole.nomPole);
        
        let iconeAUtiliser = IconeBleue;
        let htmlAlerte = '';
        const SEUIL_TRACT_CRITIQUE = 1000;
        const stockTracts = parseInt(pole.tract || 0);

        if (alerteLogistique) {
            iconeAUtiliser = IconeRouge;
            htmlAlerte = `<div style="background-color: #ffe6e6; border: 1px solid red; color: #cc0000; padding: 5px; margin-bottom: 10px; border-radius: 4px;"><strong>⚠️ MANQUE :</strong><br>${alerteLogistique.manquants.join(', ')}</div>`;
        } 
        else if (stockTracts <= SEUIL_TRACT_CRITIQUE) {
            iconeAUtiliser = IconeOrange;
            htmlAlerte = `<div style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; padding: 5px; margin-bottom: 10px; border-radius: 4px;"><strong>⚠️ TRACTS ÉPUISÉS</strong><br>Stock critique (${stockTracts})</div>`;
        }

        const content = `
            <div style="min-width: 200px; font-family: sans-serif; font-size: 14px;">
                <h3 style="margin: 0 0 10px 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; font-size: 16px;">${pole.nomPole}</h3>
                ${htmlAlerte}
                <ul style="padding-left: 20px; margin: 0; line-height: 1.6;">
                    <li><b>UNEF :</b> ${pole.unef || 0}</li>
                    <li><b>UE :</b> ${pole.ue || 0}</li>
                    <li><b>UNI :</b> ${pole.uni || 0}</li>
                    <li style="margin-top:5px; border-top: 1px solid #eee; padding-top:5px;"><b>Tracts :</b> ${stockTracts}</li>
                    <li><b>Affluence :</b> ${affluenceValue}</li>
                </ul>
            </div>
        `;

        // --- A. CRÉATION DU MARQUEUR VISUEL ---
        const marker = L.marker([pole.lattitude, pole.longitude], { 
            icon: iconeAUtiliser,
            zIndexOffset: 1000 // On force l'icône à être au-dessus visuellement
        }).addTo(map).bindPopup(content);

        // --- B. CRÉATION DE LA ZONE FANTÔME (HITBOX AMÉLIORÉE) ---
        // On crée un cercle invisible de 30px de rayon autour du point
        const ghostZone = L.circleMarker([pole.lattitude, pole.longitude], {
            radius: 30,       // Rayon de la zone cliquable (30px = gros doigt)
            stroke: false,    // Pas de bordure
            fill: true,       // Rempli
            fillColor: '#000',// Couleur (peu importe car invisible)
            fillOpacity: 0.0, // INVISIBLE (0.0)
            className: 'ghost-hitbox' // Utile si besoin de debug CSS
        }).addTo(map);

        // Quand on clique sur la zone invisible, on ouvre la popup du vrai marqueur
        ghostZone.on('click', function() {
            marker.openPopup();
        });
    });
});
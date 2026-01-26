document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');

    // ============================================================
    // 1. RÉCUPÉRATION DES DONNÉES (Sécurisée)
    // ============================================================
    const poles = JSON.parse(mapElement.dataset.poles || '[]');
    const alertes = JSON.parse(mapElement.dataset.alertes || '[]');

    // ============================================================
    // 2. CONFIGURATION DES ICÔNES (Bleu vs Rouge)
    // ============================================================
    // Leaflet ne permet pas de changer la couleur "juste comme ça".
    // On définit donc deux images d'icônes différentes via des liens CDN fiables.
    
    const IconeBleue = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    const IconeRouge = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // ============================================================
    // 3. INITIALISATION DE LA CARTE
    // ============================================================
    const map = L.map('map').setView([49.894, 2.302], 13);
    
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // ============================================================
    // 4. BOUCLE PRINCIPALE
    // ============================================================
    poles.forEach(function(pole) {
        // Sécurité : on ignore les pôles sans coordonnées
        if (!pole.lattitude || !pole.longitude) {
            return; 
        }

        // A. RECHERCHE D'ALERTE LOGISTIQUE
        // On cherche si ce pôle est présent dans la liste des alertes
        const probleme = alertes.find(a => a.nom_pole === pole.nomPole);
        
        // Par défaut, tout va bien
        let iconeAUtiliser = IconeBleue;
        let htmlAlerte = '';

        // Si on trouve un problème, on passe en mode "Alerte"
        if (probleme) {
            iconeAUtiliser = IconeRouge;
            htmlAlerte = `
                <div style="background-color: #ffe6e6; border: 1px solid red; color: #cc0000; padding: 5px; margin-bottom: 10px; border-radius: 4px;">
                    <strong>⚠️ MANQUE :</strong><br>
                    ${probleme.manquants.join(', ')}
                </div>
            `;
        }

        // B. CONSTRUCTION DU CONTENU HTML DE LA POPUP
        const content = `
            <div style="min-width: 180px; font-family: sans-serif;">
                <h3 style="margin: 0 0 10px 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px;">
                    ${pole.nomPole}
                </h3>
                
                ${htmlAlerte}

                <ul style="padding-left: 20px; margin: 0; line-height: 1.6;">
                    <li><b>UNEF :</b> ${pole.unef || 0}</li>
                    <li><b>UE :</b> ${pole.ue || 0}</li>
                    <li><b>UNI :</b> ${pole.uni || 0}</li>
                    <li style="margin-top:5px; border-top: 1px solid #eee; padding-top:5px;">
                        <b>Tracts :</b> ${pole.tract || 0}
                    </li>
                    <li><b>Affluence :</b> ${pole.affluence || 'N/A'}</li>
                </ul>
            </div>
        `;

        // C. CRÉATION DU MARQUEUR
        L.marker([pole.lattitude, pole.longitude], { icon: iconeAUtiliser })
         .addTo(map)
         .bindPopup(content);
    });
});
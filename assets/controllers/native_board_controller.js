import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    // Quand on commence à traîner une carte
    onDragStart(event) {
        // On stocke l'ID de la carte déplacée
        event.dataTransfer.setData('text/plain', event.target.dataset.id);
        event.dataTransfer.effectAllowed = 'move';
        
        // Ajout classe visuelle
        event.target.classList.add('dragging');
    }

    // Quand on relâche la carte (ou qu'on annule)
    onDragEnd(event) {
        event.target.classList.remove('dragging');
        
        // Nettoyage visuel de toutes les colonnes
        this.element.querySelectorAll('.drag-over-active').forEach(el => {
            el.classList.remove('drag-over-active');
        });
    }

    // OBLIGATOIRE : Autoriser le drop sur la zone
    onDragOver(event) {
        event.preventDefault(); // Nécessaire pour permettre le drop
        event.dataTransfer.dropEffect = 'move';
        return false;
    }

    // Effet visuel quand on entre dans une colonne
    onDragEnter(event) {
        event.currentTarget.classList.add('drag-over-active');
    }

    // Effet visuel quand on sort d'une colonne
    onDragLeave(event) {
        event.currentTarget.classList.remove('drag-over-active');
    }

    // L'ACTION FINALE : Quand on lâche la carte
    async onDrop(event) {
        event.stopPropagation(); // Empêche les conflits
        event.currentTarget.classList.remove('drag-over-active');

        // 1. Récupérer l'ID stocké
        const militantId = event.dataTransfer.getData('text/plain');
        
        // 2. Trouver l'élément HTML de la carte
        const card = document.querySelector(`.mini-card[data-id="${militantId}"]`);
        
        // 3. Identifier la nouvelle colonne (currentTarget est la div .dashboard-grid)
        const newColumn = event.currentTarget;
        const newPoleNom = newColumn.dataset.pole;

        // 4. Déplacer visuellement la carte dans le DOM tout de suite
        newColumn.appendChild(card);
        
        // 4b. Mettre à jour le texte du pôle sur la carte (optionnel mais propre)
        const poleLabel = card.querySelector('.pole-text');
        if (poleLabel) poleLabel.textContent = newPoleNom;

        // 5. Appeler le serveur (Symfony)
        this.savePosition(militantId, newPoleNom);
    }

    async savePosition(id, poleNom) {
        try {
            const response = await fetch(`/militant/${id}/change-pole`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ poleNom: poleNom })
            });

            if (!response.ok) {
                // TENTATIVE DE LIRE L'ERREUR SYMFONY
                const text = await response.text(); 
                console.error("Erreur Symfony :", text); // Regarde la console F12
                
                alert('Erreur serveur (' + response.status + ') ! Regarde la console (F12) pour le détail.');
                // window.location.reload(); // Je commente ça pour te laisser le temps de lire
            } else {
                console.log("Sauvegarde réussie !");
            }
        } catch (e) {
            console.error(e);
            alert('Erreur réseau ou JavaScript : ' + e.message);
        }
    }
}
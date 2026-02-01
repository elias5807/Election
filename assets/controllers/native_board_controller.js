import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    initialize() {
        // Configuration de l'auto-scroll
        this.scrollSpeed = 12;      // Vitesse de défilement (pixels)
        this.scrollThreshold = 120; // Zone sensible (pixels depuis le bord)
        this.autoScrollInterval = null;
    }

    // Quand on commence à traîner une carte
    onDragStart(event) {
        event.dataTransfer.setData('text/plain', event.target.dataset.id);
        event.dataTransfer.effectAllowed = 'move';
        event.target.classList.add('dragging');
    }

    // Quand on relâche la carte (ou qu'on annule)
    onDragEnd(event) {
        event.target.classList.remove('dragging');
        this.stopAutoScroll(); // Sécurité : on arrête le scroll si on lâche
        
        this.element.querySelectorAll('.drag-over-active').forEach(el => {
            el.classList.remove('drag-over-active');
        });
    }

    // Autoriser le drop ET gérer l'auto-scroll
    onDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';

        // On cible le conteneur principal qui a la scrollbar (.main-content-wrapper)
        const scrollContainer = event.currentTarget.closest('.main-content-wrapper');
        
        if (scrollContainer) {
            this.handleAutoScroll(event, scrollContainer);
        }

        return false;
    }

    // Logique d'auto-scroll
    handleAutoScroll(event, container) {
        const rect = container.getBoundingClientRect();
        const mouseY = event.clientY;

        // Calcul des distances par rapport aux bords haut et bas du container visible
        const distTop = mouseY - rect.top;
        const distBottom = rect.bottom - mouseY;

        // On nettoie l'intervalle précédent
        clearInterval(this.autoScrollInterval);
        this.autoScrollInterval = null;

        if (distTop < this.scrollThreshold) {
            // Si on est près du haut -> Scroll vers le haut
            this.autoScrollInterval = setInterval(() => {
                container.scrollTop -= this.scrollSpeed;
            }, 20);
        } else if (distBottom < this.scrollThreshold) {
            // Si on est près du bas -> Scroll vers le bas
            this.autoScrollInterval = setInterval(() => {
                container.scrollTop += this.scrollSpeed;
            }, 20);
        }
    }

    // Arrêt du scroll automatique
    stopAutoScroll() {
        if (this.autoScrollInterval) {
            clearInterval(this.autoScrollInterval);
            this.autoScrollInterval = null;
        }
    }

    onDragEnter(event) {
        event.currentTarget.classList.add('drag-over-active');
    }

    onDragLeave(event) {
        event.currentTarget.classList.remove('drag-over-active');
        // Si on quitte complètement la zone de drop, on calme le scroll
        this.stopAutoScroll();
    }

    async onDrop(event) {
        event.stopPropagation();
        this.stopAutoScroll(); // Important : arrêter le scroll au moment du drop
        event.currentTarget.classList.remove('drag-over-active');

        const militantId = event.dataTransfer.getData('text/plain');
        const card = document.querySelector(`.mini-card[data-id="${militantId}"]`);
        const newColumn = event.currentTarget;
        const newPoleNom = newColumn.dataset.pole;

        if (card) {
            newColumn.appendChild(card);
            const poleLabel = card.querySelector('.pole-text');
            if (poleLabel) poleLabel.textContent = newPoleNom;
            
            this.savePosition(militantId, newPoleNom);
        }
    }

    async savePosition(id, poleNom) {
        try {
            const response = await fetch(`/militant/${id}/change-pole`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ poleNom: poleNom })
            });

            if (!response.ok) {
                const text = await response.text(); 
                console.error("Erreur Symfony :", text);
                alert('Erreur lors de la sauvegarde. Vérifiez la console.');
            } else {
                console.log("Sauvegarde réussie pour le militant " + id);
            }
        } catch (e) {
            console.error(e);
            alert('Erreur réseau : ' + e.message);
        }
    }
}
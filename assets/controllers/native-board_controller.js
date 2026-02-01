import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    onDragOver(event) {
        event.preventDefault();
        event.currentTarget.classList.add('drag-over-active');
    }

    onDragEnter(event) {
        event.preventDefault();
    }

    onDragLeave(event) {
        event.currentTarget.classList.remove('drag-over-active');
    }

    onDrop(event) {
        event.preventDefault();
        const container = event.currentTarget;
        container.classList.remove('drag-over-active');

        const militantId = event.dataTransfer.getData('militantId');
        const card = document.getElementById(`militant-${militantId}`);
        const nouveauPoleNom = container.getAttribute('data-pole');

        if (card && nouveauPoleNom) {
            // 1. Déplacement visuel immédiat
            container.appendChild(card);

            // 2. Mise à jour en base de données via AJAX
            fetch(`/militant/${militantId}/change-pole`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ poleNom: nouveauPoleNom })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Erreur lors du changement de pôle');
                    window.location.reload(); // Annule visuellement en rechargeant
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
}
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input"]
    timeout = null;

    /**
     * Cette fonction est appelée à chaque touche tapée dans l'input
     */
    onSearch(event) {
        event.preventDefault();
        
        // "Debounce" : On annule le lancement précédent si on tape une nouvelle lettre
        // Cela évite d'envoyer 10 requêtes au serveur si on tape un nom rapidement
        clearTimeout(this.timeout);

        this.timeout = setTimeout(() => {
            this.executeSearch();
        }, 300); // Délai de 300ms après la dernière touche
    }

    /**
     * Envoie la requête AJAX au serveur et met à jour le HTML
     */
    executeSearch() {
        const query = this.inputTarget.value;
        const url = `/admin/search?q=${encodeURIComponent(query)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Erreur réseau");
            return response.text();
        })
        .then(html => {
            // 1. On remplace tout le contenu du dashboard (Sidebar + Board)
            const container = document.getElementById('search-results-container');
            if (container) {
                container.innerHTML = html;
            }

            // 2. REPOSITIONNEMENT DU CURSEUR
            // Comme on a remplacé le HTML, l'input est "neuf". On doit lui redonner le focus.
            const newScanner = document.querySelector('input[name="q"]');
            if (newScanner) {
                newScanner.focus();
                // Place le curseur à la fin du texte
                const length = newScanner.value.length;
                newScanner.setSelectionRange(length, length);
            }
        })
        .catch(error => {
            console.error("Erreur lors de la recherche :", error);
        });
    }
}
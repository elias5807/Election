import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input"]
    timeout = null;

    onSearch(event) {
        event.preventDefault();
        
        // "Debounce" : on attend 300ms avant d'envoyer la requête pour ne pas spammer le serveur
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.executeSearch();
        }, 300);
    }

    executeSearch() {
        const query = this.inputTarget.value;
        const url = `/admin/search?q=${encodeURIComponent(query)}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            // On remplace le contenu du board principal
            const resultsContainer = document.getElementById('search-results');
            if (resultsContainer) {
                resultsContainer.innerHTML = html;
            }
        });
    }
}
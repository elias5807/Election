document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-auto-save');
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // 1. On prépare les données du formulaire
            const formData = new FormData(form);

            // 2. On envoie les données via fetch (AJAX)
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Important pour Symfony
                }
            })
            .then(response => {
                if (response.ok) {
                    console.log("Sauvegarde réussie !");
                    // Optionnel : Changer la couleur de la bordure pour confirmer visuellement
                    input.style.borderColor = "green";
                    setTimeout(() => input.style.borderColor = "", 2000);
                } else {
                    console.error("Erreur lors de la sauvegarde");
                    input.style.borderColor = "red";
                }
            })
            .catch(error => console.error('Erreur réseau:', error));
        });
    });
});
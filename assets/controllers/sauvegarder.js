document.addEventListener('DOMContentLoaded', function() {
    // 1. On récupère le formulaire
    const form = document.getElementById('form-auto-save');

    // 2. On récupère tous les champs (input et select) à l'intérieur
    const inputs = form.querySelectorAll('input, select');

    // 3. On ajoute un écouteur sur chaque champ
    inputs.forEach(input => {
        // L'événement 'change' se déclenche quand on lâche le curseur (pour un range)
        // ou quand on clique ailleurs (pour un texte)
        input.addEventListener('change', function() {
            
            // Optionnel : Afficher un petit texte "Sauvegarde..."
            // document.getElementById('status').innerText = "Sauvegarde en cours...";
            
            // 4. On soumet le formulaire
            form.submit(); 
        });
    });
});

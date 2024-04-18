const adresseInput = document.getElementById('adresseInput');
const suggestionsList = document.getElementById('suggestionsList');
let validSuggestions = [];

adresseInput.addEventListener('input', function() {
    const inputValue = adresseInput.value.trim();

    // Fetch suggestions from the API
    fetch(`https://api-adresse.data.gouv.fr/search/?q=${inputValue}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('La requête n\'a pas abouti. Statut ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const suggestions = data.features;
            validSuggestions = suggestions.map(suggestion => suggestion.properties.label); // Update validSuggestions
            afficherSuggestions(suggestions);
        })
        .catch(error => console.error('Erreur lors de la récupération des suggestions:', error));
});

function afficherSuggestions(suggestions) {
    suggestionsList.innerHTML = ''; // Clear previous suggestions

    suggestions.forEach(suggestion => {
        const li = document.createElement('li');
        li.textContent = suggestion.properties.label;
        li.addEventListener('click', function(){
            adresseInput.value = suggestion.properties.label;
            suggestionsList.innerHTML = ''; // Clear suggestions list after selection
        });
        suggestionsList.appendChild(li);
    });
}

document.querySelector('form').addEventListener('submit', function(event) {
    const inputValue = adresseInput.value.trim();
    if (!validSuggestions.includes(inputValue)) {
        event.preventDefault(); // Prevent form submission if input doesn't match any valid suggestion
        alert("Veuillez sélectionner une suggestion valide ou saisir une adresse valide.");
    }
});

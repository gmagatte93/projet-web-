// Fonction de validation pour les dates (format : YYYY-MM-DD)
function isValidDate(date) {
    let regex = /^\d{4}-\d{2}-\d{2}$/;
    return regex.test(date);
}

// Validation du formulaire d'inscription
function validateForm() {
    let nom = document.getElementById("nom").value.trim();
    let prenom = document.getElementById("prenom").value.trim();
    let email = document.getElementById("email").value.trim();
    let dateNaissance = document.getElementById("date_naissance").value.trim();
    let numeroRegistre = document.getElementById("numero_registre").value.trim();

    if (!nom || !prenom || !email) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return false;
    }

    let emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailRegex.test(email)) {
        alert("Veuillez entrer un email valide.");
        return false;
    }

    if (dateNaissance && !isValidDate(dateNaissance)) {
        alert("La date de naissance n'est pas valide.");
        return false;
    }

    if (numeroRegistre && !numeroRegistre.match(/^\d+$/)) {
        alert("Le numéro de registre doit être un nombre.");
        return false;
    }

    return true;
}

// Validation du formulaire de rendez-vous
function validateRdvForm() {
    let nom = document.getElementById("nom").value.trim();
    let email = document.getElementById("email").value.trim();
    let telephone = document.getElementById("telephone").value.trim();
    let dateNaissance = document.getElementById("date_naissance").value.trim();
    let dateRdv = document.getElementById("date_rdv").value.trim();
    let numeroRegistre = document.getElementById("numero_registre").value.trim();

    if (!nom || !email || !telephone || !dateRdv || !numeroRegistre) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return false;
    }

    let emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailRegex.test(email)) {
        alert("Veuillez entrer un email valide.");
        return false;
    }

    let phoneRegex = /^[0-9]{9,15}$/;
    if (!phoneRegex.test(telephone)) {
        alert("Veuillez entrer un numéro de téléphone valide (9 à 15 chiffres).");
        return false;
    }

    if (dateNaissance && !isValidDate(dateNaissance)) {
        alert("La date de naissance n'est pas valide.");
        return false;
    }

    if (!isValidDate(dateRdv)) {
        alert("La date du rendez-vous n'est pas valide.");
        return false;
    }

    if (!numeroRegistre.match(/^\d+$/)) {
        alert("Le numéro de registre doit être un nombre.");
        return false;
    }

    return true;
}


// Fonction de validation pour la date (format : YYYY-MM-DD)
function isValidDate(date) {
    let regex = /^\d{4}-\d{2}-\d{2}$/;
    return regex.test(date);
}


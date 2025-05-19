function validateForm() {
    const firstname = document.getElementById("firstname").value;
    const lastname = document.getElementById("lastname").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;

    // Validation des champs de texte
    if (firstname === "" || lastname === "") {
        alert("Le prénom et le nom sont obligatoires.");
        return false;
    }

    // Validation de l'email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Veuillez entrer un email valide.");
        return false;
    }

    // Validation du numéro de téléphone
    const phoneRegex = /^[0-9]{3}-[0-9]{3}-[0-9]{4}$/;
    if (!phoneRegex.test(phone)) {
        alert("Le numéro de téléphone doit être au format XXX-XXX-XXXX.");
        return false;
    }

    return true;
}

<?php
session_start();
date_default_timezone_set("America/Toronto"); 

// Vérifier si un code a été généré
if (!isset($_SESSION['code_2fa']) || !isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Traitement du formulaire
$erreur = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code_saisi = $_POST['code'] ?? '';

    if ($code_saisi == $_SESSION['code_2fa']) {
        unset($_SESSION['code_2fa']);
        $_SESSION['auth'] = true; // Marque la session comme vérifiée

        // Rediriger selon l'identité de l'utilisateur
        if ($_SESSION['utilisateur'] === 'gueye@420.techinfo') {
            header('Location: admin.php');
        } else {
            header('Location: mes_rendezvous.php');
        }
        exit();
    } else {
        $erreur = "❌ Code incorrect. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification 2FA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a href="index.html">Accueil</a>
    <a href="connexion.php">Retour à la connexion</a>
</nav>

<main>
    <h2>Authentification à deux facteurs</h2>
    <p>Un code vous a été envoyé (simulé dans <code>logs/code-2fa.txt</code>).</p>

    <?php if (!empty($erreur)) echo "<p class='error'>$erreur</p>"; ?>

    <form method="post">
        <label for="code">Entrez le code reçu :</label><br><br>
        <input type="text" name="code" id="code" required><br><br>
        <button type="submit">Vérifier</button>
    </form>
</main>
</body>
</html>

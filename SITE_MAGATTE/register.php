<?php
// Sécurisation de la session
date_default_timezone_set("America/Toronto"); 
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

require_once 'config.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $courriel = filter_input(INPUT_POST, 'courriel', FILTER_VALIDATE_EMAIL);
    $mot_de_passe = filter_input(INPUT_POST, 'mot_de_passe');

    if ($nom && $courriel && $mot_de_passe) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE courriel = :courriel");
            $stmt->bindParam(':courriel', $courriel, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetch()) {
                $message = "<p class='error'>Ce courriel est déjà utilisé.</p>";
            } else {
                $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, courriel, mot_de_passe) VALUES (:nom, :courriel, :mot_de_passe)");
                $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                $stmt->bindParam(':courriel', $courriel, PDO::PARAM_STR);
                $stmt->bindParam(':mot_de_passe', $hash, PDO::PARAM_STR);
                $stmt->execute();

                $message = "<p class='success'>Utilisateur enregistré avec succès.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
        }
    } else {
        $message = "<p class='error'>Tous les champs sont requis et doivent être valides.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <a href="index.html">Accueil</a>
        <a href="connexion.php">Connexion</a>
        <a href="register.php">Inscription</a>
        <a href="mes_rendezvous.php">Prendre un RDV</a>
    </nav>

    <main>
        <?php if (!empty($message)) echo $message; ?>

        <form method="post">
            <input type="text" name="nom" placeholder="Nom complet" required><br><br>
            <input type="email" name="courriel" placeholder="Adresse courriel" required><br><br>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br><br>
            <button type="submit">S'inscrire</button>
        </form>
    </main>
</body>
</html>
